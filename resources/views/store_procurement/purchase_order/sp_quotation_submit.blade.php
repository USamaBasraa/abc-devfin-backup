@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4>Submit Vendor Quotation</h4>

    @foreach ($requests as $req)
    <div class="card mb-4">
        <div class="card-header">
            <strong>{{ $req->budgetItem->item_title }}</strong> ({{ $req->budgetHead->head_title ?? '-' }})
        </div>
        {{-- @dd($req); --}}
        <div class="card-body">
            <p><strong>Purchase Mode:</strong> {{ ucfirst($req->budgetItem->purchase_mode) }}</p>
            <p><strong>Quantity:</strong> {{ $req->budgetItem->quantity ?? '-' }}</p>
            <p><strong>Remarks:</strong> {{ $req->budgetItem->remarks ?? '-' }}</p>
            @if(is_null($req->po_id))
            <div class="text-center my-4">
                <button class="btn btn-primary btn-lg shadow-sm">
                    <i class="fas fa-envelope"></i> Send Email to Vendors
                </button>
            </div>
            @endif

           @if(is_null($req->po_id))
                <div class="mb-2">
                    <label>Select Vendor</label>
                    <select class="form-control selectVendor" data-request-id="{{ $req->qr_id }}">
                        <option value="">-- Select Vendor --</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                   <a href="{{ route('purchase-orders.show', $req->po_id) }}" class="btn btn-success btn-sm">
                            Go to Purchase Order
                        </a>
            @endif

            <!-- Area to show existing quotation OR the form -->
            <div class="quotation-area" id="quotationArea{{ $req->qr_id }}"></div>
        </div>
    </div>
    @endforeach
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    $('.selectVendor').change(function(){
        let vendorId = $(this).val();
        let requestId = $(this).data('request-id');
        let area = $('#quotationArea' + requestId);

        if(!vendorId) {
            area.html('');
            return;
        }

        // Check if quotation already exists
        $.get('{{ route("vendor.quotation.check") }}', { vendor_id: vendorId, quotation_request_id: requestId }, function(res){
            if(res.exists) {
                // Show existing quotation details
                let q = res.quotation;
                area.html(`
                    <div class="alert alert-info">
                        <strong>Quotation Already Submitted</strong><br>
                        <strong>Price:</strong> ${q.price} ${q.currency}<br>
                        <strong>Delivery Time:</strong> ${q.delivery_time ?? '-'}<br>
                        <strong>Warranty:</strong> ${q.warranty ?? '-'}<br>
                        <strong>Remarks:</strong> ${q.remarks ?? '-'}
                    </div>
                `);
            } else {
                // Show submission form
                area.html(`
                    <form class="quotationForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="quotation_request_id" value="${requestId}">
                        <input type="hidden" name="vendor_id" value="${vendorId}">

                        <div class="mb-2">
                            <label>Price</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="price" class="form-control" required>
                                <select name="currency" class="form-select" style="max-width: 120px;">
                                    <option value="PKR" selected>Rupees</option>
                                    <option value="USD">Dollar</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label>Delivery Time</label>
                            <input type="text" name="delivery_time" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Warranty</label>
                            <input type="text" name="warranty" class="form-control">
                        </div>

                        <div class="mb-2">
                            <label>Specifications</label>
                            <textarea name="specifications" class="form-control" placeholder="Optional specs..."></textarea>
                        </div>

                        <div class="mb-2">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control"></textarea>
                        </div>

                        <div class="mb-2">
                            <label>Attachment (optional)</label>
                            <input type="file" name="attachment" class="form-control">
                        </div>

                        <button class="btn btn-success submitBtn">Submit Quotation</button>
                        <div class="quotationResponse mt-2"></div>
                    </form>
                `);
            }
        });
    });

    // Handle form submission
    $(document).on('submit', '.quotationForm', function(e){
        e.preventDefault();

        let form = $(this)[0];
        let formData = new FormData(form);
        let responseBox = $(this).find('.quotationResponse');

        $.ajax({
            url: '{{ route("vendor.quotation.store") }}',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                responseBox.html(`<div class="alert alert-success">${res.message}</div>`);
                form.reset();
                location.reload();
            },
            error: function (xhr) {
                let errors = xhr.responseJSON.errors;
                let msg = '<ul>';
                $.each(errors, function (key, val) {
                    msg += `<li>${val[0]}</li>`;
                });
                msg += '</ul>';
                responseBox.html(`<div class="alert alert-danger">${msg}</div>`);
            }
        });
    });

});
</script>
@endsection
