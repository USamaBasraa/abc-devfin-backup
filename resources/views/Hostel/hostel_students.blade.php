@extends('layouts.app')

@section('content')
<head>
    <title>Export to Excel</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="{{ asset('weekStudentsReport/jquery-3.5.1.min.js') }}"></script>

</head>

@include('components.toast-notifications')
    <style>
        .card {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 0.75rem;
            background: linear-gradient(to bottom, #ffffff, #f8f9fa);
        }

        .card-header {
            background: linear-gradient(135deg, #4454c3 , #007bff) !important;

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
            color: #4454c3 ;
            background-color: transparent;
            border-bottom: 2px solid #4454c3 ;
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
            color: #4454c3 ;
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
        
        .badge i {
            font-size: 10px !important;
            margin-right: 4px !important;
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

        .table {
            margin-bottom: 0;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .table thead th {
            border-top: none;
            background: transparent;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            color: #4454c3 ;
            padding: 1rem;
        }   

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(74, 144, 226, 0.05);
            transform: translateY(-1px);
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
            color: #4454c3 ;
            border-color: #4454c3 ;
        }

        .btn-outline-primary:hover {
            background-color: #4454c3 ;
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
            border-color: #4454c3 ;
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
            color: #4454c3 ;
            font-weight: 600;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        .container {
            max-width: 1400px;
        }

        .table tbody tr.allowed-row {
            background-color: rgba(40, 167, 69, 0.05);
        }
        
        .nav-tabs .nav-link.active {
            border-bottom: 2px solid #007bff;
        }
        
        /* Specific hover styles for nested tabs */
        #oldApplicantsTab .nav-link:hover {
            color: #4454c3 !important;
            background-color: rgba(74, 144, 226, 0.05);
            border-color: transparent;
        }
        
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
            justify-content: center;
        }

        #male_students td.text-center .form-check,
        #female_students td.text-center .form-check {
            margin: 0 auto;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 24px;
        }

        #male_students td.text-center .form-check-input,
        #female_students td.text-center .form-check-input {
            margin: 0;
            position: static;
            transform: scale(1.2);
        }

        #male_students th.text-center,
        #female_students th.text-center,
        #male_students td.text-center,
        #female_students td.text-center {
            vertical-align: middle;
            white-space: nowrap;
        }

        #male_students,
        #female_students {
            width: 100% !important;
            table-layout: auto;
        }

        .dataTables_wrapper .dataTables_scrollHead table,
        .dataTables_wrapper .dataTables_scrollBody table {
            width: 100% !important;
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
        
        /* Professional Filter Styling */
        .filter-section {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 25px;
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

        /* Manual Search Section */
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
            max-height: 55vh;
            overflow-y: auto;
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
        
        /* Consistent select height */
        select.form-control:not([size]):not([multiple]) {
            height: 3.375rem;
        }
        
        /* Hover effects */
        .filter-select:hover {
            border-color: #4454c3;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(68, 84, 195, 0.15);
        }
        
        /* DataTable Search and Length Styling */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }
        
        .dataTables_wrapper .dataTables_length select {
            border-radius: 0;
            border: 2px solid #e9ecef;
            padding: 0.375rem 0.75rem;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 0;
            border: 2px solid #e9ecef;
            padding: 0.375rem 0.75rem;
        }
        
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #4454c3;
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.15);
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
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
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
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        
        /* Available seats badge styling - Soft Info Theme */
        .available-seats-badge {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #1e40af;
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
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
        
        /* Additional dropdown styling for better browser compatibility */
        #session_id {
            font-size: 16px !important;
            font-weight: 500 !important;
        }
        
        #session_id option {
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
        
        /* Dropdown container styling */
        .form-group {
            margin-bottom: 24px !important;
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
    }
    
    /* Room Details Styling - compact so long hostel names don't stretch the row */
    .room-details {
        font-size: 12px;
        line-height: 1.35;
        min-width: 150px;
        max-width: 200px;
    }

    .room-details .room-hostel {
        display: block;
        font-weight: 700;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 2px;
    }

    .room-details .room-meta {
        display: block;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .room-details .room-meta i {
        margin-right: 3px;
    }

    /* Add New Student (applicants-style) */
    .hostel-students-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 0;
    }

    .hostel-students-toolbar .nav-tabs {
        margin-bottom: 0;
        flex: 1;
    }

    .hostel-btn-add-student {
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

    .hostel-btn-add-student:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4) !important;
        color: #fff !important;
    }

    .hostel-action-modal .modal-dialog {
        max-width: 480px;
    }

    .hostel-action-modal .modal-content {
        border: none;
        border-radius: 16px;
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

    .hostel-action-modal.add-student-modal .hostel-modal-header-inner {
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        border-bottom: 1px solid #c7d2fe;
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
        color: #fff;
    }

    .hostel-action-modal.add-student-modal .hostel-modal-icon {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
    }

    .hostel-action-modal .modal-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px;
    }

    .hostel-action-modal .hostel-modal-subtitle {
        font-size: 0.8125rem;
        color: #64748b;
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
        color: #64748b;
        opacity: 1;
        padding: 0;
        margin: 0;
        z-index: 2;
    }

    .hostel-action-modal .modal-body {
        padding: 1.25rem 1.5rem 1rem;
        background: #fff;
    }

    .hostel-action-modal .hostel-modal-section-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        margin-bottom: 10px;
    }

    .hostel-add-search-group .form-control {
        border-radius: 8px 0 0 8px;
        border: 1.5px solid #e2e8f0;
        min-height: 44px;
    }

    .hostel-add-search-group .btn {
        border-radius: 0 8px 8px 0;
        min-height: 44px;
        font-weight: 600;
        padding: 0 1.1rem;
    }

    .hostel-add-student-card {
        display: none;
        margin-top: 16px;
        padding: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .hostel-add-student-card.is-visible {
        display: block;
    }

    .hostel-add-student-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 16px;
        margin-bottom: 14px;
    }

    .hostel-add-student-field .label {
        display: block;
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-bottom: 3px;
    }

    .hostel-add-student-field .value {
        font-size: 0.875rem;
        font-weight: 600;
        color: #0f172a;
        word-break: break-word;
    }

    .hostel-list-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .hostel-list-status-badge.is-yes {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .hostel-list-status-badge.is-no {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
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

    .hostel-action-modal .modal-footer {
        border-top: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
        background: #f8fafc;
    }

    .hostel-action-modal .modal-footer .hostel-modal-btn-cancel {
        background: #ffffff !important;
        border: 1.5px solid #94a3b8 !important;
        color: #334155 !important;
        border-radius: 10px !important;
        font-weight: 600 !important;
    }

    .hostel-action-modal .modal-footer .btn-success {
        background: linear-gradient(135deg, #059669, #10b981);
        border: none;
        border-radius: 10px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    @media (max-width: 575px) {
        .hostel-add-student-grid {
            grid-template-columns: 1fr;
        }
    }

</style>

    <div class=" py-4">
        <div class="card shadow-sm">

                <div class="card-header bg-primary">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0 text-white"><i class="fa fa-building mr-2"></i>Hostel Students</h4>
                <span class="badge badge-light" style="background-color: #4454c3; color: white;"></span>
            </div>
        </div>

        <div class="card-body bg-light border-bottom">
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
                <!-- Add more filters here if needed in future (e.g. Hostel Type) -->
            </div>

        </div>

        <div class="card-body">
            <div id="loading-section" class="loading" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-3">Loading hostel students data...</p>
            </div>

            <div id="content-section">
                <div id="no-session-message" class="text-center py-5" style="display: none;">
                    <div class="mb-4">
                        <i class="fa fa-calendar-times fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Please Select Academic Session</h5>
                    <p class="text-muted">Choose an Academic Session from the dropdown above to view hostel students data.</p>
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
                                        You cannot allow more students than available seats. If you need to allow more students,
                                        first vacate seats from students who haven't given consent yet (shown in "Can Vacate" count).
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="hostel-workspace-sticky" class="hostel-workspace-sticky d-none">
                        <div id="hostel-summary-stats-section" class="hostel-summary-stats-section d-none">
                            <div class="summary-stats-header">
                                <h6 class="summary-stats-title">
                                    <i class="fa fa-bar-chart mr-2"></i>Summary Stats
                                </h6>
                                <span class="summary-stats-gender-badge" id="summary-stats-gender-label"></span>
                            </div>
                            <div id="male-stats-dashboard-container" class="hostel-stats-dashboard-panel"></div>
                            <div id="female-stats-dashboard-container" class="hostel-stats-dashboard-panel d-none"></div>
                        </div>

                        <div class="hostel-students-toolbar">
                            <ul class="nav nav-tabs" id="studentsTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="male-tab" data-toggle="tab" href="#male" role="tab">
                                        <i class="fa fa-male mr-1"></i>Male <span class="badge badge-primary" id="maleCount">0</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="female-tab" data-toggle="tab" href="#female" role="tab">
                                        <i class="fa fa-female mr-1"></i>Female <span class="badge badge-primary" id="femaleCount">0</span>
                                    </a>
                                </li>
                            </ul>
                            <button type="button" class="btn hostel-btn-add-student" id="btnAddNewStudent">
                                <i class="fa fa-user-plus mr-1"></i>Add New Student
                            </button>
                        </div>
                    </div>

                    <div class="tab-content mt-2" id="studentsTabContent">
                        <!-- Male Students -->
                        <div class="tab-pane fade show active" id="male" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0 text-primary"><i class="fa fa-male mr-2"></i>Male Students</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table" id="male_students">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Enrollment No</th>
                                            <th>Name</th>
                                            <th>City</th>
                                            <th>Permanent Address</th>
                                            <th>Apply Date</th>
                                            <th>Allowed Status</th>
                                            <th>Hostel Location</th>
                                            <th>Room Details</th>
                                            <th>Challan Status</th>
                                            <th class="text-center">Disable Map Display</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="male-students-tbody">
                                        <!-- Data will be loaded here via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Female Students -->
                        <div class="tab-pane fade" id="female" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0 text-primary"><i class="fa fa-female mr-2"></i>Female Students</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table" id="female_students">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Enrollment No</th>
                                            <th>Name</th>
                                            <th>City</th>
                                            <th>Permanent Address</th>
                                            <th>Apply Date</th>
                                            <th>Allowed Status</th>
                                            <th>Hostel Location</th>
                                            <th>Room Details</th>
                                            <th>Challan Status</th>
                                            <th class="text-center">Disable Map Display</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="female-students-tbody">
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

    <!-- Add New Student Modal -->
    <div class="modal fade hostel-action-modal add-student-modal" id="addStudentModal" tabindex="-1" role="dialog"
        aria-labelledby="addStudentModalLabel" aria-hidden="true">
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
                            <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
                            <p class="hostel-modal-subtitle">Search by Enrollment No. or Student ID to add to hostel list</p>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="hostel-modal-section-label">Enrollment No. / Student ID</div>
                    <div class="input-group hostel-add-search-group">
                        <input type="text" id="add_student_search" class="form-control"
                            placeholder="e.g. BNU-F2024-001 or 12345">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" id="btnSearchAddStudent">
                                <i class="fa fa-search mr-1"></i>Search
                            </button>
                        </div>
                    </div>

                    <div id="add-student-result" class="hostel-add-student-card">
                        <div class="hostel-add-student-grid">
                            <div class="hostel-add-student-field">
                                <span class="label">Student ID</span>
                                <span class="value" id="add-result-sai-id">—</span>
                            </div>
                            <div class="hostel-add-student-field">
                                <span class="label">Enrollment No</span>
                                <span class="value" id="add-result-enrollment">—</span>
                            </div>
                            <div class="hostel-add-student-field">
                                <span class="label">Name</span>
                                <span class="value" id="add-result-name">—</span>
                            </div>
                            <div class="hostel-add-student-field">
                                <span class="label">Gender</span>
                                <span class="value" id="add-result-gender">—</span>
                            </div>
                            <div class="hostel-add-student-field">
                                <span class="label">City</span>
                                <span class="value" id="add-result-city">—</span>
                            </div>
                            <div class="hostel-add-student-field">
                                <span class="label">List Status</span>
                                <span class="value" id="add-result-list-wrap">
                                    <span class="hostel-list-status-badge is-no" id="add-result-list-status">
                                        <i class="fa fa-times-circle"></i> Not in list
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="hostel-modal-info mb-0" id="add-student-info">
                            <i class="fa fa-info-circle"></i>
                            <span>Adding this student will create an entry in hostel applicants for the selected session and show them in the list below.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn hostel-modal-btn-cancel" data-dismiss="modal">
                        <i class="fa fa-times mr-1"></i>Close
                    </button>
                    <button type="button" class="btn btn-success d-none" id="confirmAddStudent">
                        <i class="fa fa-check mr-1"></i>Add to Hostel List
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Allow Confirmation Modal -->
    <div class="modal fade" id="allowModal" tabindex="-1" role="dialog" aria-labelledby="allowModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="allowModalLabel">
                        <i class="fa fa-check-circle mr-2"></i>Allow Student
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Please select hostel location preference for this student:</p>
                    <div class="form-group">
                        <label class="font-weight-bold">Hostel Location <span class="text-danger">*</span></label>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="locationOnCampus" name="hostel_location"
                                class="custom-control-input" value="on_campus">
                            <label class="custom-control-label" for="locationOnCampus">
                                <i class="fa fa-building mr-1 text-primary"></i>On Campus
                            </label>
                        </div>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="locationOffCampus" name="hostel_location"
                                class="custom-control-input" value="off_campus">
                            <label class="custom-control-label" for="locationOffCampus">
                                <i class="fa fa-home mr-1 text-info"></i>Off Campus
                            </label>
                        </div>
                        <!-- 'Both' option removed as requested -->
                    </div>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fa fa-info-circle mr-2"></i>
                        <small>This preference will help in hostel assignment.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times mr-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-success" id="confirmAllow">
                        <i class="fa fa-check mr-1"></i>Allow
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Confirmation Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Confirm Reject</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to reject this student?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmReject">Reject</button>
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
let hostelDashboardRequests = { male: null, female: null };
let hostelStudentsRequest = null;
let hostelStudentsLoadToken = 0;
let addStudentSearchData = null;

$(document).ready(function() {
    // Force larger font size for dropdown options
    function forceDropdownFontSize() {
        $('select.form-control-lg').css('font-size', '16px');
        $('select.form-control-lg option').css('font-size', '16px');
        $('#session_id').css('font-size', '16px');
        $('#session_id option').css('font-size', '16px');
    }
    
    // Apply font size immediately and on window load
    forceDropdownFontSize();
    $(window).on('load', forceDropdownFontSize);
    
    // Add change event listener for session dropdown
    $('#session_id').on('change', function() {
        const sessionId = $(this).val();
        
        // Clear dashboard containers immediately on change
        $('#male-stats-dashboard-container').empty();
        $('#female-stats-dashboard-container').empty();
        
        if (sessionId) {
            loadHostelData();
        } else {
            clearAllData();
        }
    });

    $('#btnAddNewStudent').on('click', function() {
        const sessionId = $('#session_id').val();
        if (!sessionId) {
            showToast('error', 'Please select Academic Session first.');
            return;
        }
        resetAddStudentModal();
        $('#addStudentModal').modal('show');
    });

    $('#btnSearchAddStudent').on('click', function() {
        searchStudentForAdd();
    });

    $('#add_student_search').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            searchStudentForAdd();
        }
    });

    $('#confirmAddStudent').on('click', function() {
        addHostelStudentToList();
    });

    $('#addStudentModal').on('hidden.bs.modal', function() {
        resetAddStudentModal();
    });

    // Tab switch listener to load dashboard for the active tab
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).attr("href"); // newly activated tab
        if (target === '#male') {
            loadHostelDashboard('male');
        } else if (target === '#female') {
            loadHostelDashboard('female');
        }

        // Recalc DataTable layout after hidden tab becomes visible
        setTimeout(function () {
            adjustHostelStudentsTables();
        }, 50);
    });
    
    // Don't load data by default - wait for session selection
    clearAllData();
});

