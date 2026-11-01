@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />
@include('components.toast-notifications')

<style>
    .select2-container { width: 100% !important; }
    table.dataTable thead > tr > th {
        background-color: #4562C3 !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 12px !important;
        border: none !important;
        text-align: left !important;
    }
</style>

<div class="container-fluid py-3">
    <div class="card-header" style="background: none !important; color: #000 !important; border-bottom: none !important;">
        <h4 class="mb-0">Department Budget Rights Management</h4>
        <p class="text-muted mb-0">Assign users access to view a specific department budget.</p>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form id="permissionForm">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">School</label>
                            <select id="school_id" name="school_id" class="form-control" required>
                                <option value="">Select School</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->school_id }}">{{ $school->school_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">Department</label>
                            <select id="department_id" name="department_id" class="form-control" required>
                                <option value="">Select Department</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">User</label>
                            <select id="user_id" name="user_id" class="form-control select2-show-search" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->prefix }} {{ $user->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="font-weight-bold">Status</label>
                            <select name="status" class="form-control">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-1">
                        <label class="d-block">&nbsp;</label>
                        <button type="submit" id="submitBtn" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">Remarks</label>
                            <input type="text" name="remarks" class="form-control" placeholder="Optional note">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table id="permissions-table" class="table table-bordered table-hover mb-0 w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>School</th>
                            <th>Department</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Update Permission</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id">
                    <div class="form-group">
                        <label class="font-weight-bold">Status</label>
                        <select id="edit_status" name="status" class="form-control">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold">Remarks</label>
                        <input type="text" id="edit_remarks" name="remarks" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function () {
    $('.select2-show-search').select2({
        placeholder: 'Select User',
        allowClear: true,
        width: '100%'
    });

    $('#school_id').on('change', function () {
        const schoolId = $(this).val();
        $('#department_id').empty().append('<option value="">Loading...</option>');

        if (!schoolId) {
            $('#department_id').empty().append('<option value="">Select Department</option>');
            return;
        }

        $.get('/get-departments/' + schoolId, function (response) {
            $('#department_id').empty().append('<option value="">Select Department</option>');
            $.each(response, function (_, dept) {
                $('#department_id').append(`<option value="${dept.dept_id}">${dept.dept_name}</option>`);
            });
        }).fail(function () {
            $('#department_id').empty().append('<option value="">Error loading departments</option>');
        });
    });

    const table = $('#permissions-table').DataTable({
        processing: true,
        ajax: "{{ route('permissions.list') }}",
        columns: [
            { data: 'id' },
            { data: 'school_name', defaultContent: '-' },
            { data: 'dept_name', defaultContent: '-' },
            { data: 'user_name', defaultContent: '-' },
            { data: 'status', defaultContent: '-' },
            { data: 'remarks', defaultContent: '-' },
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-sm btn-warning editBtn" data-id="${row.id}" data-status="${row.status || ''}" data-remarks="${row.remarks || ''}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="${row.id}">
                            <i class="fa fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        order: [[0, 'desc']]
    });

    $('#permissionForm').on('submit', function (e) {
        e.preventDefault();

        $.post("{{ route('permissions.store') }}", $(this).serialize())
            .done(function (res) {
                showToast('success', res.message, 'Success');
                $('#permissionForm')[0].reset();
                $('#user_id').val(null).trigger('change');
                $('#department_id').empty().append('<option value="">Select Department</option>');
                table.ajax.reload(null, false);
            })
            .fail(function (xhr) {
                let message = 'Error saving permission';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showToast('error', message, 'Error');
            });
    });

    $(document).on('click', '.editBtn', function () {
        $('#edit_id').val($(this).data('id'));
        $('#edit_status').val($(this).data('status'));
        $('#edit_remarks').val($(this).data('remarks'));
        $('#editModal').modal('show');
    });

    $('#editForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#edit_id').val();

        $.post("{{ url('store-procurement/permissions') }}/" + id, $(this).serialize())
            .done(function (res) {
                showToast('success', res.message, 'Success');
                $('#editModal').modal('hide');
                table.ajax.reload(null, false);
            })
            .fail(function (xhr) {
                let message = 'Error updating permission';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showToast('error', message, 'Error');
            });
    });

    $(document).on('click', '.deleteBtn', function () {
        if (!confirm('Remove this department budget right?')) {
            return;
        }

        const id = $(this).data('id');
        $.ajax({
            url: "{{ url('store-procurement/permissions') }}/" + id,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                showToast('success', res.message, 'Deleted');
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                let message = 'Error deleting permission';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showToast('error', message, 'Error');
            }
        });
    });
});
</script>
@endsection
