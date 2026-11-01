@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm mb-4">
    <div class="card-body">
    <h4>Vendor Quotations for: <strong>{{ $quotation->item_name }}</strong></h4>

    <p><strong>R ID:</strong> {{ $quotation->qr_id ?? '-' }}</p>

    @if(empty($quotation->po_id))
        {{-- Show Select button --}}
        <p class="mt-1 mb-0"><strong>No Purchase Order Yet :</strong>
    @else
        {{-- Show Go to Purchase Order button --}}
        <a href="{{ route('purchase-orders.show', $quotation->po_id) }}" class="btn btn-success btn-sm">
            Go to Purchase Order
        </a>

        {{-- Show PO ID --}}
        <p class="mt-1 mb-0"><strong>PO ID:</strong> {{ $quotation->po_id }}</p>
    @endif
                <h5>Item Details</h5>
                <table class="table table-sm table-bordered">
                    <tbody>
                        {{-- @dd($quotation) --}}
                        <tr>
                            <th>Item Name</th>
                            <td>{{ $quotation->budgetItem->item_title ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Category (Head)</th>
                            <td>{{ $quotation->budgetHead->head_title ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Quantity</th>
                            <td>{{ $quotation->budgetItem->quantity ?? '-' }}</td>
                        </tr>
                        {{-- <tr>
                            <th>Rate</th>
                            <td>{{ number_format($quotation->budgetItem->rate ?? 0, 2) }}</td>
                        </tr>--}}
                        <tr>
                            <th>Total Amount</th>
                            <td>{{ number_format(($quotation->budgetItem->quantity * $quotation->budgetItem->rate) ?? 0, 2) }}</td>
                        </tr> 
                        <tr>
                            <th>Budget Type</th>
                            <td>{{ ucfirst($quotation->budgetItem->budget_type ?? '-') }}</td>
                        </tr>
                        <tr>
                            <th>Purchase Mode</th>
                            <td>{{ ucfirst($quotation->budgetItem->purchase_mode ?? '-') }}</td>
                        </tr>
                        <tr>
                            <th>Item Remarks</th>
                            <td>{{ $quotation->budgetItem->remarks ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
    {{-- <p><strong>Category:</strong> {{ $quotation->vendorCategory->category_name ?? '-' }}</p>
    <p><strong>Budget Type:</strong> {{ $quotation->budget_type }}</p>
    <p><strong>Remarks:</strong> {{ $quotation->remarks ?? '-' }}</p> --}}

    <hr>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Vendor</th>
            <th>Price</th>
            <th>Currency</th>
            <th>Delivery Time</th>
            <th>Warranty</th>
            <th>Specifications</th>
            <th>Other Details</th>
            <th>Attachment</th> {{-- NEW COLUMN --}}
            <th>remondation</th> {{-- NEW COLUMN --}}
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($quotation->vendorQuotations as $q)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $q->vendor->vendor_name ?? '-' }}</td>
                <td>{{ $q->price ?? '-' }}</td>
                <td>{{ $q->currency ?? '-' }}</td>
                <td>{{ $q->delivery_time ?? '-' }}</td>
                <td>{{ $q->warranty ?? '-' }}</td>
                <td>{{ $q->specifications ?? '-' }}</td>
                <td>{{ $q->remarks ?? '-' }}</td>
                <td>
                    @if($q->attachment)
                        <a href="{{ ('/' . $q->attachment) }}" target="_blank" class="btn btn-sm btn-info">
                            <i class="bi bi-paperclip"></i> View
                        </a>
                    @else
                        <span class="text-muted">No file</span>
                    @endif
                </td>

                    {{-- ✅ Department Recommendation Column --}}
               <td>
                    @php
                        $latestRec = $q->recommendations->sortByDesc('created_at')->first();
                    @endphp

                    @if($latestRec)
                        <strong>{{ $latestRec->department->dept_name ?? '-' }}</strong><br>
                        @if($latestRec->recommended_flag == 'recommended')
                            <span class="badge badge-success">Recommended</span>
                        @elseif($latestRec->recommended_flag == 'not_recommended')
                            <span class="badge badge-danger">Not Recommended</span>
                        @else
                            <span class="badge badge-warning">Conditional</span>
                        @endif
                        <br>
                        <small>{{ $latestRec->remarks ?? '-' }}</small>
                    @else
                        <span class="text-muted">No recommendation yet</span>
                    @endif
                </td>


                <td>{{ \Carbon\Carbon::parse($q->created_at)->format('Y-m-d') }}</td>
                <td>
                    @if(empty($quotation->po_id))
                        <button class="btn btn-primary btn-sm"
                            onclick="openPOConfirmModal(
                                {{ $q->vq_id }},
                                '{{ addslashes($q->vendor->vendor_id) }}',
                                '{{ addslashes($q->vendor->vendor_name) }}',
                                {{ $q->price }},
                                 '{{ addslashes($q->currency ) }}',
                                '{{ addslashes($q->warranty ?? '') }}',
                                '{{ addslashes($q->delivery_time ?? '') }}',
                                '{{ number_format(($quotation->budgetItem->quantity * $quotation->budgetItem->rate) ?? 0, 2) }}'
                            )">
                            Select
                        </button>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="text-center">No vendor quotations submitted yet.</td>
            </tr>
        @endforelse
    </tbody>
</table>
<hr>



<hr>
            {{-- Recommendations --}}
            <h5>Recommendations</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Recommendation</th>
                        <th>Remarks</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotation->recommendations as $rec)
                        <tr>
                            <td>{{ $rec->department->dept_name ?? '-' }}</td>
                            <input type="hidden" name="vq_id" id="vq_id">

                            <td>
                                @if($rec->recommended_flag == 'recommended')
                                    <span class="badge badge-success">Recommended</span>
                                @elseif($rec->recommended_flag == 'not_recommended')
                                    <span class="badge badge-danger">Not Recommended</span>
                                @else
                                    <span class="badge badge-warning">Conditional</span>
                                @endif
                            </td>
                            <td>{{ $rec->remarks ?? '-' }}</td>
                            <td>{{ $rec->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No recommendations yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>


    </div>
 </div>
</div>


<div class="modal fade" id="poConfirmModal" tabindex="-1" role="dialog" aria-labelledby="poConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="poForm">
            @csrf
            <input type="hidden" name="unit_price" id="poUnitPriceValue">
            <input type="hidden" name="total_price" id="poTotalPriceValue">
            <input type="hidden" name="currency" id="poCurrencyValueInput"> {{-- hidden for form submit --}}
            <input type="hidden" name="vendor_quotation_id" id="poVendorQuotationId"> {{-- hidden for form submit --}}

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="poConfirmModalLabel">Confirm Purchase Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p><strong>Vendor:</strong> <span id="poVendorName"></span></p>
                    <p><strong>Unit Price:</strong> <span id="poUnitPrice"></span></p>
                    <p><strong>Currency:</strong> <span id="poCurrencyValue"></span></p> {{-- visible to user --}}
                    <p><strong>Budget:</strong> <span id="available_budget"></span></p> {{-- visible to user --}}


                    <input type="hidden" name="qr_id" class="form-control" id="qr_id" value="{{ $quotation->qr_id }}" required min="1">

                    <div class="form-group">
                        <label for="poQuantity">Quantity</label>
                        <input type="number" name="quantity" class="form-control" id="poQuantity" required min="1">
                    </div>
                    <div id="budgetFlag" class="alert alert-danger d-none" role="alert">
                        ⚠️ This purchase order exceeds the available budget! (Unbudgeted)
                    </div>

                    <div class="form-group">
                        <label for="poTotalPrice">Total Price  </label>
                        <input type="text" class="form-control" id="poTotalPrice" readonly>
                    </div>

                    <div class="form-group">
                        <label for="warranty" class="form-label">Warranty</label>
                        <input type="text" name="warranty" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="delivery_time" class="form-label">Delivery Time</label>
                        <input type="text" name="delivery_time" class="form-control" readonly>
                    </div>

                    <input type="hidden" name="vendor_id" id="poVendorId" required>

                    <div class="form-group">
                        <label>Vendor</label>
                        <p id="poVendorNameDisplay" class="form-control-plaintext"></p>
                    </div>

                    <div class="form-group">
                        <label for="remarks">Other Details Remarks</label>
                        <textarea name="remarks" class="form-control" id="remarks"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Submit PO</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#recForm').on('submit', function(e){
    e.preventDefault();
    $.post('{{ route("recommendation.store") }}', $(this).serialize(), function(res){
        alert(res.message);
        location.reload();
    }).fail(function(xhr){
        alert(xhr.responseJSON.message ?? 'Error submitting recommendation');
    });
});
function openPOConfirmModal(vqId, vendor_id, vendorName, price, currency, warranty, deliveryTime, budget) {
    $('#poVendorQuotationId').val(vqId);
    $('#poVendorName').text(vendorName);
    $('#poUnitPrice').text(price);
    $('#available_budget').val(budget);

    $('#poCurrencyValue').text(currency);
    $('#poCurrencyValueInput').val(currency);
    $('#poUnitPriceValue').val(price);
    $('#poQuantity').val('');
    $('#poTotalPrice').val('');
    $('#poVendorId').val(vendor_id);
    $('input[name="warranty"]').val(warranty);
    $('input[name="delivery_time"]').val(deliveryTime);

    $('#budgetFlag').addClass('d-none'); // Hide flag initially

    // Open modal
    $('#poConfirmModal').modal('show');

    // Event listener for quantity change
// Event listener for quantity change
        $('#poQuantity').off('input').on('input', function () {
            const quantity = parseFloat($(this).val()) || 0;
            const unitPrice = parseFloat($('#poUnitPriceValue').val()) || 0;
            const total = quantity * unitPrice;

            // Remove commas and convert to float
            const availableBudget = parseFloat(String(budget).replace(/,/g, '')) || 0;

            $('#poTotalPrice').val(total.toFixed(2));
            $('#poTotalPriceValue').val(total.toFixed(2));

            // Check budget condition
            if (total > availableBudget) {
                $('#budgetFlag').removeClass('d-none');
            } else {
                $('#budgetFlag').addClass('d-none');
            }
        });

}

// function openPOConfirmModal(vqId, vendor_id , vendorName, price,currency, warranty, deliveryTime, budget) {
//     $('#poVendorQuotationId').val(vqId);

//     $('#poVendorName').text(vendorName);

//     $('#poUnitPrice').text(price);
//     $('#available_budget').text(budget);


//     $('#budgetFlag').addClass('d-none'); // Hide flag initially
//       // Show currency in visible span
//     $('#poCurrencyValue').text(currency);

//     // Set hidden input for backend
//     $('#poCurrencyValueInput').val(currency);

//     $('#poUnitPriceValue').val(price);
//     $('#poQuantity').val('');
//     $('#poTotalPrice').val('');
//     $('#poVendorId').val(vendor_id);
//     // Set warranty and delivery time
//     $('input[name="warranty"]').val(warranty);
//     $('input[name="delivery_time"]').val(deliveryTime);
    

//     $('#poConfirmModal').modal('show');


//     // Event listener for quantity change
//     $('#poQuantity').off('input').on('input', function () {
//         const quantity = parseFloat($(this).val()) || 0;
//         const unitPrice = parseFloat($('#poUnitPriceValue').val()) || 0;
//         const total = quantity * unitPrice;
//         const availableBudget = parseFloat(budget) || 0;

//         $('#poTotalPrice').val(total.toFixed(2));
//         $('#poTotalPriceValue').val(total.toFixed(2));

//         // Check budget condition
//         if (total > availableBudget) {
//             $('#budgetFlag').removeClass('d-none');
//         } else {
//             $('#budgetFlag').addClass('d-none');
//         }
    // });
// }


$(document).on('input', '#poQuantity', function () {
    const unitPrice = parseFloat($('#poUnitPriceValue').val());
    const qty = parseInt($(this).val());
    if (!isNaN(unitPrice) && !isNaN(qty)) {
        const total = (unitPrice * qty).toFixed(2);
        $('#poTotalPrice').val(total);
        $('#poTotalPriceValue').val(total); // <-- send to backend
    } else {
        $('#poTotalPrice').val('');
        $('#poTotalPriceValue').val('');
    }
});



$('#poForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '{{ route("purchase-order.store") }}',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            alert(response.message);
            $('#poForm')[0].reset();
            $('#poConfirmModal').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            let errors = xhr.responseJSON.errors;
            let msg = '<ul>';
            $.each(errors, function(k, v) {
                msg += `<li>${v[0]}</li>`;
            });
            msg += '</ul>';
            alert(msg);
        }
    });
});
</script>
@endsection
