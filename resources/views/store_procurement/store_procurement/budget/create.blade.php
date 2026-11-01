@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />
@include('components.toast-notifications')



<style>
    /* Red border for invalid fields */
    .is-invalid {
        border-color: #dc3545 !important;
    }

    /* Focus state for invalid fields */
    .is-invalid:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    .select2-container--default .select2-selection--single.is-invalid {
            border: 1px solid #dc3545;
        }

    .table-responsive {
        overflow-x: auto;
        overflow-y: hidden;
    }

    .card,
    .card-body,
    .table-responsive,
    .tab-pane {
        overflow: visible !important;
    }


    .dropdown-menu {
    z-index: 9999 !important;
}

.select2-container {
    z-index: 9999 !important;
}



    /* Highlight error rows in table */
    tr.table-danger {
        background-color: #f8d7da !important;
        animation: highlightError 0.5s ease-in-out;
    }

    @keyframes highlightError {
        0% { background-color: #ffffff; }
        50% { background-color: #f5c6cb; }
        100% { background-color: #f8d7da; }
    }


    /* Ensure dropdowns display full text */
    #itemsTable select {
        min-width: 180px;
        white-space: nowrap;
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
        height: auto;
    }

    /* Table responsive fixes */
    #itemsTable td {
        vertical-align: middle;
    }

    /* Form styling to match second screenshot */
    .form-group label {
        font-weight: normal !important;
        color: #000;
    }

    /* Department field styling */
    .alert-info {
        background-color: #e9ecef !important;
        border: none !important;
        border-radius: 20px !important;
        color: #212529 !important;
        padding: 0.75rem 1.25rem !important;
    }

    /* Academic Year and Budget Head Title select styling */
    #academicYearSelect, #head_title {
        background-color: #e9ecef !important;
        border: none !important;
        border-radius: 20px !important;
        padding: 0.75rem 1.25rem !important;
        color: #6c757d;
    }

    #academicYearSelect + .select2-container .select2-selection--single,
    #head_title + .select2-container .select2-selection--single {
        background-color: #e9ecef !important;
        border: none !important;
        border-radius: 20px !important;
        min-height: 46px;
        display: flex;
        align-items: center;
    }

    #academicYearSelect + .select2-container .select2-selection--single .select2-selection__rendered,
    #head_title + .select2-container .select2-selection--single .select2-selection__rendered {
        padding-left: 1.25rem !important;
        color: #6c757d;
        line-height: normal !important;
    }

    #academicYearSelect + .select2-container .select2-selection--single .select2-selection__arrow,
    #head_title + .select2-container .select2-selection--single .select2-selection__arrow {
        height: 44px !important;
        right: 10px !important;
    }

    #academicYearSelect + .select2-container .select2-selection--single .select2-selection__placeholder,
    #head_title + .select2-container .select2-selection--single .select2-selection__placeholder {
        color: #6c757d !important;
    }

    #academicYearSelect option, #head_title option {
        color: #212529;
    }

    /* Remarks textarea styling */
    #remarks {
        background-color: #e9ecef !important;
        border: none !important;
        border-radius: 20px !important;
        padding: 0.75rem 1.25rem !important;
        color: #212529;
        min-height: 100px;
    }

    /* Helper text styling */
    .form-text {
        color: #6c757d !important;
        font-size: 0.875rem;
    }

    /* Table header styling to match second screenshot - applies to all tables */
    .table thead {
        background-color: #4562C3;
        color: white;
    }

    .table thead th {
        background-color: #4562C3 !important;
        color: white !important;
        border: none !important;
        padding: 12px 15px;
        text-align: left;
        font-weight: normal;
        white-space: nowrap;
    }

    .table thead tr th:first-child {
        border-top-left-radius: 20px;
        border-bottom-left-radius: 20px;
    }

    .table thead tr th:last-child {
        border-top-right-radius: 20px;
        border-bottom-right-radius: 20px;
    }

    .table thead th .text-danger {
        color: white !important;
    }

    /* Button styling with custom color */
    .btn-primary {
        background-color: #4562C3 !important;
        border-color: #4562C3 !important;
    }

    .btn-primary:hover {
        background-color: #3544a8 !important;
        border-color: #3544a8 !important;
    }

    .btn-info {
        background-color: #4562C3 !important;
        border-color: #4562C3 !important;
    }

    .btn-info:hover {
        background-color: #3544a8 !important;
        border-color: #3544a8 !important;
    }

    /* Budget actions styling */
    .budget-action-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .budget-action-trigger {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        color: #4562C3;
        cursor: pointer;
        transition: transform 0.2s ease-in-out;
    }

    .budget-action-trigger i {
        font-size: 0.9rem;
    }

    .budget-action-trigger:hover,
    .budget-action-wrapper.open .budget-action-trigger {
        transform: translateY(-1px);
    }

    .budget-action-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        min-width: 140px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 15px 35px rgba(18, 38, 63, 0.15);
        padding: 0.5rem 0;
        display: none;
        z-index: 10;
    }

    .budget-action-wrapper.open .budget-action-dropdown {
        display: block;
    }

    .budget-action-item {
        width: 100%;
        padding: 0.45rem 1rem;
        background: transparent;
        border: none;
        text-align: left;
        font-size: 0.85rem;
        color: #374151;
        transition: background 0.2s ease-in-out;
    }

    .budget-action-item:hover {
        background: #f4f6ff;
    }

    .budget-action-item.delete {
        color: #d9534f;
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    {{-- <h1 class="page-header">Create Department Budget</h1> --}}
    <div class="card-header" style="background: none !important; color: #252525 !important;padding-left:0px !important;border-bottom:none !important;">
                <h4 class="mb-0">
                    Create Department Budget
                </h4>
            </div>
    <p class="text-muted mb-4" style="color: #252525 !important;margin-top:-15px">{{ auth()->user()->department ?? 'Information Technology Resource Center' }}</p>

    <!-- Create Budget Form Card -->
    <div class="content-card mb-4">
        {{-- <h5 class="mb-4" style="font-weight: 600; color: #252525;">Create Department Budget</h5> --}}
    <form id="budgetForm">
        @csrf

        <!-- Department (fixed to logged-in user) -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Department <span class="text-danger">*</span></label>
                            <div class="alert alert-info mb-0">
                                <strong>{{ auth()->user()->department }}</strong>
                            </div>
                            <input type="hidden" name="dept_id" id="dept_id" value="{{ auth()->user()->dept_id }}">
        </div>

                        <div class="row">
        <!-- Academic Year -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Academic Year <span class="text-danger">*</span></label>
                                    <select class="form-control select2-show-search" id="academicYearSelect" name="year_id" required>
                <option value="">-- Select Academic Year --</option>
                @foreach($academic_years as $ay)
                                            <option value="{{ $ay->ay_id }}" {{ isset($year_id) && $year_id == $ay->ay_id ? 'selected' : '' }}>
                        {{ $ay->ay_name }}
                    </option>
                @endforeach
            </select>
                                    {{-- <small class="form-text text-muted">Select the academic year for this budget</small> --}}
                                </div>
        </div>

        <!-- Budget Head Title -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Budget Head Title <span class="text-danger">*</span></label>
                                    <select class="form-control select2-show-search" id="head_title" name="head_title" required>
                    <option value="">-- Select Budget Head Title --</option>
                    @foreach($categories as $cat)
                                            <option value="{{ $cat->category_name }} ({{ $cat->budget_type ?? '' }})" data-head-id="{{ $cat->category_id }}">
                            {{ $cat->category_name }} ({{ $cat->budget_type ?? '' }})
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="bh_cat_id" id="bh_cat_id">
                                    {{-- <small class="form-text text-muted">Select the budget category</small> --}}
                                </div>
                            </div>
            </div>

        <!-- Remarks -->
                        <div class="form-group mb-4">
                            <label class="font-weight-bold">General Remarks</label>
                            <textarea class="form-control" name="remarks" id="remarks" rows="2" placeholder="Enter any general remarks for this budget..." maxlength="2000"></textarea>
                            <small class="text-muted float-right"><span id="remarksCount">0</span>/2000</small>
        </div>

        <hr>

        <!-- Budget Items Section -->
        <h5 class="mb-3"> Add Budget Items</h5>

        <!-- Items Table -->
        <div class="table-responsive">
                            <table class="table text-start" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th style="min-width: 180px;">Item Title <span class="text-danger">*</span></th>
                                        <th style="min-width: 120px;">Brand</th>
                                        <th style="min-width: 120px;">Model</th>
                                        <th style="min-width: 120px;">Quantity <span class="text-danger">*</span></th>
                                        <th style="min-width: 120px;">Rate <span class="text-danger">*</span></th>
                                        <th style="min-width: 120px;">Amount</th>
                                        <th style="min-width: 160px;">Budget Type <span class="text-danger">*</span></th>
                                        <th style="min-width: 160px;">Purchase Mode <span class="text-danger">*</span></th>
                                        <th style="min-width: 150px;">Remarks</th>
                                        <th style="min-width: 90px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                                        <td>
                                            {{-- <input type="text" name="items[0][item_title]" class="form-control form-control-sm" placeholder="Enter item title" maxlength="1000" required> --}}
                                            <select name="items[0][item_id]"
                                            class="form-control form-control-sm item-select"
                                            data-loading-text="Loading items..."
                                            disabled
                                            required>
                                                <option value="">Loading items...</option>
                                            </select>

                                    <input type="hidden" name="items[0][item_title]" class="item-title-hidden">

                                        </td>
                                        <td>
                                            <input type="text" name="items[0][item_brand]" class="form-control form-control-sm" placeholder="Brand" maxlength="1000">
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][item_model]" class="form-control form-control-sm" placeholder="Model" maxlength="1000">
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][quantity]" class="form-control form-control-sm qty" min="1" step="1" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="items[0][rate]" class="form-control form-control-sm rate" min="0" placeholder="0.00" required>
                                        </td>
                                        <td><input type="text" class="form-control form-control-sm amount bg-light" readonly value="0.00"></td>
                                        <td>
                                            <select name="items[0][budget_type]" class="form-control" required>
                                                <option value="">Select Type</option>
                                <option value="budgeted">Budgeted</option>
                                <option value="unbudgeted">Unbudgeted</option>
                                <option value="reappropriated">Reappropriated</option>
                            </select>
                        </td>
                        <td>
                                            <select name="items[0][purchase_mode]" class="form-control" required>
                                <option value="">Select Mode</option>
                                <option value="purchase">Purchase</option>
                                <option value="rent">Rent</option>
                            </select>
                                        </td>
                                        <td><input type="text" name="items[0][remarks]" class="form-control form-control-sm" placeholder="Item remarks"></td>
                                        <td class="text-left">
                                            <button type="button" class="btn btn-danger btn-sm remove-row-btn" title="Remove Item">
                                                <i class="fa fa-trash"></i>
                                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Add Item Button -->
        <div class="mt-3">
            <button type="button" class="btn btn-link text-primary p-0" id="addItemBtn" style="text-decoration: underline;">
                <i class="fa fa-plus-circle"></i> Add more items
            </button>
        </div>

        <!-- Total -->
        <div class="mt-2">
            <h5 class="mb-0 font-weight-bold">Total: <span id="totalAmount" class="text-dark">0.00</span></h5>
        </div>

        <!-- Save Budget Button -->
        <div class="mt-3 d-flex flex-wrap gap-2">
            <button type="submit" form="budgetForm" class="btn btn-primary btn-lg">
                 Save Budget
            </button>
            <button type="button" class="btn btn-outline-secondary btn-lg" id="syncBudgetBtn">
                <i class="fa fa-refresh"></i> Sync from Pinnacle
            </button>
        </div>

                    </form>
                </div>
            </div>

            <!-- Saved Budgets Section -->
            <div class="content-card mt-4" style="margin-left: 15px; margin-right: 15px;">
                <h5 class="mb-4" style="font-weight: 600; color: #252525;"> Saved Budgets</h5>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label class="font-weight-bold">Filter by Academic Year</label>
                            <select class="form-control" id="filterAcademicYear">
                                <option value="">All Academic Years</option>
                                @foreach($academic_years as $ay)
                                    <option value="{{ $ay->ay_id }}">{{ $ay->ay_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="font-weight-bold">Filter by Budget Head</label>
                            <select class="form-control" id="filterBudgetHead">
                                <option value="">All Budget Heads</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->category_id }}">{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="font-weight-bold">&nbsp;</label>
                            <button type="button" class="btn btn-info btn-block" onclick="loadBudgetCards()">
                                 Apply
                            </button>
                        </div>
                    </div>

                    <hr>

                    <!-- Budget Cards Container -->
                    <div id="budgetCardsContainer">
                        <div class="text-center text-muted py-5">
                            <i class="fa fa-info-circle fa-3x mb-3"></i>
                            <p>Select filters and click Apply to view budget items</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('javascript')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function initSearchableSelect(selector, placeholder) {
    const $select = $(selector);

    if ($select.hasClass('select2-hidden-accessible')) {
        $select.select2('destroy');
    }

    $select.select2({
        placeholder: placeholder,
        allowClear: true,
        width: '100%',
        dropdownParent: $('body')
    });
}

