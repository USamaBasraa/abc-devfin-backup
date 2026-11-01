@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />

<style>
    .store-inv-card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,.08);
        background: #fff;
        border: none;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .store-inv-header {
        background: #17675F !important;
        background-image: none !important;
        color: #fff;
        padding: 1rem 1.5rem;
        font-size: 18px;
        font-weight: 600;
    }
    .store-inv-table thead th {
        background: #17675F !important;
        color: #fff;
        font-size: 13px;
        white-space: nowrap;
        border: none;
    }
    .store-inv-table tbody td {
        font-size: 14px;
        vertical-align: middle;
        white-space: nowrap;
    }
    .store-inv-table tbody tr:nth-child(even) { background: #f4faf9; }
    .container-fluid h4 { color: #17675F; }
    .btn-primary { background: #17675F !important; border-color: #17675F !important; border-radius: 999px; }
    .btn-primary:hover { background: #0f4d47 !important; border-color: #0f4d47 !important; }
    .btn-outline-primary { color: #17675F !important; border-color: #17675F !important; border-radius: 999px; }
    .btn-outline-primary:hover { background: #17675F !important; color: #fff !important; }
    .store-year-filter select.form-control {
        height: 42px !important;
        min-height: 42px !important;
        line-height: 24px !important;
        padding: 8px 40px 8px 16px !important;
        font-size: 14px !important;
        border-radius: 10px !important;
        background-color: #f7fbfa !important;
        border: 1px solid #d5e6e3 !important;
        overflow: visible !important;
    }
    .stock-badge {
        font-size: 12px;
        padding: .25rem .6rem;
        border-radius: 4px;
        font-weight: 600;
    }
    .stock-available {
        background: #e8f5e9;
        color: #2e7d32;
    }
    .stock-empty {
        background: #ffebee;
        color: #c62828;
    }
    .stock-low {
        background: #fff3e0;
        color: #e65100;
    }
    .stock-order-alert {
        display: block;
        margin-top: 6px;
        padding: 4px 8px;
        border-radius: 6px;
        background: #fff8e1;
        border: 1px solid #ffcc80;
        color: #e65100;
        font-size: 11px;
        font-weight: 700;
        white-space: normal;
        max-width: 220px;
        margin-left: auto;
        margin-right: auto;
        text-align: center;
    }
    .warning-qty-input {
        width: 72px;
        display: inline-block;
        padding: 2px 6px;
        font-size: 12px;
        text-align: center;
    }
    .warning-qty-hint {
        font-size: 10px;
        color: #6c757d;
        display: block;
        margin-top: 2px;
    }
</style>

<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 font-weight-bold">Store Items Inventory</h4>
        <a href="{{ route('store-items.request.form') }}" class="btn btn-primary">
            <i class="fa fa-plus-circle mr-1"></i> New Handover Request
        </a>
    </div>

    {{-- Financial Year Filter --}}
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body py-2">
            <form method="GET" action="{{ url()->current() }}" class="form-inline store-year-filter" style="gap:10px; flex-wrap:wrap;">
                <label class="font-weight-bold mr-2 mb-0">Financial Year:</label>
                <select name="financial_year" class="form-control mr-2" style="min-width:240px;">
                    <option value="">-- All Financial Years --</option>
                    @foreach($academic_years as $ay)
                        <option value="{{ $ay->ay_id }}"
                            {{ (string)($financial_year ?? '') === (string)$ay->ay_id ? 'selected' : '' }}>
                            {{ $ay->academic_year ?? $ay->year_title ?? $ay->ay_name ?? ('Year #'.$ay->ay_id) }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-primary mr-2">
                    <i class="fa fa-search mr-1"></i> Filter
                </button>
                @if($financial_year)
                    <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-times mr-1"></i> Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="store-inv-card">
        <div class="store-inv-header d-flex justify-content-between align-items-center">
            <span>
                <i class="fa fa-cubes mr-2"></i> Available Store Items (Gate Pass Received)
                @if($financial_year)
                    @php
                        $selectedAy = $academic_years->firstWhere('ay_id', $financial_year);
                    @endphp
                    @if($selectedAy)
                        &mdash;
                        <span class="badge badge-warning text-dark ml-1">
                            {{ $selectedAy->academic_year ?? $selectedAy->year_title ?? $selectedAy->ay_name ?? ('Year #'.$selectedAy->ay_id) }}
                        </span>
                    @endif
                @endif
            </span>
            <span class="badge badge-light text-dark">{{ count($inventory) }} items</span>
        </div>
        <div class="table-responsive p-3">
            <table class="table table-hover mb-0 store-inv-table" id="storeInventoryTable" style="width:100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Title</th>
                        <th>Budget Head</th>
                        <th>Unit</th>
                        <th class="text-center">Received Qty</th>
                        <th class="text-center">Issued Qty</th>
                        <th class="text-center">Pending Requests</th>
                        <th class="text-center">Available Qty</th>
                        <th class="text-center" title="Alert when available stock falls below this quantity">Stock Alert At</th>
                        <th class="text-center no-export">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventory as $item)
                        @php
                            $storeItemId = (int) ($item->store_item_id ?? 0);
                            $avail = (float) ($item->available_qty ?? 0);
                            $warningQty = (float) ($item->warning_qty ?? 0);
                            $isLowStock = $warningQty > 0 && $avail <= $warningQty;
                            $unit = $item->unit ?? '';
                        @endphp
                        <tr class="inventory-row" data-store-item="{{ $storeItemId }}">
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $item->item_title ?? '-' }}</td>
                            <td>{{ $item->head_title ?? '-' }}</td>
                            <td>{{ $unit !== '' ? $unit : '-' }}</td>
                            <td class="text-center">{{ number_format((float) ($item->received_qty ?? 0), 2) }}</td>
                            <td class="text-center">{{ number_format((float) ($item->issued_qty ?? 0), 2) }}</td>
                            <td class="text-center">
                                @if(($item->pending_qty ?? 0) > 0)
                                    <span class="badge badge-warning">{{ number_format((float) $item->pending_qty, 2) }}</span>
                                @else
                                    0.00
                                @endif
                            </td>
                            <td class="text-center stock-cell"
                                data-available="{{ $avail }}"
                                data-warning="{{ $warningQty }}"
                                data-unit="{{ $unit }}">
                                @if($avail <= 0)
                                    <span class="stock-badge stock-empty">
                                        <i class="fa fa-times-circle mr-1"></i>0.00 {{ $unit }}
                                    </span>
                                @elseif($isLowStock)
                                    <span class="stock-badge stock-low">
                                        <i class="fa fa-exclamation-triangle mr-1"></i>{{ number_format($avail, 2) }} {{ $unit }}
                                    </span>
                                @else
                                    <span class="stock-badge stock-available">
                                        <i class="fa fa-check-circle mr-1"></i>{{ number_format($avail, 2) }} {{ $unit }}
                                    </span>
                                @endif
                                @if($isLowStock)
                                    <span class="stock-order-alert">
                                        <i class="fa fa-shopping-cart mr-1"></i>Order again — stock below alert level
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($storeItemId > 0)
                                    <div class="d-inline-flex align-items-center justify-content-center flex-wrap" style="gap:4px;">
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               class="form-control form-control-sm warning-qty-input"
                                               data-store-item-id="{{ $storeItemId }}"
                                               value="{{ $warningQty > 0 ? number_format($warningQty, 2, '.', '') : '' }}"
                                               placeholder="0"
                                               title="Alert when available stock is at or below this quantity">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary btn-save-warning-qty px-2"
                                                data-store-item-id="{{ $storeItemId }}"
                                                title="Save alert quantity">
                                            <i class="fa fa-save"></i>
                                        </button>
                                    </div>
                                    <span class="warning-qty-hint warning-qty-status" data-store-item-id="{{ $storeItemId }}">Alert below this qty</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center no-export">
                                @if($avail > 0)
                                    <a href="{{ route('store-items.request.form', ['storeItemId' => $storeItemId]) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-handshake-o mr-1"></i> Request Handover
                                    </a>
                                @else
                                    <span class="text-muted small">Out of stock</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
window.storeItemsWarningQtyUrlTemplate = @json(route('store-items.warning-qty.update', ['storeItemId' => '__STORE_ITEM_ID__']));

function refreshStockAlertCell($cell, available, warningQty, unit) {
    unit = unit || '';
    var isLow = warningQty > 0 && available <= warningQty;
    var badgeHtml;

    if (available <= 0) {
        badgeHtml = '<span class="stock-badge stock-empty"><i class="fa fa-times-circle mr-1"></i>0.00 ' + unit + '</span>';
    } else if (isLow) {
        badgeHtml = '<span class="stock-badge stock-low"><i class="fa fa-exclamation-triangle mr-1"></i>' + available.toFixed(2) + ' ' + unit + '</span>';
    } else {
        badgeHtml = '<span class="stock-badge stock-available"><i class="fa fa-check-circle mr-1"></i>' + available.toFixed(2) + ' ' + unit + '</span>';
    }

    var alertHtml = isLow
        ? '<span class="stock-order-alert"><i class="fa fa-shopping-cart mr-1"></i>Order again — stock below alert level</span>'
        : '';

    $cell.attr('data-available', available).attr('data-warning', warningQty);
    $cell.html(badgeHtml + alertHtml);
}

function getStoreItemIdFromEl($el) {
    var id = $el.attr('data-store-item-id');
    if (id === undefined || id === null || id === '') {
        id = $el.data('storeItemId');
    }
    return parseInt(id, 10) || 0;
}

function warningQtyFromInput($input) {
    var val = ($input.val() || '').trim();
    if (val === '') {
        return null;
    }
    var warningQty = parseFloat(val);
    if (isNaN(warningQty) || warningQty < 0) {
        return NaN;
    }
    return warningQty;
}

function previewStockAlertsForItem(storeItemId, warningQty) {
    $('tr.inventory-row[data-store-item="' + storeItemId + '"]').each(function () {
        var $cell = $(this).find('.stock-cell');
        var avail = parseFloat($cell.attr('data-available')) || 0;
        var unitText = $cell.attr('data-unit') || '';
        refreshStockAlertCell($cell, avail, warningQty, unitText);
    });
}

function saveWarningQtyForItem(storeItemId, warningQty, $input, $statusEl) {
    var url = (window.storeItemsWarningQtyUrlTemplate || '').replace('__STORE_ITEM_ID__', storeItemId);

    if ($statusEl && $statusEl.length) {
        $statusEl.text('Saving...').css('color', '#17675F');
    }

    $.ajax({
        url: url,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            warning_qty: warningQty === null ? '' : warningQty
        },
        success: function (res) {
            var w = parseFloat(res.warning_qty);
            if (isNaN(w)) {
                w = warningQty === null ? 0 : warningQty;
            }
            $('input.warning-qty-input[data-store-item-id="' + storeItemId + '"]').val(w > 0 ? w.toFixed(2) : '');
            previewStockAlertsForItem(storeItemId, w);
            if ($input && $input.length) {
                $input.addClass('is-valid');
                setTimeout(function () { $input.removeClass('is-valid'); }, 1500);
            }
            if ($statusEl && $statusEl.length) {
                $statusEl.text('Saved').css('color', '#2e7d32');
                setTimeout(function () {
                    $statusEl.text('Alert below this qty').css('color', '#6c757d');
                }, 2000);
            }
        },
        error: function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to save alert quantity';
            alert(msg);
            if ($statusEl && $statusEl.length) {
                $statusEl.text('Save failed').css('color', '#c62828');
            }
        }
    });
}

$(function () {
    var exportBodyFormat = {
        body: function (data, row, column, node) {
            var $node = $(node);
            var $input = $node.find('input.warning-qty-input');
            if ($input.length) {
                return $input.val() || '';
            }
            return $('<div>').html(data).text().replace(/\s+/g, ' ').trim();
        }
    };

    if ($.fn.DataTable.isDataTable('#storeInventoryTable')) {
        $('#storeInventoryTable').DataTable().destroy();
    }
    $('#storeInventoryTable').DataTable({
        pageLength: 10,
        order: [],
        autoWidth: false,
        language: {
            search: 'Search:',
            emptyTable: 'No store items with gate pass stock found.',
            zeroRecords: 'No matching items found.'
        },
        dom: "<'d-flex justify-content-between align-items-center mb-3 flex-wrap'<'d-flex align-items-center'B l>f>rt<'d-flex justify-content-between align-items-center mt-3 flex-wrap'i p>",
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Excel',
                className: 'btn btn-sm btn-success mr-2',
                title: 'Store Items Inventory',
                exportOptions: { columns: ':not(.no-export)', format: exportBodyFormat }
            },
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                className: 'btn btn-sm btn-danger',
                title: 'Store Items Inventory',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: { columns: ':not(.no-export)', format: exportBodyFormat }
            }
        ]
    });

    var warningSaveTimer = null;

    function persistWarningQty($input) {
        var storeItemId = getStoreItemIdFromEl($input);
        if (!storeItemId) {
            return;
        }
        var warningQty = warningQtyFromInput($input);
        if (warningQty !== null && isNaN(warningQty)) {
            alert('Please enter a valid alert quantity (0 or greater).');
            return;
        }
        var $statusEl = $('.warning-qty-status[data-store-item-id="' + storeItemId + '"]').first();
        saveWarningQtyForItem(storeItemId, warningQty, $input, $statusEl);
    }

    $(document).on('click', '.btn-save-warning-qty', function () {
        var storeItemId = getStoreItemIdFromEl($(this));
        if (!storeItemId) {
            return;
        }
        var $input = $('input.warning-qty-input[data-store-item-id="' + storeItemId + '"]').first();
        persistWarningQty($input);
    });

    $(document).on('keydown', '.warning-qty-input', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            persistWarningQty($(this));
        }
    });

    $(document).on('input', '.warning-qty-input', function () {
        var $input = $(this);
        var storeItemId = getStoreItemIdFromEl($input);
        if (!storeItemId) {
            return;
        }
        clearTimeout(warningSaveTimer);
        warningSaveTimer = setTimeout(function () {
            var warningQty = warningQtyFromInput($input);
            if (warningQty === null || isNaN(warningQty)) {
                return;
            }
            previewStockAlertsForItem(storeItemId, warningQty);
        }, 200);
    });
});
</script>
@endpush
