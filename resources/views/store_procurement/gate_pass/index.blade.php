@extends('layouts.app')
{{-- @dd("123"); --}}
@section('content')
@include('components.toast-notifications')
<link href="/css/bnu-layout.css" rel="stylesheet" />

<style>
    /* Gate Pass Table Styling */
    .gatepass-table {
        border-collapse: separate;
        border-spacing: 0;
        border: none !important;
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }

    .gatepass-table thead {
        background: #17675F !important;
    }

    .gatepass-table thead th {
        background: #17675F !important;
        color: white !important;
        font-weight: normal;
        text-align: center;
        padding: 15px;
        border: none !important;
        font-size: 14px;
        text-transform: none;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .gatepass-table thead th:first-child {
        border-top-left-radius: 8px;
    }

    .gatepass-table thead th:last-child {
        border-top-right-radius: 8px;
    }

    .gatepass-table tbody tr {
        background: white;
        border-bottom: 1px solid #f0f0f0;
    }

    .gatepass-table tbody tr:nth-child(even) {
        background: #F8F8F8;
    }

    .gatepass-table tbody tr:hover {
        background: #f5f5f5 !important;
    }

    .gatepass-table tbody td {
        padding: 12px 15px;
        border: none !important;
        color: #333;
        font-size: 14px;
    }

    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }

    /* Remove Bootstrap striped and bordered classes effect */
    .gatepass-table.table-striped tbody tr {
        background: white;
    }

    .gatepass-table.table-striped tbody tr:nth-child(even) {
        background: #F8F8F8;
    }

    .gatepass-table.table-bordered,
    .gatepass-table.table-bordered td,
    .gatepass-table.table-bordered th {
        border: none !important;
    }
    .card-header {
        background: #f5f5f5 !important;
        color: #252525 !important;
    }
    #gatePassTabs {
        gap: 0.75rem;
    }
    #gatePassTabs .nav-link {
        border-radius: 999px;
        padding: 0.6rem 1.6rem;
        background: #f3f4f6;
        color: #6b7280;
        border: none;
        transition: all 0.2s ease-in-out;
    }
    #gatePassTabs .nav-link:not(.active):hover {
        background: #e5e7eb;
        color: #4b5563;
    }
    #gatePassTabs .nav-link.active {
        background: #17675F;
        color: #ffffff !important;
        /* box-shadow: 0 8px 16px rgba(65, 84, 241, 0.25); */
    }