function initHeadTitleSelect() {
    initSearchableSelect('#head_title', '-- Select Budget Head Title --');
}

function initAcademicYearSelect() {
    initSearchableSelect('#academicYearSelect', '-- Select Academic Year --');
}

function initItemSelect(selector) {
    $(selector).each(function () {
        const $select = $(this);

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        $select.select2({
            placeholder: $select.prop('disabled') ? 'Loading items...' : 'Search item...',
            allowClear: true,
            minimumInputLength: 0,
            width: '100%',
            dropdownParent: $('body'),
            data: window.budgetItemOptions || []
        });
    });
}

function setItemSelectLoadingState(selector, isLoading) {
    $(selector).each(function () {
        const $select = $(this);
        const currentValue = $select.val();

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        if (isLoading) {
            $select.prop('disabled', true);
            $select.html('<option value="">Loading items...</option>');
        } else {
            $select.prop('disabled', false);
            initItemSelect($select);

            if (currentValue) {
                $select.val(currentValue).trigger('change');
            }
        }
    });
}

function selectedBudgetHeadId() {
    return $('#bh_cat_id').val() || $('#head_title option:selected').data('head-id') || '';
}

function clearItemSelections() {
    $('.item-select').each(function () {
        const $select = $(this);
        const row = $select.closest('tr');

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.val(null).trigger('change');
        } else {
            $select.val('');
        }

        row.find('input[name*="[item_title]"]').val('');
        row.find('input[name*="[item_brand]"]').val('');
        row.find('input[name*="[item_model]"]').val('');
        row.find('input[name*="[rate]"]').val('');
        row.find('.amount').val('0.00');
    });

    calculateTotal();
}

