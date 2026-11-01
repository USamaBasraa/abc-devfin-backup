@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Add Vendor Profiling</h4>
    <form id="profilingForm">
        @csrf

        <select name="profiling_vendor_id" class="form-control" >
            <option value="">Select Vendor</option>
            @foreach($vendors as $vendor)
                <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
            @endforeach
        </select>

        <select name="profiling_category_id" class="form-control" >
            <option value="">Select Category</option>
            @foreach($categories as $category)
                <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
            @endforeach
        </select>

        <input type="number" name="profiling_years_of_experience" placeholder="Experience" class="form-control" >
        <input type="text" name="profiling_specialization" placeholder="Specialization" class="form-control">
        <input type="text" name="profiling_certifications" placeholder="Certifications" class="form-control">
        <textarea name="profiling_past_clients" class="form-control" placeholder="Past Clients"></textarea>
        <input type="number" name="profiling_rating" min="1" max="5" placeholder="Rating" class="form-control">
        <textarea name="profiling_notes" class="form-control" placeholder="Notes"></textarea>
        <input type="date" name="profiling_entry_date" class="form-control">

        <button type="submit" class="btn btn-primary mt-2">Submit</button>
    </form>

    <div id="profilingResponse" class="mt-3"></div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        alert('dsf');exit;
        $('#profilingForm').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let formData = form.serialize();

            $.ajax({
                url: '{{ route("vendor-profiling.store") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#profilingResponse').html(
                        `<div class="alert alert-success">${response.message}</div>`
                    );
                    form[0].reset();
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let msg = '<ul>';
                    $.each(errors, function(key, value) {
                        msg += `<li>${value[0]}</li>`;
                    });
                    msg += '</ul>';
                    $('#profilingResponse').html(
                        `<div class="alert alert-danger">${msg}</div>`
                    );
                }
            });
        });
    });
</script>
@endsection
