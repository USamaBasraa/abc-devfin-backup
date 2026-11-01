@extends('layouts.app')
{{-- @dd("working"); --}}
@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />
@include('components.toast-notifications')

<style>
    .select2-container {
        width: 100% !important;
    }
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da !important;
        border-radius: 4px;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
        padding-left: 12px !important;
        padding-right: 30px !important;
        color: #495057;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        top: 1px !important;
        right: 5px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d !important;
    }
    .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .select2-results__option {
        padding: 8px 12px;
    }
    .select2-results__option--highlighted {
        background-color: #3e5871 !important;
    }
    .card-header {
        border-bottom: none;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .form-control {
        height: 38px;
    }

    /* Table Header Styling */
    table.dataTable thead > tr > th {
        background-color: #4562C3 !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 15px 12px !important;
        border: none !important;
        border-bottom: none !important;
        vertical-align: middle !important;
        text-align: center !important;
    }
    table.dataTable thead {
        background-color: #4562C3 !important;
    }
    table.dataTable thead tr {
        background-color: #4562C3 !important;
    }
    #data-table thead tr:first-child th:first-child {
        border-top-left-radius: 8px !important;
    }
    #data-table thead tr:first-child th:last-child {
        border-top-right-radius: 8px !important;
    }
    /* DataTables sorting icons - make them white */
    table.dataTable thead th.sorting:before,
    table.dataTable thead th.sorting:after,
    table.dataTable thead th.sorting_asc:before,
    table.dataTable thead th.sorting_asc:after,
    table.dataTable thead th.sorting_desc:before,
    table.dataTable thead th.sorting_desc:after {
        color: white !important;
        opacity: 0.7 !important;
    }
    /* Remove default DataTables border */
    table.dataTable {
        border-collapse: separate !important;
        border-spacing: 0 !important;
    }
    /* Ensure headers stay in one row */
    table.dataTable thead th {
        white-space: nowrap !important;
    }
    /* Hide duplicate thead created by DataTables scrollX */
    .dataTables_scrollBody thead {
        display: none !important;
    }
    .dataTables_scrollHead {
        overflow: visible !important;
    }
    /* Horizontal scroll styling */
    .table-responsive {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }
    /* Custom scrollbar for table */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .approval-page-title {
        color: #2c3e50;
        font-weight: 600;
    }

    .approval-form-card {
        border-radius: 10px;
        border: 1px solid #e8ecf3;
    }

    .approval-form-card .card-body {
        padding: 1.5rem;
    }

    .approval-section-title {
        color: #4562C3;
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 1rem;
    }

    .approval-table-section .dataTables_wrapper .dataTables_length,
    .approval-table-section .dataTables_wrapper .dataTables_filter {
        margin-bottom: 0;
    }

    .approval-table-section .dt-controls {
        gap: 10px;
    }

    .approval-table-section .dt-footer {
        width: 100%;
    }

    .approval-type-badge {
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.4em 0.75em;
        white-space: nowrap;
        border-radius: 20px;
    }

    .badge-type-budget {
        background-color: #eef2ff;
        color: #4562C3;
        border: 1px solid #c7d2fe;
    }

    .badge-type-price {
        background-color: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .approval-type-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 1rem;
    }

    .approval-type-tab {
        border: 1px solid #d1d5db;
        background: #fff;
        color: #4b5563;
        border-radius: 20px;
        padding: 6px 16px;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .approval-type-tab:hover {
        border-color: #4562C3;
        color: #4562C3;
    }

    .approval-type-tab.active {
        background: #4562C3;
        border-color: #4562C3;
        color: #fff;
    }

    #data-table tbody td {
        vertical-align: middle;
        font-size: 0.9rem;
    }

    #data-table tbody tr:hover {
        background-color: #f8f9fc;
    }
</style>

