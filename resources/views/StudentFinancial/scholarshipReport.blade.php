@extends('layouts.app')

@section('title')
  BNU CMS
@stop

<style>
    .scholarship-report-page {
        --brand: #4454c3;
        --brand-light: #eef0fb;
        --brand-lighter: #f5f6fd;
        --surface: #ffffff;
        --surface-2: #f8f9fc;
        --border: #e4e7f0;
        --text-h: #111827;
        --text-body: #374151;
        --text-muted: #6b7280;
    }

    .scholarship-report-page .card {
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
    }

    .scholarship-report-page h3 {
        font-weight: 700;
        color: var(--text-h);
        border-left: 4px solid var(--brand);
        padding-left: 0.75rem;
    }

    #school-scholarship-table-container {
        position: relative;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1rem;
    }

    #school-scholarship-table_wrapper {
        padding: 0;
    }

    #school-scholarship-table_wrapper .dataTables_scrollHead,
    #school-scholarship-table_wrapper .dataTables_scrollBody {
        width: 100% !important;
    }

    #school-scholarship-table_wrapper .dataTables_scrollBody {
        overflow-x: auto !important;
        max-height: 65vh;
        border: none !important;
    }

    #school-scholarship-table_wrapper .dataTables_scrollHead thead th,
    #school-scholarship-table thead th {
        background: var(--brand) !important;
        color: #fff !important;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        white-space: nowrap;
        padding: 0.8rem 0.75rem !important;
        border: none !important;
        border-right: 1px solid rgba(255, 255, 255, 0.3) !important;
        border-bottom: 1px solid #3a48a8 !important;
        vertical-align: middle;
        text-align: center !important;
    }

    #school-scholarship-table_wrapper .dataTables_scrollHead thead th:last-child,
    #school-scholarship-table thead th:last-child {
        border-right: none !important;
    }

    #school-scholarship-table_wrapper tbody td {
        padding: 0.75rem 0.75rem !important;
        font-size: 0.9rem;
        color: var(--text-body);
        border: none !important;
        border-right: 1px solid var(--border) !important;
        border-bottom: 1px solid var(--border) !important;
        vertical-align: middle;
        text-align: center !important;
        white-space: nowrap;
    }

    #school-scholarship-table_wrapper tbody td:last-child {
        border-right: none !important;
    }

    #school-scholarship-table_wrapper tbody tr.sch-row-even td {
        background-color: var(--surface);
    }

    #school-scholarship-table_wrapper tbody tr.sch-row-odd td {
        background-color: var(--surface-2);
    }

    #school-scholarship-table_wrapper tbody tr:hover td {
        background-color: var(--brand-light) !important;
    }

    #school-scholarship-table_wrapper tbody td:first-child {
        font-weight: 600;
        color: var(--text-h);
        max-width: 240px;
        white-space: normal;
        text-align: center !important;
    }

    #school-scholarship-table_wrapper .dt-buttons .dt-button {
        background: var(--brand) !important;
        color: #fff !important;
        border: none !important;
        border-radius: 6px !important;
        padding: 0.4rem 0.85rem !important;
        font-size: 0.8rem;
        margin-right: 0.35rem;
    }

    #school-scholarship-table_wrapper .dataTables_length,
    #school-scholarship-table_wrapper .dataTables_filter,
    #school-scholarship-table_wrapper .dataTables_info,
    #school-scholarship-table_wrapper .dataTables_paginate {
        font-size: 0.82rem;
        color: var(--text-muted);
    }

    .school-scholarship-scroll-top {
        overflow-x: auto;
        overflow-y: hidden;
        width: 100%;
        margin-bottom: 0;
        background: #fff;
        border-bottom: 1px solid var(--border);
    }

    .school-scholarship-scroll-top-inner {
        height: 1px;
    }

    .report-mode-switch {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px;
        background: var(--brand-lighter);
        border: 1px solid var(--border);
        border-radius: 999px;
    }

    .report-mode-switch input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }

    .report-mode-switch label {
        margin: 0;
        cursor: pointer;
        padding: 0.45rem 1.1rem;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: background-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
    }

    .report-mode-switch label:hover {
        color: var(--brand);
    }

    .report-mode-switch input:checked + label {
        background: var(--brand);
        color: #fff;
        box-shadow: 0 1px 3px rgba(68, 84, 195, 0.4);
    }

    .report-mode-switch input:checked + label:hover {
        color: #fff;
    }

    .report-mode-switch input:focus-visible + label {
        outline: 2px solid var(--brand);
        outline-offset: 2px;
    }

    #scholarship-detail-table thead th {
        background: var(--brand) !important;
        color: #fff !important;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        padding: 0.8rem 0.6rem !important;
        border: none !important;
        border-right: 1px solid rgba(255, 255, 255, 0.3) !important;
        vertical-align: middle;
        text-align: center !important;
    }

    #scholarship-detail-table tbody td {
        padding: 0.6rem 0.6rem !important;
        font-size: 0.9rem;
        color: var(--text-body);
        border-right: 1px solid var(--border) !important;
        border-bottom: 1px solid var(--border) !important;
        vertical-align: middle;
        text-align: center !important;
    }

    #scholarship-detail-table_wrapper .dt-buttons .dt-button {
        background: var(--brand) !important;
        color: #fff !important;
        border: none !important;
        border-radius: 6px !important;
        padding: 0.4rem 0.85rem !important;
        font-size: 0.8rem;
        margin-right: 0.35rem;
    }

    #scholarship-detail-table_wrapper .dataTables_length,
    #scholarship-detail-table_wrapper .dataTables_filter,
    #scholarship-detail-table_wrapper .dataTables_info,
    #scholarship-detail-table_wrapper .dataTables_paginate {
        font-size: 0.82rem;
        color: var(--text-muted);
    }
