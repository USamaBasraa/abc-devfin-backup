@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />
<div class="container">
    <h2 class="mb-3">Asset Details</h2>

    <div class="card mb-4">
        <div class="card-body">
            <h5>{{ $asset->name }} ({{ $asset->asset_code }})</h5>
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

    <h4>Asset Items (Serial Numbers & Inspection)</h4>
<table class="table table-bordered" id="itemsTable">
    <thead>
        <tr>
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
        @foreach($asset->items as $item)
        <tr id="item-{{ $item->asset_item_id }}">
            <td>{{ $item->serial_number }}</td>
            <td class="inspection-status">{{ $item->inspection_status ?? 'Pending' }}</td>
            <td class="unique-code">{{ $item->unique_asset_code ?? '-' }}</td>
            <td>{{ $item->allocation_department }}</td>
            <td>{{ $item->allocation_location }}</td>
            <td class="allocation-status">{{ $item->allocation_status }}</td>
            <td>
                @if($item->inspection_status !== 'Passed')
                <select class="form-control form-control-sm d-inline w-auto inspection-select" data-id="{{ $item->asset_item_id }}">
                    <option value="Passed">Pass</option>
                    <option value="Failed">Fail</option>
                </select>
                <button class="btn btn-success btn-sm update-inspection" data-id="{{ $item->asset_item_id }}">Update</button>
                @else
                    <span class="badge bg-success">Inspected</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

</div>
@endsection

    {{-- Load jQuery if not already in layouts.app --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    $(document).ready(function () {
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
                        row.find('td:last').append('<span class="badge bg-success">Inspected</span>');
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
