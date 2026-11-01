<!-- @extends('layouts.app') -->

<!-- @section('content') -->
<link href="/css/bnu-layout.css" rel="stylesheet" />
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">🚪 Create Gate Pass for PO #{{ $po->po_id }}</h4>
        </div>
        <div class="card-body">
            {{-- Add action + method --}}
            <form id="gatepassForm" action="{{ route('gatepass.store') }}" method="POST">
                @csrf

                {{-- Hidden inputs --}}
                <input type="hidden" name="budget_head_id" value="{{ $po->items[0]->bh_id }}">
                <input type="hidden" name="item_remarks" value="{{ $po->items[0]->remarks }}">
                <input type="hidden" name="budget_year_id" value="{{ $po->items[0]->ay_id }}">
                <input type="hidden" name="budget_type" value="{{ $po->items[0]->budget_type }}">
                <input type="hidden" name="item_title" value="{{ $po->items[0]->item_title }}">
                <input type="hidden" name="total_price" value="{{ $po->total_price }}">
                <input type="hidden" name="vendor_id" value="{{ $po->vendor_id }}">
                <input type="hidden" name="warranty" value="{{ $po->warranty }}">
                <input type="hidden" name="vendor_quotation_id" value="{{ $po->vendor_quotation_id }}">
                <input type="hidden" name="po_id" value="{{ $po->po_id }}">
                <input type="hidden" name="order_quantity" value="{{$po->quantity - $po->delivered_quantity }}">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Delivery Person</label>
                        <input type="text" name="delivery_person" class="form-control shadow-sm" placeholder="Enter full name" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">CNIC</label>
                        <input type="text" name="cnic" class="form-control shadow-sm" placeholder="xxxxx-xxxxxxx-x" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Contact No</label>
                        <input type="text" name="contact_no" class="form-control shadow-sm" placeholder="03xx-xxxxxxx" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Vehicle No</label>
                        <input type="text" name="vehicle_no" class="form-control shadow-sm" placeholder="e.g. LEB-1234">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Remarks</label>
                        <input type="text" name="remarks" class="form-control shadow-sm" placeholder="Optional remarks">
                    </div>
                </div>

                <h5 class="fw-bold mb-3">📦 Items</h5>
                <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle shadow-sm">
                        <thead class="table-primary text-center">

                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Ordered Qty</th>
                                <th>Remaining Qty</th>
                                <th>Delivered Qty</th>
                                <th>Serial Number(s)</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($po->quotationRequests as $qr)
                                <tr>
                                    {{-- @dd($po) --}}
                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                    <td>{{ $qr->budgetItem->item_title ?? '-' }}</td>
                                    <td class="text-center">{{ $po->quantity ?? '-' }}</td>
                                    <td class="text-center">{{ $po->remaining_quantity ?? '-' }}</td>
                                    {{-- <td>
                                        <input type="hidden" name="items[{{ $loop->index }}][item_id]" value="{{ $qr->budgetItem->item_id }}">

                                        <input type="number"
                                               name="items[{{ $loop->index }}][delivered_qty]"
                                               class="form-control shadow-sm"
                                               min="1"
                                               max="{{ $po->quantity }}"
                                               required>
                                    </td> --}}
                                    <td>
                                        <input type="hidden" name="items[{{ $loop->index }}][item_id]" value="{{ $qr->budgetItem->item_id }}">
                                        <input type="number"
                                            name="items[{{ $loop->index }}][delivered_qty]"
                                            class="form-control delivered-qty shadow-sm"
                                            min="1"
                                            max="{{ $po->quantity }}"
                                            data-index="{{ $loop->index }}"
                                            required>
                                    </td>
                                    <td>
                                        {{-- <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                                @for($i = 0; $i < $po->quantity - $po->delivered_quantity; $i++)
                                                    <tr>
                                                        <td>
                                                            <input type="text"
                                                                   name="items[{{ $loop->index }}][serials][{{ $i }}]"
                                                                   class="form-control shadow-sm"
                                                                   placeholder="e.g. SN12345"
                                                                   >
                                                        </td>
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table> --}}

                                 <div class="card p-3 serials-card">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody class="serials-body">
                                                <tr>
                                                    <td>
                                                        <input type="text"
                                                            name="items[{{ $loop->index }}][serials][]"
                                                            class="form-control shadow-sm"
                                                            placeholder="e.g. SN12345">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <div class="mt-2 text-end">
                                            <button type="button" class="btn btn-sm btn-success add-row">+</button>
                                            <button type="button" class="btn btn-sm btn-danger remove-row">−</button>
                                        </div>
                                    </div>




                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success px-4 py-2 shadow-sm">
                        💾 Save Gate Pass
                    </button>
                </div>
            </form>

            {{-- Response message --}}
            <div id="responseMessage" class="mt-3"></div>
        </div>
    </div>
</div>
<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
<!-- @endsection -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Add CSRF token for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ✅ FORM SUBMIT
    $('#gatepassForm').on('submit', function(e) {
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: "{{ route('gatepass.store') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                showToast('success', response.message);
                // $('#responseMessage').html(
                //     '<div class="alert alert-success">' + response.message + '</div>'
                // );
                // $('#gatepassForm')[0].reset();
            },
             error: function (xhr) {
                if (xhr.status === 422) {
                    // ✅ Show only first error
                    let firstError = Object.values(xhr.responseJSON.errors)[0][0];
                    showToast('error', firstError);
                   
                } else {
                    showToast('error', 'Something went wrong!');
                  
                }
            }
        });
    });

    // ✅ ADD / REMOVE ROWS
$(document).on('click', '.add-row', function () {
    console.log('Add-row clicked ✅');

    const card = $(this).closest('.serials-card');
    console.log('Card found?', card.length);

    const tbody = card.find('.serials-body');
    console.log('Tbody found?', tbody.length);

    const firstInput = tbody.find('input').first();
    console.log('First input name:', firstInput.attr('name'));

    const inputName = firstInput.attr('name') || 'items[0][serials][]';

    const newRow = `
        <tr>
            <td>
                <input type="text"
                    name="${inputName}"
                    class="form-control shadow-sm"
                    placeholder="e.g. SN12345">
            </td>
        </tr>`;
    tbody.append(newRow);
});



    $(document).on('click', '.remove-row', function () {

          console.log('Add-row clicked ✅');

        const card = $(this).closest('.serials-card');
        const tbody = card.find('.serials-body');
        const rows = tbody.find('tr');
        if (rows.length > 1) {
            rows.last().remove();
        }
    });



     // ✅ Count updater
    function updateSerialCount(card) {
        const count = card.find('.serials-body input').length;
        card.find('.serial-count').text(count);
    }
});
</script>