</style>

@section('content')
<div class="container-fluid scholarship-report-page">
    <div class="card p-4 pt-4">
        <div class="row mb-3 align-items-center">
            <div class="col-12 col-md-5">
                <h3 class="mb-2 mb-md-0">Scholarship Report</h3>
            </div>
            <div class="col-12 col-md-7 d-flex justify-content-md-end">
                <div class="report-mode-switch" id="scholarship-report-toggle">
                    <input type="radio" id="mode-school" name="scholarship_report_mode" value="school" checked>
                    <label for="mode-school"><i class="fa fa-university"></i> Schoolwise Report</label>

                    <input type="radio" id="mode-detail" name="scholarship_report_mode" value="detail">
                    <label for="mode-detail"><i class="fa fa-graduation-cap"></i> Detail with CGPA</label>
                </div>
            </div>
        </div>

        <form id="scholarshipReportFilterForm" onsubmit="return false;">
            <div class="row">
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="session_sel">Session</label>
                    <select name="session" id="session_sel" class="form-control">
                        @foreach ($sessions as $session)
                            <option value="{{ $session->sess_id }}" {{ $session->sess_id == 165 ? 'selected' : '' }}>
                                {{ $session->sess_code }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="school_sel">School</label>
                    <select name="school" id="school_sel" class="form-control">
                        <option value="">Select School</option>
                        @foreach ($schools as $school)
                            <option value="{{ $school->school_id }}">{{ $school->school_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="scholarship_type_sel">Scholarship Type</label>
                    <select name="scholarship_type" id="scholarship_type_sel" class="form-control">
                        <option value="">Select Scholarship Type</option>
                        @foreach ($scholarshipTypes as $scholarshipType)
                            <option value="{{ $scholarshipType->scholarship_id }}">
                                {{ $scholarshipType->scholarship_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="eff-from">From Date</label>
                    <input type="date" name="effective_from" id="eff-from" class="form-control">
                </div>

                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="eff-to">To Date</label>
                    <input type="date" name="effective_to" id="eff-to" class="form-control">
                </div>

                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="fee_type_sel">Fee Type</label>
                    <select name="fee_type" id="fee_type_sel" class="form-control">
                        <option value="">Select Fee Type</option>
                        <option value="Full_semester">Full Semester</option>
                        <option value="Short/Summer_Course">Short/Summer Course</option>
                        <option value="Credit_hours">Credit hours</option>
                        <option value="Hostel_Fee">Hostel Fee</option>
                    </select>
                </div>

                <div class="form-group col-12">
                    <button type="button" id="searchBtn" class="btn btn-info">
                        <i class="fa fa-filter"></i> Apply Filter
                    </button>
                </div>
            </div>
        </form>

        <div id="error_message" class="alert alert-danger d-none"></div>
        <div id="loading_message" class="alert alert-info">Loading scholarship report...</div>

        <div id="school-scholarship-table-container" class="mt-3">
            <div id="school-scholarship-scroll-top" class="school-scholarship-scroll-top d-none">
                <div id="school-scholarship-scroll-top-inner" class="school-scholarship-scroll-top-inner"></div>
            </div>
            <table id="school-scholarship-table" class="table scholarship-report-table" style="width:100%">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>

        <div id="scholarship-detail-table-container" class="mt-3 d-none">
            <table id="scholarship-detail-table" class="table scholarship-report-table" style="width:100%">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('javascript')
<script>
    var schoolScholarshipTable = null;
    var scholarshipDetailTable = null;
    var currentReportMode = 'school';

    function setReportLoading(message) {
        $('#error_message').addClass('d-none').html('');
        $('#loading_message')
            .removeClass('d-none')
            .html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>' +
                (message || 'Loading...'));
        $('#searchBtn')
            .prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> Loading...');
    }

    function clearReportLoading() {
        $('#loading_message').addClass('d-none').html('');
        $('#searchBtn').prop('disabled', false).html('<i class="fa fa-filter"></i> Apply Filter');
    }

    function showReportError(message) {
        $('#loading_message').addClass('d-none').html('');
        $('#error_message')
            .removeClass('d-none')
            .html('<i class="fa fa-exclamation-triangle mr-2"></i>' +
                (message || 'Something went wrong. Please try again.'));
    }

    function getRowValue(row, key) {
        if (!row) {
            return null;
        }

        if (row[key] !== undefined && row[key] !== null) {
            return row[key];
        }

        var upperKey = key.toUpperCase();
        if (row[upperKey] !== undefined && row[upperKey] !== null) {
            return row[upperKey];
        }

        var lowerKey = key.toLowerCase();
        if (row[lowerKey] !== undefined && row[lowerKey] !== null) {
            return row[lowerKey];
        }

        return null;
    }

    function formatNumber(value) {
        return Number(value || 0).toLocaleString('en-US');
    }

    function stripHtml(value) {
        if (value === null || value === undefined) {
            return '';
        }
        if (typeof value !== 'string' || value.indexOf('<') === -1) {
            return value;
        }
        var tmp = document.createElement('div');
        tmp.innerHTML = value;
        return tmp.textContent || tmp.innerText || '';
    }

    function renderTextCell(row, key, fallback) {
        fallback = fallback || 'NA';
        return function(data, type) {
            var value = getRowValue(row, key);
            var text = (value !== null && value !== undefined && value !== '') ? value : fallback;
            if (type === 'export' || type === 'print' || type === 'pdf') {
                return text;
            }
            return text;
        };
    }

    function renderNumberCell(row, key) {
        return function(data, type) {
            var value = Number(getRowValue(row, key) || 0);
            if (type === 'export' || type === 'sort' || type === 'filter') {
                return value;
            }
            if (type === 'print' || type === 'pdf') {
                return formatNumber(value);
            }
            return formatNumber(value);
        };
    }

    function getExportOptions(columnCount) {
        var columnIndexes = [];
        for (var i = 0; i < columnCount; i++) {
            columnIndexes.push(i);
        }

        return {
            columns: columnIndexes,
            modifier: {
                page: 'all',
                search: 'applied'
            },
            format: {
                body: function(data) {
                    return stripHtml(data);
                },
                header: function(data) {
                    return stripHtml(data);
                }
            }
        };
    }

    function findPdfTableBlock(doc) {
        if (!doc.content) {
            return null;
        }

        for (var i = 0; i < doc.content.length; i++) {
            if (doc.content[i].table) {
                return doc.content[i];
            }
        }

        return null;
    }

    function normalizePdfCell(cell, fontSize, isHeader) {
        var text = (cell && typeof cell === 'object') ? (cell.text !== undefined ? cell.text : '') : (cell || '');
        text = stripHtml(String(text));

        return {
            text: text,
            fontSize: fontSize,
            alignment: 'center',
            noWrap: false,
            fillColor: isHeader ? '#4454c3' : null,
            color: isHeader ? '#ffffff' : '#374151',
            bold: !!isHeader,
            margin: [1, 2, 1, 2]
        };
    }

    function buildPdfGridLayout() {
        return {
            hLineWidth: function() { return 0.5; },
            vLineWidth: function() { return 0.5; },
            hLineColor: function() { return '#c5cad6'; },
            vLineColor: function() { return '#c5cad6'; },
            paddingLeft: function() { return 2; },
            paddingRight: function() { return 2; },
            paddingTop: function() { return 2; },
            paddingBottom: function() { return 2; }
        };
    }

    function pickPdfColumns(row, indexes) {
        return indexes.map(function(colIndex) {
            return row[colIndex];
        });
    }

    function customizePdfDoc(doc) {
        var tableBlock = findPdfTableBlock(doc);
        if (!tableBlock || !tableBlock.table || !tableBlock.table.body || !tableBlock.table.body.length) {
            return;
        }

        var body = tableBlock.table.body;
        var headerRow = body[0];
        var dataRows = body.slice(1);
        var columnCount = headerRow.length;
        var fixedColumnCount = 5;
        var scholarshipChunkSize = 16;
        var gridLayout = buildPdfGridLayout();

        doc.defaultStyle.fontSize = 6;
        doc.defaultStyle.alignment = 'center';
        doc.styles.tableHeader.fontSize = 6;
        doc.styles.tableHeader.alignment = 'center';
        doc.styles.tableHeader.fillColor = '#4454c3';
        doc.pageMargins = [8, 22, 8, 14];
        doc.pageOrientation = 'landscape';

        function formatTableBlock(targetBlock, headerFontSize, bodyFontSize) {
            var table = targetBlock.table;
            var cols = table.body[0].length;

            table.widths = Array(cols).fill('*');
            table.body = table.body.map(function(row, rowIndex) {
                var isHeader = rowIndex === 0;
                var fontSize = isHeader ? headerFontSize : bodyFontSize;

                return row.map(function(cell) {
                    return normalizePdfCell(cell, fontSize, isHeader);
                });
            });

            targetBlock.layout = gridLayout;

            return Math.max(1190, (cols * 22) + 40);
        }

        var pdfPageWidth = 1190;

        if (columnCount <= 28) {
            var fontSize = columnCount > 20 ? 5 : 6;
            pdfPageWidth = formatTableBlock(tableBlock, fontSize, fontSize);
            doc.pageSize = { width: pdfPageWidth, height: 842 };
            return;
        }

        var titleBlock = doc.content[0];
        doc.content = [titleBlock];

        var chunkStarts = [];
        for (var start = fixedColumnCount; start < columnCount; start += scholarshipChunkSize) {
            chunkStarts.push(start);
        }

        chunkStarts.forEach(function(chunkStart, chunkIndex) {
            var chunkEnd = Math.min(chunkStart + scholarshipChunkSize, columnCount);
            var columnIndexes = [];

            for (var i = 0; i < fixedColumnCount; i++) {
                columnIndexes.push(i);
            }
            for (var j = chunkStart; j < chunkEnd; j++) {
                columnIndexes.push(j);
            }

            if (chunkIndex > 0) {
                doc.content.push({
                    text: 'Scholarship columns (continued)',
                    fontSize: 9,
                    bold: true,
                    margin: [0, 14, 0, 6],
                    alignment: 'left'
                });
            }

            var chunkBody = [];
            chunkBody.push(pickPdfColumns(headerRow, columnIndexes));
            dataRows.forEach(function(row) {
                chunkBody.push(pickPdfColumns(row, columnIndexes));
            });

            doc.content.push({
                table: {
                    headerRows: 1,
                    widths: Array(columnIndexes.length).fill('*'),
                    body: chunkBody
                },
                layout: gridLayout,
                margin: [0, chunkIndex === 0 ? 0 : 0, 0, 10]
            });

            pdfPageWidth = Math.max(
                pdfPageWidth,
                formatTableBlock(doc.content[doc.content.length - 1], 6, 5.5)
            );
        });

        doc.pageSize = { width: pdfPageWidth, height: 842 };
    }

    function customizePrintWindow(win) {
        var $body = $(win.document.body);
        $body.css('font-family', 'Arial, sans-serif');

        $body.find('h1').css({
            'font-size': '18px',
            'margin-bottom': '14px',
            'color': '#111827',
            'text-align': 'center'
        });

        $body.find('table').css({
            'width': '100%',
            'border-collapse': 'collapse',
            'font-size': '10px'
        });

        $body.find('table th').css({
            'background-color': '#4454c3',
            'color': '#fff',
            'padding': '6px 5px',
            'border': '1px solid #3a48a8',
            'white-space': 'nowrap',
            'text-align': 'center',
            'font-size': '10px'
        });

        $body.find('table td').css({
            'padding': '6px 5px',
            'border': '1px solid #c5cad6',
            'white-space': 'nowrap',
            'text-align': 'center',
            'font-size': '10px'
        });

        $body.find('table tr:nth-child(even) td').css('background-color', '#f8f9fc');
    }

    function destroySchoolScholarshipTable() {
        if ($.fn.DataTable.isDataTable('#school-scholarship-table')) {
            $('#school-scholarship-table').DataTable().clear().destroy();
        }
        schoolScholarshipTable = null;
        $('#school-scholarship-table thead').empty();
        $('#school-scholarship-table tbody').empty();
        $('#school-scholarship-scroll-top').addClass('d-none');
        $('#school-scholarship-scroll-top-inner').width(0);
        $(window).off('resize.scholarshipHScroll');
        $('#school-scholarship-scroll-top').off('.hScrollSync');
        $('#school-scholarship-table_wrapper .dataTables_scrollBody').off('.hScrollSync');
    }

    function setupTopHorizontalScroll() {
        var $wrapper = $('#school-scholarship-table_wrapper');
        var $scrollBody = $wrapper.find('.dataTables_scrollBody');
        var $scrollTop = $('#school-scholarship-scroll-top');
        var $scrollTopInner = $('#school-scholarship-scroll-top-inner');

        if (!$scrollBody.length) {
            return;
        }

        $scrollTop.insertBefore($wrapper.find('.dataTables_scroll')).removeClass('d-none');

        function updateScrollWidth() {
            var scrollWidth = $scrollBody.get(0).scrollWidth;
            $scrollTopInner.width(scrollWidth);
        }

        $scrollTop.off('.hScrollSync');
        $scrollBody.off('.hScrollSync');

        $scrollTop.on('scroll.hScrollSync', function() {
            var scrollLeft = $scrollTop.scrollLeft();
            $scrollBody.scrollLeft(scrollLeft);
            $wrapper.find('.dataTables_scrollHead').scrollLeft(scrollLeft);
        });

        $scrollBody.on('scroll.hScrollSync', function() {
            $scrollTop.scrollLeft($scrollBody.scrollLeft());
        });

        updateScrollWidth();
        $(window).off('resize.scholarshipHScroll').on('resize.scholarshipHScroll', function() {
            updateScrollWidth();
            if (schoolScholarshipTable) {
                schoolScholarshipTable.columns.adjust();
            }
        });
    }

    function buildTableColumns(scholarshipColumns) {
        var columns = [
            {
                data: null,
                title: 'School',
                render: function(data, type, row) {
                    return renderTextCell(row, 'school_name', 'NA')(data, type, row);
                }
            },
            {
                data: null,
                title: 'School Code',
                render: function(data, type, row) {
                    return renderTextCell(row, 'school_code', 'NA')(data, type, row);
                }
            },
            {
                data: null,
                title: 'Scholarships Applied',
                render: function(data, type, row) {
                    return renderNumberCell(row, 'total_scholarships_applied')(data, type, row);
                }
            },
            {
                data: null,
                title: 'Distinct Students',
                render: function(data, type, row) {
                    return renderNumberCell(row, 'unique_scholarship_students')(data, type, row);
                }
            },
            {
                data: null,
                title: 'Scholarship Amount',
                render: function(data, type, row) {
                    return renderNumberCell(row, 'total_scholarship_amount')(data, type, row);
                }
            }
        ];

        scholarshipColumns.forEach(function(scholarship) {
            columns.push({
                data: null,
                title: scholarship.name,
                defaultContent: '0',
                render: function(data, type, row) {
                    return renderNumberCell(row, scholarship.key)(data, type, row);
                }
            });
        });

        return columns;
    }

    function initSchoolScholarshipTable(tableData, scholarshipColumns) {
        var columns = buildTableColumns(scholarshipColumns);
        var exportOptions = getExportOptions(columns.length);

        schoolScholarshipTable = $('#school-scholarship-table').DataTable({
            data: tableData,
            columns: columns,
            scrollX: true,
            scrollCollapse: true,
            autoWidth: false,
            paging: true,
            pageLength: 25,
            order: [[1, 'asc']],
            dom: 'Blfrtip',
            columnDefs: [
                { className: 'text-center align-middle', targets: '_all' }
            ],
            rowCallback: function(row, data, index) {
                $(row).removeClass('sch-row-even sch-row-odd');
                $(row).addClass(index % 2 === 0 ? 'sch-row-even' : 'sch-row-odd');
            },
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'School Scholarship Report',
                    exportOptions: exportOptions
                },
                {
                    extend: 'pdfHtml5',
                    title: 'School Scholarship Report',
                    orientation: 'landscape',
                    pageSize: 'A3',
                    exportOptions: exportOptions,
                    customize: customizePdfDoc,
                    download: 'open'
                },
                {
                    extend: 'print',
                    title: 'School Scholarship Report',
                    exportOptions: exportOptions,
                    customize: customizePrintWindow
                }
            ],
            initComplete: function() {
                var api = this.api();
                api.columns.adjust();
                setupTopHorizontalScroll();
                setTimeout(function() {
                    api.columns.adjust().draw(false);
                    setupTopHorizontalScroll();
                }, 100);
            }
        });
    }

    function getFilterParams() {
        return {
            session: $('#session_sel').val() || '165',
            school: $('#school_sel').val(),
            effective_from: $('#eff-from').val(),
            effective_to: $('#eff-to').val(),
            fee_type: $('#fee_type_sel').val(),
            scholarship_type: $('#scholarship_type_sel').val()
        };
    }

    function loadSchoolScholarshipReport() {
        var fromDate = $('#eff-from').val();
        var toDate = $('#eff-to').val();
        if (fromDate && toDate && fromDate > toDate) {
            showReportError('From Date cannot be later than To Date.');
            return;
        }

        setReportLoading('Loading schoolwise scholarship report...');
        destroySchoolScholarshipTable();

        $.ajax({
            url: '/get-school-scholarship-report',
            method: 'GET',
            data: getFilterParams(),
            dataType: 'json',
            success: function(response) {
                if (!response.success) {
                    showReportError('Unable to load scholarship report.');
                    return;
                }

                var scholarshipColumns = response.scholarship_columns || [];
                var tableData = response.data || [];

                if (!scholarshipColumns.length) {
                    showReportError('No active scholarship types found for the selected filters.');
                    return;
                }

                initSchoolScholarshipTable(tableData, scholarshipColumns);

                if (!tableData.length) {
                    showReportError('No records found for the selected filters.');
                }
            },
            error: function(xhr) {
                showReportError('Failed to load scholarship report data.' +
                    (xhr && xhr.status ? ' (Error ' + xhr.status + ')' : ''));
            },
            complete: function() {
                clearReportLoading();
            }
        });
    }

    /* ----------------------------------------------------------------------
     | Scholarship Detail report (with CGPA) — independent table + exports
     | ---------------------------------------------------------------------- */

    function destroyScholarshipDetailTable() {
        if ($.fn.DataTable.isDataTable('#scholarship-detail-table')) {
            $('#scholarship-detail-table').DataTable().clear().destroy();
        }
        scholarshipDetailTable = null;
        $('#scholarship-detail-table thead').empty();
        $('#scholarship-detail-table tbody').empty();
    }

    function customizeDetailPdfDoc(doc) {
        doc.defaultStyle.fontSize = 8;
        doc.defaultStyle.alignment = 'center';
        doc.styles.tableHeader.fontSize = 8;
        doc.styles.tableHeader.alignment = 'center';
        doc.styles.tableHeader.fillColor = '#4454c3';
        doc.styles.tableHeader.color = '#ffffff';
        doc.pageMargins = [12, 18, 12, 14];

        var tableBlock = findPdfTableBlock(doc);
        if (tableBlock && tableBlock.table && tableBlock.table.body.length) {
            tableBlock.table.widths = Array(tableBlock.table.body[0].length).fill('*');
        }
    }

    function buildDetailTableColumns() {
        return [
            { data: 'student_id', title: 'Student ID', defaultContent: '-' },
            { data: 'student_name', title: 'Student Name', defaultContent: '-' },
            { data: 'school', title: 'School', defaultContent: '-' },
            { data: 'scholarship_session', title: 'Scholarship Session', defaultContent: '-' },
            { data: 'scholarship_type', title: 'Scholarship Type', defaultContent: '-' },
            {
                data: 'scholarship_percentage',
                title: 'Scholarship %age',
                defaultContent: '-',
                render: function(data, type) {
                    if (data === null || data === undefined || data === '') {
                        return type === 'export' ? '' : '-';
                    }
                    return data + ' %';
                }
            },
            {
                data: 'scholarship_amount',
                title: 'Scholarship Amount',
                defaultContent: '0',
                render: function(data, type) {
                    var value = Number(data || 0);
                    if (type === 'export' || type === 'sort' || type === 'filter') {
                        return value;
                    }
                    return formatNumber(value);
                }
            },
            { data: 'cgpa_session', title: 'CGPA Session', defaultContent: '-' },
            { data: 'cgpa_awarded', title: 'CGPA % on which Sch is awarded', defaultContent: '-' }
        ];
    }

    function initScholarshipDetailTable(tableData) {
        var columns = buildDetailTableColumns();
        var exportOptions = getExportOptions(columns.length);

        scholarshipDetailTable = $('#scholarship-detail-table').DataTable({
            data: tableData,
            columns: columns,
            autoWidth: false,
            paging: true,
            pageLength: 25,
            order: [[2, 'asc'], [1, 'asc']],
            dom: 'Blfrtip',
            columnDefs: [
                { className: 'text-center align-middle', targets: '_all' }
            ],
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Scholarship Detail Report',
                    exportOptions: exportOptions
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Scholarship Detail Report',
                    orientation: 'landscape',
                    pageSize: 'A3',
                    exportOptions: exportOptions,
                    customize: customizeDetailPdfDoc
                },
                {
                    extend: 'print',
                    title: 'Scholarship Detail Report',
                    exportOptions: exportOptions,
                    customize: customizePrintWindow
                }
            ]
        });
    }

    function loadScholarshipDetailReport() {
        var fromDate = $('#eff-from').val();
        var toDate = $('#eff-to').val();
        if (fromDate && toDate && fromDate > toDate) {
            showReportError('From Date cannot be later than To Date.');
            return;
        }

        setReportLoading('Loading scholarship detail report (with CGPA). This can take a little longer, please wait...');
        destroyScholarshipDetailTable();

        $.ajax({
            url: '/get-scholarship-detail-report',
            method: 'GET',
            data: getFilterParams(),
            dataType: 'json',
            timeout: 300000,
            success: function(response) {
                if (!response.success) {
                    showReportError('Unable to load scholarship detail report.');
                    return;
                }

                var tableData = response.data || [];
                initScholarshipDetailTable(tableData);

                if (!tableData.length) {
                    showReportError('No records found for the selected filters.');
                }
            },
            error: function(xhr, textStatus) {
                if (textStatus === 'timeout') {
                    showReportError('The report took too long to respond and timed out. Try narrowing the filters (e.g. select a School) and apply again.');
                } else {
                    showReportError('Failed to load scholarship detail report data.' +
                        (xhr && xhr.status ? ' (Error ' + xhr.status + ')' : ''));
                }
            },
            complete: function() {
                clearReportLoading();
            }
        });
    }

    function runActiveScholarshipReport() {
        if (currentReportMode === 'detail') {
            loadScholarshipDetailReport();
        } else {
            loadSchoolScholarshipReport();
        }
    }

    $(document).ready(function() {
        runActiveScholarshipReport();

        $('#searchBtn').on('click', function() {
            runActiveScholarshipReport();
        });

        $('#scholarship-report-toggle').on('change', 'input[name="scholarship_report_mode"]', function() {
            currentReportMode = $(this).val();

            if (currentReportMode === 'detail') {
                $('#school-scholarship-table-container').addClass('d-none');
                $('#scholarship-detail-table-container').removeClass('d-none');
                destroySchoolScholarshipTable();
            } else {
                $('#scholarship-detail-table-container').addClass('d-none');
                $('#school-scholarship-table-container').removeClass('d-none');
                destroyScholarshipDetailTable();
            }

            runActiveScholarshipReport();
        });

        $(window).on('resize', function() {
            if (currentReportMode === 'school' && schoolScholarshipTable) {
                schoolScholarshipTable.columns.adjust();
                setupTopHorizontalScroll();
            } else if (currentReportMode === 'detail' && scholarshipDetailTable) {
                scholarshipDetailTable.columns.adjust();
            }
        });
    });
</script>
@stop
