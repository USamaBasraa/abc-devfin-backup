@extends('layouts.app')

@section('content')
@include('components.toast-notifications')
    @php
        $totalRequests = $requests->count();
        $pendingCount = $requests->filter(fn($r) => strtolower($r->status ?? '') === 'pending')->count();
        $completedCount = $requests->filter(fn($r) => strtolower($r->status ?? '') === 'completed')->count();
        $revisedCount = $requests->filter(fn($r) => strtolower($r->status ?? '') === 'revised')->count();
    @endphp

    <style>
        .harassment-header {
            background: #fff;
            border-radius: 10px;
            padding: 24px 28px;
            box-shadow: 0 10px 24px rgba(22, 34, 66, 0.08);
            margin: 12px 0 24px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .harassment-title {
            margin: 0;
            font-size: 28px;
            line-height: 1.25;
            font-weight: 700;
            color: #1f2937;
        }
        .harassment-subtitle {
            margin: 4px 0 0;
            color: #6b7280;
            font-size: 15px;
            line-height: 1.6;
        }
        .btn-new-request {
            background: #17675F;
            color: #fff;
            border: 0;
            border-radius: 999px;
            padding: 12px 20px;
            font-weight: 600;
            font-size: 14px;
            line-height: 1.2;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-new-request:hover {
            color: #fff;
            background: #12544d;
            text-decoration: none;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 18px 20px;
            box-shadow: 0 6px 18px rgba(17, 24, 39, 0.07);
            border-left: 4px solid #d1d5db;
        }
        .stat-card.pending { border-left-color: #f59e0b; }
        .stat-card.completed { border-left-color: #10b981; }
        .stat-card.revised { border-left-color: #ef4444; }
        .stat-number {
            margin: 0;
            font-size: 30px;
            line-height: 1.15;
            font-weight: 700;
        }
        .stat-label {
            margin: 4px 0 0;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.5;
            font-weight: 600;
            letter-spacing: .4px;
            text-transform: uppercase;
        }
        .table-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 24px rgba(22, 34, 66, 0.08);
            overflow: hidden;
            margin-bottom: 20px;
        }
        .table-card .table-responsive {
            padding: 8px 10px 2px 10px;
        }
        .custom-table thead th {
            background: #4555C3;
            color: #fff;
            border-color: #4555C3;
            font-size: 12px;
            line-height: 1.4;
            letter-spacing: .4px;
            text-transform: uppercase;
            padding: 14px 12px;
        }
        .custom-table td {
            vertical-align: middle;
            font-size: 14px;
            line-height: 1.55;
            padding: 14px 12px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
            line-height: 1.2;
            font-weight: 600;
            display: inline-block;
        }
        .status-pending { background: #fff7ed; color: #9a3412; }
        .status-completed { background: #ecfdf5; color: #065f46; }
        .status-revised { background: #fef2f2; color: #991b1b; }
        .status-default { background: #f3f4f6; color: #374151; }
        .btn-view {
            background: #4555C3;
            color: #fff;
            border: 0;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 12px;
            line-height: 1.2;
            font-weight: 600;
        }
        .btn-view:hover {
            background: #3745ad;
            color: #fff;
        }
        .empty-state {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 24px rgba(22, 34, 66, 0.08);
            padding: 64px 28px;
            text-align: center;
            margin-bottom: 20px;
        }
        .empty-state i {
            font-size: 24px;
            color: #cbd5e1;
            margin-bottom: 14px;
        }
        .empty-state h3 {
            font-size: 24px;
            line-height: 1.3;
            font-weight: 700;
            margin-bottom: 8px;
            color: #1f2937;
        }
        .empty-state p {
            font-size: 15px;
            line-height: 1.7;
            color: #6b7280;
            margin-bottom: 18px;
        }
        .modal-title {
            font-size: 18px;
            line-height: 1.3;
            font-weight: 700;
        }
        .modal-body {
            padding: 24px 26px;
            font-size: 14px;
            line-height: 1.65;
            color: #374151;
        }
        .modal-body strong {
            font-weight: 700;
            color: #111827;
        }
        .modal-footer {
            padding: 12px 18px 16px 18px;
        }

        .staff-report-modal .modal-content {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(17, 24, 39, 0.18);
        }

        .staff-report-modal .modal-header {
            background: linear-gradient(135deg, #4555C3 0%, #3544a8 100%);
            color: #fff;
            border-bottom: none;
            padding: 16px 20px;
        }

        .staff-report-modal .modal-body {
            padding: 20px 22px;
            background: #f8fafc;
        }

        .staff-report-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 20px;
            margin-bottom: 14px;
        }

        .staff-report-field-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }

        .staff-report-field-value {
            font-size: 14px;
            color: #374151;
            margin: 0;
            word-break: break-word;
        }

        .staff-report-section {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 12px;
        }

        .staff-report-section:last-child {
            margin-bottom: 0;
        }

        .staff-report-description {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 14px;
            line-height: 1.55;
            color: #374151;
            white-space: pre-wrap;
            max-height: 200px;
            overflow-y: auto;
        }

        .staff-report-attachments {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .staff-report-attachments li {
            margin-bottom: 6px;
            font-size: 13px;
            word-break: break-all;
        }

        .staff-report-attachments a {
            color: #4555C3;
            font-weight: 600;
            text-decoration: none;
        }

        .staff-report-attachments a:hover {
            text-decoration: underline;
        }

        .staff-report-admin-remarks {
            background: #e8f4fc;
            border: 1px solid #b8d9f0;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 14px;
            line-height: 1.55;
            color: #1e3a5f;
            white-space: pre-wrap;
            max-height: 280px;
            overflow-y: auto;
            margin-top: 8px;
        }

        .staff-report-modal .btn-close-report {
            background: #4555C3;
            border-color: #4555C3;
            color: #fff;
            border-radius: 8px;
            font-weight: 600;
        }

        .staff-report-modal .btn-close-report:hover {
            background: #3544a8;
            border-color: #3544a8;
            color: #fff;
        }

        @media (max-width: 768px) {
            .staff-report-detail-grid {
                grid-template-columns: 1fr;
            }

            .harassment-header {
                padding: 18px 16px;
                margin-top: 8px;
            }
            .stats-grid {
                gap: 12px;
            }
            .stat-card {
                padding: 14px 14px;
            }
            .harassment-title {
                font-size: 22px;
            }
            .harassment-subtitle {
                font-size: 14px;
            }
            .stat-number {
                font-size: 24px;
            }
            .custom-table thead th,
            .custom-table td {
                padding: 10px 8px;
            }
            .custom-table td {
                font-size: 13px;
                line-height: 1.45;
            }
            .empty-state {
                padding: 42px 16px;
            }
            .empty-state h3 {
                font-size: 20px;
            }
            .empty-state p {
                font-size: 14px;
            }
        }
    </style>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <div class="harassment-header">
        <div>
            <h1 class="harassment-title">Grievance Reporting Portal</h1>
            <p class="harassment-subtitle">Track your report status and view details.</p>
        </div>
        <a href="{{ route('staff-harassment-form-new') }}" class="btn-new-request">
            <i class="fa fa-plus"></i> New Report
        </a>
    </div>

    <div class="stats-grid">
        <div class="stat-card pending">
            <p class="stat-number">{{ $pendingCount }}</p>
            <p class="stat-label">Pending</p>
        </div>
        <div class="stat-card completed">
            <p class="stat-number">{{ $completedCount }}</p>
            <p class="stat-label">Completed</p>
        </div>
        <div class="stat-card revised">
            <p class="stat-number">{{ $revisedCount }}</p>
            <p class="stat-label">In Process</p>
        </div>
        <div class="stat-card">
            <p class="stat-number">{{ $totalRequests }}</p>
            <p class="stat-label">Total Reports</p>
        </div>
    </div>

    @if($totalRequests > 0)
        <div class="table-card">
            <div class="table-responsive">
                <table id="harassmentRequestsTable" class="table custom-table mb-0">
                    <thead>
                        <tr>
                            <th>Report id</th>
                            <th>Category</th>
                            <th>Subject</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Submitted At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            @php $status = strtolower($request->status ?? ''); @endphp
                            <tr>
                                <td>#{{ $request->id }}</td>
                                <td>{{ $request->request_type ? ucfirst($request->request_type) : 'N/A' }}</td>
                                <td>{{ $request->problem ?? 'N/A' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($request->description ?? 'N/A', 60) }}</td>
                                <td>
                                    @if($status === 'pending')
                                        <span class="status-badge status-pending">Pending</span>
                                    @elseif($status === 'completed')
                                        <span class="status-badge status-completed">Completed</span>
                                    @elseif($status === 'revised')
                                        <span class="status-badge status-revised">Further Action Needed</span>
                                    @else
                                        <span class="status-badge status-default">{{ $request->status ?? 'N/A' }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($request->created_at)->format('d M Y h:i A') }}</td>
                                <td>
                                    <button type="button" class="btn-view" data-toggle="modal" data-target="#requestModal{{ $request->id }}">
                                        View
                                    </button>
                                    
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="empty-state">
            <i class="fa fa-inbox"></i>
            <h3>No Reports Yet</h3>
            <p>You have not submitted any complaints yet.</p>
            <a href="{{ route('staff-harassment-form-new') }}" class="btn-new-request">
                Create First Report
            </a>
        </div>
    @endif

    @foreach($requests as $request)
        @php
            $modalStatus = strtolower($request->status ?? 'pending');
            $rawFiles = trim($request->file_name ?? ($request->FILE_NAME ?? ''));
            $attachmentNames = [];
            if ($rawFiles !== '') {
                if ($rawFiles[0] === '[') {
                    $decoded = json_decode($rawFiles, true);
                    if (is_array($decoded)) {
                        $attachmentNames = $decoded;
                    }
                } elseif (strpos($rawFiles, '|') !== false) {
                    $attachmentNames = explode('|', $rawFiles);
                } else {
                    $attachmentNames = [preg_replace('#^/?assets/docs/#i', '', ltrim(str_replace('\\', '/', $rawFiles), '/'))];
                }
            }
        @endphp
        <div class="modal fade staff-report-modal" id="requestModal{{ $request->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-file-text-o"></i> Report Details</h5>
                        <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="staff-report-section">
                            <div class="staff-report-detail-grid">
                                <div>
                                    <span class="staff-report-field-label">Subject:</span>
                                    <p class="staff-report-field-value">{{ $request->problem ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="staff-report-field-label">Status:</span>
                                    <p class="staff-report-field-value">
                                        @if($modalStatus === 'completed')
                                            <span class="status-badge status-completed">Completed</span>
                                        @elseif($modalStatus === 'revised')
                                            <span class="status-badge status-revised">Further Action Needed</span>
                                        @else
                                            <span class="status-badge status-pending">Pending</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <span class="staff-report-field-label">Submitted At:</span>
                                    <p class="staff-report-field-value">{{ isset($request->created_at) ? \Carbon\Carbon::parse($request->created_at)->format('d M Y, h:i A') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="staff-report-field-label">Updated At:</span>
                                    <p class="staff-report-field-value">{{ isset($request->updated_at) ? \Carbon\Carbon::parse($request->updated_at)->format('d M Y, h:i A') : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="staff-report-section">
                            <span class="staff-report-field-label">Description:</span>
                            <div class="staff-report-description">{{ $request->description ?? 'N/A' }}</div>
                        </div>

                        <div class="staff-report-section">
                            <span class="staff-report-field-label">Attachments:</span>
                            @if(count($attachmentNames) > 0)
                                <ul class="staff-report-attachments mt-2">
                                    @foreach($attachmentNames as $attachmentName)
                                        @php $attachmentName = preg_replace('#^/?assets/docs/#i', '', ltrim(str_replace('\\', '/', trim($attachmentName)), '/')); @endphp
                                        <li>
                                            @if($attachmentName && file_exists(public_path('assets/docs/' . $attachmentName)))
                                                <a href="{{ asset('docs/' . $attachmentName) }}" target="_blank" rel="noopener noreferrer" title="{{ $attachmentName }}">
                                                    {{ $attachmentName }}
                                                </a>
                                            @else
                                                {{ $attachmentName ?: 'Unavailable' }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="staff-report-field-value mt-2 mb-0 text-muted">No files attached</p>
                            @endif
                        </div>

                        @if(!empty($request->admin_remarks_history) && count($request->admin_remarks_history) > 0)
                            <div class="staff-report-section">
                                <span class="staff-report-field-label">Admin Remarks:</span>
                                @include('components.grievance-admin-remarks-history', [
                                    'history' => $request->admin_remarks_history,
                                ])                    
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-close-report" data-dismiss="modal">Close</button>
                        @if($modalStatus === 'revised')
                            <a href="{{ route('staff-harassment-form-new', ['id' => $request->id]) }}" class="btn btn-primary">
                                <i class="fa fa-edit"></i> Edit Your Application
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        jQuery(function($) {
            @if(session('success'))
                if (typeof showToast === 'function') showToast('success', @json(session('success')));
            @endif
            @if(session('error'))
                if (typeof showToast === 'function') showToast('error', @json(session('error')));
            @endif

            if ($.fn.DataTable && $('#harassmentRequestsTable').length) {
                $('#harassmentRequestsTable').DataTable({
                    order: [[4, 'desc']],
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    columnDefs: [{ orderable: false, targets: 5 }],
                    language: {
                        search: 'Search:',
                        lengthMenu: 'Show _MENU_ entries',
                        info: 'Showing _START_ to _END_ of _TOTAL_ reports',
                        infoEmpty: 'No reports available'
                    }
                });
            }
        });
    </script>
@endsection
