@extends('layouts.app')
@section('title') Medical Premium Report @stop

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');

    /* ════════════════════════════════════════════════
   DESIGN TOKENS
════════════════════════════════════════════════ */
    :root {
        --brand: #4454c3;
        --brand-dark: #2f3d9e;
        --brand-deeper: #1e2875;
        --brand-mid: #6270d4;
        --brand-light: #eef0fb;
        --brand-lighter: #f5f6fd;
        --brand-glow: rgba(68, 84, 195, 0.15);
        --brand-glow-md: rgba(68, 84, 195, 0.25);

        --paid-bg: #d1fae5;
        --paid-fg: #065f46;
        --paid-dot: #10b981;
        --unpaid-bg: #fee2e2;
        --unpaid-fg: #991b1b;
        --unpaid-dot: #ef4444;
        --active-bg: #e0e7ff;
        --active-fg: #3730a3;
        --left-bg: #fef3c7;
        --left-fg: #92400e;
        --struck-bg: #ffe4e6;
        --struck-fg: #9f1239;
        --dormant-bg: #f3f4f6;
        --dormant-fg: #374151;
        --grad-bg: #ecfdf5;
        --grad-fg: #065f46;

        --surface: #ffffff;
        --surface-2: #f8f9fc;
        --surface-3: #f0f2f9;
        --border: #e4e7f0;
        --border-focus: var(--brand);
        --text-h: #111827;
        --text-body: #374151;
        --text-muted: #6b7280;
        --text-faint: #9ca3af;

        --r-sm: 6px;
        --r-md: 10px;
        --r-lg: 14px;
        --r-xl: 18px;
        --r-2xl: 24px;

        --ease: cubic-bezier(.4, 0, .2, 1);
        --t-sm: .15s;
        --t-md: .22s;

        --shadow-xs: 0 1px 2px rgba(0, 0, 0, .06);
        --shadow-sm: 0 1px 4px rgba(0, 0, 0, .08), 0 1px 2px rgba(0, 0, 0, .04);
        --shadow-md: 0 4px 16px rgba(68, 84, 195, .1), 0 1px 4px rgba(0, 0, 0, .06);
        --shadow-lg: 0 8px 32px rgba(68, 84, 195, .14), 0 2px 8px rgba(0, 0, 0, .06);
        --shadow-brand: 0 4px 14px rgba(68, 84, 195, .35);
    }

    /* ════════════════════════════════════════════════
   RESET / BASE
════════════════════════════════════════════════ */
    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: var(--surface-2);
        color: var(--text-body);
        -webkit-font-smoothing: antialiased;
    }

    /* ════════════════════════════════════════════════
   PAGE WRAPPER
════════════════════════════════════════════════ */
    .report-page {
        padding: 24px 20px 40px;
        max-width: 100%;
    }

    /* ════════════════════════════════════════════════
   TOP HERO BANNER
════════════════════════════════════════════════ */
    .page-hero {
        position: relative;
        background: linear-gradient(135deg, var(--brand-deeper) 0%, var(--brand) 55%, var(--brand-mid) 100%);
        border-radius: var(--r-xl);
        padding: 28px 32px;
        margin-bottom: 24px;
        overflow: hidden;
        box-shadow: var(--shadow-brand);
    }

    .page-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 80% -20%, rgba(255, 255, 255, .12) 0%, transparent 55%),
            radial-gradient(circle at 10% 110%, rgba(255, 255, 255, .07) 0%, transparent 50%);
        pointer-events: none;
    }

    .page-hero::after {
        content: '';
        position: absolute;
        right: -40px;
        top: -40px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        border: 40px solid rgba(255, 255, 255, .06);
        pointer-events: none;
    }

    .hero-inner {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .hero-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .hero-icon {
        width: 52px;
        height: 52px;
        background: rgba(255, 255, 255, .18);
        border: 1.5px solid rgba(255, 255, 255, .25);
        border-radius: var(--r-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
        flex-shrink: 0;
    }

    .hero-icon svg {
        width: 26px;
        height: 26px;
        fill: #fff;
    }

    .hero-text h1 {
        font-size: 1.4rem;
        font-weight: 700;
        color: #fff;
        margin: 0 0 4px;
        letter-spacing: -.4px;
        line-height: 1.2;
    }

    .hero-text p {
        font-size: .82rem;
        color: rgba(255, 255, 255, .72);
        margin: 0;
        font-weight: 400;
    }

    /* ════════════════════════════════════════════════
   SUMMARY STAT PILLS (above filter)
════════════════════════════════════════════════ */
    .stat-row {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .stat-pill {
        flex: 1;
        min-width: 140px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-lg);
        padding: 14px 18px;
        box-shadow: var(--shadow-xs);
        display: flex;
        align-items: center;
        gap: 12px;
        transition: box-shadow var(--t-md) var(--ease), transform var(--t-md) var(--ease);
    }

    .stat-pill:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .stat-pill .pill-icon {
        width: 38px;
        height: 38px;
        border-radius: var(--r-md);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-pill .pill-icon svg {
        width: 18px;
        height: 18px;
    }

    .stat-pill .pill-label {
        font-size: .72rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 2px;
    }

    .stat-pill .pill-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-h);
        font-family: 'JetBrains Mono', monospace;
        line-height: 1;
    }

    .pill-total .pill-icon {
        background: var(--brand-light);
    }

    .pill-total .pill-icon svg {
        fill: var(--brand);
    }

    .pill-paid .pill-icon {
        background: #d1fae5;
    }

    .pill-paid .pill-icon svg {
        fill: #059669;
    }

    .pill-unpaid .pill-icon {
        background: #fee2e2;
    }

    .pill-unpaid .pill-icon svg {
        fill: #dc2626;
    }


    /* ════════════════════════════════════════════════
   FILTER SECTION
════════════════════════════════════════════════ */
    .section-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-xl);
        box-shadow: var(--shadow-sm);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 24px;
        border-bottom: 1px solid var(--border);
        background: var(--brand-lighter);
    }

    .section-head-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-head-icon {
        width: 32px;
        height: 32px;
        background: var(--brand);
        border-radius: var(--r-sm);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .section-head-icon svg {
        width: 15px;
        height: 15px;
        fill: #fff;
    }

    .section-head h2 {
        font-size: .9rem;
        font-weight: 700;
        color: var(--text-h);
        margin: 0;
        letter-spacing: -.2px;
    }

    .section-head p {
        font-size: .75rem;
        color: var(--text-muted);
        margin: 0;
    }

    .section-body {
        padding: 22px 24px 20px;
    }

    /* ════════════════════════════════════════════════
   FILTER GRID
════════════════════════════════════════════════ */
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
    }

    @media (max-width: 1100px) {
        .filter-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }
    }

    .field-wrap {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .field-wrap label {
        font-size: .74rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .55px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .field-wrap label svg {
        width: 12px;
        height: 12px;
        fill: var(--brand);
        opacity: .7;
    }

    .select-wrap {
        position: relative;
    }

    .select-wrap select {
        width: 100%;
        height: 42px;
        padding: 0 36px 0 13px;
        border: 1.5px solid var(--border);
        border-radius: var(--r-md);
        background: var(--surface-2);
        font-size: .86rem;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 500;
        color: var(--text-body);
        appearance: none;
        -webkit-appearance: none;
        cursor: pointer;
        transition: border-color var(--t-sm) var(--ease),
            box-shadow var(--t-sm) var(--ease),
            background var(--t-sm) var(--ease);
    }

    .select-wrap select:focus {
        outline: none;
        border-color: var(--brand);
        background: var(--surface);
        box-shadow: 0 0 0 3.5px var(--brand-glow);
    }

    .select-wrap select:hover:not(:focus) {
        border-color: #b0b8d8;
        background: var(--surface);
    }

    .select-chevron {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: var(--text-muted);
    }

    .select-chevron svg {
        width: 14px;
        height: 14px;
        display: block;
    }

    /* ════════════════════════════════════════════════
   FILTER ACTIONS
════════════════════════════════════════════════ */
    .filter-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 20px;
        padding-top: 18px;
        border-top: 1px solid var(--border);
    }

    .filter-hint {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: .76rem;
        color: var(--text-faint);
        font-style: italic;
    }

    .filter-hint svg {
        width: 13px;
        height: 13px;
        fill: var(--text-faint);
    }

    .action-btns {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 40px;
        padding: 0 20px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .85rem;
        font-weight: 600;
        border: none;
        border-radius: var(--r-md);
        cursor: pointer;
        transition: all var(--t-md) var(--ease);
        text-decoration: none;
        white-space: nowrap;
    }

    .btn svg {
        width: 15px;
        height: 15px;
        flex-shrink: 0;
    }

    .btn-primary {
        background: var(--brand);
        color: #fff;
        box-shadow: 0 2px 8px var(--brand-glow-md);
    }

    .btn-primary:hover:not(:disabled) {
        background: var(--brand-dark);
        box-shadow: var(--shadow-brand);
        transform: translateY(-1px);
    }

    .btn-primary:active:not(:disabled) {
        transform: translateY(0);
    }

    .btn-ghost {
        background: transparent;
        color: var(--text-muted);
        border: 1.5px solid var(--border);
    }

    .btn-ghost:hover:not(:disabled) {
        background: var(--surface-3);
        border-color: #b8bdd6;
        color: var(--text-body);
    }

    .btn:disabled {
        opacity: .5;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* ════════════════════════════════════════════════
   TABLE SECTION
════════════════════════════════════════════════ */
    .table-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        padding: 14px 24px;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
    }

    .table-section-head h2 {
        font-size: .9rem;
        font-weight: 700;
        color: var(--text-h);
        margin: 0;
    }

    .table-section-head p {
        font-size: .75rem;
        color: var(--text-muted);
        margin: 0;
    }

    .export-hint {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: .75rem;
        color: var(--text-faint);
    }

    .export-hint svg {
        width: 13px;
        height: 13px;
        fill: var(--text-faint);
    }

    .table-wrapper {
        padding: 16px 20px 20px;
        overflow: hidden;
    }

    /* ════════════════════════════════════════════════
   DATATABLE OVERRIDES
════════════════════════════════════════════════ */
    .dataTables_wrapper {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* Top toolbar */
    .dataTables_wrapper .dt-buttons {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 14px;
    }

    .dt-button {
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        background: var(--brand) !important;
        color: #fff !important;
        border: none !important;
        height: 34px !important;
        padding: 0 14px !important;
        font-size: .78rem !important;
        font-weight: 600 !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        border-radius: var(--r-sm) !important;
        cursor: pointer !important;
        box-shadow: 0 1px 4px var(--brand-glow) !important;
        transition: background var(--t-sm) var(--ease), transform var(--t-sm) var(--ease) !important;
        letter-spacing: .2px;
    }

    .dt-button:hover {
        background: var(--brand-dark) !important;
        transform: translateY(-1px) !important;
    }

    .dt-button:active {
        transform: none !important;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 12px;
    }

    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        font-size: .8rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1.5px solid var(--border) !important;
        border-radius: var(--r-sm) !important;
        padding: 5px 10px !important;
        font-size: .8rem !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        color: var(--text-body);
        background: var(--surface-2);
        transition: border-color var(--t-sm), box-shadow var(--t-sm);
    }

    .dataTables_wrapper .dataTables_filter input:focus,
    .dataTables_wrapper .dataTables_length select:focus {
        outline: none !important;
        border-color: var(--brand) !important;
        box-shadow: 0 0 0 3px var(--brand-glow) !important;
    }

    /* ── THE TABLE ─────────────────────────────── */
    table#data-table {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        width: 100% !important;
    }

    /* Head */
    table#data-table thead tr th {
        background: var(--brand) !important;
        color: #fff !important;
        font-size: .7rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: .65px !important;
        padding: 13px 14px !important;
        border: none !important;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    table#data-table thead tr th:first-child {
        border-radius: var(--r-md) 0 0 0 !important;
    }

    table#data-table thead tr th:last-child {
        border-radius: 0 var(--r-md) 0 0 !important;
    }

    /* Sort icons */
    table#data-table thead .sorting::after,
    table#data-table thead .sorting_asc::after,
    table#data-table thead .sorting_desc::after {
        color: rgba(255, 255, 255, .55) !important;
    }

    /* Body rows */
    table#data-table tbody tr td {
        padding: 11px 14px !important;
        font-size: .82rem !important;
        color: var(--text-body) !important;
        border-bottom: 1px solid var(--border) !important;
        border-top: none !important;
        vertical-align: middle !important;
        white-space: nowrap !important;
        background: var(--surface);
        transition: background var(--t-sm) var(--ease);
    }

    /* ID / code columns in mono */
    table#data-table tbody tr td:nth-child(1),
    table#data-table tbody tr td:nth-child(4),
    table#data-table tbody tr td:nth-child(5),
    table#data-table tbody tr td:nth-child(6),
    table#data-table tbody tr td:nth-child(9),
    table#data-table tbody tr td:nth-child(12) {
        font-family: 'JetBrains Mono', monospace !important;
        font-size: .77rem !important;
        color: var(--text-muted) !important;
    }

    /* Name column bold */
    table#data-table tbody tr td:nth-child(2) {
        font-weight: 600 !important;
        color: var(--text-h) !important;
    }

    /* Medical amount */
    table#data-table tbody tr td:nth-child(14) {
        font-family: 'JetBrains Mono', monospace !important;
        font-weight: 600 !important;
        color: var(--brand-dark) !important;
    }

    /* Alignment helpers */
    table#data-table th.text-center,
    table#data-table td.text-center {
        text-align: center !important;
    }

    table#data-table th.text-right,
    table#data-table td.text-right {
        text-align: right !important;
    }

    /* Dept / Program can wrap — everything else nowrap */
    table#data-table tbody tr td:nth-child(10),
    table#data-table tbody tr td:nth-child(11) {
        white-space: normal !important;
        min-width: 160px;
        line-height: 1.4;
    }

    /* Zebra */
    table#data-table tbody tr:nth-child(even) td {
        background: var(--surface-2) !important;
    }

    /* Hover */
    table#data-table tbody tr:hover td {
        background: var(--brand-light) !important;
    }

    /* Last row no border */
    table#data-table tbody tr:last-child td {
        border-bottom: none !important;
    }

    /* Pagination */
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 14px !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 5px 11px !important;
        border-radius: var(--r-sm) !important;
        font-size: .78rem !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-weight: 500 !important;
        color: var(--text-muted) !important;
        border: 1.5px solid transparent !important;
        margin: 0 2px !important;
        transition: all var(--t-sm) var(--ease) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--brand-light) !important;
        border-color: var(--brand) !important;
        color: var(--brand) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: var(--brand) !important;
        border-color: var(--brand) !important;
        color: #fff !important;
        box-shadow: 0 2px 6px var(--brand-glow-md) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: .3 !important;
        cursor: not-allowed !important;
    }

    .dataTables_wrapper .dataTables_info {
        font-size: .78rem !important;
        color: var(--text-faint) !important;
        padding-top: 14px !important;
    }

    /* Processing */
    .dataTables_processing {
        background: rgba(255, 255, 255, .95) !important;
        border: 1px solid var(--border) !important;
        border-radius: var(--r-lg) !important;
        box-shadow: var(--shadow-lg) !important;
        padding: 24px 36px !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: .84rem !important;
        color: var(--brand) !important;
        font-weight: 500 !important;
    }

    /* Scrollbar */
    .dataTables_scrollBody {
        border: none !important;
    }

    .dataTables_scrollBody::-webkit-scrollbar {
        height: 5px;
        width: 5px;
    }

    .dataTables_scrollBody::-webkit-scrollbar-track {
        background: var(--surface-2);
    }

    .dataTables_scrollBody::-webkit-scrollbar-thumb {
        background: #c5cadc;
        border-radius: 3px;
    }

    .dataTables_scrollBody::-webkit-scrollbar-thumb:hover {
        background: var(--brand);
    }

    /* ════════════════════════════════════════════════
   CHIPS / BADGES
════════════════════════════════════════════════ */
    .chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 99px;
        font-size: .71rem;
        font-weight: 700;
        font-family: 'Plus Jakarta Sans', sans-serif;
        letter-spacing: .25px;
        white-space: nowrap;
    }

    .chip .cdot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .chip-paid {
        background: var(--paid-bg);
        color: var(--paid-fg);
    }

    .chip-paid .cdot {
        background: var(--paid-dot);
    }

    .chip-unpaid {
        background: var(--unpaid-bg);
        color: var(--unpaid-fg);
    }

    .chip-unpaid .cdot {
        background: var(--unpaid-dot);
    }

    .chip-active {
        background: var(--active-bg);
        color: var(--active-fg);
    }

    .chip-active .cdot {
        background: #6366f1;
    }

    .chip-left {
        background: var(--left-bg);
        color: var(--left-fg);
    }

    .chip-left .cdot {
        background: #f59e0b;
    }

    .chip-struck {
        background: var(--struck-bg);
        color: var(--struck-fg);
    }

    .chip-struck .cdot {
        background: #f43f5e;
    }

    .chip-dormant {
        background: var(--dormant-bg);
        color: var(--dormant-fg);
    }

    .chip-dormant .cdot {
        background: #9ca3af;
    }

    .chip-grad {
        background: var(--grad-bg);
        color: var(--grad-fg);
    }

    .chip-grad .cdot {
        background: #10b981;
    }

    .chip-default {
        background: var(--surface-3);
        color: var(--text-muted);
    }

    /* Gender badges */
    .gender-m {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: .78rem;
        font-weight: 600;
        color: #2563eb;
    }

    .gender-f {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: .78rem;
        font-weight: 600;
        color: #db2777;
    }

    /* Amount */
    .amount-val {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 600;
        font-size: .82rem;
        color: var(--brand-dark);
    }

    .amount-val .currency {
        font-size: .68rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    /* ════════════════════════════════════════════════
   ENTRY ANIMATION
════════════════════════════════════════════════ */
    .fade-in {
        animation: fadeUp .4s var(--ease) both;
    }

    .fade-in:nth-child(1) {
        animation-delay: .05s;
    }

    .fade-in:nth-child(2) {
        animation-delay: .10s;
    }

    .fade-in:nth-child(3) {
        animation-delay: .15s;
    }

    .fade-in:nth-child(4) {
        animation-delay: .20s;
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .hero-anim {
        animation: fadeUp .35s var(--ease) both;
    }
</style>

@section('content')
<div class="report-page">

    {{-- ══ HERO BANNER ══════════════════════════════════════ --}}
    <div class="page-hero hero-anim">
        <div class="hero-inner">
            <div class="hero-left">
                <div class="hero-icon">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-7 3a1 1 0 0 1 1 1v2h2a1 1 0 0 1 0 2h-2v2a1 1 0 0 1-2 0v-2H9a1 1 0 0 1 0-2h2V7a1 1 0 0 1 1-1zm-5 9h10a1 1 0 0 1 0 2H7a1 1 0 0 1 0-2z" />
                    </svg>
                </div>
                <div class="hero-text">
                    <h1>Medical Premium Report</h1>
                    <p>Student medical insurance premium overview &amp; challan status</p>
                </div>
            </div>

        </div>
    </div>

    {{-- ══ SUMMARY STATS ════════════════════════════════════ --}}
    <div class="stat-row">
        <div class="stat-pill pill-total fade-in">
            <div class="pill-icon">
                <svg viewBox="0 0 24 24">
                    <path
                        d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                </svg>
            </div>
            <div>
                <div class="pill-label">Total Records</div>
                <div class="pill-value" id="stat-total">—</div>
            </div>
        </div>
        <div class="stat-pill pill-paid fade-in">
            <div class="pill-icon">
                <svg viewBox="0 0 24 24">
                    <path
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                </svg>
            </div>
            <div>
                <div class="pill-label">Paid</div>
                <div class="pill-value" id="stat-paid">—</div>
            </div>
        </div>
        <div class="stat-pill pill-unpaid fade-in">
            <div class="pill-icon">
                <svg viewBox="0 0 24 24">
                    <path
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                </svg>
            </div>
            <div>
                <div class="pill-label">Unpaid</div>
                <div class="pill-value" id="stat-unpaid">—</div>
            </div>
        </div>
    </div>

    {{-- ══ FILTER CARD ══════════════════════════════════════ --}}
    <div class="section-card fade-in">
        <div class="section-head">
            <div class="section-head-left">
                <div class="section-head-icon">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M4.25 5.61C6.27 8.2 10 13 10 13v6c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-6s3.72-4.8 5.74-7.39A.998.998 0 0 0 18.95 4H5.04a1 1 0 0 0-.79 1.61z" />
                    </svg>
                </div>
                <div>
                    <h2>Filter Options</h2>
                    <p>Narrow down results by selecting criteria below</p>
                </div>
            </div>
            <span style="font-size:.72rem;color:var(--text-faint);font-weight:500;">* All fields optional</span>
        </div>

        <div class="section-body">
            <form id="filterForm" method="POST" action="">
                @csrf
                <div class="filter-grid">

                    <div class="field-wrap">
                        <label for="session_sel">
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z" />
                            </svg>
                            Academic Session
                        </label>
                        <div class="select-wrap">
                            <select name="session_id" id="session_sel">
                                <option value="">— Select Session —</option>
                                @foreach ($sessions as $key => $session)
                                    <option value="{{ $key }}">{{ $session }}</option>
                                @endforeach
                            </select>
                            <span class="select-chevron"><svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" />
                                </svg></span>
                        </div>
                    </div>

                    <div class="field-wrap">
                        <label for="admission_type">
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                            </svg>
                            Admission Type
                        </label>
                        <div class="select-wrap">
                            <select name="admission_type" id="admission_type">
                                <option value="">— Select Type —</option>
                                <option value="Existing">Existing</option>
                                <option value="New Admission FALL">New Admission — FALL</option>
                                <option value="New Admission SPRING">New Admission — SPRING</option>
                                <option value="New Admission SUMMER">New Admission — SUMMER</option>
                            </select>
                            <span class="select-chevron"><svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" />
                                </svg></span>
                        </div>
                    </div>

                    <div class="field-wrap">
                        <label for="student_status">
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" />
                            </svg>
                            Student Status
                        </label>
                        <div class="select-wrap">
                            <select name="student_status" id="student_status">
                                <option value="">— Select Status —</option>
                                <option value="Active">Active</option>
                                <option value="Left">Left</option>
                                <option value="Struck off">Struck Off</option>
                                <option value="Dormant">Dormant</option>
                                <option value="Expected-Graduate">Expected Graduate</option>
                            </select>
                            <span class="select-chevron"><svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" />
                                </svg></span>
                        </div>
                    </div>

                    <div class="field-wrap">
                        <label for="challan_type">
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" />
                            </svg>
                            Challan Status
                        </label>
                        <div class="select-wrap">
                            <select name="challan_type" id="challan_type">
                                <option value="">— Select Status —</option>
                                <option value="PAID">✓ Paid</option>
                                <option value="UNPAID">✗ Unpaid</option>
                            </select>
                            <span class="select-chevron"><svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" />
                                </svg></span>
                        </div>
                    </div>

                    <div class="field-wrap date-filter-wrap" style="display: none;">
                        <label for="from_date">
                            <svg viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z" />
                            </svg>
                            Paid From
                        </label>
                        <input type="date" name="from_date" id="from_date" class="form-control"
                            style="width: 100%; border-radius: var(--r-sm); border: 1.5px solid var(--border); padding: 8px; font-family: 'Plus Jakarta Sans', sans-serif; height: 38px;">
                    </div>

                    <div class="field-wrap date-filter-wrap" style="display: none;">
                        <label for="to_date">
                            <svg viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z" />
                            </svg>
                            Paid To
                        </label>
                        <input type="date" name="to_date" id="to_date" class="form-control"
                            style="width: 100%; border-radius: var(--r-sm); border: 1.5px solid var(--border); padding: 8px; font-family: 'Plus Jakarta Sans', sans-serif; height: 38px;">
                    </div>
                </div>

                <div class="filter-actions">
                    <div class="filter-hint">
                        <svg viewBox="0 0 24 24">
                            <path
                                d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                        </svg>
                        Leave fields empty to load all records
                    </div>
                    <div class="action-btns">
                        <button type="button" id="clear" class="btn btn-ghost"
                            onclick="document.getElementById('filterForm').reset(); $('#challan_type').trigger('change'); $('#session_sel').trigger('change'); LoadReport();">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" />
                            </svg>
                            Reset Filters
                        </button>
                        <button type="button" id="filterButton" class="btn btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35" />
                            </svg>
                            Apply &amp; Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ══ TABLE CARD ═══════════════════════════════════════ --}}
    <div class="section-card fade-in">
        <div class="table-section-head">
            <div>
                <h2>Report Results</h2>
                <!-- <p id="table-subtitle">Loading data…</p> -->
            </div>
            <!-- <div class="export-hint">
                <svg viewBox="0 0 24 24">
                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zm-8 2V5h2v6h1.17L12 13.17 9.83 11H11zm-6 7h14v2H5z" />
                </svg>
                Use Excel / PDF / Print buttons below to export
            </div> -->
        </div>
        <div class="table-wrapper">
            <table id="data-table" class="table display" style="width:100%">
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Student Name</th>
                        <th>Gender</th>
                        <th>CNIC</th>
                        <th>Date of Birth</th>
                        <th>Age</th>
                        <th>Student Status</th>
                        <th>Semester Status</th>
                        <th>School</th>
                        <th>Program</th>
                        <th>Challan Status</th>
                        <th>Medical Amount</th>
                        <th>Admission Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>
