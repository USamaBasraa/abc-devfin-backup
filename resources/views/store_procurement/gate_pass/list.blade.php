@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />
<style>
    .modern-card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        background-color: #FFFFFF;
        border: none;
        overflow: hidden;
    }

    .modern-header {
        background-color: #17675F ;
        color: white;
        padding: 1.25rem 1.5rem;
        font-size: 18px;
        font-weight: 600;
        border-radius: 12px 12px 0 0;
    }

    .modern-table {
        margin-bottom: 0;
        border: none;
        min-width: 1200px;
        white-space: nowrap;
    }

    .modern-table thead {
        background-color: #17675F ;
    }

    .modern-table thead th {
        color: white;
        font-weight: 600;
        font-size: 14px;
        padding: 1rem;
        border: none;
        text-align: left;
        vertical-align: middle;
    }

    .modern-table tbody tr {
        background-color: #FFFFFF;
        border-bottom: 1px solid #f0f0f0;
    }

    .modern-table tbody tr:nth-child(even) {
        background-color: #F8F8F8;
    }

    .modern-table tbody td {
        padding: 1rem;
        color: #333333;
        font-size: 14px;
        border: none;
        vertical-align: middle;
    }

    .modern-view-btn {
        background-color: #4A70E0;
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.3s ease;
    }

    .modern-view-btn:hover {
        background-color: #3a5fc9;
        color: white;
        text-decoration: none;
    }

    .modern-card-body {
        padding: 0;
    }

    .table-responsive {
        border-radius: 0 0 12px 12px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .modern-table thead th,
    .modern-table tbody td {
        white-space: nowrap;
    }
    .clickable-td {
        cursor: pointer;
        color: #007bff; /* Bootstrap primary blue */
        font-weight: 500;
        transition: color 0.2s ease-in-out;
    }

    .clickable-td:hover {
        text-decoration: underline;
        color: #0056b3;
    }

    .data-table-container {
        position: relative;
        display: flex;
        flex-direction: column;
        max-height: 70vh;
    }

    .table-header-fixed {
        position: sticky;
        top: 0;
        background: #FFFFFF;
        z-index: 6;
        padding: 1rem 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .table-header-left-controls {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .table-header-left-controls .dt-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .table-header-left-controls .dataTables_length {
        display: flex;
        align-items: center;
    }

    .table-header-fixed .dataTables_length label,
    .table-header-fixed .dataTables_filter label {
        margin-bottom: 0;
    }

    .table-scrollable {
        overflow-y: auto;
        flex: 1 1 auto;
    }

    .table-footer-fixed {
        position: sticky;
        bottom: 0;
        background: #FFFFFF;
        padding: 1rem 20px;
        border-top: 1px solid #e9ecef;
        z-index: 5;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .table-footer-fixed .dataTables_info {
        margin: 0;
    }

    .table-footer-fixed .dataTables_paginate {
        margin: 0;
    }

    .dataTables_length select {
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 0.375rem 2rem 0.375rem 0.75rem;
        height: 38px !important;
        font-size: 14px;
        color: #495057;
        background-color: #fff;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 12px;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        cursor: pointer;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .dataTables_length select:focus {
        border-color: #17675F;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(69, 98, 195, 0.25);
    }

    .dataTables_length label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 14px;
        color: #495057;
        font-weight: 500;
    }
</style>

<div class="container-fluid py-4">
    <div class="modern-card">

        <div class="card-header" style="background: none !important; color: #252525 !important;">
            <h4 class="mb-0">
                All Gate Passes
            </h4>
        </div>

        {{-- <div class="modern-header" style="background-color: #f5f5f5; color: #252525;">
            <i class="fas fa-file-alt"></i> All Gate Passes
            {{-- <a href="{{ route('gate_pass.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> New Gate Pass
            </a> --}}
        {{-- </div>  --}}

        <div class="modern-card-body" style="padding: 0 20px!important;">
            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- Table --}}
            <div class="data-table-container">
                <div id="gatePassTableHeader" class="table-header-fixed"></div>
                <div class="table-responsive table-scrollable">
                    <table class="modern-table table" id ="gatePassTable">
                        <thead>
                            <tr>
                                <th style="background: #17675F !important; color: #fff !important;">Sr. No.</th>
                                <th style="background: #17675F !important; color: #fff !important;">GATE PASS ID</th>
                                <th style="background: #17675F !important; color: #fff !important;">PO NUMBER</th>
                                {{-- <th>Vendor</th> --}}
                                <th style="background: #17675F !important; color: #fff !important;">DELIVERY PERSON</th>
                                <th style="background: #17675F !important; color: #fff !important;">CNIC</th>
                                <th style="background: #17675F !important; color: #fff !important;">CONTACT</th>
                                <th style="background: #17675F !important; color: #fff !important;">VEHICLE</th>
                                <th style="background: #17675F !important; color: #fff !important;">REMARKS</th>
                                <th style="background: #17675F !important; color: #fff !important;">CREATED BY</th>
                                <th style="background: #17675F !important; color: #fff !important;">CREATED AT</th>
                                <!-- <th>ACTION</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sr = 1; ?>
                            @forelse($gatePasses as $gp)
                                <tr>  
                                    <td>{{$sr++}}
                                    <td class="text-center viewGatePassBtn clickable-td"  data-id="{{ $gp->gp_id }}">{{ $gp->gp_id }}</td>
                                    <td class="text-center clickable-td po-link"  data-po-id="{{ $gp->po_id }}">{{ $gp->po_id ?? '-' }}</td>
                                    {{-- <td>{{ $gp->purchaseOrder->vendor->vendor_name ?? '-' }}</td> --}}
                                    <td>{{ $gp->delivery_person }}</td>
                                    <td>{{ $gp->cnic }}</td>
                                    <td>{{ $gp->contact_no }}</td>
                                    <td>{{ $gp->vehicle_no }}</td>
                                    <td>{{ $gp->remarks }}</td>
                                    <td>{{ $gp->user->name ?? 'N/A' }}</td>
                                    <td>{{ $gp->created_at->format('d-m-Y H:i') }}</td>
                                    <!-- <td class="text-center">
                                        <a href="{{ route('gatepass.show', $gp->gp_id) }}" class="modern-view-btn">
                                            View
                                        </a>
                                    </td> -->
                                    {{-- <td class="text-center">
                                        <a href="{{ url('gatepass/show', $gp->gp_id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle"></i> No Gate Passes Found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div id="gatePassTableFooter" class="table-footer-fixed"></div>
            </div>

            <!-- {{-- Pagination --}}
            <div class="d-flex justify-content-center my-3">
                {{ $gatePasses->links() }}
            </div> -->
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

{{-- Include Bootstrap 4 & Font Awesome --}}
@push('styles')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
@endpush

@endsection

@section('javascript')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on('click', '.po-link', function() {
    let po_id = $(this).data('po-id');
    let url = 'http://devfin.bnu.edu.pk/purchase-orders/' + po_id;
    window.open(url, '_blank'); // Opens in new tab
});
$(document).ready(function() {

    const table = $('#gatePassTable').DataTable({
        "pageLength": 10,
        "ordering": false, // ✅ Disable all sorting
        "responsive": true,
        "language": {
            "search": "Filter records:",
            "emptyTable": "No Gate Passes Found"
        },
        dom:
            "<'d-flex justify-content-between align-items-center mb-3'<'d-flex align-items-center gap-3'B l>f>" +
            "rtip",
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Excel',
                className: 'btn btn-sm btn-success'
            },
            {
                extend: 'pdfHtml5',
                className: 'btn btn-danger btn-sm mr-2',
                title: 'All Gate Passes',
                orientation: 'landscape',
                pageSize: { width: 800, height: 500 },
                exportOptions: { columns: ':not(:last-child)' },
                
            }
        ]
    });

    const headerContainer = $('#gatePassTableHeader');
    const footerContainer = $('#gatePassTableFooter');
    const wrapper = $('#gatePassTable').closest('.dataTables_wrapper');
    const headerLeft = $('<div class="table-header-left-controls"></div>');

    headerLeft.append(wrapper.find('.dt-buttons'));
    headerLeft.append(wrapper.find('.dataTables_length'));
    headerContainer.append(headerLeft);
    headerContainer.append(wrapper.find('.dataTables_filter'));
    footerContainer.append(wrapper.find('.dataTables_info'));
    footerContainer.append(wrapper.find('.dataTables_paginate'));

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
                                <td>${serials}</td>
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
                                                <th>Serial Numbers</th>
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
});

    </script>
