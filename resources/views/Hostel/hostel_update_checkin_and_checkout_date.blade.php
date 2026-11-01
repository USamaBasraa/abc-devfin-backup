@extends('layouts.app')

@section('title')
    Hostel Check-in/Check-out Management
@stop

@section('content')
@include('components.toast-notifications')

<style>
#searchButton {
    display: flex;
    align-items: center;
    padding-left: 12px;
    padding-right: 12px;
    border-left: none;
}

#searchButton i, #searchButton span {
    transition: all 0.3s ease;
}

#searchButton:hover i {
    transform: scale(1.5);
    margin-right: 5px;
}

.custom-card-header {
    background-color: #ffffff;
    color: #4454c3;
    text-align: center;
    font-weight: bold;
    padding: 1rem;
    font-size: 1.2rem;
    border-bottom: 3px solid #4454c3 !important;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1) !important;
}

#checkinCheckoutForm {
    transition: all 0.3s ease;
    padding: 20px;
    border-radius: 8px;
}

#checkinCheckoutForm.highlight {
    box-shadow: 0 0 15px rgba(0, 123, 255, 0.3);
    transform: translateY(-5px);
}

html {
    scroll-behavior: smooth;
}
</style>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Hostel Check-in/Check-out Management</h3>
        </div>
        <div class="card-body">
            <form id="checkinCheckoutForm">
                @csrf
                <input type="hidden" name="id" id="record_id">
                
                <!-- Student Search Section -->
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="studentEnrollment" class="form-label" style="font-weight: bold; font-size: 1rem; color: #333;">
                            Enrollment No# or Applicant ID
                            <span style="color: red;">*</span>
                        </label>
                        <div class="input-group mb-3" style="max-width: 400px; margin: auto;">
                            <input type="text" class="form-control" id="studentEnrollment" name="studentEnrollment" 
                                placeholder="Enrollment No / Applicant ID" onkeydown="handleEnterKey(event)"
                                style="border-right: none; background: linear-gradient(135deg, #fcfcfc, #e0f7fa); border: 2px solid #ced4da; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 12px; transition: box-shadow 0.2s ease, border-color 0.2s ease;"
                                onmouseover="this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.15)'; this.style.borderColor='#007bff';"
                                onmouseout="this.style.boxShadow='0 2px 4px rgba(0, 0, 0, 0.1)'; this.style.borderColor='#ced4da';">
                            
                            <button class="btn btn-primary ml-1" type="button" id="searchButton" onclick="searchStudent()">
                                <i class="fa fa-search"></i>
                                <span>Search</span>
                            </button>
                        </div>
                        <span id="studentEnrollment-error" class="error text-danger"></span>
                    </div>
                </div>

                <!-- Student Info Card -->
                <div class="row">
                    <div class="container">
                        <div id='profileDiv'></div>
                    </div>
                </div>
                <hr style="width: 100%; height: 6px;">

                <!-- Hidden Fields for Student Info -->
                <input name='st_school_id' id='st_school_id' hidden>
                <input name='st_dept_id' id='st_dept_id' hidden>
                <input name='st_prog_id' id='st_prog_id' hidden>
                <input name='st_aai_id' id='st_aai_id' hidden>

                <!-- Rest of the Form (Initially Hidden) -->
                <div id="formFields" style="display: none;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="session_id">Session <span class="text-danger">*</span></label>
                                <select name="session_id" id="session_id" class="form-control" required>
                                    <option value="">Select Session</option>
                                    @foreach($academicYear as $session)
                                        <option value="{{ $session->sess_id }}">{{ $session->sess_name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger" id="session_id_error"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="checkin_date">Check-in Date <span class="text-danger">*</span></label>
                                <input type="date" name="checkin_date" id="checkin_date" class="form-control" required>
                                <span class="text-danger" id="checkin_date_error"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="checkout_date">Check-out Date</label>
                                <input type="date" name="checkout_date" id="checkout_date" class="form-control">
                                <span class="text-danger" id="checkout_date_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary px-4 me-2" id="saveBtn">
                                <i class="fa fa-save"></i> Save
                            </button>
                            <button type="button" class="btn btn-secondary px-4" id="resetBtn">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <hr>

            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped" id="checkinCheckoutTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Session</th>
                            <th>Student Info</th>
                            <th>Check-in Date</th>
                            <th>Check-out Date</th>
                            <th>Entry By</th>
                            <th>Entry Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('javascript')
<script>
function handleEnterKey(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        searchStudent();
    }
}

function searchStudent() {
    var enrollmentNo = $('#studentEnrollment').val();
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    // Clear any previous error message
    $('#studentEnrollment-error').text('');
    $('#profileDiv').html('');
    $('#formFields').hide();
    
    // Validate the input
    if (enrollmentNo.trim() === '') {
        $('#studentEnrollment-error').text('Please enter an enrollment number.');
        return;
    }

    // Send the AJAX request
    $.ajax({
        url: '/finance/search-student-challans-available',
        type: 'POST',
        data: {
            studentEnrollment: enrollmentNo
        },
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(allData) {
            if(allData.studentRec) {
                makeProfileHtml(allData.studentRec);
                
                // Set hidden fields
                $('#st_school_id').val(allData.studentRec.school_id);
                $('#st_dept_id').val(allData.studentRec.dept_id);
                $('#st_prog_id').val(allData.studentRec.prog_id);
                $('#st_aai_id').val(allData.studentRec.aai_id);
                
                // Show the rest of the form
                $('#formFields').show();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            $('#studentEnrollment-error').text('An error occurred while searching. Please try again.');
        }
    });
}

function makeProfileHtml(data) {
    let dataDiv = document.getElementById('profileDiv');
    dataDiv.innerHTML = '';

    if (data && data.usernameError) {
        let errorHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            ${data.usernameError}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
        dataDiv.innerHTML = errorHtml;
    } else if (data) {
        const label = isNaN(data.username) ? 'Enrollment No:' : 'Applicant ID:';
        let imageUrl = data.api_imagename
            ? `https://admission.bnu.edu.pk/images/${data.api_imagename}`
            : `https://cms.bnu.edu.pk/students_pics/${data.username.toUpperCase()}.jpg`;

        let profileHtml = `
        <div class="shadow-sm border-0">
            <div class="card-header custom-card-header d-flex align-items-center p-2">
                <i class="fa fa-user mr-2" style="font-size: 1.5rem;"></i>
                <span class="h6 mb-0">Student Info</span>
            </div>

            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center mb-2">
                        <img src="${imageUrl}"
                            alt="Student Image"
                            class="img-fluid rounded-circle shadow-sm mb-2"
                            style="width: 80px; height: 80px;">
                        <h6 class="text-uppercase font-weight-bold text-primary mb-1" style="font-size: 1.1rem;">${data.spi_name || 'N/A'}</h6>
                        <span class="badge bg-info text-white" style="font-size: 0.8rem;">${data.sai_student_status || 'N/A'}</span>
                    </div>

                    <div class="col-md-10">
                        <div class="row">
                            ${generateInfoBox('fa-id-card', 'Enrollment No / Applicant ID:', 
                                `${data.username && isNaN(data.username) ? data.username : 'N/A'} / ${data.aai_id && !isNaN(data.aai_id) ? data.aai_id : 'N/A'}`)}
                            ${generateInfoBox('fa-envelope', 'Email:', 
                                !isNaN(data.username) ? data.spi_email || 'N/A' : `${data.username || 'N/A'}@bnu.edu.pk`)}
                            ${generateInfoBox('fa-phone', 'Mobile:', data.spi_mobile || 'N/A')}
                            ${generateInfoBox('fa-building', 'School:', data.school_name || 'N/A')}
                            ${generateInfoBox('fa-university', 'Department:', data.dept_name || 'N/A')}
                            ${generateInfoBox('fa-book', 'Program:', data.prog_name || 'N/A')}
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

        dataDiv.innerHTML = profileHtml;
    } else {
        let noDataHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            No data available
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
        dataDiv.innerHTML = noDataHtml;
    }
}

function generateInfoBox(iconClass, label, value) {
    return `
    <div class="col-md-4 mb-3">
        <div class="bg-light p-3 rounded shadow-sm d-flex align-items-center" style="min-height: 70px;">
            <i class="fa ${iconClass} mr-3 text-muted" style="font-size: 1.2rem;"></i>
            <div>
                <span class="text-muted d-block" style="font-size: 0.85rem;">${label}</span>
                <strong style="font-size: 0.9rem;">${value}</strong>
            </div>
        </div>
    </div>`;
}

$(document).ready(function() {
    // Initialize DataTable
    const userGroupId = {{ auth()->user()->group_id }};

    var table = $('#checkinCheckoutTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('hostel.checkin-checkout') }}",
            dataSrc: function(json) {
                return json.data || [];
            }
        },
        columns: [
            {data: 'id', name: 'ID'},
            {
                data: 'sess_name', 
                name: 'sess_name',
                render: function(data, type, row) {
                    return data || 'N/A';
                }
            },
            {
                data: 'student_enroll_no',
                render: function(data, type, row) {
                    return `<strong>${data}</strong>${row.app_id ? `<br><small>App ID: ${row.app_id}</small>` : ''}`;
                }
            },
            {
                data: 'checkin_date',
                render: function(data) {
                    return moment(data).format('DD-MM-YYYY');
                }
            },
            {
                data: 'checkout_date',
                render: function(data) {
                    return data ? moment(data).format('DD-MM-YYYY') : 'Not Set';
                }
            },
            {data: 'entry_username', name: 'entry_username'},
            {
                data: 'entry_datetime',
                render: function(data) {
                    return moment(data).format('DD-MM-YYYY HH:mm');
                }
            },
            {
                data: 'id',
                render: function(data) {
                    let buttons = `
                        <button class="btn btn-sm btn-primary edit-btn" data-id="${data}">
                            <i class="fa fa-edit"></i>
                        </button>
                    `;

                    if (userGroupId == 644) {
                        buttons += `
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">
                                <i class="fa fa-trash"></i>
                            </button>
                        `;
                    }

                    return buttons;
                }
            }
        ],
        order: [[0, 'desc']], // Sort by ID descending by default
        pageLength: 10,
        responsive: true
    });

    // Function to refresh table data
    function refreshTable() {
        table.ajax.reload(null, false); // false = retain page position
    }

    // Form Submit Handler
    $('#checkinCheckoutForm').on('submit', function(e) {
        e.preventDefault();
        $('.text-danger').text('');
        
        var formData = {
            id: $('#record_id').val(),
            session_id: $('#session_id').val(),
            student_enroll_no: $('#studentEnrollment').val(),
            app_id: $('#st_aai_id').val(),
            checkin_date: $('#checkin_date').val(),
            checkout_date: $('#checkout_date').val(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: "{{ route('hostel.checkin-checkout.save') }}",
            method: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                  showToast('success', response.message);   
                    
                    // Reset form
                    $('#checkinCheckoutForm')[0].reset();
                    $('#record_id').val('');
                    $('#profileDiv').html('');
                    $('#formFields').hide();
                    
                    // Refresh table data
                    refreshTable();
                }
            },
            error: function(xhr) {
                if(xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#' + key + '_error').text(value[0]);
                    });
                    if(xhr.responseJSON.duplicate_record) {
                       showToast('error', xhr.responseJSON.message);
                    } else {
                        showToast('error', xhr.responseJSON.message);
                    }
                } else {

                    showToast('error', xhr.responseJSON.message);
                }
            }
        });
    });

    // Edit Button Click Handler
    $(document).on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        var row = table.row($(this).closest('tr')).data();
        
        // Add loading state
        var $form = $('#checkinCheckoutForm');
        $form.css('opacity', '0.6');
        
        $('#record_id').val(row.id);
        $('#studentEnrollment').val(row.student_enroll_no);
        searchStudent(); // This will populate the student info
        
        setTimeout(function() {
            $('#session_id').val(row.session_id);
            
            // Format dates for input fields (YYYY-MM-DD)
            if(row.checkin_date) {
                var checkinDate = moment(row.checkin_date).format('YYYY-MM-DD');
                $('#checkin_date').val(checkinDate);
            }
            
            if(row.checkout_date) {
                var checkoutDate = moment(row.checkout_date).format('YYYY-MM-DD');
                $('#checkout_date').val(checkoutDate);
            }
            
            $('#formFields').show();
            
            // Restore form opacity
            $form.css('opacity', '1');
            
            // Smooth scroll to form with highlight effect
            $('html, body').animate({
                scrollTop: $form.offset().top - 100
            }, 800, function() {
                $form.addClass('highlight');
              
                $('#session_id').focus();
                
                // Remove highlight after animation
                setTimeout(function() {
                    $form.removeClass('highlight');
                }, 1000);
            });
        }, 1000);
    });

    // Delete Button Click Handler
    $(document).on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        if(confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                url: "{{ route('hostel.checkin-checkout.delete', '') }}/" + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if(response.success) {
                        showToast('success', response.message);
                        refreshTable();
                    } else {
                        showToast('error', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', 'Error occurred while deleting record');
                    console.error(error);
                }
            });
        }
    });

    // Reset Button Click Handler
    $('#resetBtn').click(function() {
        $('#checkinCheckoutForm')[0].reset();
        $('#record_id').val('');
        $('#profileDiv').html('');
        $('#formFields').hide();
        $('.text-danger').text('');
            showToast('info', 'Form has been reset');
    });

    // Validate Check-out Date
    $('#checkout_date').change(function() {
        var checkinDate = new Date($('#checkin_date').val());
        var checkoutDate = new Date($(this).val());
        
        if(checkoutDate < checkinDate) {
            $('#checkout_date_error').text('Check-out date cannot be earlier than check-in date');
            $(this).val('');
            showToast('error', 'Check-out date must be after check-in date');
        } else {
            $('#checkout_date_error').text('');
        }
    });


});
</script>
@stop