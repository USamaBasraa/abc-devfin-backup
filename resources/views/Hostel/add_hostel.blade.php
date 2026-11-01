<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hostel Category</title>
</head>
<body>
    <h1>Edit Hostel Category</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form action="{{ route('hostel_categories.update', $hostelCategory->ht_id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="HT_NAME">Hostel Category Name:</label>
            <input type="text" name="HT_NAME" id="HT_NAME" value="{{ old('ht_name', $hostelCategory->ht_name) }}" maxlength="50">
            @error('HT_NAME')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="HT_STATUS">Status:</label>
            <input type="text" name="HT_STATUS" id="HT_STATUS" value="{{ old('HT_STATUS', $hostelCategory->ht_status) }}" maxlength="10">
            @error('HT_STATUS')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">Update Category</button>
    </form>
</body>
</html>
