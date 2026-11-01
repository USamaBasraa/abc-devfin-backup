@extends('layouts.app')

@section('content')
<style>
    #assetCreateForm .form-control,
    #assetCreateForm select.form-control,
    #assetCreateForm textarea.form-control {
        background-color: #e9ecef !important;
        border: none !important;
        border-radius: 20px !important;
        min-height: 46px !important;
        height: 46px !important;
        padding: 0.75rem 1.25rem !important;
        line-height: 1.4 !important;
        color: #212529 !important;
    }
    #assetCreateForm textarea.form-control {
        height: auto !important;
        min-height: 100px !important;
    }
    #assetCreateForm .form-control:focus,
    #assetCreateForm select.form-control:focus,
    #assetCreateForm textarea.form-control:focus {
        background-color: #e9ecef !important;
        border: none !important;
        box-shadow: none !important;
    }
</style>
<div class="container-fluid px-3" style="margin-top: 20px;">
    <h3 class="mb-4">Asset Entry Form</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
  <div class="bg-white p-4 shadow-sm rounded">
    <div id="assetFormMessage"></div>
    <form method="POST" action="{{ route('assets.store') }}" id="assetCreateForm">
        @csrf

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Asset Code *</label>
                <input type="text" name="asset_code" class="form-control" required>
            </div>

            <div class="col-md-4 mb-3">
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
            </div>

            <div class="col-md-4 mb-3">
                <label>Item Title *</label>
                <select id="assetItemTitle" class="form-control" required>
                    <option value="">Loading items...</option>
                </select>
                <input type="hidden" name="item_title" id="assetItemTitleText">
            </div>

            <div class="col-md-4 mb-3">
                <label>Description</label>
                <input type="text" name="description" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>Brand</label>
                <input type="text" name="brand" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>Model</label>
                <input type="text" name="model" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>Serial Number</label>
                <input type="text" name="serial_number" class="form-control" placeholder="One serial, or several separated by commas">
            </div>

            <div class="col-md-3 mb-3">
                <label>Order Quantity</label>
                <input type="number" name="order_quantity" class="form-control">
            </div>

            <div class="col-md-3 mb-3">
                <label>Received Quantity</label>
                <input type="number" name="received_quantity" class="form-control">
            </div>

            <div class="col-md-3 mb-3">
                <label>Received Status</label>
                <select name="received_status" class="form-control">
                    <option value="PENDING">Pending</option>
                    <option value="RECEIVED">Received</option>
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label>Inspection Status</label>
                <select name="inspection_status" class="form-control">
                    <option value="PENDING">Pending</option>
                    <option value="PASSED">Passed</option>
                    <option value="FAILED">Failed</option>
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label>Warranty Period</label>
                <input type="text" name="warranty_period" class="form-control">
            </div>

            <div class="col-md-3 mb-3">
                <label>Warranty Start</label>
                <input type="date" name="warranty_start_date" class="form-control">
            </div>

            <div class="col-md-3 mb-3">
                <label>Warranty End</label>
                <input type="date" name="warranty_end_date" class="form-control">
            </div>

            <div class="col-md-3 mb-3">
                <label>Purchase Date</label>
                <input type="date" name="purchase_date" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>Currency</label>
                <input type="text" name="currency" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>Unit Price</label>
                <input type="number" step="0.01" min="0" name="unit_price" id="unitPrice" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>Total Price</label>
                <input type="number" step="0.01" name="total_price" id="totalPrice" class="form-control" readonly>
            </div>

            <div class="col-md-12 mb-3">
                <label>Item Remarks</label>
                <textarea name="item_remarks" class="form-control" rows="3"></textarea>
            </div>

            <div class="col-md-12 text-end">
                <button type="submit" class="btn btn-primary" id="saveAssetBtn">Save Asset</button>
            </div>
        </div>
    </form>
</div>

<div class="bg-white p-4 shadow-sm rounded mt-4">
    <h5 class="mb-3">Saved Assets</h5>
    <div class="table-responsive">
        <table class="table table-bordered" id="savedAssetsTable">
            <thead>
                <tr>
                    <th>Asset Code</th>
                    <th>Item Title</th>
                    <th>Description</th>
                    <th>Order Qty</th>
                    <th>Received Qty</th>
                    <th>Received Status</th>
                    <th>Inspection</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
</div>
@endsection

@section('javascript')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<style>
    #head_title + .select2-container .select2-selection--single,
    #assetItemTitle + .select2-container .select2-selection--single {
        background-color: #e9ecef !important;
        border: none !important;
        border-radius: 20px !important;
        min-height: 46px;
        display: flex;
        align-items: center;
    }
    #head_title + .select2-container .select2-selection--single .select2-selection__rendered,
    #assetItemTitle + .select2-container .select2-selection--single .select2-selection__rendered {
        padding-left: 1.25rem !important;
        color: #212529 !important;
        line-height: normal !important;
    }
    #head_title + .select2-container .select2-selection--single .select2-selection__arrow,
    #assetItemTitle + .select2-container .select2-selection--single .select2-selection__arrow {
        height: 44px !important;
        right: 10px !important;
    }
    #head_title + .select2-container .select2-selection--single .select2-selection__placeholder,
    #assetItemTitle + .select2-container .select2-selection--single .select2-selection__placeholder {
        color: #6c757d !important;
    }
