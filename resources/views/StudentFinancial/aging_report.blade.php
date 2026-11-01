@extends('layouts.app')

@php
    $today = date('Y-m-d');

    /*
    |--------------------------------------------------------------------------
    | Static Filter Data
    |--------------------------------------------------------------------------
    */




    /*
    |--------------------------------------------------------------------------
    | Static Challan Records
    |--------------------------------------------------------------------------
    | DataTable will show exactly these 11 records.
    */

@endphp

<style>
    #enrolled_students_table_wrapper {
        width: 100%;
    }

    #enrolled_students_table_length,
#enrolled_students_table_paginate {
    margin-top: 10px;
}

#enrolled_students_table_filter {
    margin-bottom: 10px;
}

.dataTables_wrapper .dataTables_paginate {
    float: right;
    text-align: right;
}

.dataTables_wrapper .dataTables_info {
    padding-top: 12px;
}

    #enrolled_students_table,
    #enrolled_students_table_wrapper .dataTables_scrollHead table,
    #enrolled_students_table_wrapper .dataTables_scrollBody table,
    #enrolled_students_table_wrapper .dataTables_scrollFoot table {
        table-layout: fixed !important;
        border-collapse: collapse !important;
    }

    #enrolled_students_table_wrapper .dataTables_scrollHead,
    #enrolled_students_table_wrapper .dataTables_scrollBody,
    #enrolled_students_table_wrapper .dataTables_scrollFoot {
        width: 100% !important;
    }

    #enrolled_students_table_wrapper .dataTables_scrollBody {
        overflow-x: auto !important;
    }

    #enrolled_students_table_wrapper th,
    #enrolled_students_table_wrapper td {
        box-sizing: border-box !important;
        text-align: center;
        vertical-align: middle;
    }

    #enrolled_students_table_wrapper thead th,
    #enrolled_students_table_wrapper tfoot th {
        color: #000;
        font-size: 12px;
        white-space: normal !important;
        word-break: break-word;
    }

    #enrolled_students_table_wrapper tbody td {
        white-space: nowrap !important;
    }

    #enrolled_students_table_wrapper th:nth-child(1),
    #enrolled_students_table_wrapper td:nth-child(1) {
        min-width: 130px !important;
        width: 130px !important;
    }

    #enrolled_students_table_wrapper th:nth-child(2),
    #enrolled_students_table_wrapper td:nth-child(2) {
        min-width: 120px !important;
        width: 120px !important;
    }

    #enrolled_students_table_wrapper th:nth-child(3),
    #enrolled_students_table_wrapper td:nth-child(3),
    #enrolled_students_table_wrapper th:nth-child(4),
    #enrolled_students_table_wrapper td:nth-child(4) {
        min-width: 120px !important;
        width: 120px !important;
    }

    #enrolled_students_table_wrapper th:nth-child(5),
    #enrolled_students_table_wrapper td:nth-child(5) {
        min-width: 170px !important;
        width: 170px !important;
    }

    #enrolled_students_table_wrapper th:nth-child(6),
    #enrolled_students_table_wrapper td:nth-child(6) {
        min-width: 240px !important;
        width: 240px !important;
        max-width: 240px !important;
        white-space: normal !important;
        word-break: break-word;
        overflow: hidden;
        text-align: left !important;
        padding-left: 8px;
        padding-right: 8px;
    }

    #enrolled_students_table_wrapper th:nth-child(7),
    #enrolled_students_table_wrapper td:nth-child(7) {
        min-width: 95px !important;
        width: 95px !important;
        max-width: 95px !important;
        white-space: nowrap !important;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #enrolled_students_table_wrapper th:nth-child(8),
    #enrolled_students_table_wrapper td:nth-child(8) {
        min-width: 150px !important;
        width: 150px !important;
    }

    #enrolled_students_table_wrapper th:nth-child(9),
    #enrolled_students_table_wrapper td:nth-child(9) {
        min-width: 150px !important;
        width: 150px !important;
    }

    #enrolled_students_table_wrapper th:nth-child(10),
    #enrolled_students_table_wrapper td:nth-child(10) {
        min-width: 130px !important;
        width: 130px !important;
    }

    #enrolled_students_table_wrapper th:nth-child(11),
    #enrolled_students_table_wrapper td:nth-child(11) {
        min-width: 100px !important;
        width: 100px !important;
    }

    #enrolled_students_table_wrapper .aging-col-school {
        min-width: 240px !important;
        width: 240px !important;
        max-width: 240px !important;
        white-space: normal !important;
        word-break: break-word;
        overflow: hidden;
        text-align: left !important;
        padding-left: 8px;
        padding-right: 8px;
    }

    #enrolled_students_table_wrapper .aging-col-session {
        min-width: 95px !important;
        width: 95px !important;
        max-width: 95px !important;
        white-space: nowrap !important;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #enrolled_students_table_wrapper .dataTables_length,
    #enrolled_students_table_wrapper .dataTables_filter,
    #enrolled_students_table_wrapper .dataTables_info,
    #enrolled_students_table_wrapper .dataTables_paginate {
        position: relative;
        z-index: 10;
        overflow: visible !important;
    }

    .filter-action-wrapper {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .challan-status-wrapper {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        padding-top: 8px;
    }

    .challan-status-label {
        margin-bottom: 0;
        font-weight: 500;
        color: #1f2f6d;
    }

    .challan-status-option {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 0;
        font-weight: 400;
    }

    #aging-export-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 20000;
        background: rgba(15, 23, 42, 0.45);
        align-items: center;
        justify-content: center;
    }

    #aging-export-overlay.is-visible {
        display: flex;
    }

    .aging-export-overlay-card {
        background: #fff;
        border-radius: 8px;
        padding: 24px 28px;
        min-width: 320px;
        max-width: 420px;
        text-align: center;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.2);
    }

    .aging-export-overlay-card .spinner-border {
        width: 2.5rem;
        height: 2.5rem;
        margin-bottom: 14px;
    }

    .aging-export-overlay-message {
        margin: 0;
        color: #1f2f6d;
        font-weight: 500;
    }

    div.dataTables_processing {
        z-index: 1000;
    }

    #scholarship_type + .select2-container {
        width: 100% !important;
    }

    .select2-dropdown {
        z-index: 9999;
    }
