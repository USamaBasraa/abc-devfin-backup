@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fa fa-check-circle mr-2"></i> Create Approval Master
            </h4>
        </div>

        <div class="card-body p-4">

            <!-- Alert Messages -->
            <div id="alert-container"></div>

            <!-- Form -->
            <form id="approvalForm" method="POST">
                @csrf









































                <!-- Type Selection -->
                <div class="form-group">
                    <label for="type" class="font-weight-bold">Select Type</label>
                    <select id="type" name="type" class="form-control" >
                        <option value="">-- Select Type --</option>
                        <option value="academic">Academic</option>
                        <option value="non_academic">Non Academic</option>
                    </select>
                </div>

                <!-- School Dropdown -->
                <div class="form-group d-none" id="school_div">
                    <label for="school_id" class="font-weight-bold">Select School</label>
                    <select id="school_id" name="school_id" class="form-control">
                        <option value="">-- Select School --</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->school_id }}">{{ $school->school_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Department Dropdown -->
                <div class="form-group d-none" id="department_div">
                    <label for="department_id" class="font-weight-bold">Select Department</label>
                    <select id="department_id" name="department_id" class="form-control">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Price Range -->
                <div class="form-row " id="price_range_div1">
                    <div class="form-group col-md-6">
                        <label for="min_price" class="font-weight-bold">Min Price</label>
                        <input type="number" name="min_price" step="0.01" min="0" class="form-control" placeholder="Enter minimum price">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="max_price" class="font-weight-bold">Max Price</label>
                        <input type="number" name="max_price" step="0.01" min="0" class="form-control" placeholder="Enter maximum price">
                    </div>
                </div>



              <!-- Approval Body -->
                <div class="form-group">
                    <label for="approval_user_id" class="font-weight-bold">Approval Body</label>
                    <select class="form-control select2-show-search" id="approval_user_id" name="approval_user_id" style="width: 100%;">
                        <option value="">-- Select User --</option>
                        @foreach($users as $user)
                            <option value="{{$user->id}}">{{$user->prefix}}{{$user->full_name}}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple users.</small>
                </div>
                <!-- Status -->
                <div class="form-group">
                    <label for="active_status" class="font-weight-bold">Status</label>
                    <select name="active_status" id="active_status" class="form-control" required>
                        <option value="Active" selected>Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <!-- Submit -->
                <div class="text-right mt-4">
                    <button type="submit" id="submitBtn" class="btn btn-primary px-4 py-2">
                        <i class="fa fa-save mr-1"></i> Save
                    </button>
                    <button type="button" id="loadingBtn" class="btn btn-secondary px-4 py-2 d-none" disabled>
                        <span class="spinner-border spinner-border-sm"></span> Saving...
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card mt-5 shadow-lg border-0 rounded-lg">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fa fa-list mr-2"></i>Approval Master List</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover" id="approvalTable">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>School Name</th>
                        <th>Department Name</th>
                        <th>Min Price</th>
                        <th>Max Price</th>
                        <th>Approver</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dynamic Rows will load here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Approval Master</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="editForm">
                @csrf
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>School Name</label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Department Name</label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Min Price</label>
                        <input type="number" id="edit_min_price" name="min_price" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Max Price</label>
                        <input type="number" id="edit_max_price" name="max_price" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select id="edit_status" name="active_status" class="form-control">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
<!-- jQuery + AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(function () {

    // Load existing records
    loadData();

    // Toggle fields
    $('#type').on('change', function () {
        const t = $(this).val();
        $('#school_div, #department_div, #price_range_div').addClass('d-none');
        if (t === 'academic') { $('#school_div, #price_range_div').removeClass('d-none'); }
        if (t === 'non_academic') { $('#department_div').removeClass('d-none'); }
    });

    // Save via AJAX
    $('#approvalForm').on('submit', function (e) {
        e.preventDefault();
        $('#submitBtn').addClass('d-none');
        $('#loadingBtn').removeClass('d-none');

        $.post("{{ route('approval-master.store') }}", $(this).serialize())
            .done(res => {
                $('#alert-container').html(`<div class="alert alert-success">${res.message}</div>`);
                $('#approvalForm')[0].reset();
                loadData();
            })
            .fail(xhr => {
                $('#alert-container').html(`<div class="alert alert-danger">${xhr.responseJSON?.message ?? 'Error'}</div>`);
            })
            .always(() => {
                $('#submitBtn').removeClass('d-none');
                $('#loadingBtn').addClass('d-none');
            });
    });

    // Load data into table
    function loadData() {
        $.get("{{ route('sp-approval-master.list') }}", function (data) {
            let rows = '';
            $.each(data, function (i, item) {
                rows += `
                    <tr>
    <td>${item.id}</td>
    <td>${item.school_name ?? '-'}</td>
    <td>${item.dept_name ?? '-'}</td>
    <td>${item.min_price ?? '-'}</td>
    <td>${item.max_price ?? '-'}</td>
    <td>${item.user_id ?? '-'}</td>
    <td>${item.active_status}</td>
    <td>
         <!--button class="btn btn-sm btn-warning editBtn" data-id="${item.id}">Edit</button-->
        <button class="btn btn-sm btn-danger deleteBtn" data-id="${item.id}">Delete</button>
    </td>
</tr>
`;
            });
            $('#approvalTable tbody').html(rows);
        });
    }

    // Edit button click
    $(document).on('click', '.editBtn', function () {
        const id = $(this).data('id');
        $.get(`{{ url('approval-master') }}/${id}/edit`, function (data) {
            $('#edit_id').val(data.ID);
            $('#edit_name').val(data.NAME);
            $('#edit_min_price').val(data.MIN_PRICE);
            $('#edit_max_price').val(data.MAX_PRICE);
            $('#edit_status').val(data.ACTIVE_STATUS);
            $('#editModal').modal('show');
        });
    });

    // Update record
    $('#editForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#edit_id').val();
        $.ajax({
            url: `{{ url('approval-master') }}/${id}`,
            type: 'PUT',
            data: $(this).serialize(),
            success: function (res) {
                $('#editModal').modal('hide');
                $('#alert-container').html(`<div class="alert alert-success">${res.message}</div>`);
                loadData();
            }
        });
    });

    // Delete button
// Delete button
$(document).on('click', '.deleteBtn', function () {
    if (!confirm('Are you sure you want to delete this record?')) return;

    const id = $(this).data('id');

    $.ajax({
        url: `{{ url('approval-master/delete') }}`, // dedicated delete route
        type: 'POST', // use POST method
        data: {
            id: id,
            _token: '{{ csrf_token() }}'
        },
        success: function (res) {
            $('#alert-container').html(`<div class="alert alert-success">${res.message}</div>`);
            loadData();
        },
        error: function (xhr) {
            $('#alert-container').html(`<div class="alert alert-danger">Error deleting record</div>`);
        }
    });
});


});
</script>

