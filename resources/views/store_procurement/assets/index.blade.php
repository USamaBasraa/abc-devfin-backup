@extends('layouts.app')

@section('content')
@include('components.toast-notifications')
<link href="/css/bnu-layout.css" rel="stylesheet" />
<style>
    .assets-container {
        background-color: #f5f5f5;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .assets-table {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .assets-table thead th {
        background: #17675F !important;
        color: #fff !important;
        font-weight: 600;
        padding: 12px 15px;
        border: none;
    }
    .assets-table tbody td {
        padding: 12px 15px;
        border: none;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }
    .step-hint {
        font-size: 0.9rem;
        color: #555;
        margin-bottom: 12px;
    }
    .assets-table td.action-cell {
        min-width: 220px;
        white-space: nowrap;
    }
    .action-btns {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }
    .action-btns .btn {
        border-radius: 999px !important;
        padding: 5px 14px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        line-height: 1.3 !important;
        border-width: 1px !important;
        box-shadow: none !important;
        white-space: nowrap;
    }
    .action-btns .btn-qc {
        background: #17675F !important;
        border-color: #17675F !important;
        color: #fff !important;
    }
    .action-btns .btn-qc:hover {
        background: #0f4d47 !important;
        border-color: #0f4d47 !important;
        color: #fff !important;
    }
    .action-btns .btn-allocate {
        background: #e7f3f1 !important;
        border-color: #17675F !important;
        color: #17675F !important;
    }
    .action-btns .btn-allocate:hover {
        background: #17675F !important;
        color: #fff !important;
    }
    .action-btns .btn-reallocate {
        background: #fff8e8 !important;
        border-color: #d39e00 !important;
        color: #8a6d00 !important;
    }
    .action-btns .btn-reallocate:hover {
        background: #d39e00 !important;
        border-color: #d39e00 !important;
        color: #fff !important;
    }
    .action-btns .btn-view {
        background: #fff !important;
        border-color: #cfd8d6 !important;
        color: #4a5b58 !important;
    }
    .action-btns .btn-view:hover {
        background: #f4faf9 !important;
        border-color: #17675F !important;
        color: #17675F !important;
    }
    #allocateModal #allocLocResults {
        position: absolute;
        z-index: 40;
        width: 100%;
        max-height: 220px;
        overflow: auto;
        display: none;
        background: #fff;
        border: 1px solid #cfd8d6;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,.08);
    }
    #allocateModal #allocLocResults .list-group-item {
        border: none;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
    }
    #allocateModal #allocLocResults .list-group-item:hover {
        background: #e7f3f1;
        color: #17675F;
    }
</style>

