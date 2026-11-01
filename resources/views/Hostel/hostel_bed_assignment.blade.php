@extends('layouts.app')

@section('content')
    @include('components.toast-notifications')

    <style>
        .hba-page { --hba-ink: #0f172a; --hba-muted: #64748b; --hba-line: #e2e8f0; --hba-soft: #f8fafc; --hba-green: #166534; --hba-red: #991b1b; --hba-blue: #0c4a6e; }
        .hba-page .card { border: 1px solid var(--hba-line); border-radius: 12px; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04); }
        .hba-page .card-header {
            background: linear-gradient(180deg, #fff 0%, var(--hba-soft) 100%);
            border-bottom: 1px solid var(--hba-line);
            border-radius: 12px 12px 0 0;
            padding: 18px 22px;
        }
        .hba-page .card-header h4 { font-weight: 700; margin-bottom: 2px; color: var(--hba-ink); font-size: 1.15rem; letter-spacing: -0.01em; }
        .hba-page .card-header small { color: var(--hba-muted) !important; }
        .hba-page .filter-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--hba-muted);
            font-weight: 600;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .hba-page .filter-label i { width: 14px; text-align: center; opacity: .8; }
        .hba-page .form-control {
            border-radius: 10px;
            border-color: #dbe3f0;
            font-size: 13px;
            height: 40px;
            box-shadow: none;
        }
        .hba-page .form-control:focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        .hba-page .form-control:disabled { background: #f1f5f9; color: #94a3b8; }
        .hba-filters {
            background: var(--hba-soft);
            border: 1px solid var(--hba-line);
            border-radius: 12px;
            padding: 16px;
        }
        .hba-summary-card {
            display: none;
            background: #fff;
            border: 1px solid var(--hba-line);
            border-radius: 12px;
            overflow: hidden;
            margin-top: 14px;
        }
        .hba-summary-card.is-visible { display: block; }
        .hba-summary-card .hba-summary-head {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            background: var(--hba-soft);
            border-bottom: 1px solid var(--hba-line);
        }
        .hba-summary-card .hba-summary-title {
            margin: 0;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
            color: var(--hba-ink);
        }
        .hba-summary-metrics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
        }
        .hba-summary-metric {
            padding: 14px 16px;
            text-align: center;
            border-right: 1px solid var(--hba-line);
        }
        .hba-summary-metric:last-child { border-right: 0; }
        .hba-summary-metric .hba-metric-value {
            display: block;
            font-size: 1.45rem;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.02em;
            color: var(--hba-ink);
        }
        .hba-summary-metric .hba-metric-label {
            display: block;
            margin-top: 4px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
            color: var(--hba-muted);
        }
        .hba-summary-metric.is-assigned .hba-metric-value { color: #b91c1c; }
        .hba-summary-metric.is-available .hba-metric-value { color: #15803d; }
        .hba-summary-metric.is-total .hba-metric-value { color: #1d4ed8; }
        @media (max-width: 575.98px) {
            .hba-summary-metrics { grid-template-columns: 1fr; }
            .hba-summary-metric { border-right: 0; border-bottom: 1px solid var(--hba-line); }
            .hba-summary-metric:last-child { border-bottom: 0; }
        }
        .hba-paid-btns .btn {
            border-radius: 999px;
            font-weight: 600;
            padding: 7px 14px;
        }
        .hba-paid-btns .badge {
            font-weight: 700;
            min-width: 22px;
        }
        .hba-map-hint {
            color: var(--hba-muted);
            font-size: 13px;
            margin-bottom: 0;
            flex: 1;
            min-width: 180px;
        }
        .hba-map-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
            padding: 12px 14px;
            background: #fff;
            border: 1px solid var(--hba-line);
            border-radius: 12px;
        }
        .hba-map-search {
            flex: 1 1 320px;
            max-width: 480px;
        }
        .hba-search-box {
            display: flex;
            align-items: center;
            gap: 0;
            background: #f8fafc;
            border: 1px solid #dbe3f0;
            border-radius: 12px;
            overflow: hidden;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .hba-search-box:focus-within {
            border-color: #94a3b8;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08);
        }
        .hba-search-box .hba-search-icon {
            padding: 0 12px;
            color: #94a3b8;
            font-size: 14px;
        }
        .hba-search-box .form-control {
            border: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            height: 42px;
            font-size: 13px;
            padding-left: 0;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        .hba-search-box .form-control:disabled {
            color: #94a3b8;
        }
        .hba-search-box .btn {
            border: 0;
            border-left: 1px solid #e2e8f0;
            border-radius: 0;
            height: 42px;
            padding: 0 14px;
            font-weight: 700;
            font-size: 12px;
            background: transparent;
        }
        .hba-search-box .btn-search-map {
            background: #0f172a;
            color: #fff;
            border-left-color: #0f172a;
        }
        .hba-search-box .btn-search-map:hover:not(:disabled) {
            background: #1e293b;
            color: #fff;
        }
        .hba-search-box .btn-search-map:disabled,
        .hba-search-box .btn-clear-map:disabled {
            opacity: .45;
            cursor: not-allowed;
        }
        .hba-search-box .btn-clear-map {
            color: #64748b;
            background: #fff;
        }
        .hba-search-box .btn-clear-map:hover:not(:disabled) {
            background: #f1f5f9;
            color: #0f172a;
        }
        .hba-search-result {
            display: none;
            width: 100%;
            margin-top: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #065f46;
            font-size: 13px;
            font-weight: 600;
        }
        .hba-search-result.is-visible { display: flex; align-items: center; gap: 8px; }
        .hba-search-result.is-miss {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }
        .hba-search-result .meta {
            font-weight: 500;
            opacity: .9;
        }
        .hostel-room-card.hba-search-hit {
            border-color: #f59e0b !important;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25) !important;
            scroll-margin-top: 100px;
        }
        .bed-btn.hba-search-hit {
            outline: 2px solid #f59e0b;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.2);
            animation: hbaSearchPulse 1s ease 3;
            scroll-margin-top: 120px;
        }
        @keyframes hbaSearchPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.015); }
        }
        .hba-empty {
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            background: var(--hba-soft);
            padding: 48px 20px;
            text-align: center;
            color: var(--hba-muted);
        }
        .hba-empty i { color: #94a3b8; margin-bottom: 10px; }
        .hba-empty p { margin: 0; font-size: 14px; }
        .hba-floor-block { margin-bottom: 28px; }
        .hba-floor-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--hba-line);
        }
        .hba-floor-title h5 {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--hba-ink);
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }
        .hba-floor-title .hba-floor-meta {
            font-size: 12px;
            color: var(--hba-muted);
            font-weight: 600;
        }
        .bed-btn {
            border: 1px solid transparent;
            border-radius: 10px;
            padding: 0.6rem 0.8rem;
            text-align: left;
            width: 100%;
            transition: transform 0.12s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        }
        .bed-btn:hover { transform: translateY(-1px); }
        .bed-btn.bed-available {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: var(--hba-green);
        }
        .bed-btn.bed-available:hover {
            background: #dcfce7;
            box-shadow: 0 4px 12px rgba(22, 101, 52, 0.12);
        }
        .bed-btn.bed-occupied {
            background: #fef2f2;
            border-color: #fecaca;
            color: var(--hba-red);
        }
        .bed-btn.bed-occupied:hover {
            background: #fee2e2;
            box-shadow: 0 4px 12px rgba(153, 27, 27, 0.12);
        }
        .bed-student-name {
            font-weight: 600;
            font-size: 0.85rem;
            line-height: 1.3;
        }
        .bed-enrollment-no {
            font-size: 0.78rem;
            opacity: 0.85;
            margin-top: 2px;
        }
        .badge-bed-free {
            background: #fff;
            color: var(--hba-green);
            font-weight: 600;
            font-size: 10px;
        }
        .badge-bed-assigned {
            background: rgba(255, 255, 255, 0.9);
            color: var(--hba-red);
            font-weight: 600;
            font-size: 10px;
        }
        .badge-stat-free {
            background: #f0fdf4;
            color: var(--hba-green);
            border: 1px solid #bbf7d0;
        }
        .badge-stat-assigned {
            background: #fef2f2;
            color: var(--hba-red);
            border: 1px solid #fecaca;
        }
        .hostel-room-card {
            border: 1px solid var(--hba-line);
            border-radius: 12px;
            overflow: hidden;
            transition: box-shadow 0.15s ease, border-color 0.15s ease;
        }
        .hostel-room-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        }
        .hostel-room-card .card-header {
            background: #fff !important;
            border-bottom: 1px solid var(--hba-line);
            padding: 12px 14px;
        }
        .hostel-room-card .card-header h5 {
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: -0.01em;
        }
        .hostel-room-card .card-body { padding: 14px; }
        .hostel-room-card .beds-caption {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: var(--hba-muted);
            font-weight: 600;
            border-bottom: 1px solid var(--hba-line);
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        /* Modals */
        .hba-modal .modal-content {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 12px 40px rgba(0,0,0,.15);
            overflow: hidden;
        }
        .hba-modal .modal-header {
            background: linear-gradient(90deg, #1e293b, #334155);
            border: 0;
            padding: 14px 20px;
        }
        .hba-modal .modal-header .modal-title { font-weight: 700; font-size: 1rem; }
        .hba-modal .modal-header .close { opacity: 1; text-shadow: none; }
        .hba-modal .modal-body { padding: 20px 24px; }
        .hba-modal .modal-footer {
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
            padding: 12px 20px;
        }
        .hba-modal .modal-footer .btn { border-radius: 8px; font-weight: 600; padding: 8px 20px; }
        .hba-modal-assign .modal-header { background: linear-gradient(90deg, #065f46, #047857); }
        .hba-modal-vacate .modal-header { background: linear-gradient(90deg, #7f1d1d, #b91c1c); }
        .btn-assign-from-list {
            border-radius: 999px;
            font-weight: 600;
            font-size: 12px;
            padding: 5px 12px;
            border: 0;
            background: #059669;
            color: #fff;
        }
        .btn-assign-from-list:hover { background: #047857; color: #fff; }
        .btn-assign-from-list.is-open {
            background: #fff;
            color: #64748b;
            border: 1px solid #cbd5e1;
        }
        #paidStudentsModal .modal-header {
            background: linear-gradient(90deg, #0f172a, #1e293b);
        }
        #paidStudentsModal .table { margin-bottom: 0; }
        #paidStudentsModal .table thead th {
            font-size: 11px;
            letter-spacing: .45px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 12px 14px;
            white-space: nowrap;
        }
        #paidStudentsModal .table tbody td {
            vertical-align: middle;
            padding: 12px 14px;
            border-top: 1px solid #f1f5f9;
            font-size: 13px;
            color: #0f172a;
        }
        #paidStudentsModal .hba-enroll-chip {
            display: inline-block;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 3px 8px;
            color: #334155;
        }
        #paidStudentsModal .hba-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #fed7aa;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
        }
        #paidStudentsModal .hba-status-pill::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #f97316;
        }
        tr.hba-row-active > td { background: #ecfdf5 !important; }
        tr.hba-assign-expand-row > td {
            background: #f0fdf4 !important;
            border-top: 0 !important;
            padding: 0 14px 14px !important;
        }
        .hba-inline-assign {
            background: #fff;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            animation: hbaExpandIn .2s ease;
        }
        @keyframes hbaExpandIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .hba-inline-assign-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .hba-inline-assign-head .title {
            margin: 0;
            font-size: 13px;
            font-weight: 700;
            color: #065f46;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .hba-inline-assign-head .title .icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: #dcfce7;
            color: #166534;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .hba-inline-assign-head .session-chip {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            padding: 4px 10px;
        }
        .hba-inline-assign .field-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 6px;
        }
        .hba-inline-assign .select2-container { width: 100% !important; }
        .hba-inline-assign .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #dbe3f0;
            border-radius: 10px;
            background: #fff;
        }
        .hba-inline-assign .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            padding-left: 14px;
            color: #0f172a;
            font-size: 13px;
        }
        .hba-inline-assign .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 8px;
        }
        .hba-inline-assign .select2-container--default.select2-container--focus .select2-selection--single,
        .hba-inline-assign .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #34d399;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }
        .select2-container--open .select2-dropdown {
            border-color: #bbf7d0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
        }
        .select2-results__option { font-size: 13px; padding: 8px 12px; }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background: #059669;
        }
        .hba-inline-hint {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #047857;
            background: #ecfdf5;
            border-radius: 999px;
            padding: 4px 10px;
        }
        .hba-inline-actions {
            display: flex;
            align-items: flex-end;
            justify-content: flex-end;
            gap: 8px;
            height: 100%;
            padding-bottom: 2px;
        }
        .hba-inline-actions .btn {
            border-radius: 10px;
            font-weight: 700;
            font-size: 12px;
            padding: 9px 14px;
        }
        .hba-inline-actions .btn-confirm-inline-assign {
            background: #059669;
            border-color: #059669;
            color: #fff;
        }
        .hba-inline-actions .btn-confirm-inline-assign:hover {
            background: #047857;
            border-color: #047857;
            color: #fff;
        }
        .hba-session-pill {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            color: #0c4a6e;
        }
        .hba-student-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
            padding: 14px 16px;
            margin-top: 16px;
            text-align: left;
        }
        .hba-student-box .hba-student-name {
            font-weight: 700;
            font-size: 15px;
            color: #1e293b;
            margin-bottom: 4px;
        }
        .hba-student-box .hba-student-enroll {
            font-size: 13px;
            color: #64748b;
        }
        .hba-confirm-text {
            font-size: 15px;
            color: #374151;
            margin-bottom: 0;
        }
        .hba-vacate-dates {
            text-align: left;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
        }
        .hba-vacate-dates .form-control {
            height: 40px;
            border-radius: 10px;
            border-color: #dbe3f0;
            font-size: 13px;
        }
        .hba-vacate-hint {
            font-size: 12px;
            color: #64748b;
            margin-top: 8px;
        }
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #fef2f2;
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            font-size: 22px;
        }
    </style>

    <div class="mt-4 hba-page">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Hostel Bed Assignment</h4>
            </div>

            <div class="card-body">
                <div class="hba-filters mb-3">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="session-select" class="filter-label"><i class="fa fa-calendar"></i>Session</label>
                                <select id="session-select" class="form-control" required>
                                    <option value="">Select Session</option>
                                    @foreach($academic_sessions as $session)
                                        <option value="{{ $session->sess_id }}"
                                            {{ $session->sess_id == $default_session_id ? 'selected' : '' }}>
                                            {{ $session->sess_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="hostel-location-select" class="filter-label"><i class="fa fa-map-marker"></i>Location</label>
                                <select id="hostel-location-select" class="form-control">
                                    <option value="">Select Location</option>
                                    @foreach($hostel_locations as $location)
                                        <option value="{{ $location }}">{{ ucwords(str_replace('_', ' ', $location)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="hostel-type-select" class="filter-label"><i class="fa fa-building-o"></i>Hostel Type</label>
                                <select id="hostel-type-select" class="form-control" disabled>
                                    <option value="">Select Location First</option>
                                    @foreach($hostel_type as $type)
                                        <option value="{{ $type->ht_id }}" data-location="{{ $type->ht_location ?? 'on_campus' }}">{{ $type->ht_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="floor-select" class="filter-label"><i class="fa fa-level-up"></i>Floor <span class="text-muted font-weight-normal" style="text-transform:none;letter-spacing:0;">(optional)</span></label>
                                <select id="floor-select" class="form-control" disabled>
                                    <option value="">All Floors</option>
                                    @foreach($floors_de as $floor)
                                        <option value="{{ $floor->hf_id }}" data-hostel-type="{{ $floor->hf_new_hostel_type }}">{{ $floor->hf_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="session-stats" class="hba-summary-card">
                        <div class="hba-summary-head">
                            <h6 class="hba-summary-title"><i class="fa fa-pie-chart mr-1"></i>Bed Summary</h6>
                        </div>
                        <div class="hba-summary-metrics">
                            <div class="hba-summary-metric is-assigned">
                                <span class="hba-metric-value" id="stat-assigned">0</span>
                                <span class="hba-metric-label">Assigned</span>
                            </div>
                            <div class="hba-summary-metric is-available">
                                <span class="hba-metric-value" id="stat-available">0</span>
                                <span class="hba-metric-label">Available</span>
                            </div>
                            <div class="hba-summary-metric is-total">
                                <span class="hba-metric-value" id="stat-total">0</span>
                                <span class="hba-metric-label">Total Beds</span>
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-end mt-3 hba-paid-btns">
                        <div class="col-md-12">
                            <label class="filter-label mb-2 d-block"><i class="fa fa-list"></i>Paid but not assigned (this session)</label>
                            <button id="show-male-students-btn" class="btn btn-outline-primary btn-sm mr-2" disabled>
                                <i class="fa fa-male"></i> Male <span class="badge badge-primary badge-pill ml-1" id="male-not-assigned-count">0</span>
                            </button>
                            <button id="show-female-students-btn" class="btn btn-outline-danger btn-sm" disabled>
                                <i class="fa fa-female"></i> Female <span class="badge badge-danger badge-pill ml-1" id="female-not-assigned-count">0</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="hba-map-toolbar">
                    <div class="hba-map-hint" id="map-hint">
                        Select session, location and hostel type to view all floors. Floor filter is optional.
                    </div>
                    <div class="hba-map-search">
                        <div class="hba-search-box">
                            <span class="hba-search-icon"><i class="fa fa-search"></i></span>
                            <input type="text" id="enrollment-map-search" class="form-control"
                                placeholder="Search by enrollment no…" autocomplete="off" disabled>
                            <button type="button" class="btn btn-clear-map" id="enrollment-map-search-clear" disabled title="Clear">
                                <i class="fa fa-times"></i>
                            </button>
                            <button type="button" class="btn btn-search-map" id="enrollment-map-search-btn" disabled>
                                Find
                            </button>
                        </div>
                        <div class="hba-search-result" id="enrollment-search-result"></div>
                    </div>
                </div>

                <div id="rooms-container" class="row">
                    <div class="col-12">
                        <div class="hba-empty">
                            <i class="fa fa-bed fa-2x"></i>
                            <p>Select session, location and hostel type to view bed assignments.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Occupy Bed Modal -->
    <div class="modal fade hba-modal hba-modal-assign" id="occupyBedModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title text-white"><i class="fa fa-bed mr-2"></i>Assign Bed</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                </div>
                <form id="occupyBedForm">
                    <div class="modal-body">
                        <div class="hba-session-pill mb-3">
                            <i class="fa fa-calendar mr-1"></i>
                            <strong>Session:</strong> <span id="modal-session-name">—</span>
                        </div>
                        <div class="form-group mb-3">
                            <label for="student-input" class="filter-label">Enrollment No / Applicant ID</label>
                            <input type="text" class="form-control" id="student-input" required
                                placeholder="Enter enrollment number or applicant ID">
                        </div>
                        <div class="form-group mb-0">
                            <label for="occupy-checkin-date" class="filter-label">
                                Check-in Date <span class="text-muted font-weight-normal" style="text-transform:none;letter-spacing:0;">(optional)</span>
                            </label>
                            <input type="date" class="form-control" id="occupy-checkin-date">
                            <small class="text-muted">If set, saved to hostel allocation history with your username.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="occupyBedBtn"><i class="fa fa-bed mr-1"></i>Assign Bed</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade hba-modal hba-modal-vacate" id="confirmationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title text-white"><i class="fa fa-unlink mr-2"></i>Remove Bed Assignment</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="confirmationMessage" class="text-center"></div>
                    <div class="hba-vacate-dates">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="vacate-checkin-date" class="filter-label">Check-in Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="vacate-checkin-date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="vacate-checkout-date" class="filter-label">Check-out Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="vacate-checkout-date" required>
                            </div>
                        </div>
                        <p class="hba-vacate-hint mb-0">
                            <i class="fa fa-info-circle mr-1"></i>
                            Check-in loads from history if available. Both dates are required to remove the bed.
                        </p>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-light border px-4" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-4" id="confirmActionBtn" disabled>
                        <i class="fa fa-check mr-1"></i>Confirm Remove
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Paid Students Modal -->
    <div class="modal fade hba-modal" id="paidStudentsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 85%;">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title text-white" id="paidStudentsModalTitle">Paid but not assigned</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                        <table class="table table-hover mb-0 hba-paid-table">
                            <thead style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th class="pl-3" style="width:56px;">#</th>
                                    <th>Enrollment No</th>
                                    <th>Student Name</th>
                                    <th>Status</th>
                                    <th class="text-right pr-3" style="width:140px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="students-tbody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <span class="badge badge-info mr-auto py-2 px-3" id="total-count-badge">Total: 0</span>
                    <button type="button" class="btn btn-light border" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-scripts')
<script>
        $(document).ready(function() {
            const allFloors = @json($floors_de);
            const allHostelTypes = @json($hostel_type);
            const apiToken = '{{ auth()->user()->api_token ?? "" }}';
            let rooms = [];
            let currentBedId = null;
            let pendingVacateBedId = null;
            let currentPaidListGender = null;
            let assignListEnrollment = null;
            let assignListAvailableBeds = [];
            let pendingEnrollmentSearch = null;

            function normalizeEnrollment(value) {
                return String(value || '').trim().toUpperCase();
            }

            function setMapSearchEnabled(enabled) {
                $('#enrollment-map-search, #enrollment-map-search-btn, #enrollment-map-search-clear')
                    .prop('disabled', !enabled);
            }

            function clearMapSearchHighlight() {
                $('.hostel-room-card.hba-search-hit, .bed-btn.hba-search-hit').removeClass('hba-search-hit');
                $('#enrollment-search-result').removeClass('is-visible is-miss').empty();
            }

            function showSearchResult(message, isMiss) {
                const $el = $('#enrollment-search-result');
                $el.toggleClass('is-miss', !!isMiss)
                    .addClass('is-visible')
                    .html(message);
            }

            function scrollToMapTarget($el) {
                if (!$el || !$el.length) return;
                const el = $el.get(0);
                // Prefer native smooth scroll so sticky headers / nested layouts still work
                if (el && typeof el.scrollIntoView === 'function') {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
                    return;
                }
                const top = Math.max(($el.offset() || {}).top - 140, 0);
                $('html, body').stop(true).animate({ scrollTop: top }, 450);
            }

            function findBedInRooms(query) {
                const q = normalizeEnrollment(query);
                if (!q) return null;

                for (let i = 0; i < rooms.length; i++) {
                    const room = rooms[i];
                    for (let j = 0; j < (room.beds || []).length; j++) {
                        const bed = room.beds[j];
                        if (normalizeEnrollment(bed.enrollment_no) === q) {
                            return { room: room, bed: bed };
                        }
                    }
                }
                return null;
            }

            function highlightBedOnMap(match) {
                clearMapSearchHighlight();
                const $bedBtn = $('.bed-btn[data-bed-id="' + match.bed.hb_id + '"]');
                if (!$bedBtn.length) return false;

                const $roomCard = $bedBtn.closest('.hostel-room-card');
                $roomCard.addClass('hba-search-hit');
                $bedBtn.addClass('hba-search-hit');

                const floorName = floorNameById(match.room.hr_hf_id);
                const studentLabel = match.bed.student_name || match.bed.enrollment_no || 'Student';
                const safeStudent = $('<div>').text(studentLabel).html();
                showSearchResult(
                    '<i class="fa fa-check-circle"></i>' +
                    '<span>Found on map</span>' +
                    '<span class="meta">· ' + floorName + ' · Room ' + match.room.hr_room_no +
                    ' · Bed ' + match.bed.hb_bed_number + ' · ' + safeStudent + '</span>',
                    false
                );

                // Wait a tick so highlight paints, then scroll to bed
                setTimeout(function() {
                    scrollToMapTarget($bedBtn);
                }, 60);

                return true;
            }

            function searchEnrollmentOnMap(query, fromRetry) {
                const q = normalizeEnrollment(query);
                if (!q) {
                    showToast('error', 'Enter an enrollment number to search.');
                    return;
                }
                if (!canLoadRooms()) {
                    showToast('error', 'Select session, location and hostel type first.');
                    return;
                }
                if (!rooms.length) {
                    showToast('error', 'No rooms loaded on the map yet.');
                    return;
                }

                const match = findBedInRooms(q);
                if (match) {
                    if (!highlightBedOnMap(match)) {
                        showToast('error', 'Bed found in data but not visible on map. Try again.');
                    }
                    return;
                }

                if (getFloorId() && !fromRetry) {
                    pendingEnrollmentSearch = q;
                    showSearchResult('<i class="fa fa-spinner fa-spin"></i><span>Checking all floors…</span>', false);
                    $('#floor-select').val('');
                    updateMapHint();
                    refreshRooms();
                    return;
                }

                clearMapSearchHighlight();
                showSearchResult(
                    '<i class="fa fa-exclamation-circle"></i><span>No assigned bed found for <strong>' +
                    $('<div>').text(q).html() + '</strong></span>',
                    true
                );
                showToast('error', 'No assigned bed found for enrollment "' + q + '" in this session / hostel.');
            }

            $('#enrollment-map-search-btn').click(function() {
                searchEnrollmentOnMap($('#enrollment-map-search').val());
            });

            $('#enrollment-map-search').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchEnrollmentOnMap($(this).val());
                }
            });

            $('#enrollment-map-search-clear').click(function() {
                $('#enrollment-map-search').val('');
                pendingEnrollmentSearch = null;
                clearMapSearchHighlight();
                updateMapHint();
            });

            function getSessionId() {
                return $('#session-select').val();
            }

            function getSessionName() {
                return $('#session-select option:selected').text().trim();
            }

            function getFloorId() {
                return $('#floor-select').val() || '';
            }

            function apiHeaders() {
                return { 'Accept': 'application/json', 'Authorization': 'Bearer ' + apiToken };
            }

            function canLoadRooms() {
                return getSessionId() && $('#hostel-type-select').val();
            }

            function emptyStateHtml(message) {
                return '<div class="col-12"><div class="hba-empty"><i class="fa fa-bed fa-2x"></i><p>' + message + '</p></div></div>';
            }

            function updateMapHint() {
                if (!canLoadRooms()) {
                    $('#map-hint').text('Select session, location and hostel type to view all floors. Floor filter is optional.');
                    return;
                }
                if (getFloorId()) {
                    const floorName = $('#floor-select option:selected').text().trim();
                    $('#map-hint').text('Showing rooms for ' + floorName + '. Clear floor filter to see all floors.');
                } else {
                    $('#map-hint').text('Showing all floors for selected hostel. Optionally pick a floor to refine.');
                }
            }

            function floorNameById(floorId) {
                const floor = allFloors.find(f => String(f.hf_id) === String(floorId));
                return floor ? floor.hf_name : ('Floor ' + (floorId || '—'));
            }

            function refreshRooms() {
                updateMapHint();

                if (!canLoadRooms()) {
                    $('#session-stats').removeClass('is-visible');
                    setMapSearchEnabled(false);
                    $('#rooms-container').html(emptyStateHtml('Select session, location and hostel type to view bed assignments.'));
                    return;
                }

                setMapSearchEnabled(true);

                $('#rooms-container').html('<div class="col-12 text-center py-5"><i class="fa fa-spinner fa-spin fa-2x text-muted"></i><p class="text-muted mt-2 mb-0">Loading rooms…</p></div>');

                const payload = {
                    session_id: getSessionId(),
                    hostel_type_id: $('#hostel-type-select').val()
                };
                if (getFloorId()) {
                    payload.floor_id = getFloorId();
                }

                $.ajax({
                    url: '/api/hostel-bed-assignment/room-data',
                    method: 'GET',
                    headers: apiHeaders(),
                    data: payload,
                    success: function(res) {
                        if (!res.success) {
                            showToast('error', 'Failed to load room data.');
                            return;
                        }
                        rooms = res.rooms || [];
                        if (res.stats) {
                            $('#session-stats').addClass('is-visible');
                            $('#stat-assigned').text(res.stats.assigned_beds);
                            $('#stat-available').text(res.stats.available_beds);
                            $('#stat-total').text(res.stats.total_beds);
                        }
                        renderRooms();
                        if (pendingEnrollmentSearch) {
                            const pendingQuery = pendingEnrollmentSearch;
                            pendingEnrollmentSearch = null;
                            setTimeout(function() {
                                searchEnrollmentOnMap(pendingQuery, true);
                            }, 120);
                        }
                    },
                    error: function(xhr) {
                        var resp = xhr.responseJSON || {};
                        showToast('error', resp.error || resp.message || 'Error loading rooms.');
                        $('#rooms-container').html(emptyStateHtml('Could not load rooms. Please try again.'));
                    }
                });
            }

            function getHostelLocation() {
                return $('#hostel-location-select').val() || '';
            }

            function fetchNotAssignedCount(gender) {
                const sessionId = getSessionId();
                const hostelTypeId = $('#hostel-type-select').val();
                const hostelLocation = getHostelLocation();
                if (!sessionId || !hostelTypeId || !hostelLocation) return;

                $.ajax({
                    url: '/api/hostel-bed-assignment/paid-unassigned',
                    method: 'GET',
                    headers: apiHeaders(),
                    data: { session_id: sessionId, gender: gender, hostel_type_id: hostelTypeId, hostel_location: hostelLocation },
                    success: function(res) {
                        const count = res.success
                            ? (res.students || []).filter(s => (s.room_status || '').toLowerCase() !== 'assigned').length
                            : 0;
                        $('#' + (gender === 'male' ? 'male' : 'female') + '-not-assigned-count').text(count);
                    }
                });
            }

            function updatePaidButtons() {
                const ready = getSessionId() && $('#hostel-type-select').val() && getHostelLocation();
                $('#show-male-students-btn, #show-female-students-btn').prop('disabled', !ready);
                if (ready) {
                    fetchNotAssignedCount('male');
                    fetchNotAssignedCount('female');
                } else {
                    $('#male-not-assigned-count, #female-not-assigned-count').text('0');
                }
            }

            $('#session-select').change(function() {
                updatePaidButtons();
                refreshRooms();
            });

            $('#hostel-location-select').change(function() {
                const loc = $(this).val();
                $('#hostel-type-select').html('<option value="">Select Type</option>').prop('disabled', true);
                $('#floor-select').html('<option value="">All Floors</option>').prop('disabled', true);
                $('#session-stats').removeClass('is-visible');
                rooms = [];
                pendingEnrollmentSearch = null;
                setMapSearchEnabled(false);
                $('#enrollment-map-search').val('');
                clearMapSearchHighlight();
                updateMapHint();
                $('#rooms-container').html(emptyStateHtml('Select hostel type to view all floors.'));

                if (loc) {
                    const filtered = allHostelTypes.filter(t => (t.ht_location || 'on_campus') === loc);
                    filtered.forEach(t => {
                        $('#hostel-type-select').append('<option value="' + t.ht_id + '">' + t.ht_name + '</option>');
                    });
                    $('#hostel-type-select').prop('disabled', false);
                }
                updatePaidButtons();
            });

            $('#hostel-type-select').change(function() {
                const htId = $(this).val();
                $('#floor-select').html('<option value="">All Floors</option>').prop('disabled', true);
                if (htId) {
                    allFloors.filter(f => f.hf_new_hostel_type == htId).forEach(f => {
                        $('#floor-select').append('<option value="' + f.hf_id + '">' + f.hf_name + '</option>');
                    });
                    $('#floor-select').prop('disabled', false);
                }
                updatePaidButtons();
                refreshRooms();
            });

            $('#floor-select').change(refreshRooms);

            $(document).on('click', '.bed-available', function() {
                if (!getSessionId()) {
                    showToast('error', 'Please select a session first.');
                    return;
                }
                currentBedId = $(this).data('bed-id');
                $('#modal-session-name').text(getSessionName());
                $('#student-input').val('');
                $('#occupy-checkin-date').val('');
                $('#occupyBedModal').modal('show');
            });

            function updateVacateConfirmState() {
                const checkin = $('#vacate-checkin-date').val();
                const checkout = $('#vacate-checkout-date').val();
                $('#confirmActionBtn').prop('disabled', !(checkin && checkout));
            }

            $(document).on('input change', '#vacate-checkin-date, #vacate-checkout-date', updateVacateConfirmState);

            $(document).on('click', '.bed-occupied', function() {
                if (!getSessionId()) return;
                pendingVacateBedId = $(this).data('bed-id');
                const enrollment = $(this).data('enrollment') || '';
                const name = $(this).data('name') || '';
                let studentBox = '';
                if (name || enrollment) {
                    const displayName = name || enrollment;
                    const showEnroll = enrollment && enrollment !== name;
                    studentBox = '<div class="hba-student-box">' +
                        '<div class="hba-student-name">' + displayName + '</div>' +
                        (showEnroll ? '<div class="hba-student-enroll"><i class="fa fa-id-card-o mr-1"></i>' + enrollment + '</div>' : '') +
                        '</div>';
                }
                $('#confirmationMessage').html(
                    '<div class="hba-confirm-icon"><i class="fa fa-exclamation-triangle"></i></div>' +
                    '<p class="hba-confirm-text">Remove bed assignment for <strong>' + getSessionName() + '</strong>?</p>' +
                    studentBox
                );
                $('#vacate-checkin-date').val('');
                $('#vacate-checkout-date').val('');
                $('#confirmActionBtn').prop('disabled', true).html('<i class="fa fa-check mr-1"></i>Confirm Remove');
                $('#confirmationModal').modal('show');

                $.ajax({
                    url: '/api/hostel-bed-assignment/vacate-details',
                    method: 'GET',
                    headers: apiHeaders(),
                    data: {
                        session_id: getSessionId(),
                        bed_id: pendingVacateBedId
                    },
                    success: function(res) {
                        if (res.success && res.checkin_date) {
                            $('#vacate-checkin-date').val(res.checkin_date);
                        }
                        updateVacateConfirmState();
                    }
                });
            });

            $('#confirmActionBtn').off('click').on('click', function() {
                if (!pendingVacateBedId) return;
                const checkinDate = $('#vacate-checkin-date').val();
                const checkoutDate = $('#vacate-checkout-date').val();
                if (!checkinDate || !checkoutDate) {
                    showToast('error', 'Check-in and check-out dates are required.');
                    return;
                }
                if (checkoutDate < checkinDate) {
                    showToast('error', 'Check-out date must be on or after check-in date.');
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

                $.ajax({
                    url: '/api/hostel-bed-assignment/vacate',
                    method: 'POST',
                    headers: apiHeaders(),
                    data: {
                        _token: '{{ csrf_token() }}',
                        bed_id: pendingVacateBedId,
                        session_id: getSessionId(),
                        checkin_date: checkinDate,
                        checkout_date: checkoutDate
                    },
                    success: function(res) {
                        $('#confirmationModal').modal('hide');
                        showToast('success', res.message || 'Bed vacated.');
                        pendingVacateBedId = null;
                        refreshRooms();
                        updatePaidButtons();
                    },
                    error: function(xhr) {
                        var resp = xhr.responseJSON || {};
                        showToast('error', resp.error || 'Vacate failed.');
                    },
                    complete: function() {
                        btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i>Confirm Remove');
                        updateVacateConfirmState();
                    }
                });
            });

            $('#occupyBedForm').submit(function(e) {
                e.preventDefault();
                const studentInput = $('#student-input').val().trim();
                const hostelLocation = getHostelLocation();
                if (!studentInput || !currentBedId) return;
                if (!hostelLocation) {
                    showToast('error', 'Please select hostel location first.');
                    return;
                }

                $('#occupyBedBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

                const payload = {
                    _token: '{{ csrf_token() }}',
                    bed_id: currentBedId,
                    student_input: studentInput,
                    session_id: getSessionId(),
                    hostel_location: hostelLocation
                };
                const checkinDate = $('#occupy-checkin-date').val();
                if (checkinDate) {
                    payload.checkin_date = checkinDate;
                }

                $.ajax({
                    url: '/api/hostel-bed-assignment/assign',
                    method: 'POST',
                    headers: apiHeaders(),
                    data: payload,
                    success: function(res) {
                        $('#occupyBedModal').modal('hide');
                        showToast('success', res.message || 'Bed assigned.');
                        refreshRooms();
                        updatePaidButtons();
                    },
                    error: function(xhr) {
                        var resp = xhr.responseJSON || {};
                        showToast('error', resp.error || 'Assign failed.');
                    },
                    complete: function() {
                        $('#occupyBedBtn').prop('disabled', false).html('<i class="fa fa-bed mr-1"></i>Assign Bed');
                    }
                });
            });

            function loadPaidStudents(gender) {
                const sessionId = getSessionId();
                const hostelTypeId = $('#hostel-type-select').val();
                const hostelLocation = getHostelLocation();
                if (!sessionId || !hostelTypeId || !hostelLocation) {
                    showToast('error', 'Select session, location and hostel type first.');
                    return;
                }

                currentPaidListGender = gender;
                $('#students-tbody').html('<tr><td colspan="5" class="text-center"><i class="fa fa-spinner fa-spin"></i></td></tr>');
                $('#paidStudentsModalTitle').text((gender === 'male' ? 'Male' : 'Female') + ' — Paid but not assigned (' + getSessionName() + ')');

                $.ajax({
                    url: '/api/hostel-bed-assignment/paid-unassigned',
                    method: 'GET',
                    headers: apiHeaders(),
                    data: { session_id: sessionId, gender: gender, hostel_type_id: hostelTypeId, hostel_location: hostelLocation },
                    success: function(res) {
                        const list = (res.students || []).filter(s => (s.room_status || '').toLowerCase() !== 'assigned');
                        const tbody = $('#students-tbody').empty();
                        if (!list.length) {
                            tbody.html('<tr><td colspan="5" class="text-center text-muted">No students found</td></tr>');
                        } else {
                            list.forEach((s, i) => {
                                const enroll = s.id || '';
                                const name = s.name || '';
                                const safeEnroll = $('<div>').text(enroll).html();
                                const safeName = $('<div>').text(name).html();
                                tbody.append(
                                    '<tr class="hba-student-row">' +
                                    '<td class="pl-3 text-muted">' + (i + 1) + '</td>' +
                                    '<td><span class="hba-enroll-chip">' + (safeEnroll || '—') + '</span></td>' +
                                    '<td><strong>' + (safeName || '—') + '</strong></td>' +
                                    '<td><span class="hba-status-pill">Not Assigned</span></td>' +
                                    '<td class="text-right pr-3">' +
                                    '<button type="button" class="btn btn-sm btn-assign-from-list"' +
                                    ' data-enrollment="' + safeEnroll + '"' +
                                    ' data-name="' + safeName + '">' +
                                    '<i class="fa fa-bed mr-1"></i>Assign</button>' +
                                    '</td>' +
                                    '</tr>'
                                );
                            });
                        }
                        $('#total-count-badge').text('Not Assigned: ' + list.length);
                        $('#' + (gender === 'male' ? 'male' : 'female') + '-not-assigned-count').text(list.length);
                    }
                });
            }

            function destroyInlineSelect2($el) {
                if ($el && $el.length && $el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }
            }

            function initInlineSelect2($el, placeholder) {
                destroyInlineSelect2($el);
                $el.select2({
                    dropdownParent: $('#paidStudentsModal'),
                    width: '100%',
                    placeholder: placeholder,
                    allowClear: true,
                    minimumResultsForSearch: 0
                });
            }

            function closeInlineAssignPanel() {
                const $expand = $('#students-tbody tr.hba-assign-expand-row');
                if ($expand.length) {
                    destroyInlineSelect2($expand.find('.inline-assign-floor'));
                    destroyInlineSelect2($expand.find('.inline-assign-bed'));
                    $expand.remove();
                }
                $('#students-tbody tr.hba-row-active').removeClass('hba-row-active');
                $('#students-tbody .btn-assign-from-list')
                    .removeClass('is-open')
                    .prop('disabled', false)
                    .html('<i class="fa fa-bed mr-1"></i>Assign');
                assignListEnrollment = null;
            }

            function populateInlineFloors($floor) {
                const htId = $('#hostel-type-select').val();
                $floor.empty().append('<option value=""></option>');
                allFloors.filter(f => f.hf_new_hostel_type == htId).forEach(f => {
                    $floor.append('<option value="' + f.hf_id + '">' + f.hf_name + '</option>');
                });
                initInlineSelect2($floor, 'All Floors');
            }

            function renderInlineBeds($panel) {
                const floorId = $panel.find('.inline-assign-floor').val() || '';
                const $bed = $panel.find('.inline-assign-bed');
                const $hint = $panel.find('.inline-assign-hint');
                let beds = assignListAvailableBeds;
                if (floorId) {
                    beds = beds.filter(b => String(b.floor_id) === String(floorId));
                }

                destroyInlineSelect2($bed);
                $bed.empty().append('<option value=""></option>');

                if (!beds.length) {
                    initInlineSelect2($bed, 'No free beds available');
                    $hint.html('<i class="fa fa-info-circle"></i> No free beds for selected floor');
                    return;
                }

                beds.forEach(b => {
                    const label = b.floor_name + ' · Room ' + b.room_no + ' · Bed ' + b.bed_number;
                    $bed.append('<option value="' + b.bed_id + '">' + label + '</option>');
                });
                initInlineSelect2($bed, 'Search room / bed…');
                $hint.html('<i class="fa fa-check-circle"></i> ' + beds.length + ' free bed(s) available');
            }

            function loadAvailableBedsForInline($panel) {
                const sessionId = getSessionId();
                const hostelTypeId = $('#hostel-type-select').val();
                const $bed = $panel.find('.inline-assign-bed');
                const $hint = $panel.find('.inline-assign-hint');
                destroyInlineSelect2($bed);
                $bed.html('<option value=""></option>');
                initInlineSelect2($bed, 'Loading beds…');
                assignListAvailableBeds = [];

                if (!sessionId || !hostelTypeId) {
                    destroyInlineSelect2($bed);
                    $bed.html('<option value=""></option>');
                    initInlineSelect2($bed, 'Select hostel type first');
                    return;
                }

                $.ajax({
                    url: '/api/hostel-bed-assignment/room-data',
                    method: 'GET',
                    headers: apiHeaders(),
                    data: { session_id: sessionId, hostel_type_id: hostelTypeId },
                    success: function(res) {
                        const freeBeds = [];
                        (res.rooms || []).forEach(function(room) {
                            (room.beds || []).forEach(function(bed) {
                                if (bed.is_occupied === 'Y') return;
                                freeBeds.push({
                                    bed_id: bed.hb_id,
                                    bed_number: bed.hb_bed_number,
                                    room_no: room.hr_room_no,
                                    floor_id: room.hr_hf_id,
                                    floor_name: floorNameById(room.hr_hf_id)
                                });
                            });
                        });
                        freeBeds.sort(function(a, b) {
                            return String(a.floor_name).localeCompare(String(b.floor_name))
                                || String(a.room_no).localeCompare(String(b.room_no), undefined, { numeric: true })
                                || (a.bed_number - b.bed_number);
                        });
                        assignListAvailableBeds = freeBeds;
                        renderInlineBeds($panel);
                    },
                    error: function() {
                        destroyInlineSelect2($bed);
                        $bed.html('<option value=""></option>');
                        initInlineSelect2($bed, 'Failed to load beds');
                        $hint.html('<i class="fa fa-exclamation-circle"></i> Could not load available beds');
                    }
                });
            }

            function openInlineAssignPanel($btn) {
                const enrollment = $btn.data('enrollment') || '';
                const name = $btn.data('name') || '';
                if (!enrollment) {
                    showToast('error', 'Enrollment number missing for this student.');
                    return;
                }
                if (!getHostelLocation() || !$('#hostel-type-select').val()) {
                    showToast('error', 'Select location and hostel type first.');
                    return;
                }

                const $row = $btn.closest('tr');
                if ($row.hasClass('hba-row-active')) {
                    closeInlineAssignPanel();
                    return;
                }

                closeInlineAssignPanel();
                assignListEnrollment = enrollment;
                $row.addClass('hba-row-active');
                $btn.addClass('is-open').html('<i class="fa fa-times mr-1"></i>Cancel');

                const safeName = $('<div>').text(name || enrollment).html();
                const $expand = $(
                    '<tr class="hba-assign-expand-row">' +
                    '<td colspan="5">' +
                    '<div class="hba-inline-assign">' +
                    '<div class="hba-inline-assign-head">' +
                    '<p class="title"><span class="icon"><i class="fa fa-bed"></i></span>Assign bed to ' + safeName + '</p>' +
                    '<span class="session-chip"><i class="fa fa-calendar mr-1"></i>' + getSessionName() + '</span>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-md-3 mb-3 mb-md-0">' +
                    '<label class="field-label">Floor</label>' +
                    '<select class="inline-assign-floor"><option value=""></option></select>' +
                    '</div>' +
                    '<div class="col-md-4 mb-3 mb-md-0">' +
                    '<label class="field-label">Available Bed</label>' +
                    '<select class="inline-assign-bed"><option value=""></option></select>' +
                    '<div class="hba-inline-hint inline-assign-hint"><i class="fa fa-spinner fa-spin"></i> Loading free beds…</div>' +
                    '</div>' +
                    '<div class="col-md-2 mb-3 mb-md-0">' +
                    '<label class="field-label">Check-in <span style="text-transform:none;font-weight:600;letter-spacing:0;">(optional)</span></label>' +
                    '<input type="date" class="form-control inline-assign-checkin" style="height:42px;border-radius:10px;border-color:#dbe3f0;">' +
                    '</div>' +
                    '<div class="col-md-3">' +
                    '<div class="hba-inline-actions">' +
                    '<button type="button" class="btn btn-sm btn-confirm-inline-assign">' +
                    '<i class="fa fa-check mr-1"></i>Confirm</button>' +
                    '<button type="button" class="btn btn-light border btn-sm btn-cancel-inline-assign">Close</button>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</td></tr>'
                );

                $row.after($expand);
                populateInlineFloors($expand.find('.inline-assign-floor'));
                loadAvailableBedsForInline($expand);
            }

            $(document).on('click', '.btn-assign-from-list', function() {
                openInlineAssignPanel($(this));
            });

            $(document).on('change', '.inline-assign-floor', function() {
                const $panel = $(this).closest('.hba-inline-assign');
                renderInlineBeds($panel);
            });

            $(document).on('click', '.btn-cancel-inline-assign', function() {
                closeInlineAssignPanel();
            });

            $(document).on('click', '.btn-confirm-inline-assign', function() {
                const $panel = $(this).closest('.hba-inline-assign');
                const bedId = $panel.find('.inline-assign-bed').val();
                const hostelLocation = getHostelLocation();
                if (!assignListEnrollment || !bedId) {
                    showToast('error', 'Please select an available bed.');
                    return;
                }
                if (!hostelLocation) {
                    showToast('error', 'Please select hostel location first.');
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

                const payload = {
                    _token: '{{ csrf_token() }}',
                    bed_id: bedId,
                    student_input: assignListEnrollment,
                    session_id: getSessionId(),
                    hostel_location: hostelLocation
                };
                const checkinDate = $panel.find('.inline-assign-checkin').val();
                if (checkinDate) {
                    payload.checkin_date = checkinDate;
                }

                $.ajax({
                    url: '/api/hostel-bed-assignment/assign',
                    method: 'POST',
                    headers: apiHeaders(),
                    data: payload,
                    success: function(res) {
                        showToast('success', res.message || 'Bed assigned.');
                        assignListEnrollment = null;
                        refreshRooms();
                        updatePaidButtons();
                        if (currentPaidListGender) {
                            loadPaidStudents(currentPaidListGender);
                        }
                    },
                    error: function(xhr) {
                        var resp = xhr.responseJSON || {};
                        showToast('error', resp.error || 'Assign failed.');
                        btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i>Confirm');
                    }
                });
            });

            $('#show-male-students-btn').click(function() { loadPaidStudents('male'); $('#paidStudentsModal').modal('show'); });
            $('#show-female-students-btn').click(function() { loadPaidStudents('female'); $('#paidStudentsModal').modal('show'); });

            function buildRoomCard(room) {
                const occupied = room.beds.filter(b => b.is_occupied === 'Y').length;
                const available = room.beds.length - occupied;
                const bedsHtml = room.beds.map(function(bed) {
                    const isOcc = bed.is_occupied === 'Y';
                    const occDetails = isOcc
                        ? '<div class="bed-student-info mt-1">' +
                            (bed.student_name ? '<div class="bed-student-name">' + bed.student_name + '</div>' : '') +
                            (bed.enrollment_no ? '<div class="bed-enrollment-no"><i class="fa fa-id-card-o mr-1"></i>' + bed.enrollment_no + '</div>' : '') +
                            (!bed.student_name && !bed.enrollment_no ? '<div class="bed-student-name">Assigned</div>' : '') +
                          '</div>'
                        : '';
                    return '<div class="mb-2"><button type="button" class="bed-btn ' +
                        (isOcc ? 'bed-occupied' : 'bed-available') +
                        '" data-bed-id="' + bed.hb_id + '" data-enrollment="' + (bed.enrollment_no || '') +
                        '" data-name="' + (bed.student_name || '') + '">' +
                        '<div class="d-flex justify-content-between align-items-center">' +
                        '<span><i class="fa fa-bed mr-1"></i>Bed ' + bed.hb_bed_number + '</span>' +
                        '<span class="badge ' + (isOcc ? 'badge-bed-assigned' : 'badge-bed-free') + '">' + (isOcc ? 'Assigned' : 'Free') + '</span>' +
                        '</div>' + occDetails +
                        '</button></div>';
                }).join('');

                return '<div class="col-md-4 col-lg-3 mb-3 hba-room-wrap" data-room-id="' + room.hr_id + '" data-room-no="' + room.hr_room_no + '">' +
                    '<div class="card h-100 hostel-room-card">' +
                    '<div class="card-header"><h5 class="mb-0 text-dark"><i class="fa fa-home mr-1 text-muted"></i>Room ' + room.hr_room_no + '</h5></div>' +
                    '<div class="card-body">' +
                    '<div class="mb-2"><span class="badge badge-light mr-1">Capacity: ' + room.hr_capacity + '</span>' +
                    '<span class="badge badge-stat-free mr-1">Free: ' + available + '</span>' +
                    '<span class="badge badge-stat-assigned">Assigned: ' + occupied + '</span></div>' +
                    '<div class="beds-caption">Beds — click to assign / remove</div>' + bedsHtml +
                    '</div></div></div>';
            }

            function renderRooms() {
                const container = $('#rooms-container').empty();
                if (!rooms.length) {
                    container.html(emptyStateHtml(getFloorId() ? 'No rooms found for this floor.' : 'No rooms found for this hostel type.'));
                    return;
                }

                const selectedFloor = getFloorId();

                if (selectedFloor) {
                    rooms.forEach(function(room) {
                        container.append(buildRoomCard(room));
                    });
                    return;
                }

                // Group by floor when viewing all floors
                const groups = {};
                rooms.forEach(function(room) {
                    const key = room.hr_hf_id || 'unknown';
                    if (!groups[key]) groups[key] = [];
                    groups[key].push(room);
                });

                const orderedFloorIds = allFloors
                    .filter(f => f.hf_new_hostel_type == $('#hostel-type-select').val())
                    .map(f => String(f.hf_id));

                const groupKeys = Object.keys(groups).sort(function(a, b) {
                    const ia = orderedFloorIds.indexOf(String(a));
                    const ib = orderedFloorIds.indexOf(String(b));
                    if (ia === -1 && ib === -1) return String(a).localeCompare(String(b));
                    if (ia === -1) return 1;
                    if (ib === -1) return -1;
                    return ia - ib;
                });

                groupKeys.forEach(function(floorId) {
                    const floorRooms = groups[floorId];
                    let free = 0, assigned = 0;
                    floorRooms.forEach(function(room) {
                        room.beds.forEach(function(bed) {
                            if (bed.is_occupied === 'Y') assigned++; else free++;
                        });
                    });

                    const block = $('<div class="col-12 hba-floor-block"></div>');
                    block.append(
                        '<div class="hba-floor-title">' +
                        '<h5><i class="fa fa-level-up mr-1 text-muted"></i>' + floorNameById(floorId) + '</h5>' +
                        '<div class="hba-floor-meta">' + floorRooms.length + ' rooms · ' + free + ' free · ' + assigned + ' assigned</div>' +
                        '</div>'
                    );
                    const row = $('<div class="row"></div>');
                    floorRooms.forEach(function(room) {
                        row.append(buildRoomCard(room));
                    });
                    block.append(row);
                    container.append(block);
                });
            }

            if (getSessionId()) {
                updatePaidButtons();
            }
            updateMapHint();
        });
    </script>
@endpush
