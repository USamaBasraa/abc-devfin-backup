@extends('layouts.app')

@section('title', 'BNU CMS')

@section('content')


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">


<div class="container-fluid mt-4">
    <div class="card p-4 pt-4">
    <h4 class="mb-4" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; font-weight: bold; letter-spacing: 1px; text-transform: uppercase;">Scholarship Approval</h4>

<div class="table-responsive table-bordered table-hover">
    <table  id="scholarshipTable"  class="table">
        <thead>
            <tr>
                <th>Enrollment No#</th>
                <th>Session</th>
                <th>Scholarship Name</th>
                <th>Type</th>
                <th>%Age Given</th>
                <th>Amount Given</th>
                <th>Scholarship Amount</th>
                <!-- <th>From Date</th>
                <th>To Date</th> -->
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($studentScholarshipRec as $rec)
            <tr id="scholarship_{{ $rec->fsa_id }}">
                <td>{{ $rec->fsa_enrollment_no }}</td>
                <td>{{ $rec->sess_code }}</td>
                <td>{{ $rec->scholarship_name }}</td>
                <td>{{ $rec->fsa_search_type }}</td>
                <td>{{ $rec->fsa_percentage }}</td>
                <td>{{ $rec->fsa_amount }}</td>
                <td>{{ $rec->fsa_given_scholarship_amount }}</td>
           
                <td>
                @if ($rec->fsa_ro_status == 'Pending')
                    <span class="badge badge-warning">{{ $rec->fsa_ro_status }}</span>
                @elseif ($rec->fsa_ro_status == 'Approved')
                    <span class="badge badge-success">{{ $rec->fsa_ro_status }}</span>
                @elseif ($rec->fsa_ro_status == 'Disapproved')
                    <span class="badge badge-danger">{{ $rec->fsa_ro_status }}</span>
                @else
                    <span class="badge badge-secondary">{{ $rec->fsa_ro_status }}</span>
                @endif
            </td>

            <td>
    @if ($rec->fsa_ro_status == 'Pending')
        <button onclick="updateStatus('{{ $rec->fsa_id }}', 'approve')" type="button" class="btn btn-info w-100 mb-1">Approve Now</button>

        @elseif ($rec->fsa_ro_status == 'Approved')
        <button class="btn btn-success w-100 mb-1" disabled>Approved</button>

    @endif
</td>



            </tr>
            @endforeach
        </tbody>
    </table>
</div>

</div>
</div>

<!-- Container for displaying toast messages -->
<div id="toastContainer"></div>



@stop

@section('javascript')

<!-- Include SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script>
    // Show toast notification when page loads
    // document.addEventListener('DOMContentLoaded', function() {
    //     showMessagePopup('Overall Scholarship Percentage is greater than 100%', icon = 'error')
    // });
</script>



<script>

$(document).ready(function() {
    // showMessagePopup('Overall Scholarship Percentage is greater than 100%', icon = 'info')
    $('#scholarshipTable').DataTable();
});


function showMessagePopup(message, icon = 'success') {
        Swal.fire({
            icon: icon,
            title: message,
            timer: 4000,
            timerProgressBar: true,
            toast: true,
            position: 'top-end',
            // position: 'center-end', // Center vertically and align to the right
            showConfirmButton: false
        });
    }

function updateStatus(scholarshipId, action) {
    var formData = new FormData();
    formData.append('fsa_id', scholarshipId);
    formData.append('action', action);
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    formData.append('_token', csrfToken);

    $.ajax({
        url: '/finance/update-scholarship-status',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Success:', response);
            if (response.status === 'success') {

                showMessagePopup(response.message, icon = 'success')

                setTimeout(function() {
                    window.location.reload();
                }, 5000);



            } else {
                // Handle error response if needed
            }
        },
        error: function(xhr, status, error) {
                console.error('Error:', error);
                // Optionally, you can handle errors here

                if (xhr.status === 400) {
                    // Display error message from response
                    showMessagePopup(xhr.responseJSON.message, 'error');
                } else {
                    // Show a generic error message
                    showMessagePopup('An error occurred while processing scholarship', 'error');
                }
            }
    });
}

// Function to show toast
function showToast(message) {
        // Create toast element
        var toast = document.createElement('div');
        toast.classList.add('toast');
        toast.textContent = message;

        // Append toast to container
        document.getElementById('toastContainer').appendChild(toast);

        // Automatically remove toast after 3 seconds
        setTimeout(function() {
            toast.remove();
        }, 3000);
    }

</script>
@stop
