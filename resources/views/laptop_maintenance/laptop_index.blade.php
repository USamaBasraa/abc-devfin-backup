
<?php
  $file_name= "app";
  if (auth()->user()->cms_sidebar=='new')
  {
  $file_name= "updated_app";
  }
?> 
@extends('layouts.' . $file_name)  
@section('title')
    BNU CMS
@stop
@section('content')
@include('components.toast-notifications')

<style>
    .card-header {
        background-color: #FFFFFF;
        border-bottom: 2px solid #4555C3;
        padding: 20px;
    }
    
    .card-header h3 {
        font-family: Montserrat;
        font-weight: 600;
        color: #252525;
        margin: 0;
    }
    
    /* DataTable Buttons Styling - Master Fee Structure Style */
    .dt-button {
        background-color: #4555C3;
        color: #FFFFFF;
        border: none;
        padding: 5px 10px;
        margin: 5px;
        cursor: pointer;
        font-size: 14px;
        border-radius: 4px;
        font-family: Montserrat;
    }
    
    .dt-button:hover {
        background-color: #3444A8;
        color: #FFFFFF;
    }
    
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dt-buttons {
        display: inline-block;
        align-items: center;
    }
    
    .dataTables_wrapper .dataTables_length label, 
    .dataTables_wrapper .dataTables_filter label {
        margin-top: 5px;
        font-family: Montserrat;
    }
    
    .dataTables_wrapper .dt-buttons {
        margin-right: 15px;
    }
    
    /* DataTable Search and Info */
    .dataTables_filter input {
        background-color: #F1F1F1;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px 10px;
        font-family: Montserrat;
    }
    
    .dataTables_length select {
        background-color: #F1F1F1;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px 10px;
        font-family: Montserrat;
    }
    
    .dataTables_info {
        font-family: Montserrat;
        color: #252525;
        padding-top: 10px;
    }
    
    .dataTables_paginate {
        font-family: Montserrat;
    }
    
    /* Table Display Style */
    table.display {
        width: 100%;
        font-family: Montserrat;
    }
    
    /* Card Styling */
    .card {
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        border: none;
    }
    
    /* Table Row Styling */
    table.display tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    /* Action Buttons Container */
    .action-buttons {
        display: flex;
        gap: 5px;
        white-space: nowrap;
    }
    
    /* Button Sizes */
    .btn-sm {
        min-width: 80px;
    }
</style>

<div class="card e-panel" style="margin:10px;width:98%;">
    <div class="card-header">
        <h3><i class="fa fa-laptop"></i> Laptop Maintenance Requests - Admin Panel</h3>
    </div>
    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" style="font-family: Montserrat;" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" style="font-family: Montserrat;" role="alert">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" style="font-family: Montserrat;" role="alert">
                <strong>Validation Error!</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

    </div>
</div>

