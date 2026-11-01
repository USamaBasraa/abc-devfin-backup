@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
<h1>Categories</h1>
<a href="{{ route('ep_categories_create') }}" class="btn btn-primary">Create New Category</a>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Category Title</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td>{{ $category->ec_title }}</td>
                <td>
                    <!--a href="{{ route('ep_categories_show', ['id' => $category->id]) }}" class="btn btn-info">View</a>
                    <a href="{{ route('ep_categories_edit', ['id' => $category->id]) }}" class="btn btn-primary">Edit</a-->
                    <form action="{{ route('categories.destroy', ['id' => $category->id]) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    {{-- <tbody>
        @foreach($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td>{{ $category->ec_title }}</td>
                <td>
                    <a href="{{ route('categories.show', ['id' => $category->id]) }}" class="btn btn-info">View</a>
                    <a href="{{ route('categories.edit', ['id' => $category->id]) }}" class="btn btn-primary">Edit</a>
                    <form action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody> --}}
</table>

@endsection