function preloadBudgetItems(categoryId = selectedBudgetHeadId()) {
    setItemSelectLoadingState('.item-select', true);

    return $.ajax({
        url: '{{ route("sp.items.search") }}',
        type: 'GET',
        dataType: 'json',
        data: {
            q: '',
            category_id: categoryId || ''
        }
    }).done(function (data) {
        window.budgetItemOptions = (data || []).map(function (item) {
            return {
                id: String(item.item_id || ''),
                text: item.item_title || '',
                brand: item.brand,
                model: item.model,
                remarks: item.remarks,
                rate: item.rate,
                budget_type: item.budget_type,
                purchase_mode: item.purchase_mode,
                item_type: item.item_type,
                item_category: item.item_category,
                unit: item.unit,
                app_unit: item.app_unit,
                fin_acc_title: item.fin_acc_title
            };
        });

        setItemSelectLoadingState('.item-select', false);
    }).fail(function () {
        window.budgetItemOptions = [];
        $('.item-select').each(function () {
            const $select = $(this);
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            $select.prop('disabled', true);
            $select.html('<option value="">Unable to load items</option>');
        });
    });
}



$(document).on('select2:select', '.item-select', function (e) {
    let data = e.params.data;
    let row = $(this).closest('tr');

    row.find('input[name*="[item_title]"]').val(data.text);
    row.find('input[name*="[item_brand]"]').val(data.brand ?? '');
    row.find('input[name*="[item_model]"]').val(data.model ?? '');
    row.find('input[name*="[rate]"]').val(data.rate ?? 0);

    // Optional auto-select
    if (data.budget_type) {
        // row.find('select[name*="[budget_type]"]').val(data.budget_type);
        row.find('select[name*="[budget_type]"]')
   .val(data.budget_type)
   .trigger('change');
    }
    if (data.purchase_mode) {
        // row.find('select[name*="[purchase_mode]"]').val(data.purchase_mode);

        row.find('select[name*="[purchase_mode]"]')
   .val(data.purchase_mode)
   .trigger('change');
    }

    // Recalculate amount
    let qty = parseFloat(row.find('.qty').val()) || 0;
    let rate = parseFloat(data.rate) || 0;
    row.find('.amount').val((qty * rate).toFixed(2));
    calculateTotal();
});


