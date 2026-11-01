@extends('layouts.app')

@section('content')
<div class="container-fluid px-3" style="margin-top: 20px;">
    <h3 class="mb-4">Asset Entry Form</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
  <div class="bg-white p-4 shadow-sm rounded">
    <form method="POST" action="{{ route('assets.store') }}">
        @csrf

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Asset Code *</label>
                <input type="text" name="asset_code" class="form-control" required>
            </div>

            <div class="col-md-4 mb-3">
                <label>Item Title</label>
                <input type="text" name="item_title" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>Name *</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="col-md-4 mb-3">
                <label>Category</label>
                <input type="text" name="category" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>Brand</label>
                <input type="text" name="brand" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>Model</label>
                <input type="text" name="model" class="form-control">
            </div>

            <div class="col-md-3 mb-3">
                <label>Order Quantity</label>
                <input type="number" name="order_quantity" class="form-control">
            </div>

            <div class="col-md-3 mb-3">
                <label>Received Quantity</label>
                <input type="number" name="received_quantity" class="form-control">
            </div>

            <div class="col-md-3 mb-3">
                <label>Received Status</label>
                <select name="received_status" class="form-control">
                    <option value="PENDING">Pending</option>
                    <option value="RECEIVED">Received</option>
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label>Inspection Status</label>
                <select name="inspection_status" class="form-control">
                    <option value="PENDING">Pending</option>
                    <option value="PASSED">Passed</option>
                    <option value="FAILED">Failed</option>
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label>Warranty Period</label>
                <input type="text" name="warranty_period" class="form-control">
            </div>

            <div class="col-md-3 mb-3">
                <label>Warranty Start</label>
                <input type="date" name="warranty_start_date" class="form-control">
            </div>

            <div class="col-md-3 mb-3">
                <label>Warranty End</label>
                <input type="date" name="warranty_end_date" class="form-control">
            </div>

            <div class="col-md-3 mb-3">
                <label>Purchase Date</label>
                <input type="date" name="purchase_date" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>Currency</label>
                <input type="text" name="currency" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>Total Price</label>
                <input type="number" step="0.01" name="total_price" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>SP Total Price</label>
                <input type="number" step="0.01" name="sp_total_price" class="form-control">
            </div>

            <div class="col-md-12 mb-3">
                <label>Item Remarks</label>
                <textarea name="item_remarks" class="form-control" rows="3"></textarea>
            </div>

            <div class="col-md-12 text-end">
                <button class="btn btn-primary">Save Asset</button>
            </div>
        </div>
    </form>
</div>
</div>
@endsection
