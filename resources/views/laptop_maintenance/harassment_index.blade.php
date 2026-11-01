<?php
    $file_name = "app";
    if (auth()->user()->cms_sidebar == 'new') {
        $file_name = "updated_app";
    }
?>
@extends('layouts.' . $file_name)

@section('title')
    Staff Harassment Requests
@stop

@section('content')
@include('components.toast-notifications')

<style>
    .harassment-card-header {
        border-bottom: 2px solid #4555C3;
        background: #fff;
    }
    .harassment-card-header h3 {
        margin: 0;
        font-weight: 600;
    }
    #harassmentTable thead th {
        background: #4555C3;
        color: #fff;
        border-color: #4555C3;
    }
    .status-badge {
        font-size: 12px;
        padding: 6px 10px;
        border-radius: 20px;
        display: inline-block;
        font-weight: 600;
    }
    .status-pending {
        background: #ecb4032b;
        color: #a54f26;
    }
     .status-revised {
        background: #de690c38;
        color: #de690c;
    }
    .status-completed {
        background: #d4edda;
        color: #1c6b2f;
    }
    .desc-limit {
        max-width: 250px;        /* adjust width */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .remarks-char-count {
        display: block;
        margin-top: 6px;
        font-size: 12px;
    }
    .remarks-char-count.is-limit {
        color: #C34545;
        font-weight: 600;
    }
    .harassment-remarks-field.is-invalid {
        border-color: #C34545;
    }

    .report-view-modal .modal-content {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(17, 24, 39, 0.18);
    }

    .report-view-modal .modal-header {
        background: linear-gradient(135deg, #4555C3 0%, #3544a8 100%);
        color: #fff;
        border-bottom: none;
        padding: 16px 20px;
    }

    .report-view-modal .modal-title {
        font-weight: 600;
        font-size: 1.1rem;
    }

    .report-view-modal .modal-body {
        padding: 20px 22px;
        background: #f8fafc;
    }

    .report-view-modal .modal-footer {
        background: #fff;
        border-top: 1px solid #e5e7eb;
        padding: 12px 20px;
    }

    .report-detail-grid {
        display: grid;
        grid-template-columns: 140px 1fr;
        gap: 10px 16px;
        align-items: start;
    }

    .report-detail-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6b7280;
        margin: 0;
        padding-top: 2px;
    }

    .report-detail-value {
        font-size: 14px;
        color: #1f2937;
        margin: 0;
        word-break: break-word;
    }

    .report-detail-block {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 14px;
    }

    .report-detail-block:last-child {
        margin-bottom: 0;
    }

    .report-description-box {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 14px;
        line-height: 1.55;
        color: #374151;
        white-space: pre-wrap;
        max-height: 220px;
        overflow-y: auto;
    }

    .report-attachment-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .report-attachment-item {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 12px;
        transition: border-color 0.15s, background 0.15s;
    }

    .report-attachment-item:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .report-attachment-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #eef2ff;
        color: #4555C3;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 15px;
    }

    .report-attachment-link {
        color: #4555C3;
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        line-height: 1.35;
    }

    .report-attachment-link:hover {
        color: #3544a8;
        text-decoration: underline;
    }

    .report-attachment-name {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 100%;
    }

    .report-attachment-meta {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 2px;
    }

    .report-attachment-preview {
        margin-top: 10px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
    }

    .report-attachment-preview iframe {
        display: block;
        width: 100%;
        height: 320px;
        border: 0;
        background: #f9fafb;
    }

    .report-attachment-preview video {
        display: block;
        max-width: 100%;
        max-height: 280px;
        width: 100%;
        background: #000;
    }

    .report-attachment-item.has-preview {
        align-items: flex-start;
    }

    .report-no-files {
        color: #9ca3af;
        font-size: 13px;
        font-style: italic;
    }

    .report-view-modal .btn-close-modal {
        background: #4555C3;
        border-color: #4555C3;
        color: #fff;
        border-radius: 8px;
        padding: 8px 18px;
        font-weight: 600;
    }

    .report-view-modal .btn-close-modal:hover {
        background: #3544a8;
        border-color: #3544a8;
        color: #fff;
    }

    @media (max-width: 576px) {
        .report-detail-grid {
            grid-template-columns: 1fr;
            gap: 4px;
        }
    }