// Reset Form Function
function resetBudgetForm() {
    if (confirm('Are you sure you want to reset the form?')) {
        location.reload();
    }
}

function syncBudgetFromPinnacle() {
    const $btn = $('#syncBudgetBtn');
    const originalHtml = $btn.html();

    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Syncing...');

    $.ajax({
        url: '{{ route("budget.sync") }}',
        type: 'POST',
        dataType: 'json',
        data: {
            _token: '{{ csrf_token() }}',
            year_id: $('#academicYearSelect').val()
        },
        success: function (response) {
            showToast('success', response.message || 'Budget synced successfully.', 'Success');
            loadBudgetCards();
        },
        error: function (xhr) {
            let message = 'Unable to sync budget from Pinnacle.';

            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }

            showToast('error', message, 'Sync Failed');
        },
        complete: function () {
            $btn.prop('disabled', false).html(originalHtml);
        }
    });
}

// Load budget cards on page load
$(document).ready(function() {
window.budgetItemOptions = [];
initAcademicYearSelect();
initHeadTitleSelect();
preloadBudgetItems();

    loadBudgetCards();

    $('#syncBudgetBtn').on('click', function () {
        syncBudgetFromPinnacle();
    });
});


$(document).on('click', function (e) {

    // ✅ allow clicks inside select2 input AND dropdown
    if (
        $(e.target).closest('.select2-container').length ||
        $(e.target).closest('.select2-dropdown').length
    ) {
        return;
    }

    $('.budget-action-wrapper').removeClass('open');
});




// Prevent closing when interacting with dropdown content
$(document).on('click', '.budget-action-dropdown', function(event) {
    event.stopPropagation();
});

function toggleBudgetActionMenu(event, button) {
    event.stopPropagation();
    const $wrapper = $(button).closest('.budget-action-wrapper');
    $('.budget-action-wrapper').not($wrapper).removeClass('open');
    $wrapper.toggleClass('open');
}

function handleBudgetAction(action, id) {
    $('.budget-action-wrapper').removeClass('open');
    if (action === 'edit') {
        editBudget(id);
    } else if (action === 'delete') {
        deleteBudget(id);
    }
}

