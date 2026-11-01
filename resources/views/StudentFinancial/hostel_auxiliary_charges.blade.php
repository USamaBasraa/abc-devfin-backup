@extends('layouts.app')

@php
    $today = date('Y-m-d');
@endphp

<style>
    #hostel_aux_charges_table_wrapper .dt-buttons {
        margin-bottom: 12px;
    }

    #hostel_aux_charges_table thead th {
        background-color: #4454c3;
        color: #ffff;
        text-align: center;
        vertical-align: middle;
        font-size: 12px;
        white-space: normal;
    }

    #hostel_aux_charges_table tbody td {
        text-align: center;
        vertical-align: middle;
    }

    .hostel-aux-filter-card {
        border: 1px solid #e4e7f0;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        margin-bottom: 1rem;
        background: #fff;
    }

    .filter-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
</style>

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h2 class="mb-0">Hostel &amp; Auxiliary Charges</h2>
        </div>
        <div class="card-body">
            <div class="hostel-aux-filter-card">
                <div class="row">
                    <div class="form-group col-12 col-lg-2">
                        <label class="font-weight-bold" for="from_date">From Date</label>
                        <input type="date" class="form-control" id="from_date" max="{{ $today }}">
                    </div>
                    <div class="form-group col-12 col-lg-2">
                        <label class="font-weight-bold" for="to_date">To Date</label>
                        <input type="date" class="form-control" id="to_date" max="{{ $today }}">
                    </div>
                    <div class="form-group col-12 col-lg-2">
                        <label class="font-weight-bold" for="session_id">Session</label>
                        <select class="form-control" id="session_id">
                            <option value="">Select Session</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->sess_id ?? $session->SESS_ID }}">
                                    {{ $session->sess_name ?? $session->SESS_NAME }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-lg-2">
                        <label class="font-weight-bold" for="hostel_type">Hostel Type</label>
                        <select class="form-control" id="hostel_type">
                            <option value="all">All</option>
                            <option value="BH">BH</option>
                            <option value="GH">GH</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-lg-2">
                        <label class="font-weight-bold" for="challan_type">Challan Type</label>
                        <select class="form-control" id="challan_type">
                            <option value="all">All</option>
                            <option value="hostel">Hostel_Fee</option>
                            <option value="auxiliary">Auxiliary</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-lg-2">
                        <label class="font-weight-bold" for="hostel_campus">Hostel Campus</label>
                        <select class="form-control" id="hostel_campus">
                            <option value="all">All</option>
                            <option value="bnu">BNU</option>
                            <option value="bedian">BEDIAN</option>
                            <option value="safari">Safari</option>
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <button type="button" class="btn btn-primary px-4" id="loadReportBtn">
                            <i class="fa fa-filter mr-2"></i>Load Report
                        </button>
                        <button type="button" class="btn btn-secondary px-4 ml-2" id="resetReportBtn">
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <div id="hostel_aux_charges_table_wrapper" class="table-responsive">
                <table id="hostel_aux_charges_table" class="table table-bordered mb-0" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Hostel Campus</th>
                            <th>Challan Type</th>
                            <th>Hostel Type</th>
                            <th>Fee bills Issued</th>
                            <th>Amount</th>
                            <th>Cancelled</th>
                            <th>Paid</th>
                            <th>Unpaid</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>BNU</td>
                            <td>-</td>
                            <td>-</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>BEDIAN</td>
                            <td>-</td>
                            <td>-</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>Safari</td>
                            <td>-</td>
                            <td>-</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <hr class="my-4">

            @include('Hostel.components.Summary.campus_matrix_summary', ['sessions' => $sessions])
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
(function () {
    var reportTable = null;
    var campusMatrixTable = null;

    function getExportTitle() {
        var sessionText = $('#session_id option:selected').text() || 'All Sessions';
        var fromDate = $('#from_date').val() || '-';
        var toDate = $('#to_date').val() || '-';

        return 'Hostel & Auxiliary Charges - ' + sessionText +
            ' | From: ' + fromDate +
            ' | To: ' + toDate;
    }

    function getCampusMatrixExportTitle() {
        var sessionText = $('#matrix_session_id option:selected').text() || 'All Sessions';
        var fromDate = $('#matrix_from_date').val() || '-';
        var toDate = $('#matrix_to_date').val() || '-';
        var hostelType = $('#matrix_hostel_type option:selected').text() || 'All Campuses';

        return 'Hostel Campus Summary - ' + sessionText +
            ' | From: ' + fromDate +
            ' | To: ' + toDate +
            ' | ' + hostelType;
    }

    function getExportButtons(titleFn) {
        return [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o mr-1"></i>Excel',
                title: titleFn,
                exportOptions: {
                    columns: ':visible',
                    modifier: { page: 'all' }
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf-o mr-1"></i>PDF',
                title: titleFn,
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':visible',
                    modifier: { page: 'all' }
                },
                customize: function (doc) {
                    doc.defaultStyle.fontSize = 9;
                    doc.styles.tableHeader.fontSize = 9;
                    doc.styles.tableHeader.alignment = 'center';
                    doc.pageMargins = [12, 20, 12, 12];
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print mr-1"></i>Print',
                title: titleFn,
                exportOptions: {
                    columns: ':visible',
                    modifier: { page: 'all' }
                },
                customize: function (win) {
                    var style = win.document.createElement('style');
                    style.innerHTML = 'table { width: 100% !important; border-collapse: collapse; } th, td { border: 1px solid #999; padding: 4px; text-align: center; font-size: 10pt; } th { color: white; background: #4454c3; }';
                    win.document.head.appendChild(style);
                }
            }
        ];
    }

    function initReportTable() {
        if ($.fn.DataTable.isDataTable('#hostel_aux_charges_table')) {
            reportTable = $('#hostel_aux_charges_table').DataTable();
            return;
        }

        reportTable = $('#hostel_aux_charges_table').DataTable({
            paging: false,
            searching: false,
            info: false,
            ordering: false,
            autoWidth: false,
            responsive: false,
            dom: 'Bfrtip',
            buttons: getExportButtons(getExportTitle)
        });
    }

    function initCampusMatrixDataTable() {
        if ($.fn.DataTable.isDataTable('#campus_matrix_table')) {
            campusMatrixTable = $('#campus_matrix_table').DataTable();
            return;
        }

        campusMatrixTable = $('#campus_matrix_table').DataTable({
            paging: false,
            searching: false,
            info: false,
            ordering: false,
            autoWidth: false,
            responsive: false,
            dom: 'Bfrtip',
            buttons: getExportButtons(getCampusMatrixExportTitle)
        });
    }

    function renderRows(rows) {
        if (!reportTable) {
            initReportTable();
        }

        reportTable.clear();

        if (!rows || !rows.length) {
            reportTable.draw();
            return;
        }

        rows.forEach(function (row) {
            reportTable.row.add([
                row.hostel_campus || '-',
                row.challan_type || '-',
                row.hostel_type || '-',
                row.fee_bills_issued || 0,
                row.amount || '0',
                row.cancelled || 0,
                row.paid || 0,
                row.unpaid || 0
            ]);
        });

        reportTable.draw();
    }

    function renderCampusMatrixTable(matrix) {
        if (!campusMatrixTable) {
            initCampusMatrixDataTable();
        }

        var bhOn = matrix && matrix.BH && matrix.BH.on_campus ? matrix.BH.on_campus : { issued: 0, resident: 0 };
        var bhBedian = matrix && matrix.BH && matrix.BH.bedian ? matrix.BH.bedian : { issued: 0, resident: 0 };
        var bhSafari = matrix && matrix.BH && matrix.BH.safari ? matrix.BH.safari : { issued: 0, resident: 0 };
        var ghOn = matrix && matrix.GH && matrix.GH.on_campus ? matrix.GH.on_campus : { issued: 0, resident: 0 };
        var ghBedian = matrix && matrix.GH && matrix.GH.bedian ? matrix.GH.bedian : { issued: 0, resident: 0 };
        var ghSafari = matrix && matrix.GH && matrix.GH.safari ? matrix.GH.safari : { issued: 0, resident: 0 };

        campusMatrixTable.clear();
        campusMatrixTable.row.add([
            'BH',
            bhOn.issued || 0,
            bhOn.resident || 0,
            bhBedian.issued || 0,
            bhBedian.resident || 0,
            bhSafari.issued || 0,
            bhSafari.resident || 0
        ]);
        campusMatrixTable.row.add([
            'GH',
            ghOn.issued || 0,
            ghOn.resident || 0,
            ghBedian.issued || 0,
            ghBedian.resident || 0,
            ghSafari.issued || 0,
            ghSafari.resident || 0
        ]);
        campusMatrixTable.draw(false);
    }

    function loadReport() {
        var sessionId = $('#session_id').val();
        if (!sessionId) {
            alert('Please select session.');
            return;
        }

        $('#loadReportBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-2"></i>Loading...');

        $.ajax({
            url: '{{ url('hostel-auxiliary-charges/data') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                session_id: sessionId,
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val(),
                hostel_type: $('#hostel_type').val(),
                challan_type: $('#challan_type').val(),
                hostel_campus: $('#hostel_campus').val()
            },
            success: function (response) {
                if (response.success) {
                    renderRows(response.data || []);
                } else {
                    alert(response.message || 'Unable to load report.');
                }
            },
            error: function (xhr) {
                var message = 'Unable to load report.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                alert(message);
            },
            complete: function () {
                $('#loadReportBtn').prop('disabled', false).html('<i class="fa fa-filter mr-2"></i>Load Report');
            }
        });
    }

    function loadCampusMatrixSummary() {
        var sessionId = $('#matrix_session_id').val();

        if (!sessionId) {
            alert('Please select Session');
            return;
        }

        var $btn = $('#matrixLoadBtn');
        $btn.prop('disabled', true).text('Loading...');

        $.ajax({
            url: '{{ url('/api/hostel-summary/get-campus-matrix') }}',
            type: 'GET',
            data: {
                session_id: sessionId,
                from_date: $('#matrix_from_date').val(),
                to_date: $('#matrix_to_date').val(),
                hostel_type: $('#matrix_hostel_type').val()
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
                'Accept': 'application/json'
            },
            dataType: 'json',
            success: function (response) {
                if (response && response.success) {
                    renderCampusMatrixTable(response.matrix);
                } else {
                    alert((response && response.message) ? response.message : 'Unable to load campus summary');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText || xhr.statusText);
                alert('Unable to load campus summary');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fa fa-filter mr-2"></i>Apply Filter');
            }
        });
    }

    function resetFilters() {
        $('#from_date').val('');
        $('#to_date').val('');
        $('#session_id').val('');
        $('#hostel_type').val('all');
        $('#challan_type').val('all');
        $('#hostel_campus').val('all');

        renderRows([
            { hostel_campus: 'BNU', challan_type: '-', hostel_type: '-', fee_bills_issued: 0, amount: '0', cancelled: 0, paid: 0, unpaid: 0 },
            { hostel_campus: 'BEDIAN', challan_type: '-', hostel_type: '-', fee_bills_issued: 0, amount: '0', cancelled: 0, paid: 0, unpaid: 0 },
            { hostel_campus: 'Safari', challan_type: '-', hostel_type: '-', fee_bills_issued: 0, amount: '0', cancelled: 0, paid: 0, unpaid: 0 }
        ]);
    }

    function resetCampusMatrixTable() {
        $('#matrix_from_date').val('');
        $('#matrix_to_date').val('');
        $('#matrix_session_id').val('');
        $('#matrix_hostel_type').val('all');
        renderCampusMatrixTable(null);
    }

    $(document).ready(function () {
        initReportTable();
        initCampusMatrixDataTable();
        $('#loadReportBtn').on('click', loadReport);
        $('#resetReportBtn').on('click', resetFilters);
        $('#matrixLoadBtn').on('click', loadCampusMatrixSummary);
        $('#matrixResetBtn').on('click', resetCampusMatrixTable);
    });
})();
</script>
@endsection
