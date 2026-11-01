@extends('layouts.app')

@section('content')
<!-- simplified table: removed custom styles -->

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="card-title mb-0">Budgetory Head Categories</h4>
                <button class="btn btn-primary" onclick="$('#createModal').modal('show')">
                    <i class="fas fa-plus me-1"></i> Add Category
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="categoryTable">
                    <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 15%">Name</th>
                            <th style="width: 20%">Description</th>
                            <th style="width: 20%">Parent Department</th>
                            <th style="width: 10%">Budget Type</th>
                            <th style="width: 20%">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        {{-- Data will be loaded dynamically --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="createCategoryForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group mb-3">
                        <label for="budget_type">Budget Type</label>
                        <select name="budget_type" id="budget_type" class="form-control" required>
                            <option value="">-- Select Budget Type --</option>
                            <option value="CapEx">Capital Expenditure (CapEx)</option>
                            <option value="OpEx">Operational Expenditure (OpEx)</option>
                        </select>
                    </div>
                      <div class="form-group mb-3">
                        <label for="edit_parent_department_id">Parent Department</label>
                        <select  name="parent_department_id"
                            id="parent_department_id" class="form-control" required>
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                            @endforeach
                        </select>
                    </div>



                    <div class="form-group mb-3">
                        <label for="category_name">Name</label>
                        <input
                            type="text"
                            name="category_name"
                            class="form-control"
                            placeholder="Enter category name"
                            required
                        >
                    </div>

                    <div class="form-group mb-2">
                        <label for="category_description">Description</label>
                        <textarea
                            name="category_description"
                            class="form-control"
                            placeholder="Enter description"
                            rows="3"
                        ></textarea>
                    </div>

                    <div class="form-group mb-2">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="q_parent_dept_required" name="q_parent_dept_required" value="1">
                            <label class="form-check-label" for="q_parent_dept_required">Quotation Request requires Parent Department</label>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="q_head_approval_required" name="q_head_approval_required" value="1">
                            <label class="form-check-label" for="q_head_approval_required">Quotation Request requires Head Approval</label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="editCategoryForm">
            @csrf
            <input type="hidden" name="id">

            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="edit_budget_type">Budget Type</label>
                        <select name="budget_type" id="edit_budget_type" class="form-control" required>
                            <option value="">-- Select Budget Type --</option>
                            <option value="CapEx">Capital Expenditure (CapEx)</option>
                            <option value="OpEx">Operational Expenditure (OpEx)</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_parent_department_id">Parent Department</label>
                        <select name="parent_department_id" id="edit_parent_department_id" class="form-control" required>
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="category_name">Name</label>
                        <input type="text" name="category_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="category_description">Description</label>
                        <textarea name="category_description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-group mb-2">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="edit_q_parent_dept_required" name="q_parent_dept_required" value="1">
                            <label class="form-check-label" for="edit_q_parent_dept_required">Quotation Request requires Parent Department</label>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="edit_q_head_approval_required" name="q_head_approval_required" value="1">
                            <label class="form-check-label" for="edit_q_head_approval_required">Quotation Request requires Head Approval</label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-success">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap-Select JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>




<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>

<script>


$('.selectpicker').selectpicker();


    $(document).ready(function () {

        fetchCategories();


function fetchCategories() {
    console.log("Fetching categories...");

    $.get('{{ route("vendor-category.list") }}', function(data) {
        console.log("Data received", data);
        let rows = '';
        data.forEach((cat, index) => {
            rows += `<tr>
                        <td>${index + 1}</td>
                       <td>${cat.category_name} (${cat.budget_type ?? ''})</td>

                        <td>${cat.category_description ?? ''}</td>
                        <td>${cat.parent_department_name ?? ''}</td>
                        <td>${cat.budget_type ?? ''}</td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editCategory(${cat.category_id})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteCategory(${cat.category_id})">Delete</button>
                        </td>
                    </tr>`
                    ;
        });
        $('#categoryTable tbody').html(rows);
    }).fail(function(xhr) {
        console.error("Fetch failed", xhr.responseText);
    });
}

        $('#createCategoryForm').on('submit', function(e) {

            e.preventDefault();
            $.post('{{ route("vendor-category.store") }}', $(this).serialize(), function(res) {
                $('#createModal').modal('hide');
                fetchCategories();
            });
        });

        $('#editCategoryForm').on('submit', function(e) {

            e.preventDefault();
            const id = $('#editCategoryForm input[name=id]').val();
            $.ajax({
                url: `/vendor-category/${id}`,
                type: 'post',
                data: $(this).serialize(),
                success: function(res) {
                    $('#editModal').modal('hide');
                    fetchCategories();
                }
            });
        });
    });

    function editCategory(id) {
        $.get(`/vendor-category/${id}/edit`, function(cat) {
            $('#editCategoryForm input[name=id]').val(cat.category_id);
            $('#editCategoryForm input[name=category_name]').val(cat.category_name);
            $('#editCategoryForm textarea[name=category_description]').val(cat.category_description);
            const parentDeptId = (cat.parent_department_id === null || cat.parent_department_id === undefined) ? '' : String(cat.parent_department_id);
            $('#edit_parent_department_id').val(parentDeptId).trigger('change');
            $('#edit_q_parent_dept_required').prop('checked', String(cat.q_parent_dept_required) === '1');
            $('#edit_q_head_approval_required').prop('checked', String(cat.q_head_approval_required) === '1');
            $('#editModal').modal('show');
        });
    }

    function deleteCategory(id) {
        if (confirm("Are you sure to delete this category?")) {
            $.ajax({
                url: `/vendor-category-delete/${id}`,
                type: 'post',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    alert(res.message);
                    fetchCategories();
                }
            });
        }
    }

</script>
@endsection
