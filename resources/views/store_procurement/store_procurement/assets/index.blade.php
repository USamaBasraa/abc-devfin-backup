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
    .assets-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    .assets-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #333;
    }
    .assets-table {
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
    }
    .assets-table thead {
        background-color: #4562C3 ;
    }
    .assets-table thead th {
        color: white;
        font-weight: 600;
        padding: 12px 15px;
        border: none;
    }
    .assets-table tbody tr {
        background-color: white;
        border-bottom: 1px solid #f0f0f0;
    }
    .assets-table tbody tr:last-child {
        border-bottom: none;
    }
    .assets-table tbody td {
        padding: 12px 15px;
        color: #333;
        border: none;
    }
    .assets-table {
        border-collapse: separate;
        border-spacing: 0;
    }

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
    
</style>
<div class="container-fluid py-4">

    <div class="card-header" style="background: none !important; color: #252525 !important;">
        <h4 class="mb-0">
            Assets List
        </h4>
    </div>


    <div class="assets-container">


        {{-- <div class="assets-header">
            <h4 class="assets-title">Assets List</h4>
        </div> --}}
        <div class="table-responsive">
            <table class="table assets-table align-middle" id="assetList">
                <thead class="text-start">
                    <tr>
                        <th class="text-start" style="background: #4562C3 !important; color: #fff !important;">Asset Code</th>
                        <th class="text-start" style="background: #4562C3 !important; color: #fff !important;">Asset</th>
                        <!-- <th>Allocated Resource</th> -->
                        <th class="text-start" style="background: #4562C3 !important; color: #fff !important;">Order Qty</th>
                        <th class="text-start" style="background: #4562C3 !important; color: #fff !important;">Received Qty</th>
                        <th class="text-start" style="background: #4562C3 !important; color: #fff !important;">Status</th>
                        <th class="text-start" style="background: #4562C3 !important; color: #fff !important;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr>
                        <td class="fw-bold text-start">{{ $asset->asset_code }}</td>
                        <td class="text-start">{{ $asset->item_title }}</td>
                        <!-- <td>{{ $asset->category }}</td> -->
                        <td class="text-start">{{ $asset->order_quantity }}</td>
                        <td class="text-start">{{ $asset->received_quantity }}</td>
                        <td class="text-start">
                            @if($asset->status === 'Available')
                                <span class="badge px-3 py-2 shadow-sm" style="background-color: #4562C3 ; color: white; border-radius: 50px;">Available</span>
                            @elseif($asset->status === 'Pending')
                                <span class="badge px-3 py-2 shadow-sm" style="background-color: #4562C3 ; color: white; border-radius: 50px;">Pending</span>
                            @elseif($asset->status === 'In Use')
                                <span class="badge px-3 py-2 shadow-sm" style="background-color: #4562C3 ; color: white; border-radius: 50px;">In Use</span>
                            @else
                                <span class="badge px-3 py-2 shadow-sm" style="background-color: #4562C3 ; color: white; border-radius: 50px;">{{ $asset->status }}</span>
                            @endif
                        </td>
                        <td class="text-start">
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary rounded-pill shadow-sm view-asset-details"
                                    data-id="{{ $asset->asset_id }}">
                                View Details
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-start text-muted py-4">
                            <i class="bi bi-box-seam"></i> No assets found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $assets->links() }}
        </div> -->
    </div>
</div>

<div class="modal fade" id="assetDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 1400px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Asset Details</h5>
       
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
        
      </div>
      <div class="modal-body" id="assetDetailsContent">
        <!-- AJAX content will load here -->
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="inspectionModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">QC Inspection</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">

        <input type="hidden" id="modal_asset_id">

        <!-- Condition -->
        <div class="mb-3">
          <label><strong>Item Physical Condition</strong></label><br>
          <label><input type="radio" name="condition" value="New"> New</label>
          <label class="ml-3"><input type="radio" name="condition" value="Used"> Used</label>
        </div>

        <!-- Packed -->
        <div class="mb-3">
          <label><strong>Item Packing</strong></label><br>
          <label><input type="radio" name="packed" value="Yes"> Yes</label>
          <label class="ml-3"><input type="radio" name="packed" value="No"> No</label>
        </div>

        <!-- Damaged -->
        <div class="mb-3">
          <label><strong>Any Damage Found</strong></label><br>
          <label><input type="radio" name="damaged" value="Yes"> Yes</label>
          <label class="ml-3"><input type="radio" name="damaged" value="No"> No</label>
        </div>

        <!-- Specification -->
        <div class="mb-3">
          <label><strong>Overall Specification meets as per RFQ</strong></label><br>
          <label><input type="radio" name="specification" value="Yes"> Yes</label>
          <label class="ml-3"><input type="radio" name="specification" value="No"> No</label>
        </div>

        <!-- Remarks -->
        <div class="mb-3">
          <label><strong>Comments</strong></label>
          <textarea class="form-control" id="qc_remarks" rows="3"></textarea>
        </div>

        <!-- Final QC Result -->
        <div class="mb-3">
          <label><strong>QC Status</strong></label><br>
          <label class="text-success">
            <input type="radio" name="qc_result" value="Passed"> QC Passed
          </label>
          <label class="ml-3 text-danger">
            <input type="radio" name="qc_result" value="Failed"> QC Failed
          </label>
        </div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button class="btn btn-success update-inspection" id="saveInspection">Submit</button>
      </div>

    </div>
  </div>