function clearAllData() {
    Object.keys(hostelDashboardRequests).forEach(function(gender) {
        if (hostelDashboardRequests[gender]) {
            hostelDashboardRequests[gender].abort();
            hostelDashboardRequests[gender] = null;
        }
    });

    // Reset all counts to 0
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
    $('#male-students-tbody').empty();
    $('#female-students-tbody').empty();
    
    // Show content section and hide loading
    $('#loading-section').hide();
    $('#content-section').show();
    
    // Show no-session message and hide data content
    $('#no-session-message').show();
    $('#data-content').hide();
    
    // Hide the important note when data is cleared
    $('#important-note').hide();
}

function destroyDataTables() {
    // Destroy male students DataTable
    if ($.fn.DataTable.isDataTable('#male_students')) {
        $('#male_students').DataTable().destroy();
    }
    
    // Destroy female students DataTable
    if ($.fn.DataTable.isDataTable('#female_students')) {
        $('#female_students').DataTable().destroy();
    }
}

function refreshDataTables() {
    // Refresh male students DataTable
    if ($.fn.DataTable.isDataTable('#male_students')) {
        $('#male_students').DataTable().ajax.reload(null, false);
    }
    
    // Refresh female students DataTable
    if ($.fn.DataTable.isDataTable('#female_students')) {
        $('#female_students').DataTable().ajax.reload(null, false);
    }
}