<div class="container-fluid py-4">
    <div class="card-header" style="background:none!important;color:#252525!important;">
        <h4 class="mb-0">Assets List</h4>
        {{-- <p class="step-hint mb-0 mt-2">
            Step 1: <strong>QC Inspection</strong> &nbsp;→&nbsp; Step 2: <strong>Allocate</strong> (QC Pass ke baad)
        </p> --}}
    </div>

    <div class="card-header" style="background:none!important;color:#252525!important;">
        {{-- <h4 class="mb-0">Assets List</h4> --}}
        <p class="step-hint mb-0 mt-2">
            Step 1: <strong>QC Inspection</strong> &nbsp;→&nbsp; Step 2: <strong>Allocate</strong> (After QC Pass)
        </p>
    </div>



    <div class="assets-container">
        <div class="table-responsive">
            <table class="table assets-table" id="assetList">
                <thead>
                    <tr>
                        <th>Asset Code</th>
                        <th>Asset</th>
                        <th>Order Qty</th>
                        <th>Received Qty</th>
                        <th>QC Status</th>
                        <th>Allocation</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    @php
                        $qcStatus = strtolower((string) ($asset->qc_status ?? 'pending'));
                        $pending = (int) ($asset->pending_qc_count ?? 0);
                        $ready = (int) ($asset->ready_allocate_count ?? 0);
                    @endphp
                    <tr>
                        <td class="fw-bold">{{ $asset->asset_code }}</td>
                        <td>{{ $asset->item_title ?? $asset->name }}</td>
                        <td>{{ $asset->order_quantity }}</td>
                        <td>{{ $asset->received_quantity }}</td>
                        <td>
                            @if($qcStatus === 'passed')
                                <span class="badge" style="background:#28a745;color:#fff;border-radius:50px;padding:6px 12px;">
                                    QC Passed
                                </span>
                            @elseif($qcStatus === 'failed')
                                <span class="badge" style="background:#dc3545;color:#fff;border-radius:50px;padding:6px 12px;">
                                    QC Failed
                                </span>
                            @else
                                <span class="badge" style="background:#f0ad4e;color:#fff;border-radius:50px;padding:6px 12px;">
                                    QC Pending{{ $pending > 0 ? ' ('.$pending.')' : '' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @forelse($asset->allocation_lines ?? [] as $line)
                                <div>{{ $line }}</div>
                            @empty
                                <span class="text-muted">—</span>
                            @endforelse
                        </td>
                        <td class="action-cell">
                            <div class="action-btns">
                                @if($qcStatus !== 'passed')
                                    <button type="button"
                                        class="btn btn-sm btn-qc open-qc-btn"
                                        data-id="{{ $asset->asset_id }}"
                                        data-title="{{ $asset->item_title ?? $asset->name ?? $asset->asset_code }}">
                                        <i class="fa fa-check-circle"></i> QC
                                    </button>
                                @endif

                                @if($qcStatus === 'passed' && $ready > 0)
                                    <button type="button"
                                        class="btn btn-sm btn-allocate open-allocate-btn"
                                        data-id="{{ $asset->asset_id }}"
                                        data-title="{{ $asset->item_title ?? $asset->name ?? $asset->asset_code }}">
                                        <i class="fa fa-user-plus"></i> Allocate
                                    </button>
                                @endif

                                @if($qcStatus === 'passed' && (int) ($asset->allocated_count ?? 0) > 0)
                                    <button type="button"
                                        class="btn btn-sm btn-reallocate open-reallocate-btn"
                                        data-id="{{ $asset->asset_id }}"
                                        data-title="{{ $asset->item_title ?? $asset->name ?? $asset->asset_code }}">
                                        <i class="fa fa-exchange"></i> Reallocate
                                    </button>
                                @endif

                                <button type="button"
                                    class="btn btn-sm btn-view view-asset-details"
                                    data-id="{{ $asset->asset_id }}">
                                    <i class="fa fa-eye"></i> View
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-muted py-4">No assets found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">{{ $assets->links() }}</div>
    </div>
</div>

{{-- QC Modal --}}
<div class="modal fade" id="qcModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">QC Inspection — <span id="qcTitle"></span></h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div id="qcFormError" class="alert alert-danger" style="display:none;"></div>
        <div id="qcRfqBox" class="mb-3 p-3" style="background:#f4f6ff;border:1px solid #d6defa;border-radius:8px;">
          <strong>RFQ Details</strong>
          <div class="mt-2" id="qcRfqContent">Loading RFQ...</div>
        </div>

        <div class="form-group">
          <label><strong>Serial Numbers</strong></label>
          <input type="text" id="qcSerialSearch" class="form-control mb-2" placeholder="Search serial number">
          <div class="table-responsive" style="max-height: 260px; overflow: auto;">
            <table class="table table-bordered table-sm mb-0" id="qcSerialTable">
              <thead>
                <tr>
                  <th style="width: 70px;"><label class="mb-0"><input type="checkbox" id="qcSelectAll"> All</label></th>
                  <th>Serial Number</th>
                  <th style="width: 120px;">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr><td colspan="3" class="text-muted">Loading...</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="mb-3">
          <label><strong>Item Physical Condition</strong></label><br>
          <label class="mr-3"><input type="radio" name="condition" value="New"> New</label>
          <label><input type="radio" name="condition" value="Used"> Used</label>
        </div>
        <div class="mb-3">
          <label><strong>Item Packing</strong></label><br>
          <label class="mr-3"><input type="radio" name="packed" value="Yes"> Yes</label>
          <label><input type="radio" name="packed" value="No"> No</label>
        </div>
        <div class="mb-3">
          <label><strong>Any Damage Found</strong></label><br>
          <label class="mr-3"><input type="radio" name="damaged" value="Yes"> Yes</label>
          <label><input type="radio" name="damaged" value="No"> No</label>
        </div>
        <div class="mb-3">
          <label><strong>Received item matches RFQ (brand, model, specifications above)?</strong></label><br>
          <label class="mr-3"><input type="radio" name="specification" value="Yes"> Yes</label>
          <label><input type="radio" name="specification" value="No"> No</label>
        </div>
        <div class="mb-3">
          <label><strong>Comments</strong></label>
          <textarea id="qcRemarks" class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
          <label><strong>QC Status</strong></label><br>
          <label class="text-success mr-3"><input type="radio" name="qc_result" value="Passed"> QC Passed</label>
          <label class="text-danger"><input type="radio" name="qc_result" value="Failed"> QC Failed</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="submitQcBtn">Submit QC</button>
      </div>
    </div>
  </div>
</div>

{{-- Allocate Modal (only after QC Passed) --}}
<div class="modal fade" id="allocateModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <form id="allocateForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><span id="allocModeLabel">Allocate</span> — <span id="allocTitle"></span></h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="asset_item_ids" id="allocItemIds">
          <div class="form-group">
            <label><strong>QC Passed Serial Numbers</strong></label>
            <input type="text" id="allocSerialSearch" class="form-control mb-2" placeholder="Search serial number">
            <div class="table-responsive" style="max-height: 220px; overflow: auto;">
              <table class="table table-bordered table-sm mb-0" id="allocSerialTable">
                <thead>
                  <tr>
                    <th style="width: 70px;"><label class="mb-0"><input type="checkbox" id="allocSelectAll"> All</label></th>
                    <th>Serial Number</th>
                    <th>Current Employee</th>
                    <th style="width: 140px;">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr><td colspan="4" class="text-muted">Loading...</td></tr>
                </tbody>
              </table>
            </div>
            <small class="text-muted" id="allocSerialNote">Only QC Passed items can be allocated.</small>
          </div>
          <div class="form-group">
            <label>Department</label>
            <select name="allocation_department" id="allocDept" class="form-control" required>
              <option value="">-- Select --</option>
            </select>
          </div>
          <div class="form-group" style="position:relative;">
            <label>Location</label>
            <input type="text" id="allocLocSearch" class="form-control" placeholder="Search and select location" autocomplete="off">
            <input type="hidden" name="allocation_location" id="allocLocId">
            <div id="allocLocResults" class="list-group"></div>
            <small class="text-muted" id="allocLocPicked">Type to search, then click a location.</small>
          </div>
          <div class="form-group" style="position:relative;">
            <label>Assign To</label>
            <input type="text" id="allocUserSearch" class="form-control" placeholder="Search employee by name" autocomplete="off">
            <input type="hidden" name="assignee_user_id" id="allocUserId">
            <div id="allocUserResults" class="list-group" style="position:absolute;z-index:30;width:calc(100% - 0px);max-height:220px;overflow:auto;display:none;"></div>
            <small class="text-muted" id="allocUserPicked"></small>
          </div>
          <div class="form-group">
            <label>Remarks</label>
            <textarea name="remarks" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="allocSubmitBtn">Allocate</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="assetDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Asset Details</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body" id="assetDetailsContent"></div>
    </div>
  </div>
</div>
@endsection

@section('javascript')
<script>
$(function () {
    let itemsCache = [];
    let metaCache = { departments: [], users: [], buildings: [] };
    let locationOptions = [];

    function isPassed(item) {
        return String(item.inspection_status || '').toLowerCase() === 'passed';
    }
    function canAllocate(item) {
        const a = String(item.allocation_status || '').toLowerCase();
        return isPassed(item) && a !== 'allocated' && a !== 'request sent' && a !== 'reallocated';
    }
    function canReallocate(item) {
        const a = String(item.allocation_status || '').toLowerCase();
        return isPassed(item) && (a === 'allocated' || a === 'request sent' || a === 'reallocated');
    }
    function showBsModal(selector) {
        var $m = $(selector);
        if ($m.length && typeof $m.modal === 'function') {
            $m.modal('show');
            return;
        }
        $m.addClass('show').css({ display: 'block' });
        if (!$('.modal-backdrop').length) {
            $('body').append('<div class="modal-backdrop fade show"></div>').addClass('modal-open');
        }
    }

    function renderRfq(rfq) {
        rfq = rfq || {};
        function line(label, value) {
            var text = (value === null || value === undefined || String(value).trim() === '') ? '-' : value;
            return '<div class="mb-1"><strong>' + label + ':</strong> ' + $('<div>').text(text).html() + '</div>';
        }
        $('#qcRfqContent').html(
            line('Item', rfq.item_title) +
            line('Brand', rfq.brand) +
            line('Model', rfq.model) +
            line('RFQ Specifications', rfq.specifications) +
            line('Remarks', rfq.remarks)
        );
    }

    function showQcError(message) {
        $('#qcFormError').text(message).show();
    }

    function clearQcError() {
        $('#qcFormError').hide().text('');
    }

    function itemIdOf(item) {
        return item.asset_item_id || item.ASSET_ITEM_ID || '';
    }

    function serialOf(item) {
        return item.serial_number || item.SERIAL_NUMBER || ('Item #' + itemIdOf(item));
    }

    function resetQcForm() {
        $('#qcModal input[type=radio]').prop('checked', false);
        $('#qcRemarks').val('');
        $('#qcRfqContent').html('Loading RFQ...');
        clearQcError();
    }

    function loadAsset(assetId) {
        return $.get("{{ url('/assets') }}/" + assetId).then(function (res) {
            itemsCache = res.items || [];
            metaCache.departments = res.departments || [];
            metaCache.users = res.users || [];
            metaCache.buildings = res.resbuildingsQL || [];
            return res;
        });
    }

    // Step 1: QC Inspection
    $(document).on('click', '.open-qc-btn', function () {
        const assetId = $(this).data('id');
        const title = $(this).data('title');
        $('#qcTitle').text(title);
        resetQcForm();
        $('#qcSerialSearch').val('');
        $('#qcSelectAll').prop('checked', false);
        $('#qcSerialTable tbody').html('<tr><td colspan="3" class="text-muted">Loading...</td></tr>');
        showBsModal('#qcModal');

        loadAsset(assetId).done(function (res) {
            renderRfq(res.rfq || {});
            let rows = (res.items && res.items.length) ? res.items : [];
            if (!rows.length && res.asset && res.asset.length) {
                rows = res.asset.filter(function (row) { return itemIdOf(row); });
            }
            itemsCache = rows;

            const pending = rows.filter(function (i) { return !isPassed(i) && itemIdOf(i); });
            const source = pending.length ? pending : rows.filter(function (i) { return itemIdOf(i); });

            if (!source.length) {
                $('#qcSerialTable tbody').html('<tr><td colspan="3" class="text-muted">No serial numbers found</td></tr>');
                showQcError('No serial number was found for this asset.');
                return;
            }

            let html = '';
            source.forEach(function (i) {
                const id = itemIdOf(i);
                const status = i.inspection_status || i.INSPECTION_STATUS || 'Pending';
                html += '<tr>'
                    + '<td><input type="checkbox" class="qc-item-check" value="' + id + '"></td>'
                    + '<td>' + $('<div>').text(serialOf(i)).html() + '</td>'
                    + '<td>' + $('<div>').text(status).html() + '</td>'
                    + '</tr>';
            });
            $('#qcSerialTable tbody').html(html);
        }).fail(function (xhr) {
            $('#qcSerialTable tbody').html('<tr><td colspan="3" class="text-danger">Could not load serials</td></tr>');
            showQcError((xhr.responseJSON && xhr.responseJSON.message) || 'Could not load serial numbers.');
        });
    });

    $('#qcSelectAll').on('change', function () {
        $('#qcSerialTable tbody tr:visible .qc-item-check').prop('checked', this.checked);
    });

    $(document).on('change', '.qc-item-check', function () {
        const visible = $('#qcSerialTable tbody tr:visible .qc-item-check');
        $('#qcSelectAll').prop('checked', visible.length > 0 && visible.filter(':checked').length === visible.length);
    });

    $('#qcSerialSearch').on('input', function () {
        const query = $(this).val().toLowerCase();
        $('#qcSerialTable tbody tr').each(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(query) !== -1);
        });
        const visible = $('#qcSerialTable tbody tr:visible .qc-item-check');
        $('#qcSelectAll').prop('checked', visible.length > 0 && visible.filter(':checked').length === visible.length);
    });

    $('#submitQcBtn').on('click', function () {
        clearQcError();
        const itemIds = $('.qc-item-check:checked').map(function () { return $(this).val(); }).get();
        const status = $('input[name=qc_result]:checked').val();
        const physicalCondition = $('input[name=condition]:checked').val();
        const itemPacking = $('input[name=packed]:checked').val();
        const damageFound = $('input[name=damaged]:checked').val();
        const specificationMeets = $('input[name=specification]:checked').val();

        if (!itemIds.length) {
            showQcError('Please select at least one serial number.');
            return;
        }
        if (!physicalCondition || !itemPacking || !damageFound || !specificationMeets) {
            showQcError('Please answer all QC questions.');
            return;
        }
        if (!status) {
            showQcError('Please select QC Passed or QC Failed.');
            return;
        }

        const $btn = $(this).prop('disabled', true).text('Saving...');
        $.ajax({
            url: "{{ url('/assets/items/inspection-bulk') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                item_ids: itemIds,
                inspection_status: status,
                physical_condition: physicalCondition,
                item_packing: itemPacking,
                damage_found: damageFound,
                specification_meets: specificationMeets,
                inspection_remarks: $('#qcRemarks').val() || null
            },
            success: function (res) {
                alert(res.message || 'QC saved');
                if (typeof $('#qcModal').modal === 'function') {
                    $('#qcModal').modal('hide');
                }
                location.reload();
            },
            error: function (xhr) {
                showQcError((xhr.responseJSON && xhr.responseJSON.message) || 'QC could not be saved.');
            },
            complete: function () {
                $btn.prop('disabled', false).text('Submit QC');
            }
        });
    });

    function fillAllocationLookups(res) {
        let dept = '<option value="">-- Select --</option>';
        (metaCache.departments || []).forEach(function (d) {
            const id = d.dept_id || d.DEPT_ID || '';
            const name = d.dept_name || d.DEPT_NAME || '';
            if (!id) return;
            dept += '<option value="' + String(id) + '">' + $('<div>').text(name || id).html() + '</option>';
        });
        $('#allocDept').html(dept);
        const requestedDept = String(res.request_department_id || (res.rfq && res.rfq.request_department_id) || '');
        if (requestedDept) {
            $('#allocDept').val(requestedDept);
        }

        locationOptions = [];
        (metaCache.buildings || []).forEach(function (b) {
            if (!b || typeof b !== 'object') return;
            const id = String(b.building_id || b.BUILDING_ID || b.id || b.ID || '').trim();
            const name = String(b.building_name || b.BUILDING_NAME || b.title || b.Title || id).trim();
            if (!id || !name || name === 'undefined') return;
            locationOptions.push({ id: id, name: name });
        });
        $('#allocLocSearch').val('');
        $('#allocLocId').val('');
        $('#allocLocResults').hide().empty();
        $('#allocLocPicked').text(locationOptions.length
            ? (locationOptions.length + ' locations ready. Type to search, then click one.')
            : 'No locations loaded.');
    }

    function renderLocationResults(query) {
        const needle = String(query || '').toLowerCase().trim();
        const $list = $('#allocLocResults').empty();
        let matches = locationOptions;
        if (needle) {
            matches = locationOptions.filter(function (loc) {
                return loc.name.toLowerCase().indexOf(needle) !== -1 || loc.id.toLowerCase().indexOf(needle) !== -1;
            });
        }
        matches = matches.slice(0, 40);
        if (!matches.length) {
            $list.hide();
            $('#allocLocPicked').text('No matching location found.');
            return;
        }
        matches.forEach(function (loc) {
            $('<button type="button" class="list-group-item list-group-item-action"></button>')
                .text(loc.name)
                .attr('data-id', loc.id)
                .attr('data-name', loc.name)
                .appendTo($list);
        });
        $list.show();
    }

    function openAllocationModal(assetId, title, mode) {
        const reallocating = mode === 'reallocate';
        $('#allocateForm').data('mode', reallocating ? 'reallocate' : 'allocate');
        $('#allocModeLabel').text(reallocating ? 'Reallocate' : 'Allocate');
        $('#allocSubmitBtn').text(reallocating ? 'Reallocate' : 'Allocate');
        $('#allocSerialNote').text(reallocating
            ? 'Choose the allocated serials to move. The previous employee stays in the history.'
            : 'Only QC Passed items can be allocated.');
        $('#allocTitle').text(title);
        $('#allocSerialSearch').val('');
        $('#allocSelectAll').prop('checked', false);
        $('#allocSerialTable tbody').html('<tr><td colspan="4" class="text-muted">Loading...</td></tr>');
        $('#allocUserSearch').val('');
        $('#allocUserId').val('');
        $('#allocUserPicked').text('');
        $('#allocUserResults').hide().empty();
        $('#allocLocSearch').val('');
        $('#allocLocId').val('');
        $('#allocLocResults').hide().empty();
        $('#allocLocPicked').text('Loading locations...');
        showBsModal('#allocateModal');

        loadAsset(assetId).done(function (res) {
            fillAllocationLookups(res);

            const ready = itemsCache.filter(reallocating ? canReallocate : canAllocate);
            if (!ready.length) {
                $('#allocSerialTable tbody').html('<tr><td colspan="4" class="text-muted">'
                    + (reallocating ? 'No allocated serial is ready to move.' : 'No QC passed item is ready to allocate.')
                    + '</td></tr>');
                return;
            }
            let html = '';
            ready.forEach(function (i) {
                html += '<tr>'
                    + '<td><input type="checkbox" class="alloc-item-check" value="' + itemIdOf(i) + '"' + (reallocating ? '' : ' checked') + '></td>'
                    + '<td>' + $('<div>').text(serialOf(i)).html() + '</td>'
                    + '<td>' + $('<div>').text(i.employee_name || '-').html() + '</td>'
                    + '<td>' + $('<div>').text(i.allocation_status || 'Passed').html() + '</td>'
                    + '</tr>';
            });
            $('#allocSerialTable tbody').html(html);
            $('#allocSelectAll').prop('checked', !reallocating);
        });
    }

    $(document).on('click', '.open-allocate-btn', function () {
        openAllocationModal($(this).data('id'), $(this).data('title'), 'allocate');
    });

    $(document).on('click', '.open-reallocate-btn', function () {
        openAllocationModal($(this).data('id'), $(this).data('title'), 'reallocate');
    });

    $('#allocLocSearch').on('input', function () {
        $('#allocLocId').val('');
        $('#allocLocPicked').text('Type to search, then click a location.');
        renderLocationResults($(this).val());
    });

    $('#allocLocSearch').on('focus', function () {
        renderLocationResults($(this).val());
    });

    $(document).on('click', '#allocLocResults button', function () {
        const id = $(this).attr('data-id');
        const name = $(this).attr('data-name');
        $('#allocLocId').val(id);
        $('#allocLocSearch').val(name);
        $('#allocLocPicked').text('Selected: ' + name);
        $('#allocLocResults').hide().empty();
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#allocLocSearch, #allocLocResults').length) {
            $('#allocLocResults').hide();
        }
    });

    $('#allocDept').on('change', function () {
        $('#allocUserSearch').val('');
        $('#allocUserId').val('');
        $('#allocUserPicked').text('');
        $('#allocUserResults').hide().empty();
    });

    let employeeTimer = null;
    $('#allocUserSearch').on('input', function () {
        const q = $(this).val().trim();
        const deptId = $('#allocDept').val();
        $('#allocUserId').val('');
        $('#allocUserPicked').text('');
        clearTimeout(employeeTimer);
        if (!deptId) {
            $('#allocUserResults').hide().empty();
            if (q.length) {
                $('#allocUserPicked').text('Select a department first.');
            }
            return;
        }
        if (q.length < 2) {
            $('#allocUserResults').hide().empty();
            return;
        }
        employeeTimer = setTimeout(function () {
            $.get("{{ url('/assets/employees/search') }}", { q: q, dept_id: deptId }).done(function (rows) {
                const $list = $('#allocUserResults').empty();
                if (!rows || !rows.length) {
                    $list.hide();
                    $('#allocUserPicked').text('No employee found in this department.');
                    return;
                }
                rows.forEach(function (emp) {
                    const id = emp.id || emp.ID;
                    const name = emp.full_name || emp.FULL_NAME || id;
                    const username = emp.username || emp.USERNAME || '';
                    const dept = emp.dept_name || emp.DEPT_NAME || '';
                    const label = name + (username ? ' (' + username + ')' : '') + (dept ? ' — ' + dept : '');
                    $('<button type="button" class="list-group-item list-group-item-action"></button>')
                        .text(label)
                        .data('id', id)
                        .data('label', name)
                        .appendTo($list);
                });
                $list.show();
            });
        }, 250);
    });

    $(document).on('click', '#allocUserResults button', function () {
        $('#allocUserId').val($(this).data('id'));
        $('#allocUserSearch').val($(this).data('label'));
        $('#allocUserPicked').text('Selected employee ID: ' + $(this).data('id'));
        $('#allocUserResults').hide().empty();
    });

    $('#allocSelectAll').on('change', function () {
        $('#allocSerialTable tbody tr:visible .alloc-item-check').prop('checked', this.checked);
    });

    $(document).on('change', '.alloc-item-check', function () {
        const visible = $('#allocSerialTable tbody tr:visible .alloc-item-check');
        $('#allocSelectAll').prop('checked', visible.length > 0 && visible.filter(':checked').length === visible.length);
    });

    $('#allocSerialSearch').on('input', function () {
        const query = $(this).val().toLowerCase();
        $('#allocSerialTable tbody tr').each(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(query) !== -1);
        });
        const visible = $('#allocSerialTable tbody tr:visible .alloc-item-check');
        $('#allocSelectAll').prop('checked', visible.length > 0 && visible.filter(':checked').length === visible.length);
    });

    $('#allocateForm').on('submit', function (e) {
        e.preventDefault();
        const ids = $('.alloc-item-check:checked').map(function () { return $(this).val(); }).get().join(',');
        if (!ids) {
            alert($('#allocateForm').data('mode') === 'reallocate'
                ? 'Please select at least one allocated serial number.'
                : 'Please select at least one QC Passed serial number.');
            return;
        }
        if (!$('#allocLocId').val()) {
            alert('Please search and select a location.');
            $('#allocLocSearch').focus();
            return;
        }
        if (!$('#allocDept').val()) {
            alert('Please select a department.');
            return;
        }
        $('#allocItemIds').val(ids);

        $.ajax({
            url: $('#allocateForm').data('mode') === 'reallocate'
                ? "{{ url('/assets/reallocate') }}"
                : "{{ url('/assets/allocate') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                alert(res.message || 'Allocated');
                $('#allocateModal').modal('hide');
                location.reload();
            },
            error: function (xhr) {
                alert((xhr.responseJSON && xhr.responseJSON.message) || 'Allocation failed');
            }
        });
    });

    $(document).on('click', '.view-asset-details', function () {
        const id = $(this).data('id');
        $('#assetDetailsContent').html('<div class="text-center py-4">Loading...</div>');
        $('#assetDetailsModal').modal('show');
        loadAsset(id).done(function (res) {
            const h = (res.asset && res.asset[0]) || {};
            const cell = function (value) {
                return $('<div>').text(value || '-').html();
            };
            let rows = '';
            (res.items || []).forEach(function (i, idx) {
                rows += '<tr>'
                    + '<td>' + (idx + 1) + '</td>'
                    + '<td>' + cell(i.serial_number) + '</td>'
                    + '<td>' + cell(i.employee_name) + '</td>'
                    + '<td>' + cell(i.location_name || i.allocation_location) + '</td>'
                    + '<td>' + cell(i.inspection_status || 'Pending') + '</td>'
                    + '<td>' + cell(i.allocation_status) + '</td>'
                    + '<td>' + cell(i.unique_asset_code) + '</td>'
                    + '</tr>';
            });
            if (!rows) rows = '<tr><td colspan="7" class="text-center">No items</td></tr>';
            let historyRows = '';
            (res.allocation_history || []).forEach(function (entry) {
                historyRows += '<tr>'
                    + '<td>' + cell(entry.serial_number) + '</td>'
                    + '<td>' + cell(entry.employee_name) + '</td>'
                    + '<td>' + cell(entry.dept_name) + '</td>'
                    + '<td>' + cell(entry.location_name || entry.allocation_location) + '</td>'
                    + '<td>' + cell(entry.allocation_status) + '</td>'
                    + '<td>' + cell(entry.remarks) + '</td>'
                    + '<td>' + cell(entry.created_at) + '</td>'
                    + '</tr>';
            });
            if (!historyRows) historyRows = '<tr><td colspan="7" class="text-center">No allocation history yet.</td></tr>';
            $('#assetDetailsContent').html(
                '<h5>' + cell(h.item_title || h.name) + ' (' + cell(h.asset_code) + ')</h5>'
                + '<table class="table table-bordered mt-3">'
                + '<thead><tr><th>#</th><th>Serial</th><th>Employee</th><th>Location</th><th>QC</th><th>Allocation</th><th>Code</th></tr></thead>'
                + '<tbody>' + rows + '</tbody></table>'
                + '<h6 class="mt-4">Allocation History</h6>'
                + '<table class="table table-bordered">'
                + '<thead><tr><th>Serial</th><th>Employee</th><th>Department</th><th>Location</th><th>Action</th><th>Remarks</th><th>Date</th></tr></thead>'
                + '<tbody>' + historyRows + '</tbody></table>'
            );
        });
    });
});
</script>
@endsection
