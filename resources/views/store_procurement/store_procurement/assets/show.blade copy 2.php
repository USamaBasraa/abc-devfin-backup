@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />

<div class="container">
    <h2 class="mb-3">Asset Details</h2>

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">

            <h5 class="fw-bold">{{ $asset->name }} ({{ $asset->asset_code }})</h5>
            <p><strong>Category:</strong> {{ $asset->category }}</p>
            <p><strong>Brand:</strong> {{ $asset->brand }} | <strong>Model:</strong> {{ $asset->model }}</p>
            <p><strong>PO ID:</strong> {{ $asset->po_id }}</p>
            <p><strong>Order Qty:</strong> {{ $asset->order_quantity }}</p>
            <p><strong>Received Qty:</strong> {{ $asset->received_quantity }}</p>
            <p><strong>Received Status:</strong> {{ $asset->received_status }}</p>
            <p><strong>Inspection:</strong> {{ $asset->inspection_status }}</p>
            <p><strong>Warranty:</strong> {{ $asset->warranty_period }}</p>
            <p><strong>Warranty Validity:</strong>
                {{ $asset->warranty_start_date }} → {{ $asset->warranty_end_date }}
            </p>
            <p><strong>Status:</strong> {{ $asset->status }}</p>
        </div>
    </div>

    <h4 class="mb-3">Asset Items (Serial Numbers & Inspection)</h4>
    <form id="bulkAllocateForm">
        @csrf
        <table class="table table-bordered table-hover shadow-sm align-middle" id="itemsTable">
            <thead class="table-primary text-center">
                <tr>
                    <th>#</th>
                    <th>Serial No</th>
                    <th>Inspection</th>
                    <th>Unique Asset Code</th>
                    <th>Allocation Dept</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($asset->items as $index => $item)
                <tr id="item-{{ $item->asset_item_id }}">
                    <td class="text-center">
                        @if($item->inspection_status === 'Passed' && $item->allocation_status !== 'Allocated')
                            <input type="checkbox" name="selected_items[]" value="{{ $item->asset_item_id }}" class="form-check-input">
                        @endif
                        {{ $loop->iteration }}
                    </td>
                    <td>{{ $item->serial_number }}</td>
                    <td class="inspection-status">
                        {{ $item->inspection_status ?? 'Pending' }}
                    </td>
                    <td class="unique-code">{{ $item->unique_asset_code ?? '-' }}</td>
                    <td class="allocation-dept">{{ $item->allocation_department ?? '-' }}</td>
                    <td class="allocation-location">{{ $item->allocation_location ?? '-' }}</td>
                    <td class="allocation-status">
                        {{ $item->allocation_status ?? 'Unallocated' }}
                    </td>
                    <td>
                        @if($item->inspection_status !== 'Passed')
                            {{-- Inspection Pending --}}
                            <select class="form-control form-control-sm d-inline w-auto inspection-select" data-id="{{ $item->asset_item_id }}">
                                <option value="Passed">Pass</option>
                                <option value="Failed">Fail</option>
                            </select>
                            <button class="btn btn-success btn-sm update-inspection" data-id="{{ $item->asset_item_id }}">Update</button>
                        @else
                            {{-- If already inspected, show allocate button --}}
                            @if($item->allocation_status !== 'Allocated')
                                <button type="button" class="btn btn-primary btn-sm allocate-asset"
                                        data-id="{{ $item->asset_item_id }}"
                                        data-serial="{{ $item->serial_number }}">
                                    Allocate
                                </button>
                            @else
                                <span class="badge bg-info">Allocated</span>
                            @endif
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Bulk Allocate Button --}}
        <div class="mt-3">
            <button type="button" class="btn btn-warning" id="bulkAllocateBtn" disabled>
                Allocate Selected
            </button>
        </div>
    </form>
</div>

{{-- Modal for Single / Bulk Allocation --}}
<div class="modal fade" id="allocateModal" tabindex="-1" aria-labelledby="allocateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="allocateForm">
        @csrf
        <input type="hidden" id="allocate_item_ids" name="asset_item_ids">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Allocate Asset(s)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Serial(s):</strong> <span id="serial_display"></span></p>

                <div class="mb-3">
                    <label class="form-label">Department</label>
                    <select class="form-control" name="allocation_department" required>
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                        @endforeach
                    </select>
                </div>


                 <!-- Assignee Body -->

                            <div class="form-group">
                                <label class="form-label">Assignee</label>
                                <select class="form-control"id="assignee_user_id" name="assignee_user_id"  required>
                                    <option value="">Select Assignee</option>
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}">{{$user->prefix}} {{$user->full_name}}</option>
                                    @endforeach
                                </select>
                            </div>









                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="allocation_location" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Allocate</button>
            </div>
        </div>
    </form>
  </div>
</div>

@endsection



{{-- Load jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {

    // Inspection update
    $(document).on('click', '.update-inspection', function () {
        let itemId = $(this).data('id');
        let status = $(`.inspection-select[data-id="${itemId}"]`).val();

        $.ajax({
            url: "{{ url('/assets/items') }}/" + itemId + "/inspection",
            type: "POST",
            dataType: "json",
            data: {
                _token: "{{ csrf_token() }}",
                inspection_status: status
            },
            success: function (response) {
                if (response.success) {
                    let row = $(`#item-${itemId}`);
                    row.find('.inspection-status').text(response.item.inspection_status);
                    row.find('.unique-code').text(response.item.unique_asset_code ?? '-');
                    row.find('.allocation-status').text(response.item.allocation_status);

                    row.find('.update-inspection').remove();
                    row.find('.inspection-select').remove();
                    row.find('td:last').append('<button class="btn btn-primary btn-sm allocate-asset" data-id="'+itemId+'" data-serial="'+response.item.serial_number+'">Allocate</button>');
                }
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr.responseText);
                alert("Error: " + xhr.status + " " + xhr.statusText);
            }
        });
    });

    // Show allocate modal
    $(document).on('click', '.allocate-asset', function () {
        let itemId = $(this).data('id');
        let serial = $(this).data('serial');
        $('#allocate_item_id').val(itemId);
        $('#serial_display').text(serial);
        $('#allocateModal').modal('show');
    });

    // Handle allocation form
    $('#allocateForm').on('submit', function (e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            url: "{{ url('assets/allocate') }}",
            type: "POST",
            data: formData,
            success: function (response) {
                if (response.success) {
                    let row = $(`#item-${response.item.asset_item_id}`);
                    row.find('.allocation-dept').text(response.item.allocation_department);
                    row.find('.allocation-location').text(response.item.allocation_location);
                    row.find('.allocation-status').text('Allocated');
                    row.find('td:last').html('<span class="badge bg-info">Allocated</span>');

                    $('#allocateModal').modal('hide');
                }
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr.responseText);
                alert("Error: " + xhr.status + " " + xhr.statusText);
            }
        });
    });

});
</script>
