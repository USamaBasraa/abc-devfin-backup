@extends('layouts.app')

@section('content')
@include('components.toast-notifications')
<link href="/css/bnu-layout.css" rel="stylesheet" />

<style>
    .items-page .section-header {
        background: none !important;
        border: none !important;
        color: #252525 !important;
    }

    .items-page .section-title {
        color: #2d3748;
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .items-page .section-subtitle {
        color: #718096;
        font-size: 14px;
        margin: 0;
    }

    .items-page .content-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        padding: 24px;
    }

    .items-page .toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
        margin-bottom: 20px;
    }

    .items-page .toolbar-field {
        flex: 1 1 220px;
        min-width: 0;
    }

    .items-page .toolbar-field.search-field {
        flex: 2 1 320px;
    }

    .items-page .toolbar-field label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .items-page .search-wrap {
        position: relative;
    }

    .items-page .search-wrap .fa-search {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        pointer-events: none;
    }

    .items-page .search-input,
    .items-page .filter-select {
        width: 100%;
        height: 42px;
        border-radius: 10px;
        border: 1px solid #d9deea;
        box-shadow: none;
        font-size: 14px;
        color: #2d3748;
        background: #fff;
    }

    .items-page .search-input {
        padding: 0 40px 0 40px;
    }

    .items-page .search-clear {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #a0aec0;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .items-page .search-clear:hover {
        background: #edf2f7;
        color: #4a5568;
    }

    .items-page .search-clear.visible {
        display: inline-flex;
    }

    .items-page .filter-select {
        padding: 0 12px;
    }

    .items-page .toolbar-actions {
        display: flex;
        gap: 8px;
        flex: 0 0 auto;
    }

    .items-page .btn-reset {
        height: 42px;
        padding: 0 16px;
        border-radius: 10px;
        border: 1px solid #d9deea;
        background: #fff;
        color: #4a5568;
        font-size: 14px;
        font-weight: 500;
        white-space: nowrap;
    }

    .items-page .btn-reset:hover {
        background: #f7fafc;
        border-color: #cbd5e0;
    }

    .items-page .stats-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        padding: 12px 16px;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #edf2f7;
    }

    .items-page .table-summary {
        color: #4a5568;
        font-size: 14px;
        font-weight: 500;
    }

    .items-page .active-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .items-page .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(69, 98, 195, 0.1);
        color: #17675F;
        font-size: 12px;
        font-weight: 600;
    }

    .items-page .filter-chip button {
        border: none;
        background: transparent;
        color: inherit;
        padding: 0;
        line-height: 1;
        cursor: pointer;
        opacity: 0.7;
    }

    .items-page .filter-chip button:hover {
        opacity: 1;
    }

    .items-page .table-wrap {
        border: 1px solid #edf2f7;
        border-radius: 12px;
        overflow: hidden;
    }

    .items-page .table-responsive {
        max-height: calc(100vh - 340px);
        min-height: 320px;
        overflow: auto;
    }

    .items-page .table {
        margin-bottom: 0;
    }

    .items-page .table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background-color: #17675F !important;
        color: #fff !important;
        border-color: #17675F !important;
        white-space: nowrap;
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.04em;
        padding: 12px 14px;
    }

    .items-page .table tbody td {
        vertical-align: middle;
        padding: 11px 14px;
        font-size: 14px;
        color: #2d3748;
        border-top: 1px solid #edf2f7;
    }

    .items-page .table tbody tr:hover {
        background: #f8fafc;
    }

    .items-page .table tbody tr:nth-child(even) {
        background: #fcfdff;
    }

    .items-page .table tbody tr:nth-child(even):hover {
        background: #f8fafc;
    }

    .items-page .col-index {
        width: 52px;
        color: #718096;
        font-weight: 600;
    }

    .items-page .col-id {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 12px;
        color: #4a5568;
        white-space: nowrap;
    }

    .items-page .col-title {
        min-width: 220px;
        max-width: 320px;
    }

    .items-page .title-text {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-weight: 500;
    }

    .items-page .badge-soft {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .items-page .badge-soft.type {
        background: rgba(69, 98, 195, 0.12);
        color: #17675F;
    }

    .items-page .unit-pill {
        display: inline-flex;
        align-items: center;
        padding: 3px 8px;
        border-radius: 6px;
        background: #edf2f7;
        color: #4a5568;
        font-size: 12px;
        font-weight: 600;
    }

    .items-page .fin-acc-title {
        min-width: 180px;
        max-width: 260px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .items-page .table-status {
        text-align: center;
        padding: 48px 20px;
        color: #718096;
        font-weight: 500;
    }

    .items-page .table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-top: 1px solid #edf2f7;
        background: #fafbfc;
    }

    .items-page .load-more-btn {
        background: #17675F;
        border-color: #17675F;
        color: #fff;
        border-radius: 8px;
        padding: 8px 18px;
        font-size: 13px;
        font-weight: 600;
    }

    .items-page .load-more-btn:hover,
    .items-page .load-more-btn:focus {
        background: #3d56ab;
        border-color: #3d56ab;
        color: #fff;
    }

    .items-page .load-more-btn:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    .items-page .loading-hint {
        color: #718096;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .items-page .spinner-sm {
        width: 16px;
        height: 16px;
        border: 2px solid #cbd5e0;
        border-top-color: #17675F;
        border-radius: 50%;
        animation: items-spin 0.7s linear infinite;
    }

    @keyframes items-spin {
        to { transform: rotate(360deg); }
    }

    .items-page .skeleton-row td {
        padding: 14px;
    }

    .items-page .skeleton {
        height: 14px;
        border-radius: 6px;
        background: linear-gradient(90deg, #edf2f7 25%, #f7fafc 50%, #edf2f7 75%);
        background-size: 200% 100%;
        animation: items-shimmer 1.2s ease-in-out infinite;
    }

    .items-page .skeleton.w-30 { width: 30%; }
    .items-page .skeleton.w-50 { width: 50%; }
    .items-page .skeleton.w-70 { width: 70%; }
    .items-page .skeleton.w-90 { width: 90%; }

    @keyframes items-shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .items-page .empty-state i {
        font-size: 28px;
        color: #cbd5e0;
        margin-bottom: 10px;
    }

    .items-page .is-hidden-ui {
        display: none !important;
    }

    @media (max-width: 767px) {
        .items-page .table-responsive {
            max-height: none;
        }

        .items-page .table-footer {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<div class="container-fluid px-4 py-4 items-page">
    <div class="card-header section-header mb-3">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h4 class="mb-0 section-title">Items Management</h4>
                <p class="section-subtitle">Browse capital expenditure items from Pinnacle</p>
            </div>
            <button class="btn load-more-btn is-hidden-ui" type="button" id="openAddItemModal">
                Add Item
            </button>
        </div>
    </div>

    <div class="content-card">
        <div class="toolbar">
            <div class="toolbar-field search-field">
                <label for="itemsSearchInput">Search</label>
                <div class="search-wrap">
                    <i class="fa fa-search"></i>
                    <input type="text" class="form-control search-input" id="itemsSearchInput"
                        placeholder="Search by ID, title, unit, or financial account..." autocomplete="off">
                    <button type="button" class="search-clear" id="clearSearchBtn" title="Clear search">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="toolbar-field">
                <label for="itemTypeFilter">Item Type</label>
                <select class="form-control filter-select" id="itemTypeFilter">
                    <option value="">All types</option>
                </select>
            </div>
            <div class="toolbar-field">
                <label for="finAccFilter">Financial Account</label>
                <select class="form-control filter-select" id="finAccFilter">
                    <option value="">All accounts</option>
                </select>
            </div>
            <div class="toolbar-actions">
                <button type="button" class="btn-reset" id="resetFiltersBtn">
                    <i class="fa fa-undo mr-1"></i> Reset
                </button>
            </div>
        </div>

        <div class="stats-bar">
            <span class="table-summary" id="itemsSummary">Loading items...</span>
            <div class="active-filters" id="activeFilters"></div>
        </div>

        <div class="table-wrap">
            <div class="table-responsive" id="itemsTableScroll">
                <table class="table" id="itemsTable">
                    <thead>
                        <tr>
                            <th class="col-index">#</th>
                            <th>Item ID</th>
                            <th>Item Title</th>
                            <th>Item Type</th>
                            <th>Unit</th>
                            <th>App Unit</th>
                            <th>Fin Acc ID</th>
                            <th>Fin Acc Title</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody"></tbody>
                </table>
            </div>
            <div class="table-footer">
                <span class="loading-hint d-none" id="itemsLoadingMore">
                    <span class="spinner-sm"></span>
                    <span id="itemsLoadingText">Loading more items...</span>
                </span>
                <button type="button" class="btn load-more-btn ml-auto d-none" id="loadMoreBtn">
                    Load more
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form method="POST" id="itemForm">
            @csrf
            <input type="hidden" id="itemFormMethod" name="_method" value="POST">
            <input type="hidden" id="item_id" name="item_id">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #17675F;">
                    <h5 class="modal-title text-white" id="itemModalLabel">Add Item</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="item_title">Item Title</label>
                            <input type="text" name="item_title" id="item_title" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="item_type">Item Type</label>
                            <select name="item_type" id="item_type" class="form-control" required>
                                <option value="">Select Item Type</option>
                                <option value="EQUIPMENT">Equipment</option>
                                <option value="CONSUMABLE">Consumable</option>
                                <option value="SERVICE">Service</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="item_category">Category</label>
                            <input type="text" name="item_category" id="item_category" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="brand">Brand</label>
                            <input type="text" name="brand" id="brand" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="model">Model</label>
                            <input type="text" name="model" id="model" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="budget_type">Budget Type</label>
                            <select name="budget_type" id="budget_type" class="form-control">
                                <option value="">Select Budget Type</option>
                                <option value="CAPEX">CAPEX</option>
                                <option value="OPEX">OPEX</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="ACTIVE">ACTIVE</option>
                                <option value="INACTIVE">INACTIVE</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="remarks">Remarks</label>
                        <textarea name="remarks" id="remarks" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn load-more-btn" id="itemSubmitBtn" type="submit">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="deleteItemModal" tabindex="-1" role="dialog" aria-labelledby="deleteItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form method="POST" id="deleteItemForm">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header" style="background-color: #17675F;">
                    <h5 class="modal-title text-white" id="deleteItemModalLabel">Delete Item</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this item?
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        const lazyState = {
            offset: 0,
            limit: 50,
            total: 0,
            loading: false,
            hasMore: true,
            query: '',
            itemType: '',
            finAcc: ''
        };

        let activeRequest = null;
        let searchTimer = null;
        let metaLoaded = false;

        const $tableBody = $('#itemsTableBody');
        const $summary = $('#itemsSummary');
        const $loadingMore = $('#itemsLoadingMore');
        const $loadingText = $('#itemsLoadingText');
        const $loadMoreBtn = $('#loadMoreBtn');
        const $activeFilters = $('#activeFilters');
        const $tableScroll = $('#itemsTableScroll');

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function typeBadge(value) {
            return `<span class="badge-soft type">${escapeHtml(value || '-')}</span>`;
        }

        function unitPill(value) {
            return `<span class="unit-pill">${escapeHtml(value || '-')}</span>`;
        }

        function renderRow(item, index) {
            const title = item.item_title || '-';
            return `
                <tr>
                    <td class="col-index">${index}</td>
                    <td class="col-id">${escapeHtml(item.api_item_id || item.item_id || '-')}</td>
                    <td class="col-title" title="${escapeHtml(title)}">
                        <span class="title-text">${escapeHtml(title)}</span>
                    </td>
                    <td>${typeBadge(item.item_type)}</td>
                    <td>${unitPill(item.unit)}</td>
                    <td>${unitPill(item.app_unit)}</td>
                    <td class="col-id">${escapeHtml(item.fin_acc_id || '-')}</td>
                    <td class="fin-acc-title" title="${escapeHtml(item.fin_acc_title || '-')}">${escapeHtml(item.fin_acc_title || '-')}</td>
                </tr>
            `;
        }

        function renderSkeletonRows(count = 8) {
            let rows = '';
            for (let i = 0; i < count; i++) {
                rows += `
                    <tr class="skeleton-row">
                        <td><div class="skeleton w-30"></div></td>
                        <td><div class="skeleton w-70"></div></td>
                        <td><div class="skeleton w-90"></div></td>
                        <td><div class="skeleton w-50"></div></td>
                        <td><div class="skeleton w-30"></div></td>
                        <td><div class="skeleton w-30"></div></td>
                        <td><div class="skeleton w-70"></div></td>
                        <td><div class="skeleton w-90"></div></td>
                    </tr>
                `;
            }
            $tableBody.html(rows);
        }

        function renderEmpty(message, icon = 'fa-inbox') {
            $tableBody.html(`
                <tr>
                    <td colspan="8" class="table-status empty-state">
                        <div><i class="fa ${icon}"></i></div>
                        <div>${escapeHtml(message)}</div>
                    </td>
                </tr>
            `);
        }

        function updateSummary() {
            if (lazyState.loading && lazyState.offset === 0) {
                $summary.text('Loading items...');
                return;
            }

            if (lazyState.total === 0) {
                $summary.text('No items found');
                return;
            }

            const shown = Math.min(lazyState.offset, lazyState.total);
            $summary.text(`Showing ${shown} of ${lazyState.total} items`);
        }

        function updateLoadMoreUi() {
            if (lazyState.loading) {
                $loadingMore.removeClass('d-none');
                $loadMoreBtn.addClass('d-none');
                $loadingText.text(lazyState.offset === 0 ? 'Loading items...' : 'Loading more items...');
                return;
            }

            $loadingMore.addClass('d-none');

            if (lazyState.hasMore && lazyState.total > 0) {
                $loadMoreBtn.removeClass('d-none').prop('disabled', false).text('Load more');
            } else {
                $loadMoreBtn.addClass('d-none');
            }
        }

        function populateFilterOptions(meta) {
            if (!meta || metaLoaded) {
                return;
            }

            const $typeFilter = $('#itemTypeFilter');
            const $finFilter = $('#finAccFilter');

            (meta.item_types || []).forEach(function (type) {
                $typeFilter.append(`<option value="${escapeHtml(type)}">${escapeHtml(type)}</option>`);
            });

            (meta.fin_accounts || []).forEach(function (account) {
                const label = account.title || account.id;
                $finFilter.append(`<option value="${escapeHtml(account.id)}">${escapeHtml(label)}</option>`);
            });

            metaLoaded = true;
        }

        function renderActiveFilters() {
            const chips = [];

            if (lazyState.query) {
                chips.push({ key: 'query', label: `Search: "${lazyState.query}"` });
            }
            if (lazyState.itemType) {
                chips.push({ key: 'itemType', label: `Type: ${lazyState.itemType}` });
            }
            if (lazyState.finAcc) {
                const finLabel = $('#finAccFilter option:selected').text() || lazyState.finAcc;
                chips.push({ key: 'finAcc', label: `Account: ${finLabel}` });
            }

            if (!chips.length) {
                $activeFilters.empty();
                return;
            }

            let html = '';
            chips.forEach(function (chip) {
                html += `
                    <span class="filter-chip">
                        ${escapeHtml(chip.label)}
                        <button type="button" data-filter-key="${chip.key}" title="Remove filter">&times;</button>
                    </span>
                `;
            });
            $activeFilters.html(html);
        }

        function toggleSearchClear() {
            const hasValue = $('#itemsSearchInput').val().trim() !== '';
            $('#clearSearchBtn').toggleClass('visible', hasValue);
        }

        function loadItems(reset = false) {
            if (lazyState.loading && !reset) {
                return;
            }

            if (reset) {
                if (activeRequest) {
                    activeRequest.abort();
                    activeRequest = null;
                }

                lazyState.offset = 0;
                lazyState.total = 0;
                lazyState.hasMore = true;
                renderSkeletonRows();
                updateSummary();
                updateLoadMoreUi();
            }

            if (!lazyState.hasMore && !reset) {
                return;
            }

            lazyState.loading = true;
            updateSummary();
            updateLoadMoreUi();

            activeRequest = $.ajax({
                url: '{{ route("items.data") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    lazy: 1,
                    offset: lazyState.offset,
                    limit: lazyState.limit,
                    q: lazyState.query,
                    item_type: lazyState.itemType,
                    fin_acc: lazyState.finAcc
                },
                success: function (response) {
                    const items = response.data || [];

                    if (response.meta) {
                        populateFilterOptions(response.meta);
                    }

                    if (reset) {
                        $tableBody.empty();
                    }

                    if (!items.length && lazyState.offset === 0) {
                        renderEmpty('No items match your search or filters.');
                    } else {
                        let rows = '';
                        items.forEach(function (item, index) {
                            rows += renderRow(item, lazyState.offset + index + 1);
                        });
                        $tableBody.append(rows);
                    }

                    lazyState.total = parseInt(response.total || 0, 10);
                    lazyState.offset = parseInt(response.next_offset || lazyState.offset + items.length, 10);
                    lazyState.hasMore = Boolean(response.has_more);
                    updateSummary();
                    renderActiveFilters();
                },
                error: function (xhr, status) {
                    if (status === 'abort') {
                        return;
                    }

                    if (lazyState.offset === 0) {
                        renderEmpty('Unable to load items right now.', 'fa-exclamation-circle');
                    }
                    showToast('error', 'Unable to load items.');
                },
                complete: function () {
                    lazyState.loading = false;
                    activeRequest = null;
                    updateLoadMoreUi();
                }
            });
        }

        function resetFilters() {
            lazyState.query = '';
            lazyState.itemType = '';
            lazyState.finAcc = '';
            $('#itemsSearchInput').val('');
            $('#itemTypeFilter').val('');
            $('#finAccFilter').val('');
            toggleSearchClear();
            renderActiveFilters();
            loadItems(true);
        }

        $('#itemsSearchInput').on('input', function () {
            toggleSearchClear();
            const value = $(this).val().trim();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                lazyState.query = value;
                loadItems(true);
            }, 350);
        });

        $('#clearSearchBtn').on('click', function () {
            $('#itemsSearchInput').val('').trigger('focus');
            toggleSearchClear();
            lazyState.query = '';
            loadItems(true);
        });

        $('#itemTypeFilter, #finAccFilter').on('change', function () {
            lazyState.itemType = $('#itemTypeFilter').val();
            lazyState.finAcc = $('#finAccFilter').val();
            loadItems(true);
        });

        $('#resetFiltersBtn').on('click', resetFilters);

        $activeFilters.on('click', 'button[data-filter-key]', function () {
            const key = $(this).data('filter-key');
            if (key === 'query') {
                $('#itemsSearchInput').val('');
                lazyState.query = '';
                toggleSearchClear();
            } else if (key === 'itemType') {
                $('#itemTypeFilter').val('');
                lazyState.itemType = '';
            } else if (key === 'finAcc') {
                $('#finAccFilter').val('');
                lazyState.finAcc = '';
            }
            loadItems(true);
        });

        $loadMoreBtn.on('click', function () {
            loadItems(false);
        });

        $tableScroll.on('scroll', function () {
            if (!lazyState.hasMore || lazyState.loading) {
                return;
            }

            const el = this;
            const nearBottom = el.scrollTop + el.clientHeight >= el.scrollHeight - 80;
            if (nearBottom) {
                loadItems(false);
            }
        });

        $('#openAddItemModal').on('click', function () {
            resetItemForm();
            $('#itemModal').modal('show');
        });

        function resetItemForm() {
            $('#itemForm')[0].reset();
            $('#itemForm').attr('action', '{{ route("items.store") }}');
            $('#itemFormMethod').val('POST');
            $('#item_id').val('');
            $('#itemModalLabel').text('Add Item');
            $('#itemSubmitBtn').text('Save');
        }

        $('#itemModal').on('hidden.bs.modal', function () {
            resetItemForm();
        });

        renderSkeletonRows();
        loadItems(true);
    });
</script>
@endsection
