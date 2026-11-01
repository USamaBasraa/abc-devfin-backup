@extends('layouts.app', ['pageTitle' => 'BNU Event Approval'])
@section('content')
    @include('components.toast-notifications')
    <style>

    </style>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Event List</h5>
                    {{-- <a href="{{url("UniversityEvent/calendar")}}" class="btn btn-primary btn-sm">View Calendar</a> --}}
                </div>
                <!-- 
                    This table displays all events fetched from the database. 
                    - It shows Pending events at the top.
                    - 'Action' column allows approving/declining events.
                -->
                <div class="card-body table-responsive">
                    <table class="table table-striped table-hover" id="table">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Event Title</th>
                                <th scope="col">Event Date</th>
                                <th scope="col">Time</th>
                                <th scope="col">Venue</th>
                                <th scope="col">Category</th>
                                <th scope="col">Event By</th>
                                <th scope="col">Status</th>
                                <th scope="col">Remarks</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody id="event_table_body">
                            @foreach ($events as $index => $e)
                                <tr>
                                    <td>{{ $e->id }}</td>
                                    <td>{{ $e->event_title }}</td>
                                    @if ($e->to_event_date != '')
                                        <td>{{ \Carbon\Carbon::parse($e->event_date)->format('F j, Y')}} - {{\Carbon\Carbon::parse($e->to_event_date)->format('F j, Y') }} </td>
                                    @else
                                        <td>{{ \Carbon\Carbon::parse($e->event_date)->format('F j, Y')}} </td>
                                    @endif
                                    <td>{{ $e->start_time_slot . ' - ' . $e->end_time_slot }}</td>
                                    <td>{{ $e->room_name }}</td>

                                    <td>{{ ucfirst($e->event_type) }}</td>
                                    <td>{{ ucfirst($e->uv_entry_user) }}</td>
                                    <td>
                                        @if ($e->status == 'Declined')
                                            <span class="badge badge-danger">{{ $e->status }}</span>
                                        @elseif ($e->status == 'Approved')
                                            <span class="badge badge-success">{{ $e->status }}</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $e->remarks }}</td>
                                    <td>
                                        <!-- 
                                            LOGIC: Action Button Visibility 
                                            1. If status is NOT Approved or Declined (i.e., Pending), show the 'Update Status' button.
                                            2. If already processed, show a DISABLED 'Updated' button with a lock icon.
                                        -->
                                        @if ($e->status != 'Approved' && $e->status != 'Declined')
                                            <button class="btn btn-primary btn-sm" onclick="showModal({{ $e->id }})">
                                                <i class="fa fa-edit mr-1"></i> Update Status
                                            </button>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                <i class="fa fa-lock mr-1"></i> Updated
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Professional Bootstrap 4 Modal -->
    <div class="modal fade" id="eventStatusUpdate" tabindex="-1" role="dialog" aria-labelledby="eventStatusUpdateLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <!-- Header with Primary Color -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="eventStatusUpdateLabel">
                        <i class="fa fa-edit mr-2"></i> Update Event Status
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <!-- Body -->
                <div class="modal-body p-4 bg-light">
                    <input type="hidden" id="id">
                    <div class="form-group mb-0">
                        <label for="remarks" class="font-weight-bold text-dark"><i class="fa fa-comment-alt mr-1 text-muted"></i> Remarks (Optional for Approval)</label>
                        <textarea class="form-control border-secondary shadow-sm" id="remarks" rows="5" placeholder="Please enter the reason for your decision here..."></textarea>
                        <small class="form-text text-muted mt-2">
                            <i class="fa fa-info-circle mr-1"></i> Use this field to provide feedback or reasons for declination.
                        </small>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Close</button>
                    <div class="ml-auto">
                        <button type="button" class="btn btn-outline-danger px-4 mr-2" onclick="updateStatus('Declined')">
                            <i class="fa fa-times mr-1"></i> Decline
                        </button>
                        <button type="button" class="btn btn-success px-4" onclick="updateStatus('Approved')">
                            <i class="fa fa-check mr-1"></i> Approve
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {

            var dataTable = $('#table').DataTable({
                "order": [
                    [0, 'desc']
                ],
                "pageLength": 100,
                "stateSave": true // Keeps page number and sorting on interaction
            });

        });

        // Use Bootstrap native modal methods
        function CloseModal() {
            $('#eventStatusUpdate').modal('hide');
        }

        function showModal(id) {
            $("#id").val(id);
            $("#remarks").val(''); // Clear previous remarks
            $('#eventStatusUpdate').modal('show');
        }

        // === MAIN FUNCTION: Update Event Status ===
        // This function sends the approval/decline request to the server via AJAX.
        function updateStatus(status) {

            $.ajax({
                type: "POST",
                url: "/api/UniversityEvent/update-event-status",
                data: {
                    status: status, // 'Approved' or 'Declined'
                    id: $("#id").val(), // The Event ID from the hidden input in Modal
                    remarks: $("#remarks").val() // Admin comments
                },
                headers: {
                    Authorization: 'Bearer {{ auth()->user()->api_token }}',
                    Accept: 'application/json'
                },
                success: function(response) {
                    if (response.error == true) {
                        showToast('error', response.message);
                    } else {
                        showToast('success', response.message);
                        
                        // === LOCAL ROW UPDATE LOGIC (IMPORTANT) ===
                        // Instead of reloading the whole page (which loses scroll position),
                        // we find the specific row in the table and update it manually.
                        
                        let eventId = parseInt($("#id").val());
                        let newStatus = status; 
                        let newRemarks = $("#remarks").val();
                        let table = $('#table').DataTable();

                        // Step 1: Find the row in DataTable where Column 0 (ID) matches our Event ID
                        let row = table.row(function(idx, data, node) {
                            return parseInt(data[0]) === eventId;
                        });

                        if (row.length > 0) {
                             // Update Badge (Index 7)
                            let newBadge = '';
                            if (newStatus == 'Declined') newBadge = `<span class="badge badge-danger">Declined</span>`;
                            else if (newStatus == 'Approved') newBadge = `<span class="badge badge-success">Approved</span>`;
                            
                            // Important: Use cell().data() to update internal data
                            row.cell(row.index(), 7).data(newBadge); 

                            // Update Remarks (Index 8)
                            if(newRemarks) {
                                row.cell(row.index(), 8).data(newRemarks);
                            }

                            // Update Action Button (Index 9) - Make it Locked
                            let newAction = `<button class="btn btn-secondary btn-sm" disabled><i class="fa fa-lock mr-1"></i> Updated</button>`;
                            row.cell(row.index(), 9).data(newAction);
                            
                            // Redraw to show changes (keep paging)
                            table.draw(false); 
                        } else {
                            // Fallback if somehow row not found (e.g. page changed)
                            console.warn("Row not found for local update, reloading table.");
                            loadTable();
                        }

                        CloseModal();
                        // No full loadTable() call here to preserve state, local update done above.
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });

        }

        function loadTable() {
            $('#table').DataTable().clear().draw();
            $.ajax({

                type: "GET",
                url: "/api/UniversityEvent/event-approval",
                headers: {
                    Authorization: 'Bearer {{ auth()->user()->api_token }}',
                    Accept: 'application/json'
                },
                success: function(response) {
                    let html = '';
                    response.forEach(function(item, index) {
                        let badge = '';
                        if (item.status == 'Declined') {
                            badge = `<span class="badge badge-danger">${item.status}</span>`;
                        } else if (item.status == 'Approved') {
                            badge = `<span class="badge badge-success">${item.status}</span>`;
                        } else {
                            badge = `<span class="badge badge-warning">Pending</span>`;
                        }

                        // Define Action Button logic based on status
                        let actionHtml = '';
                        if(item.status != 'Approved' && item.status != 'Declined') {
                             actionHtml = `<button class="btn btn-primary btn-sm" onclick="showModal(${item.id})"><i class="fa fa-edit mr-1"></i> Update Status</button>`;
                        } else {
                             actionHtml = `<button class="btn btn-secondary btn-sm" disabled><i class="fa fa-lock mr-1"></i> Updated</button>`;
                        }

                        // Handle Date Formatting
                        let dateDisplay = item.event_date.split('00:00:00')[0];
                        if(item.to_event_date) {
                             dateDisplay += ' - ' + item.to_event_date.split('00:00:00')[0];
                        }
                        
                        var newData = [
                            item.id, 
                            item.event_title, 
                            dateDisplay, 
                            item.start_time_slot + ' - ' + item.end_time_slot, 
                            item.room_name, 
                            capitalizeFirstLetter(removeUnderscores(item.event_type)),
                            item.uv_entry_user,
                            badge, 
                            (item.remarks ? item.remarks : ''),
                            actionHtml
                        ];
                        $('#table').DataTable().row.add(newData);
                    });
                    $('#table').DataTable().draw();

                    // $("#event_table_body").append(html);

                    // if(response.length>0){
                    //     $("#Modal").fadeIn();
                    //     return false;
                    // }
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
    </script>
@stop