</style>
<div class="container-fluid mt-4">
       <div class="card shadow-sm mb-4" style="background: none !important; box-shadow: none !important;">
            <div class="card-header" style="background: none !important; color: #252525 !important;">
                <h4 class="mb-0">
                    Purchase Order Gate Pass
                </h4>
            </div>
    <div class="card shadow-lg border-0 rounded-lg" style="background: none !important;">



        {{-- <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="background-color: #fff !important; color: #252525 !important;">
            <h4 class="mb-0"><i class="bi bi-truck"></i>Purchase Order Gate Pass</h4>
        </div> --}}
        <div class="card-body" style="background: none !important;">

            <!-- Tabs -->
            <ul class="nav nav-pills" id="gatePassTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="create-tab" data-toggle="tab" href="#create" role="tab">
                        <i class="bi bi-plus-circle"></i> Create Gate Pass
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="created-tab" data-toggle="tab" href="#created" role="tab">
                        <i class="bi bi-list-check"></i> Created Gate Passes
                    </a>
                </li>
            </ul>

            <div class="tab-content mt-4" id="gatePassTabsContent">

                <!-- ✅ Create Gate Pass Tab -->
                <div class="tab-pane fade show active" id="create" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle shadow-sm gatepass-table">
                            <thead class="text-center">
                                <tr>
                                    <th>#</th>
                                    <th>PO ID</th>
                                    <th>PO Number</th>
                                    <th>Vendor</th>
                                    <th>Date</th>
                                    <th>Delivery Status</th>
                                    <!-- <th>Status</th> -->
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrders as $po)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center"><span class="badge badge-secondary">{{ $po->po_id }}</span></td>
                                        <td class="text-center"><span class="badge badge-secondary">{{ $po->po_no }}</span></td>
                                        <td>{{ $po->vendor->vendor_name ?? '-' }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($po->po_date)->format('Y-m-d') }}</td>
                                        <td class="text-center">
                                            @if($po->delivery_status == 'Partially Delivered')
                                                <span class="badge badge-warning">Partially Delivered</span>
                                            @elseif($po->delivery_status == 'Delivered')
                                                <span class="badge badge-success">Delivered</span>
                                            @else
                                                <span class="badge badge-secondary">Pending</span>
                                            @endif
                                        </td>
                                        <!-- <td class="text-center"><span class="badge badge-success">Approved</span></td> -->
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-sm btn-primary create-gatepass-btn"
                                                data-po-id="{{ $po->po_id }}">
                                                <i class="bi bi-box-arrow-in-right"></i> Create
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-3">No approved purchase orders available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ✅ Created Gate Passes Tab -->
                <div class="tab-pane fade" id="created" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered align-middle shadow-sm gatepass-table">
                            <thead class="text-center">
                                <tr>
                                    <th>#</th>
                                    <th>PO ID</th>
                                    <th>PO Number</th>
                                    <th>Vendor</th>
                                    <th>Date</th>
                                    {{-- <th>Total Price</th> --}}
                                    <th>Gate Pass No</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($createdGatePasses as $po)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center po-link" data-po-id="{{ $po->po_id }}">
                                            <span class="badge badge-secondary" style="cursor:pointer;">
                                                {{ $po->po_id }}
                                            </span>
                                        </td>
                                        <td class="text-center"><span class="badge badge-secondary">{{ $po->po_no }}</span></td>
                                        <td>{{ $po->vendor->vendor_name ?? '-' }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($po->po_date)->format('Y-m-d') }}</td>
                                        {{-- <td class="text-center">{{ number_format($po->total_price, 2) }} {{ $po->currency }}</td> --}}
                                        <td class="text-center"><span class="badge badge-info">{{ $po->gatePass->gp_id ?? '-' }}</span></td>
                                        <!-- <td class="text-center">
                                            <a href="{{ route('gatepass.show', $po->gatePass->gp_id) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td> -->
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-info viewGatePassBtn" data-id="{{ $po->gatePass->gp_id }}">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                            <!-- <button class="btn btn-sm btn-info" onclick="updateSerial({{ $po->po_id }})">
                                                <i class="bi bi-eye"></i> EDit
                                            </button> -->
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">No created gate passes yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Gate Pass Modal -->
<div class="modal fade" id="gatePassModal" tabindex="-1" role="dialog" aria-labelledby="gatePassModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="gatePassModalLabel"><i class="bi bi-file-earmark-text"></i> Gate Pass Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="gatePassModalBody">
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="gatepassModal" tabindex="-1" role="dialog" aria-labelledby="gatepassModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" style="max-width:95% !important" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="gatepassModalLabel">🚪 Create Gate Pass</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="gatepassForm" action="{{ route('gatepass.store') }}" method="POST">
          @csrf
          <!-- Hidden fields -->
          <input type="hidden" name="po_id" id="po_id">
          <input type="hidden" name="gp_id" id="gp_id">
          <input type="hidden" name="vendor_id" id="vendor_id">
          <input type="hidden" name="budget_head_id" id="budget_head_id">
          <input type="hidden" name="budget_year_id" id="budget_year_id">
          <input type="hidden" name="budget_type" id="budget_type">
          <input type="hidden" name="is_store_items" id="is_store_items" value="0">
          <input type="hidden" name="request_type" id="request_type" value="">
          <input type="hidden" name="item_title" id="item_title">
          <input type="hidden" name="total_price" id="total_price">
          <input type="hidden" name="warranty" id="warranty">
          <input type="hidden" name="vendor_quotation_id" id="vendor_quotation_id">
          <input type="hidden" name="order_quantity" id="order_quantity">
          <input type="hidden" name="ordered_qty" id="ordered_qty">
          <input type="hidden" name="item_remarks" id="item_remarks">

          {{-- Delivery Person --}}
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">Delivery Person</label>
              <input type="text" name="delivery_person" class="form-control shadow-sm" required>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-bold">CNIC</label>
              <input type="text" name="cnic" class="form-control shadow-sm" required>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-bold">Contact No</label>
              <input type="text" name="contact_no" class="form-control shadow-sm" required>
            </div>
          </div>

          <div class="row mb-4">
            <div class="col-md-6">
              <label class="form-label fw-bold">Vehicle No</label>
              <input type="text" name="vehicle_no" class="form-control shadow-sm" placeholder="e.g. LEB-1234">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Remarks</label>
              <input type="text" name="remarks" class="form-control shadow-sm" placeholder="Remarks">
            </div>
          </div>

          {{-- Items Table --}}
          <h5 class="fw-bold mb-3">📦 Items</h5>
          <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle shadow-sm">
              <thead class="table-primary text-center">
                <tr>
                  <th>#</th>
                  <th>Item</th>
                  <th>Ordered Qty</th>
                  <th>Remaining Qty</th>
                  <th>Delivered Qty</th>
                  <th class="serials-header">Add Serial Number(s)</th>

                </tr>
              </thead>
              <tbody id="itemsTableBody">
                <!-- Filled dynamically -->
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-end mt-4">
            <button type="submit" id="saveGatepassBtn" class="btn btn-success px-4 py-2 shadow-sm">
              💾 Save Gate Pass
            </button>
          </div>
        </form>
        <div id="responseMessage" class="mt-3"></div>
      </div>
    </div>
  </div>