<div class="container-fluid py-3">
    <div class="mb-3">
        <h4 class="mb-1 approval-page-title">Approval Configuration Management</h4>
        <p class="text-muted mb-0">Configure budget permissions and price range approval workflows.</p>
    </div>
    <div class="card shadow-sm approval-form-card">
        <div class="card-body">
            <form id="approvalForm">
                @csrf

                <div class="row">
                    <!-- Main Selection -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="approval_type" class="font-weight-bold">Approval Type</label>
                            <select id="approval_type" name="approval_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="budget_permission">Budget Permission</option>
                                <option value="price_range_approval">Price Range Approval</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ===================== BUDGET PERMISSION SECTION ===================== -->
                <div id="budget_permission_section" class="d-none">
                    <div class="row">
                        <!-- School Dropdown -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="school_id_budget" class="font-weight-bold">School</label>
                                <select id="school_id_budget" name="school_id_budget" class="form-control">
                                    <option value="">Select School</option>
                                    @foreach($schools as $school)
                                        <option value="{{ $school->school_id }}">{{ $school->school_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Department Dropdown -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="department_id_budget" class="font-weight-bold">Department</label>
                                <select id="department_id_budget" name="department_id_budget" class="form-control">
                                    <option value="">Select Department</option>
                                </select>
                            </div>
                        </div>

                        <!-- Approval Body -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="allowed_user_id" class="font-weight-bold">Allowed Person</label>
                                <select class="form-control select2-show-search" id="allowed_user_id" name="allowed_user_id">
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}">{{$user->prefix}} {{$user->full_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-3">
                            <label class="d-block">&nbsp;</label>
                            <button type="submit" id="submitBtn" class="btn btn-primary btn-sm">
                                 Save
                            </button>
                            <button type="button" id="loadingBtn" class="btn btn-secondary btn-sm d-none" disabled>
                                <span class="spinner-border spinner-border-sm mr-1"></span> Saving...
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ===================== PRICE RANGE APPROVAL SECTION ===================== -->
                <div id="price_range_section" class="d-none">
                    <div class="row">
                        <!-- Type Selection -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="type" class="font-weight-bold">Type</label>
                                <select id="type" name="type" class="form-control">
                                    <option value="">Select Type</option>
                                    <option value="academic">Academic</option>
                                    <option value="non_academic">Non Academic</option>
                                </select>
                            </div>
                        </div>

                        <!-- Shared column: School / Department -->
                        <div class="col-md-3">
                            <!-- School Dropdown -->
                            <div class="form-group d-none" id="school_div">
                                <label for="school_id" class="font-weight-bold">School</label>
                                <select id="school_id" name="school_id" class="form-control">
                                    <option value="">Select School</option>
                                    @foreach($schools as $school)
                                        <option value="{{ $school->school_id }}">{{ $school->school_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Department Dropdown -->
                            <div class="form-group d-none" id="department_div">
                                <label for="department_id" class="font-weight-bold">Department</label>
                                <select id="department_id" name="department_id" class="form-control">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Role Dropdown -->
                        <div class="col-md-3">
                            <div class="form-group d-none" id="role_div">
                                <label for="role" class="font-weight-bold">Role</label>
                                <select name="role" id="role" class="form-control">
                                    <option value="">Select Role</option>
                                    <option value="hod">Head of Department</option>
                                    <option value="gm">GM</option>
                                    <option value="purchase_committee_chair_person">Purchase Committee Chair Person</option>
                                    <option value="purchase_committee_line_department">Purchase Committee Line Department</option>
                                    <option value="purchase_committee_member">Purchase Committee Member</option>
                                    <option value="vc">VC</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Min Price -->
                        <div class="col-md-3" id="price_range_div1">
                            <div class="form-group">
                                <label for="min_price" class="font-weight-bold">Min Price</label>
                                <input type="number" name="min_price" step="0.01" min="0" class="form-control" placeholder="Min price">
                            </div>
                        </div>

                        <!-- Max Price -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="max_price" class="font-weight-bold">Max Price</label>
                                <input type="number" name="max_price" step="0.01" min="0" class="form-control" placeholder="Max price">
                            </div>
                        </div>

                        <!-- Approval Body -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="approval_user_id" class="font-weight-bold">Approver</label>
                                <select class="form-control select2-show-search" id="approval_user_id" name="approval_user_id">
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}">{{$user->prefix}} {{$user->full_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-3">
                            <label class="d-block">&nbsp;</label>
                            <button type="submit" id="submitBtn" class="btn btn-primary btn-sm">
                                <i class="fa fa-save mr-1"></i> Save
                            </button>
                            <button type="button" id="loadingBtn" class="btn btn-secondary btn-sm d-none" disabled>
                                <span class="spinner-border spinner-border-sm mr-1"></span> Saving...
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <hr class="my-4">

            <div class="approval-section-title">
                <i class="fa fa-list-alt mr-1"></i> Configuration Records
            </div>

            <div class="approval-type-tabs" id="approvalTypeTabs">
                <button type="button" class="approval-type-tab active" data-type="">All Records</button>
                <button type="button" class="approval-type-tab" data-type="budget_permission">Budget Permission</button>
                <button type="button" class="approval-type-tab" data-type="price_range_approval">Price Range Approval</button>
            </div>

            <div class="approval-table-section">
                <table id="data-table" class="table table-hover mb-0" style="width: 100%;">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>School</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Min Price</th>
                        <th>Max Price</th>
                        <th>Approver</th>
                        <th>Allowed Person</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function () {

     $('#school_id_budget').on('change', function() {
        var schoolId = $(this).val();
        $('#department_id_budget').empty().append('<option value="">Loading...</option>');

        if (schoolId) {
            $.ajax({
                url: '/get-departments/' + schoolId,
                type: 'GET',
                success: function(response) {
                    $('#department_id_budget').empty().append('<option value="">Select Department</option>');
                    $.each(response, function(key, dept) {
                        $('#department_id_budget').append(
                            `<option value="${dept.dept_id}">${dept.dept_name}</option>`
                        );
                    });
                },
                error: function() {
                    $('#department_id_budget').empty().append('<option value="">Error loading departments</option>');
                }
            });
        } else {
            $('#department_id_budget').empty().append('<option value="">Select Department</option>');
        }
    });

    // ✅ Initialize Select2
    $('.select2-show-search').select2({
        placeholder: 'Select User',
        allowClear: true,
        width: '100%',
        theme: 'default'
    });

    // Initialize DataTable
    const roleMap = {
        'hod': 'Head of Department',
        'gm': 'GM',
        'vc': 'VC',
        'purchase_committee_chair_person': 'Purchase Committee Chair Person',
        'purchase_committee_line_department': 'Purchase Committee Line Department',
        'purchase_committee_member': 'Purchase Committee Member'
    };

    const approvalTypeMap = {
        'budget_permission': {
            label: 'Budget Permission',
            badgeClass: 'badge-type-budget'
        },
        'price_range_approval': {
            label: 'Price Range Approval',
            badgeClass: 'badge-type-price'
        }
    };

    let activeTypeFilter = '';

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        if (settings.nTable.id !== 'data-table') {
            return true;
        }

        if (!activeTypeFilter) {
            return true;
        }

        const api = new $.fn.dataTable.Api(settings);
        const rowData = api.row(dataIndex).data();
        return rowData && rowData.type === activeTypeFilter;
    });

    let table = $('#data-table').DataTable({
        autoWidth: false,
        processing: true,
        serverSide: false,
        responsive: true,
        pageLength: 10,
        lengthChange: true,
        lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, 'All']],
        order: [[0, 'desc']],
        dom: "<'d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3'<'dt-controls d-flex align-items-center flex-wrap'lB>f>" +
             "<'table-responsive'rt>" +
             "<'dt-footer d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-3'ip>",
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'Approval Configuration Records',
                exportOptions: {
                    columns: ':not(:last-child)',
                    orthogonal: 'export'
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf-o"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Approval Configuration Records',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':not(:last-child)',
                    orthogonal: 'export'
                }
            }
        ],
        ajax: {
            url: "{{ url('sp-approval-master/list') }}",
            type: 'GET',
            dataType: 'json',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { data: 'school_name', defaultContent: '-' },
            { data: 'dept_name', defaultContent: '-' },
            {
                data: 'role',
                defaultContent: '-',
                render: function (data, type) {
                    if (!data) {
                        return '-';
                    }
                    const label = roleMap[data] || data;
                    return type === 'export' ? label : label;
                }
            },
            {
                data: 'min_price',
                defaultContent: '-',
                render: function (data, type) {
                    if (!data && data !== 0) {
                        return '-';
                    }
                    const formatted = Number(data).toLocaleString();
                    return type === 'export' ? formatted : formatted;
                }
            },
            {
                data: 'max_price',
                defaultContent: '-',
                render: function (data, type) {
                    if (!data && data !== 0) {
                        return '-';
                    }
                    const formatted = Number(data).toLocaleString();
                    return type === 'export' ? formatted : formatted;
                }
            },
            { data: 'approver_name', defaultContent: '-' },
            { data: 'allowed_person_name', defaultContent: '-' },
            {
                data: 'type',
                defaultContent: '-',
                render: function (data, type) {
                    if (!data || !approvalTypeMap[data]) {
                        return '-';
                    }

                    const config = approvalTypeMap[data];
                    if (type === 'export' || type === 'filter' || type === 'sort') {
                        return config.label;
                    }

                    return `<span class="badge approval-type-badge ${config.badgeClass}">${config.label}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    if (type === 'export') {
                        return '';
                    }
                    return `
                        <button class="btn btn-sm btn-outline-danger deleteBtn" data-id="${row.id}" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        language: {
            emptyTable: 'No configuration records found',
            loadingRecords: 'Loading...',
            processing: 'Loading records...',
            search: 'Search:',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'Showing 0 to 0 of 0 entries',
            infoFiltered: '(filtered from _MAX_ total entries)',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }
        }
    });

    $('#approvalTypeTabs').on('click', '.approval-type-tab', function () {
        $('#approvalTypeTabs .approval-type-tab').removeClass('active');
        $(this).addClass('active');
        activeTypeFilter = $(this).data('type') || '';
        table.draw();
    });

    function resetBudgetPermissionFields() {
        $('#school_id_budget').val('');
        $('#department_id_budget').empty().append('<option value="">Select Department</option>');
        $('#allowed_user_id').val(null).trigger('change');
    }

    function resetPriceRangeFields() {
        $('#type').val('');
        $('#school_id').val('');
        $('#department_id').val('');
        $('#role').val('');
        $('input[name="min_price"], input[name="max_price"]').val('');
        $('#approval_user_id').val(null).trigger('change');
        $('#school_div, #department_div, #role_div').addClass('d-none');
    }

    function updatePriceRangeTypeFields() {
        const type = $('#type').val();

        $('#school_div, #department_div, #role_div').addClass('d-none');

        if (type === 'academic') {
            $('#school_div, #role_div').removeClass('d-none');
        } else if (type === 'non_academic') {
            $('#department_div, #role_div').removeClass('d-none');
        }
    }

    // Show/hide sections based on approval type
    $('#approval_type').change(function () {
        const selected = $(this).val();

        resetBudgetPermissionFields();
        resetPriceRangeFields();

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

    // Type Change (Academic vs Non Academic)
    $('#type').change(function () {
        $('#school_id, #department_id, #role').val('');
        updatePriceRangeTypeFields();
    });
    // Save via AJAX
    $('#approvalForm').on('submit', function (e) {
        e.preventDefault();
        $('#submitBtn').addClass('d-none');
        $('#loadingBtn').removeClass('d-none');

        $.ajax({
            url: "{{ route('approval-master.store') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if (res.success) {
                    showToast('success', res.message, 'Success');

                    // Reset form
                    $('#approvalForm')[0].reset();
                    $('#approval_type').val('');
                    resetBudgetPermissionFields();
                    resetPriceRangeFields();
                    $('#budget_permission_section, #price_range_section').addClass('d-none');

                    // Reload DataTable
                    table.ajax.reload(null, false);
                } else {
                    showToast('error', res.message, 'Error');
                }
            },
            error: function(xhr) {
                let message = 'Error saving configuration';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showToast('error', message, 'Error');
            },
            complete: function() {
                $('#submitBtn').removeClass('d-none');
                $('#loadingBtn').addClass('d-none');
            }
        });
    });


    // Edit button click (if needed in future)
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

    // Update record (if needed in future)
    $('#editForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#edit_id').val();
        $.ajax({
            url: `{{ url('approval-master') }}/${id}`,
            type: 'PUT',
            data: $(this).serialize(),
            success: function (res) {
                $('#editModal').modal('hide');
                showToast('success', res.message, 'Updated');
                table.ajax.reload(null, false);
            },
            error: function(xhr) {
                let message = 'Error updating record';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showToast('error', message, 'Error');
            }
        });
    });

    // Delete button
    $(document).on('click', '.deleteBtn', function () {
        if (!confirm('Are you sure you want to delete this record?')) return;

        const id = $(this).data('id');
        const btn = $(this);

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');

        $.ajax({
            url: `{{ url('approval-master/delete') }}`,
            type: 'POST',
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            success: function (res) {
                if (res.success) {
                    showToast('success', res.message, 'Deleted');
                    table.ajax.reload(null, false);
                } else {
                    showToast('error', res.message, 'Error');
                    btn.prop('disabled', false).html('<i class="fa fa-trash"></i> Delete');
                }
            },
            error: function (xhr) {
                let message = 'Error deleting record';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showToast('error', message, 'Error');
                btn.prop('disabled', false).html('<i class="fa fa-trash"></i> Delete');
            }
        });
    });

});
</script>
@endsection