</div>

@endsection
@section('javascript')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>


$(document).on('click', '.btn-close', function() {
    $('#assetDetailsModal').modal('hide');
});
$(document).on('click', '.po-link', function() {
    let po_id = $(this).data('po-id');
    let url = 'http://devfin.bnu.edu.pk/purchase-orders/' + po_id;
    window.open(url, '_blank'); // Opens in new tab
});
$(document).ready(function() {

    //  $('.select2-show-search').select2({
    //     placeholder: 'Select User',
    //     allowClear: true,
    //     width: '100%',
    //     theme: 'default'
    // });



    $('#assetList').DataTable({
        "pageLength": 10,
        "ordering": false, // ✅ Disable all sorting
        "responsive": true,
        "language": {
            "search": "Filter records:",
            "emptyTable": "No Gate Passes Found"
        },
      dom:
"<'row mb-2'<'col-12'l>>" +   // 🔥 counter upar
"<'row mb-2'<'col-md-6 d-flex align-items-center gap-2'B><'col-md-6 text-end'f>>" +
"rt" +
"<'row mt-2'<'col-md-6'i><'col-md-6'p>>",
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Excel',
                className: 'btn btn-sm btn-success'
            },
            {
                extend: 'pdfHtml5',
                className: 'btn btn-danger btn-sm mr-2',
                title: 'Asset List',
                orientation: 'landscape',
                pageSize: { width: 800, height: 500 },
                exportOptions: { columns: ':not(:last-child)' },
                
            }
        ]
    });

    $(document).on('click', '.update-inspection', function () {
        let itemId = $(this).data('id');
        let status = $(`.inspection-select[data-id="${itemId}"]`).val();

        $.ajax({
            url: "{{ url('/assets/items') }}/" + itemId + "/inspection",
            type: "POST",
            dataType: "json",
            data: {
                _token: "{{ csrf_token() }}",
                inspection_status: status
            },
            success: function (response) {
                if (response.success) {
                    let row = $(`#item-${itemId}`);
                    row.find('.inspection-status').text(response.item.inspection_status);
                    row.find('.unique-code').text(response.item.unique_asset_code ?? '-');
                    row.find('.allocation-status').text(response.item.allocation_status);

                    row.find('.update-inspection').remove();
                    row.find('.inspection-select').remove();
                    row.find('td:last').append('<button class="btn btn-primary btn-sm allocate-asset" data-id="'+itemId+'" data-serial="'+response.item.serial_number+'">Allocate</button>');
                }
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr.responseText);
                alert("Error: " + xhr.status + " " + xhr.statusText);
            }
        });
    });
   
});
// Show allocate modal
$(document).on('click', '.allocate-asset', function (e) {
    e.preventDefault(); // ✅ prevent form submit or page refresh
    // alert('123');
    // return false;
    let itemId = $(this).data('id');
    let serial = $(this).data('serial');
    $('#allocate_item_id').val(itemId);
    $('#serial_display').text(serial);
    $('#allocateModal').modal('show');
});
$(document).on('click', '.view-asset-details', function (e) {
    e.preventDefault();

    let id = $(this).data('id');
    let url = `/assets/${id}`; // adjust if your route prefix is different
    let modal = $('#assetDetailsModal');
    let modalBody = $('#assetDetailsContent');

    // Show loading spinner
    modalBody.html(`
        <div class="text-center py-5 text-muted">
            <div class="spinner-border text-primary"></div>
            <p class="mt-3">Loading asset details...</p>
        </div>
    `);
    modal.modal('show');

    // Fetch data via AJAX
    $.ajax({
        url: url,
        type: 'GET',
        success: function (res) {
            const asset = res.asset;
            console.log(asset[0]['name']);
            const departments = res.departments;
            const users = res.users;
            const buildings = res.resbuildingsQL;

            // ---- BUILD HTML ----
            let html = `
            <div class="container-fluid">
               
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">${asset[0].name ?? '-'} (${asset[0].asset_code ?? '-'})</h5>
                        
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Category:</strong> ${asset[0].category ?? '-'}</div>
                            <div class="col-md-4"><strong>Brand:</strong> ${asset[0].brand ?? '-'}</div>
                            <div class="col-md-4"><strong>Model:</strong> ${asset[0].model ?? '-'}</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>PO ID:</strong> ${asset[0].po_id ?? '-'}</div>
                            <div class="col-md-4"><strong>Order Qty:</strong> ${asset[0].order_quantity ?? '-'}</div>
                            <div class="col-md-4"><strong>Received Qty:</strong> ${asset[0].received_quantity ?? '-'}</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Received Status:</strong> ${asset[0].received_status ?? '-'}</div>
                            <div class="col-md-4"><strong>Inspection:</strong> ${asset[0].inspection_status ?? '-'}</div>
                            <div class="col-md-4"><strong>Status:</strong> ${asset[0].status ?? '-'}</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Warranty:</strong> ${asset[0].warranty_period ?? '-'}</div>
                            <div class="col-md-4">
                                <strong>Warranty Validity:</strong> 
                                ${asset[0].warranty_start_date && asset[0].warranty_end_date 
                                    ? asset[0].warranty_start_date + ' – ' + asset[0].warranty_end_date 
                                    : 'N/A'}
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="mb-3">Asset Items (Serial Numbers & Inspection)</h4>
                <form id="bulkAllocateForm">
                    <table class="table table-bordered table-hover shadow-sm align-middle" id="itemsTable">
                        <thead class="table-primary text-start">
                            <tr>
                                <th class="text-center align-middle" style="width: 40px;">
    <input type="checkbox" id="select_all">
</th>
                                <th class="text-start">#</th>
                                <th class="text-start">Serial No</th>
                                <th class="text-start">Inspection Status</th>
                                <th class="text-start">Unique Asset Code</th>
                                <th class="text-start">Assigned Dept.</th>
                                <th class="text-start">Location</th>
                                <th class="text-start">Assigned Resource</th>
                                <th class="text-start">Status</th>
                                <th class="text-start">Action</th>
                            </tr>
                        </thead>
                        <tbody>`;

            asset.forEach((item, index) => {
                html += `
                <tr id="item-${item.asset_item_id}">
                    <td class="text-center align-middle">
                        ${
                            ((item.inspection_status === 'Passed' && item.allocation_status !== 'Allocated')
                            || item.allocation_status !== 'Request Sent')
                                ? `<input style="margin-left:-10px;margin-top: -9px;" type="checkbox" name="selected_items[]" value="${item.asset_item_id}" class="form-check-input">`
                                : ''
                        }
                    </td>
                    <td class="text-start align-middle">${index + 1}</td>
                    <td class="text-start">${item.serial_number ?? '-'}</td>
                    <td class="inspection-status text-start">${item.inspection_status ?? 'Pending'}</td>
                    <td class="unique-code text-start">${item.unique_asset_code ?? '-'}</td>
                    <td class="allocation-dept text-start">${item.dept_name ?? '-'}</td>
                    <td class="allocation-location text-start">${item.building_name ?? '-'}</td>
                    <td class="allocation-resource text-start">
                        ${item.emp_f_name && item.emp_l_name ? item.emp_f_name + ' ' + item.emp_l_name : '-'}
                    </td>
                    <td class="allocation-status text-start">${item.allocation_status ?? 'Unallocated'}</td>
                    <td class="text-start">
    ${  
        item.inspection_status !== 'Passed'
            ? `
                <div class="d-flex align-items-center gap-2">
                  
                  
                    <button type="button" 
                        class="btn btn-success btn-sm update-inspection-modal" 
                        data-id="${item.asset_item_id}">
                        QC Inspection
                    </button>
                </div>
              `
            : (
                item.allocation_status !== 'Allocated'
                    ? `
                        <button type="button" class="btn btn-primary btn-sm allocate-asset"
                                data-id="${item.asset_item_id}"
                                data-serial="${item.serial_number}">
                            Allocate
                        </button>
                      `
                    : `
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-info">Allocated</span>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deAllocate(${item.asset_item_id})">
                                Deallocate
                            </button>
                        </div>
                      `
              )
    }
</td>
                </tr>`;
            });

            html += `
                        </tbody>
                    </table>
                    <div class="mt-3">
                        <button type="button" class="btn btn-warning" id="bulkAllocateBtn" disabled>Allocate Selected</button>
                    </div>
                </form>
            </div>

            <!-- Hidden allocation modal for inner use -->
            <div class="modal fade" id="allocateModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <form id="allocateFormSubmit">
                @csrf
                    <input type="text" id="allocate_item_id" name="asset_item_ids">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Allocate Asset(s)</h5>                            
                           
                        </div>
                        <div class="modal-body">
                            <p><strong>Serial(s):</strong> <span id="serial_display"></span></p>

                            <div class="mb-3">
                                <label class="form-label">Department</label>
                                <select class="form-control" name="allocation_department" required>
                                    <option value="">-- Select Department --</option>`;
                                        departments.forEach(d => {
                                            html += `<option value="${d.dept_id}">${d.dept_name}</option>`;
                                        });
                            html += `</select></div>
                            <div class="mb-3">
                            <label class="form-label">Assignee</label>
                            <select class="form-control select2-show-search" id="assignee_user_id" name="assignee_user_id" required>
                                <option value="">Select Assigneessss</option>`;
                                users.forEach(u => {
                                    html += `<option value="${u.id}">${u.prefix} ${u.full_name}</option>`;
                                });
                            html += `</select></div>
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                
                                
                                 <select class="form-control" name="allocation_location" required>
                                    <option value="">-- Select Location --</option>`;
                                        buildings.forEach(b => {
                                            html += `<option value="${b.building_id}">${b.building_name}</option>`;
                                        });
                                html += `</select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Remarks</label>
                                <textarea name="remarks" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">                        
                            <button type="button" class="btn btn-secondary closeModal">Cancel</button>
                            <button type="button" class="btn btn-primary allocateForm">Allocate</button>
                        </div>
                    </div>
                </form>
              </div>
            </div>`;

            // Inject HTML into modal
            modalBody.html(html);

            $('#assignee_user_id').select2({
                dropdownParent: $('#allocateModal'),
                placeholder: 'Select or search assignee',
                allowClear: true,
                width: '100%'
            });

            $('#itemsTable').DataTable({
                "pageLength": 10,
                "ordering": false, // ✅ Disable all sorting
                "responsive": true,
                "language": {
                    "search": "Filter records:",
                    "emptyTable": "No Gate Passes Found"
                },
            dom:
"<'row mb-2'<'col-12'l>>" +   // 🔥 counter upar
"<'row mb-2'<'col-md-6 d-flex align-items-center gap-2'B><'col-md-6 text-end'f>>" +
"rt" +
"<'row mt-2'<'col-md-6'i><'col-md-6'p>>",
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        className: 'btn btn-sm btn-success',
                        exportOptions: { columns: ':not(:first-child):not(:last-child)' },
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-danger btn-sm mr-2',
                        title: 'Asset List',
                        orientation: 'landscape',
                        pageSize: { width: 800, height: 500 },
                        exportOptions: { columns: ':not(:first-child):not(:last-child)' },
                        
                    }
                ]
            });

        },
        error: function (xhr) {
            modalBody.html(`
                <div class="alert alert-danger text-center">
                    <strong>Error:</strong> Could not load asset details. (${xhr.status})
                </div>
            `);
        }
    });
});

