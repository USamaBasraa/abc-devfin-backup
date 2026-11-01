@extends('layouts.app')

@php
    $today = date('Y-m-d');
@endphp

<style>
    /* Shared report theme (aligned with medical_premium_report & challan_summary_report) */
    .fee-global-summary-page {
        --brand: #4454c3;
        --brand-dark: #2f3d9e;
        --brand-mid: #6270d4;
        --brand-light: #eef0fb;
        --brand-lighter: #f5f6fd;
        --brand-glow: rgba(68, 84, 195, 0.15);
        --brand-glow-md: rgba(68, 84, 195, 0.25);
        --brand-footer: rgba(142, 152, 219, 0.42);
        --accent-blue: #25a4fe;
        --accent-blue-dark: #1b82cb;
        --surface: #ffffff;
        --surface-2: #f8f9fc;
        --border: #e4e7f0;
        --text-h: #111827;
        --text-body: #374151;
        --text-muted: #6b7280;
        --r-sm: 6px;
        --r-md: 10px;
        --r-lg: 14px;
        --ease: cubic-bezier(.4, 0, .2, 1);
    }

    .fee-global-summary-page .card {
        border: 1px solid var(--border);
        border-radius: var(--r-lg);
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .fee-global-summary-page .card-header {
        background: var(--brand-lighter);
        border-bottom: 1px solid var(--border);
        padding: 1.1rem 1.5rem;
    }

    .fee-global-summary-page .card-header h2 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-h);
        border-left: 4px solid var(--brand);
        padding-left: 0.75rem;
    }

    .fee-summary-table-section {
        margin-top: 0.25rem;
    }

    .fee-summary-table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 0;
        padding: 0.85rem 1.25rem;
        background: var(--brand-lighter);
        border-bottom: 1px solid var(--border);
    }

    .fee-summary-table-title {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-h);
    }

    .fee-summary-table-subtitle {
        margin: 0.15rem 0 0;
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .fee-summary-table-container {
        background: var(--surface);
    }

    #fee_global_summary_table_wrapper {
        width: 100%;
        padding: 1rem 1.25rem 0.85rem;
    }

    #fee_global_summary_table_wrapper .dataTables_scrollHead,
    #fee_global_summary_table_wrapper .dataTables_scrollBody,
    #fee_global_summary_table_wrapper .dataTables_scrollFoot {
        width: 100% !important;
    }

    #fee_global_summary_table_wrapper .dataTables_scrollBody {
        overflow-x: auto !important;
        border: none !important;
    }

    /* scrollX clones footer into .dataTables_scrollFoot — hide the source tfoot to avoid duplicate Total row */
    #fee_global_summary_table_wrapper #fee_global_summary_table > tfoot {
        display: none !important;
    }

    #fee_global_summary_table,
    #fee_global_summary_table_wrapper .dataTables_scrollHead table,
    #fee_global_summary_table_wrapper .dataTables_scrollBody table,
    #fee_global_summary_table_wrapper .dataTables_scrollFoot table {
        table-layout: fixed !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        margin: 0 !important;
    }

    #fee_global_summary_table_wrapper thead th,
    #fee_global_summary_table thead th {
        background: var(--brand) !important;
        color: #fff !important;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        white-space: normal !important;
        word-break: break-word;
        padding: 0.85rem 0.75rem !important;
        border: none !important;
        vertical-align: middle;
    }

    #fee_global_summary_table_wrapper tbody td {
        padding: 0.75rem 0.75rem !important;
        font-size: 0.82rem;
        color: var(--text-body);
        border: none !important;
        border-bottom: 1px solid var(--border) !important;
        vertical-align: middle;
        background: var(--surface);
        transition: background 0.15s var(--ease);
    }

    #fee_global_summary_table_wrapper tbody tr.fee-row-even td {
        background-color: var(--surface);
    }

    #fee_global_summary_table_wrapper tbody tr.fee-row-odd td {
        background-color: var(--surface-2);
    }

    #fee_global_summary_table_wrapper tbody tr:hover td {
        background-color: var(--brand-light) !important;
    }

    #fee_global_summary_table_wrapper tbody tr:last-child td {
        border-bottom: none !important;
    }

    #fee_global_summary_table_wrapper tfoot th {
        background-color: var(--brand-footer) !important;
        color: var(--text-h) !important;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 0.9rem 0.75rem !important;
        border: none !important;
        border-top: 2px solid var(--brand) !important;
        white-space: nowrap !important;
        vertical-align: middle;
    }

    #fee_global_summary_table_wrapper tfoot .fee-amount {
        color: var(--text-h) !important;
        font-size: 0.84rem;
    }

    #fee_global_summary_table_wrapper tfoot .fee-amount--total {
        color: var(--brand-dark) !important;
        font-size: 0.9rem;
    }

    #fee_global_summary_table_wrapper th,
    #fee_global_summary_table_wrapper td {
        box-sizing: border-box !important;
        text-align: center;
    }

    .fee-school-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
        background: var(--brand-light);
        color: var(--brand-dark);
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        border: 1px solid rgba(68, 84, 195, 0.2);
    }

    .fee-amount {
        display: inline-block;
        min-width: 4.5rem;
        font-variant-numeric: tabular-nums;
        font-feature-settings: 'tnum';
        font-weight: 600;
        color: var(--text-h);
    }

    .fee-amount--total {
        color: var(--brand);
        font-weight: 700;
    }

    .fee-total-label {
        font-size: 0.78rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--text-h);
    }

    #fee_global_summary_table_wrapper th:nth-child(1),
    #fee_global_summary_table_wrapper td:nth-child(1),
    #fee_global_summary_table_wrapper tfoot th:first-child {
        min-width: 110px !important;
        width: 110px !important;
        text-align: left !important;
    }

    #fee_global_summary_table_wrapper th:nth-child(2),
    #fee_global_summary_table_wrapper td:nth-child(2),
    #fee_global_summary_table_wrapper th:nth-child(3),
    #fee_global_summary_table_wrapper td:nth-child(3),
    #fee_global_summary_table_wrapper th:nth-child(4),
    #fee_global_summary_table_wrapper td:nth-child(4),
    #fee_global_summary_table_wrapper th:nth-child(5),
    #fee_global_summary_table_wrapper td:nth-child(5),
    #fee_global_summary_table_wrapper th:nth-child(6),
    #fee_global_summary_table_wrapper td:nth-child(6),
    #fee_global_summary_table_wrapper th:nth-child(7),
    #fee_global_summary_table_wrapper td:nth-child(7) {
        text-align: right !important;
    }

    #fee_global_summary_table_wrapper th:nth-child(2),
    #fee_global_summary_table_wrapper td:nth-child(2) {
        min-width: 130px !important;
        width: 130px !important;
    }

    #fee_global_summary_table_wrapper th:nth-child(3),
    #fee_global_summary_table_wrapper td:nth-child(3) {
        min-width: 150px !important;
        width: 150px !important;
    }

    #fee_global_summary_table_wrapper th:nth-child(4),
    #fee_global_summary_table_wrapper td:nth-child(4) {
        min-width: 170px !important;
        width: 170px !important;
    }

    #fee_global_summary_table_wrapper th:nth-child(5),
    #fee_global_summary_table_wrapper td:nth-child(5) {
        min-width: 170px !important;
        width: 170px !important;
    }

    /* Scholarship Amount */
    #fee_global_summary_table_wrapper th:nth-child(6),
    #fee_global_summary_table_wrapper td:nth-child(6) {
        min-width: 160px !important;
        width: 160px !important;
    }

    /* Total */
    #fee_global_summary_table_wrapper th:nth-child(7),
    #fee_global_summary_table_wrapper td:nth-child(7) {
        min-width: 150px !important;
        width: 150px !important;
    }

    #fee_global_summary_table_wrapper .dataTables_processing {
        background: rgba(255, 255, 255, 0.95) !important;
        border: 1px solid var(--border) !important;
        border-radius: var(--r-lg) !important;
        color: var(--brand) !important;
        font-weight: 500;
        box-shadow: 0 8px 32px var(--brand-glow);
    }

    #fee_global_summary_table_wrapper .dt-buttons {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 0.75rem;
    }

    #fee_global_summary_table_wrapper .dt-button {
        background: var(--brand) !important;
        color: #fff !important;
        border: none !important;
        border-radius: var(--r-sm) !important;
        height: 34px !important;
        padding: 0 14px !important;
        font-size: 0.78rem !important;
        font-weight: 600 !important;
        letter-spacing: 0.02em;
        box-shadow: 0 1px 4px var(--brand-glow) !important;
        transition: background 0.15s var(--ease), transform 0.15s var(--ease);
    }

    #fee_global_summary_table_wrapper .dt-button:hover {
        background: var(--brand-dark) !important;
        color: #fff !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px var(--brand-glow-md) !important;
    }

    #fee_global_summary_table_wrapper .dataTables_length,
    #fee_global_summary_table_wrapper .dataTables_info,
    #fee_global_summary_table_wrapper .dataTables_paginate {
        position: relative;
        z-index: 10;
        overflow: visible !important;
        color: var(--text-muted);
        font-size: 0.78rem;
    }

    #fee_global_summary_table_wrapper .dataTables_length select {
        border: 1.5px solid var(--border);
        border-radius: var(--r-sm);
        padding: 0.3rem 0.5rem;
        margin: 0 0.35rem;
        color: var(--text-body);
        background: var(--surface-2);
    }

    #fee_global_summary_table_wrapper .dataTables_paginate .paginate_button {
        border-radius: var(--r-sm) !important;
        border: 1.5px solid transparent !important;
        margin-left: 2px !important;
        padding: 0.3rem 0.65rem !important;
        background: transparent !important;
        color: var(--text-muted) !important;
        font-weight: 500 !important;
    }

    #fee_global_summary_table_wrapper .dataTables_paginate .paginate_button.current,
    #fee_global_summary_table_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: var(--brand) !important;
        border-color: var(--brand) !important;
        color: #fff !important;
        box-shadow: 0 2px 6px var(--brand-glow-md) !important;
    }

    #fee_global_summary_table_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--brand-light) !important;
        border-color: var(--brand) !important;
        color: var(--brand) !important;
    }

    #fee_global_summary_table_wrapper .dataTables_length,
    #fee_global_summary_table_wrapper .dataTables_paginate {
        margin-top: 0.85rem;
    }

    .filter-action-wrapper {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    #filterBtn {
        background-color: var(--brand);
        border-color: var(--brand);
        border-radius: var(--r-sm);
        font-weight: 600;
        box-shadow: 0 4px 14px var(--brand-glow);
    }

    #filterBtn:hover {
        background-color: var(--brand-dark);
        border-color: var(--brand-dark);
    }

    #resetBtn {
        background-color: var(--accent-blue);
        border-color: var(--accent-blue);
        color: #fff;
        border-radius: var(--r-sm);
        font-weight: 600;
    }

    #resetBtn:hover {
        background-color: var(--accent-blue-dark);
        border-color: var(--accent-blue-dark);
        color: #fff;
    }
