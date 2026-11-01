@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />

<style>
    .store-gp-card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        background: #fff;
        border: none;
        overflow: hidden;
    }
    .store-gp-header {
        background: #17675F !important;
        background-image: none !important;
        color: #fff;
        padding: 1.1rem 1.5rem;
        font-size: 18px;
        font-weight: 600;
    }
    .store-gp-table thead th {
        background: #17675F;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
        border: none;
        vertical-align: middle;
    }
    .store-gp-table tbody td {
        font-size: 14px;
        vertical-align: middle;
        white-space: nowrap;
    }
    .store-gp-table tbody tr:nth-child(even) {
        background: #f4faf9;
    }
    .container-fluid h4 { color: #17675F; }
</style>

<div class="container-fluid py-4">
    <div class="store-gp-card">
        <div class="store-gp-header d-flex justify-content-between align-items-center">
            <span>
                <i class="fa fa-truck mr-2"></i>
                Store Items Gate Pass
            </span>
            <span class="badge badge-light text-dark">
                {{ count($gatePassItems) }} {{ Str::plural('Record', count($gatePassItems)) }}
            </span>
        </div>

        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover mb-0 store-gp-table" id="storeGatePassTable" style="width:100%;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Gate Pass #</th>
                            <th>PO No</th>
                            <th>Item Title</th>
                            <th>Budget Head</th>
                            <th>Vendor</th>
                            <th class="text-center">Ordered Qty</th>
                            <th class="text-center">Delivered Qty</th>
                            <th>Delivery Status</th>
                            <th>Delivery Person</th>
                            <th>Contact</th>
                            <th>Vehicle</th>
                            <th>Gate Pass Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gatePassItems as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>GP-{{ $row->gp_id }}</strong></td>
                                <td>{{ $row->po_no ?? ('PO-' . ($row->po_id ?? '-')) }}</td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $row->item_title ?? $row->po_item_title ?? '-' }}</div>
                                    @if(!empty($row->unit))
                                        <small class="text-muted">Unit: {{ $row->unit }}</small>
                                    @endif
                                </td>
                                <td>{{ $row->head_title ?? '-' }}</td>
                                <td>{{ $row->vendor_name ?? '-' }}</td>
                                <td class="text-center">{{ $row->requested_quantity ?? $row->po_quantity ?? '-' }}</td>
                                <td class="text-center">{{ $row->delivered_quantity ?? '-' }}</td>
                                <td>
                                    @php $status = strtolower(trim((string) ($row->delivery_status ?? ''))); @endphp
                                    @if($status === 'completed')
                                        <span class="badge badge-success">Completed</span>
                                    @elseif($status === 'partially delivered')
                                        <span class="badge badge-warning">Partially Delivered</span>
                                    @elseif($status !== '')
                                        <span class="badge badge-secondary">{{ $row->delivery_status }}</span>
                                    @else
                                        <span class="badge badge-light text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $row->delivery_person ?? '-' }}</td>
                                <td>{{ $row->contact_no ?? '-' }}</td>
                                <td>{{ $row->vehicle_no ?? '-' }}</td>
                                <td>
                                    @if(!empty($row->gate_pass_date))
                                        {{ \Carbon\Carbon::parse($row->gate_pass_date)->format('d-M-Y h:i A') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
$(function () {
    var exportBodyFormat = {
        body: function (data) {
            return $('<div>').html(data).text().replace(/\s+/g, ' ').trim();
        }
    };

    if ($.fn.DataTable.isDataTable('#storeGatePassTable')) {
        $('#storeGatePassTable').DataTable().destroy();
    }
    $('#storeGatePassTable').DataTable({
        pageLength: 10,
        order: [],
        autoWidth: false,
        language: {
            search: 'Search:',
            emptyTable: 'No store items with gate pass found.',
            zeroRecords: 'No matching gate passes found.'
        },
        dom: "<'d-flex justify-content-between align-items-center mb-3 flex-wrap'<'d-flex align-items-center'B l>f>rt<'d-flex justify-content-between align-items-center mt-3 flex-wrap'i p>",
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Excel',
                className: 'btn btn-sm btn-success mr-2',
                title: 'Store Items Gate Pass',
                exportOptions: { format: exportBodyFormat }
            },
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                className: 'btn btn-sm btn-danger',
                title: 'Store Items Gate Pass',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: { format: exportBodyFormat }
            }
        ]
    });
});
</script>
@endpush
