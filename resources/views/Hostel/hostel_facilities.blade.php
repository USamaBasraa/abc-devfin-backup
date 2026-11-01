@extends('layouts.app')



@section('content')

    <!-- Include Toast Component -->
    @include('components.toast-notifications')

    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fa fa-plus-circle mr-2"></i>Add Hostel Facility</h4>
            </div>

            <div class="card-body">
                <!-- Facility Form -->
                <form action="{{ route('submit.facilities') }}" method="POST">
                    @csrf
                
                    <div class="form-group">
                        <label for="facility_name"><i class="fa fa-building mr-2"></i>Facility Name:</label>
                        <select class="form-control" name="facility_name" id="facility_name">
                            @foreach($hostelNames as $hostel)
                                <option value="{{ $hostel->hf_name }}">{{ $hostel->hf_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="facility_dues"><i class="fa fa-money mr-2"></i>Facility Dues:</label>
                        <input type="number" step="0.01" class="form-control" id="facility_dues" name="facility_dues" required>
                    </div>
                    <div class="form-group">
                        <label for="session"><i class="fa fa-calendar mr-2"></i>Session:</label>
                        <select class="form-control" id="session" name="session" required>
                            <option value="">Select a session</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->sess_id }}">{{ $session->sess_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fa fa-toggle-on mr-2"></i>Status:</label><br>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="enabled" name="status" value="enabled" class="custom-control-input" required>
                            <label class="custom-control-label" for="enabled">Enabled</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="disabled" name="status" value="disabled" class="custom-control-input" required>
                            <label class="custom-control-label" for="disabled">Disabled</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save mr-2"></i>Submit
                    </button>
                </form>

                <!-- List of Added Facilities -->
                <h4 class="mt-5 mb-4"><i class="fa fa-list mr-2"></i>Added Facilities</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Dues</th>
                                <th>Session</th>
                                <th>Status</th>
                                <th>Entry Date & Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($facilities AS $facility)
                        <tr>
                            <td>{{ $facility->hf_id }}</td>
                            <td>{{ $facility->hf_name }}</td>
                            <td>{{ $facility->hf_cost }}</td>
                            <td>{{ $facility->hf_session }}</td>
                            <td>
                                <span class="badge badge-{{ $facility->hf_status === 'enabled' ? 'success' : 'danger' }}">
                                    {{ $facility->hf_status }}
                                </span>
                            </td>
                            <td>{{ $facility->hf_entrydatetime }}</td>
                            <td>
                                <button class="btn btn-outline-warning btn-sm edit-facility" data-id="{{ $facility->hf_id }}" data-name="{{ $facility->hf_name }}" data-dues="{{ $facility->hf_cost }}" data-session="{{ $facility->hf_session }}" data-status="{{ $facility->hf_status }}">
                                    <i class="fa fa-edit mr-1"></i>Edit
                                </button>
                                
                                @if($facility->hf_status === 'disabled')
                                <button type="button" class="btn btn-outline-success btn-sm enable-facility" data-id="{{ $facility->hf_id }}">
                                    <i class="fa fa-check-circle mr-1"></i>Enable
                                </button>
                                @else
                                <button type="button" class="btn btn-outline-danger btn-sm disable-facility" data-id="{{ $facility->hf_id }}">
                                    <i class="fa fa-ban mr-1"></i>Disable
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

    <!-- Edit Facility Modal -->
    <div class="modal fade" id="editFacilityModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('edit.facility') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fa fa-edit mr-2"></i>Edit Facility</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="facility_id" id="editFacilityId">
                        <div class="form-group">
                            <label for="editFacilityName"><i class="fa fa-building mr-2"></i>Facility Name:</label>
                            <input type="text" class="form-control" id="editFacilityName" name="facility_name" required>
                        </div>
                        <div class="form-group">
                            <label for="editFacilityDues"><i class="fa fa-money mr-2"></i>Facility Dues:</label>
                            <input type="number" step="0.01" class="form-control" id="editFacilityDues" name="facility_dues" required>
                        </div>
                        <div class="form-group">
                            <label for="editSession"><i class="fa fa-calendar mr-2"></i>Session:</label>
                            <input type="text" class="form-control" id="editSession" name="session" required>
                        </div>
                        <div class="form-group">
                            <label for="editStatus"><i class="fa fa-toggle-on mr-2"></i>Status:</label>
                            <input type="text" class="form-control" id="editStatus" name="status" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                            <i class="fa fa-times mr-2"></i>Close
                        </button>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fa fa-save mr-2"></i>Save changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa fa-question-circle mr-2"></i>Confirm Action</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="confirmationMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        <i class="fa fa-times mr-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-outline-primary text-primary" id="confirmActionBtn">
                        <i class="fa fa-check mr-2"></i>Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Required JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('.table').DataTable({
                responsive: true,
                order: [[0, 'desc']]
            });

            // Edit Facility
            $('.edit-facility').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var dues = $(this).data('dues');
                var session = $(this).data('session');
                var status = $(this).data('status');

                $('#editFacilityId').val(id);
                $('#editFacilityName').val(name);
                $('#editFacilityDues').val(dues);
                $('#editSession').val(session);
                $('#editStatus').val(status);

                $('#editFacilityModal').modal('show');
            });

            // Enable/Disable Facility
            $('.enable-facility, .disable-facility').on('click', function() {
                const facilityId = $(this).data('id');
                const action = $(this).hasClass('enable-facility') ? 'enable' : 'disable';
                const message = `Are you sure you want to ${action} this facility?`;
                
                $('#confirmationMessage').text(message);
                $('#confirmActionBtn')
                    .removeClass('btn-success btn-danger')
                    .addClass(action === 'enable' ? 'btn-success' : 'btn-danger')
                    .text(action === 'enable' ? 'Enable' : 'Disable');

                $('#confirmationModal').modal('show');

                $('#confirmActionBtn').off('click').on('click', function() {
                    const form = $('<form>', {
                        'method': 'POST',
                        'action': `{{ url('/') }}/${action}-facility/${facilityId}`
                    }).append($('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': '{{ csrf_token() }}'
                    }));

                    form.appendTo('body').submit();
                });
            });

            // Show success message using toast
            @if(session('success'))
                showToast('success', '{{ session('success') }}');
            @endif

            // Show error message using toast
            @if(session('error'))
                showToast('error', '{{ session('error') }}');
            @endif
        });
    </script>
@endsection