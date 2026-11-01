@extends('layouts.app')

@section('content')
<head>
    <title>Export to Excel</title>
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
        
        .badge i {
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
            margin-right: 6px;
            transform: scale(1.3);
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
        
        /* Dropdown container styling */
        .form-group {
            margin-bottom: 24px !important;
        }
        
        /* Additional dropdown styling for better browser compatibility */
        #session_id, #admission_schedule_id {
            font-size: 16px !important;
            font-weight: 500 !important;
        }
        
        #session_id option, #admission_schedule_id option {
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
        
        /* Consistent select height */
        select.form-control:not([size]):not([multiple]) {
            height: 3.375rem;
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

    }
    

</style>

    <div class=" py-4">
        <div class="card shadow-sm">

            <div class="card-header bg-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-white"><i class="fa fa-building mr-2"></i>Hostel Applicants</h4>

                    <span class="badge badge-light" style="background-color: #4454c3; color: white;"></span>
                </div>
            </div>

            <div class="card-body bg-light border-bottom">
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-4">
                        <div class="form-group">
                            <label for="session_id" class="font-weight-bold text-primary">
                                <i class="fa fa-calendar mr-2"></i>Academic Session
                            </label>
                            <select id="session_id" name="session_id" class="form-control form-control-lg" required>
                                <option value="">Select Academic Session</option>
                                @foreach ($session as $sess)
                                    <option value="{{ $sess->sess_id }}">{{ $sess->sess_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-4">
                        <div class="form-group">
                            <label for="admission_schedule_id" class="font-weight-bold text-primary">
                                <i class="fa fa-calendar mr-2"></i>Admission Schedule
                            </label>
                            <select id="admission_schedule_id" name="admission_schedule_id" class="form-control form-control-lg" required>
                                <option value="">Select Admission Schedule</option>
                                <option value="1">Early Round</option>
                                <option value="2">Regular Round</option>
                                <option value="3">Rolling Round</option>
                            </select>
                        </div>
                    </div>
                </div>
                

            </div>




            <div class="card-body">
                <div class="form-group mb-4">
                    <div class="input-group">
                        {{-- <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                        </div> --}}
                        {{-- <input type="text" id="searchInput" class="form-control" placeholder="Search applicants..."> --}}
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div></div>
                    <!-- Export buttons are now handled by DataTables -->
                </div>

                <div id="loading-section" class="loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-3">Loading hostel applicants data...</p>
                </div>

                <div id="content-section" style="display: none;">
                    <div id="no-session-message" class="text-center py-5" style="display: none;">
                        <div class="mb-4">
                            <i class="fa fa-calendar-times fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">Please Select Academic Session and Admission Schedule</h5>
                        <p class="text-muted">Choose both Academic Session and Admission Schedule from the dropdowns above to view hostel applicants data.</p>
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
                                            You cannot allow more applicants than available seats. If you need to allow more applicants, 
                                            first vacate seats from applicants who haven't given consent yet (shown in "Can Vacate" count).
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    <ul class="nav nav-tabs" id="applicantsTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="new-students-tab" data-toggle="tab" href="#new-students" role="tab">
                                    <i class="fa fa-user-plus mr-1"></i>Applicants <span class="badge badge-primary" id="new_count">0</span>
                            </a>
                        </li>
                    </ul>

                <div class="tab-content mt-4" id="applicantsTabContent">
                    <!-- Applicants Tab -->
                    <div class="tab-pane fade show active" id="new-students" role="tabpanel">
                        <ul class="nav nav-tabs" id="newApplicantsTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="new-male-tab" data-toggle="tab" href="#new-male" role="tab">
                                        <i class="fa fa-male mr-1"></i>Male <span class="badge badge-primary" id="maleCount">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="new-female-tab" data-toggle="tab" href="#new-female" role="tab">
                                        <i class="fa fa-female mr-1"></i>Female <span class="badge badge-primary" id="femaleCount">0</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content mt-4" id="newApplicantsTabContent">
                            <!-- Male Applicants -->
                            <div class="tab-pane fade show active" id="new-male" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0 text-primary"><i class="fa fa-male mr-2"></i>Male Applicants</h5>
                                    <div class="text-right">
                                        <div class="mb-3">
                                            <span class="available-seats-badge" id="male_vacant_seats">
                                                <i class="fa fa-bed mr-1"></i>Available Seats: 0
                                            </span>
                                            <span class="available-seats-badge ml-2" id="male_remaining_seats" style="display: none;">
                                                <i class="fa fa-calculator mr-1"></i>Remaining: 0
                                            </span>
                                        </div>
                                                        <div class="seat-status-container" id="male_seat_status">
                    <div class="status-item">
                        <span class="status-icon allowed-icon"><i class="fa fa-check-circle"></i></span>
                        <span class="status-label">Allowed</span>
                        <span class="status-count" id="male_allowed_count">0</span>
                    </div>
                    <div class="status-item">
                        <span class="status-icon consent-icon"><i class="fa fa-file-text"></i></span>
                        <span class="status-label">Consent</span>
                        <span class="status-count" id="male_challan_count">0</span>
                    </div>
                    <div class="status-item">
                        <span class="status-icon vacate-icon"><i class="fa fa-exclamation-triangle"></i></span>
                        <span class="status-label">UNPAID</span>
                        <span class="status-count" id="male_vacate_count">0</span>
                    </div>
                    <div class="status-item">
                        <span class="status-icon paid-icon"><i class="fa fa-credit-card"></i></span>
                        <span class="status-label">Paid</span>
                        <span class="status-count" id="male_paid_count">0</span>
                    </div>
                </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                        <table class="table" id="male_aplicants">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Applicant Id</th>
                                                <th>Enrollment No</th>
                                                <th>Admission Round</th>
                                                <th>Name</th>
                                                <th>City</th>
                                                <th>Permanent Address</th>
                                                <th>Apply Date</th>
                                                <th>Allowed Status</th>
                                                <th>Scholarship Details</th>
                                                <th>Last Degree Marks</th>
                                                <th>Challan Status</th>
                                                <th class="text-center">Disable Map Display</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                            <tbody id="male-applicants-tbody">
                                                <!-- Data will be loaded here via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Female Applicants -->
                            <div class="tab-pane fade" id="new-female" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0 text-primary"><i class="fa fa-female mr-2"></i>Female Applicants</h5>
                                    <div class="text-right">
                                        <div class="mb-3">
                                            <span class="available-seats-badge" id="female_vacant_seats">
                                                <i class="fa fa-bed mr-1"></i>Available Seats: 0
                                            </span>
                                            <span class="available-seats-badge ml-2" id="female_remaining_seats" style="display: none;">
                                                <i class="fa fa-calculator mr-1"></i>Remaining: 0
                                            </span>
                                        </div>
                                                        <div class="seat-status-container" id="female_seat_status">
                    <div class="status-item">
                        <span class="status-icon allowed-icon"><i class="fa fa-check-circle"></i></span>
                        <span class="status-label">Allowed</span>
                        <span class="status-count" id="female_allowed_count">0</span>
                    </div>
                    <div class="status-item">
                        <span class="status-icon consent-icon"><i class="fa fa-file-text"></i></span>
                        <span class="status-label">Consent</span>
                        <span class="status-count" id="female_challan_count">0</span>
                    </div>
                    <div class="status-item">
                        <span class="status-icon vacate-icon"><i class="fa fa-exclamation-triangle"></i></span>
                        <span class="status-label">UNPAID</span>
                        <span class="status-count" id="female_vacate_count">0</span>
                    </div>
                    <div class="status-item">
                        <span class="status-icon paid-icon"><i class="fa fa-credit-card"></i></span>
                        <span class="status-label">Paid</span>
                        <span class="status-count" id="female_paid_count">0</span>
                    </div>
                </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                        <table class="table" id="female_aplicants">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Applicant Id</th>
                                                <th>Enrollment No</th>
                                                <th>Admission Round</th>
                                                <th>Name</th>
                                                <th>City</th>
                                                <th>Permanent Address</th>
                                                <th>Apply Date</th>
                                                <th>Allowed Status</th>
                                                <th>Scholarship Details</th>
                                                <th>Last Degree Marks</th>
                                                <th>Challan Status</th>
                                                <th class="text-center">Disable Map Display</th>
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

    <!-- Allow Confirmation Modal -->
    <div class="modal fade" id="allowModal" tabindex="-1" role="dialog" aria-labelledby="allowModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allowModalLabel">Confirm Allow</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to allow this applicant?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmAllow">Allow</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Confirmation Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Confirm Reject</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to reject this applicant?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmReject">Reject</button>
                </div>
            </div>
        </div>
    </div>


<script>
$(document).ready(function() {
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
    
    // Add change event listener for session dropdown
    $('#session_id').on('change', function() {
        const sessionId = $(this).val();
        
        // Clear admission schedule when session changes
        $('#admission_schedule_id').val('');
        
        if (sessionId) {
            loadHostelData();
        } else {
            // Clear all data when session is not selected
            clearAllData();
        }
    });
    
    // Add change event listener for admission schedule dropdown
    $('#admission_schedule_id').on('change', function() {
        const sessionId = $('#session_id').val();
        const admissionScheduleId = $(this).val();
        
        if (sessionId) {
            loadHostelData();
        } else {
            // Clear all data when session is not selected
            clearAllData();
        }
    });
    
    // Don't load data by default - wait for session selection
    clearAllData();
});

function clearAllData() {
    // Reset all counts to 0
    $('#new_count').text('0');
    $('#maleCount').text('0');
    $('#femaleCount').text('0');
    
    // Reset vacant seats
    $('#male_vacant_seats').text('Available Seats: 0').hide();
    $('#female_vacant_seats').text('Available Seats: 0').hide();
    
    // Reset remaining seats
    $('#male_remaining_seats').text('Remaining: 0').hide();
    $('#female_remaining_seats').text('Remaining: 0').hide();
    
    // Reset seat status information
    $('#male_allowed_count').text('0');
    $('#female_allowed_count').text('0');
    
    $('#male_challan_count').text('0');
    $('#female_challan_count').text('0');
    
    $('#male_paid_count').text('0');
    $('#female_paid_count').text('0');
    
    $('#male_vacate_count').text('0');
    $('#female_vacate_count').text('0');
    
    // Destroy DataTables if they exist
    destroyDataTables();
    
    // Clear all table bodies
    $('#male-applicants-tbody').empty();
    $('#female-applicants-tbody').empty();
    
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
    // Destroy male applicants DataTable
    if ($.fn.DataTable.isDataTable('#male_aplicants')) {
        $('#male_aplicants').DataTable().destroy();
    }
    
    // Destroy female applicants DataTable
    if ($.fn.DataTable.isDataTable('#female_aplicants')) {
        $('#female_aplicants').DataTable().destroy();
    }
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

function refreshSeatCounts() {
    const sessionId = $('#session_id').val();
    const admissionScheduleId = $('#admission_schedule_id').val();
    
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
        success: function(response) {
            // Update counts only (don't reload tables)
            $('#new_count').text(response.new_count || 0);
            $('#maleCount').text(response.maleCount || 0);
            $('#femaleCount').text(response.femaleCount || 0);
            
            // Store available seats data globally for later use
            window.maleAvailableSeats = response.male_vacant_seats || 0;
            window.femaleAvailableSeats = response.female_vacant_seats || 0;
            
            // Hide original available seats and show remaining calculation
            $('#male_vacant_seats').hide();
            $('#female_vacant_seats').hide();
            
            // Calculate and show remaining seats
            const maleRemaining = (response.male_vacant_seats || 0) - (response.male_allowed_count || 0);
            const femaleRemaining = (response.female_vacant_seats || 0) - (response.female_allowed_count || 0);
            
            $('#male_remaining_seats').html('<i class="fa fa-calculator mr-1"></i>Remaining: ' + maleRemaining).show();
            $('#female_remaining_seats').html('<i class="fa fa-calculator mr-1"></i>Remaining: ' + femaleRemaining).show();
            
            // Update seat status information
            $('#male_allowed_count').text(response.male_allowed_count || 0);
            $('#female_allowed_count').text(response.female_allowed_count || 0);
            
            $('#male_challan_count').text(response.male_challan_count || 0);
            $('#female_challan_count').text(response.female_challan_count || 0);
            
            $('#male_paid_count').text(response.male_paid_count || 0);
            $('#female_paid_count').text(response.female_paid_count || 0);
            
            $('#male_vacate_count').text(response.male_vacate_count || 0);
            $('#female_vacate_count').text(response.female_vacate_count || 0);
        },
        error: function(xhr, status, error) {
            console.error('Error refreshing seat counts:', error);
        }
    });
}

function updateRemainingSeatsCount() {
    // Get current counts from the page
    const maleAllowedCount = parseInt($('#male_allowed_count').text()) || 0;
    const femaleAllowedCount = parseInt($('#female_allowed_count').text()) || 0;
    
    // Get available seats from the response data (we need to store this)
    const maleAvailableSeats = window.maleAvailableSeats || 0;
    const femaleAvailableSeats = window.femaleAvailableSeats || 0;
    
    // Calculate remaining seats
    const maleRemaining = maleAvailableSeats - maleAllowedCount;
    const femaleRemaining = femaleAvailableSeats - femaleAllowedCount;
    
    // Update the display
    $('#male_remaining_seats').html('<i class="fa fa-calculator mr-1"></i>Remaining: ' + maleRemaining);
    $('#female_remaining_seats').html('<i class="fa fa-calculator mr-1"></i>Remaining: ' + femaleRemaining);
}

function updateApplicantRow(aai_id, action) {
    console.log('Updating row for aai_id:', aai_id, 'action:', action);
    
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
        
        // Update the action button (13th column, index 12)
        const actionCell = targetRow.find('td:eq(12)');
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
            newActionButton = `<button type="button" class="btn btn-outline-success btn-sm" onclick="showAllowModal('${aai_id}', 'new')">
                <i class="fa fa-check-circle mr-1"></i>Allow
               </button>`;
        }
        
        actionCell.html(newActionButton);
        
        // Update the map display checkbox - respect the actual checkbox state from server
        if (action === 'allowed') {
            // If allowed, checkbox should be disabled but keep its original state
            checkbox.prop('disabled', true);
        } else if (action === 'rejected') {
            // If rejected, checkbox should be unchecked and enabled
            checkbox.prop('checked', false).prop('disabled', false);
        }
        
        // Update the allowed status column (8th column, index 7)
        const statusCell = targetRow.find('td:eq(7)');
        if (action === 'allowed') {
            statusCell.html('<span class="badge badge-allowed"><i class="fa fa-check-circle"></i>Allowed</span>');
            // Update allowed count immediately
            const currentAllowedCount = parseInt($('#male_allowed_count').text()) || 0;
            const currentFemaleAllowedCount = parseInt($('#female_allowed_count').text()) || 0;
            
            // Determine if this is male or female based on which table the row is in
            if (targetRow.closest('#male_aplicants').length > 0) {
                $('#male_allowed_count').text(currentAllowedCount + 1);
            } else if (targetRow.closest('#female_aplicants').length > 0) {
                $('#female_allowed_count').text(currentFemaleAllowedCount + 1);
            }
        } else if (action === 'rejected') {
            statusCell.html('<span class="badge badge-secondary"><i class="fa fa-times-circle"></i>Not Allowed</span>');
            // Update allowed count immediately
            const currentAllowedCount = parseInt($('#male_allowed_count').text()) || 0;
            const currentFemaleAllowedCount = parseInt($('#female_allowed_count').text()) || 0;
            
            // Determine if this is male or female based on which table the row is in
            if (targetRow.closest('#male_aplicants').length > 0) {
                $('#male_allowed_count').text(Math.max(0, currentAllowedCount - 1));
            } else if (targetRow.closest('#female_aplicants').length > 0) {
                $('#female_allowed_count').text(Math.max(0, currentFemaleAllowedCount - 1));
            }
        }
        
        console.log('Row updated successfully');
        
        // Update remaining seats count immediately
        updateRemainingSeatsCount();
    } else {
        console.log('Row not found for aai_id:', aai_id);
    }
}