function updateStudentRow(enrollment_no, action, hostelLocation = null, roomDetails = null) {
    console.log('Updating row for enrollment_no:', enrollment_no, 'action:', action, 'location:', hostelLocation, 'roomDetails:', roomDetails);
    
    // Try multiple ways to find the row
    let targetRow = null;
    let checkbox = null;
    
    // Method 1: Find by checkbox ID
    checkbox = $(`input[id="dummy_${enrollment_no}"]`);
    if (checkbox.length > 0) {
        targetRow = checkbox.closest('tr');
    }
    
    // Method 2: If not found, search in both tables
    if (!targetRow || targetRow.length === 0) {
        const maleTable = $('#male_students').DataTable();
        const femaleTable = $('#female_students').DataTable();
        
        // Search in male table
        const maleRow = maleTable.row(`td:contains("${enrollment_no}")`).node();
        if (maleRow) {
            targetRow = $(maleRow);
            checkbox = targetRow.find(`input[id="dummy_${enrollment_no}"]`);
        } else {
            // Search in female table
            const femaleRow = femaleTable.row(`td:contains("${enrollment_no}")`).node();
            if (femaleRow) {
                targetRow = $(femaleRow);
                checkbox = targetRow.find(`input[id="dummy_${enrollment_no}"]`);
            }
        }
    }
    
    console.log('Found row:', targetRow && targetRow.length > 0);
    
    if (targetRow && targetRow.length > 0) {
        // Update row class for styling
        if (action === 'allowed') {
            targetRow.removeClass('allowed-row').addClass('allowed-row');
        } else if (action === 'rejected') {
            targetRow.removeClass('allowed-row');
        }
        
        // Update the action button (11th column, index 10 - Actions column)
        const actionCell = targetRow.find('td:eq(10)');
        let newActionButton = '';
        
        // Check if challan is paid from the row's data attribute
        const challanStatus = targetRow.data('challan-status');
        const isPaid = challanStatus === 'PAID';
        
        if (isPaid) {
            // If challan is paid, show Seat Reserved regardless of action
            newActionButton = `<span class="badge badge-seat-reserved"><i class="fa fa-bed mr-1"></i>Seat Reserved</span>`;
        } else if (action === 'allowed') {
            // If allowed but not paid, show Reject button
            newActionButton = `<button type="button" class="btn btn-outline-danger btn-sm" onclick="showRejectModal('${enrollment_no}', 'old')">
                <i class="fa fa-times-circle mr-1"></i>Reject
               </button>`;
        } else if (action === 'rejected') {
            // If rejected and not paid, show Allow button
            newActionButton = `<button type="button" class="btn btn-outline-success btn-sm" onclick="showAllowModal('${enrollment_no}', 'old')">
                <i class="fa fa-check-circle mr-1"></i>Allow
               </button>`;
        }
        
        actionCell.html(newActionButton);
        
        // Update the Allowed Status column (6th column, index 5)
        const allowedStatusCell = targetRow.find('td:eq(5)');
        if (action === 'allowed') {
            allowedStatusCell.html(`<span class="badge badge-allowed">
                <i class="fa fa-check-circle"></i>
                Allowed
            </span>`);
        } else if (action === 'rejected') {
            allowedStatusCell.html(`<span class="badge badge-secondary">
                <i class="fa fa-times-circle"></i>
                Rejected
            </span>`);
        }

        // Update the hostel location column (7th column, index 6)
        const locationCell = targetRow.find('td:eq(6)');
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
            } else if (hostelLocation === 'both') {
                badgeClass = 'badge-success';
                icon = 'fa-th-large';
                text = 'Both';
            }
            
            locationCell.html(`<span class="badge ${badgeClass}"><i class="fa ${icon} mr-1"></i>${text}</span>`);
        } else if (action === 'rejected') {
            locationCell.html('<span class="badge badge-secondary">N/A</span>');
        }
        
        // Update Room Details Column (8th column, index 7)
        const roomDetailsCell = targetRow.find('td:eq(7)');
        if (action === 'allowed' && roomDetails) {
            roomDetailsCell.html(buildRoomDetailsHtml({
                hostel_name: roomDetails.hostel,
                floor_name: roomDetails.floor,
                room_no: roomDetails.room,
                bed_no: roomDetails.bed
            }));
        } else if (action === 'rejected') {
            roomDetailsCell.html('<span class="badge badge-secondary">Not Assigned</span>');
        }

        // Update the map display checkbox
        if (checkbox.length > 0) {
            if (action === 'allowed') {
                // If allowed, checkbox should be disabled but keep its original state
                checkbox.prop('disabled', true);
            } else if (action === 'rejected') {
                // If rejected, checkbox should be unchecked and enabled
                checkbox.prop('checked', false).prop('disabled', false);
            }
        }
        
        console.log('Row updated successfully');
    } else {
        console.log('Row not found for enrollment_no:', enrollment_no);
        // If row not found, reload the data to ensure consistency
        loadHostelData();
        return;
    }

    // Update the Challan Status column (9th column, index 8)
    const challanStatusCell = targetRow.find('td:eq(8)');
    if (action === 'rejected') {
        challanStatusCell.html(`<span class="badge badge-secondary">
            <i class="fa fa-ban mr-1"></i>Cancelled
        </span>`);
        // Update the data attribute SO subsequent actions read the correct status
        targetRow.data('challan-status', 'CANCELLED');
    } else if (action === 'allowed') {
         challanStatusCell.html(`<span class="badge badge-unpaid">
            <i class="fa fa-exclamation-triangle mr-1"></i>UNPAID
        </span>`);
        targetRow.data('challan-status', 'UNPAID');
    }
}

