<table class="table table-bordered">
    <thead>
        <tr>
            <th>PO Number</th>
            <th>Vendor</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Total Price</th>
            <th>Currency</th>
            <th>Budget Type</th>
            <th>Warranty</th>
            <th>Delivery Time</th>
            <th>Remarks</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $po)
        {{-- @dd($po) --}}
        <tr>
            <td>{{ $po->po_id }}</td>
            <td>{{ $po->vendor->vendor_name ?? '-' }}</td>
            <td>{{ $po->quantity }}</td>
            <td>{{ number_format($po->unit_price, 2) }}</td>
            <td>{{ number_format($po->total_price, 2) }}</td>
            <td>{{ $po->currency }}</td>
            <td>{{ $po->budget_type ?? '-' }}</td>
            <td>{{ $po->warranty ?? '-' }}</td>
            <td>{{ $po->delivery_time ?? '-' }}</td>
            <td>{{ $po->remarks ?? '-' }}</td>
            <td>
                <span class="badge
                    @if($po->approvel_status == 'approved') bg-success
                    @elseif($po->approvel_status == 'rejected') bg-danger
                    @else bg-warning @endif">
                    {{ ucfirst($po->approvel_status) }}
                </span>
            </td>
            <td>
                @if($po->approvel_status == 'pending')
                    <button class="btn btn-success btn-sm" onclick="approvePO({{ $po->po_id }})">Approve</button>
                    <button class="btn btn-danger btn-sm" onclick="$('#rejectModal{{ $po->po_id }}').modal('show')">Reject</button>
                @else
                    <em>Processed</em>
                @endif
            </td>
        </tr>

        <!-- Reject Modal -->
        <div class="modal fade" id="rejectModal{{ $po->po_id }}" tabindex="-1">
            <div class="modal-dialog">
                <form id="rejectForm{{ $po->po_id }}" onsubmit="event.preventDefault(); rejectPO({{ $po->po_id }})">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Reject Purchase Order</h5>
                        </div>
                        <div class="modal-body">
                            <textarea name="remarks" class="form-control" placeholder="Enter rejection reason..." required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn btn-danger">Reject</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <tr>
            <td colspan="11" class="text-center">No records found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