</style>

@section('content')
<div class="container-fluid" style="margin-bottom:50px;">
    <div class="card p-4">
        <div class="card-header">
            <h2 class="mb-4 text-left">Aging Report Details</h2>
        </div>

        <div class="card-body">
            <div class="bs-stepper-content">
                <form id="enrolledStudentsFilterForm" onsubmit="return false;">
                    <div id="test-l-1" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger1">
                        <div class="form-row">

                            <div class="form-group col-12 col-lg-3">
                                <label for="session">Session</label>
                                <select class="form-control" id="session" name="session">
                                    <option value="">Select Session</option>
                                    @foreach($sessions as $session)
                                        @if($session->sess_id >= 157)
                                            <option value="{{ $session->sess_id }}">
                                                {{ $session->sess_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12 col-lg-3">
                                <label for="school">School</label>
                                <select class="form-control" id="school" name="school">
                                    <option value="">Select School</option>
                                    @foreach($schools as $school)
                                        <option value="{{ $school->school_id }}">
                                            {{ $school->school_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12 col-lg-3">
                                <label for="fee_type">Fee Type</label>
                                <select class="form-control" id="fee_type" name="fee_type">
                                    <option value="">Select Fee Type</option>
                                        <option value="Full_semester">Full Semester</option>
                                        <option value="Short/Summer_Course">Short / Summer Course </option>
                                        <option value="Credit_hours">Credit Hours</option>
                                        <option value="Hostel_Fee">Hostel Fee</option>
                                        <option value="Fine">Fine</option>
                                        <option value="Convocation_Fee">Convocation Fee</option>
                                        {{-- <option value="shortcourse">Short Course</option> --}}
                                        <option value="misc_fee">Misc Fee</option>
                                </select>
                            </div>

                            <div class="form-group col-12 col-lg-3">
                                <label for="scholarship_type">Scholarship Type</label>
                                <select
                                    class="form-control select2-show-search"
                                    id="scholarship_type"
                                    name="scholarship_type"
                                    data-placeholder="Select Scholarship Type"
                                >
                                    <option value="">Select Scholarship Type</option>
                                    @foreach($scholarshipTypes as $scholarshipType)
                                        <option value="{{ $scholarshipType->scholarship_id }}">
                                            {{ $scholarshipType->scholarship_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12 col-lg-6">
                                <label for="from_date">From Date</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    id="from_date"
                                    name="from_date"
                                    max="{{ $today }}"
                                >
                            </div>

                            <div class="form-group col-12 col-lg-6">
                                <label for="to_date">To Date</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    id="to_date"
                                    name="to_date"
                                    max="{{ $today }}"
                                >
                            </div>

                            <div class="form-group col-12">
                                <div class="filter-action-wrapper">
                                    <button type="button" id="searchBtn" class="btn btn-primary px-4">
                                        Apply Filter
                                    </button>

                                    <button type="button" id="resetBtn" class="btn btn-secondary px-4">
                                        Reset
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body">
            <table id="enrolled_students_table" class="table table-striped table-bordered nowrap" style="width:100%;">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Challan #</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Student Reg</th>
                        <th>School</th>
                        <th>Session</th>
                        <th>Fee Type</th>
                        <th>Receivable Amount</th>
                        <th>Challan Status</th>
                        <th>Aging</th>
                    </tr>
                </thead>

                <tbody>

                </tbody>

                <tfoot>
                    <tr>
                        <th>Student ID</th>
                        <th>Challan #</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Student Reg</th>
                        <th>School</th>
                        <th>Session</th>
                        <th>Fee Type</th>
                        <th>Receivable Amount</th>
                        <th>Challan Status</th>
                        <th>Aging</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div id="aging-export-overlay" aria-hidden="true">
    <div class="aging-export-overlay-card">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="aging-export-overlay-message">Please wait...</p>
    </div>
</div>
@stop

@section('javascript')
<script>
$(document).ready(function () {
    var tableId = '#enrolled_students_table';
    var $exportOverlay = $('#aging-export-overlay');
    var $exportOverlayMessage = $exportOverlay.find('.aging-export-overlay-message');

    $('#scholarship_type').select2({
        placeholder: 'Select Scholarship Type',
        allowClear: true,
        width: '100%'
    });

    function getFilterParams() {
        return {
            _token: "{{ csrf_token() }}",
            from_date: $('#from_date').val(),
            to_date: $('#to_date').val(),
            session: $('#session').val(),
            school: $('#school').val(),
            fee_type: $('#fee_type').val(),
            scholarship_type: $('#scholarship_type').val()
        };
    }

    function showExportOverlay(message) {
        $exportOverlayMessage.text(message || 'Please wait...');
        $exportOverlay.addClass('is-visible').attr('aria-hidden', 'false');
    }

    function hideExportOverlay() {
        $exportOverlay.removeClass('is-visible').attr('aria-hidden', 'true');
    }

    function getExportTableColumns() {
        return [
            { data: 'student_id', name: 'student_id', defaultContent: '-' },
            { data: 'challan_no', name: 'challan_no', defaultContent: '-' },
            { data: 'issue_date', name: 'issue_date', defaultContent: '-' },
            { data: 'due_date', name: 'due_date', defaultContent: '-' },
            { data: 'student_reg', name: 'student_reg', defaultContent: '-' },
            {
                data: 'school',
                name: 'school',
                defaultContent: '-',
                className: 'aging-col-school',
                render: function (data) {
                    if (data === null || data === undefined || data === '') {
                        return '-';
                    }

                    var text = $('<div>').text(data).html();
                    return '<span title="' + text + '">' + text + '</span>';
                }
            },
            { data: 'sess_code', name: 'sess_code', defaultContent: '-', className: 'aging-col-session' },
            { data: 'fee_type', name: 'fee_type', defaultContent: '-' },
            { data: 'receivable_amount', name: 'receivable_amount', defaultContent: '0' },
            { data: 'challan_status', name: 'challan_status', defaultContent: '-' },
            { data: 'aging', name: 'aging', defaultContent: '-' }
        ];
    }

    function stripHtmlCell(value) {
        if (value === null || value === undefined) {
            return '';
        }

        if (typeof value === 'string' && value.indexOf('<') !== -1) {
            var tmp = document.createElement('div');
            tmp.innerHTML = value;
            return tmp.textContent || tmp.innerText || '';
        }

        return String(value);
    }

    function getExportHeaders() {
        return [
            'Student ID',
            'Challan #',
            'Issue Date',
            'Due Date',
            'Student Reg',
            'School',
            'Session',
            'Fee Type',
            'Receivable Amount',
            'Challan Status',
            'Aging'
        ];
    }

    function mapAgingExportRow(row) {
        return [
            stripHtmlCell(row.student_id || '-'),
            stripHtmlCell(row.challan_no || '-'),
            stripHtmlCell(row.issue_date || '-'),
            stripHtmlCell(row.due_date || '-'),
            stripHtmlCell(row.student_reg || '-'),
            stripHtmlCell(row.school || '-'),
            stripHtmlCell(row.sess_code || '-'),
            stripHtmlCell(row.fee_type || '-'),
            stripHtmlCell(row.receivable_amount || '0'),
            stripHtmlCell(row.challan_status || '-'),
            stripHtmlCell(row.aging || '-')
        ];
    }

    function getExportButtonApi(dt, node) {
        if (!dt || !node || typeof dt.button !== 'function') {
            return null;
        }

        try {
            return dt.button(node);
        } catch (error) {
            return null;
        }
    }

    function setExportButtonDisabled(dt, node, disabled) {
        var buttonApi = getExportButtonApi(dt, node);

        if (buttonApi) {
            if (disabled && typeof buttonApi.disable === 'function') {
                buttonApi.disable();
                return;
            }

            if (!disabled && typeof buttonApi.enable === 'function') {
                buttonApi.enable();
                return;
            }
        }

        if (node) {
            $(node).prop('disabled', disabled).toggleClass('disabled', disabled);
        }
    }

    function exportAgingPdf(rows, onComplete) {
        if (typeof pdfMake === 'undefined') {
            throw new Error('PDF library is not available.');
        }

        var headers = getExportHeaders();
        var content = [
            { text: 'Aging Report Details', style: 'title', margin: [0, 0, 0, 8] }
        ];
        var chunkSize = 120;

        for (var i = 0; i < rows.length; i += chunkSize) {
            var chunk = rows.slice(i, i + chunkSize);
            var body = [headers.map(function (title) {
                return { text: title, style: 'tableHeader' };
            })];

            chunk.forEach(function (row) {
                body.push(mapAgingExportRow(row));
            });

            content.push({
                table: {
                    headerRows: 1,
                    widths: Array(headers.length).fill('*'),
                    body: body
                },
                layout: {
                    fillColor: function (rowIndex) {
                        return rowIndex === 0 ? '#eef0fb' : null;
                    }
                },
                pageBreak: i + chunkSize < rows.length ? 'after' : undefined,
                margin: [0, 0, 0, 8]
            });
        }

        var pdfDoc = pdfMake.createPdf({
            pageOrientation: 'landscape',
            pageSize: 'A3',
            pageMargins: [8, 20, 8, 10],
            content: content,
            styles: {
                title: { fontSize: 12, bold: true },
                tableHeader: { fontSize: 6, bold: true, alignment: 'center' }
            },
            defaultStyle: {
                fontSize: 6
            }
        });

        if (typeof pdfDoc.getBlob === 'function') {
            pdfDoc.getBlob(function (blob) {
                try {
                    var link = document.createElement('a');
                    var url = window.URL.createObjectURL(blob);

                    link.href = url;
                    link.download = 'aging-report.pdf';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                } finally {
                    if (typeof onComplete === 'function') {
                        onComplete();
                    }
                }
            });
            return;
        }

        pdfDoc.download('aging-report.pdf');

        if (typeof onComplete === 'function') {
            onComplete();
        }
    }

    function exportAgingPrint(rows) {
        var headers = getExportHeaders();
        var html = '<html><head><title>Aging Report Details</title>';
        html += '<style>body{font-family:Arial,sans-serif;font-size:7pt;} table{width:100%;border-collapse:collapse;} th,td{border:1px solid #94a3b8;padding:2px;word-wrap:break-word;} th{background:#eef0fb;}</style>';
        html += '</head><body><h3>Aging Report Details</h3><table><thead><tr>';
        headers.forEach(function (title) {
            html += '<th>' + $('<div>').text(title).html() + '</th>';
        });
        html += '</tr></thead><tbody>';

        rows.forEach(function (row) {
            html += '<tr>';
            mapAgingExportRow(row).forEach(function (cell) {
                html += '<td>' + $('<div>').text(cell).html() + '</td>';
            });
            html += '</tr>';
        });

        html += '</tbody></table></body></html>';

        var printWindow = window.open('', '_blank');
        printWindow.document.open();
        printWindow.document.write(html);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    }

    function fetchAllAgingRecords() {
        return $.ajax({
            url: "{{ route('aging.report.export.data') }}",
            type: 'POST',
            timeout: 300000,
            data: $.extend({}, getFilterParams(), {
                search: table.search()
            })
        });
    }

    function finishClientExport(dt, node) {
        hideExportOverlay();
        setExportButtonDisabled(dt, node, false);
    }

    function runClientExport(extend, e, dt, node) {
        var totalRecords = dt.page.info().recordsDisplay || 0;

        if (totalRecords < 1) {
            alert('No records available to export.');
            return;
        }

        showExportOverlay('Fetching all ' + totalRecords.toLocaleString() + ' records for export. Please wait...');
        setExportButtonDisabled(dt, node, true);

        fetchAllAgingRecords()
            .done(function (response) {
                if (!response || response.status === false) {
                    alert((response && response.message) ? response.message : 'Unable to fetch records for export.');
                    finishClientExport(dt, node);
                    return;
                }

                var rows = response.data || [];
                if (!rows.length) {
                    alert('No records available to export.');
                    finishClientExport(dt, node);
                    return;
                }

                showExportOverlay('Generating ' + extend.replace('Html5', '') + ' for ' + rows.length.toLocaleString() + ' records. Please wait...');

                window.setTimeout(function () {
                    try {
                        if (extend === 'pdfHtml5') {
                            exportAgingPdf(rows, function () {
                                finishClientExport(dt, node);
                            });
                            return;
                        }

                        if (extend === 'print') {
                            exportAgingPrint(rows);
                        }
                    } catch (error) {
                        console.error(error);
                        alert('Export failed. Please try again.');
                    }

                    if (extend !== 'pdfHtml5') {
                        finishClientExport(dt, node);
                    }
                }, 100);
            })
            .fail(function (xhr) {
                console.error(xhr.responseText || xhr.statusText);
                alert('Unable to fetch records for export. Please try again.');
                finishClientExport(dt, node);
            });
    }

    function runServerExcelExport() {
        showExportOverlay('Preparing Excel download. This may take a while for large datasets...');

        var formData = new FormData();
        var params = getFilterParams();

        Object.keys(params).forEach(function (key) {
            formData.append(key, params[key] || '');
        });

        fetch("{{ route('aging.report.export') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'text/csv'
            },
            body: formData
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Export failed');
                }

                var disposition = response.headers.get('Content-Disposition') || '';
                var filename = 'aging-report.csv';
                var match = disposition.match(/filename="?([^"]+)"?/i);

                if (match && match[1]) {
                    filename = match[1];
                }

                return response.blob().then(function (blob) {
                    return {
                        blob: blob,
                        filename: filename
                    };
                });
            })
            .then(function (result) {
                var link = document.createElement('a');
                var url = window.URL.createObjectURL(result.blob);

                link.href = url;
                link.download = result.filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
            })
            .catch(function () {
                alert('Excel export failed. Please try again.');
            })
            .finally(function () {
                hideExportOverlay();
            });
    }

    if ($.fn.DataTable.isDataTable(tableId)) {
        $(tableId).DataTable().clear().destroy();
    }

    var table = $(tableId).DataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        scrollX: true,
        autoWidth: false,

        paging: true,
        pageLength: 10,
        lengthChange: true,
        lengthMenu: [
            [10, 25, 50, 100],
            [10, 25, 50, 100]
        ],

        searching: true,
        ordering: true,
        order: [[2, 'desc']],
        info: true,

        dom:
            "<'row mb-2'<'col-md-6'B><'col-md-6'f>>" +
            "<'row mb-2'<'col-md-6'l><'col-md-6'>>" +
            "<'row'<'col-md-12'tr>>" +
            "<'row mt-3'<'col-md-6'i><'col-md-6'p>>",

        ajax: {
            url: "{{ route('aging.report.data') }}",
            type: "POST",
            data: function (d) {
                return $.extend({}, d, getFilterParams());
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                alert('Aging report data load nahi ho raha.');
            }
        },

        columns: getExportTableColumns(),

        buttons: [
            {
                text: 'Excel',
                action: function () {
                    runServerExcelExport();
                }
            },
            {
                text: 'PDF',
                action: function (e, dt, node) {
                    runClientExport('pdfHtml5', e, dt, node);
                }
            },
            {
                text: 'Print',
                action: function (e, dt, node) {
                    runClientExport('print', e, dt, node);
                }
            }
        ],

        language: {
            processing: 'Loading records...',
            search: 'Search:',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'Showing 0 to 0 of 0 entries',
            infoFiltered: '(filtered from _MAX_ total entries)',
            emptyTable: 'No challan records found',
            zeroRecords: 'No matching challan records found',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }
        },

        drawCallback: function () {
            setTimeout(function () {
                table.columns.adjust();
            }, 100);
        }
    });

    $('#searchBtn').on('click', function () {
        var $btn = $(this);

        $btn.prop('disabled', true).text('Applying...');
        $('#resetBtn').prop('disabled', true);

        table.ajax.reload(function () {
            $btn.prop('disabled', false).text('Apply Filter');
            $('#resetBtn').prop('disabled', false);
        });
    });

    $('#resetBtn').on('click', function () {
        $('#enrolledStudentsFilterForm')[0].reset();
        $('#scholarship_type').val('').trigger('change');

        table.search('');
        table.columns().search('');
        table.ajax.reload();
    });

    $(window).on('resize', function () {
        table.columns.adjust();
    });
});
</script>
@stop