function adjustHostelStudentsTables() {
    ['#male_students', '#female_students'].forEach(function(selector) {
        if ($.fn.DataTable.isDataTable(selector)) {
            const table = $(selector).DataTable();
            table.columns.adjust();
            if (table.responsive && typeof table.responsive.recalc === 'function') {
                table.responsive.recalc();
            }
        }
    });
}

function scrollHostelTablesIntoView() {
    const $target = $('#hostel-workspace-sticky');
    if (!$target.length || !$target.is(':visible')) {
        return;
    }

    const top = Math.max(0, ($target.offset().top || 0) - 24);
    $('html, body').stop(true).animate({ scrollTop: top }, 280);
}

function loadHostelData() {
    const sessionId = $('#session_id').val();
    const loadToken = ++hostelStudentsLoadToken;

    if (!sessionId) {
        clearAllData();
        return;
    }

    if (hostelStudentsRequest) {
        hostelStudentsRequest.abort();
        hostelStudentsRequest = null;
    }

    Object.keys(hostelDashboardRequests).forEach(function(gender) {
        if (hostelDashboardRequests[gender]) {
            hostelDashboardRequests[gender].abort();
            hostelDashboardRequests[gender] = null;
        }
    });

    // Keep content visible so DataTables init with correct dimensions.
    $('#loading-section').show();
    $('#content-section').show();
    $('#no-session-message').hide();
    $('#data-content').show();
    $('#important-note').show();
    $('#hostel-workspace-sticky').removeClass('d-none');

    hostelStudentsRequest = $.ajax({
        url: '/api/get-hostel-students-data',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            session_id: sessionId || ''
        }),
        success: function(response) {
            if (loadToken !== hostelStudentsLoadToken) {
                return;
            }

            // Update counts
            $('#maleCount').text(response.maleCount || 0);
            $('#femaleCount').text(response.femaleCount || 0);

            window.maleAvailableSeats = parseInt(response.male_available_seats || response.male_vacant_seats || 0);
            window.femaleAvailableSeats = parseInt(response.female_available_seats || response.female_vacant_seats || 0);
            window.maleAllowedCount = parseInt(response.male_allowed_count || 0);
            window.femaleAllowedCount = parseInt(response.female_allowed_count || 0);
            window.maleRemainingSeats = parseInt(response.male_remaining_seats ?? Math.max(0, window.maleAvailableSeats - window.maleAllowedCount));
            window.femaleRemainingSeats = parseInt(response.female_remaining_seats ?? Math.max(0, window.femaleAvailableSeats - window.femaleAllowedCount));

            // Destroy existing DataTables before populating new data
            destroyDataTables();

            $('#loading-section').hide();
            $('#content-section').show();
            $('#no-session-message').hide();
            $('#data-content').show();
            $('#important-note').show();
            $('#hostel-workspace-sticky').removeClass('d-none');

            // Populate tables while content is visible
            populateMaleStudents(response.maleStudents || []);
            populateFemaleStudents(response.femaleStudents || []);

            setTimeout(function() {
                if (loadToken !== hostelStudentsLoadToken) {
                    return;
                }

                adjustHostelStudentsTables();
                scrollHostelTablesIntoView();

                const activeTab = $('#studentsTab .nav-link.active').attr('href');
                if (activeTab === '#male') {
                    loadHostelDashboard('male');
                } else if (activeTab === '#female') {
                    loadHostelDashboard('female');
                }
            }, 150);
        },
        error: function(xhr, status, error) {
            if (status === 'abort' || loadToken !== hostelStudentsLoadToken) {
                return;
            }

            console.error('Error loading data:', error);
            showToast('error', 'Error loading data. Please try again.');
            $('#loading-section').hide();
            $('#content-section').show();
            $('#data-content').show();
            $('#hostel-workspace-sticky').removeClass('d-none');
        },
        complete: function() {
            if (loadToken === hostelStudentsLoadToken) {
                hostelStudentsRequest = null;
            }
        }
    });
}

function buildRoomDetailsHtml(student) {
    if (!student || !student.room_no || !student.bed_no) {
        return '<span class="badge badge-secondary">Not Assigned</span>';
    }

    const hostel = student.hostel_name || 'N/A';
    const floor = student.floor_name || 'N/A';
    const room = student.room_no;
    const bed = student.bed_no;
    const fullTitle = `${hostel} | ${floor} | Room ${room} | Bed ${bed}`;

    return `<div class="room-details" title="${fullTitle.replace(/"/g, '&quot;')}">
        <span class="room-hostel"><i class="fa fa-building text-primary"></i> ${hostel}</span>
        <span class="room-meta"><i class="fa fa-layer-group text-info"></i>${floor} · R${room} · B${bed}</span>
    </div>`;
}