// Load Budget Cards with filters
function loadBudgetCards() {
    $('#budgetCardsContainer').html('<div class="text-center py-5"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading...</p></div>');

    $.ajax({
        url: '{{ route("budget.cards.data") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            academic_year: $('#filterAcademicYear').val(),
            budget_head: $('#filterBudgetHead').val()
        },
        success: function(response) {
            console.log('Budget Cards Data:', response);

            if (response.budgets && response.budgets.length > 0) {
                let html = '';
                let grandTotal = 0;

                response.budgets.forEach(function(budget) {
                    console.log('Budget Head:', budget.head_title, 'Items:', budget.items.length);
                    let budgetTotal = 0;
                    let sanitizedHeadTitle = (budget.head_title || '').replace(/\s*\d+(?=\s*(\(|$))/g, '').replace(/\s+/g, ' ').trim();

                    html += `
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0" style="font-weight: 600; color: #4562C3;">
                                    ${sanitizedHeadTitle}
                                </h6>
                                <span class="badge badge-primary">
                                    ${budget.academic_year}
                                </span>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Item Title</th>
                                                <th>Brand</th>
                                                <th>Model</th>
                                                <th>Qty</th>
                                                <th>Rate</th>
                                                <th>Amount</th>
                                                <th>Budget Type</th>
                                                <th>Purchase Mode</th>
                                                <th style="width:150px">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;

                    if (budget.items && budget.items.length > 0) {
                        budget.items.forEach(function(item, index) {
                            let amount = parseFloat(item.quantity) * parseFloat(item.rate);
                            budgetTotal += amount;

                            console.log('Adding Item:', index + 1, item.item_title);

                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.item_title}</td>
                                    <td>${item.brand || '-'}</td>
                                    <td>${item.model || '-'}</td>
                                    <td class="text-left">${item.quantity}</td>
                                    <td class="text-left">Rs. ${parseFloat(item.rate).toFixed(2)}</td>
                                    <td class="text-left">Rs. ${amount.toFixed(2)}</td>
                                    <td>
                                        <span class="badge badge-${item.budget_type == 'budgeted' ? 'success' : 'warning'}">
                                            ${item.budget_type}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-${item.purchase_mode == 'purchase' ? 'primary' : 'info'}">
                                            ${item.purchase_mode}
                                        </span>
                                    </td>
                                    <td class="text-left">
                                        <div class="budget-action-wrapper">
                                            <button type="button" class="budget-action-trigger" onclick="toggleBudgetActionMenu(event, this)" title="Actions">
                                                <i class="fa fa-ellipsis-h"></i>
                                            </button>
                                            <div class="budget-action-dropdown">
                                                <button type="button" class="budget-action-item" onclick="handleBudgetAction('edit', ${item.item_id})">Edit</button>
                                                <button type="button" class="budget-action-item delete" onclick="handleBudgetAction('delete', ${item.item_id})">Delete</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>`;
                        });
                    } else {
                        html += `
                            <tr>
                                <td colspan="11" class="text-center text-muted">No items in this budget head</td>
                            </tr>`;
                    }

                    html += `
                                        </tbody>
                                        <tfoot>
                                            <tr style="background-color: #f8f9fa; font-weight: 600;">
                                                <td colspan="6" class="text-right">Total:</td>
                                                <td class="text-right text-primary">${budgetTotal.toFixed(2)}/</td>
                                                <td colspan="3"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                        </div>`;

                    grandTotal += budgetTotal;
                });

                // Grand Total
                if (grandTotal > 0) {
                    html += `
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mt-4">
                            <h4 class="mb-0 font-weight-bold">Grand Total:</h4>
                            <h4 class="mb-0 text-primary font-weight-bold">Rs. ${grandTotal.toFixed(2)}</h4>
                        </div>`;
                }

                $('#budgetCardsContainer').html(html);
            } else {
                $('#budgetCardsContainer').html(`
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle fa-2x mb-2"></i>
                        <p>No budget items found for selected filters.</p>
                    </div>
                `);
            }
        },
        error: function() {
            $('#budgetCardsContainer').html(`
                <div class="alert alert-danger text-center">
                    <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>Error loading budget items. Please try again.</p>
                </div>
            `);
        }
    });
}

let rowIndex = 1;

// Add Item Button - Using event listener to prevent multiple clicks
$('#addItemBtn').on('click', function() {
    addItemRow();
});

// Remove Row Button - Using event delegation
$(document).on('click', '.remove-row-btn', function() {
    $(this).closest('tr').remove();
    calculateTotal();
    clearFormErrors(); // Clear any validation errors
});

function addItemRow() {
    let row = `
    <tr>
        <td style="position: relative;">
               <select name="items[${rowIndex}][item_id]"
        class="form-control form-control-sm item-select"
        data-loading-text="Loading items..."
        disabled
        required>
            <option value="">Loading items...</option>
        </select>

                    <input type="hidden"
                        name="items[${rowIndex}][item_title]"
                        class="item-title-hidden">
                </td>
        <td>
            <input type="text" name="items[${rowIndex}][item_brand]" class="form-control form-control-sm" placeholder="Brand" maxlength="1000">
        </td>
        <td>
            <input type="text" name="items[${rowIndex}][item_model]" class="form-control form-control-sm" placeholder="Model" maxlength="1000">
        </td>
        <td>
            <input type="number" name="items[${rowIndex}][quantity]" class="form-control form-control-sm qty" min="1" step="1" placeholder="0" required>
        </td>
        <td>
            <input type="number" step="0.01" name="items[${rowIndex}][rate]" class="form-control form-control-sm rate" min="0" placeholder="0.00" required>
        </td>
        <td><input type="text" class="form-control form-control-sm amount bg-light" readonly value="0.00"></td>
        <td>
            <select name="items[${rowIndex}][budget_type]" class="form-control" required>
                <option value="">Select Type</option>
                <option value="budgeted">Budgeted</option>
                <option value="unbudgeted">Unbudgeted</option>
                <option value="reappropriated">Reappropriated</option>
            </select>
        </td>
        <td>
            <select name="items[${rowIndex}][purchase_mode]" class="form-control" required>
                <option value="">Select Mode</option>
                    <option value="purchase">Purchase</option>
                    <option value="rent">Rent</option>
            </select>
        </td>
        <td><input type="text" name="items[${rowIndex}][remarks]" class="form-control form-control-sm" placeholder="Item remarks"></td>
        <td class="text-left">
            <button type="button" class="btn btn-danger btn-sm remove-row-btn" title="Remove Item">
                <i class="fa fa-trash"></i>
            </button>
        </td>
    </tr>`;
    $('#itemsTable tbody').append(row);
    if (Array.isArray(window.budgetItemOptions) && window.budgetItemOptions.length) {
        setItemSelectLoadingState('#itemsTable tbody tr:last .item-select', false);
    } else {
        setItemSelectLoadingState('#itemsTable tbody tr:last .item-select', true);
    }
    rowIndex++;
}

