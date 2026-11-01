@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
<h1>Edit Category</h1>
<form method="POST" action="{{ route('categories.update', ['id' => $category->id]) }}">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="EC_TITLE">Category Title</label>
        <input type="text" class="form-control" id="EC_TITLE" name="EC_TITLE" value="{{ $category->EC_TITLE }}" required>
    </div>
    <!-- Add other form fields as needed -->
    <button type="submit" class="btn btn-primary">Update Category</button>
</form>

@endsection