function populateMaleStudents(students) {
    const tbody = $('#male-students-tbody');
    tbody.empty();
    
    // Convert object to array if needed
    const studentsArray = Array.isArray(students) ? students : Object.values(students);
    
    studentsArray.forEach(function(student) {
        const rowClass = student.happ_allowed_status == 'Allowed' ? 'allowed-row' : '';
        const applyDate = student.apply_date ? formatDate(student.apply_date) : 'N/A';
        
        // Handle checkbox state based on server data and challan status
        let checkboxAttributes = '';
        if (student.happ_allowed_status == 'Allowed') {
            // If allowed, checkbox should be disabled
            checkboxAttributes = student.ha_disable_map_display == 'Yes' ? 'checked disabled' : 'disabled';
        } else {
            // If not allowed, checkbox should be enabled
            checkboxAttributes = student.ha_disable_map_display == 'Yes' ? 'checked' : '';
        }
        
        // Check if challan is paid
        const isPaid = student.sc_challan_status === 'PAID';
        
        // If challan is paid, disable map display
        if (isPaid) {
            checkboxAttributes = 'checked disabled';
        }
        
        let actionButton = '';
        if (isPaid) {
            // If challan is paid, show Seat Reserved regardless of allowed status
            actionButton = `<span class="badge badge-seat-reserved"><i class="fa fa-bed mr-1"></i>Seat Reserved</span>`;
        } else if (student.happ_allowed_status == 'Allowed') {
            // If allowed but not paid, show Reject button
            actionButton = `<button type="button" class="btn btn-outline-danger btn-sm" onclick="showRejectModal('${student.happ_enrollment_no}', 'old')">
                <i class="fa fa-times-circle mr-1"></i>Reject
               </button>`;
        } else {
            // If not allowed and not paid, show Allow button
            actionButton = `<button type="button" class="btn btn-outline-success btn-sm" onclick="showAllowModal('${student.happ_enrollment_no}', 'old')">
                <i class="fa fa-check-circle mr-1"></i>Allow
               </button>`;
        }
        
        const row = `
            <tr class="${rowClass}" data-challan-status="${student.sc_challan_status || ''}">
                <td>${(student.happ_enrollment_no || 'N/A').toUpperCase()}</td>
                <td>${student.student_name || 'N/A'}</td>
                <td>${student.permanent_city || 'N/A'}</td>
                <td>${student.permanent_address || 'N/A'}</td>
                <td>${applyDate}</td>
                <td>
                    <span class="badge ${student.happ_allowed_status === 'Allowed' ? 'badge-allowed' : 'badge-secondary'}">
                        <i class="fa ${student.happ_allowed_status === 'Allowed' ? 'fa-check-circle' : 'fa-times-circle'}"></i>
                        ${student.happ_allowed_status || 'N/A'}
                    </span>
                </td>
                <td>
                    ${student.hostel_location_preference === 'off_campus' ? 
                        '<span class="badge badge-info"><i class="fa fa-home mr-1"></i>Off Campus</span>' : 
                        student.hostel_location_preference === 'both' ?
                        '<span class="badge badge-success"><i class="fa fa-th-large mr-1"></i>Both</span>' :
                        student.hostel_location_preference === 'on_campus' ?
                        '<span class="badge badge-primary"><i class="fa fa-building mr-1"></i>On Campus</span>' :
                        '<span class="badge badge-secondary">N/A</span>'}
                </td>
                <td>
                    ${buildRoomDetailsHtml(student)}
                </td>
                <td>
                    <span class="badge ${student.sc_challan_status === 'PAID' ? 'badge-paid' : student.sc_challan_status === 'UNPAID' ? 'badge-unpaid' : 'badge-secondary'}">
                        <i class="fa ${student.sc_challan_status === 'PAID' ? 'fa-credit-card' : student.sc_challan_status === 'UNPAID' ? 'fa-exclamation-triangle' : 'fa-question-circle'}"></i>
                        ${student.sc_challan_status || 'N/A'}
                    </span>
                </td>
                <td class="text-center">
                    <div class="form-check d-inline-flex justify-content-center align-items-center">
                        <input class="form-check-input" type="checkbox" name="seat_reservation_map_display" id="dummy_${student.happ_enrollment_no}" ${checkboxAttributes}>
                    </div>
                </td>
                <td class="text-center text-nowrap">
                    ${actionButton}
                </td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Initialize DataTable for male students
    if ($.fn.DataTable.isDataTable('#male_students')) {
        $('#male_students').DataTable().destroy();
    }
    
    $('#male_students').DataTable({
        scrollX: true,
        autoWidth: false,
        scrollCollapse: true,
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"i>>' +
             '<"row"<"col-sm-12"<"custom-filters">>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        columnDefs: [
            { className: 'text-nowrap', targets: [0, 4, 5, 6, 8] },
            { className: 'text-center', targets: [9, 10] },
            { width: '110px', targets: 0 },
            { width: '130px', targets: 1 },
            { width: '90px', targets: 2 },
            { width: '160px', targets: 3 },
            { width: '95px', targets: 4 },
            { width: '110px', targets: 5 },
            { width: '110px', targets: 6 },
            { width: '170px', targets: 7 },
            { width: '100px', targets: 8 },
            { width: '90px', targets: 9 },
            { width: '110px', targets: 10 }
        ],
        drawCallback: function () {
            this.api().columns.adjust();
        },
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Male Hostel Students',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'Male Hostel Students',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            },
            {
                extend: 'print',
                title: 'Male Hostel Students',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            }
        ],
        pageLength: -1,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        responsive: false,
        language: {
            search: "Search students:",
            lengthMenu: "Show _MENU_ students per page",
            info: "Showing _START_ to _END_ of _TOTAL_ students",
            infoEmpty: "Showing 0 to 0 of 0 students",
            infoFiltered: "(filtered from _MAX_ total students)"
        },
        initComplete: function () {
            // Add custom filters
            var api = this.api();
            api.columns.adjust();
            setTimeout(function () {
                api.columns.adjust();
            }, 150);
            // Create professional filter container
            var filterContainer = $('<div class="filter-section mb-4">' +
                '<div class="filter-header">' +
                '<h6 class="filter-title"><i class="fa fa-filter mr-2"></i>Advanced Filters</h6>' +
                '<div class="filter-subtitle">Filter students by payment status</div>' +
                '</div>' +
                '<div class="filter-content">' +
                '<div class="row">' +
                '<div class="col-md-6 col-lg-4">' +
                '<div class="filter-group">' +
                '<label class="filter-label"><i class="fa fa-credit-card mr-2"></i>Payment Status</label>' +
                '<div class="select-wrapper">' +
                '<select class="form-control filter-select" id="male-challan-filter">' +
                '<option value="" class="filter-option">📋 Show All Records</option>' +
                '<option value="PAID" class="filter-option">✅ Paid Students</option>' +
                '<option value="UNPAID" class="filter-option">❌ Unpaid Students</option>' +
                '</select>' +
                '<i class="fa fa-chevron-down select-arrow"></i>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>');
            
            // Insert filters after the table header
            $(api.table().container()).find('.custom-filters').html(filterContainer);
            
            // Method 1: Using column().search() with custom search function
            var challanFilter = $('#male-challan-filter');
            
            // Challan Status Filter  
            challanFilter.on('change', function() {
                var filterValue = $(this).val();
                
                // Clear any existing custom filters first
                $.fn.dataTable.ext.search.splice(0, $.fn.dataTable.ext.search.length);
                
                if (filterValue !== '') {
                    // Add custom filter for challan status
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        if (settings.nTable.id === 'male_students') {
                            var challanStatus = data[8]; // Index 8: Challan Status
                            var challanText = $('<div>').html(challanStatus).text().trim();
                            
                            if (filterValue === 'UNPAID') {
                                // For UNPAID: show UNPAID status OR (Allowed + null challan status)
                                var allowedStatus = data[5]; // Index 5: Allowed Status
                                var allowedText = $('<div>').html(allowedStatus).text().trim();
                                
                                return challanText === 'UNPAID' || 
                                       (allowedText === 'Allowed' && (challanText === 'N/A' || challanText === ''));
                            } else {
                                // For PAID: show only exact PAID status
                                return challanText === filterValue;
                            }
                        }
                        return true;
                    });
                }
                
                api.draw();
            });
        }
    });
}

function populateFemaleStudents(students) {
    const tbody = $('#female-students-tbody');
    tbody.empty();
    
    // Convert object to array if needed
    const studentsArray = Array.isArray(students) ? students : Object.values(students);
    
    studentsArray.forEach(function(student) {
        const rowClass = student.happ_allowed_status == 'Allowed' ? 'allowed-row' : '';
        const applyDate = student.apply_date ? formatDate(student.apply_date) : 'N/A';
        
        // Handle checkbox state based on server data and challan status
        let checkboxAttributes = '';
        if (student.happ_allowed_status == 'Allowed') {
            // If allowed, checkbox should be disabled
            checkboxAttributes = student.ha_disable_map_display == 'Yes' ? 'checked disabled' : 'disabled';
        } else {
            // If not allowed, checkbox should be enabled
            checkboxAttributes = student.ha_disable_map_display == 'Yes' ? 'checked' : '';
        }
        
        // Check if challan is paid
        const isPaid = student.sc_challan_status === 'PAID';
        
        // If challan is paid, disable map display
        if (isPaid) {
            checkboxAttributes = 'checked disabled';
        }
        
        let actionButton = '';
        if (isPaid) {
            // If challan is paid, show Seat Reserved regardless of allowed status
            actionButton = `<span class="badge badge-seat-reserved"><i class="fa fa-bed mr-1"></i>Seat Reserved</span>`;
        } else if (student.happ_allowed_status == 'Allowed') {
            // If allowed but not paid, show Reject button
            actionButton = `<button type="button" class="btn btn-outline-danger btn-sm" onclick="showRejectModal('${student.happ_enrollment_no}', 'old')">
                <i class="fa fa-times-circle mr-1"></i>Reject
               </button>`;
        } else {
            // If not allowed and not paid, show Allow button
            actionButton = `<button type="button" class="btn btn-outline-success btn-sm" onclick="showAllowModal('${student.happ_enrollment_no}', 'old')">
                <i class="fa fa-check-circle mr-1"></i>Allow
               </button>`;
        }
        
        const row = `
            <tr class="${rowClass}" data-challan-status="${student.sc_challan_status || ''}">
                <td>${(student.happ_enrollment_no || 'N/A').toUpperCase()}</td>
                <td>${student.student_name || 'N/A'}</td>
                <td>${student.permanent_city || 'N/A'}</td>
                <td>${student.permanent_address || 'N/A'}</td>
                <td>${applyDate}</td>
                <td>
                    <span class="badge ${student.happ_allowed_status === 'Allowed' ? 'badge-allowed' : 'badge-secondary'}">
                        <i class="fa ${student.happ_allowed_status === 'Allowed' ? 'fa-check-circle' : 'fa-times-circle'}"></i>
                        ${student.happ_allowed_status || 'N/A'}
                    </span>
                </td>
                <td>
                    ${student.hostel_location_preference === 'off_campus' ? 
                        '<span class="badge badge-info"><i class="fa fa-home mr-1"></i>Off Campus</span>' : 
                        student.hostel_location_preference === 'both' ?
                        '<span class="badge badge-success"><i class="fa fa-th-large mr-1"></i>Both</span>' :
                        student.hostel_location_preference === 'on_campus' ?
                        '<span class="badge badge-primary"><i class="fa fa-building mr-1"></i>On Campus</span>' :
                        '<span class="badge badge-secondary">N/A</span>'}
                </td>
                <td>
                    ${buildRoomDetailsHtml(student)}
                </td>
                <td>
                    <span class="badge ${student.sc_challan_status === 'PAID' ? 'badge-paid' : student.sc_challan_status === 'UNPAID' ? 'badge-unpaid' : 'badge-secondary'}">
                        <i class="fa ${student.sc_challan_status === 'PAID' ? 'fa-credit-card' : student.sc_challan_status === 'UNPAID' ? 'fa-exclamation-triangle' : 'fa-question-circle'}"></i>
                        ${student.sc_challan_status || 'N/A'}
                    </span>
                </td>
                <td class="text-center">
                    <div class="form-check d-inline-flex justify-content-center align-items-center">
                        <input class="form-check-input" type="checkbox" name="seat_reservation_map_display" id="dummy_${student.happ_enrollment_no}" ${checkboxAttributes}>
                    </div>
                </td>
                <td class="text-center text-nowrap">
                    ${actionButton}
                </td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Initialize DataTable for female students
    if ($.fn.DataTable.isDataTable('#female_students')) {
        $('#female_students').DataTable().destroy();
    }
    
    $('#female_students').DataTable({
        scrollX: true,
        autoWidth: false,
        scrollCollapse: true,
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"i>>' +
             '<"row"<"col-sm-12"<"custom-filters">>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        columnDefs: [
            { className: 'text-nowrap', targets: [0, 4, 5, 6, 8] },
            { className: 'text-center', targets: [9, 10] },
            { width: '110px', targets: 0 },
            { width: '130px', targets: 1 },
            { width: '90px', targets: 2 },
            { width: '160px', targets: 3 },
            { width: '95px', targets: 4 },
            { width: '110px', targets: 5 },
            { width: '110px', targets: 6 },
            { width: '170px', targets: 7 },
            { width: '100px', targets: 8 },
            { width: '90px', targets: 9 },
            { width: '110px', targets: 10 }
        ],
        drawCallback: function () {
            this.api().columns.adjust();
        },
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Female Hostel Students',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'Female Hostel Students',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            },
            {
                extend: 'print',
                title: 'Female Hostel Students',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            }
        ],
        pageLength: -1,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        responsive: false,
        language: {
            search: "Search students:",
            lengthMenu: "Show _MENU_ students per page",
            info: "Showing _START_ to _END_ of _TOTAL_ students",
            infoEmpty: "Showing 0 to 0 of 0 students",
            infoFiltered: "(filtered from _MAX_ total students)"
        },
        initComplete: function () {
            // Add custom filters
            var api = this.api();
            api.columns.adjust();
            setTimeout(function () {
                api.columns.adjust();
            }, 150);
            // Create professional filter container
            var filterContainer = $('<div class="filter-section mb-4">' +
                '<div class="filter-header">' +
                '<h6 class="filter-title"><i class="fa fa-filter mr-2"></i>Advanced Filters</h6>' +
                '<div class="filter-subtitle">Filter students by payment status</div>' +
                '</div>' +
                '<div class="filter-content">' +
                '<div class="row">' +
                '<div class="col-md-6 col-lg-4">' +
                '<div class="filter-group">' +
                '<label class="filter-label"><i class="fa fa-credit-card mr-2"></i>Payment Status</label>' +
                '<div class="select-wrapper">' +
                '<select class="form-control filter-select" id="female-challan-filter">' +
                '<option value="" class="filter-option">📋 Show All Records</option>' +
                '<option value="PAID" class="filter-option">✅ Paid Students</option>' +
                '<option value="UNPAID" class="filter-option">❌ Unpaid Students</option>' +
                '</select>' +
                '<i class="fa fa-chevron-down select-arrow"></i>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>');
            
            // Insert filters after the table header
            $(api.table().container()).find('.custom-filters').html(filterContainer);
            
            // Method 1: Using column().search() with custom search function
            var challanFilter = $('#female-challan-filter');
            
            // Challan Status Filter  
            challanFilter.on('change', function() {
                var filterValue = $(this).val();
                
                // Clear any existing custom filters first
                $.fn.dataTable.ext.search.splice(0, $.fn.dataTable.ext.search.length);
                
                if (filterValue !== '') {
                    // Add custom filter for challan status
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        if (settings.nTable.id === 'female_students') {
                            var challanStatus = data[8]; // Index 8: Challan Status
                            var challanText = $('<div>').html(challanStatus).text().trim();
                            
                            if (filterValue === 'UNPAID') {
                                // For UNPAID: show UNPAID status OR (Allowed + null challan status)
                                var allowedStatus = data[5]; // Index 5: Allowed Status
                                var allowedText = $('<div>').html(allowedStatus).text().trim();
                                
                                return challanText === 'UNPAID' || 
                                       (allowedText === 'Allowed' && (challanText === 'N/A' || challanText === ''));
                            } else {
                                // For PAID: show only exact PAID status
                                return challanText === filterValue;
                            }
                        }
                        return true;
                    });
                }
                
                api.draw();
            });
        }
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB'); // DD/MM/YYYY format
}

