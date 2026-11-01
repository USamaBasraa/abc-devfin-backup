@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Item Entry</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('items.store') }}">
        @csrf

        <div class="mb-3">
            <label>BH Category ID</label>
            <input type="number" name="bh_cat_id" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Item Title</label>
            <input type="text" name="item_title" class="form-control" maxlength="1000" required>
        </div>

        <div class="mb-3">
            <label>Item Type</label>
            <select name="item_type" class="form-control" required>
                <option value="">Select</option>
                <option value="EQUIPMENT">Equipment</option>
                <option value="CONSUMABLE">Consumable</option>
                <option value="SERVICE">Service</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Item Category</label>
            <input type="text" name="item_category" class="form-control">
        </div>

        <div class="mb-3">
            <label>Brand</label>
            <input type="text" name="brand" class="form-control">
        </div>

        <div class="mb-3">
            <label>Model</label>
            <input type="text" name="model" class="form-control">
        </div>

        <div class="mb-3">
            <label>Budget Type</label>
            <select name="budget_type" class="form-control">
                <option value="">Select</option>
                <option value="CAPEX">CAPEX</option>
                <option value="OPEX">OPEX</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="ACTIVE">ACTIVE</option>
                <option value="INACTIVE">INACTIVE</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Remarks</label>
            <textarea name="remarks" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Item</button>
    </form>
</div>
@endsection
