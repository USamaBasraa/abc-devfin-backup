<!-- resources/views/holidays/index.blade.php -->

@extends('layouts.app', ['pageTitle' => 'BNU Holidays'])


<style>
    @media (min-width: 576px) {
        div.dataTables_wrapper div.dataTables_filter input {
            width: 100%;
}
    div.dataTables_wrapper div.dataTables_filter input {
        width: 450px;
        height: 40px;
    }
}
    tr:nth-child(even) {
        background-color: #dddddd;
    }



</style>






@section('content')
<div class="container-fluid">
    <div class="card p-4 pt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <h2 class="text-center">Holiday Management Form</h2>
                <form id="holidayForm">
                    <!-- Holiday Date Form Group -->
                    <div class="mb-3">
                        <label for="holidayDate" class="form-label">Holiday Date</label>
                        <input type="date" class="form-control" id="holidayDate" name="holidayDate" required>
                        <span id="holidayDate-error" class="error text-danger"></span>
                    </div>

                    <!-- Holiday Description Form Group -->
                    <div class="mb-3">
                        <label for="holidayDescription" class="form-label">Holiday Description</label>
                        <textarea class="form-control" id="holidayDescription" name="holidayDescription" rows="4" placeholder="Enter holiday description" required></textarea>
                        <span id="holidayDescription-error" class="error text-danger"></span>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-end">
                        <button type="button" class="btn btn-primary" id='submitButton'   onclick="submitHolidayForm()">
                            <i class="fa fa-check"></i> Submit
                        </button>
                    </div>


                </form>

                <!-- Success Message -->
                <div id="successMessage" class="mt-3"></div>
            </div>
        </div>

        <!-- Table -->
        <div class="dt-responsive ">
        <table id="simpletable" class="table table-striped table-bordered dt-responsive">
            <thead>
                <tr>
                    <th>SR #</th>
                    <th>Holiday Date</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($holidays as $index => $holiday)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('d F Y') }}</td>
                        <td>{{ $holiday->holiday_description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </div>
</div>
@stop

@section('javascript')
<script>



$(document).ready(function() {
    $('#simpletable').DataTable();
});


document.addEventListener('DOMContentLoaded', () => {
    // Get today's date in YYYY-MM-DD format
    const today = new Date().toISOString().split('T')[0];
    // Set the min attribute of the date input
    document.getElementById('holidayDate').setAttribute('min', today);
});

function submitHolidayForm() {
    let form = document.getElementById('holidayForm');
    let data = new FormData(form);
    let successMsg = document.getElementById('successMessage');
    let submitButton = $('#submitButton');
    
    // Update button to show spinner and disable it
    submitButton.html('<i class="fa fa-spinner fa-spin"></i> Submitting').attr('disabled', true);

    $.ajax({
        type: 'POST',
        url: '{{ route('university-holidays-management') }}', // Use Laravel's route helper to get the URL
        data: data,
        contentType: false, // Do not set content type
        processData: false, // Do not process the data
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}', // Add CSRF token for Laravel security
             "Authorization": 'Bearer {{ auth()->user()->api_token }}',
            "Accept": "application/json"
        },
        success: function(response) {
            if (response.status) {
                // Reset button text and icon before reloading the page
                submitButton.html('<i class="fa fa-check"></i> Submit').attr('disabled', false);
                
                successMsg.innerHTML = `<div class="alert alert-success">${response.message}</div>`;
                
                setTimeout(function() {
                    // Reload the page after 3 seconds
                    window.location.reload();
                }, 3000);
            } else {
                // Handle non-validation errors here if needed
                successMsg.innerHTML = `<div class="alert alert-danger">${response.message}</div>`;
                setTimeout(function() {
                    $('#successMessage').html('');
                }, 3000);
            }
        },
        error: function(xhr) {
            // Handle validation errors
            displayValidationErrors(xhr);
            // Restore original button state
            submitButton.html('<i class="fa fa-check"></i> Submit').attr('disabled', false);
        }
    });
}


function displayValidationErrors(xhr) {
    if (xhr.status == 422) {
        var errors = xhr.responseJSON.errors;
        // Clear previous errors
        $('.error').html('').removeClass('text-danger');

        $.each(errors, function(key, value) {
            var errorElement = $("#" + key + "-error");
            errorElement.html('<i class="fa fa-exclamation-circle"></i> ' + value[0]);
            errorElement.addClass('text-danger');
            
            // Remove the error message after 5 seconds
            setTimeout(function(){
                errorElement.html('');
                errorElement.removeClass('text-danger');
            }, 5000);
        });
    }
}

</script>
@stop
