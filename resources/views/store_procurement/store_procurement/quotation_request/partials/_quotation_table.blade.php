<div class="table-responsive shadow-sm rounded">
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Category</th>
                <th>Vendor</th>
                <th>Price</th>
                <th>Currency</th>
                <th>Approval Body</th>
                <th>Status</th>
                @if($status === 'pending')
                    <th class="text-center">Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($quotations as $q)
            @dd($quotations);
                @php
                    $vendorQuotation = $q->vendorQuotations->first();
                    $price = $vendorQuotation->price ?? 0;

                    if ($price <= 50000) {
                        $approvalBody = "Head of Department";
                    } elseif ($price <= 200000) {
                        $approvalBody = "General Manager";
                    } elseif ($price <= 500000) {
                        $approvalBody = "Purchasing Committee";
                    } else {
                        $approvalBody = "Vice Chancellor";
                    }
                @endphp

                <tr id="row-{{ $q->po_id }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $q->budgetItem->item_title ?? '-' }}</td>
                    <td>{{ $q->budgetHead->head_title ?? '-' }}</td>
                    <td>{{ $vendorQuotation->vendor->vendor_name ?? '-' }}</td>
                    <td>{{ number_format($price, 2) }}</td>
                    <td>{{ $vendorQuotation->currency ?? '-' }}</td>
                    <td><span class="badge badge-info">{{ $approvalBody }}</span></td>
                    <td>
                        <span class="badge badge-{{ $q->approval_status == 'approved' ? 'success' : ($q->approval_status == 'rejected' ? 'danger' : 'warning') }}">
                            {{ ucfirst($q->approval_status) }}
                        </span>
                    </td>
                    @if($status === 'pending')
                        <td class="text-center">
                            <button type="button" class="btn btn-success btn-sm approve-btn" data-id="{{ $q->po_id }}">
                                <i class="fa fa-check"></i> Approve
                            </button>
                            <button type="button" class="btn btn-danger btn-sm reject-btn ml-1" data-id="{{ $q->po_id }}">
                                <i class="fa fa-times"></i> Reject
                            </button>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $status === 'pending' ? 9 : 8 }}" class="text-center text-muted">
                        No {{ ucfirst($status) }} quotations.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
