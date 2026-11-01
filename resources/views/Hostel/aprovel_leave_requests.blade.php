<!-- admin_leave_requests.blade.php -->
@extends('layouts.app')

@section('content')
    <!-- Include Toast Component -->
    @include('components.toast-notifications')

    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fa fa-calendar-check-o mr-2"></i>Leave Requests</h4>
            </div>

            <div class="card-body">
                <!-- Tabs for Pending and Processed Requests -->
                <ul class="nav nav-tabs mb-4" id="leaveRequestTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pending-tab" data-toggle="tab" href="#pending" role="tab">
                            <i class="fa fa-clock-o mr-2"></i>Pending Requests ({{ $pendingCount }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="processed-tab" data-toggle="tab" href="#processed" role="tab">
                            <i class="fa fa-check-circle mr-2"></i>Processed Requests ({{ $approvedCount + $declinedCount }})
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="leaveRequestTabsContent">
                    <!-- Pending Requests Tab -->
                    <div class="tab-pane fade show active" id="pending" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Enrolment No</th>
                                        <th>Name</th>
                                        <th>Room No</th>
                                        <th>Leave Reason</th>
                                        <th>Submit Date</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingRequests as $request)
                                        <tr>
                                            <td>{{ $request->hnl_enrolment_no }}</td>
                                            <td>{{ $request->full_name }}</td>
                                            <td>{{ $request->hnl_room_no }}</td>
                                            <td>{{ $request->hnl_leave_reason }}</td>
                                            <td>{{ $request->hnl_entry_date_time }}</td>
                                            <td>{{ $request->hnl_leave_from }}</td>
                                            <td>{{ $request->hnl_leave_to }}</td>
                                            <td>
                                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#approveModal{{ $request->hnl_id }}">
                                                    <i class="fa fa-check mr-1"></i>Approve
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#declineModal{{ $request->hnl_id }}">
                                                    <i class="fa fa-times mr-1"></i>Decline
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Approve Modal -->
                                        <div class="modal fade" id="approveModal{{ $request->hnl_id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <form action="{{ route('leave-requests-approve', $request->hnl_id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title">
                                                                <i class="fa fa-check-circle mr-2"></i>Approve Leave Request
                                                            </h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="approve_remarks">
                                                                    <i class="fa fa-comment mr-2"></i>Remarks for Approval:
                                                                </label>
                                                                <textarea name="approve_remarks" class="form-control" rows="3" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                <i class="fa fa-times mr-2"></i>Cancel
                                                            </button>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="fa fa-check mr-2"></i>Approve
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Decline Modal -->
                                        <div class="modal fade" id="declineModal{{ $request->hnl_id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <form action="{{ route('leave-requests-decline', $request->hnl_id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title">
                                                                <i class="fa fa-times-circle mr-2"></i>Decline Leave Request
                                                            </h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="decline_reason">
                                                                    <i class="fa fa-comment mr-2"></i>Reason for Decline:
                                                                </label>
                                                                <textarea name="decline_reason" class="form-control" rows="3" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                <i class="fa fa-times mr-2"></i>Cancel
                                                            </button>
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fa fa-ban mr-2"></i>Decline
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Processed Requests Tab -->
                    <div class="tab-pane fade" id="processed" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Enrolment No</th>
                                        <th>Name</th>
                                        <th>Room No</th>
                                        <th>Leave Reason</th>
                                        <th>Submit Date</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Remarks</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($processedRequests as $request)
                                        <tr>
                                            <td>{{ $request->hnl_enrolment_no }}</td>
                                            <td>{{ $request->full_name }}</td>
                                            <td>{{ $request->hnl_room_no }}</td>
                                            <td>{{ $request->hnl_leave_reason }}</td>
                                            <td>{{ $request->hnl_entry_date_time }}</td>
                                            <td>{{ $request->hnl_leave_from }}</td>
                                            <td>{{ $request->hnl_leave_to }}</td>
                                            <td>{{ $request->hnl_remarks }}</td>
                                            <td>
                                                @if($request->hnl_approve_status === 'Approved')
                                                    <span class="badge badge-success">
                                                        <i class="fa fa-check-circle mr-1"></i>Approved
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger">
                                                        <i class="fa fa-times-circle mr-1"></i>Declined
                                                    </span>
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
            // Initialize DataTables
            $('.table').DataTable({
                responsive: true,
                order: [[4, 'desc']], // Sort by submit date by default
                pageLength: 10
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