$(document).on('change', '#select_all', function () {
    $('input[name="selected_items[]"]').prop('checked', $(this).prop('checked'));
});
$(document).on('click', '.update-inspection-modal', function () {
    let id = $(this).data('id');

    $('#modal_asset_id').val(id);

    let modal = new bootstrap.Modal(document.getElementById('inspectionModal'));
    modal.show();
});

$(document).on('click', '.closeModal', function () {
    $('#allocateModal').modal('hide');
});

function deAllocate(assetItemId) {
    if (!confirm("Are you sure you want to deallocate this asset?")) {
        return;
    }

    $.ajax({
        url: '/assets/deallocate', // Laravel route (update accordingly)
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token
            asset_item_id: assetItemId
        },
        beforeSend: function() {
            showToast('info', 'Processing deallocation...');
        },
        success: function(response) {
            if (response.success) {
                showToast('success', 'Asset deallocated successfully!');               
                
            } else {
                showToast('error', response.message || 'Something went wrong!');
            }
        },
        error: function(xhr) {
            showToast('error', 'Server error!');
            console.error(xhr.responseText);
        }
    });
}



// Handle allocation form
// $(document).on('click', '.allocateForm', function (e) {
//     e.preventDefault(); // prevent default button behavior

//     let form = $('#allocateFormSubmit'); // target the form
//     let formData = form.serialize();

