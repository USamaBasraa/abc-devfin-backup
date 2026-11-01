@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
<h1>Create New Category</h1>
<form method="POST" action="{{ route('categories_add') }}">
    @csrf
    <!--Arsalan working on this -->
    <div class="container">
<!-- Schools  Dropdown (Searchable) -->
        <div class="form-group">
            <label for="category">Select School</label>
            <!--input list="category-options" id="category" class="form-control" autocomplete="off" oninput="updateCategoryId()"-->
            <select id="school_id" name="s_id" class="form-control">
                <option value="">-- Select School --</option>
                @foreach($schools as $school)
                    <option data-id="{{ $school->id }}" value="{{ $school->id }}">{{ $school->name }}</option>
                @endforeach
            </select>
            <!-- Hidden input to store the selected category ID -->
            <input type="hidden" id="school_id" name="school_id">
        </div>

 <!-- departments  Dropdown (Searchable) -->
        <div class="form-group">
            <label for="category">Select Department</label>
            <!--input list="category-options" id="category" class="form-control" autocomplete="off" oninput="updateCategoryId()"-->
            <select id="dept_id" name="d_id" class="form-control">
                <option value="">-- Select Department --</option>
                @foreach($depts as $dept)
                    <option data-id="{{ $dept->id }}" value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
            <!-- Hidden input to store the selected category ID -->
            <input type="hidden" id="dept_id" name="dept_id">
        </div>



    <div class="form-group">
        <label for="EC_TITLE">Category Title</label>
        <input type="text" class="form-control" id="EC_TITLE" name="EC_TITLE" required>
    </div>
    <!-- Add other form fields as needed -->
    <button type="submit" class="btn btn-primary">Create Category</button>
</form>

@endsection
