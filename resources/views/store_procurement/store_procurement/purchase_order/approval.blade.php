@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />
<div class="container mt-4">
    <h4>Purchase Orders Approval</h4>

    <!-- Nav Tabs -->
    <ul class="nav nav-tabs" id="poTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pending-tab" data-toggle="tab" href="#pending" role="tab">Pending</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="approved-tab" data-toggle="tab" href="#approved" role="tab">Approved</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="rejected-tab" data-toggle="tab" href="#rejected" role="tab">Rejected</a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content mt-3">
        <!-- Pending Orders -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            @include('store_procurement.purchase_order.po_table', ['orders' => $pendingOrders])
        </div>

        <!-- Approved Orders -->
        <div class="tab-pane fade" id="approved" role="tabpanel">
            @include('store_procurement.purchase_order.po_table', ['orders' => $approvedOrders])
        </div>

        <!-- Rejected Orders -->
        <div class="tab-pane fade" id="rejected" role="tabpanel">
            @include('store_procurement.purchase_order.po_table', ['orders' => $rejectedOrders])
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function approvePO(id) {
    if (!confirm('Approve this Purchase Order?')) return;

    $.post('{{ route("purchase-order.approve") }}', {
        _token: '{{ csrf_token() }}',
        po_id: id
    }, function(res) {
        alert(res.message);
        location.reload();
    });
}

function rejectPO(id) {
    const remarks = $(`#rejectForm${id} textarea[name=remarks]`).val();
    if (!remarks.trim()) {
        alert('Please provide a rejection reason.');
        return;
    }

    $.post('{{ route("purchase-order.reject") }}', {
        _token: '{{ csrf_token() }}',
        po_id: id,
        remarks: remarks
    }, function(res) {
        alert(res.message);
        location.reload();
    });
}
</script>
@endsection