</style>

<div class="row" style="margin:10px;">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header harassment-card-header">
                <h3><i class="fa fa-exclamation-circle"></i> Grievance Management</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="harassmentTable" class="table table-striped table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Report Id</th>
                                <th>Category</th>
                                <th>Subject</th>
                                <th>Description</th>
                                <!-- <th>Submitted By</th> -->
                                <th>Status</th>
                                <th>Submitted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $index => $request)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $request->id}}</td>
                                    <td>{{ $request->request_type ?? '-' }}</td>
                                    <td>{{ $request->problem ?? '-' }}</td>
                                    <!-- <td>{{ \Illuminate\Support\Str::limit($request->description ?? '-', 100) }}</td> -->
                                    <td class="desc-limit" data-full="{{ $request->description }}" title="{{ $request->description }}">{{ $request->description ?? '-' }}</td>
                                    <!-- <td>{{ $request->submitted_by ?? '-' }}</td> -->
                                    <td>
                                        @if(strtolower($request->status ?? '') === 'completed')
                                            <span class="status-badge status-completed">Completed</span>
                                        @elseif(strtolower($request->status ?? '') === 'revised')
                                            <span class="status-badge status-revised">Revised</span>
                                        @else
                                            <span class="status-badge status-pending">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ isset($request->created_at) ? \Carbon\Carbon::parse($request->created_at)->format('d M Y, h:i A') : '-' }}</td>
                                    <td style="white-space: nowrap;">
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#viewModal{{ $request->id }}">
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                        @if(strtolower($request->status ?? '') !== 'completed')
                                            @if(strtolower($request->status ?? '') === 'revised')
                                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#closeRevisedModal{{ $request->id }}">
                                                    <i class="fa fa-check"></i> Mark Complete
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#completeModal{{ $request->id }}">
                                                    <i class="fa fa-check"></i> Take Action
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No harassment reports found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($requests as $request)
    @php
        $viewStatus = strtolower($request->status ?? 'pending');
        $rawFiles = trim($request->file_name ?? ($request->FILE_NAME ?? ''));
        $adminAttachments = [];
        if ($rawFiles !== '') {
            if ($rawFiles[0] === '[') {
                $decoded = json_decode($rawFiles, true);
                if (is_array($decoded)) {
                    $adminAttachments = $decoded;
                }
            } elseif (strpos($rawFiles, '|') !== false) {
                $adminAttachments = explode('|', $rawFiles);
            } else {
                $adminAttachments = [preg_replace('#^/?assets/docs/#i', '', ltrim(str_replace('\\', '/', $rawFiles), '/'))];
            }
        }
    @endphp