function formatOldDate(dateString) {
    // Handle old date format with dots
    const cleanDate = dateString.replace(/\./g, ':');
    const date = new Date(cleanDate);
    return date.toISOString().split('T')[0]; // YYYY-MM-DD format
}

function updateSeatReservation(student_id) {
    var hiddenCheckbox = document.getElementById('seat_reservation_' + student_id);
    var visibleCheckbox = document.getElementById('dummy_' + student_id);
    
    if (hiddenCheckbox && visibleCheckbox) {
        // Set the hidden checkbox value based on the visible checkbox state
        hiddenCheckbox.checked = visibleCheckbox.checked;
    }
}

        // Debug function to check action button logic
        function debugActionButton(student) {
            console.log('=== DEBUG ACTION BUTTON ===');
            console.log('Enrollment:', student.happ_enrollment_no);
            console.log('Allowed Status:', student.happ_allowed_status, 'Type:', typeof student.happ_allowed_status);
            console.log('Challan Status:', student.sc_challan_status, 'Type:', typeof student.sc_challan_status);
            console.log('Is Allowed (==):', student.happ_allowed_status == 'Allowed');
            console.log('Is Allowed (===):', student.happ_allowed_status === 'Allowed');
            console.log('Is Paid:', student.sc_challan_status === 'PAID');
            console.log('==========================');
        }
        
        // Fix action button logic
        function getActionButton(student) {
            const allowedStatus = (student.happ_allowed_status || '').toString().trim();
            const challanStatus = (student.sc_challan_status || '').toString().trim();
            const isPaid = challanStatus === 'PAID';
            
            console.log('Fixed Logic - Allowed:', `"${allowedStatus}"`, 'Challan:', `"${challanStatus}"`, 'Is Paid:', isPaid);
            
            if (allowedStatus === 'Allowed') {
                if (isPaid) {
                    return `<span class="badge badge-seat-reserved"><i class="fa fa-bed mr-1"></i>Seat Reserved</span>`;
                } else {
                    return `<button type="button" class="btn btn-outline-danger btn-sm" onclick="showRejectModal('${student.happ_enrollment_no}', 'old')">
                        <i class="fa fa-times-circle mr-1"></i>Reject
                       </button>`;
                }
            } else {
                return `<button type="button" class="btn btn-outline-success btn-sm" onclick="showAllowModal('${student.happ_enrollment_no}', 'old')">
                    <i class="fa fa-check-circle mr-1"></i>Allow
                   </button>`;
            }
        }