//     $.ajax({
//         url: "{{ url('assets/allocate') }}",
//         type: "POST",
//         data: formData,
//         success: function (response) {
//             // if (response.success) {
//             //        $('#allocateModal').modal('hide');
//             //         let row = $(`#item-${response.allocated_ids}`);
//             //         console.log(row);
//             //         row.find('.allocation-dept').text(response.department_id);
//             //         row.find('.allocation-location').text(response.allocated_location);
//             //         row.find('.allocation-status').text('Allocated');
//             //         row.find('td:last').html('<span class="badge bg-info">Allocated</span>');

                 
//             //     }
//             if (response.success) {
//                 $('#allocateModal').modal('hide');

//                 // Check if allocated_ids is array (bulk) or single (string)
//                 let ids = Array.isArray(response.allocated_ids)
//                     ? response.allocated_ids
//                     : [response.allocated_ids];

//                 ids.forEach(function(id) {
//                     let row = $(`#item-${id}`);
//                     row.find('.allocation-dept').text(response.department_id);
//                     row.find('.allocation-location').text(response.allocated_location);
//                     row.find('.allocation-status').text('Allocated');
//                     row.find('td:last').html('<span class="badge bg-info">Allocated</span>');
//                     row.find('input[type="checkbox"]').prop('checked', false);
//                 });

