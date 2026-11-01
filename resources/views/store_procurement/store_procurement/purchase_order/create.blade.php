@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Create Purchase Order</h4>
    <form id="poForm">
        @csrf

        <div class="mb-3">
            <label>PO Number</label>
            <input type="text" name="po_number" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Vendor</label>
            <select name="vendor_id" class="form-control" required>
                <option value="">-- Select Vendor --</option>
                @foreach($vendors as $vendor)
                    <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="po_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Remarks</label>
            <textarea name="remarks" class="form-control"></textarea>
        </div>

        <h5>Items</h5>
        <table class="table" id="itemTable">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th><button type="button" class="btn btn-sm btn-success" onclick="addRow()">Add</button></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <button type="submit" class="btn btn-primary">Submit PO</button>
    </form>
    <div id="poResponse" class="mt-3"></div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function addRow() {
    $('#itemTable tbody').append(`
        <tr>
            <td><input type="text" name="items[][item_name]" class="form-control" required></td>
            <td><input type="number" name="items[][quantity]" class="form-control" required></td>
            <td><input type="number" name="items[][unit_price]" class="form-control" required></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">Remove</button></td>
        </tr>
    `);
}

function removeRow(btn) {
    $(btn).closest('tr').remove();
}

$('#poForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '{{ route("purchase-order.store") }}',
        type: 'POST',
        data: $(this).serialize(),
        success: function(res) {
            $('#poResponse').html(`<div class="alert alert-success">${res.message}</div>`);
            $('#poForm')[0].reset();
            $('#itemTable tbody').html('');
        },
        error: function(xhr) {
            let msg = '<ul>';
            $.each(xhr.responseJSON.errors, function(k, v) {
                msg += `<li>${v[0]}</li>`;
            });
            msg += '</ul>';
            $('#poResponse').html(`<div class="alert alert-danger">${msg}</div>`);
        }
    });
});
</script>
@endsection