@stop

@section('javascript')
<script>
    $(document).ready(function () {
        checkSession();

        $('#session_sel').on('change', function () {
            checkSession();
        });

        $('#challan_type').on('change', function () {
            if ($(this).val() === 'PAID') {
                $('.date-filter-wrap').show();
            } else {
                $('.date-filter-wrap').hide();
                $('#from_date').val('');
                $('#to_date').val('');
            }
        });

        LoadReport();
        $(document).on('click', '#filterButton', function () { LoadReport(); });
    });

    function checkSession() {
        if (!$('#session_sel').val()) {
            $('#admission_type, #student_status, #challan_type, #from_date, #to_date').prop('disabled', true);
            $('#filterButton').prop('disabled', true);
        } else {
            $('#admission_type, #student_status, #challan_type, #from_date, #to_date').prop('disabled', false);
            $('#filterButton').prop('disabled', false);
        }
    }

    function getFilterParams() {
        return {
            session_id: $('#session_sel').val(),
            admission_type: $('#admission_type').val(),
            student_status: $('#student_status').val(),
            challan_type: $('#challan_type').val(),
            from_date: $('#from_date').val(),
            to_date: $('#to_date').val(),
            _token: '{{ csrf_token() }}'
        };
    }

    /* ── Chip renderer helpers ──────────────────────────────────── */
    function challanChip(val) {
        if (!val || val === '—') return '<span class="chip chip-default">—</span>';
        if (val === 'PAID') return '<span class="chip chip-paid"><span class="cdot"></span>Paid</span>';
        if (val === 'UNPAID') return '<span class="chip chip-unpaid"><span class="cdot"></span>Unpaid</span>';
        return `<span class="chip chip-default">${val}</span>`;
    }
    function statusChip(val) {
        if (!val || val === '—') return '<span class="chip chip-default">—</span>';
        const map = {
            'Active': 'chip-active',
            'Left': 'chip-left',
            'Struck off': 'chip-struck',
            'Struck Off': 'chip-struck',
            'Dormant': 'chip-dormant',
            'Expected-Graduate': 'chip-grad',
            'Expected Graduate': 'chip-grad',
        };
        const cls = map[val] || 'chip-default';
        return `<span class="chip ${cls}"><span class="cdot"></span>${val}</span>`;
    }
    function genderIcon(val) {
        if (!val) return '—';
        const v = val.toLowerCase();
        if (v === 'male' || v === 'm') return '<span class="gender-m">♂ Male</span>';
        if (v === 'female' || v === 'f') return '<span class="gender-f">♀ Female</span>';
        return val;
    }
    function dobFmt(val) {
        if (!val || val === '—') return '<span style="color:var(--text-faint)">—</span>';
        const months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        const d = new Date(val);
        if (isNaN(d.getTime())) return val;
        const day = String(d.getDate()).padStart(2, '0');
        const month = months[d.getMonth()];
        const year = d.getFullYear();
        return `${day}-${month}-${year}`;
    }
    function amountFmt(val) {
        if (!val || val === '—') return '<span style="color:var(--text-faint)">—</span>';
        const num = parseFloat(val);
        if (isNaN(num)) return val;
        return `<span class="amount-val"><span class="currency">PKR</span>${num.toLocaleString('en-PK')}</span>`;
    }

    /* ── Lightweight PDF / Print via browser print dialog ──────── */
    function stripTags(html) {
        var tmp = document.createElement('div');
        tmp.innerHTML = html || '';
        return (tmp.textContent || tmp.innerText || '').trim();
    }

    function exportToPDF(dt, printOnly) {
        var headers = [];
        dt.columns().header().each(function (th) {
            headers.push($(th).text().trim());
        });

        var rows = [];
        dt.rows({ search: 'applied' }).data().each(function (row) {
            rows.push(row);
        });

        var colKeys = [
            'regno', 'name', 'gender', 'cnic', 'dob', 'age',
            'student_status', 'semester_status', 'school_code',
            'prog_name',
            'challan_status', 'medical_amount', 'std_status'
        ];

        var date = new Date().toLocaleDateString('en-PK', { day: '2-digit', month: 'short', year: 'numeric' });

        /* Build table rows HTML — plain text, no chips */
        var tbodyHtml = '';
        rows.forEach(function (row, i) {
            var bg = (i % 2 === 0) ? '#ffffff' : '#f5f6fd';
            tbodyHtml += '<tr style="background:' + bg + '">';
            colKeys.forEach(function (key, ci) {
                var val = row[key] || '—';
                val = stripTags(String(val));
                /* Format amount */
                if (key === 'medical_amount') {
                    var n = parseFloat(val);
                    if (!isNaN(n)) val = 'PKR ' + n.toLocaleString('en-PK');
                }
                var align = [2, 4, 5, 6, 7, 8, 10, 12].indexOf(ci) !== -1 ? 'center'
                    : ci === 11 ? 'right' : 'left';
                var extra = ci === 1 ? 'font-weight:600;color:#111827;'
                    : ci === 11 ? 'font-weight:600;color:#2f3d9e;'
                        : [0, 3, 4, 5, 8].indexOf(ci) !== -1 ? 'color:#6b7280;' : '';
                tbodyHtml += '<td style="text-align:' + align + ';' + extra + '">' + val + '</td>';
            });
            tbodyHtml += '</tr>';
        });

        var theadHtml = '<tr>' + headers.map(function (h) {
            return '<th>' + h + '</th>';
        }).join('') + '</tr>';

        var html = '<!DOCTYPE html><html><head><meta charset="utf-8">'
            + '<title>Medical Premium Report</title>'
            + '<style>'
            + '@page { size: A3 landscape; margin: 15mm 10mm 12mm; }'
            + 'body { font-family: Arial, sans-serif; font-size: 7.5pt; color: #374151; margin:0; }'
            + '.report-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; border-bottom:2px solid #4454c3; padding-bottom:6px; }'
            + '.report-header h1 { font-size:13pt; color:#4454c3; margin:0; font-weight:700; }'
            + '.report-header span { font-size:7.5pt; color:#6b7280; }'
            + '.report-meta { font-size:7pt; color:#6b7280; margin-bottom:8px; }'
            + 'table { width:100%; border-collapse:collapse; table-layout:fixed; }'
            + 'thead tr { background:#4454c3; color:#fff; }'
            + 'thead th { padding:5px 4px; font-size:6.5pt; font-weight:700; text-align:center; text-transform:uppercase; letter-spacing:.4px; border:none; overflow:hidden; word-wrap:break-word; }'
            + 'tbody td { padding:4px 4px; font-size:7pt; border-bottom:1px solid #e4e7f0; overflow:hidden; word-wrap:break-word; }'
            + 'tbody tr:last-child td { border-bottom:none; }'
            /* fixed column widths */
            + 'col.c0  { width:4.8% } col.c1  { width:8%   } col.c2  { width:3.5% }'
            + 'col.c3  { width:6%   } col.c4  { width:5%   } col.c5  { width:2.2% }'
            + 'col.c6  { width:4.8% } col.c7  { width:4.8% } col.c8  { width:3.5% }'
            + 'col.c9  { width:28%  } col.c10 { width:4.2% } col.c11 { width:5%   }'
            + 'col.c12 { width:5.2% }'
            + '.footer { position:fixed; bottom:0; left:0; right:0; display:flex; justify-content:space-between; font-size:6.5pt; color:#9ca3af; border-top:1px solid #e4e7f0; padding-top:4px; }'
            + '@media print { .footer { display:flex; } }'
            + '</style></head><body>'
            + '<div class="report-header">'
            + '  <h1>&#x1F4CB; Medical Premium Report</h1>'
            + '  <span>Generated: ' + date + '</span>'
            + '</div>'
            + '<div class="report-meta">Total Records: <strong>' + rows.length + '</strong> &nbsp;|&nbsp; Confidential — For Internal Use Only</div>'
            + '<table>'
            + '<colgroup>'
            + [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].map(function (i) { return '<col class="c' + i + '">'; }).join('')
            + '</colgroup>'
            + '<thead>' + theadHtml + '</thead>'
            + '<tbody>' + tbodyHtml + '</tbody>'
            + '</table>'
            + '<div class="footer"><span>Confidential — For Internal Use Only</span><span id="pg"></span></div>'
            + '<script>window.onload=function(){ var el=document.getElementById("pg"); if(el) el.textContent=""; '
            + (printOnly ? '' : 'window.print();')
            + ' };<\/script>'
            + '</body></html>';

        var win = window.open('', '_blank', 'width=1200,height=800,scrollbars=yes');
        if (!win) { alert('Pop-up blocked! Please allow pop-ups for this site.'); return; }
        win.document.write(html);
        win.document.close();
        if (!printOnly) {
            /* give browser time to render then trigger print */
            setTimeout(function () { win.focus(); win.print(); }, 800);
        }
    }

    /* ── Load / reload report ───────────────────────────────────── */
    function LoadReport() {
        const filterParams = getFilterParams();

        $('#filterButton').prop('disabled', true).html(
            '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="animation:spin .75s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Searching…'
        );
        $('#clear').prop('disabled', true);
        $('#table-subtitle').text('Loading data…');

        /* Reset stat pills */
        ['stat-total', 'stat-paid', 'stat-unpaid'].forEach(id => $('#' + id).text('—'));

        $('#data-table').DataTable({
            scrollX: true,
            responsive: true,
            processing: true,
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-2x" style="color:#4454c3;display:block;margin-bottom:8px;"></i><span>Fetching records, please wait…</span>',
                emptyTable: '<div style="padding:32px 0;color:var(--text-muted);font-size:.9rem;">No records found matching the selected filters.</div>',
                zeroRecords: '<div style="padding:32px 0;color:var(--text-muted);font-size:.9rem;">No matching records found.</div>'
            },
            deferRender: true,
            serverSide: false,
            searching: true,
            destroy: true,
            lengthChange: true,
            paging: true,
            lengthMenu: [[10, 100, 500, 1000], [10, 100, 500, 1000]],
            ajax: {
                dataType: 'json',
                method: 'post',
                url: '/api/get-medical-premium-report',
                data: filterParams,
                dataSrc: function (json) {
                    /* ── Compute summary stats from returned data ── */
                    const rows = json.data || json || [];
                    let paid = 0, unpaid = 0;
                    rows.forEach(function (r) {
                        if (r.challan_status === 'PAID') paid++;
                        if (r.challan_status === 'UNPAID') unpaid++;
                    });
                    $('#stat-total').text(rows.length.toLocaleString());
                    $('#stat-paid').text(paid.toLocaleString());
                    $('#stat-unpaid').text(unpaid.toLocaleString());
                    $('#table-subtitle').text(rows.length.toLocaleString() + ' record(s) found');
                    return rows;
                },
                complete: function () {
                    $('#filterButton').prop('disabled', false).html(
                        '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg> Apply &amp; Search'
                    );
                    $('#clear').prop('disabled', false);
                }
            },
            columns: [
                { data: "regno", defaultContent: "—", width: "100px" },
                { data: "name", defaultContent: "—", width: "160px" },
                { data: "gender", defaultContent: "—", width: "90px", className: "text-center", render: d => genderIcon(d) },
                { data: "cnic", defaultContent: "—", width: "130px" },
                { data: "dob", defaultContent: "—", width: "115px", className: "text-center", render: d => dobFmt(d) },
                { data: "age", defaultContent: "—", width: "60px", className: "text-center" },
                { data: "student_status", defaultContent: "—", width: "110px", className: "text-center", render: d => statusChip(d) },
                { data: "semester_status", defaultContent: "—", width: "120px", className: "text-center" },
                { data: "school_code", defaultContent: "—", width: "80px", className: "text-center" },
                { data: "prog_name", defaultContent: "—", width: "220px" },
                { data: "challan_status", defaultContent: "—", width: "110px", className: "text-center", render: d => challanChip(d) },
                { data: "medical_amount", defaultContent: "—", width: "130px", className: "text-right", render: d => amountFmt(d) },
                { data: "std_status", defaultContent: "—", width: "120px", className: "text-center", render: d => statusChip(d) }
            ],
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Medical Premium Report',
                    className: 'dt-button',
                    exportOptions: {
                        format: {
                            body: function (data, row, column, node) {
                                var temp = document.createElement('div');
                                temp.innerHTML = data || '';
                                return temp.textContent || temp.innerText || '';
                            }
                        }
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Medical Premium Report',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    className: 'dt-button',
                    exportOptions: {
                        format: {
                            body: function (data, row, column, node) {
                                var temp = document.createElement('div');
                                temp.innerHTML = data || '';
                                return temp.textContent || temp.innerText || '';
                            }
                        }
                    }
                },
                {
                    extend: 'print',
                    title: 'Medical Premium Report',
                    className: 'dt-button',
                    exportOptions: {
                        format: {
                            body: function (data, row, column, node) {
                                var temp = document.createElement('div');
                                temp.innerHTML = data || '';
                                return temp.textContent || temp.innerText || '';
                            }
                        }
                    }
                }
            ]
        });
    }
</script>

<style>
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
@stop