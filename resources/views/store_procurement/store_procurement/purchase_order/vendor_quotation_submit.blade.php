@extends('layouts.app')

@section('content')
<div class="container mt-4">
      <div class="card shadow-sm">
        <div class="card-body">


 <h4>Submit Quotations</h4>

@forelse ($requests as $req)
    <div class="card mb-4">
        <div class="card-header">
            <strong>{{ $req->item_name }}</strong> ({{ $req->vendorCategory->category_name ?? '-' }})
        </div>
        <div class="card-body">
            <p><strong>Purchase Mode:</strong> {{ ucfirst($req->purchase_mode) }}</p>
            <p><strong>Model No:</strong> {{ $req->model_no }}</p>
            <p><strong>Remarks:</strong> {{ $req->remarks ?? '-' }}</p>

            @php
                // Check if a quotation already exists for this vendor (selected by staff)
                $existingQuotation = null;
                if(request()->has('vendor_id')) {
                    $existingQuotation = $req->vendorQuotations
                        ->where('vendor_id', request('vendor_id'))
                        ->first();
                }
            @endphp

            @if($existingQuotation)
                <div class="alert alert-info">
                    <strong>Quotation Already Submitted</strong><br>
                    <strong>Price:</strong> {{ $existingQuotation->price }} {{ $existingQuotation->currency }}<br>
                    <strong>Delivery Time:</strong> {{ $existingQuotation->delivery_time }}<br>
                    <strong>Warranty:</strong> {{ $existingQuotation->warranty }}<br>
                    <strong>Remarks:</strong> {{ $existingQuotation->remarks ?? '-' }}
                </div>
            @else
                <form class="quotationForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="quotation_request_id" value="{{ $req->qr_id }}">

                    <div class="mb-2">
                        <label>Vendor</label>
                        <select name="vendor_id" class="form-control" required>
                            <option value="">-- Select Vendor --</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
                            @endforeach
                        </select>
                    </div>

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
                        <textarea name="specifications" class="form-control"></textarea>
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
            @endif
        </div>
    </div>
@empty
    <div class="alert alert-warning">No quotation requests found.</div>
@endforelse


    </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        $('.quotationForm').on('submit', function (e) {
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
