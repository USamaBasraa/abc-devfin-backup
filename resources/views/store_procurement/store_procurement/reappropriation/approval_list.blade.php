@extends('layouts.app')

@section('content')
@include('components.toast-notifications')
<link href="/css/bnu-layout.css" rel="stylesheet" />

<div class="container-fluid mt-5 px-4">
    <div class="card shadow-sm border-0">
<div class="container-fluid">
    <h4 class="mb-4">Reappropriation Approvals</h4>

    {{-- Tabs Header --}}
    <ul class="nav nav-tabs" id="approvalTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pending-tab" data-toggle="tab" href="#pending" role="tab">Pending</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="approved-tab" data-toggle="tab" href="#approved" role="tab">Approved</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="declined-tab" data-toggle="tab" href="#declined" role="tab">Declined</a>
        </li>
    </ul>

    {{-- Tabs Content --}}
    <div class="tab-content mt-3" id="approvalTabsContent">
        {{-- Pending Tab --}}
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            @include('store_procurement.reappropriation.partials.table', [
                'items' => $pendingItems,
                'status' => 'pending'
            ])
        </div>

        {{-- Approved Tab --}}
        <div class="tab-pane fade" id="approved" role="tabpanel">
            @include('store_procurement.reappropriation.partials.table', [
                'items' => $approvedItems,
                'status' => 'approved'
            ])
        </div>

        {{-- Declined Tab --}}
        <div class="tab-pane fade" id="declined" role="tabpanel">
            @include('store_procurement.reappropriation.partials.table', [
                'items' => $declinedItems,
                'status' => 'declined'
            ])
        </div>
    </div>
</div>
</div>


<!-- Remarks Modal -->
<div class="modal fade" id="remarksModal" tabindex="-1" role="dialog" aria-labelledby="remarksModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="remarksModalLabel">Provide Remarks</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="remarks-item-id">
        <input type="hidden" id="remarks-action">
        <div class="form-group">
          <label for="remarks-text">Remarks (optional):</label>
          <textarea id="remarks-text" class="form-control" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="submit-remarks" class="btn btn-primary">Submit</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

@endsection
@section('javascript')
<script>

    $(document).on('click', '.approve-btn, .decline-btn', function() {
    let itemId = $(this).data('item-id');
    let action = $(this).hasClass('approve-btn') ? 'approve' : 'decline';

    $('#remarks-item-id').val(itemId);
    $('#remarks-action').val(action);
    $('#remarks-text').val('');
    $('#remarksModal').modal('show');
});

$('#submit-remarks').on('click', function() {
    let itemId = $('#remarks-item-id').val();
    let action = $('#remarks-action').val();
    let remarks = $('#remarks-text').val();

    $.ajax({
        url: action === 'approve' ? "{{ route('reappropriation.approve') }}" : "{{ route('reappropriation.decline') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            item_id: itemId,
            remarks: remarks
        },
        success: function(res) {
            $('#remarksModal').modal('hide');
            if (res.success) {
                 showToast('success', 'Data submitted successfully.', 'Success');
                // alert(res.message);
                $('#item-row-' + itemId).fadeOut(400);
                 setTimeout(function() {
            location.reload();
        }, 2000);
            }
            // else //alert(res.message);
            //   showToast('success', 'Data submitted successfully.', 'Success');
        },
        error: () => {
          showToast('error', 'Something went wrong while submitting.', 'Error');
             }
        // error: () =>   showToast('success', 'sdfgdsfgsdfg', 'Success');
        //alert('Error submitting remarks.')
    });
});

// $(document).on('click', '.approve-btn', function() {
//     let itemId = $(this).data('item-id');
//     if (!confirm('Approve this item?')) return;

//     $.ajax({
//         url: "{{ route('reappropriation.approve') }}",
//         method: "POST",
//         data: { _token: "{{ csrf_token() }}", item_id: itemId },
//         success: function(res) {
//             if (res.success) {
//                 alert(res.message);
//                 $('#item-row-' + itemId).fadeOut(400);
//             } else alert(res.message);
//         },
//         error: () => alert('Error approving item.')
//     });
// });

// $(document).on('click', '.decline-btn', function() {
//     let itemId = $(this).data('item-id');
//     if (!confirm('Decline this item?')) return;

//     $.ajax({
//         url: "{{ route('reappropriation.decline') }}",
//         method: "POST",
//         data: { _token: "{{ csrf_token() }}", item_id: itemId },
//         success: function(res) {
//             if (res.success) {
//                  showToast('success', res.message, 'Success');
//                 // alert(res.message);
//                 $('#item-row-' + itemId).fadeOut(400);
//             } else alert(res.message);
//         },
//            error: () => {
//           showToast('error', 'Something went wrong while submitting.', 'Error');
//              }
//     });
// });
</script>
@stop