$(document).on('input', '.qty, .rate', function() {
    let row = $(this).closest('tr');
    let qty = parseFloat(row.find('.qty').val()) || 0;
    let rate = parseFloat(row.find('.rate').val()) || 0;
    let amount = qty * rate;
    row.find('.amount').val(amount.toFixed(2));
    calculateTotal();
});

function calculateTotal() {
    let total = 0;
    $('.amount').each(function(){
        total += parseFloat($(this).val()) || 0;
    });
    $('#totalAmount').text(total.toFixed(2));
}

// Character counter for General Remarks
$(document).on('input', '#remarks', function() {
    $('#remarksCount').text($(this).val().length);
});

// Clear all form errors
function clearFormErrors() {
    // Remove error styling from all fields
    $('.is-invalid').removeClass('is-invalid');
    $('#academicYearSelect, #head_title').removeClass('is-invalid');
    $('#academicYearSelect, #head_title').each(function () {
        $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
    });
    $('tr.table-danger').removeClass('table-danger');
}

// Show inline error for specific field
function showFieldError(field, message) {
    // Only highlight table rows, not standalone fields
    let row = field.closest('tr');
    if (row.length) {
        field.addClass('is-invalid');
        row.addClass('table-danger');

        // Scroll to the first error row
        if ($('.table-danger').length === 1) {
            $('html, body').animate({
                scrollTop: row.offset().top - 100
            }, 500);
        }
    }
}

// Clear error when user starts typing
$(document).on('input change', 'input, select, textarea', function() {
    $(this).removeClass('is-invalid');
    if ($(this).is('#head_title, #academicYearSelect')) {
        $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
    }
    $(this).closest('tr').removeClass('table-danger');
});

// Client-side validation with inline errors
function validateForm() {
    clearFormErrors(); // Clear previous errors
    let isValid = true;

    // Check Academic Year
    if (!$('#academicYearSelect').val()) {
        $('#academicYearSelect').addClass('is-invalid');
        $('#academicYearSelect').next('.select2-container').find('.select2-selection').addClass('is-invalid');
        showToast('error', 'Please select an academic year.', 'Validation Error');
        isValid = false;
    }

    // Check Budget Head Title
    if (!$('#head_title').val()) {
        $('#head_title').addClass('is-invalid');
        $('#head_title').next('.select2-container').find('.select2-selection').addClass('is-invalid');
        showToast('error', 'Please select a budget head title.', 'Validation Error');
        isValid = false;
    }

    // Check if at least one item exists
    if ($('#itemsTable tbody tr').length === 0) {
        showToast('error', 'Please add at least one budget item.', 'Validation Error');
        isValid = false;
        return false;
    }

    // Check each item row with inline errors
    $('#itemsTable tbody tr').each(function(index) {
        let row = $(this);
        let itemTitleField = row.find('input[name*="[item_title]"]');
        let itemIdField = row.find('select[name*="[item_id]"]');

        let quantityField = row.find('input[name*="[quantity]"]');
        let rateField = row.find('input[name*="[rate]"]');
        let budgetTypeField = row.find('select[name*="[budget_type]"]');
        let purchaseModeField = row.find('select[name*="[purchase_mode]"]');

        // Validate Item Title
        if (!itemTitleField.val()) {
            showFieldError(itemTitleField, 'Required');
            isValid = false;
        }

        if (!itemIdField.val()) {
                showFieldError(itemIdField, 'Required');
                isValid = false;
            }


        // Validate Quantity
        let quantity = quantityField.val();
        if (!quantity || parseFloat(quantity) < 1) {
            showFieldError(quantityField, 'Min: 1');
            isValid = false;
        }

        // Validate Rate
        let rate = rateField.val();
        if (!rate || parseFloat(rate) < 0) {
            showFieldError(rateField, 'Min: 0');
            isValid = false;
        }

        // Validate Budget Type
        if (!budgetTypeField.val()) {
            showFieldError(budgetTypeField, 'Required');
            isValid = false;
        }

        // Validate Purchase Mode
        if (!purchaseModeField.val()) {
            showFieldError(purchaseModeField, 'Required');
            isValid = false;
        }
    });

    if (!isValid) {
        showToast('error', 'Please fix the highlighted errors in the form.', 'Validation Error');
    }

    return isValid;
}