function loadHostelData() {
    const sessionId = $('#session_id').val();
    const admissionScheduleId = $('#admission_schedule_id').val();
    
    // Validate session is selected
    if (!sessionId) {
        showToast('error', 'Please select Academic Session first.');
        return;
    }
    
    // Show loading and hide content
    $('#loading-section').show();
    $('#content-section').hide();

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
        success: function(response) {
            // Update counts
            $('#new_count').text(response.new_count || 0);
            $('#maleCount').text(response.maleCount || 0);
            $('#femaleCount').text(response.femaleCount || 0);
            
            // Store available seats data globally for later use
            window.maleAvailableSeats = response.male_vacant_seats || 0;
            window.femaleAvailableSeats = response.female_vacant_seats || 0;
            
            // Hide original available seats and show remaining calculation
            $('#male_vacant_seats').hide();
            $('#female_vacant_seats').hide();
            
            // Calculate and show remaining seats
            const maleRemaining = (response.male_vacant_seats || 0) - (response.male_allowed_count || 0);
            const femaleRemaining = (response.female_vacant_seats || 0) - (response.female_allowed_count || 0);
            
            $('#male_remaining_seats').html('<i class="fa fa-calculator mr-1"></i>Remaining: ' + maleRemaining).show();
            $('#female_remaining_seats').html('<i class="fa fa-calculator mr-1"></i>Remaining: ' + femaleRemaining).show();
            
            // Update seat status information
            $('#male_allowed_count').text(response.male_allowed_count || 0);
            $('#female_allowed_count').text(response.female_allowed_count || 0);
            
            $('#male_challan_count').text(response.male_challan_count || 0);
            $('#female_challan_count').text(response.female_challan_count || 0);
            
            $('#male_paid_count').text(response.male_paid_count || 0);
            $('#female_paid_count').text(response.female_paid_count || 0);
            
            $('#male_vacate_count').text(response.male_vacate_count || 0);
            $('#female_vacate_count').text(response.female_vacate_count || 0);
            
            // Destroy existing DataTables before populating new data
            destroyDataTables();
            
            // Populate tables
            populateMaleApplicants(response.maleApplicants || []);
            populateFemaleApplicants(response.femaleApplicants || []);
            
            // Show content and hide loading
            $('#loading-section').hide();
            $('#content-section').show();
            
            // Hide no-session message and show data content
            $('#no-session-message').hide();
            $('#data-content').show();
            
            // Show the important note when data is loaded
            $('#important-note').show();
        },
        error: function(xhr, status, error) {
            console.error('Error loading data:', error);
            showToast('error', 'Error loading data. Please try again.');
            $('#loading-section').hide();
            $('#content-section').show();
            clearAllData();
        }
    });
}
 
