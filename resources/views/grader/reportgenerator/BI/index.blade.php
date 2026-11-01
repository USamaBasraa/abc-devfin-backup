@extends('layouts.app', ['pageTitle' => 'BI Report Dashboard'])
@section('content')
    <style>
        :root {
            --bi-surface: #ffffff;
            --bi-page: #f0f2f5;
            --bi-border: #e2e5ea;
            --bi-text: #111827;
            --bi-muted: #5c6370;
            --bi-accent: #1f776e;
            --bi-accent-soft: #e6f5f3;
            --bi-accent-dark: #155e54;
            --bi-shadow-sm: 0 1px 2px rgba(17, 24, 39, 0.05);
            --bi-shadow-md: 0 4px 20px rgba(17, 24, 39, 0.07);
            --bi-radius: 12px;
        }

        .bi-page {
            background: var(--bi-page);
            margin: -12px -15px 0;
            padding: 1.25rem 1.25rem 2.5rem;
            min-height: 60vh;
            max-width: 1680px;
            margin-left: auto;
            margin-right: auto;
            overflow-x: hidden;
        }

        .bi-page-header {
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--bi-border);
        }

        .bi-page-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--bi-text);
            letter-spacing: -0.02em;
            margin: 0 0 0.35rem;
        }

        .bi-page-lead {
            margin: 0;
            font-size: 0.9375rem;
            color: var(--bi-muted);
            line-height: 1.45;
            max-width: 46rem;
        }

        .bi-card {
            background: var(--bi-surface);
            border: 1px solid var(--bi-border);
            border-radius: var(--bi-radius);
            box-shadow: var(--bi-shadow-md);
            overflow: hidden;
        }

        .bi-nav-wrap {
            padding: 0.65rem 0.85rem 0.35rem;
            border-bottom: 1px solid var(--bi-border);
            background: linear-gradient(180deg, #fcfcfd 0%, #f5f6f8 100%);
        }

        .bi-nav-wrap .nav-link {
            color: var(--bi-muted);
            font-size: 0.8125rem;
            font-weight: 600;
            border-radius: 8px;
            padding: 0.5rem 0.85rem;
            margin: 0 0.15rem 0.45rem 0;
            border: 1px solid transparent;
            white-space: nowrap;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .bi-nav-wrap .nav-link:hover {
            color: var(--bi-text);
            background: #eef0f3;
        }

        .bi-nav-wrap .nav-link.active {
            color: #fff;
            background: var(--bi-accent);
            border-color: var(--bi-accent);
            box-shadow: 0 2px 8px rgba(31, 119, 110, 0.35);
        }

        .bi-nav-wrap .nav-link.bi-tab-disabled,
        .bi-nav-wrap .nav-link.disabled {
            color: #a0a7b0;
            background: #eef0f3;
            border-color: #e4e7eb;
            opacity: 0.72;
            cursor: not-allowed;
            pointer-events: none;
            box-shadow: none;
        }

        .bi-nav-wrap .nav-link.bi-tab-disabled:hover,
        .bi-nav-wrap .nav-link.disabled:hover {
            color: #a0a7b0;
            background: #eef0f3;
        }

        .bi-panel-body {
            padding: 1.35rem 1.35rem 1.15rem;
            background: #fafbfc;
        }

        .bi-meta {
            font-size: 0.8125rem;
            color: var(--bi-muted);
            margin-bottom: 1rem;
        }

        .bi-meta code {
            font-size: 0.78rem;
            color: #374151;
            background: #f3f4f6;
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
        }

        .bi-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding: 0.65rem 0.85rem;
            background: #fff;
            border: 1px solid var(--bi-border);
            border-radius: 10px;
        }

        .bi-rows-pill {
            font-size: 0.8125rem;
            color: var(--bi-muted);
            font-weight: 500;
        }

        .bi-rows-pill strong {
            color: var(--bi-text);
        }

        .bi-badge-soft {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.35rem 0.65rem;
            border-radius: 8px;
            background: var(--bi-accent-soft);
            color: var(--bi-accent-dark);
            letter-spacing: 0.02em;
        }

        .bi-table-wrap {
            border: 1px solid var(--bi-border);
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            box-shadow: var(--bi-shadow-sm);
        }

        .bi-table-wrap table.dataTable {
            margin-bottom: 0 !important;
            font-size: 0.8125rem;
        }

        .bi-table-wrap table.dataTable thead th {
            background: #f4f5f7;
            color: #374151;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 2px solid var(--bi-border);
            white-space: nowrap;
        }

        .bi-table-wrap .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--bi-border);
            border-radius: 8px;
            padding: 0.4rem 0.65rem;
            font-size: 0.8125rem;
        }

        .bi-table-wrap .dataTables_wrapper .dataTables_length select {
            border: 1px solid var(--bi-border);
            border-radius: 8px;
            padding: 0.3rem 0.55rem;
            font-size: 0.8125rem;
        }

        .bi-empty {
            text-align: center;
            padding: 2.5rem 1rem;
            color: var(--bi-muted);
        }

        .bi-empty i {
            font-size: 2rem;
            opacity: 0.35;
            display: block;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 767px) {
            .bi-nav-wrap .nav {
                flex-wrap: nowrap;
                overflow-x: auto;
                padding-bottom: 0.25rem;
                -webkit-overflow-scrolling: touch;
            }
        }

        .bi-filter-card {
            background: var(--bi-surface);
            border: 1px solid var(--bi-border);
            border-radius: var(--bi-radius);
            padding: 0;
            margin-bottom: 1.35rem;
            box-shadow: var(--bi-shadow-md);
            overflow: hidden;
        }

        .bi-filter-card-inner {
            padding: 1.15rem 1.35rem 1.25rem;
        }

        .bi-filter-card-heading {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 1rem 1.35rem;
            background: linear-gradient(135deg, #f8faf9 0%, #f0f4f3 100%);
            border-bottom: 1px solid var(--bi-border);
        }

        .bi-filter-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--bi-text);
            margin: 0 0 0.2rem;
        }

        .bi-filter-hint {
            margin: 0;
            font-size: 0.8125rem;
            color: var(--bi-muted);
            max-width: 36rem;
            line-height: 1.4;
        }

        .bi-filter-actions .btn {
            font-weight: 600;
            border-radius: 8px;
            padding: 0.4rem 0.9rem;
        }

        .bi-btn-primary {
            background-color: var(--bi-accent) !important;
            border-color: var(--bi-accent) !important;
        }

        .bi-btn-primary:hover,
        .bi-btn-primary:focus {
            background-color: var(--bi-accent-dark) !important;
            border-color: var(--bi-accent-dark) !important;
        }

        .bi-filter-group {
            margin-bottom: 1.1rem;
        }

        .bi-filter-group:last-child {
            margin-bottom: 0;
        }

        .bi-filter-group-label {
            display: block;
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--bi-accent-dark);
            margin-bottom: 0.5rem;
            padding-left: 0.15rem;
        }

        .bi-input-disclaimer {
            margin-top: 0.35rem;
            font-size: 0.6875rem;
            line-height: 1.35;
            color: #6b7280;
            font-weight: 500;
        }

        .bi-field-hint {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 0.95rem;
            height: 0.95rem;
            margin-left: 0.2rem;
            border-radius: 50%;
            border: 1px solid #c5cbd3;
            color: #6b7280;
            font-size: 0.625rem;
            font-weight: 700;
            line-height: 1;
            cursor: help;
            vertical-align: middle;
            user-select: none;
        }

        .bi-field-hint:hover {
            border-color: var(--bi-accent);
            color: var(--bi-accent-dark);
            background: var(--bi-accent-soft);
        }

        .bi-filter-card label {
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--bi-muted);
            margin-bottom: 0.28rem;
        }

        .bi-filter-card .form-control {
            border-radius: 8px;
            border-color: var(--bi-border);
        }

        .bi-filter-card .form-control:focus {
            border-color: var(--bi-accent);
            box-shadow: 0 0 0 0.15rem rgba(31, 119, 110, 0.2);
        }

        .bi-dashboard-block {
            background: #fff;
            border: 1px solid var(--bi-border);
            border-radius: 10px;
            padding: 1rem 1.15rem 1.05rem;
            margin-bottom: 1.25rem;
            box-shadow: var(--bi-shadow-sm);
        }

        .bi-kpi {
            border: 1px solid var(--bi-border);
            border-radius: 10px;
            border-left: 4px solid var(--bi-accent);
            padding: 0.85rem 1rem;
            background: #fff;
            height: 100%;
            box-shadow: var(--bi-shadow-sm);
            transition: box-shadow 0.15s ease;
        }

        .bi-kpi:hover {
            box-shadow: 0 4px 12px rgba(17, 24, 39, 0.08);
        }

        .bi-kpi .kpi-label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--bi-muted);
            margin-bottom: 0.4rem;
            font-weight: 600;
            line-height: 1.3;
        }

        .bi-kpi .kpi-value {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--bi-text);
            letter-spacing: -0.02em;
        }

        .bi-kpi-compact {
            padding: 0.7rem 0.85rem;
        }

        .bi-kpi-compact .kpi-label {
            font-size: 0.65rem;
            line-height: 1.25;
        }

        .bi-kpi-compact .kpi-value {
            font-size: 1.15rem;
        }

        .bi-school-card-clickable {
            cursor: pointer;
        }

        .bi-school-card-clickable:hover,
        .bi-school-card-clickable:focus {
            border-color: var(--bi-accent);
            box-shadow: 0 6px 18px rgba(31, 119, 110, 0.16);
            outline: none;
        }

        .bi-school-modal .modal-header {
            background: #fafbfc;
            border-bottom: 1px solid var(--bi-border);
        }

        .bi-school-breakdown-table thead th {
            background: #f4f5f7;
            color: var(--bi-muted);
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .bi-school-breakdown-table td {
            font-size: 0.84rem;
            vertical-align: middle;
        }

        .bi-fee-summary {
            border-left: 4px solid var(--bi-accent);
            background: linear-gradient(135deg, #ffffff 0%, #f8fffd 100%);
        }

        .bi-fee-formula-card {
            border: 1px solid var(--bi-border);
            border-radius: 12px;
            background: #fff;
            padding: 0.85rem 1rem;
            box-shadow: var(--bi-shadow-sm);
        }

        .bi-fee-formula-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.48rem 0;
            border-bottom: 1px dashed #d7dce2;
            font-size: 0.9rem;
        }

        .bi-fee-formula-row:last-child {
            border-bottom: 0;
        }

        .bi-fee-formula-row span {
            color: #374151;
            font-weight: 600;
        }

        .bi-fee-formula-row strong {
            color: var(--bi-text);
            font-size: 1rem;
            white-space: nowrap;
        }

        .bi-fee-attention strong {
            color: #b42318;
        }

        .bi-fee-mini-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .bi-fee-mini-card {
            border: 1px solid var(--bi-border);
            border-radius: 12px;
            background: #fff;
            padding: 0.8rem;
            box-shadow: var(--bi-shadow-sm);
        }

        .bi-fee-mini-card span,
        .bi-fee-conclusion span {
            display: block;
            color: var(--bi-muted);
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }

        .bi-fee-mini-card strong {
            color: var(--bi-text);
            display: block;
            font-size: 1.4rem;
            line-height: 1.1;
        }

        .bi-fee-mini-card small {
            color: var(--bi-muted);
            display: block;
            line-height: 1.35;
            margin-top: 0.45rem;
        }

        .bi-fee-mini-warn {
            border-left: 4px solid #dc2626;
        }

        .bi-fee-conclusion {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
            margin-top: 0.9rem;
            padding-top: 0.9rem;
            border-top: 1px solid var(--bi-border);
        }

        .bi-fee-conclusion div {
            border-radius: 10px;
            background: var(--bi-accent-soft);
            padding: 0.7rem 0.85rem;
        }

        .bi-fee-conclusion strong {
            color: var(--bi-accent-dark);
            font-size: 1.2rem;
        }

        .bi-fee-strength-row {
            grid-column: 1 / -1;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .bi-fee-strength-label {
            display: block;
            color: var(--bi-muted);
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 0.15rem;
        }

        .bi-fee-strength-formula {
            display: block;
            font-size: 0.92rem;
            color: var(--bi-text);
            line-height: 1.6;
            flex-wrap: wrap;
        }

        .bi-fee-strength-formula strong {
            color: var(--bi-accent-dark);
            font-size: 1.15rem;
        }

        .bi-fee-compare {
            margin-top: 0.9rem;
            padding-top: 0.9rem;
            border-top: 1px solid var(--bi-border);
        }

        .bi-fee-compare-title {
            color: var(--bi-text);
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            margin-bottom: 0.7rem;
            text-transform: uppercase;
        }

        .bi-fee-compare-card {
            border: 1px solid var(--bi-border);
            border-radius: 10px;
            background: #fff;
            padding: 0.7rem 0.8rem;
            height: 100%;
            box-shadow: var(--bi-shadow-sm);
        }

        .bi-fee-compare-card span {
            display: block;
            color: var(--bi-muted);
            font-size: 0.66rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }

        .bi-fee-compare-card strong {
            color: var(--bi-text);
            display: block;
            font-size: 1.1rem;
            line-height: 1.15;
        }

        .bi-fee-compare-card small {
            color: var(--bi-muted);
            display: block;
            line-height: 1.35;
            margin-top: 0.35rem;
        }

        .bi-gym-summary {
            border-left: 4px solid var(--bi-accent);
            background: linear-gradient(135deg, #ffffff 0%, #f8fffd 100%);
        }

        .bi-gym-card {
            border: 1px solid var(--bi-border);
            border-radius: 12px;
            background: #fff;
            padding: 0.8rem 0.9rem;
            box-shadow: var(--bi-shadow-sm);
        }

        .bi-gym-card-label {
            color: var(--bi-muted);
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            line-height: 1.25;
            margin-bottom: 0.2rem;
            text-transform: uppercase;
        }

        .bi-gym-card-month {
            color: var(--bi-accent-dark);
            font-size: 0.75rem;
            font-weight: 700;
            margin-bottom: 0.45rem;
        }

        .bi-gym-total {
            color: var(--bi-text);
            font-size: 1.55rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 0.55rem;
        }

        .bi-gym-split {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
        }

        .bi-gym-split span {
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.25rem 0.5rem;
        }

        .bi-gym-paid {
            background: #e7f8ef;
            color: #0d6b3f;
        }

        .bi-gym-unpaid {
            background: #fff1f1;
            color: #b42318;
        }

        .bi-gym-totals {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
            margin-top: 0.9rem;
            padding-top: 0.9rem;
            border-top: 1px solid var(--bi-border);
        }

        .bi-gym-totals div {
            border-radius: 10px;
            background: var(--bi-accent-soft);
            padding: 0.7rem 0.85rem;
        }

        .bi-gym-totals span {
            display: block;
            color: var(--bi-muted);
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
        }

        .bi-gym-totals strong {
            color: var(--bi-accent-dark);
            font-size: 1.2rem;
        }

        .bi-attendance-summary {
            border-left: 4px solid var(--bi-accent);
            background: linear-gradient(135deg, #ffffff 0%, #f8fffd 100%);
        }

        .bi-attendance-card {
            border: 1px solid var(--bi-border);
            border-radius: 12px;
            background: #fff;
            padding: 0.75rem 0.85rem;
            box-shadow: var(--bi-shadow-sm);
        }

        .bi-attendance-code {
            color: var(--bi-muted);
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.07em;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
        }

        .bi-attendance-value {
            color: var(--bi-text);
            font-size: 1.45rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .bi-attendance-label {
            color: var(--bi-muted);
            font-size: 0.72rem;
            font-weight: 600;
            margin-top: 0.25rem;
        }

        .bi-attendance-total {
            align-items: center;
            background: var(--bi-accent-soft);
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            margin-top: 0.85rem;
            padding: 0.75rem 0.9rem;
        }

        .bi-attendance-total span {
            color: var(--bi-muted);
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .bi-attendance-total strong {
            color: var(--bi-accent-dark);
            font-size: 1.25rem;
        }

        .bi-section-title {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            font-size: 0.8125rem;
            font-weight: 700;
            color: var(--bi-text);
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin-bottom: 0.85rem;
        }

        .bi-section-title::before {
            content: '';
            width: 4px;
            height: 1.05rem;
            background: var(--bi-accent);
            border-radius: 2px;
            flex-shrink: 0;
        }

        .bi-compare-panel {
            border: 1px solid var(--bi-border);
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            box-shadow: var(--bi-shadow-sm);
        }

        .bi-compare-card-kpi .bi-compare-card-sub {
            margin-top: 0.35rem;
            font-size: 0.68rem;
            line-height: 1.35;
            font-variant-numeric: tabular-nums;
        }

        .bi-compare-table thead th {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--bi-muted);
            background: #f4f5f7;
            font-weight: 700;
            border-bottom: 2px solid var(--bi-border);
            vertical-align: middle;
        }

        .bi-compare-table td {
            font-size: 0.875rem;
            vertical-align: middle;
        }

        .bi-compare-table tbody tr:nth-child(even) {
            background: #fafbfc;
        }

        .bi-compare-delta-pos {
            color: #0d6b3f;
            font-weight: 700;
        }

        .bi-compare-delta-neg {
            color: #b42318;
            font-weight: 700;
        }

        .bi-range-pivot-wrap {
            overflow-x: auto;
            overflow-y: visible;
            border-radius: 10px;
            border: 1px solid var(--bi-border);
            background: #fff;
            box-shadow: var(--bi-shadow-sm);
        }

        .bi-range-pivot-table {
            margin-bottom: 0;
            white-space: nowrap;
            width: 100%;
        }

        .bi-range-pivot-table thead th {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--bi-muted);
            background: #f4f5f7;
            font-weight: 700;
            border-bottom: 2px solid var(--bi-border);
        }

        .bi-range-pivot-table thead th.bi-range-pivot-sticky {
            left: 0;
            z-index: 3;
            min-width: 14rem;
            max-width: 28rem;
            white-space: normal;
            vertical-align: bottom;
            box-shadow: 1px 0 0 var(--bi-border);
        }

        .bi-range-pivot-table tbody th.bi-range-pivot-sticky {
            font-size: 0.8125rem;
            font-weight: 600;
            background: #fff;
            vertical-align: middle;
            white-space: normal;
            word-break: break-word;
            min-width: 14rem;
            max-width: 28rem;
            position: sticky;
            left: 0;
            z-index: 1;
            border-right: 1px solid var(--bi-border);
            box-shadow: 1px 0 0 var(--bi-border);
        }

        .bi-range-pivot-table tbody tr:nth-child(even) th.bi-range-pivot-sticky,
        .bi-range-pivot-table tbody tr:nth-child(even) td {
            background-color: #fafbfc;
        }

        .bi-range-pivot-table tbody td {
            font-size: 0.8125rem;
            font-variant-numeric: tabular-nums;
        }

        .bi-page.bi-loading {
            opacity: 0.65;
            pointer-events: none;
        }
    </style>

    <div class="bi-page" id="bi-dashboard-root">
        @include('grader.reportgenerator.BI.dashboard-body')
    </div>
@endsection

@section('javascript')
    <script>
        window.biCharts = @json(collect($biDatasets ?? [])->map(function ($d) {
            return $d['charts'] ?? [];
        }));

        $(function() {
            var baseUrl = typeof url !== 'undefined' ? url : '';

            function biDisposeCharts() {
                if (!window.biChartInstances) {
                    return;
                }
                Object.keys(window.biChartInstances).forEach(function(k) {
                    var c = window.biChartInstances[k];
                    if (c && typeof c.dispose === 'function') {
                        c.dispose();
                    }
                });
                window.biChartInstances = {};
            }

            function biMoveSummaryBlocksToTop() {
                $('.bi-report-summary-source').each(function() {
                    var $block = $(this);
                    var key = $block.attr('data-summary-tab') || '';
                    var $pane = $('.bi-top-summary-pane[data-summary-tab="' + key + '"]');
                    if (!$pane.length) {
                        return;
                    }
                    $block.removeClass('bi-report-summary-source');
                    $pane.append($block);
                });
            }

            function biAfterDashboardSwap(resp) {
                window.biCharts = resp.charts || {};
                var bf = resp.biFilter || {};
                var initSchool = $('#bi_filter_school').val();
                if (initSchool) {
                    biFillDepartments(initSchool, bf.dept_id || '', bf.prog_id || '');
                }
                biMoveSummaryBlocksToTop();
                $('.tab-pane.active table.bi-datatable').each(function() {
                    initBiTable($(this));
                });
                var tabKey = resp.firstTabKey;
                if (tabKey) {
                    setTimeout(function() {
                        biInitChartsForTab(tabKey);
                        if (window.biChartInstances) {
                            Object.keys(window.biChartInstances).forEach(function(k) {
                                var c = window.biChartInstances[k];
                                if (c && typeof c.resize === 'function') {
                                    c.resize();
                                }
                            });
                        }
                    }, 50);
                }
            }

            function biFillDepartments(schoolId, selectedDept, selectedProg) {
                var $dept = $('#bi_filter_dept');
                var $prog = $('#bi_filter_prog');
                $dept.prop('disabled', true).html('<option value=\"\">All departments</option>');
                $prog.prop('disabled', true).html('<option value=\"\">All programs</option>');
                if (!schoolId) {
                    return;
                }
                $.getJSON(baseUrl + '/get-departments/' + schoolId, function(rows) {
                    $dept.prop('disabled', false);
                    $.each(rows, function(_, r) {
                        var sel = String(selectedDept) === String(r.dept_id) ? ' selected' : '';
                        $dept.append('<option value=\"' + r.dept_id + '\"' + sel + '>' + r.dept_name +
                            '</option>');
                    });
                    if (selectedDept) {
                        biFillPrograms(schoolId, selectedDept, selectedProg);
                    }
                });
            }

            function biFillPrograms(schoolId, deptId, selectedProg) {
                var $prog = $('#bi_filter_prog');
                $prog.prop('disabled', true).html('<option value=\"\">All programs</option>');
                if (!schoolId || !deptId) {
                    return;
                }
                $.getJSON(baseUrl + '/get-programs', {
                    school_id: schoolId,
                    department_id: deptId
                }, function(rows) {
                    $prog.prop('disabled', false);
                    $.each(rows, function(_, r) {
                        var sel = String(selectedProg) === String(r.prog_id) ? ' selected' : '';
                        $prog.append('<option value=\"' + r.prog_id + '\"' + sel + '>' + r.prog_name + '</option>');
                    });
                });
            }

            $(document).on('change', '#bi_filter_school', function() {
                biFillDepartments($(this).val(), '', '');
            });
            $(document).on('change', '#bi_filter_dept', function() {
                biFillPrograms($('#bi_filter_school').val(), $(this).val(), null);
            });

            $(document).on('submit', '#bi-filter-form', function(e) {
                e.preventDefault();
                var $f = $(this);
                var $root = $('#bi-dashboard-root');
                var $err = $('#bi_filter_date_error');
                $err.addClass('d-none').text('');
                var activeTarget = $('#biReportTabs a.nav-link.active').attr('href') || '';
                var activeKey = activeTarget.replace('#tab-', '');
                if (activeKey) {
                    $('#bi_active_tab').val(activeKey);
                }
                // Session + empty From/To → today (matches server default; keeps URL in sync).
                var sessVal = $('#bi_filter_sess').val();
                var $dateFrom = $('#bi_filter_date_from');
                var $dateTo = $('#bi_filter_date_to');
                if (sessVal && !$dateFrom.val() && !$dateTo.val()) {
                    var today = (function() {
                        var d = new Date();
                        var m = String(d.getMonth() + 1).padStart(2, '0');
                        var day = String(d.getDate()).padStart(2, '0');
                        return d.getFullYear() + '-' + m + '-' + day;
                    })();
                    $dateFrom.val(today);
                    $dateTo.val(today);
                }

                var fromVal = $dateFrom.val();
                var toVal = $dateTo.val();
                if (fromVal && toVal && fromVal > toVal) {
                    $err.removeClass('d-none').text('To date must be the same as or after From date.');
                    $dateTo.focus();
                    return;
                }

                var cmpA = $('#bi_filter_cmp_a').val();
                var cmpB = $('#bi_filter_cmp_b').val();
                if ((cmpA && !cmpB) || (!cmpA && cmpB)) {
                    $err.removeClass('d-none').text('Please set both Compare date A and Compare date B, or leave both blank.');
                    (cmpA ? $('#bi_filter_cmp_b') : $('#bi_filter_cmp_a')).focus();
                    return;
                }
                if (cmpA && cmpB && cmpB <= cmpA) {
                    $err.removeClass('d-none').text('Compare date B must be greater than Compare date A.');
                    $('#bi_filter_cmp_b').focus();
                    return;
                }

                var qs = $f.serialize();
                var action = $f.attr('action') || '';
                var hrefSep = action.indexOf('?') >= 0 ? '&' : '?';
                var href = action + hrefSep + qs;
                var ajaxSep = href.indexOf('?') >= 0 ? '&' : '?';
                var ajaxUrl = href + ajaxSep + 'bi_ajax=1';
                $root.addClass('bi-loading');
                $.ajax({
                    url: ajaxUrl,
                    method: 'GET',
                    dataType: 'json'
                }).done(function(resp) {
                    if (resp && resp.html) {
                        biDisposeCharts();
                        $root.html(resp.html);
                        try {
                            history.replaceState(null, '', href);
                        } catch (err) {}
                        biAfterDashboardSwap(resp);
                    }
                }).fail(function() {
                    window.location.href = href;
                }).always(function() {
                    $('#bi-dashboard-root').removeClass('bi-loading');
                });
            });

            $(document).on('change', '#bi_filter_date_from, #bi_filter_date_to', function() {
                var fromVal = $('#bi_filter_date_from').val();
                var toVal = $('#bi_filter_date_to').val();
                if (fromVal) {
                    $('#bi_filter_date_to').attr('min', fromVal);
                } else {
                    $('#bi_filter_date_to').removeAttr('min');
                }
                if (toVal) {
                    $('#bi_filter_date_from').attr('max', toVal);
                } else {
                    $('#bi_filter_date_from').removeAttr('max');
                }
            });

            $(document).on('change', '#bi_filter_cmp_a, #bi_filter_cmp_b', function() {
                var a = $('#bi_filter_cmp_a').val();
                if (a) {
                    // B must be strictly after A
                    var next = (function(iso) {
                        var d = new Date(iso + 'T00:00:00');
                        d.setDate(d.getDate() + 1);
                        var m = String(d.getMonth() + 1).padStart(2, '0');
                        var day = String(d.getDate()).padStart(2, '0');
                        return d.getFullYear() + '-' + m + '-' + day;
                    })(a);
                    $('#bi_filter_cmp_b').attr('min', next);
                } else {
                    $('#bi_filter_cmp_b').removeAttr('min');
                }
            });

            var initSchool = $('#bi_filter_school').val();
            var initDept = @json($biFilter['dept_id'] ?? '');
            var initProg = @json($biFilter['prog_id'] ?? '');
            if (initSchool) {
                biFillDepartments(initSchool, initDept, initProg);
            }
            $('#bi_filter_date_from, #bi_filter_date_to').trigger('change');
            $('#bi_filter_cmp_a').trigger('change');

            window.biChartInstances = window.biChartInstances || {};

            function biRenderBar(domId, payload) {
                if (typeof echarts === 'undefined') {
                    return null;
                }
                var el = document.getElementById(domId);
                if (!el) return null;
                var chart = echarts.init(el);
                chart.setOption({
                    color: ['#1f776e', '#2a9d8f', '#264653', '#e9c46a', '#f4a261'],
                    title: {
                        text: payload.title,
                        left: 0,
                        textStyle: {
                            fontSize: 14,
                            fontWeight: 600
                        }
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    grid: {
                        left: 52,
                        right: 20,
                        top: 48,
                        bottom: 72
                    },
                    xAxis: {
                        type: 'category',
                        data: payload.labels,
                        axisLabel: {
                            rotate: 28,
                            fontSize: 11
                        }
                    },
                    yAxis: {
                        type: 'value',
                        splitLine: {
                            lineStyle: {
                                type: 'dashed'
                            }
                        }
                    },
                    series: [{
                        type: 'bar',
                        data: payload.values,
                        barMaxWidth: 40,
                        itemStyle: {
                            borderRadius: [4, 4, 0, 0]
                        }
                    }]
                });
                return chart;
            }

            function biRenderPie(domId, payload) {
                if (typeof echarts === 'undefined') {
                    return null;
                }
                var el = document.getElementById(domId);
                if (!el) return null;
                var chart = echarts.init(el);
                chart.setOption({
                    title: {
                        text: payload.title,
                        left: 0,
                        textStyle: {
                            fontSize: 14,
                            fontWeight: 600
                        }
                    },
                    tooltip: {
                        trigger: 'item'
                    },
                    legend: {
                        type: 'scroll',
                        bottom: 4,
                        textStyle: {
                            fontSize: 10
                        }
                    },
                    series: [{
                        type: 'pie',
                        radius: ['38%', '64%'],
                        avoidLabelOverlap: true,
                        data: payload.series,
                        itemStyle: {
                            borderRadius: 4,
                            borderColor: '#fff',
                            borderWidth: 2
                        },
                        label: {
                            formatter: '{b}\n{c}'
                        }
                    }]
                });
                return chart;
            }

            function biRenderLine(domId, payload) {
                if (typeof echarts === 'undefined') {
                    return null;
                }
                var el = document.getElementById(domId);
                if (!el) return null;
                var chart = echarts.init(el);
                chart.setOption({
                    color: ['#1f776e'],
                    title: {
                        text: payload.title,
                        left: 0,
                        textStyle: {
                            fontSize: 14,
                            fontWeight: 600
                        }
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    grid: {
                        left: 52,
                        right: 20,
                        top: 48,
                        bottom: 72
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: payload.labels,
                        axisLabel: {
                            rotate: 28,
                            fontSize: 10
                        }
                    },
                    yAxis: {
                        type: 'value',
                        splitLine: {
                            lineStyle: {
                                type: 'dashed'
                            }
                        }
                    },
                    series: [{
                        type: 'line',
                        smooth: 0.25,
                        symbolSize: 6,
                        areaStyle: {
                            opacity: 0.12
                        },
                        data: payload.values
                    }]
                });
                return chart;
            }

            function biInitChartsForTab(tabKey) {
                if (!window.biCharts || !window.biCharts[tabKey]) {
                    return;
                }
                var pack = window.biCharts[tabKey];
                if (pack.by_date_line && document.getElementById('bi-chart-line-' + tabKey)) {
                    var lid = 'bi-line-' + tabKey;
                    if (!window.biChartInstances[lid]) {
                        window.biChartInstances[lid] = biRenderLine('bi-chart-line-' + tabKey, pack.by_date_line);
                    }
                }
                if (pack.by_school && document.getElementById('bi-chart-bar-' + tabKey)) {
                    var bid = 'bi-bar-' + tabKey;
                    if (!window.biChartInstances[bid]) {
                        window.biChartInstances[bid] = biRenderBar('bi-chart-bar-' + tabKey, pack.by_school);
                    }
                }
                if (pack.by_dept_pie && document.getElementById('bi-chart-pie-' + tabKey)) {
                    var pid = 'bi-pie-' + tabKey;
                    if (!window.biChartInstances[pid]) {
                        window.biChartInstances[pid] = biRenderPie('bi-chart-pie-' + tabKey, pack.by_dept_pie);
                    }
                }
            }

            function biDataTableConfig($table) {
                var exportTitle = $table.data('title') || 'BI_Report';
                return {
                    responsive: !!($.fn.dataTable && $.fn.dataTable.Responsive),
                    scrollX: true,
                    autoWidth: false,
                    pageLength: 25,
                    lengthMenu: [
                        [10, 25, 50, 100, 250, 500],
                        [10, 25, 50, 100, 250, 500]
                    ],
                    order: [],
                    dom: "<'row align-items-center mb-2'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row align-items-center mt-2'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>B",
                    buttons: [{
                            extend: 'copy',
                            className: 'btn btn-sm btn-light border mr-1'
                        },
                        {
                            extend: 'csvHtml5',
                            className: 'btn btn-sm btn-light border mr-1',
                            title: exportTitle
                        },
                        {
                            extend: 'excelHtml5',
                            className: 'btn btn-sm btn-light border mr-1',
                            title: exportTitle
                        },
                        {
                            extend: 'print',
                            className: 'btn btn-sm btn-light border',
                            title: exportTitle
                        }
                    ]
                };
            }

            function initBiTable($table) {
                if ($table.length && !$.fn.DataTable.isDataTable($table[0])) {
                    $table.DataTable(biDataTableConfig($table));
                }
            }

            function biNormalizeSchoolName(value) {
                return String(value || '')
                    .toLowerCase()
                    .replace(/&/g, ' and ')
                    .replace(/[^a-z0-9]+/g, '');
            }

            function biExtractSchoolDetailsFromGrid(schoolName) {
                var $pane = $('.tab-pane.active');
                var $table = $pane.find('table.bi-datatable').first();
                if (!$table.length) {
                    return [];
                }

                var wantedSchool = biNormalizeSchoolName(schoolName);
                var groups = {};

                function cellText(v) {
                    if (v === null || v === undefined) {
                        return '';
                    }
                    return $.trim($('<div>').html(String(v)).text());
                }

                function pickField(obj, names) {
                    if (!obj || typeof obj !== 'object') {
                        return '';
                    }
                    for (var i = 0; i < names.length; i++) {
                        var n = names[i];
                        if (!n) continue;
                        if (obj[n] !== undefined && obj[n] !== null && obj[n] !== '') {
                            return obj[n];
                        }
                        var lo = String(n).toLowerCase();
                        if (obj[lo] !== undefined && obj[lo] !== null && obj[lo] !== '') {
                            return obj[lo];
                        }
                        var up = String(n).toUpperCase();
                        if (obj[up] !== undefined && obj[up] !== null && obj[up] !== '') {
                            return obj[up];
                        }
                    }
                    return '';
                }

                if ($.fn.DataTable.isDataTable($table[0])) {
                    var schoolDataKeys = ['bi_school_name', 'school_name', 'school'];
                    var deptDataKeys = ['bi_department_name', 'department_name', 'dept_name', 'department'];
                    var progDataKeys = ['bi_program_name', 'program_name', 'prog_name', 'program'];
                    var countDataKeys = ['total_applied_applicants', 'TOTAL_APPLIED_APPLICANTS', 'total_count'];

                    try {
                        var dt = $table.DataTable();
                        var dataSrcs = [];

                        if (dt.columns && typeof dt.columns().every === 'function') {
                            dt.columns().every(function() {
                                var src = typeof this.dataSrc === 'function' ? this.dataSrc() : null;
                                if (typeof src === 'string' || typeof src === 'number') {
                                    dataSrcs.push(src);
                                } else {
                                    dataSrcs.push(null);
                                }
                            });
                        }

                        function findIdxBySrc(predict) {
                            for (var c = 0; c < dataSrcs.length; c++) {
                                var d = dataSrcs[c];
                                if (d === null || d === '') {
                                    continue;
                                }
                                var ds = String(d).toLowerCase();
                                if (predict(ds)) {
                                    return c;
                                }
                            }
                            return -1;
                        }

                        var schoolIdx = dataSrcs.length ? findIdxBySrc(function(l) {
                            return l.indexOf('school') >= 0 && l.indexOf('session') < 0;
                        }) : -1;
                        var deptIdx = dataSrcs.length ? findIdxBySrc(function(l) {
                            return l.indexOf('department') >= 0 || l === 'dept_name' || l === 'dept';
                        }) : -1;
                        var progIdx = dataSrcs.length ? findIdxBySrc(function(l) {
                            return l.indexOf('program') >= 0 || l === 'prog_name' || l === 'prog';
                        }) : -1;
                        var countIdx = dataSrcs.length ? findIdxBySrc(function(l) {
                            return l.indexOf('total_applied') >= 0 || l.indexOf('applied_applicant') >= 0 || l === 'total_count';
                        }) : -1;

                        function colFromRow(rowData, idx) {
                            if (idx < 0 || idx >= dataSrcs.length || dataSrcs[idx] === null) {
                                return '';
                            }
                            var key = dataSrcs[idx];
                            if (rowData && (rowData[key] !== undefined && rowData[key] !== null)) {
                                return rowData[key];
                            }
                            var lo = String(key).toLowerCase();
                            if (rowData && rowData[lo] !== undefined) {
                                return rowData[lo];
                            }
                            return '';
                        }

                        dt.rows({
                            search: 'applied',
                            page: 'all'
                        }).every(function() {
                            var rowData = this.data();
                            var rowSchool = cellText(
                                schoolIdx >= 0 ? colFromRow(rowData, schoolIdx) : pickField(rowData, schoolDataKeys)
                            );
                            if (biNormalizeSchoolName(rowSchool) !== wantedSchool) {
                                return;
                            }
                            var dept = cellText(
                                deptIdx >= 0 ? colFromRow(rowData, deptIdx) : pickField(rowData, deptDataKeys)
                            ) || 'No department';
                            var prog = cellText(
                                progIdx >= 0 ? colFromRow(rowData, progIdx) : pickField(rowData, progDataKeys)
                            ) || 'No program';
                            var rawCount = cellText(
                                countIdx >= 0 ? colFromRow(rowData, countIdx) : pickField(rowData, countDataKeys)
                            ).replace(/,/g, '');
                            var count = Number(rawCount) || 0;
                            var key = dept + '||' + prog;
                            if (!groups[key]) {
                                groups[key] = {
                                    department: dept,
                                    program: prog,
                                    count: 0
                                };
                            }
                            groups[key].count += count;
                        });

                        return Object.keys(groups).map(function(k) {
                            return groups[k];
                        }).sort(function(a, b) {
                            return (b.count - a.count) || a.department.localeCompare(b.department) || a.program.localeCompare(b.program);
                        });
                    } catch (err) {
                        /* fall through to DOM path */
                    }
                }

                var headers = [];
                $table.find('thead th').each(function() {
                    headers.push($.trim($(this).text()).toLowerCase());
                });

                var schoolIdx = headers.findIndex(function(h) {
                    return h === 'school' || h.indexOf('school') >= 0;
                });
                var deptIdx = headers.findIndex(function(h) {
                    return h.indexOf('department') >= 0 || h === 'dept';
                });
                var progIdx = headers.findIndex(function(h) {
                    return h.indexOf('program') >= 0 || h === 'prog';
                });
                var countIdx = headers.findIndex(function(h) {
                    return h.indexOf('total applied applicants') >= 0 || h.indexOf('total count') >= 0;
                });

                if (schoolIdx < 0) schoolIdx = 0;
                if (deptIdx < 0) deptIdx = 1;
                if (progIdx < 0) progIdx = 2;
                if (countIdx < 0) countIdx = 3;

                var rows = [];
                $table.find('tbody tr').each(function() {
                    var row = [];
                    $(this).children('td').each(function() {
                        row.push($.trim($(this).text()));
                    });
                    rows.push(row);
                });

                rows.forEach(function(row) {
                    var rowSchool = cellText(row[schoolIdx]);
                    if (biNormalizeSchoolName(rowSchool) !== wantedSchool) {
                        return;
                    }
                    var dept = cellText(row[deptIdx]) || 'No department';
                    var prog = cellText(row[progIdx]) || 'No program';
                    var rawCount = cellText(row[countIdx]).replace(/,/g, '');
                    var count = Number(rawCount) || 0;
                    var key = dept + '||' + prog;
                    if (!groups[key]) {
                        groups[key] = {
                            department: dept,
                            program: prog,
                            count: 0
                        };
                    }
                    groups[key].count += count;
                });

                return Object.keys(groups).map(function(k) {
                    return groups[k];
                }).sort(function(a, b) {
                    return (b.count - a.count) || a.department.localeCompare(b.department) || a.program.localeCompare(b.program);
                });
            }

            function biRenderSchoolBreakdownModal(schoolName, total, details) {
                $('#biSchoolBreakdownTitle').text(schoolName);
                $('#biSchoolBreakdownSubtitle').text('Program wise count · Total ' + total);
                var rowsHtml = '';
                if (details && details.length) {
                    details.forEach(function(row) {
                        var count = Number(row.count || 0);
                        rowsHtml += '<tr>' +
                            '<td>' + $('<div>').text(row.program || 'No program').html() + '</td>' +
                            '<td class="text-right font-weight-bold">' + count.toLocaleString('en-US', {maximumFractionDigits: 0}) + '</td>' +
                            '</tr>';
                    });
                } else {
                    rowsHtml = '<tr><td colspan="2" class="text-muted text-center">No detail found.</td></tr>';
                }
                $('#biSchoolBreakdownRows').html(rowsHtml);
                $('#biSchoolBreakdownModal').modal('show');
            }

            function biOpenSchoolBreakdown($card) {
                var schoolName = $card.attr('data-school-name') || 'School';
                var total      = $card.attr('data-school-total') || '0';
                var tabKey     = $card.attr('data-tab-key') || '';
                var schoolId   = $card.attr('data-school-id') || '';

                // Admission Grand Summary: always fetch fresh from server via AJAX.
                if (tabKey === 'admission_grand_summary' && schoolId) {
                    $('#biSchoolBreakdownTitle').text(schoolName);
                    $('#biSchoolBreakdownSubtitle').text('Program wise count · Loading…');
                    $('#biSchoolBreakdownRows').html('<tr><td colspan="2" class="text-center text-muted"><span class="spinner-border spinner-border-sm"></span> Loading…</td></tr>');
                    $('#biSchoolBreakdownModal').modal('show');

                    var params = {school_id: schoolId};
                    // Pass active date filter so we query the correct snapshot.
                    var dateVal = $('#bi_filter_cmp_a').val() || $('#bi_filter_date_to').val() || $('#bi_filter_date_from').val();
                    if (dateVal) {
                        params.date = dateVal;
                    }
                    var sessVal = $('#bi_filter_sess').val();
                    if (sessVal) {
                        params.sess_id = sessVal;
                    }

                    $.get('{{ route('reports/bi-grand-summary-school-breakdown') }}', params)
                        .done(function(resp) {
                            biRenderSchoolBreakdownModal(schoolName, total, resp.details || []);
                        })
                        .fail(function() {
                            biRenderSchoolBreakdownModal(schoolName, total, []);
                        });
                    return;
                }

                // Other tabs: fallback to pre-loaded details.
                var details = [];
                if (tabKey !== 'admission_grand_summary') {
                    details = biExtractSchoolDetailsFromGrid(schoolName);
                }
                biRenderSchoolBreakdownModal(schoolName, total, details);
            }

            $(document).on('click', '.bi-school-card-clickable', function() {
                biOpenSchoolBreakdown($(this));
            });

            $(document).on('keydown', '.bi-school-card-clickable', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    biOpenSchoolBreakdown($(this));
                }
            });

            $(document).on('shown.bs.tab', '#biReportTabs a[data-toggle="pill"]', function(e) {
                var target = $(e.target).attr('href');
                initBiTable($(target).find('table.bi-datatable'));
                var key = (target || '').replace('#tab-', '');
                if (key) {
                    $('#bi_active_tab').val(key);
                    $('.bi-top-summary-pane').addClass('d-none');
                    $('.bi-top-summary-pane[data-summary-tab="' + key + '"]').removeClass('d-none');
                    setTimeout(function() {
                        biInitChartsForTab(key);
                        if (window.biChartInstances) {
                            Object.keys(window.biChartInstances).forEach(function(k) {
                                var c = window.biChartInstances[k];
                                if (c && typeof c.resize === 'function') {
                                    c.resize();
                                }
                            });
                        }
                    }, 50);
                }
            });

            biMoveSummaryBlocksToTop();

            $('.tab-pane.active table.bi-datatable').each(function() {
                initBiTable($(this));
            });

            var firstTab = @json($firstTabKey);
            if (firstTab) {
                setTimeout(function() {
                    biInitChartsForTab(firstTab);
                    if (window.biChartInstances) {
                        Object.keys(window.biChartInstances).forEach(function(k) {
                            var c = window.biChartInstances[k];
                            if (c && typeof c.resize === 'function') {
                                c.resize();
                            }
                        });
                    }
                }, 50);
            }

            $(window).on('resize', function() {
                if (window.biChartInstances) {
                    Object.keys(window.biChartInstances).forEach(function(k) {
                        var c = window.biChartInstances[k];
                        if (c && typeof c.resize === 'function') {
                            c.resize();
                        }
                    });
                }
            });
        });
    </script>
@endsection