</style>

@section('content')
<div class="container-fluid fee-global-summary-page" style="margin-bottom:50px;">
    <div class="card p-4">
        <div class="card-header">
            <h2 class="mb-4 text-left">Fee Global Summary Details</h2>
        </div>

        <div class="card-body">
            <div class="bs-stepper-content">
                <form id="feeGlobalSummaryFilterForm" onsubmit="return false;">
                    <div id="test-l-1" role="tabpanel" class="bs-stepper-pane" aria-labelledby="stepper1trigger1">
                        <div class="form-row">
                            <div class="form-group col-12 col-lg-6">
                                <label for="session">Session</label>
                                <select class="form-control" id="session" name="session">
                                    <option value="">Select Session</option>
                                    @foreach($sessions as $key => $session)
                                        @if($key > 144)
                                            <option value="{{ $key }}">
                                                {{ $session }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12 col-lg-6">
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

                            <div class="form-group col-12 col-lg-6">
                                <label for="fee_type">Fee Type</label>
                                <select class="form-control" id="fee_type" name="fee_type">
                                    <option value="">Select Fee Type</option>
                                    <option value="Full_semester">Full Semester</option>
                                    <option value="Short/Summer_Course">Short/Summer Course</option>
                                    <option value="Credit_hours">Credit hours</option>

                                </select>
                            </div>

                            <div class="form-group col-12">
                                <div class="filter-action-wrapper">
                                    <button type="button" id="filterBtn" class="btn btn-primary px-4">
                                        Apply Filter
                                    </button>

                                    <button type="button" id="resetBtn" class="btn px-4">
                                        Reset
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body pt-0">
            <div class="fee-summary-table-section">
                {{-- <div class="fee-summary-table-header">
                    <div>
                        <h5 class="fee-summary-table-title">Summary by School</h5>
                        <p class="fee-summary-table-subtitle">Fee breakdown across all schools for the selected filters</p>
                    </div>
                </div> --}}

                <div class="fee-summary-table-container">
                    <table id="fee_global_summary_table" class="table nowrap fee-global-summary-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th>School</th>
                                <th>Admission Fee</th>
                                <th>Activity Charges</th>
                                <th>Medical Insurance</th>
                                <th>Tuition Fee</th>
                                <th>Scholarship Amount</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody></tbody>

                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('javascript')
<script>
    $(document).ready(function () {
        var tableId = '#fee_global_summary_table';
        var table;

        function parseAmount(value) {
            return Number(value || 0);
        }

        function formatAmount(value) {
            return parseAmount(value).toLocaleString('en-US');
        }

        function getFilterParams() {
            return {
                session: $('#session').val(),
                school: $('#school').val(),
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val(),
                fee_type: $('#fee_type').val(),
                _token: '{{ csrf_token() }}'
            };
        }

        function getRowAmount(row, key, columnIndex) {
            if (row && typeof row === 'object' && !Array.isArray(row)) {
                if (row[key] !== undefined && row[key] !== null) {
                    return parseAmount(row[key]);
                }

                var upperKey = key.toUpperCase();
                if (row[upperKey] !== undefined && row[upperKey] !== null) {
                    return parseAmount(row[upperKey]);
                }
            }

            if (Array.isArray(row)) {
                return parseAmount(row[columnIndex]);
            }

            return 0;
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

        function getExportOptions() {
            return {
                modifier: {
                    page: 'all',
                    search: 'applied'
                },
                format: {
                    body: function (data) {
                        return stripHtml(data);
                    },
                    footer: function (data) {
                        return stripHtml(data);
                    }
                }
            };
        }

        function buildExportFooterRow(api) {
            var totalAdmissionFee = 0;
            var totalActivityCharges = 0;
            var totalMedicalInsurance = 0;
            var totalTutitionFee = 0;
            var totalScholarshipAmount = 0;
            var grandTotal = 0;

            api.rows({ page: 'all', search: 'applied' }).every(function () {
                var row = this.data();
                totalAdmissionFee += getRowAmount(row, 'admission_fee', 1);
                totalActivityCharges += getRowAmount(row, 'activity_charges', 2);
                totalMedicalInsurance += getRowAmount(row, 'medical_insurance', 3);
                totalTutitionFee += getRowAmount(row, 'tuition_fee', 4);
                totalScholarshipAmount += getRowAmount(row, 'scholarship_amount', 5);
                grandTotal += getRowAmount(row, 'total', 6);
            });

            return [
                'Total',
                formatAmount(totalAdmissionFee),
                formatAmount(totalActivityCharges),
                formatAmount(totalMedicalInsurance),
                formatAmount(totalTutitionFee),
                formatAmount(totalScholarshipAmount),
                formatAmount(grandTotal)
            ];
        }

        function appendExportFooterRow(docContent, footerRow) {
            var i;
            for (i = docContent.length - 1; i >= 0; i--) {
                if (docContent[i].table && docContent[i].table.body) {
                    docContent[i].table.body.push(
                        footerRow.map(function (cell) {
                            return {
                                text: String(cell),
                                bold: true,
                                fillColor: '#eef0fb'
                            };
                        })
                    );
                    break;
                }
            }
        }

        function runExportAction(extend, e, dt, button, config) {
            updateFooterTotals(dt);
            $.fn.dataTable.ext.buttons[extend].action.call(this, e, dt, button, config);
        }

        function formatAmountHtml(value, isTotal) {
            var amountClass = isTotal ? 'fee-amount fee-amount--total' : 'fee-amount';
            return '<span class="' + amountClass + '">' + formatAmount(value) + '</span>';
        }

        function updateFooterTotals(api) {
            var footerRow = buildExportFooterRow(api);

            footerRow.forEach(function (value, index) {
                if (index === 0) {
                    $(api.column(index).footer()).html('<span class="fee-total-label">' + value + '</span>');
                } else {
                    var amount = parseAmount(String(value).replace(/,/g, ''));
                    $(api.column(index).footer()).html(formatAmountHtml(amount, index === 6));
                }
            });
        }

        function adjustTableColumns() {
            if (!table) {
                return;
            }

            table.columns.adjust();
        }

        function initFeeGlobalSummaryTable() {
            if ($.fn.DataTable.isDataTable(tableId)) {
                $(tableId).DataTable().clear().destroy();
            }

            table = $(tableId).DataTable({
                scrollX: true,
                scrollCollapse: true,
                autoWidth: false,
                deferRender: true,
                processing: true,
                serverSide: false,
                paging: true,
                info: true,
                pageLength: 10,
                searching: false,
                ordering: false,
                dom: 'Brtip',
                ajax: {
                    url: '/api/get-fee-global-summary',
                    type: 'POST',
                    data: function () {
                        return getFilterParams();
                    },
                    dataSrc: function (json) {
                        if (!json.status) {
                            if (json.message) {
                                alert(json.message);
                            }
                            return [];
                        }
                        return json.data || [];
                    },
                    error: function () {
                        alert('Unable to load fee global summary data.');
                    }
                },
                columns: [
                    {
                        data: 'schoold',
                        defaultContent: '',
                        render: function (data, type) {
                            if (type === 'display') {
                                return data ? '<span class="fee-school-badge">' + data + '</span>' : '';
                            }
                            if (type === 'export') {
                                return data || '';
                            }
                            return data || '';
                        }
                    },
                    {
                        data: 'admission_fee',
                        defaultContent: 0,
                        render: function (data, type) {
                            if (type === 'display' || type === 'filter') {
                                return formatAmountHtml(data, false);
                            }
                            if (type === 'export') {
                                return formatAmount(data);
                            }
                            return parseAmount(data);
                        }
                    },
                    {
                        data: 'activity_charges',
                        defaultContent: 0,
                        render: function (data, type) {
                            if (type === 'display' || type === 'filter') {
                                return formatAmountHtml(data, false);
                            }
                            if (type === 'export') {
                                return formatAmount(data);
                            }
                            return parseAmount(data);
                        }
                    },
                    {
                        data: 'medical_insurance',
                        defaultContent: 0,
                        render: function (data, type) {
                            if (type === 'display' || type === 'filter') {
                                return formatAmountHtml(data, false);
                            }
                            if (type === 'export') {
                                return formatAmount(data);
                            }
                            return parseAmount(data);
                        }
                    },
                    {
                        data: 'tuition_fee',
                        defaultContent: 0,
                        render: function (data, type) {
                            if (type === 'display' || type === 'filter') {
                                return formatAmountHtml(data, false);
                            }
                            if (type === 'export') {
                                return formatAmount(data);
                            }
                            return parseAmount(data);
                        }
                    },
                    {
                        data: 'scholarship_amount',
                        defaultContent: 0,
                        render: function (data, type) {
                            if (type === 'display' || type === 'filter') {
                                return formatAmountHtml(data, false);
                            }
                            if (type === 'export') {
                                return formatAmount(data);
                            }
                            return parseAmount(data);
                        }
                    },
                    {
                        data: 'total',
                        defaultContent: 0,
                        render: function (data, type) {
                            if (type === 'display' || type === 'filter') {
                                return formatAmountHtml(data, true);
                            }
                            if (type === 'export') {
                                return formatAmount(data);
                            }
                            return parseAmount(data);
                        }
                    }
                ],
                createdRow: function (row, data, dataIndex) {
                    $(row).addClass(dataIndex % 2 === 0 ? 'fee-row-even' : 'fee-row-odd');
                },
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Fee Global Summary',
                        exportOptions: getExportOptions(),
                        customizeData: function (data) {
                            data.body.push(buildExportFooterRow(table));
                        },
                        action: function (e, dt, button, config) {
                            runExportAction.call(this, 'excelHtml5', e, dt, button, config);
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Fee Global Summary',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: getExportOptions(),
                        customize: function (doc) {
                            appendExportFooterRow(doc.content, buildExportFooterRow(table));
                        },
                        action: function (e, dt, button, config) {
                            runExportAction.call(this, 'pdfHtml5', e, dt, button, config);
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Fee Global Summary',
                        exportOptions: getExportOptions(),
                        customize: function (win) {
                            var footerRow = buildExportFooterRow(table);
                            var $row = $('<tr></tr>');

                            footerRow.forEach(function (cell) {
                                $row.append(
                                    $('<td></td>').text(cell).css({
                                        fontWeight: 'bold',
                                        backgroundColor: '#eef0fb'
                                    })
                                );
                            });

                            $(win.document.body).find('table tbody').append($row);
                        },
                        action: function (e, dt, button, config) {
                            runExportAction.call(this, 'print', e, dt, button, config);
                        }
                    }
                ],
                language: {
                    emptyTable: 'No fee summary records found',
                    zeroRecords: 'No matching fee summary records found',
                    processing: 'Loading fee summary...'
                },
                footerCallback: function () {
                    updateFooterTotals(this.api());
                },
                initComplete: function () {
                    updateFooterTotals(this.api());
                    adjustTableColumns();
                }
            });
        }

        function reloadFeeGlobalSummaryTable() {
            if (!table) {
                initFeeGlobalSummaryTable();
                return;
            }

            table.ajax.reload(function () {
                updateFooterTotals(table);
                adjustTableColumns();
            }, false);
        }

        $('#filterBtn').on('click', function () {
            reloadFeeGlobalSummaryTable();
        });

        $('#resetBtn').on('click', function () {
            $('#feeGlobalSummaryFilterForm')[0].reset();
            reloadFeeGlobalSummaryTable();
        });

        var resizeTimer;
        $(window).on('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                adjustTableColumns();
            }, 150);
        });

        initFeeGlobalSummaryTable();
    });
</script>
@stop
