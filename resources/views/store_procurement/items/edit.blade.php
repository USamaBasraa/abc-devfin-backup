@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Item</h3>

    <form method="POST" action="{{ route('items.update', $item->item_id) }}">
        @csrf
        @method('PUT')

        @include('store_procurement.items.form', ['item' => $item])

        <button class="btn btn-success">Update</button>
        <a href="{{ route('items.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
