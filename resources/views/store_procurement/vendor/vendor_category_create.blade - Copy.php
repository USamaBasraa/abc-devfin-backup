@extends('layouts.app')

<link href="/css/bnu-layout.css" rel="stylesheet" />


@section('content')

<div class="container-fluid px-4 py-4">

    <div class="card-header" style="background: #4562C3; color: white;">
                <h4 class="mb-0">
                    Budgetary Head Categories
                </h4>
    </div>

    {{-- <div class="card-header" style="background: #4562C3; color: white;">
        <h4 class="page-header" style="font-family: 'Montserrat', sans-serif;">Budgetary Head Categories</h4>
    </div> --}}
    <!-- Add/Edit Category Form (Moved to Top) -->
    <div class="content-card mb-4">
        <h5 class="mb-4" style="font-weight: 600; color: #252525;" id="formTitle">Add Category</h5>
        <form id="categoryForm">
            @csrf
            <input type="hidden" name="category_id" id="category_id">
            <!-- Row 1: 3 fields -->
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="budget_type">Budget Type</label>
                    <select name="budget_type" id="budget_type" class="form-control" required>
                        <option value="">-- Select Budget Type --</option>
                        <option value="CapEx">Capital Expenditure (CapEx)</option>
                        <option value="OpEx">Operational Expenditure (OpEx)</option>
                    </select>
                    <div class="invalid-feedback" id="budget_type_error"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="parent_department_id">Parent Department</label>
                    <select name="parent_department_id" id="parent_department_id" class="form-control" required>
                        <option value="">-- Select Department --</option>
                        <option value="0">By Self</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="parent_department_id_error"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="category_name">Name</label>
                    <input type="text" name="category_name" id="category_name" class="form-control" placeholder="Enter category name" required>
                    <div class="invalid-feedback" id="category_name_error"></div>
                </div>
            </div>

            <!-- Row 2: Description -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="category_description">Description</label>
                    <textarea name="category_description" id="category_description" class="form-control" placeholder="Enter description" rows="3"></textarea>
                    <div class="invalid-feedback" id="category_description_error"></div>
                </div>
            </div>

            <!-- Row 4: Both checkboxes -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="d-flex align-items-center" style="gap: 40px;">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="q_parent_dept_required" name="q_parent_dept_required" value="1">
                            <label class="form-check-label" for="q_parent_dept_required">Quotation Request requires Parent Department</label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="q_head_approval_required" name="q_head_approval_required" value="1">
                            <label class="form-check-label" for="q_head_approval_required">Quotation Request requires Head Approval</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 5: Approvers (Hidden by default) -->
            <div class="row" id="approvers_row" style="display: none;">
                <div class="col-md-6 mb-3" id="parent_dept_users_col" style="display: none;">
                    <label for="q_parent_dept_users" class="form-label">Approver (Parent Department) <span class="text-danger">*</span></label>
                    <select class="form-control select2-show-search" id="q_parent_dept_users" name="q_parent_dept_users[]" multiple>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ trim($user->prefix . ' ' . $user->full_name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3" id="head_approval_users_col" style="display: none;">
                    <label for="q_head_approval_users" class="form-label">Approver (Head Approval) <span class="text-danger">*</span></label>
                    <select class="form-control select2-show-search" id="q_head_approval_users" name="q_head_approval_users[]" multiple>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ trim($user->prefix . ' ' . $user->full_name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-danger" onclick="resetForm()" >Reset</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
            </div>
        </form>
    </div>

    <!-- Categories Table -->
    <div class="content-card">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="categoryTable" style="width:100%">
                <thead>
                    <tr>
                        <th>Sr.</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Parent Department</th>
                        <th>Budget Type</th>
                        <th>Parent Dept Approval</th>
                        <th>Head Approval</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #4562C3;">
                <h5 class="modal-title" id="deleteConfirmModalLabel" style="color: #FFFFFF;">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: #FFFFFF;">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this category? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
    </div>

@include('components.toast-notifications')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        // Initialize DataTable
        var categoryTable = $('#categoryTable').DataTable({
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12 col-md-12"B>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm mr-2',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                }
            ],
            ajax: {
                url: '{{ route("vendor-category.list") }}',
                dataSrc: ''
            },
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    },
                    orderable: false,
                    searchable: false
                },
                { data: 'category_name' },
                {
                    data: 'category_description',
                    render: function(data) {
                        return data ?? '-';
                    }
                },
                {
                    data: 'parent_department_id',
                    render: function(data, type, row) {
                        return data == 0 ? 'By Self' : (row.parent_department_name ?? '-');
                    }
                },
                {
                    data: 'budget_type',
                    render: function(data) {
                        return data ?? '-';
                    }
                },
                {
                    data: 'q_parent_dept_required',
                    render: function(data) {
                        return String(data) === '1' ? 'Yes' : 'No';
                    }
                },
                {
                    data: 'q_head_approval_required',
                    render: function(data) {
                        return String(data) === '1' ? 'Yes' : 'No';
                    }
                },
                {
                    data: 'category_id',
                    render: function(data) {
                        return `
                            <button class="btn btn-primary btn-sm" onclick="editCategory(${data}); return false;">
                                <i class="fa fa-edit mr-1"></i>Edit
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteCategory(${data}); return false;">
                                <i class="fa fa-trash mr-1"></i>Delete
                            </button>
                        `;
                    },
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[1, 'asc']],
            language: {
                emptyTable: "No categories found",
                loadingRecords: "Loading...",
                processing: "Processing...",
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            pageLength: 10,
            responsive: true
        });

        // Function to reload DataTable
        function fetchCategories() {
            categoryTable.ajax.reload(null, false);
        }

        // Function to initialize select2 on a specific element
        function initializeSelect2(selector) {
            if (!$(selector).hasClass('select2-hidden-accessible')) {
                $(selector).select2({
                    placeholder: 'Select Users',
                    allowClear: true,
                    width: '100%',
                    theme: 'default',
                    templateResult: formatUser,
                    templateSelection: formatUser,
                    escapeMarkup: function(markup) { return markup; }
                });
            }
        }

        // Format function for displaying users properly
        function formatUser(user) {
            if (!user.id) {
                return user.text;
            }
            return user.text;
        }

        // Handle checkbox change for Parent Department Users
        $('#q_parent_dept_required').on('change', function() {
            if ($(this).is(':checked')) {
                $('#approvers_row').show();
                $('#parent_dept_users_col').show();
                initializeSelect2('#q_parent_dept_users');
                $('#q_parent_dept_users').prop('required', true);
            } else {
                $('#parent_dept_users_col').hide();
                $('#q_parent_dept_users').prop('required', false);
                $('#q_parent_dept_users').val(null).trigger('change');
                // Hide main row if both columns are hidden
                if (!$('#q_head_approval_required').is(':checked')) {
                    $('#approvers_row').hide();
                }
            }
        });

        // Handle checkbox change for Head Approval Users
        $('#q_head_approval_required').on('change', function() {
            if ($(this).is(':checked')) {
                $('#approvers_row').show();
                $('#head_approval_users_col').show();
                initializeSelect2('#q_head_approval_users');
                $('#q_head_approval_users').prop('required', true);
            } else {
                $('#head_approval_users_col').hide();
                $('#q_head_approval_users').prop('required', false);
                $('#q_head_approval_users').val(null).trigger('change');
                // Hide main row if both columns are hidden
                if (!$('#q_parent_dept_required').is(':checked')) {
                    $('#approvers_row').hide();
                }
            }
        });

        function clearValidationErrors() {
            const fields = ['budget_type','parent_department_id','category_name','category_description'];
            fields.forEach(function(field){
                const $input = $('#' + field);
                $input.removeClass('is-invalid');
                $('#' + field + '_error').text('');
            });
        }

        function applyValidationErrors(errors) {
            Object.keys(errors || {}).forEach(function(key){
                const messages = errors[key];
                const $input = $('#' + key);
                const $error = $('#' + key + '_error');
                if ($input.length) {
                    $input.addClass('is-invalid');
                }
                if ($error.length) {
                    $error.text(Array.isArray(messages) ? messages[0] : String(messages));
                }
            });
        }

        function firstErrorMessage(errors) {
            if (!errors) return '';
            const firstKey = Object.keys(errors)[0];
            if (!firstKey) return '';
            const msgs = errors[firstKey];
            return Array.isArray(msgs) ? msgs[0] : String(msgs);
        }

        // Handle form submission for both create and edit
        $('#categoryForm').on('submit', function(e) {
            e.preventDefault();

            const categoryId = $('#category_id').val();
            const isEdit = categoryId !== '';
            clearValidationErrors();

            if (isEdit) {
                // Update existing category
                $.ajax({
                    url: `/vendor-category/${categoryId}/update`,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (typeof showToast === 'function') {
                            showToast('success', res.message || 'Category updated successfully');
                        }
                        resetForm();
                        fetchCategories();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            applyValidationErrors(xhr.responseJSON.errors);
                            if (typeof showToast === 'function') {
                                showToast('error', firstErrorMessage(xhr.responseJSON.errors) || 'Validation error');
                            }
                        } else {
                            if (typeof showToast === 'function') {
                                showToast('error', (xhr.responseJSON?.message || 'Failed to update category'));
                            }
                        }
                    }
                });
            } else {
                // Create new category
                $.post('{{ route("vendor-category.store") }}', $(this).serialize(), function(res) {
                    if (typeof showToast === 'function') {
                        showToast('success', res.message || 'Category created successfully');
                    }
                    resetForm();
                    fetchCategories();
                }).fail(function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        applyValidationErrors(xhr.responseJSON.errors);
                        if (typeof showToast === 'function') {
                            showToast('error', firstErrorMessage(xhr.responseJSON.errors) || 'Validation error');
                        }
                    } else {
                        if (typeof showToast === 'function') {
                            showToast('error', (xhr.responseJSON?.message || 'Failed to create category'));
                        }
                    }
                });
            }
        });

        // Edit category - populate form with data
        window.editCategory = function(id) {
            $.get(`/vendor-category/${id}/edit`, function(cat) {
                // Change form title and button
                $('#formTitle').text('Edit Category');
                $('#submitBtn').text('Update');

                // Populate form fields
                $('#category_id').val(cat.category_id);
                $('#category_name').val(cat.category_name);
                $('#category_description').val(cat.category_description);
                $('#budget_type').val(cat.budget_type);

                // Handle parent_department_id: if null/undefined = '', if 0 = '0', else convert to string
                let parentDeptId = '';
                if (cat.parent_department_id !== null && cat.parent_department_id !== undefined) {
                    parentDeptId = String(cat.parent_department_id);
                }
                $('#parent_department_id').val(parentDeptId);

                $('#q_parent_dept_required').prop('checked', String(cat.q_parent_dept_required) === '1');
                $('#q_head_approval_required').prop('checked', String(cat.q_head_approval_required) === '1');

                // Show/hide user dropdowns based on checkbox values
                var showApproversRow = false;

                if (String(cat.q_parent_dept_required) === '1') {
                    showApproversRow = true;
                    $('#parent_dept_users_col').show();
                    initializeSelect2('#q_parent_dept_users');
                    $('#q_parent_dept_users').prop('required', true);

                    // Populate parent dept users
                    if (cat.q_parent_dept_users) {
                        setTimeout(function() {
                            var parentUsers = cat.q_parent_dept_users.split(',').map(function(id) { return id.trim(); });
                            $('#q_parent_dept_users').val(parentUsers).trigger('change');
                        }, 100);
                    }
                } else {
                    $('#parent_dept_users_col').hide();
                    $('#q_parent_dept_users').prop('required', false);
                }

                if (String(cat.q_head_approval_required) === '1') {
                    showApproversRow = true;
                    $('#head_approval_users_col').show();
                    initializeSelect2('#q_head_approval_users');
                    $('#q_head_approval_users').prop('required', true);

                    // Populate head approval users
                    if (cat.q_head_approval_users) {
                        setTimeout(function() {
                            var headUsers = cat.q_head_approval_users.split(',').map(function(id) { return id.trim(); });
                            $('#q_head_approval_users').val(headUsers).trigger('change');
                        }, 100);
                    }
                } else {
                    $('#head_approval_users_col').hide();
                    $('#q_head_approval_users').prop('required', false);
                }

                // Show approvers row if either column should be visible
                if (showApproversRow) {
                    $('#approvers_row').show();
                } else {
                    $('#approvers_row').hide();
                }

                // Scroll to form
                $('html, body').animate({
                    scrollTop: $('#categoryForm').offset().top - 100
                }, 500);
            }).fail(function(xhr) {
                if (typeof showToast === 'function') {
                    showToast('error', (xhr.responseJSON?.message || 'Failed to load category'));
                }
            });
        }

        let pendingDeleteCategoryId = null;

        window.deleteCategory = function(id) {
            pendingDeleteCategoryId = id;
            $('#deleteConfirmModal').modal('show');
        }

        $('#confirmDeleteBtn').on('click', function() {
            if (!pendingDeleteCategoryId) return;
            const id = pendingDeleteCategoryId;
            // disable button to prevent double click
            const $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                url: `/vendor-category-delete/${id}`,
                type: 'get',
                success: function(res) {
                    if (typeof showToast === 'function') {
                        showToast('success', res.message || 'Category deleted successfully');
                    }
                    $('#deleteConfirmModal').modal('hide');
                    fetchCategories();
                },
                error: function(xhr) {
                    if (typeof showToast === 'function') {
                        showToast('error', (xhr.responseJSON?.message || 'Failed to delete category'));
                    }
                },
                complete: function() {
                    pendingDeleteCategoryId = null;
                    $btn.prop('disabled', false);
                }
            });
        });

        window.resetForm = function() {
            $('#categoryForm')[0].reset();
            $('#category_id').val('');
            $('#formTitle').text('Add Category');
            $('#submitBtn').text('Save');

            // Reset and hide user dropdowns
            if ($('#q_parent_dept_users').hasClass('select2-hidden-accessible')) {
                $('#q_parent_dept_users').val(null).trigger('change');
            }
            $('#parent_dept_users_col').hide();
            $('#q_parent_dept_users').prop('required', false);

            if ($('#q_head_approval_users').hasClass('select2-hidden-accessible')) {
                $('#q_head_approval_users').val(null).trigger('change');
            }
            $('#head_approval_users_col').hide();
            $('#q_head_approval_users').prop('required', false);

            // Hide approvers row
            $('#approvers_row').hide();

            $('#q_parent_dept_required').prop('checked', false);
            $('#q_head_approval_required').prop('checked', false);
        }
    });
</script>
@endsection