function showAllowModal(enrollment_no, fromManualSearch, forceGender, scChallanStatus) {
    $('input[name="hostel_location"]').prop('checked', false);

    let gender = forceGender || null;
    if (!gender) {
        gender = $('#male-tab').hasClass('active') ? 'male' : 'female';
    }

    $('#confirmAllow').data('enrollment_no', enrollment_no);
    $('#confirmAllow').data('from_manual_search', fromManualSearch === true);
    $('#confirmAllow').data('gender', gender);
    $('#confirmAllow').data('challan_status', scChallanStatus || '');

    $('#allowModal').modal('show');
}

function showRejectModal(enrollment_no, fromManualSearch) {
    $('#confirmReject').data('enrollment_no', enrollment_no);
    $('#confirmReject').data('from_manual_search', fromManualSearch === true);
    $('#rejectModal').modal('show');
}

function getStudentGenderKey(gender) {
    return String(gender || '').toUpperCase().trim() === 'MALE' ? 'male' : 'female';
}

function resetAddStudentModal() {
    addStudentSearchData = null;
    $('#add_student_search').val('');
    $('#add-student-result').removeClass('is-visible');
    $('#add-result-sai-id').text('—');
    $('#add-result-enrollment').text('—');
    $('#add-result-name').text('—');
    $('#add-result-gender').text('—');
    $('#add-result-city').text('—');
    $('#add-result-list-status').removeClass('is-yes').addClass('is-no')
        .html('<i class="fa fa-times-circle"></i> Not in list');
    $('#confirmAddStudent').addClass('d-none').prop('disabled', false)
        .html('<i class="fa fa-check mr-1"></i>Add to Hostel List');
    $('#add-student-info').html('<i class="fa fa-info-circle"></i><span>Adding this student will create an entry in hostel applicants for the selected session and show them in the list below.</span>');
}

function searchStudentForAdd() {
    const sessionId = $('#session_id').val();
    const search = $('#add_student_search').val().trim();

    if (!sessionId) {
        showToast('error', 'Please select Academic Session first.');
        return;
    }

    if (!search) {
        showToast('error', 'Please enter Enrollment No. or Student ID.');
        return;
    }

    const $btn = $('#btnSearchAddStudent');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Searching...');
    $('#confirmAddStudent').addClass('d-none');

    $.ajax({
        url: '/api/search-hostel-student',
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
        success: function(response) {
            if (response.success && response.student) {
                renderAddStudentResult(response.student);
            } else {
                resetAddStudentModal();
                $('#add_student_search').val(search);
                showToast('error', response.message || 'Student not found.');
            }
        },
        error: function(xhr) {
            resetAddStudentModal();
            $('#add_student_search').val(search);
            const message = (xhr.responseJSON && xhr.responseJSON.message)
                ? xhr.responseJSON.message
                : 'Error searching student. Please try again.';
            showToast('error', message);
        },
        complete: function() {
            $btn.prop('disabled', false).html('<i class="fa fa-search mr-1"></i>Search');
        }
    });
}

function renderAddStudentResult(student) {
    addStudentSearchData = student;
    const inList = !!student.in_hostel_list;

    $('#add-result-sai-id').text(student.sai_id || 'N/A');
    $('#add-result-enrollment').text(student.sai_enrollment_no || 'N/A');
    $('#add-result-name').text(student.student_name || 'N/A');
    $('#add-result-gender').text(student.gender || 'N/A');
    $('#add-result-city').text(student.permanent_city || 'N/A');

    const $listBadge = $('#add-result-list-status');
    if (inList) {
        $listBadge.removeClass('is-no').addClass('is-yes')
            .html('<i class="fa fa-check-circle"></i> Already in list');
        $('#confirmAddStudent').addClass('d-none');
        $('#add-student-info').html('<i class="fa fa-check-circle"></i><span>This student is already in the hostel list for the selected session.</span>');
    } else {
        $listBadge.removeClass('is-yes').addClass('is-no')
            .html('<i class="fa fa-times-circle"></i> Not in list');
        $('#confirmAddStudent').removeClass('d-none');
        $('#add-student-info').html('<i class="fa fa-info-circle"></i><span>Adding this student will create an entry in hostel applicants for the selected session and show them in the list below.</span>');
    }

    $('#add-student-result').addClass('is-visible');
}

function addHostelStudentToList() {
    if (!addStudentSearchData || !addStudentSearchData.sai_enrollment_no) {
        showToast('error', 'Please search a student first.');
        return;
    }

    const sessionId = $('#session_id').val();
    if (!sessionId) {
        showToast('error', 'Please select Academic Session first.');
        return;
    }

    const $btn = $('#confirmAddStudent');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Processing...');

    $.ajax({
        url: '/api/add-hostel-student',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            session_id: sessionId,
            enrollment_no: addStudentSearchData.sai_enrollment_no,
            sai_id: addStudentSearchData.sai_id
        }),
        success: function(response) {
            if (response.success) {
                showToast(
                    response.already_exists ? 'warning' : 'success',
                    response.message
                );
                $('#addStudentModal').modal('hide');
                loadHostelData();

                const gender = getStudentGenderKey(
                    (response.student && response.student.gender) || addStudentSearchData.gender
                );
                if (gender === 'male') {
                    $('#male-tab').tab('show');
                } else if (gender === 'female') {
                    $('#female-tab').tab('show');
                }
            } else {
                showToast('error', response.message || 'Failed to add student to hostel list.');
            }
        },
        error: function(xhr) {
            const message = (xhr.responseJSON && xhr.responseJSON.message)
                ? xhr.responseJSON.message
                : 'Error adding student. Please try again.';
            showToast('error', message);
        },
        complete: function() {
            $btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i>Add to Hostel List');
        }
    });
}

