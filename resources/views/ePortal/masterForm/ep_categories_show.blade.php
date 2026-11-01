@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
<h1>Category Details</h1>
<p><strong>ID:</strong> {{ $category->id }}</p>
<p><strong>Category Title:</strong> {{ $category->EC_TITLE }}</p>
<!-- Display other category details -->
<a href="{{ route('categories.index') }}" class="btn btn-primary">Back to Categories</a>


@endsection
