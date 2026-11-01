@extends('layouts.app')

@section('content')

    <head>
        <title>Export to Excel</title>
        <script src="{{ asset('weekStudentsReport/jquery-3.5.1.min.js') }}"></script>
    </head>

    @include('components.toast-notifications')
    <style>
        :root {
            --hostel-primary: #4f46e5;
            --hostel-primary-hover: #4338ca;
            --hostel-primary-soft: #eef2ff;
            --hostel-accent: #06b6d4;
            --hostel-success: #10b981;
            --hostel-warning: #f59e0b;
            --hostel-danger: #ef4444;
            --hostel-surface: #ffffff;
            --hostel-bg: #f1f5f9;
            --hostel-border: #e2e8f0;
            --hostel-text: #0f172a;
            --hostel-text-muted: #64748b;
            --hostel-radius: 16px;
            --hostel-radius-sm: 10px;
            --hostel-shadow: 0 1px 3px rgba(15, 23, 42, 0.06), 0 1px 2px rgba(15, 23, 42, 0.04);
            --hostel-shadow-md: 0 10px 15px -3px rgba(15, 23, 42, 0.08), 0 4px 6px -4px rgba(15, 23, 42, 0.05);
            --hostel-shadow-lg: 0 20px 25px -5px rgba(79, 70, 229, 0.1), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
        }

        .hostel-modern-page {
            background: linear-gradient(180deg, #eef2ff 0%, var(--hostel-bg) 220px, var(--hostel-bg) 100%);
            min-height: 100vh;
        }

        .hostel-modern-page > .hostel-main-card {
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: var(--hostel-radius);
            box-shadow: var(--hostel-shadow-lg);
            overflow: hidden;
            background: var(--hostel-surface);
        }

        .hostel-modern-page .hostel-page-header {
            background: linear-gradient(135deg, #312e81 0%, #4f46e5 45%, #6366f1 100%);
            border-bottom: none;
            padding: 1.35rem 1.75rem;
            position: relative;
            overflow: hidden;
        }

        .hostel-modern-page .hostel-page-header::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -5%;
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hostel-modern-page .hostel-page-header::after {
            content: '';
            position: absolute;
            bottom: -60%;
            left: 10%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hostel-modern-page .hostel-page-title {
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .hostel-modern-page .hostel-page-subtitle {
            font-size: 0.85rem;
            opacity: 0.85;
            margin: 4px 0 0;
            font-weight: 400;
            position: relative;
            z-index: 1;
        }

        .hostel-modern-page .hostel-filters-card {
            background: var(--hostel-surface);
            border-bottom: 1px solid var(--hostel-border);
            padding: 1.25rem 1.75rem;
        }

        .hostel-modern-page .hostel-filters-card .form-group label {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--hostel-text-muted);
            margin-bottom: 0.5rem;
        }

        .hostel-modern-page .hostel-filters-card .form-group label .fa {
            color: var(--hostel-primary);
            opacity: 0.9;
        }

        .hostel-modern-page .hostel-content-body {
            padding: 1.5rem 1.75rem 1.75rem;
            background: var(--hostel-bg);
        }

        /* Fix horizontal scroll and Select2 overflow */
        body {
            overflow-x: hidden;
        }

        .select2-container {
            width: 100% !important;

        }

        .select2-dropdown {
            z-index: 9999 !important;
        }

        .card-body {
            overflow: visible !important;
        }

        .form-group {
            overflow: visible !important;
        }

        .card {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 0.75rem;
            background: linear-gradient(to bottom, #ffffff, #f8f9fa);
        }

        .card-header {
            background: linear-gradient(135deg, #4454c3, #007bff) !important;

            border-bottom: none;
            padding: 1.5rem;
            color: white;
            border-radius: 0.75rem 0.75rem 0 0 !important;
        }

        .nav-tabs {
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 1.5rem;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            padding: 0.75rem 1.25rem;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-tabs .nav-link.active {
            color: #4454c3;
            background-color: transparent;
            border-bottom: 2px solid #4454c3;
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: #4454c3 !important;
            background-color: rgba(74, 144, 226, 0.05);
        }

        .badge {
            font-weight: 500;
            padding: 0.4em 0.7em;
            border-radius: 50px;
            background: rgba(74, 144, 226, 0.1);
            color: #4454c3;
        }

        .badge.badge-success {
            font-size: 16px !important;
            padding: 8px 16px !important;
            font-weight: 600 !important;
            background: #28a745 !important;
            color: white !important;
        }

        .badge.badge-success i {
            font-size: 18px !important;
            margin-right: 8px !important;
        }

        .badge.badge-seat-reserved {
            font-size: 11px !important;
            padding: 4px 8px !important;
            font-weight: 500 !important;
            background: linear-gradient(135deg, #28a745, #20c997) !important;
            color: white !important;
            border-radius: 12px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2) !important;
        }

        .badge.badge-seat-reserved i {
            font-size: 10px !important;
            margin-right: 4px !important;
        }

        .badge.badge-paid {
            font-size: 11px !important;
            padding: 4px 8px !important;
            font-weight: 500 !important;
            background: linear-gradient(135deg, #28a745, #20c997) !important;
            color: white !important;
            border-radius: 12px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2) !important;
        }

        .badge.badge-unpaid {
            font-size: 11px !important;
            padding: 4px 8px !important;
            font-weight: 500 !important;
            background: linear-gradient(135deg, #dc3545, #fd7e14) !important;
            color: white !important;
            border-radius: 12px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2) !important;
        }

        .badge.badge-allowed {
            font-size: 11px !important;
            padding: 4px 8px !important;
            font-weight: 500 !important;
            background: linear-gradient(135deg, #17a2b8, #20c997) !important;
            color: white !important;
            border-radius: 12px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            box-shadow: 0 2px 4px rgba(23, 162, 184, 0.2) !important;
        }

        .badge.badge-danger {
            font-size: 11px !important;
            padding: 4px 8px !important;
            font-weight: 500 !important;
            background: linear-gradient(135deg, #dc3545, #c82333) !important;
            color: white !important;
            border-radius: 12px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2) !important;
        }

        .badge i {
            font-size: 10px !important;
            margin-right: 4px !important;
        }

        .table {
            margin-bottom: 0;
            border-radius: 0.5rem;
            overflow: visible;
        }

        .table thead th {
            border-top: none;
            background: transparent;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            color: #4454c3;
            padding: 1rem;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(74, 144, 226, 0.05);
        }

        .btn {
            font-weight: 500;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-sm {
            padding: 0.35rem 1rem;
        }

        .btn-outline-primary {
            color: #4454c3;
            border-color: #4454c3;
        }

        .btn-outline-primary:hover {
            background-color: #4454c3;
            color: white;
        }

        .btn-outline-success {
            color: #28a745;
            border-color: #28a745;
        }

        .btn-outline-success:hover {
            background-color: #28a745;
            color: white;
        }

        .form-control {

            padding: 0.75rem 1.25rem;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            border-color: #4454c3;
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.15);
            background-color: #ffffff;
        }

        .tab-content {
            background-color: #ffffff;
            border-radius: 0 0 0.75rem 0.75rem;
            padding: .5rem;
        }

        .text-muted {
            color: #6c757d !important;
        }

        h5 {
            color: #4454c3;
            font-weight: 600;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        .container {
            max-width: 1400px;
        }

        .nav-tabs .nav-link.active {
            border-bottom: 2px solid #007bff;
        }

        /* Main Tables Fixed Header and Scrollable Body */
        .tab-content .table-responsive {
            position: relative;
        }

        /* Drill-down Modal Header White Text */
        #drillDownModal .modal-header * {
            color: white !important;
        }

        /* Drill-down Modal Fixed Header and Scrollable Body */
        #drillDownModal .modal-body {
            padding: 0 !important;
        }

        #drillDownModal .table-responsive {
            max-height: 600px;
            overflow-y: auto;
            overflow-x: auto;
        }

        #drillDownModal #drillDownTable thead {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #f8f9fa;
        }

        #drillDownModal #drillDownTable thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            position: sticky;
            top: 0;
        }

        /* Specific hover styles for nested tabs */
        #newApplicantsTab .nav-link:hover,
        #oldApplicantsTab .nav-link:hover {
            color: #4454c3 !important;
            background-color: rgba(74, 144, 226, 0.05);
            border-color: transparent;
        }

        #newApplicantsTab .nav-link.active:hover,
        #oldApplicantsTab .nav-link.active:hover {
            color: #4454c3 !important;
            background-color: transparent;
        }

        /* Tab hover fixes */
        .nav-tabs .nav-link:hover,
        .nav-tabs .nav-link:focus {
            color: #4454c3 !important;
            background-color: rgba(74, 144, 226, 0.05) !important;
            border-color: transparent !important;
        }

        .nav-tabs .nav-link.active:hover,
        .nav-tabs .nav-link.active:focus {
            color: #4454c3 !important;
            background-color: transparent !important;
        }

        /* Custom styling for action buttons and checkboxes */
        .action-container {
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: space-between;
        }

        .form-check {
            margin: 0;
            padding: 0;
            order: 2;
        }

        .form-check-input {
            margin: 0;
            transform: scale(1.2);
        }

        .hostel-applicants-table .form-check {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }

        .form-check-label {
            margin: 0;
            font-size: 13px;
            color: #6c757d;
            white-space: nowrap;
            font-weight: 500;
        }

        .btn-container {
            order: 1;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.25rem;
        }

        .loading {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }

        #hostel-applicants-panel {
            position: relative;
            min-height: 180px;
        }

        #hostel-applicants-panel.is-loading {
            pointer-events: none;
        }

        .hostel-data-loading-overlay {
            position: absolute;
            inset: 0;
            z-index: 30;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background: rgba(255, 255, 255, 0.82);
            border-radius: 0.75rem;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        /* Fix dropdown text visibility */
        .form-control-lg {
            height: auto !important;
            padding: 0.75rem 1rem !important;
            font-size: 1rem !important;
            line-height: 1.5 !important;
        }

        .form-control-lg option {
            padding: 0.5rem !important;
            font-size: 1rem !important;
        }

        /* DataTable Button Styling */
        .dt-buttons {
            margin-bottom: 1rem;
        }

        .dt-button {
            background: linear-gradient(135deg, #4454c3, #007bff) !important;
            color: white !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 0.5rem 1.25rem !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
            margin-right: 0.5rem !important;
        }

        .dt-button:hover {
            background: linear-gradient(135deg, #3748a8, #0056b3) !important;
            color: white !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2) !important;
        }

        .dt-button:active {
            transform: translateY(0) !important;
        }

        /* Professional Seat Status Design - Horizontal Layout */
        .seat-status-container {
            display: flex;
            flex-direction: row;
            gap: 12px;
            background: #ffffff;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid #e1e5e9;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            flex-wrap: wrap;
        }

        .status-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 8px;
            background: #f8fafc;
            transition: all 0.2s ease;
            border: 1px solid #e2e8f0;
            min-width: 120px;
        }

        .status-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            background: #ffffff;
        }

        .status-icon {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            font-size: 14px;
        }

        .allowed-icon {
            background: #dcfce7;
            color: #166534;
        }

        .consent-icon {
            background: #dbeafe;
            color: #1e40af;
        }

        .paid-icon {
            background: #fef3c7;
            color: #92400e;
        }

        .vacate-icon {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-label {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            white-space: nowrap;
        }

        .status-count {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            background: #ffffff;
            padding: 6px 10px;
            border-radius: 6px;
            min-width: 35px;
            text-align: center;
            border: 1px solid #d1d5db;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        /* Available seats badge styling - Soft Info Theme */
        .available-seats-badge {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #ffffff;
            font-size: 13px;
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid #93c5fd;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
        }

        /* Large Tab Styling for Male/Female Tabs - Soft Colors */
        .nav-tabs .nav-link {
            font-size: 16px !important;
            font-weight: 600 !important;
            padding: 12px 20px !important;
            color: #6b7280 !important;
            border: none !important;
            border-radius: 8px 8px 0 0 !important;
            transition: all 0.3s ease !important;
            background: #f9fafb !important;
            margin-right: 4px !important;
            border-bottom: 2px solid transparent !important;
        }

        .nav-tabs .nav-link:hover {
            background: #f3f4f6 !important;
            color: #374151 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
        }

        .nav-tabs .nav-link.active {
            background: #eff6ff !important;
            color: #1e40af !important;
            font-weight: 700 !important;
            border-bottom: 2px solid #3b82f6 !important;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1) !important;
        }

        .nav-tabs .nav-link .fa {
            font-size: 18px !important;
            margin-right: 8px !important;
        }

        .nav-tabs .nav-link .badge {
            font-size: 12px !important;
            padding: 4px 8px !important;
            margin-left: 8px !important;
            background: #e5e7eb !important;
            color: #6b7280 !important;
        }

        .nav-tabs .nav-link.active .badge {
            background: #dbeafe !important;
            color: #1e40af !important;
        }

        /* Professional Important Note Styling - Warning Theme */
        .important-note-container {
            background: linear-gradient(135deg, #fefdf8, #fef9e7);
            border: 1px solid #fef3c7;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.05);
            position: relative;
            overflow: hidden;
        }

        .important-note-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #f59e0b, #d97706);
        }

        .important-note-content {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .important-note-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 8px rgba(245, 158, 11, 0.2);
        }

        .important-note-icon i {
            color: white;
            font-size: 24px;
        }

        .important-note-text {
            flex: 1;
        }

        .important-note-title {
            font-size: 20px;
            font-weight: 700;
            color: #92400e;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .important-note-description {
            font-size: 16px;
            font-weight: 500;
            color: #a16207;
            line-height: 1.6;
            margin: 0;
        }

        /* Hover effect for the note */
        .important-note-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(245, 158, 11, 0.12);
            transition: all 0.3s ease;
        }

        /* Large Dropdown Styling */
        .form-group label {
            font-size: 18px !important;
            font-weight: 600 !important;
            color: #374151 !important;
            margin-bottom: 12px !important;
        }

        .form-group label .fa {
            font-size: 20px !important;
            margin-right: 10px !important;
        }

        .form-control-lg {
            font-size: 16px !important;
            padding: 12px 16px !important;
            border-radius: 8px !important;
            border: 2px solid #e5e7eb !important;
            background-color: #ffffff !important;
            transition: all 0.3s ease !important;
        }

        .form-control-lg:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
            outline: none !important;
        }

        /* Specific styling for select dropdowns */
        select.form-control-lg {
            font-size: 16px !important;
            line-height: 1.5 !important;
        }

        select.form-control-lg option {
            font-size: 16px !important;
            padding: 12px 16px !important;
            background-color: #ffffff !important;
            color: #374151 !important;
            line-height: 1.5 !important;
        }

        /* Target the dropdown list specifically */
        select.form-control-lg:not([size]) {
            font-size: 16px !important;
        }

        /* For webkit browsers (Chrome, Safari) */
        select.form-control-lg::-webkit-listbox {
            font-size: 16px !important;
        }

        /* For Firefox */
        select.form-control-lg:-moz-listbox {
            font-size: 16px !important;
        }

        /* Dropdown container styling */
        .form-group {
            margin-bottom: 24px !important;
        }

        /* Additional dropdown styling for better browser compatibility */
        #session_id,
        #admission_schedule_id {
            font-size: 16px !important;
            font-weight: 500 !important;
        }

        #session_id option,
        #admission_schedule_id option {
            font-size: 16px !important;
            font-weight: 500 !important;
            padding: 8px 12px !important;
        }

        /* Ensure dropdown text is readable */
        select option {
            font-size: 16px !important;
            background: white !important;
            color: #374151 !important;
        }

        /* Force larger text for all select elements and their options */
        select {
            font-size: 16px !important;
        }

        select option {
            font-size: 16px !important;
            font-weight: 500 !important;
        }

        /* Target specific dropdowns with higher specificity */
        .form-group select.form-control-lg {
            font-size: 16px !important;
        }

        .form-group select.form-control-lg option {
            font-size: 16px !important;
            font-weight: 500 !important;
        }

        /* Use !important with maximum specificity */
        body .form-group select.form-control-lg {
            font-size: 16px !important;
        }

        body .form-group select.form-control-lg option {
            font-size: 16px !important;
            font-weight: 500 !important;
        }

        /* Responsive design for smaller screens */
        @media (max-width: 768px) {
            .seat-status-container {
                gap: 8px;
                padding: 12px;
            }

            .status-item {
                min-width: 100px;
                padding: 8px 10px;
            }

            .status-label {
                font-size: 11px;
            }

            .status-count {
                font-size: 16px;
                padding: 4px 8px;
            }

            .nav-tabs .nav-link {
                font-size: 14px !important;
                padding: 10px 16px !important;
            }

            .nav-tabs .nav-link .fa {
                font-size: 16px !important;
            }

            /* Mobile responsive for important note */
            .important-note-container {
                padding: 16px;
            }

            .important-note-content {
                gap: 12px;
            }

            .important-note-icon {
                width: 40px;
                height: 40px;
            }

            .important-note-icon i {
                font-size: 20px;
            }

            .important-note-title {
                font-size: 18px;
            }

            .important-note-description {
                font-size: 14px;
            }

            /* Mobile responsive for dropdowns */
            .form-group label {
                font-size: 16px !important;
            }

            .form-group label .fa {
                font-size: 18px !important;
            }

            .form-control-lg {
                font-size: 15px !important;
                padding: 10px 14px !important;
            }

            .form-control-lg option {
                font-size: 15px !important;
                padding: 6px 10px !important;
            }
        }

        /* Professional Filter Styling */
        .filter-section {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        .filter-section.hostel-dt-filters {
            padding: 12px 14px;
            margin-bottom: 12px;
            border-radius: 8px;
        }

        .filter-section.hostel-dt-filters .filter-header {
            margin-bottom: 10px;
            padding-bottom: 8px;
        }

        .filter-section.hostel-dt-filters .filter-title {
            font-size: 14px;
        }

        .filter-section.hostel-dt-filters .filter-subtitle {
            font-size: 12px;
            margin-top: 2px;
        }

        .filter-section.hostel-dt-filters .filter-group {
            margin-bottom: 0;
        }

        .filter-section.hostel-dt-filters .filter-select {
            min-height: 42px;
            padding: 8px 14px;
            font-size: 14px;
        }

        .filter-header {
            margin-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 15px;
        }

        .filter-title {
            color: #2c3e50;
            font-weight: 700;
            font-size: 18px;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .filter-title i {
            color: #4454c3;
            margin-right: 8px;
        }

        .filter-subtitle {
            color: #6c757d;
            font-size: 14px;
            margin-top: 5px;
            font-weight: 500;
        }

        .filter-group {
            margin-bottom: 20px;
        }

        .filter-label {
            display: block;
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .filter-label i {
            color: #4454c3;
            margin-right: 6px;
        }

        .select-wrapper {
            position: relative;
        }

        .filter-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px 20px;
            font-size: 16px;
            font-weight: 500;
            background: #ffffff;
            transition: all 0.3s ease;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            height: auto;
            min-height: 50px;
            line-height: 1.5;
        }

        .filter-select:focus {
            border-color: #4454c3;
            box-shadow: 0 0 0 3px rgba(68, 84, 195, 0.1);
            outline: none;
        }

        .select-arrow {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            pointer-events: none;
            font-size: 14px;
        }

        .filter-option {
            padding: 12px 16px;
            font-weight: 500;
            font-size: 16px;
            line-height: 1.5;
        }

        .filter-info {
            display: flex;
            align-items: center;
            height: 100%;
        }

        .info-card {
            background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            width: 100%;
        }

        .info-icon {
            color: #1976d2;
            font-size: 18px;
            margin-top: 2px;
        }

        .info-content {
            flex: 1;
        }

        .info-title {
            font-weight: 600;
            color: #1565c0;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .info-text {
            color: #424242;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
        }

        /* Hover effects */
        .filter-select:hover {
            border-color: #4454c3;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(68, 84, 195, 0.15);
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(25, 118, 210, 0.15);
            transition: all 0.3s ease;
        }

        /* Manual Search Section - hidden
        .manual-search-section {
            margin-top: 1rem !important;
            padding-top: 0 !important;
            border-top: none !important;
        }

        .manual-search-section .filter-header {
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
        }

        .manual-search-input-group .input-group-text {
            background: linear-gradient(135deg, #4454c3, #007bff);
            color: #fff;
            border: none;
            border-radius: 8px 0 0 8px;
            min-width: 48px;
            justify-content: center;
        }

        .manual-search-input-group .form-control {
            border: 2px solid #e9ecef;
            border-left: none;
            border-radius: 0 8px 8px 0;
            min-height: 50px;
            font-size: 15px;
            font-weight: 500;
        }

        .manual-search-input-group .form-control:focus {
            border-color: #4454c3;
            box-shadow: none;
        }

        .manual-search-btn {
            min-height: 50px;
            border-radius: 8px !important;
            font-weight: 600;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 12px rgba(68, 84, 195, 0.25);
        }

        .manual-search-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(68, 84, 195, 0.35);
        }

        .manual-result-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(68, 84, 195, 0.08);
        }

        .manual-result-top {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 20px 24px;
            background: linear-gradient(135deg, #f8faff, #eef2ff);
            border-bottom: 1px solid #e2e8f0;
            flex-wrap: wrap;
        }

        .manual-result-avatar {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #fff;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .manual-result-avatar.male {
            background: linear-gradient(135deg, #4454c3, #6366f1);
        }

        .manual-result-avatar.female {
            background: linear-gradient(135deg, #db2777, #ec4899);
        }

        .manual-result-identity {
            flex: 1;
            min-width: 200px;
        }

        .manual-result-name {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 6px 0;
        }

        .manual-result-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }

        .manual-result-meta span i {
            color: #4454c3;
            margin-right: 4px;
        }

        .manual-result-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }

        .manual-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px !important;
            font-weight: 600 !important;
            padding: 4px 10px !important;
            border-radius: 20px !important;
            letter-spacing: 0.3px;
            text-transform: none !important;
        }

        .manual-badge-consent {
            background: rgba(40, 167, 69, 0.12) !important;
            color: #15803d !important;
            border: 1px solid rgba(40, 167, 69, 0.25);
        }

        .manual-badge-warning {
            background: rgba(245, 158, 11, 0.12) !important;
            color: #b45309 !important;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        .manual-badge-allowed {
            background: rgba(23, 162, 184, 0.12) !important;
            color: #0e7490 !important;
            border: 1px solid rgba(23, 162, 184, 0.25);
        }

        .manual-badge-danger {
            background: rgba(220, 53, 69, 0.12) !important;
            color: #b91c1c !important;
            border: 1px solid rgba(220, 53, 69, 0.25);
        }

        .manual-badge-secondary {
            background: rgba(100, 116, 139, 0.1) !important;
            color: #475569 !important;
            border: 1px solid rgba(100, 116, 139, 0.2);
        }

        .manual-result-action {
            flex-shrink: 0;
        }

        .manual-result-action .btn {
            min-width: 110px;
            padding: 0.5rem 1.25rem;
        }

        .manual-result-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1px;
            background: #e2e8f0;
        }

        .manual-stat-item {
            background: #fff;
            padding: 14px 18px;
        }

        .manual-stat-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .manual-stat-value {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            word-break: break-word;
        }

        .manual-challan-details {
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
            margin-top: 6px;
            line-height: 1.55;
        }

        .manual-challan-details strong {
            color: #475569;
            font-weight: 600;
        }

        @media (max-width: 767px) {
            .manual-result-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .manual-result-action {
                width: 100%;
            }

            .manual-result-action .btn {
                width: 100%;
            }

            .manual-search-btn {
                margin-top: 12px;
            }
        }
        */

        /* Summary Stats - sticky with Male/Female tabs */
        .hostel-workspace-sticky {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: #ffffff;
            padding: 12px 0 4px;
            margin: 0 0 12px;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 4px 14px rgba(68, 84, 195, 0.08);
        }

        .hostel-summary-stats-section {
            margin-bottom: 0.75rem;
            background: linear-gradient(135deg, #f8f9ff, #ffffff);
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 14px 4px;
        }

        .hostel-workspace-sticky .nav-tabs {
            margin-bottom: 0;
            border-bottom: none;
        }

        .summary-stats-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        .summary-stats-title {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
        }

        .summary-stats-title i {
            color: #4454c3;
        }

        .summary-stats-gender-badge {
            font-size: 12px;
            font-weight: 600;
            color: #4454c3;
            background: rgba(68, 84, 195, 0.1);
            padding: 4px 12px;
            border-radius: 20px;
        }

        .hostel-stats-dashboard-panel .hostel-stats-container {
            margin-bottom: 0 !important;
        }

        .hostel-workspace-sticky .stats-card .card-header-gradient {
            padding: 8px 16px !important;
        }

        .hostel-workspace-sticky .stats-card .card-body {
            padding: 10px 12px !important;
        }

        .hostel-workspace-sticky .stat-value {
            font-size: 17px !important;
        }

        /* Consistent select height */
        select.form-control:not([size]):not([multiple]) {
            height: 3.375rem;
        }

        /* DataTable Search and Length Styling */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 0;
        }

        .dataTables_wrapper .dataTables_length select {
            border-radius: 6px;
            border: 2px solid #e9ecef;
            padding: 0.375rem 0.75rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 6px;
            border: 2px solid #e9ecef;
            padding: 0.375rem 0.75rem;
            min-width: 200px;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #4454c3;
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.15);
        }

        .dataTables_wrapper .dataTables_info {
            font-size: 0.875rem;
            color: #64748b;
            padding-top: 0.5rem;
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: 0.5rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 6px !important;
        }

        /* Hostel table panel */
        .hostel-table-panel {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 2px 8px rgba(68, 84, 195, 0.06);
        }

        .hostel-table-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .hostel-table-panel-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #334155;
        }

        .hostel-dt-toolbar {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 12px !important;
            align-items: center;
        }

        .hostel-dt-controls {
            margin-bottom: 12px !important;
            align-items: center;
        }

        .hostel-dt-footer {
            margin-top: 12px !important;
            align-items: center;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
        }

        /* Hostel applicants table alignment */
        .hostel-applicants-table {
            width: 100% !important;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 0;
        }

        .hostel-applicants-table thead th {
            background: #f1f5f9 !important;
            color: #334155 !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.7rem 0.6rem !important;
            vertical-align: middle !important;
            white-space: nowrap;
            border-bottom: 2px solid #cbd5e1 !important;
            border-top: none !important;
            line-height: 1.3;
        }

        .hostel-applicants-table tbody td {
            padding: 0.65rem 0.6rem !important;
            vertical-align: middle !important;
            font-size: 0.84rem;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
            line-height: 1.45;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .hostel-applicants-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .hostel-applicants-table tbody tr.allowed-row {
            background-color: rgba(40, 167, 69, 0.06) !important;
        }

        .hostel-applicants-table tbody tr:hover {
            background-color: #eff6ff !important;
            transform: none !important;
        }

        .hostel-applicants-table .col-name {
            font-weight: 600;
            text-transform: capitalize;
        }

        .hostel-applicants-table .col-address {
            font-size: 0.8rem;
            color: #475569;
            white-space: normal;
        }

        .hostel-applicants-table .col-scholarship {
            font-size: 0.8rem;
            white-space: normal;
        }

        /* DataTables scrollX header/body column sync */
        .dataTables_wrapper .dataTables_scroll {
            clear: both;
        }

        .dataTables_wrapper .dataTables_scrollHead {
            overflow: hidden !important;
        }

        .dataTables_wrapper .dataTables_scrollHeadInner {
            width: 100% !important;
            padding-right: 0 !important;
            box-sizing: border-box !important;
        }

        .dataTables_wrapper .dataTables_scrollHeadInner table,
        .dataTables_wrapper .dataTables_scrollBody table {
            width: 100% !important;
            margin: 0 !important;
        }

        .dataTables_wrapper .dataTables_scrollBody {
            border-bottom: 1px solid #e2e8f0;
        }

        #male_aplicants_wrapper .dataTables_scrollHead thead th,
        #female_aplicants_wrapper .dataTables_scrollHead thead th {
            background-color: #f1f5f9 !important;
            border-bottom: 2px solid #cbd5e1 !important;
        }

        /* ===== Modern UI Layer ===== */
        .hostel-modern-page .card-header:not(.hostel-page-header) {
            background: linear-gradient(135deg, #4454c3, #007bff) !important;
        }

        .hostel-modern-page .hostel-workspace-sticky {
            background: rgba(241, 245, 249, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--hostel-border);
            box-shadow: var(--hostel-shadow);
            border-radius: var(--hostel-radius-sm);
            padding: 14px 16px 10px;
            margin-bottom: 16px;
        }

        .hostel-modern-page .hostel-summary-stats-section {
            background: var(--hostel-surface);
            border: 1px solid var(--hostel-border);
            border-radius: var(--hostel-radius-sm);
            padding: 14px 16px 8px;
            box-shadow: var(--hostel-shadow);
        }

        .hostel-modern-page .summary-stats-gender-badge {
            background: var(--hostel-primary-soft);
            color: var(--hostel-primary);
            border: 1px solid #c7d2fe;
            padding: 5px 14px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .hostel-modern-page #newApplicantsTab.hostel-gender-tabs {
            display: inline-flex;
            gap: 6px;
            background: #e2e8f0;
            padding: 5px;
            border-radius: 12px;
            border: none;
            margin-top: 12px;
        }

        .hostel-modern-page #newApplicantsTab.hostel-gender-tabs .nav-item {
            margin: 0;
        }

        .hostel-modern-page #newApplicantsTab.hostel-gender-tabs .nav-link {
            border: none !important;
            border-radius: 8px !important;
            padding: 10px 20px !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: var(--hostel-text-muted) !important;
            background: transparent !important;
            box-shadow: none !important;
            transform: none !important;
            transition: all 0.2s ease !important;
        }

        .hostel-modern-page #newApplicantsTab.hostel-gender-tabs .nav-link:hover {
            color: var(--hostel-text) !important;
            background: rgba(255, 255, 255, 0.6) !important;
        }

        .hostel-modern-page #newApplicantsTab.hostel-gender-tabs .nav-link.active {
            background: var(--hostel-surface) !important;
            color: var(--hostel-primary) !important;
            box-shadow: var(--hostel-shadow) !important;
            border-bottom: none !important;
        }

        .hostel-modern-page #newApplicantsTab.hostel-gender-tabs .nav-link .badge {
            background: #e2e8f0 !important;
            color: var(--hostel-text-muted) !important;
            font-size: 11px !important;
            padding: 3px 8px !important;
            border-radius: 20px !important;
            font-weight: 700 !important;
        }

        .hostel-modern-page #newApplicantsTab.hostel-gender-tabs .nav-link.active .badge {
            background: var(--hostel-primary-soft) !important;
            color: var(--hostel-primary) !important;
        }

        .hostel-modern-page #applicantsTab .nav-link {
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            border-radius: 8px 8px 0 0 !important;
        }

        .hostel-modern-page .hostel-table-panel {
            background: var(--hostel-surface);
            border: 1px solid var(--hostel-border);
            border-radius: var(--hostel-radius);
            padding: 0;
            box-shadow: var(--hostel-shadow-md);
            overflow: hidden;
        }

        .hostel-modern-page .hostel-table-panel-header {
            padding: 16px 20px;
            margin: 0;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            border-bottom: 1px solid var(--hostel-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .hostel-modern-page .hostel-table-panel-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--hostel-text);
            letter-spacing: -0.01em;
        }

        .hostel-modern-page .hostel-table-panel-title .fa {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--hostel-primary-soft);
            color: var(--hostel-primary);
            border-radius: 8px;
            font-size: 14px !important;
            margin-right: 10px !important;
        }

        .hostel-modern-page .hostel-table-panel .table-responsive,
        .hostel-modern-page .hostel-table-panel .dataTables_wrapper {
            padding: 0 16px 16px;
        }

        .hostel-modern-page .hostel-applicants-table thead th {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%) !important;
            color: #475569 !important;
            font-size: 0.68rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.06em;
            border-bottom: 1px solid var(--hostel-border) !important;
            padding: 0.85rem 0.65rem !important;
        }

        .hostel-modern-page .hostel-applicants-table tbody td {
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.8125rem;
        }

        .hostel-modern-page .hostel-applicants-table tbody tr:hover {
            background-color: #f5f3ff !important;
        }

        .hostel-modern-page .hostel-dt-toolbar {
            background: #f8fafc;
            border: 1px solid var(--hostel-border);
            border-radius: var(--hostel-radius-sm);
            padding: 12px 14px;
            margin: 16px 0 10px !important;
        }

        .hostel-modern-page .dt-button {
            background: var(--hostel-surface) !important;
            color: var(--hostel-text) !important;
            border: 1px solid var(--hostel-border) !important;
            border-radius: 8px !important;
            padding: 0.45rem 1rem !important;
            font-size: 0.8125rem !important;
            font-weight: 600 !important;
            box-shadow: var(--hostel-shadow) !important;
            margin-right: 6px !important;
        }

        .hostel-modern-page .dt-button:hover {
            background: var(--hostel-primary-soft) !important;
            color: var(--hostel-primary) !important;
            border-color: #c7d2fe !important;
            transform: translateY(-1px) !important;
            box-shadow: var(--hostel-shadow-md) !important;
        }

        .hostel-modern-page .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px;
            border: 1px solid var(--hostel-border);
            padding: 0.5rem 0.85rem;
            background: var(--hostel-surface);
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .hostel-modern-page .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--hostel-primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
            outline: none;
        }

        .hostel-modern-page .dataTables_wrapper .dataTables_length select {
            border-radius: 8px;
            border: 1px solid var(--hostel-border);
            background: var(--hostel-surface);
        }

        .hostel-modern-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--hostel-primary) !important;
            color: white !important;
            border: none !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
        }

        .hostel-modern-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--hostel-primary-soft) !important;
            color: var(--hostel-primary) !important;
            border: 1px solid #c7d2fe !important;
            border-radius: 8px !important;
        }

        .hostel-modern-page .filter-section.hostel-dt-filters {
            background: linear-gradient(135deg, #fafbff 0%, #f8fafc 100%);
            border: 1px solid var(--hostel-border);
            border-radius: var(--hostel-radius-sm);
            box-shadow: none;
        }

        .hostel-modern-page .filter-section.hostel-dt-filters .filter-title {
            color: var(--hostel-text);
            font-size: 13px;
        }

        .hostel-modern-page .filter-select {
            border-radius: 8px !important;
            border: 1px solid var(--hostel-border) !important;
            background: var(--hostel-surface) !important;
        }

        .hostel-modern-page .filter-select:focus {
            border-color: var(--hostel-primary) !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important;
        }

        .hostel-modern-page .important-note-container {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 1px solid #fde68a;
            border-radius: var(--hostel-radius-sm);
            box-shadow: var(--hostel-shadow);
        }

        .hostel-modern-page .important-note-icon {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
        }

        .hostel-modern-page .hostel-empty-state {
            padding: 3.5rem 2rem;
            background: var(--hostel-surface);
            border-radius: var(--hostel-radius);
            border: 2px dashed var(--hostel-border);
            text-align: center;
        }

        .hostel-modern-page .hostel-empty-state .fa {
            width: 72px;
            height: 72px;
            line-height: 72px;
            background: var(--hostel-primary-soft);
            color: var(--hostel-primary);
            border-radius: 50%;
            font-size: 1.75rem;
            margin-bottom: 1.25rem;
        }

        .hostel-modern-page .hostel-data-loading-overlay {
            background: rgba(241, 245, 249, 0.9);
            backdrop-filter: blur(4px);
            border-radius: var(--hostel-radius);
        }

        .hostel-modern-page .hostel-stats-container .stats-card {
            border: 1px solid var(--hostel-border) !important;
            border-radius: var(--hostel-radius-sm) !important;
            box-shadow: var(--hostel-shadow) !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease !important;
        }

        .hostel-modern-page .hostel-stats-container .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--hostel-shadow-md) !important;
        }

        .hostel-modern-page .hostel-stats-container .card-header-gradient {
            background: linear-gradient(135deg, #312e81, #4f46e5) !important;
        }

        .hostel-modern-page .hostel-stats-container .stat-value {
            font-size: 1.35rem !important;
            font-weight: 800 !important;
            color: var(--hostel-text) !important;
            letter-spacing: -0.02em;
        }

        .hostel-modern-page .hostel-stats-container .stat-label {
            font-size: 0.65rem !important;
            font-weight: 700 !important;
            color: var(--hostel-text-muted) !important;
            letter-spacing: 0.05em;
        }

        .hostel-modern-page .badge.badge-paid,
        .hostel-modern-page .badge.badge-seat-reserved {
            background: linear-gradient(135deg, #059669, #10b981) !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
            letter-spacing: 0.03em;
        }

        .hostel-modern-page .badge.badge-unpaid {
            background: linear-gradient(135deg, #dc2626, #f97316) !important;
            border-radius: 6px !important;
        }

        .hostel-modern-page .badge.badge-allowed {
            background: linear-gradient(135deg, #0891b2, #06b6d4) !important;
            border-radius: 6px !important;
        }

        .hostel-modern-page .badge.badge-secondary {
            background: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid var(--hostel-border) !important;
            border-radius: 6px !important;
        }

        .hostel-modern-page .btn-outline-success {
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 0.8rem !important;
            border-width: 1.5px !important;
            transition: all 0.2s ease !important;
        }

        .hostel-modern-page .btn-outline-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        .hostel-modern-page .btn-outline-danger {
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 0.8rem !important;
            border-width: 1.5px !important;
        }

        .hostel-modern-page .select2-container--default .select2-selection--single {
            border: 1px solid var(--hostel-border) !important;
            border-radius: var(--hostel-radius-sm) !important;
            min-height: 50px !important;
            background: var(--hostel-surface) !important;
        }

        .hostel-modern-page .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 48px !important;
            padding-left: 14px !important;
            color: var(--hostel-text) !important;
        }

        .hostel-modern-page .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--hostel-primary) !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important;
        }

        /* ===== Allow / Reject Modals ===== */
        .hostel-action-modal .modal-dialog {
            max-width: 480px;
        }

        .hostel-action-modal .modal-content {
            border: none;
            border-radius: var(--hostel-radius);
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
            overflow: hidden;
        }

        .hostel-action-modal .modal-header {
            border: none;
            padding: 0;
            position: relative;
        }

        .hostel-action-modal .hostel-modal-header-inner {
            padding: 1.5rem 1.5rem 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .hostel-action-modal.allow-modal .hostel-modal-header-inner {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-bottom: 1px solid #a7f3d0;
        }

        .hostel-action-modal.reject-modal .hostel-modal-header-inner {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-bottom: 1px solid #fecaca;
        }

        .hostel-action-modal .hostel-modal-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.25rem;
        }

        .hostel-action-modal.allow-modal .hostel-modal-icon {
            background: linear-gradient(135deg, #059669, #10b981);
            color: #fff;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        }

        .hostel-action-modal.reject-modal .hostel-modal-icon {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: #fff;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
        }

        .hostel-action-modal .modal-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--hostel-text);
            margin: 0 0 4px;
            letter-spacing: -0.01em;
        }

        .hostel-action-modal .hostel-modal-subtitle {
            font-size: 0.8125rem;
            color: var(--hostel-text-muted);
            margin: 0;
            line-height: 1.45;
        }

        .hostel-action-modal .close {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.7);
            color: var(--hostel-text-muted);
            opacity: 1;
            padding: 0;
            margin: 0;
            transition: all 0.2s ease;
            z-index: 2;
        }

        .hostel-action-modal .close:hover {
            background: #fff;
            color: var(--hostel-text);
            transform: scale(1.05);
        }

        .hostel-action-modal .modal-body {
            padding: 1.25rem 1.5rem 1rem;
            background: var(--hostel-surface);
        }

        .hostel-action-modal .hostel-modal-section-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--hostel-text-muted);
            margin-bottom: 10px;
        }

        .hostel-action-modal .hostel-location-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .hostel-action-modal .hostel-location-option {
            position: relative;
            margin: 0;
            padding: 0;
        }

        .hostel-action-modal .hostel-location-option .custom-control-input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .hostel-action-modal .hostel-location-option .custom-control-label {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 14px 16px;
            margin: 0;
            border: 2px solid var(--hostel-border);
            border-radius: var(--hostel-radius-sm);
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
            color: var(--hostel-text);
        }

        .hostel-action-modal .hostel-location-option .custom-control-label::before,
        .hostel-action-modal .hostel-location-option .custom-control-label::after {
            display: none;
        }

        .hostel-action-modal .hostel-location-option .option-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .hostel-action-modal .hostel-location-option .option-icon.on-campus {
            background: #eef2ff;
            color: var(--hostel-primary);
        }

        .hostel-action-modal .hostel-location-option .option-icon.off-campus {
            background: #ecfeff;
            color: #0891b2;
        }

        .hostel-action-modal .hostel-location-option .option-text {
            flex: 1;
        }

        .hostel-action-modal .hostel-location-option .option-text strong {
            display: block;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .hostel-action-modal .hostel-location-option .option-text small {
            display: block;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--hostel-text-muted);
        }

        .hostel-action-modal .hostel-location-option .option-check {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s ease;
            color: transparent;
            font-size: 10px;
        }

        .hostel-action-modal .hostel-location-option .custom-control-input:checked + .custom-control-label {
            border-color: var(--hostel-success);
            background: #ecfdf5;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
        }

        .hostel-action-modal .hostel-location-option .custom-control-input:checked + .custom-control-label .option-check {
            background: var(--hostel-success);
            border-color: var(--hostel-success);
            color: #fff;
        }

        .hostel-action-modal .hostel-modal-info {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 14px;
            padding: 12px 14px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            font-size: 0.8125rem;
            color: #1e40af;
            line-height: 1.45;
        }

        .hostel-action-modal .hostel-modal-info .fa {
            margin-top: 2px;
            font-size: 1rem;
            opacity: 0.85;
        }

        .hostel-action-modal .hostel-reject-warning {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: var(--hostel-radius-sm);
        }

        .hostel-action-modal .hostel-reject-warning .warning-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
        }

        .hostel-action-modal .hostel-reject-warning .warning-text {
            font-size: 0.9rem;
            color: #92400e;
            line-height: 1.5;
            margin: 0;
        }

        .hostel-action-modal .hostel-reject-warning .warning-text strong {
            display: block;
            font-size: 0.95rem;
            color: #78350f;
            margin-bottom: 4px;
        }

        .hostel-action-modal .modal-footer {
            border-top: 1px solid var(--hostel-border);
            padding: 1rem 1.5rem 1.25rem;
            background: #f8fafc;
            gap: 10px;
        }

        .hostel-action-modal .modal-footer .btn {
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.55rem 1.25rem;
            min-width: 100px;
            transition: all 0.2s ease;
        }

        .hostel-action-modal .modal-footer .hostel-modal-btn-cancel {
            background: #ffffff !important;
            border: 1.5px solid #94a3b8 !important;
            color: #334155 !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06) !important;
        }

        .hostel-action-modal .modal-footer .hostel-modal-btn-cancel:hover,
        .hostel-action-modal .modal-footer .hostel-modal-btn-cancel:focus {
            background: #f1f5f9 !important;
            border-color: #64748b !important;
            color: #0f172a !important;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.1) !important;
            transform: translateY(-1px);
        }

        .hostel-action-modal .modal-footer .hostel-modal-btn-cancel:active {
            background: #e2e8f0 !important;
            border-color: #475569 !important;
            transform: translateY(0);
        }

        .hostel-action-modal.allow-modal .modal-footer .hostel-modal-btn-cancel {
            border-color: #cbd5e1 !important;
            color: #475569 !important;
        }

        .hostel-action-modal.reject-modal .modal-footer .hostel-modal-btn-cancel {
            background: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #1e293b !important;
        }

        .hostel-action-modal.reject-modal .modal-footer .hostel-modal-btn-cancel:hover,
        .hostel-action-modal.reject-modal .modal-footer .hostel-modal-btn-cancel:focus {
            background: #e2e8f0 !important;
            border-color: #64748b !important;
            color: #0f172a !important;
        }

        .hostel-action-modal .modal-footer .hostel-modal-btn-cancel .fa {
            color: inherit;
            opacity: 0.85;
        }

        .hostel-action-modal .modal-footer .btn-success {
            background: linear-gradient(135deg, #059669, #10b981);
            border: none;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .hostel-action-modal .modal-footer .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
        }

        .hostel-action-modal .modal-footer .btn-danger {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            border: none;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .hostel-action-modal .modal-footer .btn-danger:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
        }

        /* Add New Applicant */
        .hostel-applicants-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 0;
        }

        .hostel-applicants-toolbar .nav-tabs {
            margin-bottom: 0;
            flex: 1;
        }

        .hostel-btn-add-applicant {
            background: linear-gradient(135deg, #4f46e5, #6366f1) !important;
            border: none !important;
            color: #fff !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            padding: 0.55rem 1.15rem !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3) !important;
            white-space: nowrap;
        }

        .hostel-btn-add-applicant:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4) !important;
            color: #fff !important;
        }

        .hostel-action-modal.add-applicant-modal .hostel-modal-header-inner {
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            border-bottom: 1px solid #c7d2fe;
        }

        .hostel-action-modal.add-applicant-modal .hostel-modal-icon {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
        }

        .hostel-add-search-group .form-control {
            border-radius: 8px 0 0 8px;
            border: 1.5px solid var(--hostel-border);
            min-height: 44px;
        }

        .hostel-add-search-group .btn {
            border-radius: 0 8px 8px 0;
            min-height: 44px;
            font-weight: 600;
            padding: 0 1.1rem;
        }

        .hostel-add-applicant-card {
            display: none;
            margin-top: 16px;
            padding: 16px;
            background: #f8fafc;
            border: 1px solid var(--hostel-border);
            border-radius: var(--hostel-radius-sm);
        }

        .hostel-add-applicant-card.is-visible {
            display: block;
        }

        .hostel-add-applicant-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px 16px;
            margin-bottom: 14px;
        }

        .hostel-add-applicant-field .label {
            display: block;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--hostel-text-muted);
            margin-bottom: 3px;
        }

        .hostel-add-applicant-field .value {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--hostel-text);
            word-break: break-word;
        }

        .hostel-consent-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .hostel-consent-status-badge.is-yes {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .hostel-consent-status-badge.is-no {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .hostel-add-schedule-warning {
            display: none;
            margin-top: 12px;
            padding: 10px 12px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            font-size: 0.8rem;
            color: #92400e;
        }

        .hostel-add-schedule-warning.is-visible {
            display: block;
        }

        @media (max-width: 575px) {
            .hostel-add-applicant-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="container-fluid py-4 hostel-modern-page">
        <div class="card shadow-sm hostel-main-card">

            <div class="card-header hostel-page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="hostel-page-title text-white"><i class="fa fa-building mr-2"></i>Hostel Applicants</h4>
                        <p class="hostel-page-subtitle text-white mb-0">Manage consent-based applicants, seat allocation & challan status</p>
                    </div>
                </div>
            </div>

            <div class="card-body hostel-filters-card">
                <div class="row mx-0">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="session_id" class="font-weight-bold text-primary">
                                <i class="fa fa-calendar mr-2"></i>Academic Session <span class="text-danger">*</span>
                            </label>
                            <select id="session_id" name="session_id" class="form-control form-control-lg select2" required>
                                <option value="">Select Academic Session</option>
                                @foreach ($session as $sess)
                                    <option value="{{ $sess->sess_id }}">{{ $sess->sess_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="admission_schedule_id" class="font-weight-bold text-primary">
                                <i class="fa fa-calendar mr-2"></i>Admission Schedule <span class="text-danger">*</span>
                            </label>
                            <select id="admission_schedule_id" name="admission_schedule_id"
                                class="form-control form-control-lg select2" required>
                                <option value="">Select Admission Schedule</option>
                                <option value="1">Early Round</option>
                                <option value="2">Regular Round</option>
                                <option value="3">Rolling Round</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Manual Applicant Search - hidden
                <div class="manual-search-section filter-section" id="manual-search-section">
                    <div class="filter-header">
                        <h6 class="filter-title mb-1">
                            <i class="fa fa-search"></i> Manual Applicant Search
                        </h6>
                        <div class="filter-subtitle">
                            Search by Applicant ID or Enrollment No. to allow/assign an applicant manually.
                            Consent-based list below remains unchanged.
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="col-md-9 col-lg-10">
                            <label for="manual_applicant_search" class="filter-label">
                                <i class="fa fa-id-card"></i> Applicant ID / Enrollment No.
                            </label>
                            <div class="input-group manual-search-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                                </div>
                                <input type="text" id="manual_applicant_search" class="form-control"
                                    placeholder="e.g. 104285 or SUM26-0015">
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <button type="button" id="btnManualSearch" class="btn btn-primary btn-block manual-search-btn">
                                <i class="fa fa-search mr-1"></i> Search
                            </button>
                        </div>
                    </div>
                </div>

                <div id="manual-search-result" class="d-none mt-3">
                    <div class="manual-result-card">
                        <div id="manual-search-result-body"></div>
                    </div>
                </div>
                --}}

                <div class="row mx-0">
                    <div class="col-md-6" style="display: none;">
                        <div class="form-group">
                            <label for="hostel_type" class="font-weight-bold text-primary">
                                <i class="fa fa-building mr-2"></i>Hostel Type
                            </label>
                            <select id="hostel_type" name="hostel_type" class="form-control form-control-lg select2">
                                <option value="">Select Hostel Type</option>
                                <option value="regular">University Hostel - (Paid)</option>
                                <option value="regular_outside">External Hostel - (Paid/Unpaid)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>




            <div class="card-body position-relative hostel-content-body">
                <div class="form-group mb-4">
                    <div class="input-group">
                        {{-- <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                        </div> --}}
                        {{-- <input type="text" id="searchInput" class="form-control" placeholder="Search applicants...">
                        --}}
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div></div>
                    <!-- Export buttons are now handled by DataTables -->
                </div>

                <div id="hostel-applicants-panel">
                    <div id="hostel-data-loading-overlay" class="hostel-data-loading-overlay d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-3 mb-0">Loading hostel applicants data...</p>
                    </div>

                    <div id="content-section" style="display: none;">
                        <div id="no-session-message" class="hostel-empty-state" style="display: none;">
                            <i class="fa fa-calendar-times"></i>
                            <h5 class="font-weight-bold mb-2" style="color: var(--hostel-text);">Select Session & Schedule</h5>
                            <p class="text-muted mb-0">Choose Academic Session and Admission Schedule above to load applicants.</p>
                        </div>

                        <div id="data-content">
                            <!-- Important Note - Only visible when data is loaded -->
                            <div id="important-note" class="mb-4" style="display: none;">
                                <div class="important-note-container">
                                    <div class="important-note-content">
                                        <div class="important-note-icon">
                                            <i class="fa fa-lightbulb-o"></i>
                                        </div>
                                        <div class="important-note-text">
                                            <div class="important-note-title">Important Note:</div>
                                            <div class="important-note-description">
                                                You cannot allow more applicants than available seats. If you need to allow
                                                more
                                                applicants,
                                                first vacate seats from applicants who haven't given consent yet (shown in
                                                "Can
                                                Vacate" count).
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="hostel-applicants-toolbar">
                                <ul class="nav nav-tabs" id="applicantsTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="new-students-tab" data-toggle="tab" href="#new-students"
                                            role="tab">
                                            <i class="fa fa-user-plus mr-1"></i>Applicants <span class="badge badge-primary"
                                                id="new_count">0</span>
                                        </a>
                                    </li>
                                </ul>
                                <button type="button" class="btn hostel-btn-add-applicant" id="btnAddNewApplicant">
                                    <i class="fa fa-user-plus mr-1"></i>Add New Applicant
                                </button>
                            </div>

                            <div class="tab-content mt-4" id="applicantsTabContent">
                                <!-- Applicants Tab -->
                                <div class="tab-pane fade show active" id="new-students" role="tabpanel">
                                    <div id="hostel-workspace-sticky" class="hostel-workspace-sticky d-none">
                                        <div id="hostel-summary-stats-section" class="hostel-summary-stats-section d-none">
                                            <div class="summary-stats-header">
                                                <h6 class="summary-stats-title">
                                                    <i class="fa fa-bar-chart mr-2"></i>Summary Stats
                                                </h6>
                                                <span class="summary-stats-gender-badge"
                                                    id="summary-stats-gender-label"></span>
                                            </div>
                                            <div id="male-stats-dashboard-container" class="hostel-stats-dashboard-panel">
                                            </div>
                                            <div id="female-stats-dashboard-container"
                                                class="hostel-stats-dashboard-panel d-none"></div>
                                        </div>

                                        <ul class="nav nav-tabs hostel-gender-tabs" id="newApplicantsTab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="new-male-tab" data-toggle="tab"
                                                    href="#new-male" role="tab">
                                                    <i class="fa fa-male mr-1"></i>Male <span class="badge badge-primary"
                                                        id="maleCount">0</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="new-female-tab" data-toggle="tab" href="#new-female"
                                                    role="tab">
                                                    <i class="fa fa-female mr-1"></i>Female <span
                                                        class="badge badge-primary" id="femaleCount">0</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="tab-content mt-2" id="newApplicantsTabContent">
                                        <!-- Male Applicants -->
                                        <div class="tab-pane fade show active" id="new-male" role="tabpanel">
                                            @include('Hostel.components.male_applicants_table')
                                            <div class="hostel-table-panel">
                                                <div class="hostel-table-panel-header">
                                                    <h5 class="hostel-table-panel-title"><i class="fa fa-male"></i>Male Applicants</h5>
                                                    <span class="summary-stats-gender-badge d-none d-md-inline">Consent List</span>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table hostel-applicants-table mb-0" id="male_aplicants">
                                                        <thead>
                                                            <tr>
                                                                <th title="Student/Applicant ID">Applicant ID</th>
                                                                <th title="Enrollment Number">Enrollment</th>
                                                                <th title="Admission Round">Round</th>
                                                                <th title="Applicant Name">Name</th>
                                                                <th title="City">City</th>
                                                                <th title="Permanent Address">Address</th>
                                                                <th title="Apply Date">Apply Date</th>
                                                                <th title="Allowed Status">Status</th>
                                                                <th title="Hostel Location">Location</th>
                                                                <th title="Room Details">Room</th>
                                                                <th title="Scholarship Details">Scholarship</th>
                                                                <th title="Last Degree Marks">Marks</th>
                                                                <th title="Challan Status">Challan</th>
                                                                <th class="text-center" title="Disable Map Display">Map Off</th>
                                                                <th class="text-center">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="male-applicants-tbody">
                                                            <!-- Data will be loaded here via AJAX -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Female Applicants -->
                                        <div class="tab-pane fade" id="new-female" role="tabpanel">
                                            @include('Hostel.components.female_applicants_table')
                                            <div class="hostel-table-panel">
                                                <div class="hostel-table-panel-header">
                                                    <h5 class="hostel-table-panel-title"><i class="fa fa-female"></i>Female Applicants</h5>
                                                    <span class="summary-stats-gender-badge d-none d-md-inline">Consent List</span>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table hostel-applicants-table mb-0" id="female_aplicants">
                                                        <thead>
                                                            <tr>
                                                                <th title="Student/Applicant ID">Applicant ID</th>
                                                                <th title="Enrollment Number">Enrollment</th>
                                                                <th title="Admission Round">Round</th>
                                                                <th title="Applicant Name">Name</th>
                                                                <th title="City">City</th>
                                                                <th title="Permanent Address">Address</th>
                                                                <th title="Apply Date">Apply Date</th>
                                                                <th title="Allowed Status">Status</th>
                                                                <th title="Hostel Location">Location</th>
                                                                <th title="Room Details">Room</th>
                                                                <th title="Scholarship Details">Scholarship</th>
                                                                <th title="Last Degree Marks">Marks</th>
                                                                <th title="Challan Status">Challan</th>
                                                                <th class="text-center" title="Disable Map Display">Map Off</th>
                                                                <th class="text-center">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="female-applicants-tbody">
                                                            <!-- Data will be loaded here via AJAX -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Allow Confirmation Modal -->
        <div class="modal fade hostel-action-modal allow-modal" id="allowModal" tabindex="-1" role="dialog"
            aria-labelledby="allowModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="hostel-modal-header-inner w-100">
                            <div class="hostel-modal-icon">
                                <i class="fa fa-check-circle"></i>
                            </div>
                            <div>
                                <h5 class="modal-title" id="allowModalLabel">Allow Applicant</h5>
                                <p class="hostel-modal-subtitle">Select hostel location preference before confirming</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="hostel-modal-section-label">Hostel Location <span class="text-danger">*</span></div>

                        <div class="hostel-location-options">
                            <div class="custom-control custom-radio hostel-location-option">
                                <input type="radio" id="locationOnCampus" name="hostel_location"
                                    class="custom-control-input" value="on_campus">
                                <label class="custom-control-label" for="locationOnCampus">
                                    <span class="option-icon on-campus"><i class="fa fa-building"></i></span>
                                    <span class="option-text">
                                        <strong>On Campus Hostel</strong>
                                        <small>University main campus accommodation</small>
                                    </span>
                                    <span class="option-check"><i class="fa fa-check"></i></span>
                                </label>
                            </div>
                            <div class="custom-control custom-radio hostel-location-option">
                                <input type="radio" id="locationOffCampus" name="hostel_location"
                                    class="custom-control-input" value="off_campus">
                                <label class="custom-control-label" for="locationOffCampus">
                                    <span class="option-icon off-campus"><i class="fa fa-home"></i></span>
                                    <span class="option-text">
                                        <strong>Off Campus Safari Hostel</strong>
                                        <small>External safari hostel accommodation</small>
                                    </span>
                                    <span class="option-check"><i class="fa fa-check"></i></span>
                                </label>
                            </div>
                        </div>

                        <div class="hostel-modal-info">
                            <i class="fa fa-info-circle"></i>
                            <span>This preference will be used for hostel assignment and seat allocation.</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn hostel-modal-btn-cancel" data-dismiss="modal">
                            <i class="fa fa-times mr-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-success" id="confirmAllow">
                            <i class="fa fa-check mr-1"></i>Allow Applicant
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Confirmation Modal -->
        <div class="modal fade hostel-action-modal reject-modal" id="rejectModal" tabindex="-1" role="dialog"
            aria-labelledby="rejectModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="hostel-modal-header-inner w-100">
                            <div class="hostel-modal-icon">
                                <i class="fa fa-times-circle"></i>
                            </div>
                            <div>
                                <h5 class="modal-title" id="rejectModalLabel">Reject Applicant</h5>
                                <p class="hostel-modal-subtitle">This action will remove the applicant's allowed status</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="hostel-reject-warning">
                            <div class="warning-icon">
                                <i class="fa fa-exclamation-triangle"></i>
                            </div>
                            <p class="warning-text">
                                <strong>Are you sure you want to reject?</strong>
                                The applicant will be marked as rejected and their seat reservation will be released.
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn hostel-modal-btn-cancel" data-dismiss="modal">
                            <i class="fa fa-arrow-left mr-1"></i>Go Back
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmReject">
                            <i class="fa fa-times mr-1"></i>Confirm Reject
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add New Applicant Modal -->
        <div class="modal fade hostel-action-modal add-applicant-modal" id="addApplicantModal" tabindex="-1" role="dialog"
            aria-labelledby="addApplicantModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="hostel-modal-header-inner w-100">
                            <div class="hostel-modal-icon">
                                <i class="fa fa-user-plus"></i>
                            </div>
                            <div>
                                <h5 class="modal-title" id="addApplicantModalLabel">Add New Applicant</h5>
                                <p class="hostel-modal-subtitle">Search by Applicant ID to grant hostel consent</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="hostel-modal-section-label">Applicant ID / Enrollment No.</div>
                        <div class="input-group hostel-add-search-group">
                            <input type="text" id="add_applicant_search" class="form-control"
                                placeholder="e.g. 104288 or SUM26-0015">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" id="btnSearchAddApplicant">
                                    <i class="fa fa-search mr-1"></i>Search
                                </button>
                            </div>
                        </div>

                        <div id="add-applicant-result" class="hostel-add-applicant-card">
                            <div class="hostel-add-applicant-grid">
                                <div class="hostel-add-applicant-field">
                                    <span class="label">Applicant ID</span>
                                    <span class="value" id="add-result-aai-id">b </span>
                                </div>
                                <div class="hostel-add-applicant-field">
                                    <span class="label">Enrollment No</span>
                                    <span class="value" id="add-result-enrollment">b </span>
                                </div>
                                <div class="hostel-add-applicant-field">
                                    <span class="label">Name</span>
                                    <span class="value" id="add-result-name">b </span>
                                </div>
                                <div class="hostel-add-applicant-field">
                                    <span class="label">Gender</span>
                                    <span class="value" id="add-result-gender">b </span>
                                </div>
                                <div class="hostel-add-applicant-field">
                                    <span class="label">City</span>
                                    <span class="value" id="add-result-city">b </span>
                                </div>
                                <div class="hostel-add-applicant-field">
                                    <span class="label">Admission Round</span>
                                    <span class="value" id="add-result-round">b </span>
                                </div>
                                <div class="hostel-add-applicant-field" style="grid-column: span 2;">
                                    <span class="label">Hostel Consent</span>
                                    <span class="value" id="add-result-consent-wrap">
                                        <span class="hostel-consent-status-badge is-no" id="add-result-consent">
                                            <i class="fa fa-times-circle"></i> No
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div id="add-schedule-warning" class="hostel-add-schedule-warning">
                                <i class="fa fa-exclamation-triangle mr-1"></i>
                                <span id="add-schedule-warning-text"></span>
                            </div>
                            <div class="hostel-modal-info mb-0" id="add-consent-info">
                                <i class="fa fa-info-circle"></i>
                                <span>Granting consent will set hostel preference to <strong>Yes</strong> and show this applicant in the consent list below.</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn hostel-modal-btn-cancel" data-dismiss="modal">
                            <i class="fa fa-times mr-1"></i>Close
                        </button>
                        <button type="button" class="btn btn-success d-none" id="confirmGrantConsent">
                            <i class="fa fa-check mr-1"></i>Grant Hostel Consent
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Drill-down Modal (Single Global Modal) -->
        <div class="modal fade" id="drillDownModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                    <div class="modal-header bg-primary text-white border-0" style="border-radius: 12px 12px 0 0;">
                        <h5 class="modal-title font-weight-bold text-white">
                            <i class="fa fa-users mr-2"></i> <span id="drillDownTitle" class="text-white">Student
                                List</span>
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0" id="drillDownTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="pl-4">#</th>
                                        <th>Student/Applicant ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Location</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="drillDownBody">
                                    <!-- Students list will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        <div id="drillDownLoading" class="text-center py-5" style="display: none;">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted font-weight-bold">Loading student list...</p>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light" style="border-radius: 0 0 12px 12px;">
                        <button type="button" class="btn btn-secondary font-weight-bold px-4"
                            data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <script>
            let suppressHostelFilterEvents = false;
            let hostelApplicantsRequest = null;
            let hostelLoadToken = 0;
            let hostelDashboardRequests = { male: null, female: null };

            function setHostelLoading(isLoading) {
                if (isLoading) {
                    $('#hostel-data-loading-overlay').removeClass('d-none');
                    $('#hostel-applicants-panel').addClass('is-loading');
                } else {
                    $('#hostel-data-loading-overlay').addClass('d-none');
                    $('#hostel-applicants-panel').removeClass('is-loading');
                }
            }

            function resetFilterFieldsSilently() {
                suppressHostelFilterEvents = true;
                try {
                    $('#admission_schedule_id').val('').trigger('change');
                    $('#hostel_type').val('').trigger('change');
                } finally {
                    suppressHostelFilterEvents = false;
                }
            }

            function unwrapDataTable(tableSelector) {
                const $table = $(tableSelector);
                if (!$table.length) {
                    return;
                }

                const $wrapper = $table.closest('.dataTables_wrapper');
                if ($wrapper.length) {
                    $table.insertBefore($wrapper);
                    $wrapper.remove();
                }
            }

            $(document).ready(function () {
                // Initialize Select2 for professional dropdowns
                $('.select2').select2({
                    width: '100%',
                    placeholder: 'Select an option'
                });

                // Force larger font size for dropdown options
                function forceDropdownFontSize() {
                    $('select.form-control-lg').css('font-size', '16px');
                    $('select.form-control-lg option').css('font-size', '16px');
                    $('#session_id, #admission_schedule_id').css('font-size', '16px');
                    $('#session_id option, #admission_schedule_id option').css('font-size', '16px');
                }

                // Apply font size immediately and on window load
                forceDropdownFontSize();
                $(window).on('load', forceDropdownFontSize);

                // Add change event listener for hostel type dropdown
                $('#hostel_type').on('change', function () {
                    if (suppressHostelFilterEvents) {
                        return;
                    }

                    const sessionId = $('#session_id').val();

                    // Clear dashboard containers immediately on change
                    $('#male-stats-dashboard-container').empty();
                    $('#female-stats-dashboard-container').empty();

                    if (sessionId) {
                        loadHostelData();
                    } else {
                        showToast('error', 'Please select Academic Session first.');
                    }
                });

                // Add change event listener for session dropdown
                $('#session_id').on('change', function () {
                    const sessionId = $(this).val();

                    // Clear dashboard containers immediately on change
                    $('#male-stats-dashboard-container').empty();
                    $('#female-stats-dashboard-container').empty();

                    // Reset dependent filters without triggering duplicate reloads
                    resetFilterFieldsSilently();

                    if (sessionId) {
                        loadHostelData();
                    } else {
                        // Clear all data when session is not selected
                        clearAllData();
                    }
                });

                // Add change event listener for admission schedule dropdown
                $('#admission_schedule_id').on('change', function () {
                    if (suppressHostelFilterEvents) {
                        return;
                    }

                    const sessionId = $('#session_id').val();

                    // Clear dashboard containers immediately on change
                    $('#male-stats-dashboard-container').empty();
                    $('#female-stats-dashboard-container').empty();

                    if (sessionId) {
                        loadHostelData();
                    } else {
                        showToast('error', 'Please select Academic Session first.');
                    }
                });


                $('#btnAddNewApplicant').on('click', function () {
                    resetAddApplicantModal();
                    $('#addApplicantModal').modal('show');
                });

                $('#btnSearchAddApplicant').on('click', function () {
                    searchApplicantForConsent();
                });

                $('#add_applicant_search').on('keypress', function (e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        searchApplicantForConsent();
                    }
                });

                $('#confirmGrantConsent').on('click', function () {
                    grantHostelConsent();
                });

                $('#addApplicantModal').on('hidden.bs.modal', function () {
                    resetAddApplicantModal();
                });

                // $('#btnManualSearch').on('click', function () {
                //     searchManualApplicant();
                // });

                // $('#manual_applicant_search').on('keypress', function (e) {
                //     if (e.which === 13) {
                //         e.preventDefault();
                //         searchManualApplicant();
                //     }
                // });

                // Don't load data by default - wait for session selection
                clearAllData();

                // Tab switch listener to load dashboard for the active tab
                $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                    const target = $(e.target).attr("href");
                    if (target === '#new-male') {
                        loadHostelDashboard('male');
                        if ($.fn.DataTable.isDataTable('#male_aplicants')) {
                            $('#male_aplicants').DataTable().columns.adjust();
                        }
                    } else if (target === '#new-female') {
                        loadHostelDashboard('female');
                        if ($.fn.DataTable.isDataTable('#female_aplicants')) {
                            $('#female_aplicants').DataTable().columns.adjust();
                        }
                    }
                });
            });

            /**
             * Show summary stats panel for a specific gender.
             */
            function showSummaryStatsForGender(gender) {
                const genderLabel = gender === 'male' ? 'Male Applicants' : 'Female Applicants';
                $('#summary-stats-gender-label').text(genderLabel);

                if (gender === 'male') {
                    $('#male-stats-dashboard-container').removeClass('d-none');
                    $('#female-stats-dashboard-container').addClass('d-none');
                } else {
                    $('#male-stats-dashboard-container').addClass('d-none');
                    $('#female-stats-dashboard-container').removeClass('d-none');
                }

                $('#hostel-summary-stats-section').removeClass('d-none');
                $('#hostel-workspace-sticky').removeClass('d-none');
            }

            /**
             * Sync summary stats visibility with the active Male/Female tab.
             */
            function updateSummaryStatsVisibility() {
                const isMaleTab = $('#new-male-tab').hasClass('active');
                showSummaryStatsForGender(isMaleTab ? 'male' : 'female');
            }

            /**
             * Load the professional hostel stats dashboard via AJAX.
             * @param {string} gender - 'male' or 'female'
             * @param {boolean} switchView - when true, show this gender's stats panel (tab switch); when false, only refresh data in background
             */
            function loadHostelDashboard(gender, switchView) {
                const sessionId = $('#session_id').val();
                const container = (gender === 'male') ? $('#male-stats-dashboard-container') : $('#female-stats-dashboard-container');
                const shouldSwitchView = switchView !== false;

                if (!sessionId) return;

                if (shouldSwitchView) {
                    showSummaryStatsForGender(gender);
                }

                if (hostelDashboardRequests[gender]) {
                    hostelDashboardRequests[gender].abort();
                    hostelDashboardRequests[gender] = null;
                }

                // Add a small loading spinner to the container
                container.html('<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading Dashboard...</div>');

                // Fetch dashboard HTML (includes hidden fields with stats)
                hostelDashboardRequests[gender] = $.ajax({
                    url: '/api/hostel-dashboard/get-stats',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}'
                    },
                    data: {
                        session_id: sessionId,
                        gender: gender,
                        context: 'applicant',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (html) {
                        container.html(html);
                    },
                    error: function (_xhr, status) {
                        if (status === 'abort') {
                            return;
                        }
                        container.html('<div class="alert alert-danger py-2">Failed to load dashboard.</div>');
                    },
                    complete: function () {
                        hostelDashboardRequests[gender] = null;
                    }
                });
            }

            /**
             * Refresh both male and female dashboard stats without switching the visible panel.
             * Keeps stats in sync after allow/reject while respecting the active Male/Female tab.
             */
            function refreshAllHostelDashboards() {
                loadHostelDashboard('male', false);
                loadHostelDashboard('female', false);
                updateSummaryStatsVisibility();
            }

            function updatePageContent(hostelType) {
                if (hostelType === 'regular_outside') {
                    // Update for External Hostel
                    $('#important-note .important-note-title').text('External Hostel Management:');
                    $('#important-note .important-note-description').html(`
                        This section manages students who have paid hostel fees but are staying in external hostels. 
                    `);

                    // Hide dashboard section for external hostel
                    $('#hostel-summary-stats-section').hide();
                    $('#hostel-workspace-sticky').show();
                } else {
                    // Update for University Hostel
                    $('#important-note .important-note-title').text('Important Note:');
                    $('#important-note .important-note-description').html(`
                        You cannot allow more applicants than available seats. If you need to allow more applicants, 
                        first vacate seats from applicants who haven't given consent yet (shown in "Can Vacate" count).
                    `);

                    // Show dashboard section for university hostel
                    $('#hostel-workspace-sticky').show();
                    $('#hostel-summary-stats-section').show();
                    updateSummaryStatsVisibility();
                }
            }

            function clearAllData() {
                if (hostelApplicantsRequest) {
                    hostelApplicantsRequest.abort();
                    hostelApplicantsRequest = null;
                }

                Object.keys(hostelDashboardRequests).forEach(function (gender) {
                    if (hostelDashboardRequests[gender]) {
                        hostelDashboardRequests[gender].abort();
                        hostelDashboardRequests[gender] = null;
                    }
                });

                hostelLoadToken++;

                // Reset all counts to 0
                $('#new_count').text('0');
                $('#maleCount').text('0');
                $('#femaleCount').text('0');

                // Clear dashboard containers
                $('#male-stats-dashboard-container').empty();
                $('#female-stats-dashboard-container').empty();
                $('#hostel-summary-stats-section').addClass('d-none');
                $('#hostel-workspace-sticky').addClass('d-none');
                $('#summary-stats-gender-label').text('');

                // Destroy DataTables if they exist
                destroyDataTables();

                // Clear all table bodies
                $('#male-applicants-tbody').empty();
                $('#female-applicants-tbody').empty();

                setHostelLoading(false);
                $('#content-section').show();

                // Show no-session message and hide data content
                $('#no-session-message').show();
                $('#data-content').hide();
                // clearManualSearchResult(true);

                // Hide the important note when data is cleared
                $('#important-note').hide();
            }

            function destroyDataTables() {
                ['#male_aplicants', '#female_aplicants'].forEach(function (selector) {
                    const $table = $(selector);
                    if (!$table.length) {
                        return;
                    }

                    if ($.fn.DataTable.isDataTable($table)) {
                        $table.DataTable().clear().destroy();
                    }

                    unwrapDataTable(selector);
                });

                $.fn.dataTable.ext.search = [];
                window.maleHostelTableFilter = null;
                window.femaleHostelTableFilter = null;
            }

            function refreshDataTables() {
                // Refresh male applicants DataTable
                if ($.fn.DataTable.isDataTable('#male_aplicants')) {
                    $('#male_aplicants').DataTable().ajax.reload(null, false);
                }

                // Refresh female applicants DataTable
                if ($.fn.DataTable.isDataTable('#female_aplicants')) {
                    $('#female_aplicants').DataTable().ajax.reload(null, false);
                }
            }

            function refreshSeatCounts(callback) {
                const sessionId = $('#session_id').val();
                const admissionScheduleId = $('#admission_schedule_id').val();
                const hostelType = $('#hostel_type').val();

                if (!sessionId) {
                    return;
                }

                // Make AJAX call to get updated counts
                $.ajax({
                    url: '/api/get-hostel-applicants-data',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({
                        session_id: sessionId,
                        admission_schedule_id: admissionScheduleId || ''
                    }),
                    success: function (response) {
                        // Update counts only (don't reload tables)
                        $('#new_count').text(response.new_count || 0);
                        $('#maleCount').text(response.maleCount || 0);
                        $('#femaleCount').text(response.femaleCount || 0);

                        // Store available seats data globally (session-wise, synced with summary dashboard)
                        window.maleAvailableSeats = parseInt(response.male_available_seats || response.male_vacant_seats || 0);
                        window.femaleAvailableSeats = parseInt(response.female_available_seats || response.female_vacant_seats || 0);
                        window.maleAllowedCount = parseInt(response.male_allowed_count || 0);
                        window.femaleAllowedCount = parseInt(response.female_allowed_count || 0);
                        window.maleRemainingSeats = parseInt(response.male_remaining_seats ?? Math.max(0, window.maleAvailableSeats - window.maleAllowedCount));
                        window.femaleRemainingSeats = parseInt(response.female_remaining_seats ?? Math.max(0, window.femaleAvailableSeats - window.femaleAllowedCount));

                        // Refresh both dashboards; keep the active tab's stats panel visible
                        refreshAllHostelDashboards();

                        // Call callback if provided
                        if (callback && typeof callback === 'function') {
                            callback();
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error refreshing seat counts:', error);
                    }
                });
            }

            function updateRemainingSeatsCount() {
                // This function is no longer needed since we show available seats directly
                // Keeping the function to avoid breaking any calls to it
                return;
            }

            function refreshAllAllowButtons() {
                // Keep all Allow buttons enabled in both male and female tables
                // Only ensure they have the correct styling and onclick handlers

                $('#male_aplicants tbody tr').each(function () {
                    const row = $(this);
                    const actionCell = row.find('td:eq(14)'); // Actions column
                    const allowButton = actionCell.find('button[onclick*="showAllowModal"]');

                    if (allowButton.length > 0) {
                        const aaiId = allowButton.attr('onclick').match(/'([^']+)'/)[1];

                        // Always keep Allow buttons enabled and styled properly
                        allowButton.removeClass('btn-outline-secondary').addClass('btn-outline-success')
                            .prop('disabled', false).removeAttr('title')
                            .attr('onclick', `showAllowModal('${aaiId}', 'new')`);
                    }
                });

                $('#female_aplicants tbody tr').each(function () {
                    const row = $(this);
                    const actionCell = row.find('td:eq(14)'); // Actions column
                    const allowButton = actionCell.find('button[onclick*="showAllowModal"]');

                    if (allowButton.length > 0) {
                        const aaiId = allowButton.attr('onclick').match(/'([^']+)'/)[1];

                        // Always keep Allow buttons enabled and styled properly
                        allowButton.removeClass('btn-outline-secondary').addClass('btn-outline-success')
                            .prop('disabled', false).removeAttr('title')
                            .attr('onclick', `showAllowModal('${aaiId}', 'new')`);
                    }
                });
            }

            function updateApplicantRow(aai_id, action, hostelLocation = null, newChallanStatus = null) {
                console.log('Updating row for aai_id:', aai_id, 'action:', action, 'location:', hostelLocation);

                // Find the row by looking for the checkbox with the specific aai_id
                const checkbox = $(`input[id="dummy_${aai_id}"]`);
                const targetRow = checkbox.closest('tr');

                console.log('Found row:', targetRow.length > 0);

                if (targetRow.length > 0) {
                    // Update row class for styling
                    if (action === 'allowed') {
                        targetRow.removeClass('allowed-row').addClass('allowed-row');
                    } else if (action === 'rejected') {
                        targetRow.removeClass('allowed-row');
                    }

                    // 1. Update Status and Global Counts FIRST (so button logic uses fresh counts)
                    // Update the allowed status column (8th column, index 7)
                    const statusCell = targetRow.find('td:eq(7)');

                    if (action === 'allowed') {
                        statusCell.html('<span class="badge badge-allowed"><i class="fa fa-check-circle"></i>Allowed</span>');

                        // Update global counts and validation variables
                        if (targetRow.closest('#male_aplicants').length > 0) {
                            window.maleAllowedCount = (window.maleAllowedCount || 0) + 1;
                            window.maleRemainingSeats = Math.max(0, window.maleAvailableSeats - window.maleAllowedCount);
                            $('#maleCount').text(window.maleAllowedCount);
                        } else if (targetRow.closest('#female_aplicants').length > 0) {
                            window.femaleAllowedCount = (window.femaleAllowedCount || 0) + 1;
                            window.femaleRemainingSeats = Math.max(0, window.femaleAvailableSeats - window.femaleAllowedCount);
                            $('#femaleCount').text(window.femaleAllowedCount);
                        }
                    } else if (action === 'rejected') {
                        statusCell.html('<span class="badge badge-danger"><i class="fa fa-times-circle"></i>Rejected</span>');

                        // Update global counts and validation variables
                        if (targetRow.closest('#male_aplicants').length > 0) {
                            window.maleAllowedCount = Math.max(0, (window.maleAllowedCount || 0) - 1);
                            window.maleRemainingSeats = Math.max(0, window.maleAvailableSeats - window.maleAllowedCount);
                            $('#maleCount').text(window.maleAllowedCount);
                        } else if (targetRow.closest('#female_aplicants').length > 0) {
                            window.femaleAllowedCount = Math.max(0, (window.femaleAllowedCount || 0) - 1);
                            window.femaleRemainingSeats = Math.max(0, window.femaleAvailableSeats - window.femaleAllowedCount);
                            $('#femaleCount').text(window.femaleAllowedCount);
                        }
                    }

                    // 2. Update Hostel Location Column (Index 8)
                    const locationCell = targetRow.find('td:eq(8)');
                    if (action === 'allowed' && hostelLocation) {
                        let badgeClass = 'badge-secondary';
                        let icon = 'fa-question-circle';
                        let text = 'N/A';

                        if (hostelLocation === 'on_campus') {
                            badgeClass = 'badge-primary';
                            icon = 'fa-building';
                            text = 'On Campus';
                        } else if (hostelLocation === 'off_campus') {
                            badgeClass = 'badge-info';
                            icon = 'fa-home';
                            text = 'Off Campus';
                        }
                        locationCell.html(`<span class="badge ${badgeClass}"><i class="fa ${icon} mr-1"></i>${text}</span>`);
                    } else if (action === 'rejected') {
                        locationCell.html('<span class="badge badge-secondary">N/A</span>');
                    }

                    // 2.5 Update Challan Status Column (Index 12)
                    if (newChallanStatus) {
                        const challanCell = targetRow.find('td:eq(12)');
                        let badgeClass = 'badge-secondary';

                        if (newChallanStatus === 'PAID') {
                            badgeClass = 'badge-success';
                        } else if (newChallanStatus === 'UNPAID') {
                            badgeClass = 'badge-warning';
                        } else if (newChallanStatus === 'N/A') {
                            badgeClass = 'badge-secondary';
                        }

                        challanCell.html(`<span class="badge ${badgeClass}">${newChallanStatus}</span>`);

                        // Update data attribute as well so button logic uses fresh status
                        targetRow.data('challan-status', newChallanStatus);
                    }

                    // 3. Update Action Button (Index 14) - Uses Updated Counts
                    const actionCell = targetRow.find('td:eq(14)');
                    let newActionButton = '';

                    // Check if challan is paid from the row's data attribute
                    const challanStatus = targetRow.data('challan-status');
                    const isPaid = challanStatus === 'PAID';

                    if (action === 'allowed') {
                        if (isPaid) {
                            newActionButton = `<span class="badge badge-seat-reserved"><i class="fa fa-bed mr-1"></i>Seat Reserved</span>`;
                        } else {
                            newActionButton = `<button type="button" class="btn btn-outline-danger btn-sm" onclick="showRejectModal('${aai_id}', 'new')">
                                <i class="fa fa-times-circle mr-1"></i>Reject
                               </button>`;
                        }
                    } else if (action === 'rejected') {
                        // Check if this is external hostel type
                        const hostelType = $('#hostel_type').val();
                        if (hostelType === 'regular_outside') {
                            newActionButton = `<button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="fa fa-check-circle mr-1"></i>Allow
                               </button>`;
                        } else {
                            // Check seat availability based on which table the row is in
                            let availableSeats, allowedCount;
                            if (targetRow.closest('#male_aplicants').length > 0) {
                                availableSeats = window.maleAvailableSeats || 0;
                                allowedCount = window.maleAllowedCount || 0;
                            } else if (targetRow.closest('#female_aplicants').length > 0) {
                                availableSeats = window.femaleAvailableSeats || 0;
                                allowedCount = window.femaleAllowedCount || 0;
                            }

                            const seatsAvailable = allowedCount < availableSeats;

                            if (seatsAvailable) {
                                newActionButton = `<button type="button" class="btn btn-outline-success btn-sm" onclick="showAllowModal('${aai_id}', 'new')">
                                    <i class="fa fa-check-circle mr-1"></i>Allow
                                   </button>`;
                            } else {
                                newActionButton = `<button type="button" class="btn btn-outline-secondary btn-sm" disabled title="No seats available">
                                    <i class="fa fa-check-circle mr-1"></i>Allow
                                   </button>`;
                            }
                        }
                    }

                    actionCell.html(newActionButton);

                    // Update the map display checkbox
                    if (action === 'allowed') {
                        checkbox.prop('disabled', true);
                    } else if (action === 'rejected') {
                        checkbox.prop('checked', false).prop('disabled', false);
                    }

                    console.log('Row updated successfully');
                } else {
                    console.log('Row not found for aai_id:', aai_id);
                }
            }


            function loadHostelData() {
                const sessionId = $('#session_id').val();
                const admissionScheduleId = $('#admission_schedule_id').val();
                const hostelType = $('#hostel_type').val();
                const loadToken = ++hostelLoadToken;

                // Validate required fields are selected
                if (!sessionId) {
                    showToast('error', 'Please select Academic Session first.');
                    return;
                }

                if (hostelApplicantsRequest) {
                    hostelApplicantsRequest.abort();
                    hostelApplicantsRequest = null;
                }

                Object.keys(hostelDashboardRequests).forEach(function (gender) {
                    if (hostelDashboardRequests[gender]) {
                        hostelDashboardRequests[gender].abort();
                        hostelDashboardRequests[gender] = null;
                    }
                });

                setHostelLoading(true);
                $('#content-section').show();
                $('#no-session-message').hide();
                $('#data-content').show();

                // Update page title and description based on hostel type
                updatePageContent(hostelType);

                hostelApplicantsRequest = $.ajax({
                    url: '/api/get-hostel-applicants-data',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({
                        session_id: sessionId,
                        admission_schedule_id: admissionScheduleId || '',
                        hostel_type: hostelType
                    }),
                    success: function (response) {
                        if (loadToken !== hostelLoadToken) {
                            return;
                        }

                        // Update counts
                        $('#new_count').text(response.new_count || 0);
                        $('#maleCount').text(response.maleCount || 0);
                        $('#femaleCount').text(response.femaleCount || 0);

                        // Store data globally for validation logic
                        window.maleAvailableSeats = parseInt(response.male_available_seats || 0);
                        window.femaleAvailableSeats = parseInt(response.female_available_seats || 0);
                        window.maleAllowedCount = parseInt(response.male_allowed_count || 0);
                        window.femaleAllowedCount = parseInt(response.female_allowed_count || 0);
                        window.maleRemainingSeats = parseInt(response.male_remaining_seats ?? Math.max(0, (window.maleAvailableSeats - window.maleAllowedCount)));
                        window.femaleRemainingSeats = parseInt(response.female_remaining_seats ?? Math.max(0, (window.femaleAvailableSeats - window.femaleAllowedCount)));

                        // Destroy existing DataTables before populating new data
                        destroyDataTables();

                        // Populate tables
                        populateMaleApplicants(response.maleApplicants || []);
                        populateFemaleApplicants(response.femaleApplicants || []);

                        // Hide no-session message and show data content
                        $('#no-session-message').hide();
                        $('#data-content').show();

                        // Show the important note when data is loaded
                        $('#important-note').show();
                        $('#hostel-workspace-sticky').removeClass('d-none');

                        // Load both dashboards; show stats for the active Male/Female tab
                        setTimeout(function () {
                            if (loadToken !== hostelLoadToken) {
                                return;
                            }

                            refreshAllHostelDashboards();
                        }, 150);
                    },
                    error: function (xhr, status) {
                        if (status === 'abort' || loadToken !== hostelLoadToken) {
                            return;
                        }

                        console.error('Error loading data:', xhr.responseText || status);
                        showToast('error', 'Error loading data. Please try again.');
                        clearAllData();
                    },
                    complete: function (_xhr, status) {
                        if (loadToken !== hostelLoadToken) {
                            return;
                        }

                        if (status !== 'abort') {
                            setHostelLoading(false);
                        }

                        hostelApplicantsRequest = null;
                    }
                });
            }



            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-GB'); // DD/MM/YYYY format
            }

            let addApplicantSearchData = null;

            function resetAddApplicantModal() {
                addApplicantSearchData = null;
                $('#add_applicant_search').val('');
                $('#add-applicant-result').removeClass('is-visible');
                $('#add-schedule-warning').removeClass('is-visible');
                $('#confirmGrantConsent').addClass('d-none').prop('disabled', false)
                    .html('<i class="fa fa-check mr-1"></i>Grant Hostel Consent');
                $('#btnSearchAddApplicant').prop('disabled', false)
                    .html('<i class="fa fa-search mr-1"></i>Search');
            }

            function isHostelConsentYes(value) {
                const normalized = String(value || '').trim().toUpperCase();
                return normalized === 'YES' || normalized === 'Y';
            }

            function searchApplicantForConsent() {
                const sessionId = $('#session_id').val();
                const search = $('#add_applicant_search').val().trim();

                if (!sessionId) {
                    showToast('error', 'Please select Academic Session first.');
                    return;
                }

                if (!search) {
                    showToast('error', 'Please enter Applicant ID or Enrollment No.');
                    return;
                }

                const $btn = $('#btnSearchAddApplicant');
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Searching...');
                $('#confirmGrantConsent').addClass('d-none');

                $.ajax({
                    url: '/api/search-hostel-applicant',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({
                        session_id: sessionId,
                        search: search
                    }),
                    success: function (response) {
                        if (response.success && response.applicant) {
                            renderAddApplicantResult(response.applicant);
                        } else {
                            resetAddApplicantModal();
                            showToast('error', response.message || 'Applicant not found.');
                        }
                    },
                    error: function (xhr) {
                        resetAddApplicantModal();
                        const message = (xhr.responseJSON && xhr.responseJSON.message)
                            ? xhr.responseJSON.message
                            : 'Error searching applicant. Please try again.';
                        showToast('error', message);
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html('<i class="fa fa-search mr-1"></i>Search');
                    }
                });
            }

            function renderAddApplicantResult(applicant) {
                addApplicantSearchData = applicant;
                const hasConsent = applicant.in_consent_list || isHostelConsentYes(applicant.api_ishostel);

                $('#add-result-aai-id').text(applicant.aai_id || 'N/A');
                $('#add-result-enrollment').text(applicant.cms_sai_enrollment_no || 'N/A');
                $('#add-result-name').text(applicant.api_name || 'N/A');
                $('#add-result-gender').text(applicant.api_gender || 'N/A');
                $('#add-result-city').text(applicant.api_permanentcity || 'N/A');
                $('#add-result-round').text(applicant.admission_sch_round || 'N/A');

                const $consentBadge = $('#add-result-consent');
                if (hasConsent) {
                    $consentBadge.removeClass('is-no').addClass('is-yes')
                        .html('<i class="fa fa-check-circle"></i> Yes');
                    $('#confirmGrantConsent').addClass('d-none');
                    $('#add-consent-info').html('<i class="fa fa-check-circle"></i><span>This applicant already has hostel consent and should appear in the list below.</span>');
                } else {
                    $consentBadge.removeClass('is-yes').addClass('is-no')
                        .html('<i class="fa fa-times-circle"></i> ' + (applicant.api_ishostel || 'No'));
                    $('#confirmGrantConsent').removeClass('d-none');
                    $('#add-consent-info').html('<i class="fa fa-info-circle"></i><span>Granting consent will set hostel preference to <strong>Yes</strong> and show this applicant in the consent list below.</span>');
                }

                const roundMap = {
                    'Early Round': '1',
                    'Regular Round': '2',
                    'Rolling Round': '3'
                };
                const selectedSchedule = $('#admission_schedule_id').val();
                const applicantRound = roundMap[applicant.admission_sch_round] || '';
                if (selectedSchedule && applicantRound && selectedSchedule !== applicantRound) {
                    const scheduleNames = { '1': 'Early Round', '2': 'Regular Round', '3': 'Rolling Round' };
                    $('#add-schedule-warning-text').text(
                        'Selected admission schedule is "' + (scheduleNames[selectedSchedule] || selectedSchedule) +
                        '" but applicant belongs to "' + (applicant.admission_sch_round || 'Unknown') +
                        '". They may not appear until you change the schedule filter.'
                    );
                    $('#add-schedule-warning').addClass('is-visible');
                } else {
                    $('#add-schedule-warning').removeClass('is-visible');
                }

                $('#add-applicant-result').addClass('is-visible');
            }

            function grantHostelConsent() {
                if (!addApplicantSearchData || !addApplicantSearchData.aai_id) {
                    showToast('error', 'Please search an applicant first.');
                    return;
                }

                const sessionId = $('#session_id').val();
                if (!sessionId) {
                    showToast('error', 'Please select Academic Session first.');
                    return;
                }

                const $btn = $('#confirmGrantConsent');
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Processing...');

                $.ajax({
                    url: '/api/grant-hostel-consent',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({
                        session_id: sessionId,
                        aai_id: addApplicantSearchData.aai_id
                    }),
                    success: function (response) {
                        if (response.success) {
                            showToast('success', response.message);
                            $('#addApplicantModal').modal('hide');
                            loadHostelData();

                            const gender = String(addApplicantSearchData.api_gender || '').toUpperCase().trim();
                            if (gender === 'MALE') {
                                $('#new-male-tab').tab('show');
                            } else if (gender === 'FEMALE') {
                                $('#new-female-tab').tab('show');
                            }
                        } else {
                            showToast('error', response.message || 'Failed to grant hostel consent.');
                        }
                    },
                    error: function (xhr) {
                        const message = (xhr.responseJSON && xhr.responseJSON.message)
                            ? xhr.responseJSON.message
                            : 'Error granting hostel consent. Please try again.';
                        showToast('error', message);
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i>Grant Hostel Consent');
                    }
                });
            }



            function updateSeatReservation(applicant_id) {
                var hiddenCheckbox = document.getElementById('seat_reservation_' + applicant_id);
                var visibleCheckbox = document.getElementById('dummy_' + applicant_id);

                if (hiddenCheckbox && visibleCheckbox) {
                    // Set the hidden checkbox value based on the visible checkbox state
                    hiddenCheckbox.checked = visibleCheckbox.checked;
                }
            }

            /* Manual Applicant Search - hidden
            function clearManualSearchResult(clearInput) {
                $('#manual-search-result').addClass('d-none');
                $('#manual-search-result-body').empty();
                if (clearInput) {
                    $('#manual_applicant_search').val('');
                }
            }

            function getApplicantGenderKey(apiGender) {
                return String(apiGender || '').toUpperCase().trim() === 'MALE' ? 'male' : 'female';
            }

            function searchManualApplicant() {
                const sessionId = $('#session_id').val();
                const search = $('#manual_applicant_search').val().trim();

                if (!sessionId) {
                    showToast('error', 'Please select Academic Session first.');
                    return;
                }

                if (!search) {
                    showToast('error', 'Please enter Applicant ID or Enrollment No.');
                    return;
                }

                const $btn = $('#btnManualSearch');
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Searching...');

                $.ajax({
                    url: '/api/search-hostel-applicant',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({
                        session_id: sessionId,
                        search: search
                    }),
                    success: function (response) {
                        if (response.success && response.applicant) {
                            renderManualSearchResult(response.applicant);
                            const gender = getApplicantGenderKey(response.applicant.api_gender);
                            loadHostelDashboard(gender);
                        } else {
                            clearManualSearchResult(false);
                            showToast('error', response.message || 'Applicant not found.');
                        }
                    },
                    error: function (xhr) {
                        clearManualSearchResult(false);
                        const message = (xhr.responseJSON && xhr.responseJSON.message)
                            ? xhr.responseJSON.message
                            : 'Error searching applicant. Please try again.';
                        showToast('error', message);
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html('<i class="fa fa-search mr-1"></i> Search');
                    }
                });
            }

            function buildManualChallanDisplay(record) {
                if (record.sc_challan_status === 'PAID') {
                    return '<span class="badge badge-paid">PAID</span>';
                }
                if (record.sc_challan_status === 'UNPAID') {
                    return '<span class="badge badge-unpaid">UNPAID</span>';
                }
                if (record.sc_challan_status) {
                    return record.sc_challan_status;
                }
                return 'N/A';
            }

            function renderManualSearchResult(applicant) {
                const genderKey = getApplicantGenderKey(applicant.api_gender);
                const isPaid = applicant.sc_challan_status === 'PAID';
                const genderIcon = genderKey === 'male' ? 'fa-male' : 'fa-female';

                const consentBadge = applicant.in_consent_list
                    ? '<span class="manual-badge manual-badge-consent"><i class="fa fa-check-circle"></i> In consent list</span>'
                    : '<span class="manual-badge manual-badge-warning"><i class="fa fa-exclamation-triangle"></i> No hostel consent b  manual assign</span>';

                let allowedBadge = '';
                if (applicant.allowed_status === 'Allowed') {
                    allowedBadge = '<span class="manual-badge manual-badge-allowed"><i class="fa fa-check"></i> Allowed</span>';
                } else if (applicant.allowed_status === 'Rejected') {
                    allowedBadge = '<span class="manual-badge manual-badge-danger"><i class="fa fa-times"></i> Rejected</span>';
                } else {
                    allowedBadge = '<span class="manual-badge manual-badge-secondary"><i class="fa fa-minus-circle"></i> Not Allowed</span>';
                }

                let challanDisplay = buildManualChallanDisplay(applicant);

                let actionHtml = '';
                if (applicant.allowed_status === 'Allowed') {
                    if (isPaid) {
                        actionHtml = '<span class="badge badge-seat-reserved"><i class="fa fa-bed mr-1"></i>Seat Reserved</span>';
                    } else {
                        actionHtml = `<button type="button" class="btn btn-outline-danger btn-sm" onclick="showRejectModal('${applicant.aai_id}', 'new')">
                                <i class="fa fa-times-circle mr-1"></i>Reject
                            </button>`;
                    }
                } else {
                    actionHtml = `<button type="button" class="btn btn-success btn-sm" onclick="showAllowModal('${applicant.aai_id}', 'new', '${genderKey}', '${applicant.sc_challan_status || ''}')">
                            <i class="fa fa-check-circle mr-1"></i>Allow
                        </button>`;
                }

                const bedInfo = (applicant.floor_name && applicant.room_no && applicant.bed_no)
                    ? `${applicant.floor_name}, Room ${applicant.room_no}, Bed ${applicant.bed_no}`
                    : 'N/A';

                const html = `
                        <div class="manual-result-top">
                            <div class="manual-result-avatar ${genderKey}">
                                <i class="fa ${genderIcon}"></i>
                            </div>
                            <div class="manual-result-identity">
                                <h5 class="manual-result-name">${applicant.api_name || 'N/A'}</h5>
                                <div class="manual-result-meta">
                                    <span><i class="fa fa-hashtag"></i>${applicant.aai_id || 'N/A'}</span>
                                    <span><i class="fa fa-graduation-cap"></i>${applicant.cms_sai_enrollment_no || 'N/A'}</span>
                                </div>
                                <div class="manual-result-badges">
                                    ${consentBadge}
                                    ${allowedBadge}
                                </div>
                            </div>
                            <div class="manual-result-action">
                                ${actionHtml}
                            </div>
                        </div>
                        <div class="manual-result-grid">
                            <div class="manual-stat-item">
                                <span class="manual-stat-label">Gender</span>
                                <span class="manual-stat-value">${applicant.api_gender || 'N/A'}</span>
                            </div>
                            <div class="manual-stat-item">
                                <span class="manual-stat-label">Admission Round</span>
                                <span class="manual-stat-value">${applicant.admission_sch_round || 'N/A'}</span>
                            </div>
                            <div class="manual-stat-item">
                                <span class="manual-stat-label">City</span>
                                <span class="manual-stat-value">${applicant.api_permanentcity || 'N/A'}</span>
                            </div>
                            <div class="manual-stat-item">
                                <span class="manual-stat-label">Hostel Consent</span>
                                <span class="manual-stat-value">${applicant.api_ishostel || 'N/A'}</span>
                            </div>
                            <div class="manual-stat-item">
                                <span class="manual-stat-label">Challan Status</span>
                                <span class="manual-stat-value">${challanDisplay}</span>
                            </div>
                            <div class="manual-stat-item">
                                <span class="manual-stat-label">Bed Assignment</span>
                                <span class="manual-stat-value">${bedInfo}</span>
                            </div>
                        </div>
                    `;

                $('#manual-search-result-body').html(html);
                $('#manual-search-result').removeClass('d-none');
            }
            */

            function showAllowModal(aai_id, type, forceGender, scChallanStatus) {
                // Step 1: Check if a session is selected
                const sessionId = $('#session_id').val();

                if (!sessionId) {
                    showToast('error', 'Please select Academic Session first.');
                    return;
                }

                // Validation: Ensure "Disable Map Display" is checked
                // const checkbox = document.getElementById('dummy_' + aai_id);
                // if (!checkbox || !checkbox.checked) {
                //     showToast('error', 'Please check the box in "Disable Map Display" column before allowing.');
                //     return;
                // }

                // Step 2: Determine gender (manual search passes applicant gender)
                let gender = forceGender || null;
                if (!gender) {
                    const isMaleTab = $('#new-male-tab').hasClass('active');
                    gender = isMaleTab ? 'male' : 'female';
                }

                // Step 2: If everything is okay, set data for the confirm button
                $('#confirmAllow').data('aai_id', aai_id);
                $('#confirmAllow').data('type', type);
                $('#confirmAllow').data('gender', gender);
                $('#confirmAllow').data('from_manual_search', !!forceGender);
                $('#confirmAllow').data('challan_status', scChallanStatus || '');

                // Step 6: Reset the hostel location radio buttons
                $('input[name="hostel_location"]').prop('checked', false);

                // Step 7: Open the allow confirmation modal
                const $confirmButton = $('#confirmAllow');
                $confirmButton.prop('disabled', false).html('<i class="fa fa-check mr-1"></i>Allow Applicant');
                $('#allowModal').modal('show');
            }

            function showRejectModal(aai_id, type) {
                // Reset button state before showing modal
                const $confirmButton = $('#confirmReject');
                $confirmButton.prop('disabled', false).html('<i class="fa fa-times mr-1"></i>Confirm Reject');

                $confirmButton.data('aai_id', aai_id);
                $confirmButton.data('type', type);
                $('#rejectModal').modal('show');
            }

            // Reset modal state when modal is hidden
            $('#rejectModal').on('hidden.bs.modal', function () {
                const $confirmButton = $('#confirmReject');
                $confirmButton.prop('disabled', false).html('<i class="fa fa-times mr-1"></i>Confirm Reject');
            });

            $('#allowModal').on('hidden.bs.modal', function () {
                const $confirmButton = $('#confirmAllow');
                $confirmButton.prop('disabled', false).html('<i class="fa fa-check mr-1"></i>Allow Applicant');
            });

            $(document).on('click', '#confirmAllow', function () {
                const $button = $(this);
                const aai_id = $button.data('aai_id');
                const type = $button.data('type');
                const sessionId = $('#session_id').val();
                const admissionScheduleId = $('#admission_schedule_id').val();
                const hostelType = $('#hostel_type').val();

                // Step 1: Get the selected hostel location (On Campus/Off Campus/Both)
                const hostelLocation = $('input[name="hostel_location"]:checked').val();

                // Step 2: Make sure a location choice was made
                if (!hostelLocation) {
                    showToast('error', 'Please select hostel location preference');
                    return;
                }

                // Step 2.5: Frontend validation - Check seat availability from hidden fields
                const gender = $button.data('gender') || ($('#new-male-tab').hasClass('active') ? 'male' : 'female');
                const locationKey = hostelLocation === 'on_campus' ? 'on_campus' : 'off_campus';
                const availableFieldId = `stats_${gender}_${locationKey}_available`;
                const allowedFieldId = `stats_${gender}_${locationKey}_allowed`;
                const $availableField = $('#' + availableFieldId);
                const $allowedField = $('#' + allowedFieldId);

                if ($availableField.length && $allowedField.length) {
                    const availableSeats = parseInt($availableField.val(), 10) || 0;
                    const allowedCount = parseInt($allowedField.val(), 10) || 0;

                    if (availableSeats > 0 && allowedCount >= availableSeats) {
                        const locationLabel = hostelLocation === 'on_campus' ? 'ON campus hostel' : 'Off campus safari hostel';
                        const genderLabel = gender === 'male' ? 'Male' : 'Female';
                        showToast('error', `Cannot allow more ${genderLabel} applicants for ${locationLabel}. Available: ${availableSeats}, Already Allowed: ${allowedCount}. Please select a different location or increase capacity.`);
                        return;
                    }
                }

                if (!aai_id) {
                    showToast('error', 'Applicant ID is missing. Please close the modal and try again.');
                    return;
                }

                if (!sessionId) {
                    showToast('error', 'Please select Academic Session first.');
                    return;
                }

                // Disable button and show processing status (after all validations pass)
                $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Processing...');

                // Step 5: Check the "Disable Map Display" checkbox state
                const checkbox = document.getElementById('dummy_' + aai_id);
                const fromManualSearch = $button.data('from_manual_search');
                const storedChallanStatus = String($button.data('challan_status') || '').toUpperCase();
                let seatReservationValue = checkbox && checkbox.checked ? '1' : '0';

                // Manual search: auto-generate challan + allocate (same as checkbox checked) if no challan yet
                if (fromManualSearch && storedChallanStatus !== 'PAID' && storedChallanStatus !== 'UNPAID') {
                    seatReservationValue = '1';
                }

                const autoGenerateChallan = seatReservationValue === '1';

                // Step 6: Determine the API endpoint based on applicant type
                let url = '';
                if (type === 'new') {
                    url = '/api/allow-new-applicant/' + aai_id;
                } else {
                    url = '/api/allow-old-applicant/' + aai_id;
                }

                // Step 7: Send the data to the server via AJAX
                $.ajax({
                    url: url,
                    method: 'POST',
                    timeout: 120000,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: JSON.stringify({
                        session_id: sessionId,
                        admission_schedule_id: admissionScheduleId,
                        seat_reservation_map_display: seatReservationValue,
                        hostel_location: hostelLocation,
                        auto_generate_challan: autoGenerateChallan
                    }),
                    success: function (response) {
                        try {
                            // Step 8: If success, update the table row and seat counts
                            if (response.success) {
                                // Check if auto-allocation was performed
                                if (response.challanAlreadyExists) {
                                    showToast('success', response.message || 'Applicant allowed b  existing hostel challan reused.');
                                } else if (response.autoAssigned && response.allocationDetails && response.challanDetails) {
                                    // Show detailed success message for auto-allocation
                                    const details = response.allocationDetails;
                                    const challan = response.challanDetails;
                                    const challanNo = challan.id || 'N/A';
                                    const amountText = (challan.amount != null && challan.amount !== '')
                                        ? String(challan.amount)
                                        : 'N/A';
                                    const message = `Applicant allowed and auto-assigned successfully!\n\n` +
                                        `p Location: ${details.location}\n` +
                                        `p" Hostel: ${details.hostel}\n` +
                                        `p* Room: ${details.room}, Bed: ${details.bed}\n\n` +
                                        `p0 Challan: ${challanNo}\n` +
                                        `Amount: Rs. ${amountText}\n` +
                                        `Due Date: ${challan.due_date || 'N/A'}`;
                                    showToast('success', message);
                                } else {
                                    // Show simple success message for manual process
                                    showToast('success', response.message);
                                }

                                const challanStatus = response.challanDetails
                                    ? (response.challanDetails.status || 'UNPAID')
                                    : null;
                                updateApplicantRow(aai_id, 'allowed', hostelLocation, challanStatus);
                                // if (fromManualSearch) {
                                //     searchManualApplicant();
                                //     $button.data('from_manual_search', false);
                                // } else {
                                //     updateApplicantRow(aai_id, 'allowed', hostelLocation, challanStatus);
                                // }

                                refreshSeatCounts(refreshAllAllowButtons);
                            } else {
                                // Step 9: Show error message as toast
                                showToast('error', response.message || 'Failed to allow applicant');
                            }
                        } catch (e) {
                            console.error("Error in allow success handler:", e);
                            showToast('error', 'Applicant allowed but UI update failed. Please refresh.');
                        }
                        // Step 10: Close the modal and reset button state
                        $('#allowModal').modal('hide');
                    },
                    error: function (xhr, status, error) {
                        console.error('Error allowing applicant:', status, error, xhr.responseText);
                        let errorMessage = 'Error allowing applicant. Please try again.';
                        if (status === 'timeout') {
                            errorMessage = 'Request timed out. Please try again or contact support.';
                        } else if (xhr.responseJSON) {
                            errorMessage = xhr.responseJSON.message || xhr.responseJSON.error || errorMessage;
                        }
                        showToast('error', errorMessage);
                        $('#allowModal').modal('hide');
                    },
                    complete: function () {
                        setTimeout(() => {
                            $button.prop('disabled', false).html('<i class="fa fa-check mr-1"></i>Allow Applicant');
                        }, 300);
                    }
                });
            });

            $(document).on('click', '#confirmReject', function () {
                const $button = $(this);
                const aai_id = $button.data('aai_id');
                const type = $button.data('type');
                const sessionId = $('#session_id').val();
                const admissionScheduleId = $('#admission_schedule_id').val();
                const hostelType = $('#hostel_type').val();

                // Disable button to prevent multiple clicks
                $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Processing...');

                // Validate session is selected
                if (!sessionId) {
                    showToast('error', 'Please select Academic Session first.');
                    // Re-enable button immediately
                    $button.prop('disabled', false).html('<i class="fa fa-times mr-1"></i>Confirm Reject');
                    $('#rejectModal').modal('hide');
                    return;
                }

                // Get checkbox value
                const checkbox = document.getElementById('dummy_' + aai_id);
                const seatReservationValue = checkbox && checkbox.checked ? '1' : '0';

                let url = '';
                if (type === 'new') {
                    url = '/api/reject-new-applicant/' + aai_id;
                } else {
                    url = '/api/reject-old-applicant/' + aai_id;
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({
                        session_id: sessionId,
                        admission_schedule_id: admissionScheduleId,
                        seat_reservation_map_display: seatReservationValue
                    }),
                    success: function (response) {
                        try {
                            if (response.success) {
                                showToast('success', response.message);
                                updateApplicantRow(aai_id, 'rejected', null, 'N/A');
                                // if (!$('#manual-search-result').hasClass('d-none')) {
                                //     searchManualApplicant();
                                // } else {
                                //     updateApplicantRow(aai_id, 'rejected', null, 'N/A');
                                // }
                                // Refresh the seat counts after rejecting, then refresh buttons
                                refreshSeatCounts(refreshAllAllowButtons);
                            } else {
                                showToast('error', response.message);
                            }
                        } catch (e) {
                            console.error("Error in reject success handler:", e);
                        }

                        // Re-enable button and reset text
                        $button.prop('disabled', false).html('<i class="fa fa-times mr-1"></i>Confirm Reject');
                        $('#rejectModal').modal('hide');
                    },
                    error: function (xhr, status, error) {
                        console.error('Error rejecting applicant:', error);
                        let errorMessage = 'Error rejecting applicant. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showToast('error', errorMessage);

                        // Re-enable button and reset text
                        $button.prop('disabled', false).html('<i class="fa fa-times mr-1"></i>Confirm Reject');
                        $('#rejectModal').modal('hide');
                    }
                });
            });



            // Old export function removed - now using DataTable's built-in export functionality
        </script>

@endsection

    <style>
        /* Loading animation */
        .loading {
            text-align: center;
            padding: 40px;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3em;
        }

        /* Important note styling - overridden by .hostel-modern-page */
        .hostel-modern-page .important-note-container {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border-radius: 10px;
            padding: 20px;
            color: inherit;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.08);
            margin-bottom: 20px;
        }

        .hostel-modern-page .important-note-title,
        .hostel-modern-page .important-note-description {
            color: #92400e;
        }

        /* Summary cards styling */
        .hostel-modern-page .card:hover {
            transform: none;
        }

        /* Table styling - scoped to non-hostel tables */
        .table:not(.hostel-applicants-table) th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
        }

        /* Nav tabs styling */
        .nav-tabs .nav-link {
            border: none;
            border-bottom: 2px solid transparent;
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            border-bottom-color: #007bff;
            background-color: transparent;
            color: #007bff;
        }

        .nav-tabs .nav-link:hover {
            border-bottom-color: #007bff;
            background-color: transparent;
        }

        /* Badge styling */
        .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }
    </style>