<!-- Data Table Card -->
<div class="row" style="margin:10px;">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-body pb-2">
                <div class="table-responsive">
                    <table id="maintenanceTable" class="table display" style="width:100%">
                <thead style=" color:#FFFFFF; font-family: Montserrat;">
                    <tr>
                        <th>#</th>
                        <!-- <th>Request ID</th> -->
                        <th>Student Name</th>
                        <th>Enrollment No</th>
                        <th>Department</th>
                        <th>Mobile No</th>
                        <th>Laptop Type</th>
                        <th>Brand</th>
                        <th>Problem</th>
                        <th>Status</th>
                        <th>Request Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody style="font-family: Montserrat; color:#252525;">
                    @forelse($requests as $index => $request)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <!-- <td>{{ $request->id }}</td> -->
                        <td>{{ $request->student_name ?? 'N/A' }}</td>
                        <td>{{ $request->enrollment_no ?? 'N/A' }}</td>
                        <td>{{ $request->department ?? 'N/A' }}</td>
                        <td>{{ $request->mobile_no ?? 'N/A' }}</td>
                        <td>{{ $request->laptop_type ?? 'N/A' }}</td>
                        <td>{{ $request->brand ?? 'N/A' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($request->problem ?? 'N/A', 50) }}</td>
                        <td>
                            @if(strtolower($request->status) == 'pending')
                                <span class="badge badge-warning" style="font-family: Montserrat;">
                                    <i class="fa fa-clock-o"></i> Pending
                                </span>
                            @elseif(strtolower($request->status) == 'completed')
                                <span class="badge badge-success" style="font-family: Montserrat;">
                                    <i class="fa fa-check"></i> Completed
                                </span>
                            @else
                                <span class="badge badge-secondary" style="font-family: Montserrat;">
                                    <i class="fa fa-info"></i> {{ ucfirst($request->status) }}
                                </span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($request->created_at)->format('d M Y, h:i A') }}</td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-sm btn-primary" onclick="viewDetails{{ $request->id }}()" style="background-color:#4555C3; border-color:#4555C3; font-family: Montserrat;">
                                    <i class="fa fa-eye"></i> View
                                </button>
                                @if(strtolower($request->status) != 'completed')
                                <button type="button" class="btn btn-sm btn-success" onclick="openCompleteModal{{ $request->id }}()" style="font-family: Montserrat;">
                                    <i class="fa fa-check"></i> Complete
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>

                    <!-- View Details Modal for this request -->
                    <div class="modal fade" id="viewModal{{ $request->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background: #4555C3; color: #FFFFFF; font-family: Montserrat;">
                                    <h5 class="modal-title" style="color: #FFFFFF;"><i class="fa fa-eye"></i> Request Details</h5>
                                    <button type="button" class="close" data-dismiss="modal" style="color:#FFFFFF; opacity: 1;">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" style="font-family: Montserrat; color:#252525;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Request ID:</strong> {{ $request->id }}</p>
                                            <p><strong>Student Name:</strong> {{ $request->student_name ?? 'N/A' }}</p>
                                            <p><strong>Enrollment No:</strong> {{ $request->enrollment_no ?? 'N/A' }}</p>
                                            <p><strong>Department:</strong> {{ $request->department ?? 'N/A' }}</p>
                                            <p><strong>Mobile No:</strong> {{ $request->mobile_no ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Laptop Type:</strong> {{ $request->laptop_type ?? 'N/A' }}</p>
                                            <p><strong>Brand:</strong> {{ $request->brand ?? 'N/A' }}</p>
                                            <p><strong>Laptop Password:</strong> {{ $request->laptop_password ?? 'N/A' }}</p>
                                            <p><strong>Status:</strong> 
                                                @if(strtolower($request->status) == 'pending')
                                                    <span class="badge badge-warning"><i class="fa fa-clock-o"></i> Pending</span>
                                                @elseif(strtolower($request->status) == 'completed')
                                                    <span class="badge badge-success"><i class="fa fa-check"></i> Completed</span>
                                                @else
                                                    <span class="badge badge-info">{{ ucfirst($request->status) }}</span>
                                                @endif
                                            </p>
                                            <p><strong>Submitted By:</strong> {{ $request->submitted_by ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Request Date:</strong> {{ \Carbon\Carbon::parse($request->created_at)->format('d M Y, h:i A') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            @if($request->completed_at)
                                            <p><strong>Completed Date:</strong> {{ \Carbon\Carbon::parse($request->completed_at)->format('d M Y, h:i A') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p><strong>Description:</strong></p>
                                            <p style="background-color:#F1F1F1; padding: 10px; border-radius: 5px;">{{ $request->description ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p><strong>Problem:</strong></p>
                                            <p style="background-color:#F1F1F1; padding: 10px; border-radius: 5px;">{{ $request->problem ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    @if($request->os_changes)
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p><strong>OS Changes:</strong></p>
                                            <p style="background-color:#F1F1F1; padding: 10px; border-radius: 5px;">{{ $request->os_changes }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    @if($request->software_changes)
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p><strong>Software Changes:</strong></p>
                                            <p style="background-color:#F1F1F1; padding: 10px; border-radius: 5px;">{{ $request->software_changes }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    @if($request->resolver_remarks)
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Admin Remarks:</strong></p>
                                            <p style="background-color:#F1F1F1; padding: 10px; border-radius: 5px;">{{ $request->resolver_remarks }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Resolved by:</strong></p>
                                            <p style="background-color:#F1F1F1; padding: 10px; border-radius: 5px;">{{ $request->resolved_by }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Complete Modal for this request -->
                    <div class="modal fade" id="completeModal{{ $request->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="POST" action="{{ url('/laptop-maintenance/mark-complete') }}">
                                    @csrf
                                    <input type="hidden" name="request_id" value="{{ $request->id }}">
                                    <div class="modal-header" style="background: #4555C3; color: #FFFFFF; font-family: Montserrat;">
                                        <h5 class="modal-title" style="color: #FFFFFF;">
                                            <i class="fa fa-check-circle"></i> 
                                            @if(strtolower($request->status) == 'completed')
                                                View Completion Details
                                            @else
                                                Mark as Complete
                                            @endif
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" style="color:#FFFFFF; opacity: 1;">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body" style="font-family: Montserrat; color:#252525;">
                                        @if(strtolower($request->status) == 'completed')
                                            <div class="alert alert-success" style="font-family: Montserrat;">
                                                <i class="fa fa-check-circle"></i> This request has already been completed.
                                            </div>
                                            <div class="form-group">
                                                <label for="remarks{{ $request->id }}">Previous Remarks:</label>
                                                <textarea class="form-control" name="remarks" id="remarks{{ $request->id }}" rows="4" style="background-color:#F1F1F1; font-family: Montserrat;" >{{ $request->resolver_remarks ?? 'No remarks available' }}</textarea>
                                            </div>
                                            @if($request->completed_at)
                                            <p><strong>Completed On:</strong> {{ \Carbon\Carbon::parse($request->completed_at)->format('d M Y, h:i A') }}</p>
                                            @endif
                                        @else
                                            <div class="form-group">
                                                <label for="remarks{{ $request->id }}">Remarks <span style="color:#C34545;">*</span></label>
                                                <textarea class="form-control" name="remarks" id="remarks{{ $request->id }}" rows="4" placeholder="Enter completion remarks..." style="background-color:#F1F1F1; font-family: Montserrat;" required>{{ $request->resolver_remarks ?? '' }}</textarea>
                                            </div>
                                            
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        @if(strtolower($request->status) != 'completed')
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-check"></i> Mark Complete
                                        </button>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                   

                    <script>
                        function viewDetails{{ $request->id }}() {
                            jQuery('#viewModal{{ $request->id }}').modal('show');
                        }
                        function openCompleteModal{{ $request->id }}() {
                            jQuery('#completeModal{{ $request->id }}').modal('show');
                        }
                    </script>

                    @empty
                    <tr>
                        <td colspan="11" class="text-center" style="font-family: Montserrat; color:#252525;">No maintenance requests found</td>
                    </tr>
                    @endforelse
                </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script type="text/javascript">
// DataTable with Excel and PDF export buttons - Master Fee Structure Style
jQuery(document).ready(function($) {
    // Wait for DOM to be fully loaded
    setTimeout(function() {
        // Destroy if already initialized
        if ($.fn.DataTable.isDataTable('#maintenanceTable')) {
            $('#maintenanceTable').DataTable().destroy();
        }
        
        // Initialize with buttons - Master Fee Structure Configuration
        var table = $('#maintenanceTable').DataTable({
            scrollX: true,
            responsive: true,
            fixedHeader: false,
            scrollCollapse: false,
            processing: true,
            serverSide: false,
            select: false,
            searching: true,
            destroy: true,
            lengthChange: true,
            paging: true,
            pageLength: 10,
            order: [[0, "asc"]],
            autoWidth: false,
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    title: 'Laptop_Maintenance_Requests',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF',
                    title: 'Laptop Maintenance Requests',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                    },
                    customize: function(doc) {
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 9;
                        doc.styles.tableHeader.fillColor = '#4555C3';
                        doc.styles.title.fontSize = 14;
                        doc.styles.title.alignment = 'center';
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
                    title: 'Laptop Maintenance Requests',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                    }
                },
                {
                    extend: 'copy',
                    text: '<i class="fa fa-copy"></i> Copy',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                    }
                }
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "No entries available",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
        
        console.log('Laptop Maintenance DataTable initialized successfully');
    }, 800);
});
</script>
@endsection