//                 // Disable bulk allocate button after operation
//                 $('#bulkAllocateBtn').prop('disabled', true);
//             }
//         },
//         error: function (xhr) {
//             console.error("AJAX Error:", xhr.responseText);
//             alert("Error: " + xhr.status + " " + xhr.statusText);
//         }
//     });
// });

$(document).on('click', '.allocateForm', function (e) {
    e.preventDefault(); // prevent default button behavior

    let form = $('#allocateFormSubmit'); // target the form
    let formData = form.serialize();

    $.ajax({
        url: "{{ url('assets/allocate') }}",
        type: "POST",
        data: formData,
        success: function (response) {
            if (response.success) {

                // 🔹 Do NOT hide the modal immediately — show updates first
                 $('#allocateModal').modal('hide');

                // Loop through each allocated item from the response
                response.allocated_items.forEach(function (item) {
                    let row = $(`#item-${item.asset_item_id}`);
                    if (row.length) {
                        row.find('.allocation-dept').text(item.department_id);  
                        row.find('.allocation-location').text(item.allocated_location);
                        row.find('.allocation-resource').text(item.emp_name);
                        row.find('.allocation-status').text('Request Sent');
                        row.find('td:last').html('<span class="badge bg-info">Allocated</span>');
                        row.find('input[type="checkbox"]').prop('checked', false);
                         row.find('input[type="checkbox"]').prop('checked', false).hide(); 
                    }
                });

                // Disable bulk allocate button again
               
                $('#bulkAllocateBtn').prop('disabled', true);

                // ✅ Show success alert
                Swal.fire({
                    title: "Success!",
                    text: response.message,
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        },
        error: function (xhr) {
            console.error("AJAX Error:", xhr.responseText);
            Swal.fire({
                title: "Error!",
                text: "Something went wrong during allocation.",
                icon: "error"
            });
        }
    });
});


$(document).on('change', 'input[name="selected_items[]"]', function () {
    const anyChecked = $('input[name="selected_items[]"]:checked').length > 0;
    $('#bulkAllocateBtn').prop('disabled', !anyChecked);
});

// Bulk allocation button click
$(document).on('click', '#bulkAllocateBtn', function () {

    const selectedIds = $('input[name="selected_items[]"]:checked')
        .map(function () { return $(this).val(); })
        .get();

    const selectedSerials = $('input[name="selected_items[]"]:checked')
        .closest('tr')
        .find('td:nth-child(3)') // adjust if serial number is in a different column
        .map(function () { return $(this).text(); })
        .get()
        .join(', ');

    // Set hidden input value (comma-separated IDs)
    $('#allocate_item_id').val(selectedIds.join(','));

    // Display serials in modal
    $('#serial_display').text(selectedSerials);

    $('#allocateModal').modal('show');
});


</script>

