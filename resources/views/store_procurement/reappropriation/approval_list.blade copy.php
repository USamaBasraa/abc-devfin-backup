@extends('layouts.app')

@section('content')
@include('components.toast-notifications')
<link href="/css/bnu-layout.css" rel="stylesheet" />
<div class="container">
    <h4 class="mb-4">Pending Reappropriation Approvals</h4>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Item Name</th>
                <th>Budget Type</th>
                <th>Approval Status</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pendingItems as $index => $item)
                <tr id="item-row-{{ $item->id }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->item_title ?? 'N/A' }}</td>
                    <td>{{ ucfirst($item->budget_type) }}</td>
                    <td>
                        <span class="badge badge-warning">{{ ucfirst($item->reappropriation_approval) }}</span>
                    </td>
                    <td class="text-center">
                        <button 
                            class="btn btn-sm btn-success approve-btn" 
                            data-item-id="{{ $item->item_id }}"
                        >
                            Approve
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No pending reappropriation items.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


@endsection
@section('javascript')
<script>
$(document).on('click', '.approve-btn', function() {
    let itemId = $(this).data('item-id');

    if(!confirm('Are you sure you want to approve this item?')) return;

    $.ajax({
        url: "{{ route('reappropriation.approve') }}",
        type: "POST",
        data: {
            item_id: itemId,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            if (response.success) {
                alert(response.message);
                $('#item-row-' + itemId).fadeOut(500, function() { $(this).remove(); });
            } else {
                alert(response.message);
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert('Error approving item.');
        }
    });
});
</script>

@stop