function populateMaleApplicants(applicants) {
    const tbody = $('#male-applicants-tbody');
    tbody.empty();
    
    // Convert object to array if needed
    const applicantsArray = Array.isArray(applicants) ? applicants : Object.values(applicants);
    
    applicantsArray.forEach(function(applicant) {
        const rowClass = applicant.allowed_status == 'Allowed' ? 'allowed-row' : '';
        const applyDate = applicant.aai_apply_date ? formatDate(applicant.aai_apply_date) : 'N/A';
        
        // Handle checkbox state based on server data
        let checkboxAttributes = '';
        if (applicant.allowed_status == 'Allowed') {
            // If allowed, checkbox should be disabled
            checkboxAttributes = applicant.ha_disable_map_display == 'Yes' ? 'checked disabled' : 'disabled';
        } else {
            // If not allowed, checkbox should be enabled
            checkboxAttributes = applicant.ha_disable_map_display == 'Yes' ? 'checked' : '';
        }
        
        // Check if challan is paid
        const isPaid = applicant.sc_challan_status === 'PAID';
        
        let actionButton = '';
        if (applicant.allowed_status == 'Allowed') {
            if (isPaid) {
                actionButton = `<span class="badge badge-seat-reserved"><i class="fa fa-bed mr-1"></i>Seat Reserved</span>`;
            } else {
                actionButton = `<button type="button" class="btn btn-outline-danger btn-sm" onclick="showRejectModal('${applicant.aai_id}', 'new')">
                    <i class="fa fa-times-circle mr-1"></i>Reject
                   </button>`;
            }
        } else {
            actionButton = `<button type="button" class="btn btn-outline-success btn-sm" onclick="showAllowModal('${applicant.aai_id}', 'new')">
                <i class="fa fa-check-circle mr-1"></i>Allow
               </button>`;
        }
        
        const row = `
            <tr class="${rowClass}" data-challan-status="${applicant.sc_challan_status || ''}">
                <td>${applicant.aai_id || 'N/A'}</td>
                <td>${applicant.cms_sai_enrollment_no || 'N/A'}</td>
                <td>${applicant.admission_sch_round || 'N/A'}</td>
                <td>${applicant.api_name || 'N/A'}</td>
                <td>${applicant.api_permanentcity || 'N/A'}</td>
                <td>${applicant.api_permanentaddress || 'N/A'}</td>
                <td>${applyDate}</td>
                <td>
                    <span class="badge ${applicant.allowed_status === 'Allowed' ? 'badge-allowed' : 'badge-secondary'}">
                        <i class="fa ${applicant.allowed_status === 'Allowed' ? 'fa-check-circle' : 'fa-times-circle'}"></i>
                        ${applicant.allowed_status || 'N/A'}
                    </span>
                </td>
                <td>${applicant.scholarship_details || 'N/A'}</td>
                <td>${applicant.last_degree_marks || 'N/A'}</td>
                <td>
                    <span class="badge ${applicant.sc_challan_status === 'PAID' ? 'badge-paid' : applicant.sc_challan_status === 'UNPAID' ? 'badge-unpaid' : 'badge-secondary'}">
                        <i class="fa ${applicant.sc_challan_status === 'PAID' ? 'fa-credit-card' : applicant.sc_challan_status === 'UNPAID' ? 'fa-exclamation-triangle' : 'fa-question-circle'}"></i>
                        ${applicant.sc_challan_status || 'N/A'}
                    </span>
                </td>
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="seat_reservation_map_display" id="dummy_${applicant.aai_id}" ${checkboxAttributes}>
                    </div>
                </td>
                <td class="text-center">
                    ${actionButton}
                </td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Initialize DataTable for male applicants
    if ($.fn.DataTable.isDataTable('#male_aplicants')) {
        $('#male_aplicants').DataTable().destroy();
    }
    
    $('#male_aplicants').DataTable({
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"i>>' +
             '<"row"<"col-sm-12"<"custom-filters">>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Male Hostel Applicants',
                text: '<i class="fa fa-file-excel-o mr-1"></i>Excel',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] // Include all columns except Map Display and Actions
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'Male Hostel Applicants',
                text: '<i class="fa fa-file-pdf-o mr-1"></i>PDF',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] // Include all columns except Map Display and Actions
                }
            },
            {
                extend: 'print',
                title: 'Male Hostel Applicants',
                text: '<i class="fa fa-print mr-1"></i>Print',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] // Include all columns except Map Display and Actions
                }
            }
        ],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        responsive: true,
        language: {
            search: "Search applicants:",
            lengthMenu: "Show _MENU_ applicants per page",
            info: "Showing _START_ to _END_ of _TOTAL_ applicants",
            infoEmpty: "Showing 0 to 0 of 0 applicants",
            infoFiltered: "(filtered from _MAX_ total applicants)"
        },
        initComplete: function () {
            // Add custom filters
            var api = this.api();
            
            // Create professional filter container
            var filterContainer = $('<div class="filter-section mb-4">' +
                '<div class="filter-header">' +
                '<h6 class="filter-title"><i class="fa fa-filter mr-2"></i>Advanced Filters</h6>' +
                '<div class="filter-subtitle">Filter applicants by payment status</div>' +
                '</div>' +
                '<div class="filter-content">' +
                '<div class="row">' +
                '<div class="col-md-6 col-lg-4">' +
                '<div class="filter-group">' +
                '<label class="filter-label"><i class="fa fa-credit-card mr-2"></i>Payment Status</label>' +
                '<div class="select-wrapper">' +
                '<select class="form-control filter-select" id="male-challan-filter">' +
                '<option value="" class="filter-option">📋 Show All Records</option>' +
                '<option value="PAID" class="filter-option">✅ Paid Applicants</option>' +
                '<option value="UNPAID" class="filter-option">❌ Unpaid Applicants</option>' +
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
                        if (settings.nTable.id === 'male_aplicants') {
                            var challanStatus = data[10];
                            var challanText = $('<div>').html(challanStatus).text().trim();
                            
                            if (filterValue === 'UNPAID') {
                                // For UNPAID: show UNPAID status OR (Allowed + null challan status)
                                var allowedStatus = data[7];
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
            
            // Alternative Method 2: Using data attributes (if you want to modify the row generation)
            // In the row generation, you could add data attributes like:
            // <tr data-allowed="Allowed" data-challan="PAID">
            // Then filter like: $('tr[data-allowed="' + filterValue + '"]').show();
            
            // Alternative Method 3: Using server-side filtering
            // You could send filter parameters to server and get filtered data back
            
            // Alternative Method 4: Using jQuery filter directly
            // $('#male_aplicants tbody tr').filter(function() {
            //     return $(this).find('td:eq(7)').text().indexOf(allowedValue) !== -1;
            // }).show();
        }
    });
}

function populateFemaleApplicants(applicants) {
    const tbody = $('#female-applicants-tbody');
    tbody.empty();
    
    // Convert object to array if needed
    const applicantsArray = Array.isArray(applicants) ? applicants : Object.values(applicants);
    
    applicantsArray.forEach(function(applicant) {
        const rowClass = applicant.allowed_status == 'Allowed' ? 'allowed-row' : '';
        const applyDate = applicant.aai_apply_date ? formatDate(applicant.aai_apply_date) : 'N/A';
        
        // Handle checkbox state based on server data
        let checkboxAttributes = '';
        if (applicant.allowed_status == 'Allowed') {
            // If allowed, checkbox should be disabled
            checkboxAttributes = applicant.ha_disable_map_display == 'Yes' ? 'checked disabled' : 'disabled';
        } else {
            // If not allowed, checkbox should be enabled
            checkboxAttributes = applicant.ha_disable_map_display == 'Yes' ? 'checked' : '';
        }
        
        // Check if challan is paid
        const isPaid = applicant.sc_challan_status === 'PAID';
        
        let actionButton = '';
        if (applicant.allowed_status == 'Allowed') {
            if (isPaid) {
                actionButton = `<span class="badge badge-seat-reserved"><i class="fa fa-bed mr-1"></i>Seat Reserved</span>`;
            } else {
                actionButton = `<button type="button" class="btn btn-outline-danger btn-sm" onclick="showRejectModal('${applicant.aai_id}', 'new')">
                    <i class="fa fa-times-circle mr-1"></i>Reject
                   </button>`;
            }
        } else {
            actionButton = `<button type="button" class="btn btn-outline-success btn-sm" onclick="showAllowModal('${applicant.aai_id}', 'new')">
                <i class="fa fa-check-circle mr-1"></i>Allow
               </button>`;
        }
        
        const row = `
            <tr class="${rowClass}" data-challan-status="${applicant.sc_challan_status || ''}">
                <td>${applicant.aai_id || 'N/A'}</td>
                <td>${(applicant.cms_sai_enrollment_no || 'N/A')}</td>
                <td>${applicant.admission_sch_round || 'N/A'}</td>
                <td>${applicant.api_name || 'N/A'}</td>
                <td>${applicant.api_permanentcity || 'N/A'}</td>
                <td>${applicant.api_permanentaddress || 'N/A'}</td>
                <td>${applyDate}</td>
                <td>
                    <span class="badge ${applicant.allowed_status === 'Allowed' ? 'badge-allowed' : 'badge-secondary'}">
                        <i class="fa ${applicant.allowed_status === 'Allowed' ? 'fa-check-circle' : 'fa-times-circle'}"></i>
                        ${applicant.allowed_status || 'N/A'}
                    </span>
                </td>
                <td>${applicant.scholarship_details || 'N/A'}</td>
                <td>${applicant.last_degree_marks || 'N/A'}</td>
                <td>
                    <span class="badge ${applicant.sc_challan_status === 'PAID' ? 'badge-paid' : applicant.sc_challan_status === 'UNPAID' ? 'badge-unpaid' : 'badge-secondary'}">
                        <i class="fa ${applicant.sc_challan_status === 'PAID' ? 'fa-credit-card' : applicant.sc_challan_status === 'UNPAID' ? 'fa-exclamation-triangle' : 'fa-question-circle'}"></i>
                        ${applicant.sc_challan_status || 'N/A'}
                    </span>
                </td>
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="seat_reservation_map_display" id="dummy_${applicant.aai_id}" ${checkboxAttributes}>
                    </div>
                </td>
                <td class="text-center">
                    ${actionButton}
                </td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Initialize DataTable for female applicants
    if ($.fn.DataTable.isDataTable('#female_aplicants')) {
        $('#female_aplicants').DataTable().destroy();
    }
    
    $('#female_aplicants').DataTable({
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"i>>' +
             '<"row"<"col-sm-12"<"custom-filters">>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Female Hostel Applicants',
                text: '<i class="fa fa-file-excel-o mr-1"></i>Excel',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] // Include all columns except Map Display and Actions
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'Female Hostel Applicants',
                text: '<i class="fa fa-file-pdf-o mr-1"></i>PDF',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] // Include all columns except Map Display and Actions
                }
            },
            {
                extend: 'print',
                title: 'Female Hostel Applicants',
                text: '<i class="fa fa-print mr-1"></i>Print',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] // Include all columns except Map Display and Actions
                }
            }
        ],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        responsive: true,
        language: {
            search: "Search applicants:",
            lengthMenu: "Show _MENU_ applicants per page",
            info: "Showing _START_ to _END_ of _TOTAL_ applicants",
            infoEmpty: "Showing 0 to 0 of 0 applicants",
            infoFiltered: "(filtered from _MAX_ total applicants)"
        },
        initComplete: function () {
            // Add custom filters
            var api = this.api();
            
            // Create professional filter container
            var filterContainer = $('<div class="filter-section mb-4">' +
                '<div class="filter-header">' +
                '<h6 class="filter-title"><i class="fa fa-filter mr-2"></i>Advanced Filters</h6>' +
                '<div class="filter-subtitle">Filter applicants by payment status</div>' +
                '</div>' +
                '<div class="filter-content">' +
                '<div class="row">' +
                '<div class="col-md-6 col-lg-4">' +
                '<div class="filter-group">' +
                '<label class="filter-label"><i class="fa fa-credit-card mr-2"></i>Payment Status</label>' +
                '<div class="select-wrapper">' +
                '<select class="form-control filter-select" id="female-challan-filter">' +
                '<option value="" class="filter-option">📋 Show All Records</option>' +
                '<option value="PAID" class="filter-option">✅ Paid Applicants</option>' +
                '<option value="UNPAID" class="filter-option">❌ Unpaid Applicants</option>' +
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
                        if (settings.nTable.id === 'female_aplicants') {
                            var challanStatus = data[10];
                            var challanText = $('<div>').html(challanStatus).text().trim();
                            
                            if (filterValue === 'UNPAID') {
                                // For UNPAID: show UNPAID status OR (Allowed + null challan status)
                                var allowedStatus = data[7];
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



function updateSeatReservation(applicant_id) {
    var hiddenCheckbox = document.getElementById('seat_reservation_' + applicant_id);
    var visibleCheckbox = document.getElementById('dummy_' + applicant_id);
    
    if (hiddenCheckbox && visibleCheckbox) {
        // Set the hidden checkbox value based on the visible checkbox state
        hiddenCheckbox.checked = visibleCheckbox.checked;
    }
}

function showAllowModal(aai_id, type) {
    $('#confirmAllow').data('aai_id', aai_id);
    $('#confirmAllow').data('type', type);
    $('#allowModal').modal('show');
}

function showRejectModal(aai_id, type) {
    $('#confirmReject').data('aai_id', aai_id);
    $('#confirmReject').data('type', type);
    $('#rejectModal').modal('show');
}

$(document).on('click', '#confirmAllow', function() {
    const aai_id = $(this).data('aai_id');
    const type = $(this).data('type');
    const sessionId = $('#session_id').val();
    const admissionScheduleId = $('#admission_schedule_id').val();
    
    // Validate session is selected
    if (!sessionId) {
        showToast('error', 'Please select Academic Session first.');
        $('#allowModal').modal('hide');
        return;
    }
    
    // Get checkbox value
    const checkbox = document.getElementById('dummy_' + aai_id);
    const seatReservationValue = checkbox && checkbox.checked ? '1' : '0';
    
    let url = '';
    if (type === 'new') {
        url = '/api/allow-new-applicant/' + aai_id;
    } else {
        url = '/api/allow-old-applicant/' + aai_id;
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
        success: function(response) {
            if (response.success) {
                showToast('success', response.message);
                // Update only the specific row's action and map display columns
                updateApplicantRow(aai_id, 'allowed');
                // Refresh the seat counts after allowing
                refreshSeatCounts();
            } else {
                // Check if it's a seat validation error
                if (response.errorType === 'seat_validation') {
                    showHostelValidationError(response, 'warning');
                } else {
                    showToast('error', response.message);
                }
            }
            $('#allowModal').modal('hide');
        },
        error: function(xhr, status, error) {
            console.error('Error allowing applicant:', error);
            let errorMessage = 'Error allowing applicant. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showToast('error', errorMessage);
            $('#allowModal').modal('hide');
        }
    });
});

$(document).on('click', '#confirmReject', function() {
    const aai_id = $(this).data('aai_id');
    const type = $(this).data('type');
    const sessionId = $('#session_id').val();
    const admissionScheduleId = $('#admission_schedule_id').val();
    
    // Validate session is selected
    if (!sessionId) {
        showToast('error', 'Please select Academic Session first.');
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
        success: function(response) {
            if (response.success) {
                showToast('success', response.message);
                // Update only the specific row's action and map display columns
                updateApplicantRow(aai_id, 'rejected');
                // Refresh the seat counts after rejecting
                refreshSeatCounts();
            } else {
                showToast('error', response.message);
            }
            $('#rejectModal').modal('hide');
        },
        error: function(xhr, status, error) {
            console.error('Error rejecting applicant:', error);
            let errorMessage = 'Error rejecting applicant. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showToast('error', errorMessage);
            $('#rejectModal').modal('hide');
        }
    });
});



// Old export function removed - now using DataTable's built-in export functionality
</script>

@endsection
