@extends('layouts.app')

@section('content')
@include('components.toast-notifications')
<div class="container">

    <!-- ✅ Alert container -->


    <div class="card shadow p-4">
        <h4 class="mb-4 font-weight-bold">Approval Configuration</h4>

        <form id="approvalForm">
            @csrf

            <!-- Main Selection -->
            <div class="form-group">
                <label for="approval_type" class="font-weight-bold">Select Approval Type</label>
                <select id="approval_type" name="approval_type" class="form-control" required>
                    <option value="">-- Select Option --</option>
                    <option value="budget_permission">Budget Permission</option>
                    <option value="price_range_approval">Price Range Approval</option>
                </select>
            </div>

            <!-- ===================== BUDGET PERMISSION SECTION ===================== -->
            <div id="budget_permission_section" class="d-none">

                <!-- School Dropdown -->
                <div class="form-group">
                    <label for="school_id_budget" class="font-weight-bold">Select School</label>
                    <select id="school_id_budget" name="school_id_budget" class="form-control">
                        <option value="">-- Select School --</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->school_id }}">{{ $school->school_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Department Dropdown -->
                <div class="form-group">
                    <label for="department_id_budget" class="font-weight-bold">Select Department</label>
                    <select id="department_id_budget" name="department_id_budget" class="form-control">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Approval Body -->
                <div class="form-group">
                    <label for="allowed_user_id" class="font-weight-bold">Page Allowed Persons</label>
                    <select class="form-control select2-show-search" id="allowed_user_id" name="allowed_user_id" style="width: 100%;">
                        <option value="">-- Select User --</option>
                        @foreach($users as $user)
                            <option value="{{$user->id}}">{{$user->prefix}}{{$user->full_name}}</option>
                        @endforeach
                    </select>
                    {{-- <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple users.</small> --}}
                </div>
            </div>

            <!-- ===================== PRICE RANGE APPROVAL SECTION ===================== -->
            <div id="price_range_section" class="d-none">
                <!-- Type Selection -->
                <div class="form-group">
                    <label for="type" class="font-weight-bold">Select Type</label>
                    <select id="type" name="type" class="form-control">
                        <option value="">-- Select Type --</option>
                        <option value="academic">Academic</option>
                        <option value="non_academic">Non Academic</option>
                    </select>
                </div>

                <!-- School Dropdown -->
                <div class="form-group" id="school_div">
                    <label for="school_id" class="font-weight-bold">Select School</label>
                    <select id="school_id" name="school_id" class="form-control">
                        <option value="">-- Select School --</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->school_id }}">{{ $school->school_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Department Dropdown -->
                <div class="form-group" id="department_div">
                    <label for="department_id" class="font-weight-bold">Select Department</label>
                    <select id="department_id" name="department_id" class="form-control">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                        @endforeach
                    </select>
                </div>


                                        <!-- Non Academic: Role Dropdown -->
                <div class="col-md-6 d-none" id="role_div">
                    <label for="role" class="form-label">Select Role</label>
                    <select name="role" id="role" class="form-control">
                        <option value="">Select Role</option>
                        <option value="hod">Head of Department</option>
                        <option value="gm">GM</option>
                        <option value="purchase_committee">Purchase Committee</option>
                        <option value="vc">VC</option>
                    </select>
                </div>

                <!-- Price Range -->
                <div class="form-row" id="price_range_div1">
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
                <!-- <div class="form-group">
                    <label for="approval_user_id_price" class="font-weight-bold">Approval Body</label>
                    <select class="form-control select2-show-search" id="approval_user_id" name="approval_user_id_price" style="width: 100%;">
                        <option value="">-- Select User --</option>
                        @foreach($users as $user)
                            <option value="{{$user->id}}">{{$user->prefix}}{{$user->full_name}}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple users.</small>
                </div> -->

                <!-- Status -->
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
            </div>

            <!-- Submit Buttons -->
            <div class="text-right mt-4">
                    <div id="alert-container"></div>
                <button type="submit" id="submitBtn" class="btn btn-primary px-4 py-2">
                    <i class="fa fa-save mr-1"></i> Save
                </button>
                <button type="button" id="loadingBtn" class="btn btn-secondary px-4 py-2 d-none" disabled>
                    <span class="spinner-border spinner-border-sm"></span> Saving...
                </button>
            </div>
        </form>
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
                        <th>Approver Role</th>
                        <th>Min Price</th>
                        <th>Max Price</th>
                        <th>Approver</th>
                        <th>Allowed Person</th>
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
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {

     // ✅ Show/hide sections based on approval type
    $('#approval_type').change(function () {
        const selected = $(this).val();

        if (selected === 'budget_permission') {
            $('#budget_permission_section').removeClass('d-none');
            $('#price_range_section').addClass('d-none');
        } else if (selected === 'price_range_approval') {
            $('#price_range_section').removeClass('d-none');
            $('#budget_permission_section').addClass('d-none');
        } else {
            $('#budget_permission_section, #price_range_section').addClass('d-none');
        }
    });
    // ✅ Type Change (Academic vs Non Academic)
    $('#type').change(function () {
        const type = $(this).val();

        if (type === 'academic') {
            $('#school_div').removeClass('d-none');
            $('#department_div, #role_div').addClass('d-none');
        }
        else if (type === 'non_academic') {
            $('#department_div, #role_div').removeClass('d-none');
            $('#school_div').addClass('d-none');
        }
        else {
            $('#school_div, #department_div, #role_div').addClass('d-none');
        }
    });

    // // ✅ Show/hide School or Department based on type (Academic / Non Academic)
    // $('#type').change(function () {
    //     const type = $(this).val();
    //     if (type === 'academic') {
    //         $('#school_div').removeClass('d-none');
    //         $('#department_div').addClass('d-none');
    //     } else if (type === 'non_academic') {
    //         $('#department_div').removeClass('d-none');
    //         $('#school_div').addClass('d-none');
    //     } else {
    //         $('#school_div, #department_div').addClass('d-none');
    //     }
    // });

    // $('#approval_type').change(function () {
    //     const selected = $(this).val();

    //     if (selected === 'budget_permission') {
    //         $('#budget_permission_section').removeClass('d-none');
    //         $('#price_range_section').addClass('d-none');
    //     } else if (selected === 'price_range_approval') {
    //         $('#price_range_section').removeClass('d-none');
    //         $('#budget_permission_section').addClass('d-none');
    //     } else {
    //         $('#budget_permission_section').addClass('d-none');
    //         $('#price_range_section').addClass('d-none');
    //     }
    // });

    // // Optional: show school/department based on type
    // $('#type').change(function () {
    //     const type = $(this).val();
    //     if (type === 'academic') {
    //         $('#school_div1').removeClass('d-none');
    //         $('#department_div').addClass('d-none');
    //     } else if (type === 'non_academic') {
    //         $('#department_div1').removeClass('d-none');
    //         $('#school_div').addClass('d-none');
    //     } else {
    //         $('#school_div, #department_div').addClass('d-none');
    //     }
    // });


});




$(function () {

    // Load existing records
    loadData();





    //////////////



    // Save via AJAX
    $('#approvalForm').on('submit', function (e) {
        e.preventDefault();
        $('#submitBtn').addClass('d-none');
        $('#loadingBtn').removeClass('d-none');

        $.post("{{ route('approval-master.store') }}", $(this).serialize())
            .done(res => {
                  showToast('success', 'Quotation requests sent successfully!', 'Success');
                    setTimeout(function() {
                    location.reload();
                    }, 1500);
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
                        <td>${item.role ?? '-'}</td>
                        <td>${item.min_price ?? '-'}</td>
                        <td>${item.max_price ?? '-'}</td>
                        <td>${item.user_id ?? '-'}</td>
                        <td>${item.allowed_user_id ?? '-'}</td>
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