// Function to refresh seat counts without reloading the entire page
function refreshSeatCounts() {
    const sessionId = $('#session_id').val();
    
    if (!sessionId) {
        return;
    }
    
    $.ajax({
        url: '/api/get-hostel-students-data',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            session_id: sessionId || ''
        }),
        success: function(response) {
            // Update counts only (don't reload tables)
            $('#maleCount').text(response.maleCount || 0);
            $('#femaleCount').text(response.femaleCount || 0);

            window.maleAvailableSeats = parseInt(response.male_available_seats || response.male_vacant_seats || 0);
            window.femaleAvailableSeats = parseInt(response.female_available_seats || response.female_vacant_seats || 0);
            window.maleAllowedCount = parseInt(response.male_allowed_count || 0);
            window.femaleAllowedCount = parseInt(response.female_allowed_count || 0);
            window.maleRemainingSeats = parseInt(response.male_remaining_seats ?? Math.max(0, window.maleAvailableSeats - window.maleAllowedCount));
            window.femaleRemainingSeats = parseInt(response.female_remaining_seats ?? Math.max(0, window.femaleAvailableSeats - window.femaleAllowedCount));

            // Refresh dashboard for the currently active tab
            const activeTab = $('#studentsTab .nav-link.active').attr('href');
            if (activeTab === '#male') {
                loadHostelDashboard('male');
            } else if (activeTab === '#female') {
                loadHostelDashboard('female');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error refreshing seat counts:', error);
        }
    });
}

function showHostelValidationError(response, type) {
    if (response.details && response.details.message) {
        showToast(type, response.details.message);
    } else {
        showToast(type, response.message || 'Validation Error');
    }
}

$(document).on('click', '#confirmAllow', function() {
    const $button = $(this);
    const enrollment_no = $button.data('enrollment_no');
    const sessionId = $('#session_id').val();
    const hostelLocation = $('input[name="hostel_location"]:checked').val();

    if (!hostelLocation) {
        showToast('error', 'Please select hostel location preference');
        return;
    }

    const gender = $button.data('gender') || ($('#male-tab').hasClass('active') ? 'male' : 'female');
    const locationKey = hostelLocation === 'on_campus' ? 'on_campus' : 'off_campus';
    const availableSeats = parseInt($(`#stats_${gender}_${locationKey}_available`).val()) || 0;
    const allowedCount = parseInt($(`#stats_${gender}_${locationKey}_allowed`).val()) || 0;

    if (allowedCount >= availableSeats) {
        const locationLabel = hostelLocation === 'on_campus' ? 'ON campus hostel' : 'Off campus safari hostel';
        const genderLabel = gender === 'male' ? 'Male' : 'Female';
        showToast('error', `Cannot allow more ${genderLabel} students for ${locationLabel}. Available: ${availableSeats}, Already Allowed: ${allowedCount}. Please select a different location or increase capacity.`);
        return;
    }

    if (!sessionId) {
        showToast('error', 'Please select Academic Session first.');
        $('#allowModal').modal('hide');
        return;
    }

    const checkbox = document.getElementById('dummy_' + enrollment_no);
    const fromManualSearch = $('#confirmAllow').data('from_manual_search');
    const storedChallanStatus = String($('#confirmAllow').data('challan_status') || '').toUpperCase();
    let seatReservationValue = checkbox && checkbox.checked ? '1' : '0';

    if (fromManualSearch && storedChallanStatus !== 'PAID' && storedChallanStatus !== 'UNPAID') {
        seatReservationValue = '1';
    }

    const autoGenerateChallan = seatReservationValue === '1';

    $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i>Processing...');

    $.ajax({
        url: '/api/allow-old-applicant/' + enrollment_no,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            session_id: sessionId,
            seat_reservation_map_display: seatReservationValue,
            hostel_location: hostelLocation,
            auto_generate_challan: autoGenerateChallan
        }),
        success: function(response) {
            if (response.success) {
                showToast('success', response.message);
                updateStudentRow(enrollment_no, 'allowed', hostelLocation, response.allocationDetails);
                refreshSeatCounts();
            } else {
                if (response.errorType === 'seat_validation') {
                    showHostelValidationError(response, 'warning');
                } else {
                    showToast('error', response.message);
                }
            }
            $('#allowModal').modal('hide');
            $button.prop('disabled', false).html('<i class="fa fa-check mr-1"></i>Allow');
        },
        error: function(xhr, status, error) {
            console.error('Error allowing student:', error);
            let errorMessage = 'Error allowing student. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showToast('error', errorMessage);
            $('#allowModal').modal('hide');
            $button.prop('disabled', false).html('<i class="fa fa-check mr-1"></i>Allow');
        }
    });
});

$(document).on('click', '#confirmReject', function() {
    const enrollment_no = $(this).data('enrollment_no');
    const sessionId = $('#session_id').val();
    
    // Validate session is selected
    if (!sessionId) {
        showToast('error', 'Please select Academic Session first.');
        $('#rejectModal').modal('hide');
        return;
    }
    
    // Get checkbox value
    const checkbox = document.getElementById('dummy_' + enrollment_no);
    const seatReservationValue = checkbox && checkbox.checked ? '1' : '0';
    
    $.ajax({
        url: '/api/reject-old-applicant/' + enrollment_no,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            session_id: sessionId,
            seat_reservation_map_display: seatReservationValue
        }),
        success: function(response) {
            if (response.success) {
                showToast('success', response.message);
                updateStudentRow(enrollment_no, 'rejected');
                refreshSeatCounts();
            } else {
                showToast('error', response.message);
            }
            $('#rejectModal').modal('hide');
        },
        error: function(xhr, status, error) {
            console.error('Error rejecting student:', error);
            let errorMessage = 'Error rejecting student. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showToast('error', errorMessage);
            $('#rejectModal').modal('hide');
        }
    });
});



// Old export function removed - now using DataTable's built-in export functionality

/**
 * Show summary stats panel for a specific gender.
 */
function showSummaryStatsForGender(gender) {
    const genderLabel = gender === 'male' ? 'Male Students' : 'Female Students';
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
 * Load the professional hostel stats dashboard via AJAX
 */
function loadHostelDashboard(gender) {
    const sessionId = $('#session_id').val();
    const container = (gender === 'male') ? $('#male-stats-dashboard-container') : $('#female-stats-dashboard-container');

    if (!sessionId) return;

    showSummaryStatsForGender(gender);

    if (hostelDashboardRequests[gender]) {
        hostelDashboardRequests[gender].abort();
        hostelDashboardRequests[gender] = null;
    }

    container.html('<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading Dashboard...</div>');

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
            context: 'student',
            _token: '{{ csrf_token() }}'
        },
        success: function(html) {
            container.html(html);
            setTimeout(adjustHostelStudentsTables, 50);
        },
        error: function(_xhr, status) {
            if (status === 'abort') {
                return;
            }
            container.html('<div class="alert alert-danger py-2">Failed to load dashboard.</div>');
        },
        complete: function() {
            hostelDashboardRequests[gender] = null;
        }
    });
}

</script>

@endsection