<div class="modal fade report-view-modal" id="viewModal{{ $request->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-file-text-o"></i> Report Details</h5>
                    <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="report-detail-block">
                        <div class="report-detail-grid">
                            <p class="report-detail-label">Report ID</p>
                            <p class="report-detail-value">#{{ $request->id }}</p>

                            <p class="report-detail-label">Category</p>
                            <p class="report-detail-value">{{ ucfirst($request->request_type ?? '-') }}</p>

                            <p class="report-detail-label">Subject</p>
                            <p class="report-detail-value">{{ $request->problem ?? '-' }}</p>

                            <p class="report-detail-label">Status</p>
                            <p class="report-detail-value">
                                @if($viewStatus === 'completed')
                                    <span class="status-badge status-completed">Completed</span>
                                @elseif($viewStatus === 'revised')
                                    <span class="status-badge status-revised">Revised</span>
                                @else
                                    <span class="status-badge status-pending">Pending</span>
                                @endif
                            </p>

                            <p class="report-detail-label">Submitted</p>
                            <p class="report-detail-value">{{ isset($request->created_at) ? \Carbon\Carbon::parse($request->created_at)->format('d M Y, h:i A') : '-' }}</p>
                        </div>
                    </div>

                    <div class="report-detail-block">
                        <p class="report-detail-label mb-2"><i class="fa fa-commenting-o"></i> Admin Remarks</p>
                        @include('components.grievance-admin-remarks-history', [
                            'history' => $request->admin_remarks_history ?? [],
                        ])
                    </div>

                    <div class="report-detail-block">
                        <p class="report-detail-label mb-2">Description</p>
                        <div class="report-description-box">{{ $request->description ?? '-' }}</div>
                    </div>

                    <div class="report-detail-block">
                        <p class="report-detail-label mb-2">Attachments</p>
                        @if(count($adminAttachments) > 0)
                            <ul class="report-attachment-list">
                                @foreach($adminAttachments as $adminAttachment)
                                    @php
                                        $adminAttachment = preg_replace('#^/?assets/docs/#i', '', ltrim(str_replace('\\', '/', trim($adminAttachment)), '/'));
                                        $attachmentExt = strtolower(pathinfo($adminAttachment, PATHINFO_EXTENSION));
                                        $attachmentIcon = 'fa-file-o';
                                        if (in_array($attachmentExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                                            $attachmentIcon = 'fa-file-image-o';
                                        } elseif ($attachmentExt === 'pdf') {
                                            $attachmentIcon = 'fa-file-pdf-o';
                                        } elseif (in_array($attachmentExt, ['doc', 'docx'], true)) {
                                            $attachmentIcon = 'fa-file-word-o';
                                        } elseif (in_array($attachmentExt, ['xls', 'xlsx', 'csv'], true)) {
                                            $attachmentIcon = 'fa-file-excel-o';
                                        } elseif (in_array($attachmentExt, ['mp4', 'mov'], true)) {
                                            $attachmentIcon = 'fa-file-video-o';
                                        }
                                        $isImageAttachment = in_array($attachmentExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
                                        $canPreviewPdf = $attachmentExt === 'pdf';
                                        $canPreviewVideo = in_array($attachmentExt, ['mp4', 'mov'], true);
                                        $hasInlinePreview = $canPreviewPdf || $canPreviewVideo;
                                        $attachmentExists = $adminAttachment && file_exists(public_path('assets/docs/' . $adminAttachment));
                                        $attachmentViewUrl = $attachmentExists ? asset('docs/' . $adminAttachment) : '';
                                        $attachmentDownloadUrl = $attachmentExists
                                            ? route('admin.file.download', ['id' => $request->id, 'file' => $adminAttachment])
                                            : '';
                                        $attachmentLinkUrl = $isImageAttachment ? $attachmentViewUrl : $attachmentDownloadUrl;
                                    @endphp
                                    <li class="report-attachment-item{{ $hasInlinePreview && $attachmentExists ? ' has-preview' : '' }}">
                                        <span class="report-attachment-icon"><i class="fa {{ $attachmentIcon }}"></i></span>
                                        <div class="flex-grow-1" style="min-width:0;">
                                            @if($attachmentExists)
                                                <a href="{{ $attachmentLinkUrl }}"
                                                   class="report-attachment-link"
                                                   target="_blank"
                                                   rel="noopener noreferrer"
                                                   title="{{ $adminAttachment }}">
                                                    <span class="report-attachment-name">{{ $adminAttachment }}</span>
                                                    <span class="report-attachment-meta">
                                                        @if($isImageAttachment)
                                                            Click to view image in a new tab
                                                        @elseif($hasInlinePreview)
                                                            Download file · preview below
                                                        @else
                                                            Click to download (preview not available for this file type)
                                                        @endif
                                                    </span>
                                                </a>
                                                @if($canPreviewPdf)
                                                    <div class="report-attachment-preview">
                                                        <iframe src="{{ $attachmentViewUrl }}#toolbar=0"
                                                                title="PDF preview of {{ $adminAttachment }}"></iframe>
                                                    </div>
                                                @elseif($canPreviewVideo)
                                                    <div class="report-attachment-preview">
                                                        <video controls preload="metadata"
                                                               src="{{ $attachmentViewUrl }}">
                                                            Your browser does not support video preview.
                                                        </video>
                                                    </div>
                                                @endif
                                            @else
                                                <span class="report-detail-value">{{ $adminAttachment ?: 'Unavailable' }}</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="report-no-files">No files attached</span>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    @if($viewStatus !== 'completed')
                        @if($viewStatus === 'revised')
                            <button type="button" class="btn btn-success" data-dismiss="modal" data-toggle="modal" data-target="#closeRevisedModal{{ $request->id }}">
                                <i class="fa fa-check"></i> Mark Complete
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" data-toggle="modal" data-target="#completeModal{{ $request->id }}">
                                <i class="fa fa-check"></i> Take Action
                            </button>
                        @endif
                    @endif
                    <button type="button" class="btn btn-close-modal" data-dismiss="modal">
                        <i class="fa fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>
</div>

    @if($viewStatus === 'revised')
        <div class="modal fade" id="closeRevisedModal{{ $request->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST"
                          action="{{ route('staff-harassment.mark-complete') }}"
                          class="harassment-action-form harassment-close-revised-form"
                          novalidate>
                        @csrf
                        <input type="hidden" name="request_id" value="{{ $request->id }}">
                        <input type="hidden" name="action_context" value="close_revised">
                        <input type="hidden" name="status_action" value="completed">
                        <div class="modal-header" style="background:#4555C3;color:#fff;">
                            <h5 class="modal-title"><i class="fa fa-check-circle"></i> Mark Complete</h5>
                            <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted small mb-3">The user has not responded to the revision request. Add closing remarks and mark this report as completed.</p>
                            <div class="form-group mb-0">
                                <label for="closeRemarks{{ $request->id }}">Admin Remarks <span style="color:#C34545;">*</span></label>
                                <textarea class="form-control harassment-remarks-field"
                                          name="remarks"
                                          id="closeRemarks{{ $request->id }}"
                                          rows="4"
                                          required
                                          placeholder="Enter closing remarks (max 3900 characters)..."
                                          maxlength="3900"
                                          data-max-chars="3900">{{ old('request_id') == $request->id && old('action_context') === 'close_revised' ? old('remarks') : '' }}</textarea>
                                <small class="remarks-char-count text-muted" id="closeRemarksCount{{ $request->id }}">0 / 3900 characters</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-check"></i> Mark Complete
                            </button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @elseif($viewStatus !== 'completed')
        <div class="modal fade" id="completeModal{{ $request->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST"
                          action="{{ route('staff-harassment.mark-complete') }}"
                          class="harassment-action-form"
                          novalidate>
                        @csrf
                        <input type="hidden" name="request_id" value="{{ $request->id }}">
                        <div class="modal-header" style="background:#4555C3;color:#fff;">
                            <h5 class="modal-title"><i class="fa fa-check-circle"></i> Take Action</h5>
                            <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="remarks{{ $request->id }}">Admin Remarks <span style="color:#C34545;">*</span></label>
                                <textarea class="form-control harassment-remarks-field"
                                          name="remarks"
                                          id="remarks{{ $request->id }}"
                                          rows="4"
                                          required
                                          placeholder="Enter completion remarks (max 3900 characters)..."
                                          maxlength="3900"
                                          data-max-chars="3900">{{ old('request_id') == $request->id && old('action_context') !== 'close_revised' ? old('remarks') : '' }}</textarea>
                                <small class="remarks-char-count text-muted" id="remarksCount{{ $request->id }}">0 / 3900 characters</small>
                            </div>
                            <div class="form-group">
                                <label>Status Action <span style="color:#C34545;">*</span></label><br>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status_action" id="complete{{ $request->id }}" value="completed" required
                                        {{ old('request_id') == $request->id && old('status_action') === 'completed' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="complete{{ $request->id }}">
                                        Mark as Complete
                                    </label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status_action" id="revised{{ $request->id }}" value="revised" required
                                        {{ old('request_id') == $request->id && old('status_action') === 'revised' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="revised{{ $request->id }}">
                                        Mark as Revised
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-check"></i> Submit
                            </button>
                            <!-- <button type="button" class="btn btn-danger" data-dismiss="modal">
                                <i class="fa fa-check"></i> Mark as Revised
                            </button> -->
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

@section('javascript')
<script type="text/javascript">
    jQuery(function($) {
        var REMARKS_MAX_CHARS = 3900;

        function updateRemarksCharCount($textarea) {
            var len = ($textarea.val() || '').length;
            var max = parseInt($textarea.data('max-chars'), 10) || REMARKS_MAX_CHARS;
            var $counter = $textarea.siblings('.remarks-char-count');

            $counter.text(len + ' / ' + max + ' characters');
            $counter.toggleClass('is-limit', len >= max);
            $textarea.toggleClass('is-invalid', len > max);
        }

        function initRemarksFields($scope) {
            $scope.find('.harassment-remarks-field').each(function() {
                updateRemarksCharCount($(this));
            });
        }

        $(document).on('input', '.harassment-remarks-field', function() {
            var $textarea = $(this);
            var max = parseInt($textarea.data('max-chars'), 10) || REMARKS_MAX_CHARS;
            var value = $textarea.val() || '';

            if (value.length > max) {
                $textarea.val(value.substring(0, max));
            }

            updateRemarksCharCount($textarea);
        });

        $(document).on('shown.bs.modal', '.modal', function() {
            initRemarksFields($(this));
        });

        $(document).on('submit', '.harassment-action-form', function(e) {
            var $form = $(this);
            var $remarks = $form.find('.harassment-remarks-field');
            var max = parseInt($remarks.data('max-chars'), 10) || REMARKS_MAX_CHARS;
            var remarks = $.trim($remarks.val() || '');
            var isCloseRevisedForm = $form.hasClass('harassment-close-revised-form');
            var statusSelected = isCloseRevisedForm
                || $form.find('input[name="status_action"]:checked').length > 0;

            if (!remarks) {
                e.preventDefault();
                $remarks.addClass('is-invalid').focus();
                if (typeof showToast === 'function') {
                    showToast('error', 'Admin remarks are required.');
                }
                return false;
            }

            if (remarks.length > max) {
                e.preventDefault();
                $remarks.addClass('is-invalid').focus();
                if (typeof showToast === 'function') {
                    showToast('error', 'Remarks cannot exceed ' + max + ' characters.');
                }
                return false;
            }

            if (!statusSelected) {
                e.preventDefault();
                if (typeof showToast === 'function') {
                    showToast('error', 'Please select a status action (Complete or Revised).');
                }
                return false;
            }

            $remarks.removeClass('is-invalid');
            $form.find('button[type="submit"]').prop('disabled', true);
        });

        initRemarksFields($(document));

        @if(session('success'))
            if (typeof showToast === 'function') {
                showToast('success', @json(session('success')));
            }
        @endif

        @if(session('error'))
            if (typeof showToast === 'function') {
                showToast('error', @json(session('error')));
            }
        @endif

        @if($errors->any())
            if (typeof showToast === 'function') {
                showToast('error', @json($errors->first()));
            }
            @if(old('request_id'))
                @if(old('action_context') === 'close_revised')
                    $('#closeRevisedModal{{ old('request_id') }}').modal('show');
                @else
                    $('#completeModal{{ old('request_id') }}').modal('show');
                @endif
            @endif
        @endif

        var $table = $('#harassmentTable');
        if (!$table.length) {
            return;
        }

        if ($.fn.DataTable) {
            if ($.fn.DataTable.isDataTable('#harassmentTable')) {
                $table.DataTable().destroy();
            }
          $table.DataTable({
    pageLength: 10,
    order: [[0, 'asc']],
    responsive: true,
    autoWidth: false,
    dom: 'Bfrtip',

    buttons: [
        {
            extend: 'copy',
            text: 'Copy',
            exportOptions: {
                columns: [0,1,2,3,4,5,6], // ❌ exclude Actions column (index 7)
                format: {
                    body: function (data, row, column, node) {

                        // ✅ Description full text
                        if (column === 4) {
                            return $(node).closest('td').data('full') || data;

                        }

                        // ✅ Remove HTML (status, buttons, etc.)
                        return $('<div>').html(data).text();
                    }
                }
            }
        },
        {
            extend: 'excel',
            text: 'Excel',
            exportOptions: {
                columns: [0,1,2,3,4,5,6],
                format: {
                    body: function (data, row, column, node) {

                        if (column === 4) {
                            return $(node).closest('td').data('full') || data;
                        }

                        return $('<div>').html(data).text();
                    }
                }
            }
        },
        {
            extend: 'print',
            text: 'Print',
            exportOptions: {
                columns: [0,1,2,3,4,5,6],
                format: {
                    body: function (data, row, column, node) {

                        if (column === 4) {
                            return $(node).closest('td').data('full') || data;
                        }

                        return $('<div>').html(data).text();
                    }
                }
            }
        }
    ]
});
        }
    });
</script>
@endsection