// Bind Create Submit Handler
function bindCreateSubmitHandler() {
    $('#budgetForm').off('submit').on('submit', function(e){
    e.preventDefault();

        // Validate form before submission
        if (!validateForm()) {
            return false;
        }

        // Disable submit button to prevent double submission
        let submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

    $.ajax({
        url: '{{ route("budget.store") }}',
        method: 'POST',
        data: $(this).serialize(),
        success: function(res){
                showToast('success', res.message || 'Budget created successfully!', 'Success');

                // Reset form
            $('#budgetForm')[0].reset();
           $('.item-select').select2('destroy');
            initItemSelect('.item-select');

                $('#itemsTable tbody').find('tr:not(:first)').remove();
                rowIndex = 1;
            $('#totalAmount').text('0.00');
                clearFormErrors();
                submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Budget');

                // Reload budget cards
                loadBudgetCards();
        },
        error: function(xhr){
                // Re-enable submit button
                submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Budget');

                clearFormErrors(); // Clear previous errors

                // Handle validation errors
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    let errorCount = 0;

                    // Show inline errors for each field
                    $.each(errors, function(key, value) {
                        errorCount++;
                        let errorMsg = value[0];

                        // Check if error is for main form fields
                        if (key === 'year_id') {
                            $('#academicYearSelect').addClass('is-invalid');
                            $('#academicYearSelect').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                        } else if (key === 'head_title') {
                            $('#head_title').addClass('is-invalid');
                            $('#head_title').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                        } else if (key === 'bh_cat_id') {
                            $('#head_title').addClass('is-invalid');
                            $('#head_title').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                        } else if (key.startsWith('items.')) {
                            // Parse items.0.item_title to get row index and field name
                            let matches = key.match(/items\.(\d+)\.(.+)/);
                            if (matches) {
                                let rowIndex = matches[1];
                                let fieldName = matches[2];

                                // Find the specific field
                                let field = $(`[name="items[${rowIndex}][${fieldName}]"]`);
                                if (field.length) {
                                    showFieldError(field, errorMsg);
                                }
                            }
                        }
                    });

                    showToast('error', `Please fix ${errorCount} validation error(s) highlighted in the form.`, 'Validation Error');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    showToast('error', xhr.responseJSON.message, 'Error');
                } else if (xhr.responseText) {
                    try {
                        let response = JSON.parse(xhr.responseText);
                        showToast('error', response.message || 'An error occurred.', 'Error');
                    } catch (e) {
                        showToast('error', 'An unexpected error occurred.', 'Error');
                    }
                } else {
                    showToast('error', 'Error saving budget', 'Error');
                }
        }
    });
});
}

// Initialize create handler on page load
bindCreateSubmitHandler();


    $('#head_title').on('change select2:select select2:clear', function() {
        let headId = $(this).find(':selected').data('head-id') || '';
        $('#bh_cat_id').val(headId);
        clearItemSelections();
        preloadBudgetItems(headId);
    });
