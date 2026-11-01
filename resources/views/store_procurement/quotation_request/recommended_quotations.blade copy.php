@extends('layouts.app')
@dd("abc");
@section('content')
<div class="container mt-4">
    <h4 class="mb-3">Recommended Quotations – Pending Approval</h4>
    <hr>

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
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotations as $q)
                    @php
                        $rec = $q->recommendations->sortByDesc('created_at')->first();
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

                    <tr id="row-{{ $q->qr_id }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $q->budgetItem->item_title ?? '-' }}</td>
                        <td>{{ $q->budgetHead->head_title ?? '-' }}</td>
                        <td>{{ $vendorQuotation->vendor->vendor_name ?? '-' }}</td>
                        <td>{{ number_format($price, 2) }}</td>
                        <td>{{ $vendorQuotation->currency ?? '-' }}</td>
                        <td><span class="badge badge-info">{{ $approvalBody }}</span></td>
                        <td>
                            {{-- @dd($q) --}}
                            <span class="badge badge-{{ $q->purchaseOrder->approval_status == 'approved' ? 'success' : ($q->approval_status == 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($q->approval_status) ?? 'Pending' }}
                            </span>
                        </td>

                        <td class="text-center">
                            <button type="button" class="btn btn-success btn-sm approve-btn" data-id="{{ $q->po_id }}">
                                <i class="fa fa-check"></i> Approve
                            </button>
                            <button type="button" class="btn btn-danger btn-sm reject-btn ml-1" data-id="{{ $q->po_id }}">
                                <i class="fa fa-times"></i> Reject
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            No recommended quotations pending approval.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    // Approve
    $(".approve-btn").click(function () {
        let id = $(this).data("id");
        $.ajax({
            url: "{{ route('quotation.approve') }}",  // no ID in URL
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                po_id: id
            },
            success: function (res) {
                $("#row-" + id + " td:nth-child(8)").html('<span class="badge badge-success">Approved</span>');
                toastr.success("Quotation approved successfully!");
            },
            error: function () {
                toastr.error("Something went wrong!");
            }
        });
    });

    // Reject
    $(".reject-btn").click(function () {
        let id = $(this).data("id");
        $.ajax({
            url: "{{ route('quotation.reject') }}",  // no ID in URL
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                po_id: id
            },
            success: function (res) {
                $("#row-" + id + " td:nth-child(8)").html('<span class="badge badge-danger">Rejected</span>');
                toastr.info("Quotation rejected.");
            },
            error: function () {
                toastr.error("Something went wrong!");
            }
        });
    });
});
</script>

