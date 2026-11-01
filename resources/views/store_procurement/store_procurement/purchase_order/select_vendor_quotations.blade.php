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
            <th>Item Title</th>
            <th class="text-right">Unit Rate</th>
            <th class="text-right">Vendor Total</th>
            <th>Currency</th>
            <th>Advance Payment</th>
            <th class="text-right">Advance %</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($quotation->vendorQuotations as $q)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $q->vendor->vendor_name ?? '-' }}</td>
                <td>{{ $q->item_title ?? '-' }}</td>
                <td class="text-right">
                    @if(!is_null($q->rate))
                        {{ number_format($q->rate, 2) }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">
                    @if(!is_null($q->price))
                        {{ number_format($q->price, 2) }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $q->currency ?? '-' }}</td>
                <td>{{ ucfirst($q->advance_required ?? 'no') }}</td>
                <td class="text-right">
                    @if(!is_null($q->advance_percent))
                        {{ number_format($q->advance_percent, 2) }}%
                    @else
                        -
                    @endif
                </td>
                <td>{{ optional($q->created_at)->format('Y-m-d') ?? '-' }}</td>
                <td>
                    @if(empty($quotation->po_id))
                        <button class="btn btn-primary btn-sm"
                            onclick="openPOConfirmModal(
                                {{ $q->vq_id }},
                                '{{ addslashes($q->vendor->vendor_id) }}',
                                '{{ addslashes($q->vendor->vendor_name) }}',
                                {{ $q->rate ?? 0 }},
                                 '{{ addslashes($q->currency ?? '') }}',
                                '{{ addslashes($q->warranty ?? '') }}',
                                '{{ addslashes($q->delivery_time ?? '') }}',
                                '{{ number_format(($quotation->budgetItem->quantity * $quotation->budgetItem->rate) ?? 0, 2) }}',
                                '{{ ucfirst($quotation->budgetItem->budget_type ?? '-') }}',
                                '{{ addslashes($q->item_title ?? '') }}',
                                '{{ addslashes($q->brand ?? '') }}',
                                '{{ addslashes($q->model ?? '') }}',
                                '{{ $q->quantity ?? '' }}',
                                {{ $q->price ?? 0 }},
                                '{{ addslashes($q->advance_required ?? 'no') }}',
                                '{{ $q->advance_percent ?? '' }}'
                            )">
                            Select
                        </button>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center">No vendor quotations submitted yet.</td>
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
            <input type="hidden" name="budget_type" id="budget_type">
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
                    <div class="bg-light p-3 rounded mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Vendor:</strong> <span id="poVendorName"></span></p>
                                <p class="mb-1"><strong>Item Title:</strong> <span id="poItemTitle"></span></p>
                                <p class="mb-1"><strong>Brand:</strong> <span id="poBrand"></span></p>
                                <p class="mb-1"><strong>Model:</strong> <span id="poModel"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Vendor Quantity:</strong> <span id="poVendorQuantity"></span></p>
                                <p class="mb-1"><strong>Unit Rate:</strong> <span id="poUnitPrice"></span></p>
                                <p class="mb-1"><strong>Vendor Quoted Total:</strong> <span id="poVendorTotal"></span></p>
                                <p class="mb-1"><strong>Advance:</strong> <span id="poAdvanceRequired"></span> <span id="poAdvancePercent"></span></p>
                            </div>
                        </div>
                    </div>
                    <p><strong>Currency:</strong> <span id="poCurrencyValue"></span></p>
                    <p><strong>Available Budget:</strong> <span id="available_budget"></span></p>


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
    function formatNumberDisplay(value, decimals = 2) {
        const num = parseFloat(value);
        if (isNaN(num)) {
            return '-';
        }
        return num.toLocaleString(undefined, {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    }

    function formatCurrencyDisplay(value, currencyCode) {
        const num = parseFloat(value);
        if (isNaN(num)) {
            return '-';
        }
        const code = (currencyCode || '').toString().toUpperCase();
        const symbol = code === 'USD' ? '$' : code === 'EUR' ? '€' : code === 'GBP' ? '£' : '₨';
        return `${symbol} ${num.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })}`;
    }

    function normalizeAdvance(value) {
        const adv = (value || '').toString().toLowerCase();
        if (adv === 'yes') return 'Yes';
        if (adv === 'no') return 'No';
        return '-';
    }

    $('#recForm').on('submit', function(e){
    e.preventDefault();
    $.post('{{ route("recommendation.store") }}', $(this).serialize(), function(res){
        alert(res.message);
        location.reload();
    }).fail(function(xhr){
        alert(xhr.responseJSON.message ?? 'Error submitting recommendation');
    });
});
function openPOConfirmModal(
    vqId,
    vendor_id,
    vendorName,
    unitRate,
    currency,
    warranty,
    deliveryTime,
    budget,
    budget_type,
    itemTitle = '',
    brand = '',
    model = '',
    vendorQuantity = '',
    vendorTotal = '',
    advanceRequired = 'no',
    advancePercent = ''
) {
    const currencyCode = (currency || '').toString().toUpperCase();
    const parsedUnitRate = parseFloat(unitRate) || 0;
    const parsedVendorQuantity = parseFloat(vendorQuantity);
    const parsedVendorTotal = vendorTotal !== '' ? parseFloat(vendorTotal) : (isNaN(parsedVendorQuantity) ? NaN : parsedVendorQuantity * parsedUnitRate);
    const parsedAdvancePercent = advancePercent !== '' ? parseFloat(advancePercent) : NaN;
    const availableBudget = parseFloat(String(budget).replace(/,/g, '')) || 0;

    $('#poVendorQuotationId').val(vqId);
    $('#poVendorName').text(vendorName || '-');
    $('#poVendorNameDisplay').text(vendorName || '-');
    $('#poItemTitle').text(itemTitle || '-');
    $('#poBrand').text(brand || '-');
    $('#poModel').text(model || '-');
    $('#poVendorQuantity').text(formatNumberDisplay(parsedVendorQuantity));
    $('#poVendorTotal').text(formatCurrencyDisplay(parsedVendorTotal, currencyCode));
    $('#poAdvanceRequired').text(normalizeAdvance(advanceRequired));
    $('#poAdvancePercent').text(!isNaN(parsedAdvancePercent) ? `(${formatNumberDisplay(parsedAdvancePercent, 2)}%)` : '');

    $('#poUnitPrice').text(formatCurrencyDisplay(parsedUnitRate, currencyCode));
    $('#poUnitPriceValue').val(parsedUnitRate);

    $('#poCurrencyValue').text(currencyCode || '-');
    $('#poCurrencyValueInput').val(currencyCode);
    $('#available_budget').text(budget || '-');

    $('#poQuantity').val('');
    $('#poTotalPrice').val('');
    $('#poTotalPriceValue').val('');
    $('#poVendorId').val(vendor_id);
    $('input[name="warranty"]').val(warranty || '');
    $('input[name="delivery_time"]').val(deliveryTime || '');
    $('#remarks').val('');

    $('#budget_type').val(budget_type || '');
    $('#budgetFlag').addClass('d-none');

    $('#poConfirmModal').modal('show');

    $('#poQuantity').off('input').on('input', function () {
        const quantity = parseFloat($(this).val()) || 0;
        const total = quantity * parsedUnitRate;

        $('#poTotalPrice').val(quantity ? formatNumberDisplay(total, 2) : '');
        $('#poTotalPriceValue').val(quantity ? total.toFixed(2) : '');

        if (total > availableBudget && (budget_type || '').toLowerCase() === 'budgeted') {
            $('#budget_type').val('Partial Budgeted');
            $('#budgetFlag').removeClass('d-none');
        } else {
            $('#budget_type').val(budget_type || '');
            $('#budgetFlag').addClass('d-none');
        }
    });

    if (!isNaN(parsedVendorQuantity) && parsedVendorQuantity > 0) {
        $('#poQuantity').val(parsedVendorQuantity).trigger('input');
    }
}


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
