@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />
@include('components.toast-notifications')

<style>
    .store-budget-page {
        --sb-primary: #17675F;
        --sb-primary-dark: #0f4d47;
        --sb-soft: #e7f3f1;
        --sb-line: #d5e6e3;
        background: #f4f7f6;
        min-height: 100%;
    }
    .store-budget-page .page-title {
        color: #17675F;
        font-weight: 700;
        letter-spacing: -.2px;
    }
    .store-budget-page .dept-chip {
        display: inline-block;
        background: #e7f3f1;
        color: #17675F;
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 16px;
    }
    .store-budget-page .content-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #e4eeec;
        box-shadow: 0 8px 24px rgba(23, 103, 95, .06);
    }
    .store-budget-page .content-card h5,
    .store-budget-page .content-card h6 {
        color: #17675F;
        font-weight: 700;
    }
    .store-budget-page label { color: #1f3d3a; font-weight: 600; font-size: 13px; }
    .store-budget-page .soft-field {
        background: #f7fbfa !important;
        border: 1px solid #d5e6e3 !important;
        border-radius: 10px !important;
        padding: .7rem 1rem !important;
        color: #1f3d3a;
    }
    .store-budget-page .soft-field:focus {
        border-color: #17675F !important;
        box-shadow: 0 0 0 3px rgba(23, 103, 95, .15) !important;
    }
    .store-budget-page .alert-info {
        background: #e7f3f1 !important;
        color: #17675F !important;
        border: 1px solid #c5ddd8 !important;
    }
    .store-budget-page .table thead th {
        background: #17675F !important;
        color: #fff !important;
        border: none !important;
        white-space: nowrap;
        font-size: 12px;
        letter-spacing: .3px;
        text-transform: uppercase;
    }
    .store-budget-page .table { border-color: #e4eeec; }
    .store-budget-page .table td { vertical-align: middle; }
    .store-budget-page .btn-primary {
        background: #17675F !important;
        border-color: #17675F !important;
        border-radius: 999px;
        font-weight: 600;
        padding: .45rem 1rem;
    }
    .store-budget-page .btn-primary:hover,
    .store-budget-page .btn-primary:focus {
        background: #0f4d47 !important;
        border-color: #0f4d47 !important;
    }
    .store-budget-page .text-primary { color: #17675F !important; }
    .store-budget-page .badge-secondary {
        background: #e7f3f1;
        color: #17675F;
        border-radius: 999px;
        font-weight: 600;
        padding: 5px 10px;
    }
    .store-budget-page .table-success { background: #eef7f5 !important; }
    .store-item-search-wrap { position: relative; }
    .store-item-dropdown { position: absolute; z-index: 9999; top: 100%; left: 0; right: 0; max-height: 260px; overflow-y: auto; background: #fff; border: 1px solid #d5e6e3; border-radius: 10px; box-shadow: 0 12px 28px rgba(23,103,95,.12); display: none; }
    .store-item-option { padding: 9px 12px; cursor: pointer; border-bottom: 1px solid #f1f6f5; }
    .store-item-option:hover { background: #17675F; color: #fff; }
    .store-item-option small { display: block; opacity: .75; }
    #storeItemsTable input, #storeItemsTable select { min-width: 110px; }
    #storeItemsTable .item-title-input { min-width: 240px; }
    .store-parent-row { background: #e7f3f1; font-weight: 600; }
    .store-parent-meta { font-size: 12px; color: #4b5563; }
    .store-parent-block > .d-flex strong { color: #17675F; font-size: 15px; }
    .saved-store-table thead th { font-size: 12px; font-weight: 600; letter-spacing: .2px; }
    .saved-store-table tbody td { font-size: 13.5px; }
    .saved-store-table input[type="checkbox"] { transform: scale(1.35); accent-color: #17675F; }
    #floatingStoreActionBar .btn-primary {
        background: #17675F !important;
        border-color: #17675F !important;
        border-radius: 999px;
        font-weight: 600;
    }
    #floatingStoreActionBar .text-primary { color: #17675F !important; }
    .floating-action-bar-bottom {
        position: fixed;
        left: 50%;
        transform: translateX(-50%);
        bottom: 16px;
        z-index: 1050;
        border-radius: 14px;
        border: 1px solid #c5ddd8;
        padding: 10px 16px;
        min-width: 420px;
    }
    #storeQuotationConfirmModal .modal-content { border: none; border-radius: 12px; overflow: hidden; }
    #storeQuotationConfirmModal .modal-header { background: #17675F; color: #fff; }
    #storeQuotationConfirmModal .modal-title { font-size: 18px; font-weight: 600; }
    #storeQuotationConfirmModal .btn-primary {
        background: #17675F;
        border-color: #17675F;
        border-radius: 999px;
    }
    #storeQuotationConfirmModal .modal-body .note-box {
        background: #e7f3f1;
        border: 1px solid #c5ddd8;
        color: #0f4d47;
        padding: 10px 12px;
        border-radius: 8px;
        margin-bottom: 12px;
    }
    #storeQuotationConfirmModal table th,
    #storeQuotationConfirmModal table td { font-size: 14px; vertical-align: middle; }
    #storeQuotationConfirmModal thead th { background: #17675F; color: #fff; }
    #storeQuotationConfirmModal .expand-item-details {
        color: #17675F;
        text-decoration: underline;
        cursor: pointer;
        font-weight: 500;
    }
    #storeQuotationConfirmModal .item-detail-row { background: #f4faf9; }
    #storeQuotationConfirmModal .item-detail-box { font-size: 13px; color: #374151; }
    #storeQuotationConfirmModal .item-detail-table td {
        font-size: 13px;
        padding: 6px 10px;
        border: 1px solid #d5e6e3;
        background: #f4faf9;
    }
    #storeQuotationConfirmModal .item-detail-table .label-cell { width: 18%; font-weight: 600; color: #4b5563; }
    #storeQuotationConfirmModal .item-detail-table .value-cell { width: 32%; color: #111827; }
    #storeQuotationConfirmModal .request-qty-input { max-width: 120px; }
</style>

<div class="container-fluid px-4 py-4 store-budget-page">
    <div class="mb-1">
        <h4 class="page-title mb-1">Create Store Items Budget</h4>
        @if(auth()->user()->department)
            <span class="dept-chip">{{ auth()->user()->department }}</span>
        @endif
    </div>

    <div class="content-card mb-4">
        <form id="storeBudgetForm">
            @csrf
            <div class="form-group mb-3">
                <label>Department <span class="text-danger">*</span></label>
                <div class="alert alert-info soft-field mb-0"><strong>{{ auth()->user()->department }}</strong></div>
                <input type="hidden" name="dept_id" value="{{ auth()->user()->dept_id }}">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Financial Year <span class="text-danger">*</span></label>
                        <select name="year_id" class="form-control soft-field" required>
                            <option value="">-- Select Financial Year --</option>
                            @foreach($academic_years as $year)
                                <option value="{{ $year->ay_id }}">{{ $year->academic_year ?? $year->year_title ?? $year->ay_name ?? $year->ay_id }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>General Remarks</label>
                <textarea name="remarks" class="form-control soft-field" rows="3" maxlength="2000" placeholder="Enter remarks for store items budget..."></textarea>
            </div>

            <hr>
            <h6 class="mb-3">Add Store Budget Items</h6>
            <div class="table-responsive">
                <table class="table table-bordered" id="storeItemsTable">
                    <thead>
                        <tr>
                            <th>Item Title <span class="text-danger">*</span></th>
                            <th>Unit</th>
                            <th>App Unit</th>
                            <th>Quantity <span class="text-danger">*</span></th>
                            <th>Rate <span class="text-danger">*</span></th>
                            <th>Amount</th>
                            <th>Budget Type <span class="text-danger">*</span></th>
                            <th>Purchase Mode <span class="text-danger">*</span></th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <button type="button" class="btn btn-sm btn-primary" id="addStoreItemRow"><i class="fa fa-plus"></i> Add Item</button>
            <button type="submit" class="btn btn-primary ml-2"><i class="fa fa-save"></i> Save Store Budget</button>
        </form>
    </div>

    <div class="content-card">
        <h5 class="mb-3">Saved Store Items Budgets</h5>
        @php
            $allStoreItems = collect($heads)->flatMap(function ($head) {
                return $head->items->map(function ($item) use ($head) {
                    $item->store_head_id = $head->sbh_id;
                    return $item;
                });
            });

            $groupedItems = $allStoreItems->groupBy(function ($item) {
                return trim((string) ($item->fin_acc01_title ?? 'Unmapped Store Head')) ?: 'Unmapped Store Head';
            });
        @endphp

        @forelse($groupedItems as $parentTitle => $items)
            <div class="mb-4 store-parent-block">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>{{ $parentTitle }}</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered saved-store-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;" class="text-center">
                                    <input type="checkbox" class="select-parent-store-items"
                                           data-parent-key="{{ md5($parentTitle) }}">
                                </th>
                                <th>Item Title</th>
                                <th>Unit</th>
                                <th>App Unit</th>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Budget Type</th>
                                <th>Purchase Mode</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                @php $alreadySent = $item->quotationRequests->count() > 0; @endphp
                                <tr class="{{ $alreadySent ? 'table-success' : '' }}">
                                    <td class="text-center">
                                        <input type="checkbox"
                                               class="store-item-checkbox parent-{{ md5($parentTitle) }}"
                                               value="{{ $item->sbi_id }}"
                                               data-head-id="{{ $item->store_head_id }}"
                                               data-item-title="{{ $item->item_title }}"
                                               data-unit="{{ $item->unit ?? '-' }}"
                                               data-app-unit="{{ $item->app_unit ?? '-' }}"
                                               data-rate="{{ number_format((float) $item->rate, 2, '.', '') }}"
                                               data-budget-type="{{ $item->budget_type ?? '-' }}"
                                               data-purchase-mode="{{ $item->purchase_mode ?? 'purchase' }}"
                                               data-fin-acc-title="{{ $item->fin_acc01_title ?? '-' }}"
                                               data-quantity="{{ $item->quantity }}"
                                               {{ $alreadySent ? 'checked disabled' : '' }}>
                                    </td>
                                    <td>{{ $item->item_title }}</td>
                                    <td>{{ $item->unit ?? '-' }}</td>
                                    <td>{{ $item->app_unit ?? '-' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format((float) $item->rate, 2) }}</td>
                                    <td>{{ number_format((float) $item->quantity * (float) $item->rate, 2) }}</td>
                                    <td><span class="badge badge-secondary">{{ ucfirst($item->budget_type) }}</span></td>
                                    <td>{{ ucfirst($item->purchase_mode ?? 'purchase') }}</td>
                                    <td>{{ $alreadySent ? 'Sent' : 'Draft' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <p class="text-muted mb-0">No store items budget saved yet.</p>
        @endforelse
    </div>
</div>
<div id="floatingStoreActionBar" class="floating-action-bar-bottom bg-white shadow-lg d-none">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <strong class="text-dark"><span id="selectedStoreItemsCountDisplay" class="text-primary">0</span> Selected</strong>
            <div class="small text-muted">Submit quotation request for selected store items</div>
        </div>
        <button type="button" class="btn btn-primary" id="submitStoreQuotationBtn">
            <i class="fa fa-paper-plane"></i> Submit Quotation Request
        </button>
    </div>
</div>

<div class="modal fade" id="storeQuotationConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-check-circle"></i> Confirm Quotation Request</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="note-box">
                    <i class="fa fa-info-circle"></i>
                    <strong>Note:</strong> Enter quantity you want to request quotation for. This can be different from budget quantity.
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Item Title</th>
                                <th style="width:140px;">Budget Qty</th>
                                <th style="width:170px;">Request Qty <span class="text-danger">*</span></th>
                            </tr>
                        </thead>
                        <tbody id="storeRequestItemsBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStoreRequestBtn"><i class="fa fa-check"></i> Send Request</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
$(function() {
    let rowIndex = 0;
    window.storeItemOptions = [];
    let selectedStoreItemsForRequest = [];

    function money(value) {
        return (parseFloat(value || 0) || 0).toFixed(2);
    }

    function rowHtml() {
        const i = rowIndex++;
        return `<tr>
            <td>
                <select name="items[${i}][item_selector]" class="form-control item-select" data-loading-text="Loading store items..." disabled required>
                    <option value="">Loading store items...</option>
                </select>
                <input type="hidden" name="items[${i}][item_title]" class="item-title-hidden">
                <input type="hidden" name="items[${i}][pinnacle_item_id]" class="pinnacle-item-id">
                <input type="hidden" name="items[${i}][item_type]" class="item-type">
                <input type="hidden" name="items[${i}][fin_acc01_id]" class="fin-acc-id">
                <input type="hidden" name="items[${i}][fin_acc01_title]" class="fin-acc-title">
            </td>
            <td><input type="text" class="form-control unit-field" name="items[${i}][unit]" readonly></td>
            <td><input type="text" class="form-control app-unit-field" name="items[${i}][app_unit]" readonly></td>
            <td><input type="number" class="form-control qty-field" name="items[${i}][quantity]" min="1" value="1" required></td>
            <td><input type="number" class="form-control rate-field" name="items[${i}][rate]" min="0" step="0.01" value="0" required></td>
            <td><input type="text" class="form-control amount-field" value="0.00" readonly></td>
            <td><select class="form-control" name="items[${i}][budget_type]" required><option value="budgeted">Budgeted</option><option value="unbudgeted">Unbudgeted</option><option value="reappropriated">Reappropriated</option></select></td>
            <td><select class="form-control" name="items[${i}][purchase_mode]" required><option value="purchase" selected>Purchase</option><option value="rent">Rent</option></select></td>
            <td><input type="text" class="form-control" name="items[${i}][remarks]" placeholder="Remarks"></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-store-row"><i class="fa fa-trash"></i></button></td>
        </tr>`;
    }

    function recalc(row) {
        const qty = parseFloat(row.find('.qty-field').val()) || 0;
        const rate = parseFloat(row.find('.rate-field').val()) || 0;
        row.find('.amount-field').val(money(qty * rate));
    }

    function initItemSelect(selector) {
        $(selector).each(function () {
            const $select = $(this);

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.select2({
                placeholder: $select.prop('disabled') ? 'Loading store items...' : 'Search store item...',
                allowClear: true,
                minimumInputLength: 0,
                width: '100%',
                dropdownParent: $('body'),
                data: window.storeItemOptions || []
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
                $select.html('<option value="">Loading store items...</option>');
            } else {
                $select.prop('disabled', false);
                initItemSelect($select);

                if (currentValue) {
                    $select.val(currentValue).trigger('change');
                }
            }
        });
    }

    function preloadStoreItems() {
        setItemSelectLoadingState('.item-select', true);
        return $.ajax({
            url: '{{ route("store-items-budget.items.search") }}',
            type: 'GET',
            dataType: 'json',
            data: { q: '' }
        }).done(function (data) {
            window.storeItemOptions = (data || []).map(function (item) {
                return {
                    id: String(item.item_id || ''),
                    text: item.item_title || '',
                    item_id: item.item_id || '',
                    item_title: item.item_title || '',
                    item_type: item.item_type || '',
                    unit: item.unit || '',
                    app_unit: item.app_unit || '',
                    fin_acc01_id: item.fin_acc01_id || '',
                    fin_acc01_title: item.fin_acc01_title || ''
                };
            });

            setItemSelectLoadingState('.item-select', false);
        }).fail(function () {
            window.storeItemOptions = [];
            $('.item-select').each(function () {
                const $select = $(this);
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                $select.prop('disabled', true);
                $select.html('<option value="">Unable to load store items</option>');
            });
        });
    }

    $('#addStoreItemRow').on('click', function() {
        $('#storeItemsTable tbody').append(rowHtml());
        if (Array.isArray(window.storeItemOptions) && window.storeItemOptions.length) {
            setItemSelectLoadingState('#storeItemsTable tbody tr:last .item-select', false);
        } else {
            setItemSelectLoadingState('#storeItemsTable tbody tr:last .item-select', true);
        }
    }).trigger('click');

    $(document).on('input', '.qty-field,.rate-field', function() {
        recalc($(this).closest('tr'));
    });

    $(document).on('click', '.remove-store-row', function() {
        if ($('#storeItemsTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
        }
    });

    $(document).on('select2:select', '.item-select', function (e) {
        const item = e.params.data || {};
        const row = $(this).closest('tr');
        row.find('.item-title-hidden').val(item.item_title || item.text || '');
        row.find('.pinnacle-item-id').val(item.item_id || item.id || '');
        row.find('.item-type').val(item.item_type || '');
        row.find('.unit-field').val(item.unit || '');
        row.find('.app-unit-field').val(item.app_unit || '');
        row.find('.fin-acc-id').val(item.fin_acc01_id || '');
        row.find('.fin-acc-title').val(item.fin_acc01_title || '');
    });

    $(document).on('select2:clear', '.item-select', function () {
        const row = $(this).closest('tr');
        row.find('.item-title-hidden').val('');
        row.find('.pinnacle-item-id').val('');
        row.find('.item-type').val('');
        row.find('.unit-field').val('');
        row.find('.app-unit-field').val('');
        row.find('.fin-acc-id').val('');
        row.find('.fin-acc-title').val('');
    });

    $('#storeBudgetForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        const original = btn.html();
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        $.post('{{ route("store-items-budget.store") }}', $(this).serialize())
            .done(function(res) {
                showToast('success', res.message || 'Saved.', 'Success');
                setTimeout(function(){ location.reload(); }, 1000);
            })
            .fail(function(xhr) {
                const msg = xhr.responseJSON?.message || 'Unable to save store items budget.';
                showToast('error', msg, 'Error');
                btn.prop('disabled', false).html(original);
            });
    });

    function refreshStoreSelectionUI() {
        const selectedCount = $('.store-item-checkbox:checked:not(:disabled)').length;
        $('#selectedStoreItemsCountDisplay').text(selectedCount);
        if (selectedCount > 0) {
            $('#floatingStoreActionBar').removeClass('d-none');
        } else {
            $('#floatingStoreActionBar').addClass('d-none');
        }
    }

    $(document).on('change', '.select-parent-store-items', function() {
        const parentKey = $(this).data('parent-key');
        $('.store-item-checkbox.parent-' + parentKey + ':not(:disabled)').prop('checked', $(this).prop('checked'));
        refreshStoreSelectionUI();
    });

    $(document).on('change', '.store-item-checkbox', function() {
        const classList = ($(this).attr('class') || '').split(/\s+/);
        const parentClass = classList.find(cls => cls.indexOf('parent-') === 0);
        if (parentClass) {
            const parentKey = parentClass.replace('parent-', '');
            const parentSelector = '.store-item-checkbox.parent-' + parentKey + ':not(:disabled)';
            const allParentChecked = $(parentSelector).length && $(parentSelector + ':checked').length === $(parentSelector).length;
            $('.select-parent-store-items[data-parent-key="' + parentKey + '"]').prop('checked', allParentChecked);
        }
        refreshStoreSelectionUI();
    });

    $('#submitStoreQuotationBtn').on('click', function() {
        const selected = $('.store-item-checkbox:checked:not(:disabled)');
        if (!selected.length) {
            showToast('warning', 'Please select at least one store item.', 'No Items Selected');
            return;
        }

        selectedStoreItemsForRequest = [];
        let modalRows = '';
        selected.each(function() {
            const itemId = String($(this).val());
            const headId = String($(this).data('head-id'));
            const budgetQty = parseFloat($(this).data('quantity')) || 0;
            const itemTitle = $(this).data('item-title') || ('Item #' + itemId);

            selectedStoreItemsForRequest.push({
                item_id: itemId,
                head_id: headId,
                item_title: itemTitle,
                budget_qty: budgetQty,
                unit: $(this).data('unit') || '-',
                app_unit: $(this).data('app-unit') || '-',
                rate: $(this).data('rate') || '0.00',
                budget_type: $(this).data('budget-type') || '-',
                purchase_mode: $(this).data('purchase-mode') || '-',
                fin_acc_title: $(this).data('fin-acc-title') || '-'
            });

            modalRows += `
                <tr>
                    <td>
                        <span class="expand-item-details" data-item-id="${itemId}">
                            <i class="fa fa-angle-down mr-1"></i>${itemTitle}
                        </span>
                    </td>
                    <td class="text-center">${budgetQty}</td>
                    <td class="text-center">
                        <input type="number" min="1" step="1" class="form-control request-qty-input mx-auto"
                               data-item-id="${itemId}" value="${budgetQty}">
                    </td>
                </tr>
                <tr class="item-detail-row d-none" id="item-detail-${itemId}">
                    <td colspan="3">
                        <div class="item-detail-box">
                            <table class="table table-sm item-detail-table mb-0">
                                <tbody>
                                    <tr>
                                        <td class="label-cell">Item ID:</td>
                                        <td class="value-cell">${itemId}</td>
                                        <td class="label-cell">Budget ID:</td>
                                        <td class="value-cell">${headId}</td>
                                    </tr>
                                    <tr>
                                        <td class="label-cell">Budget Qty:</td>
                                        <td class="value-cell">${budgetQty}</td>
                                        <td class="label-cell">Rate:</td>
                                        <td class="value-cell">${$(this).data('rate') || '0.00'}</td>
                                    </tr>
                                    <tr>
                                        <td class="label-cell">Amount:</td>
                                        <td class="value-cell"><strong>${(budgetQty * (parseFloat($(this).data('rate')) || 0)).toFixed(2)}</strong></td>
                                        <td class="label-cell">Budget Type:</td>
                                        <td class="value-cell">${$(this).data('budget-type') || '-'}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            `;
        });
        $('#storeRequestItemsBody').html(modalRows);
        $('#storeQuotationConfirmModal').modal('show');
    });

    $(document).on('click', '.expand-item-details', function() {
        const itemId = $(this).data('item-id');
        const detailRow = $('#item-detail-' + itemId);
        const icon = $(this).find('i');
        detailRow.toggleClass('d-none');
        if (detailRow.hasClass('d-none')) {
            icon.removeClass('fa-angle-up').addClass('fa-angle-down');
        } else {
            icon.removeClass('fa-angle-down').addClass('fa-angle-up');
        }
    });

    $('#confirmStoreRequestBtn').on('click', function() {
        if (!selectedStoreItemsForRequest.length) {
            $('#storeQuotationConfirmModal').modal('hide');
            return;
        }

        const requestQtyMap = {};
        let invalid = false;
        $('#storeRequestItemsBody .request-qty-input').each(function() {
            const itemId = String($(this).data('item-id'));
            const qty = parseFloat($(this).val());
            if (!qty || qty <= 0) {
                invalid = true;
                $(this).addClass('is-invalid');
                return;
            }
            $(this).removeClass('is-invalid');
            requestQtyMap[itemId] = qty;
        });

        if (invalid) {
            showToast('warning', 'Please enter valid request quantity for all items.', 'Validation');
            return;
        }

        const groupedByHead = {};
        selectedStoreItemsForRequest.forEach(function(item) {
            if (!groupedByHead[item.head_id]) {
                groupedByHead[item.head_id] = { items: [], quantities: {} };
            }
            groupedByHead[item.head_id].items.push(item.item_id);
            groupedByHead[item.head_id].quantities[item.item_id] = requestQtyMap[item.item_id] ?? item.budget_qty;
        });

        const headIds = Object.keys(groupedByHead);
        const confirmBtn = $(this);
        const originalText = confirmBtn.html();
        confirmBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');

        let completed = 0;
        let failed = false;
        headIds.forEach(function(headId) {
            const payload = Object.assign({ _token: '{{ csrf_token() }}' }, groupedByHead[headId]);
            $.post('{{ url('/store-items-budget') }}/' + headId + '/request-quotation', payload)
                .done(function() {
                    completed++;
                    if (completed === headIds.length && !failed) {
                        showToast('success', 'Quotation request submitted successfully.', 'Success');
                        setTimeout(function(){ location.reload(); }, 900);
                    }
                })
                .fail(function(xhr) {
                    if (!failed) {
                        failed = true;
                        confirmBtn.prop('disabled', false).html(originalText);
                        showToast('error', xhr.responseJSON?.message || 'Unable to send request.', 'Error');
                    }
                });
        });
    });

    preloadStoreItems();
    refreshStoreSelectionUI();
});
</script>
@endsection
