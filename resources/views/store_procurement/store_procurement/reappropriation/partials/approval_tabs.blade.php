{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">Reappropriation Approvals</h4>

    {{-- Tabs Header --}}
    {{-- <ul class="nav nav-tabs" id="approvalTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pending-tab" data-toggle="tab" href="#pending" role="tab">Pending</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="approved-tab" data-toggle="tab" href="#approved" role="tab">Approved</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="declined-tab" data-toggle="tab" href="#declined" role="tab">Declined</a>
        </li>
    </ul> --}}

    {{-- Tabs Content --}}
    <div class="tab-content mt-3" id="approvalTabsContent">
        {{-- Pending Tab --}}
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            @include('reappropriation.partials.table', [
                'items' => $pendingItems,
                'status' => 'pending'
            ])
        </div>

        {{-- Approved Tab --}}
        <div class="tab-pane fade" id="approved" role="tabpanel">
            @include('reappropriation.partials.table', [
                'items' => $approvedItems,
                'status' => 'approved'
            ])
        </div>

        {{-- Declined Tab --}}
        <div class="tab-pane fade" id="declined" role="tabpanel">
            @include('reappropriation.partials.table', [
                'items' => $declinedItems,
                'status' => 'declined'
            ])
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script>
$(document).on('click', '.approve-btn', function() {
    let itemId = $(this).data('item-id');
    if (!confirm('Approve this item?')) return;

    $.ajax({
        url: "{{ route('reappropriation.approve') }}",
        method: "POST",
        data: { _token: "{{ csrf_token() }}", item_id: itemId },
        success: function(res) {
            if (res.success) {
                alert(res.message);
                $('#item-row-' + itemId).fadeOut(400);
            } else alert(res.message);
        },
        error: () => alert('Error approving item.')
    });
});

$(document).on('click', '.decline-btn', function() {
    let itemId = $(this).data('item-id');
    if (!confirm('Decline this item?')) return;

    $.ajax({
        url: "{{ route('reappropriation.decline') }}",
        method: "POST",
        data: { _token: "{{ csrf_token() }}", item_id: itemId },
        success: function(res) {
            if (res.success) {
                alert(res.message);
                $('#item-row-' + itemId).fadeOut(400);
            } else alert(res.message);
        },
        error: () => alert('Error declining item.')
    });
});
</script>
@stop --}}