</style>
<script>
function initSearchableSelect(selector, placeholder) {
    var $select = $(selector);
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

function selectedBudgetHeadId() {
    return $('#bh_cat_id').val() || $('#head_title option:selected').data('head-id') || '';
}

function loadBudgetItems(categoryId) {
    var $select = $('#assetItemTitle');
    if ($select.hasClass('select2-hidden-accessible')) {
        $select.select2('destroy');
    }
    $select.prop('disabled', true).html('<option value="">Loading items...</option>');
    $('#assetItemTitleText').val('');

    return $.ajax({
        url: '{{ route('sp.items.search') }}',
        type: 'GET',
        dataType: 'json',
        data: {
            q: '',
            category_id: categoryId || ''
        }
    }).done(function (data) {
        var html = '<option value="">Search item...</option>';
        (data || []).forEach(function (item) {
            if (!item.item_title) {
                return;
            }
            var option = $('<option>')
                .val(item.item_id || item.item_title)
                .text(item.item_title)
                .attr('data-title', item.item_title)
                .attr('data-brand', item.brand || '')
                .attr('data-model', item.model || '');
            html += option.prop('outerHTML');
        });
        $select.prop('disabled', false).html(html);
        initSearchableSelect('#assetItemTitle', 'Search item...');
    }).fail(function () {
        $select.prop('disabled', true).html('<option value="">Unable to load items</option>');
    });
}

$(document).ready(function () {
    initSearchableSelect('#head_title', '-- Select Budget Head Title --');
    loadBudgetItems(selectedBudgetHeadId());
});

$('#head_title').on('change select2:select select2:clear', function () {
    var headId = $(this).find(':selected').data('head-id') || '';
    $('#bh_cat_id').val(headId);
    $('[name="brand"]').val('');
    $('[name="model"]').val('');
    loadBudgetItems(headId);
});

$('#assetItemTitle').on('select2:select', function () {
    var $opt = $(this).find('option:selected');
    $('#assetItemTitleText').val($opt.data('title') || $opt.text() || '');
    $('[name="brand"]').val($opt.data('brand') || '');
    $('[name="model"]').val($opt.data('model') || '');
});

$('#assetItemTitle').on('select2:clear', function () {
    $('#assetItemTitleText').val('');
    $('[name="brand"]').val('');
    $('[name="model"]').val('');
});

function updateTotalPrice() {
    var qty = parseFloat($('[name="order_quantity"]').val());
    var unit = parseFloat($('#unitPrice').val());
    if (!isNaN(qty) && !isNaN(unit)) {
        $('#totalPrice').val((qty * unit).toFixed(2));
    } else {
        $('#totalPrice').val('');
    }
}

$(document).on('input', '[name="order_quantity"], #unitPrice', updateTotalPrice);

function initSavedAssetsTable() {
    if ($.fn.DataTable.isDataTable('#savedAssetsTable')) {
        $('#savedAssetsTable').DataTable().destroy();
    }
    $('#savedAssetsTable').DataTable({
        pageLength: 10,
        order: [],
        language: {
            search: 'Search:',
            emptyTable: 'No assets saved yet.',
            zeroRecords: 'No matching assets found.'
        },
        dom: "<'d-flex justify-content-between align-items-center mb-3'<'d-flex align-items-center'B l>f>rt<'d-flex justify-content-between align-items-center mt-3'i p>",
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Excel',
                className: 'btn btn-sm btn-success mr-2',
                title: 'Saved Assets'
            },
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                className: 'btn btn-sm btn-danger',
                title: 'Saved Assets',
                orientation: 'landscape',
                pageSize: 'A4'
            }
        ]
    });
}

function loadSavedAssets() {
    $.get('{{ route('assets.manual.list') }}', function (res) {
        var rows = (res && res.assets) ? res.assets : [];
        var html = '';
        var cell = function (value) {
            return $('<div>').text(value || '-').html();
        };
        rows.forEach(function (asset) {
            html += '<tr>'
                + '<td>' + cell(asset.asset_code) + '</td>'
                + '<td>' + cell(asset.item_title) + '</td>'
                + '<td>' + cell(asset.description) + '</td>'
                + '<td>' + cell(asset.order_quantity) + '</td>'
                + '<td>' + cell(asset.received_quantity) + '</td>'
                + '<td>' + cell(asset.received_status) + '</td>'
                + '<td>' + cell(asset.inspection_status) + '</td>'
                + '<td>' + cell((asset.currency ? asset.currency + ' ' : '') + (asset.total_price || '')) + '</td>'
                + '</tr>';
        });
        if ($.fn.DataTable.isDataTable('#savedAssetsTable')) {
            $('#savedAssetsTable').DataTable().clear().destroy();
        }
        $('#savedAssetsTable tbody').html(html);
        initSavedAssetsTable();
    }).fail(function () {
        if ($.fn.DataTable.isDataTable('#savedAssetsTable')) {
            $('#savedAssetsTable').DataTable().clear().destroy();
        }
        $('#savedAssetsTable tbody').html('<tr><td colspan="8" class="text-danger">Could not load the list.</td></tr>');
    });
}

$('#assetCreateForm').on('submit', function (e) {
    e.preventDefault();
    var $selectedItem = $('#assetItemTitle option:selected');
    if ($selectedItem.val()) {
        $('#assetItemTitleText').val($selectedItem.data('title') || $selectedItem.text());
    }
    var $btn = $('#saveAssetBtn');
    $btn.prop('disabled', true);
    $('#assetFormMessage').html('');
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (res) {
            $('#assetFormMessage').html('<div class="alert alert-success">' + (res.message || 'Asset saved successfully!') + '</div>');
            $('#assetCreateForm')[0].reset();
            $('#bh_cat_id, #assetItemTitleText').val('');
            $('#head_title').val(null).trigger('change');
            loadSavedAssets();
        },
        error: function (xhr) {
            var message = 'Asset could not be saved.';
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) message = xhr.responseJSON.message;
                if (xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors)[0][0];
                }
            }
            $('#assetFormMessage').html('<div class="alert alert-danger">' + message + '</div>');
        },
        complete: function () {
            $btn.prop('disabled', false);
        }
    });
});

loadSavedAssets();
</script>
@endsection