///////////////////////////////////////
function editBudget(itemId) {
    $.ajax({
        url: `/budget/${itemId}/edit`,
        type: 'GET',
        success: function (item) {
            console.log('Edit Item Data:', item);

            // Scroll to form for better UX
            $('html, body').animate({ scrollTop: $('#budgetForm').offset().top }, 500);

            // Fill general fields if they exist
            $('#academicYearSelect').val(item.ay_id).trigger('change');
            $('#bh_cat_id').val(item.bh_cat_id);

            // Set Budget Head Title dropdown by matching bh_cat_id
            let headTitleSet = false;
            $('#head_title option').each(function() {
                let optionHeadId = $(this).attr('data-head-id');
                console.log('Checking option:', $(this).val(), 'data-head-id:', optionHeadId, 'vs item.bh_cat_id:', item.bh_cat_id);

                if (optionHeadId == item.bh_cat_id) {
                    $('#head_title').val($(this).val()).trigger('change');
                    $('#bh_cat_id').val(optionHeadId);
                    headTitleSet = true;
                    console.log('Budget Head Title SET:', $(this).val());
                    return false;
                }
            });

            if (!headTitleSet) {
                console.error('Budget Head Title NOT SET! bh_cat_id:', item.bh_cat_id);
                showToast('warning', 'Could not set Budget Head Title. Please select manually.', 'Warning');
            }

            $('textarea[name="remarks"]').val(item.remarks || '');

            // Clear old rows
            $('#itemsTable tbody').empty();

            // Add one row with this item data
            let amount = (parseFloat(item.quantity) * parseFloat(item.rate)).toFixed(2);
            let row = `
                <tr>
                    <td>
                        <select name="items[0][item_id]" class="form-control item-select" required>
                            <option value=""></option>
                        </select>


                        <input type="hidden"
                            name="items[0][item_title]"
                            class="item-title-hidden">
                    </td>

                    <td>
                        <input type="text" name="items[0][item_brand]" class="form-control form-control-sm" value="${item.brand ?? ''}" maxlength="1000">
                    </td>
                    <td>
                        <input type="text" name="items[0][item_model]" class="form-control form-control-sm" value="${item.model ?? ''}" maxlength="1000">
                    </td>
                    <td>
                        <input type="number" name="items[0][quantity]" class="form-control form-control-sm qty" value="${item.quantity}" min="1" step="1" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="items[0][rate]" class="form-control form-control-sm rate" value="${item.rate}" min="0" required>
                    </td>
                    <td><input type="text" class="form-control form-control-sm amount bg-light" value="${amount}" readonly></td>
                    <td>
                        <select name="items[0][budget_type]" class="form-control">
                            <option value="budgeted" ${item.budget_type === 'budgeted' ? 'selected' : ''}>Budgeted</option>
                            <option value="unbudgeted" ${item.budget_type === 'unbudgeted' ? 'selected' : ''}>Unbudgeted</option>
                            <option value="unbudgeted" ${item.budget_type === 'reappropriated' ? 'selected' : ''}>Reappropriated</option>
                        </select>
                    </td>
                    <td>
                        <select name="items[0][purchase_mode]" class="form-control">
                            <option value="purchase" ${item.purchase_mode === 'purchase' ? 'selected' : ''}>Purchase</option>
                            <option value="rent" ${item.purchase_mode === 'rent' ? 'selected' : ''}>Rent</option>
                        </select>
                    </td>
                    <td><input type="text" name="items[0][remarks]" class="form-control form-control-sm" value="${item.remarks ?? ''}"></td>
                    <td class="text-left">
                        <button type="button" class="btn btn-danger btn-sm remove-row-btn" title="Remove Item">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>`;

            $('#itemsTable tbody').append(row);
            // ✅ initialize select2
            initItemSelect('#itemsTable tbody .item-select');

            // ✅ preselect existing item
            let option = new Option(item.item_title, item.item_id, true, true);
            $('#itemsTable tbody .item-select').append(option).trigger('change');

            $('#totalAmount').text(amount);

            // Change submit button text to indicate edit mode
            $('button[type="submit"][form="budgetForm"]')
                .html('<i class="fa fa-save"></i> Update Item')
                .removeClass('btn-primary')
                .addClass('btn-warning');

            // Switch form submission to update mode
            $('#budgetForm').off('submit').on('submit', function (e) {
                e.preventDefault();

                if (!validateForm()) {
                    return false;
                }

                // Disable submit button
                let submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');

                $.ajax({
                    url: `/edit-bh-item/${itemId}`,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (res) {
                        showToast('success', res.message || 'Item updated successfully!', 'Success');

                        // Reload budget cards
                        loadBudgetCards();
                        location.reload();

                        // Reset form and button
                        $('#budgetForm')[0].reset();
                        $('.item-select').select2('destroy');
                      initItemSelect('.item-select');

                        $('#itemsTable tbody').html(`
                            <tr>
                                <td>
                                    <input type="text" name="items[0][item_title]" class="form-control form-control-sm" placeholder="Enter item title" maxlength="1000" required>
                                </td>
                                <td>
                                    <input type="text" name="items[0][item_brand]" class="form-control form-control-sm" placeholder="Brand" maxlength="1000">
                                </td>
                                <td>
                                    <input type="text" name="items[0][item_model]" class="form-control form-control-sm" placeholder="Model" maxlength="1000">
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-control form-control-sm qty" min="1" step="1" placeholder="0" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[0][rate]" class="form-control form-control-sm rate" min="0" placeholder="0.00" required>
                                </td>
                                <td><input type="text" class="form-control form-control-sm amount bg-light" readonly value="0.00"></td>
                                <td>
                                    <select name="items[0][budget_type]" class="form-control form-control-sm" required>
                                        <option value="">Select Type</option>
                                        <option value="budgeted">Budgeted</option>
                                        <option value="unbudgeted">Unbudgeted</option>
                                        <option value="reappropriated">Reappropriated</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="items[0][purchase_mode]" class="form-control form-control-sm" required>
                                        <option value="">Select Mode</option>
                                        <option value="purchase">Purchase</option>
                                        <option value="rent">Rent</option>
                                    </select>
                                </td>
                                <td><input type="text" name="items[0][remarks]" class="form-control form-control-sm" placeholder="Item remarks"></td>
                                <td class="text-left">
                                    <button type="button" class="btn btn-danger btn-sm remove-row-btn" title="Remove Item">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                        $('#totalAmount').text('0.00');
                        $('button[type="submit"][form="budgetForm"]')
                            .html('<i class="fa fa-save"></i> Save Budget')
                            .removeClass('btn-warning')
                            .addClass('btn-primary');
                        rowIndex = 1;
                        clearFormErrors();

                        // Restore create mode handler
                        bindCreateSubmitHandler();
                    },
                    error: function (xhr) {
                        // Re-enable submit button
                        submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Budget');

                        clearFormErrors(); // Clear previous errors

                        // Handle validation errors
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            let errorCount = 0;

                            // Show inline errors for each field
                            $.each(errors, function(key, value) {
                                errorCount++;
                                let errorMsg = value[0];

                                if (key.startsWith('items.')) {
                                    // Parse items.0.item_title to get row index and field name
                                    let matches = key.match(/items\.(\d+)\.(.+)/);
                                    if (matches) {
                                        let rowIndex = matches[1];
                                        let fieldName = matches[2];

                                        // Find the specific field
                                        let field = $(`[name="items[${rowIndex}][${fieldName}]"]`);
                                        if (field.length) {
                                            showFieldError(field, errorMsg);
                                        }
                                    }
                                }
                            });

                            showToast('error', `Please fix ${errorCount} validation error(s) highlighted in the form.`, 'Validation Error');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            showToast('error', xhr.responseJSON.message, 'Error');
                        } else {
                            showToast('error', 'Error updating item', 'Error');
                        }
                    }
                });
            });
        },
        error: function () {
            showToast('error', 'Error loading item data for editing.', 'Error');
        }
    });
}



function deleteBudget(id) {
    if (!confirm('Are you sure you want to delete this budget item?')) return;

    // Show loading toast
    showToast('info', 'Deleting item, please wait...', 'Processing');

    $.ajax({
        url: `/delete-bh-item/${id}`,
        type: 'get',
        data: { _token: '{{ csrf_token() }}' },
        success: function (res) {
             showToast('success', res.message, 'Success');

            // Reload budget cards
            loadBudgetCards();
            location.reload();
        },
        error: function () {
            showToast('error', 'Error deleting budget item. Please try again.', 'Error');
        }
    });
}

</script>
@stop
