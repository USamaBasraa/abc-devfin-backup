@extends('layouts.app')

@section('content')
<meta name="api-token" content="{{ auth()->user()->api_token ?? '' }}"> {{-- ✅ Add this at the top --}}
<div class="container mt-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="card-title mb-3" id="formTitle">Add New Vendor</h4>

            <form id="vendorForm">
                @csrf
                <input type="hidden" id="vendor_id" name="vendor_id"> <!-- hidden id for edit -->

                <div class="form-group">
                    <label for="vendor_name">Vendor Name</label>
                    <input type="text" class="form-control" name="vendor_name" id="vendor_name" placeholder="Enter vendor name" required>
                </div>

                <div class="form-group">
                    <label for="vendor_contact_person">Contact Person</label>
                    <input type="text" class="form-control" name="vendor_contact_person" id="vendor_contact_person" placeholder="Enter contact person">
                </div>

                <div class="form-group">
                    <label for="vendor_phone">Phone</label>
                    <input type="text" class="form-control" name="vendor_phone" id="vendor_phone" placeholder="Enter phone number">
                </div>

                <div class="form-group">
                    <label for="vendor_email">Email</label>
                    <input type="email" class="form-control" name="vendor_email" id="vendor_email" placeholder="Enter email address">
                </div>

                <div class="form-group">
                    <label for="vendor_address">Address</label>
                    <textarea class="form-control" name="vendor_address" id="vendor_address" placeholder="Enter vendor address"></textarea>
                </div>

                <div class="form-group">
                    <label for="vendor_category_id">Vendor Category</label>
                    <select class="form-control" name="vendor_category_id" id="vendor_category_id" required>
                        <option value="">-- Select Category --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="vendor_registration_date">Registration Date</label>
                    <input type="date" class="form-control" name="vendor_registration_date" id="vendor_registration_date">
                </div>

                <button type="submit" class="btn btn-primary" id="saveBtn">
                    <i class="fas fa-save"></i> Save Vendor
                </button>
                <button type="button" class="btn btn-secondary d-none" id="cancelEditBtn">
                    Cancel Edit
                </button>
            </form>

            <div id="vendorResponse" class="mt-3"></div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title mb-3">Vendor List</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="vendorTable">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Vendor Name</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Category</th>
                            <th>Address</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Vendor data will be loaded dynamically --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
        let authToken = $('meta[name="api-token"]').attr('content') || '';

    function submitVendorForm() {
        let vendorId = $('#vendor_id').val();
        let url = vendorId ? '{{ route("vendor.update") }}' : '{{ route("vendor.store") }}';
        let method = vendorId ? 'PUT' : 'POST';
        // let method = 'POST';

        $.ajax({
            url: url,
            type: method,
            data: $('#vendorForm').serialize(),
            headers: {
            'Authorization': 'Bearer ' + authToken,   // 👈 sending auth token
            'X-CSRF-TOKEN': '{{ csrf_token() }}'      // 👈 Laravel CSRF token
             },
            success: function (response) {
                $('#vendorResponse').html(`<div class="alert alert-success">${response.message}</div>`);
                $('#vendorForm')[0].reset();
                $('#vendor_id').val('');
                $('#formTitle').text('Add New Vendor');
                $('#saveBtn').text('Save Vendor');
                $('#cancelEditBtn').addClass('d-none');
                fetchVendors();
            },
            error: function (xhr) {
                let errors = xhr.responseJSON.errors;
                let msg = '<ul>';
                $.each(errors, function (key, value) {
                    msg += `<li>${value[0]}</li>`;
                });
                msg += '</ul>';
                $('#vendorResponse').html(`<div class="alert alert-danger">${msg}</div>`);
            }
        });
    }

    function fetchVendors() {
        $.get('{{ route("vendor.list") }}', function(data) {
            console.log(data);
            let rows = '';
            data.forEach((vendor, index) => {
               rows += `<tr>

                    <td>${index + 1}</td>
                    <td>${vendor.vendor_name}</td>
                    <td>${vendor.vendor_contact_person ?? ''}</td>
                    <td>${vendor.vendor_email ?? ''}</td>
                    <td>${vendor.vendor_phone ?? ''}</td>
                    <td>${vendor.category_name ?? '-'}</td>

                    <td>${vendor.vendor_address ?? ''}</td>
                    <td>${vendor.vendor_registration_date ?? ''}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="editVendor(${vendor.vendor_id})">Edit
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteVendor(${vendor.vendor_id})">Delete
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>`;

            });
            $('#vendorTable tbody').html(rows);
        });
    }

    function editVendor(id) {
        $.get(`{{ url('vendor-show') }}/${id}`, function(vendor) {
            // Prefill fields
            $('#vendor_id').val(vendor.vendor_id);
            $('#vendor_name').val(vendor.vendor_name);
            $('#vendor_contact_person').val(vendor.vendor_contact_person);
            $('#vendor_phone').val(vendor.vendor_phone);
            $('#vendor_email').val(vendor.vendor_email);
            $('#vendor_address').val(vendor.vendor_address);
            $('#vendor_category_id').val(vendor.vendor_category_id);
            $('#vendor_registration_date').val(vendor.vendor_registration_date);

            // Update form state
            $('#formTitle').text('Edit Vendor');
            $('#saveBtn').text('Update Vendor');
            $('#cancelEditBtn').removeClass('d-none');
        });
    }

    // function deleteVendor(id) {
    //     if (!confirm('Are you sure you want to delete this vendor?')) return;

    //     $.ajax({
    //         url: `{{ url('vendor/delete') }}/${id}`,
    //         type: 'DELETE',
    //         data: {_token: '{{ csrf_token() }}'},
    //         success: function (response) {
    //             $('#vendorResponse').html(`<div class="alert alert-success">${response.message}</div>`);
    //             fetchVendors();
    //         }
    //     });
    // }
    function deleteVendor(id) {
        if (!confirm('Are you sure you want to delete this vendor?')) return;

        $.ajax({
            url: `/vendor/delete/${id}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'POST' // Laravel interprets this as DELETE
            },
            success: function(response) {
                $('#vendorResponse').html(`<div class="alert alert-success">${response.message}</div>`);
                fetchVendors();
            },
            error: function() {
                alert('Error deleting vendor');
            }
        });
    }


    $(document).ready(function () {
        fetchVendors();

        $('#vendorForm').on('submit', function (e) {
            e.preventDefault();
            submitVendorForm();
        });

        $(document).on('click', '.editVendor', function () {
            let id = $(this).data('id');
            editVendor(id);
        });

        $(document).on('click', '.deleteVendor', function () {
            let id = $(this).data('id');
            deleteVendor(id);
        });

        $('#cancelEditBtn').on('click', function () {
            $('#vendorForm')[0].reset();
            $('#vendor_id').val('');
            $('#formTitle').text('Add New Vendor');
            $('#saveBtn').text('Save Vendor');
            $(this).addClass('d-none');
        });
    });
</script>
@endsection