</div>
<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
@endsection
@section('javascript')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function updateSerial(id){
    $.ajax({
        url: "{{ url('update-serial') }}",   // Laravel route that handles update
        type: "POST",
        data: {
            id: id,

            _token: "{{ csrf_token() }}"     // include CSRF token for Laravel POST
        },
        beforeSend: function() {
            $('#serial_' + id).prop('disabled', true);
        },
        success: function(response) {
            if (response.success) {
                alert('Serial number updated successfully.');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            alert('Something went wrong while updating serial number.');
        },
        complete: function() {
            $('#serial_' + id).prop('disabled', false);
        }
    });
}
$(document).on('click', '.po-link', function() {
    let po_id = $(this).data('po-id');
    let url = 'http://devfin.bnu.edu.pk/purchase-orders/' + po_id;
    window.open(url, '_blank'); // Opens in new tab
});
$(document).ready(function() {

    $('.viewGatePassBtn').click(function () {
        let gp_id = $(this).data('id');

        $('#gatePassModal').modal('show');
        $('#gatePassModalBody').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `);

        $.ajax({
            url: '/gatepass/' + gp_id,
            type: 'GET',
            dataType: 'json',
            success: function (gatePass) {
                // Build serial items table rows
                let itemsHTML = '';
                if (gatePass.items && gatePass.items.length > 0) {
                    gatePass.items.forEach((item, index) => {
                        const serials = item.serial_number
                            ? item.serial_number.split(',').map(s => `<span class="badge badge-info mr-1">${s.trim()}</span>`).join(' ')
                            : 'N/A';

                        itemsHTML += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td class="text-center">${item.item_id ?? ''}</td>
                                <td class="text-center">${item.quantity ?? ''}</td>
                                ${gatePass.budget_type === 'CapEx' ? `<td>${serials}</td>` : ''}
                            </tr>
                        `;
                    });
                } else {
                    itemsHTML = `
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">
                                <i class="bi bi-exclamation-circle"></i> No items found for this gate pass.
                            </td>
                        </tr>
                    `;
                }

                let serialHeader = (gatePass.budget_type === 'CapEx')
    ? '<th>Serial Number</th>'
    : '';


                // Build full HTML
                let html = `
                    <div class="container mt-2">
                        <div class="card shadow-lg border-0 rounded-lg">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0">
                                    <i class="bi bi-file-earmark-text"></i> Gate Pass #${gatePass.gp_id}
                                </h4>
                            </div>

                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><strong>PO Number:</strong> <span class="badge badge-secondary">${gatePass.po_id ?? 'N/A'}</span></p>
                                        <p><strong>Delivery Person:</strong> ${gatePass.delivery_person ?? 'N/A'}</p>
                                        <p><strong>CNIC:</strong> ${gatePass.cnic ?? 'N/A'}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Contact No:</strong> ${gatePass.contact_no ?? 'N/A'}</p>
                                        <p><strong>Vehicle No:</strong> ${gatePass.vehicle_no ?? 'N/A'}</p>
                                        <p><strong>Remarks:</strong> ${gatePass.remarks ?? 'N/A'}</p>
                                    </div>
                                </div>

                                <hr>

                                <h5 class="mt-4 mb-3"><i class="bi bi-box-seam"></i> Delivered Items</h5>
                                <div class="table-responsive">
                                    <table id="itemsTable" class="table table-bordered table-hover table-striped align-middle shadow-sm">
                                        <thead class="thead-dark text-center">
                                            <tr>
                                                <th>#</th>
                                                <th>Item ID</th>
                                                <th>Quantity</th>
                                               ${serialHeader}
                                               <input type="hidden" name="budget_type" id="budget_type">
                                            </tr>
                                        </thead>
                                        <tbody>${itemsHTML}</tbody>
                                    </table>
                                </div>

                                <div class="text-right mt-3">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        <i class="bi bi-arrow-left"></i> Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // ✅ Inject built HTML into modal body
                $('#gatePassModalBody').html(html);
            },
            error: function () {
                $('#gatePassModalBody').html(`
                    <div class="alert alert-danger text-center my-3">
                        <i class="bi bi-exclamation-triangle-fill"></i> Unable to load gate pass details.
                    </div>
                `);
            }
        });
    });
    // Add CSRF token for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ✅ FORM SUBMIT


    $(document).on('click', '#saveGatepassBtn', function(e) {
        e.preventDefault();
        let $btn = $(this);
        if ($btn.data('saving')) {
            return;
        }
        $btn.data('saving', true).prop('disabled', true);

        $.ajax({
            url: "{{ route('gatepass.store') }}",
            type: "POST",
            dataType: "json",
            data: $('#gatepassForm').serialize(),
            success: function(response) {
                showToast('success', response.message || 'Gate Pass saved successfully!');
                $('#gatepassModal').modal('hide');
                $('#gatepassForm')[0].reset();
                $('#gp_id').val('');

                // Refresh both Create / Created lists after save
                setTimeout(function() {
                    window.location.reload();
                }, 800);
            },
            error: function(xhr) {
                let res = xhr.responseJSON || {};
                let errorMsg = 'Failed to save gate pass.';

                if (res.errors) {
                    errorMsg = Object.values(res.errors)[0][0];
                } else if (res.message) {
                    errorMsg = res.message;
                } else if (xhr.status === 422) {
                    errorMsg = 'Validation failed.';
                }

                showToast('error', errorMsg);
            },
            complete: function() {
                $btn.data('saving', false).prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.add-row', function() {
        let index = $(this).data('index');
        let currentRow = $(this).closest('tr');

        // find the last serial row after this item-row
        let lastSerialRow = currentRow.nextAll('.serial-row').last();

        // If no serial rows exist yet, insert after item-row
        let insertAfter = lastSerialRow.length ? lastSerialRow : currentRow;

        let newRow = `
        <tr class="serial-row">
            <td colspan="6">
            <div class="ms-5">
                <input type="text"
                    name="items[${index}][serials][]"
                    class="form-control w-50 shadow-sm d-inline-block"
                    placeholder="Enter Serial Number">
            </div>
            </td>
        </tr>`;

        insertAfter.after(newRow);
    });

    // ➖ Remove last serial input row
    $(document).on('click', '.remove-row', function() {
        let itemRow = $(this).closest('.item-row');
        let serialRows = itemRow.nextUntil('.item-row', '.serial-row');

        if (serialRows.length > 0) {
            serialRows.last().remove();
        } else {
            alert('No more serial rows to remove.');
        }
    });


     // ✅ Count updater
    function updateSerialCount(card) {
        const count = card.find('.serials-body input').length;
        card.find('.serial-count').text(count);
    }
});

$(document).on('click', '.create-gatepass-btn', function() {
    let po_id = $(this).data('po-id');

    $.ajax({
        url: "{{ url('get-po-details') }}/" + po_id,
        type: "GET",
    //    success: function(po) {
    // console.log(po);

    success: function(response) {

    let po         = response.po;
    let budgetType = response.budget_type;   // From getPODetails()
    let isStoreItems = !!(response.is_store_items);
    // Store items: never treat as CapEx for serials/assets UI
    const isCapEx  = (!isStoreItems && budgetType === 'CapEx');

    console.log("PO:", po);
    console.log("Budget Type:", budgetType);
    console.log("Store Items:", isStoreItems);

    // ✅ Fill all hidden fields (unchanged)
    $('#po_id').val(po.po_id);
    $('#vendor_id').val(po.vendor_id);
    $('#budget_head_id').val(po.items?.[0]?.bh_id ?? po.quotation_requests?.[0]?.store_bh_id ?? '');
    $('#budget_year_id').val(po.items?.[0]?.ay_id ?? '');
    $('#budget_type').val(budgetType || '');
    $('#is_store_items').val(isStoreItems ? '1' : '0');
    $('#request_type').val(isStoreItems ? 'store_items' : (po.quotation_requests?.[0]?.request_type ?? ''));
    $('#item_title').val(
        po.items?.[0]?.item_title
        ?? po.quotation_requests?.[0]?.store_budget_item?.item_title
        ?? po.quotation_requests?.[0]?.budget_item?.item_title
        ?? po.item_title
        ?? ''
    );
    $('#total_price').val(po.total_price ?? '');
    $('#warranty').val(po.warranty ?? '');
    $('#vendor_quotation_id').val(po.vendor_quotation_id ?? '');
    $('#order_quantity').val((po.quantity ?? 0) - (po.delivered_quantity ?? 0));
    $('#ordered_qty').val(po.quantity ?? 0);
    $('#item_remarks').val(
        po.items?.[0]?.remarks
        ?? po.quotation_requests?.[0]?.store_budget_item?.remarks
        ?? po.quotation_requests?.[0]?.budget_item?.remarks
        ?? ''
    );

    // ✅ Fill Delivery Person, CNIC, etc. from sp_gate_pass if exists
    if (po.latest_gatepass) {
        let gp = po.latest_gatepass;
        $('#gp_id').val(gp.gp_id);
        $('input[name="delivery_person"]').val(gp.delivery_person ?? '');
        $('input[name="cnic"]').val(gp.cnic ?? '');
        $('input[name="contact_no"]').val(gp.contact_no ?? '');
        $('input[name="vehicle_no"]').val(gp.vehicle_no ?? '');
        $('input[name="remarks"]').val(gp.remarks ?? '');
    } else {
        // Clear gp_id so store creates a NEW gate pass (not update previous PO's gp)
        $('#gp_id').val('');
        $('input[name="delivery_person"]').val('');
        $('input[name="cnic"]').val('');
        $('input[name="contact_no"]').val('');
        $('input[name="vehicle_no"]').val('');
        $('input[name="remarks"]').val('');
    }

    // ✅ Update modal title
    $('#gatepassModalLabel').text('🚪 Create Gate Pass for PO #' + po.po_no);

    // ✅ Show / hide serials header based on budget type
    if (isCapEx) {
        $('.serials-header').show();
    } else {
        $('.serials-header').hide();
    }

    // ✅ Build items table (CapEx => serials, otherwise no serials)
    let rows = '';
    if (po.quotation_requests && po.quotation_requests.length > 0) {

        po.quotation_requests.forEach((qr, index) => {
            const storeItem = qr.store_budget_item || qr.storeBudgetItem || null;
            const budgetItem = qr.budget_item || qr.budgetItem || null;
            const lineItem = storeItem || budgetItem || {};
            const itemTitle = lineItem.item_title || qr.item_name || po.item_title || '-';
            const itemId = budgetItem?.item_id ?? '';
            const storeItemId = storeItem?.sbi_id ?? storeItem?.item_id ?? qr.store_item_id ?? '';
            let quantity  = qr.requested_quantity ?? qr.quantity ?? lineItem.quantity ?? po.quantity ?? 0;
            let remaining = po.remaining_quantity != null ? po.remaining_quantity : 'N/A';
            const defaultDelivered = Math.max(
                1,
                (po.quantity ?? quantity ?? 0) - (po.delivered_quantity ?? 0)
            ) || 1;

            let serialInputs = '';
            if (isCapEx) {
                // If there is a previous gatepass with serials, pre-fill them
                if (po.latest_gatepass &&
                    po.latest_gatepass.serial_numbers &&
                    po.latest_gatepass.serial_numbers.length > 0) {

                    po.latest_gatepass.serial_numbers.forEach(serial => {
                        serialInputs += `
                            <div class="ms-5 mb-2">
                                <input type="text"
                                    name="items[${index}][serials][]"
                                    class="form-control w-50 shadow-sm d-inline-block"
                                    value="${serial}">
                            </div>`;
                    });
                } else {
                    // Default: one empty serial field
                    serialInputs = `
                        <div class="ms-5">
                            <input type="text"
                                name="items[${index}][serials][]"
                                class="form-control w-50 shadow-sm d-inline-block"
                                placeholder="Enter Serial Number">
                        </div>`;
                }
            }

            rows += `
            <tr class="item-row" data-index="${index}">
                <td class="text-center fw-bold">${index + 1}</td>
                <td>${itemTitle}</td>
                <td class="text-center total-qty">${quantity}</td>
                <td class="text-center remaining-qty">${remaining}</td>
                <td>
                    <input type="hidden" name="items[${index}][item_id]" value="${itemId}">
                    <input type="hidden" name="items[${index}][store_item_id]" value="${storeItemId}">
                    <input type="number" name="items[${index}][delivered_qty]" value="${defaultDelivered}"
                        class="form-control delivered-qty shadow-sm text-center"
                        min="1" max="${quantity || ''}"
                        placeholder="0" required>
                </td>
                ${isCapEx ? `
                <td class="text-center">
                    <div class="d-flex justify-content-center align-items-center gap-1">
                        <button type="button" class="btn btn-sm btn-success add-row" data-index="${index}">+</button>
                        <button type="button" class="btn btn-sm btn-danger remove-row">−</button>
                    </div>
                </td>
                ` : ''}
            </tr>
            ${isCapEx ? `
            <tr class="serial-row">
                <td colspan="6">
                    ${serialInputs}
                </td>
            </tr>` : ''}`;
        });

    } else {
        rows = `<tr><td colspan="${isCapEx ? 6 : 5}" class="text-center text-muted">No items found for this PO</td></tr>`;
    }

    $('#itemsTableBody').html(rows);

    // ✅ Show modal
    $('#gatepassModal').modal('show');
}

//     success: function(response) {

//     let po = response.po;
//     let budgetType = response.budget_type;
//      const isCapEx  = (budgetType === 'CapEx');
//     console.log("PO:", po);
//     console.log("Budget Type:", budgetType);

//     // ✅ Fill all hidden fields
//     $('#po_id').val(po.po_id);
//     $('#vendor_id').val(po.vendor_id);
//     $('#budget_head_id').val(po.items?.[0]?.bh_id ?? '');
//     $('#budget_year_id').val(po.items?.[0]?.ay_id ?? '');
//     $('#budget_type').val(po.items?.[0]?.budget_type ?? '');
//     $('#item_title').val(po.items?.[0]?.item_title ?? '');
//     $('#total_price').val(po.total_price ?? '');
//     $('#warranty').val(po.warranty ?? '');
//     $('#vendor_quotation_id').val(po.vendor_quotation_id ?? '');
//     $('#order_quantity').val((po.quantity ?? 0) - (po.delivered_quantity ?? 0));
//     $('#ordered_qty').val(po.quantity ?? 0);
//     $('#item_remarks').val(po.items?.[0]?.remarks ?? '');

//     // ✅ Fill Delivery Person, CNIC, etc. from sp_gate_pass if exists
//     if (po.latest_gatepass) {
//         let gp = po.latest_gatepass;
//         $('#gp_id').val(gp.gp_id);
//         $('input[name="delivery_person"]').val(gp.delivery_person ?? '');
//         $('input[name="cnic"]').val(gp.cnic ?? '');
//         $('input[name="contact_no"]').val(gp.contact_no ?? '');
//         $('input[name="vehicle_no"]').val(gp.vehicle_no ?? '');
//         $('input[name="remarks"]').val(gp.remarks ?? '');
//     } else {
//         // Clear if no previous record
//         $('input[name="delivery_person"]').val('');
//         $('input[name="cnic"]').val('');
//         $('input[name="contact_no"]').val('');
//         $('input[name="vehicle_no"]').val('');
//         $('input[name="remarks"]').val('');
//     }

//     // ✅ Update modal title
//     $('#gatepassModalLabel').text('🚪 Create Gate Pass for PO #' + po.po_no);

//     // ✅ Build items table
//     let rows = '';
//     if (po.quotation_requests && po.quotation_requests.length > 0) {
//         po.quotation_requests.forEach((qr, index) => {
//             let quantity = qr.quantity ?? po.quantity ?? 0;
//             let remaining = po.remaining_quantity ? po.remaining_quantity : 'N/A';

//             let serialInputs = '';
//             if (po.latest_gatepass && po.latest_gatepass.serial_numbers && po.latest_gatepass.serial_numbers.length > 0) {
//                 po.latest_gatepass.serial_numbers.forEach(serial => {
//                     serialInputs += `
//                         <div class="ms-5 mb-2">
//                             <input type="text" name="items[${index}][serials][]"
//                                 class="form-control w-50 shadow-sm d-inline-block"
//                                 value="${serial}">
//                         </div>`;
//                 });
//             } else {
//                 serialInputs = `
//                     <div class="ms-5">
//                         <input type="text" name="items[${index}][serials][]"
//                             class="form-control w-50 shadow-sm d-inline-block"
//                             placeholder="Enter Serial Number">
//                     </div>`;
//             }


//             rows += `
//             <tr class="item-row" data-index="${index}">
//                 <td class="text-center fw-bold">${index + 1}</td>
//                 <td>${qr.budget_item?.item_title ?? '-'}</td>
//                 <td class="text-center total-qty">${quantity}</td>
//                 <td class="text-center remaining-qty">${remaining}</td>
//                 <td>
//                     <input type="hidden" name="items[${index}][item_id]" value="${qr.budget_item?.item_id ?? ''}">
//                     <input type="number" name="items[${index}][delivered_qty]" value="${po.delivered_quantity}"
//                         class="form-control delivered-qty shadow-sm text-center"
//                         min="1" max="${quantity}"
//                         placeholder="0" required>
//                 </td>
//                 <td class="text-center">
//                     <div class="d-flex justify-content-center align-items-center gap-1">
//                         <button type="button" class="btn btn-sm btn-success add-row" data-index="${index}">+</button>
//                         <button type="button" class="btn btn-sm btn-danger remove-row">−</button>
//                     </div>
//                 </td>
//             </tr>
//             <tr class="serial-row">
//                 <td colspan="6">${serialInputs}</td>
//             </tr>`;
//         });
//     } else {
//         rows = `<tr><td colspan="6" class="text-center text-muted">No items found for this PO</td></tr>`;
//     }

//     $('#itemsTableBody').html(rows);

//     // ✅ Show modal
//     $('#gatepassModal').modal('show');
// }

,

        error: function(xhr) {
            let msg = (xhr.responseJSON && xhr.responseJSON.message)
                ? xhr.responseJSON.message
                : ('Failed to fetch Purchase Order details. (HTTP ' + xhr.status + ')');
            alert(msg);
            console.error('get-po-details error', xhr.status, xhr.responseText);
        }
    });
});

$(document).on('input', '.delivered-qty', function() {
    let $input = $(this);
    let delivered = parseFloat($input.val()) || 0;

    let $row = $input.closest('.item-row');
    let totalQty = parseFloat($row.find('.total-qty').text()) || 0;

    // Prevent delivered > total
    if (delivered > totalQty) {
        // Only show the toast once, not when equal
        if (delivered > totalQty + 0) {
            showToast('error', `Delivered quantity cannot exceed total quantity (${totalQty}).`);
        }

        $input.val(totalQty);
        delivered = totalQty;
    }

    // Update remaining quantity in real time
    let remaining = totalQty - delivered;
    $row.find('.remaining-qty').text(remaining);
});

</script>
