@extends('layouts.app')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $isResubmit = !empty($data['id']);
    $existingFiles = $data['file_names'] ?? [];
    $maxFiles = 10;
@endphp
<style>
    /* Figma-aligned visual system — styling only, form structure/logic unchanged */
    .page-wrap {
        margin: 28px auto 40px;
        padding: 0 20px;
        font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
        color: #252525;
    }

    .hero-strip {
        position: relative;
        background: transparent;
        color: #252525;
        border-radius: 0;
        padding: 0 0 18px;
        margin-bottom: 18px;
        box-shadow: none;
        border: 0;
    }

    .hero-strip h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        line-height: 1.35;
        letter-spacing: -0.2px;
    }

    .hero-strip p {
        margin: 6px 0 0;
        color: #5f6368;
        opacity: 1;
        font-size: 14px;
        line-height: 1.5;
    }

    .form-container {
        background: #ffffff;
        border: 1px solid rgba(209, 209, 209, 0.5);
        border-radius: 16px;
        padding: 32px;
        max-width: 100%;
        margin: 0 auto;
    }

    .form-header {
        margin-bottom: 24px;
        border-bottom: 1px solid #edf0f2;
        padding-bottom: 18px;
    }

    .form-header h2 {
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        font-size: 18px;
        line-height: 1.4;
    }

    .form-subtitle {
        margin: 6px 0 0;
        color: #94a3b8;
        font-size: 13px;
        line-height: 1.5;
    }

    .form-group-box {
        background: #ffffff;
        border: 1px solid #e8ecef;
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 16px;
    }

    .form-group-box:focus-within {
        border-color: rgba(23, 103, 95, 0.45);
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #252525;
        margin-bottom: 9px;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        min-height: 48px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 11px 14px;
        font-size: 14px;
        color: #252525;
        background-color: #ffffff;
        box-shadow: none;
    }

    textarea.form-control {
        min-height: 150px;
        resize: vertical;
        line-height: 1.6;
    }

    .form-control::placeholder {
        color: #a8b0ba;
    }

    .form-control:focus {
        border-color: #17675f;
        outline: 0;
    }

    .form-control.is-invalid {
        border-color: #e11d48;
    }

    .btn-primary {
        background-color: #17675f;
        border-color: #17675f;
        border-radius: 10px;
        padding: 12px 24px;
        min-height: 46px;
        font-weight: 600;
        font-size: 14px;
        min-width: 180px;
        box-shadow: none;
    }

    .btn-primary:hover,
    .btn-primary:focus,
    .btn-primary:active {
        background-color: #12554e !important;
        border-color: #12554e !important;
    }

    .btn-primary:active {
        transform: translateY(1px);
    }

    .btn-outline-secondary {
        color: #475569;
        border-color: #d7dde2;
        background: #ffffff;
        border-radius: 10px;
        padding: 11px 20px;
        min-height: 46px;
        font-size: 14px;
        font-weight: 600;
    }

    .btn-outline-secondary:hover,
    .btn-outline-secondary:focus {
        color: #17675f;
        border-color: #17675f;
        background: rgba(23, 103, 95, 0.04);
        box-shadow: none;
    }

    .btn-outline-primary {
        color: #17675f;
        border-color: #17675f;
        border-radius: 8px;
    }

    .btn-outline-primary:hover,
    .btn-outline-primary:focus {
        color: #ffffff;
        background-color: #17675f;
        border-color: #17675f;
    }

    #formFeedback {
        border: 0;
        border-radius: 10px;
        padding: 13px 16px;
        font-size: 13px;
    }

    #formFeedback.alert-success {
        border-left: 4px solid #10b981;
        background-color: #ecfdf5;
        color: #065f46;
    }

    #formFeedback.alert-danger {
        border-left: 4px solid #e11d48;
        background-color: #fff1f2;
        color: #9f1239;
    }

    .submit-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        border-top: 1px solid #edf0f2;
        padding-top: 20px;
        margin-top: 4px;
    }

    .char-count {
        font-size: 11px;
        color: #94a3b8;
        text-align: right;
        display: block;
        margin-top: 7px;
    }

    .char-count.is-limit {
        color: #e11d48;
        font-weight: 600;
    }

    .form-label .label-hint {
        font-weight: 400;
        font-size: 12px;
        color: #94a3b8;
        line-height: 1.5;
        margin-top: 5px;
    }

    .evidence-file-input {
        border: 1px dashed rgba(23, 103, 95, 0.55);
        border-radius: 10px;
        padding: 10px 12px;
        background: rgba(23, 103, 95, 0.025);
    }

    .evidence-file-input:hover {
        border-color: #17675f;
        background: rgba(23, 103, 95, 0.045);
    }

    .evidence-help {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 8px;
        line-height: 1.5;
    }

    .current-evidence {
        background: #f7fbfa !important;
        border: 1px solid rgba(23, 103, 95, 0.14) !important;
        border-radius: 12px !important;
    }

    .evidence-item {
        border: 1px solid #e8ecef !important;
        border-radius: 10px !important;
    }

    .form-check-input:checked {
        background-color: #17675f;
        border-color: #17675f;
    }

    .text-danger {
        color: #e11d48 !important;
    }

    .text-warning {
        color: #d97706 !important;
    }

    @media (max-width: 992px) {
        .page-wrap {
            padding: 0 16px;
        }

        .form-container {
            padding: 26px;
        }
    }

    @media (max-width: 768px) {
        .page-wrap {
            margin: 18px auto 28px;
            padding: 0 12px;
        }

        .hero-strip {
            padding-bottom: 14px;
            margin-bottom: 14px;
        }

        .hero-strip h2 {
            font-size: 18px;
        }

        .form-container {
            padding: 18px;
            border-radius: 12px;
        }

        .form-group-box {
            padding: 15px;
        }

        .submit-section {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .submit-section .btn-primary,
        .submit-section .btn-outline-secondary {
            width: 100%;
        }
    }
</style>
@section('content')
@include('components.toast-notifications')

<div class="container-fluid page-wrap">
    <div class="hero-strip">
        <h2>{{ $isResubmit ? 'Resubmit Grievance Report' : 'Grievance Report' }}</h2>
        <p>{{ $isResubmit ? 'Update your report based on admin remarks, then resubmit for review.' : 'Share your concern with details and optional evidence.' }}</p>
    </div>
    <div class="form-container">
        <div class="form-header">
            <h2>{{ $isResubmit ? 'Edit & Resubmit' : 'Create Grievance Report' }}</h2>
            <p class="form-subtitle">Fields marked with * are required. Description max 3000 characters.</p>
        </div>

         @if($isResubmit && !empty($data['admin_remarks_history']) && count($data['admin_remarks_history']) > 0)
            <div class="mb-4 p-3" style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:10px;">
                <p class="mb-2" style="font-size:13px;font-weight:700;color:#374151;">Admin remarks history</p>
                @include('components.grievance-admin-remarks-history', [
                    'history' => $data['admin_remarks_history'],
                ])
            </div>
        @endif

        <div id="formFeedback" class="alert d-none mb-3" role="alert" aria-live="polite"></div>

        <form id="staffHarassmentForm" action="{{ route('staff.harassment.submit') }}" enctype="multipart/form-data" method="post" novalidate>
            @csrf
            <input type="hidden" name="id" value="{{ $data['id'] ?? '' }}">

            <div class="form-group-box">
                <label class="form-label">Category <span class="text-danger">*</span></label>
                <select name="category" class="form-control" required>
                    <option value="">Select Category</option>
                    <option value="harassment" {{ (isset($data['request_type']) && $data['request_type'] == 'harassment') ? 'selected' : '' }}>Harassment</option>
                    <option value="grievance" {{ (isset($data['request_type']) && $data['request_type'] == 'grievance') ? 'selected' : '' }}>Other Grievance Report</option>
                </select>
            </div>

            <div class="form-group-box">
                <label class="form-label">Subject <span class="text-danger">*</span></label>
                <input type="text" name="subject" class="form-control" maxlength="255"
                       value="{{ $data['problem'] ?? '' }}" placeholder="Enter subject" required>
            </div>

            <div class="form-group-box">
                <label class="form-label">
                    Description <span class="text-danger">*</span>
                    <span class="label-hint d-block">(Please provide complete details of the incident, including the date, time, location, and individuals involved)</span>
                </label>
                <textarea name="description" id="description" class="form-control" rows="5"
                          maxlength="3000" data-max-chars="3000" placeholder="Enter description" required>{{ $data['description'] ?? '' }}</textarea>
                <small id="charCount" class="char-count">0 / 3000</small>
            </div>

            <div class="form-group-box">
                <label class="form-label">Evidence</label>

                @if($isResubmit && count($existingFiles) > 0)
                    <div class="current-evidence mb-3 p-3" style="background:#f0f4ff;border:1px solid #dbe3ff;border-radius:8px;">
                        <p class="mb-2" style="font-size:13px;"><strong>Current evidence ({{ count($existingFiles) }}):</strong></p>
                        @foreach($existingFiles as $existingFile)
                            @php
                                $existingPath = public_path('assets/docs/' . $existingFile);
                                $existingExists = file_exists($existingPath);
                                $existingUrl = $existingExists ? asset('docs/' . $existingFile) : null;
                                $existingExt = strtolower(pathinfo($existingFile, PATHINFO_EXTENSION));
                                $existingIsImage = in_array($existingExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
                            @endphp
                            <div class="evidence-item mb-3 p-2" style="background:#fff;border:1px solid #e5e7eb;border-radius:8px;" data-existing-file="{{ $existingFile }}">
                                <p class="mb-1 text-muted" style="font-size:12px;word-break:break-all;">{{ $existingFile }}</p>
                                @if($existingExists)
                                    @if($existingIsImage)
                                        <a href="{{ $existingUrl }}" target="_blank" rel="noopener noreferrer" class="d-inline-block mb-2">
                                            <img src="{{ $existingUrl }}" alt="Evidence"
                                                 style="max-width:100%;max-height:160px;border-radius:6px;border:1px solid #d1d5db;">
                                        </a>
                                    @else
                                        <a href="{{ $existingUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary mb-2">View / Download</a>
                                    @endif
                                @else
                                    <span class="text-warning d-block mb-2" style="font-size:12px;">File not found on server</span>
                                @endif
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input remove-single-file" name="remove_files[]" value="{{ $existingFile }}" id="remove_{{ md5($existingFile) }}">
                                    <label class="form-check-label" for="remove_{{ md5($existingFile) }}" style="font-size:12px;color:#C34545;">Remove this file</label>
                                </div>
                            </div>
                        @endforeach
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" name="remove_all_files" id="removeAllFiles" value="1">
                            <label class="form-check-label" for="removeAllFiles" style="font-size:13px;color:#C34545;">Remove all current evidence</label>
                        </div>
                    </div>
                @endif

                <label class="form-label mb-1 evidence-upload-label" style="font-size:13px;">{{ ($isResubmit && count($existingFiles) > 0) ? 'Add more evidence (optional)' : 'Upload evidence (optional)' }}</label>
                <input type="file" name="file[]" id="evidenceFile" class="form-control evidence-file-input" multiple
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.webp,.mp4,.mov,.csv">
                <p class="evidence-help mb-0">Optional supporting files (PDF, Word, Excel, images, video). Maximum {{ $maxFiles }} files or accumulative size of 30 MB</p>
                <small id="newFilesCount" class="char-count">0 new file(s) selected (max {{ $maxFiles }} total)</small>
                <div id="newFilesPreview" class="mt-2"></div>
            </div>

            <div class="submit-section">
                <a href="{{ route('staff-harassment-form') }}" class="btn btn-outline-secondary">Back to My Reports</a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span id="btnText">{{ $isResubmit ? 'Resubmit Report' : 'Submit Report' }}</span>
                    <span id="btnSpinner" style="display: none;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span style="margin-left: 8px;">Submitting...</span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('javascript')
<script>
    jQuery(function($) {
        var DESC_MAX = 3000;
        var $textarea = $('#description');
        var $counter = $('#charCount');

        function updateDescCount() {
            var len = ($textarea.val() || '').length;
            $counter.text(len + ' / ' + DESC_MAX);
            $counter.toggleClass('is-limit', len >= DESC_MAX);
            $textarea.toggleClass('is-invalid', len > DESC_MAX);
        }

        $textarea.on('input', function() {
            var val = $textarea.val() || '';
            if (val.length > DESC_MAX) {
                $textarea.val(val.substring(0, DESC_MAX));
            }
            updateDescCount();
        });
        updateDescCount();

        function showFormFeedback(type, message) {
            var $fb = $('#formFeedback');
            $fb.removeClass('alert-success alert-danger d-none')
                .addClass(type === 'success' ? 'alert-success' : 'alert-danger')
                .text(message || '')
                .removeClass('d-none');
        }

        function hideFormFeedback() {
            $('#formFeedback').addClass('d-none').removeClass('alert-success alert-danger').text('');
        }

        var MAX_FILES = {{ $maxFiles }};

        $('#staffHarassmentForm').on('input change', 'input, textarea, select', hideFormFeedback);

        function countKeptExistingFiles() {
            var total = $('.evidence-item').length;
            var removed = $('.remove-single-file:checked').length;
            if ($('#removeAllFiles').is(':checked')) {
                return 0;
            }
            return Math.max(0, total - removed);
        }

        function toggleRemoveAllEvidence() {
            if ($('#removeAllFiles').is(':checked')) {
                $('.remove-single-file').prop('checked', true).prop('disabled', true);
                $('.evidence-item').css('opacity', '0.45');
            } else {
                $('.remove-single-file').prop('disabled', false);
                $('.evidence-item').css('opacity', '1');
            }
            updateNewFilesPreview();
        }

        $(document).on('change', '#removeAllFiles', toggleRemoveAllEvidence);
        $(document).on('change', '.remove-single-file', function() {
            if ($('#removeAllFiles').is(':checked')) {
                return;
            }
            updateNewFilesPreview();
        });

        function updateNewFilesPreview() {
            var input = document.getElementById('evidenceFile');
            var $preview = $('#newFilesPreview');
            var $count = $('#newFilesCount');
            $preview.empty();

            if (!input || !input.files) {
                $count.text('0 new file(s) selected (max ' + MAX_FILES + ' total)');
                return;
            }

            var kept = countKeptExistingFiles();
            var newCount = input.files.length;
            var total = kept + newCount;

            $count.text(newCount + ' new file(s) selected — total after submit: ' + total + ' / ' + MAX_FILES);
            $count.toggleClass('is-limit', total > MAX_FILES);

            for (var i = 0; i < input.files.length; i++) {
                $preview.append('<div class="text-muted mb-1" style="font-size:12px;">+ ' + input.files[i].name + '</div>');
            }
        }

        $('#evidenceFile').on('change', updateNewFilesPreview);

        $('#staffHarassmentForm').on('submit', function(e) {
            e.preventDefault();
            hideFormFeedback();

            var $form = $(this);
            var $submitBtn = $('#submitBtn');
            var subject = $.trim($form.find('[name="subject"]').val() || '');
            var description = $.trim($textarea.val() || '');
            var category = $form.find('[name="category"]').val();

            if (!category) {
                showFormFeedback('error', 'Please select a category.');
                if (typeof showToast === 'function') showToast('error', 'Please select a category.');
                return false;
            }
            if (!subject) {
                showFormFeedback('error', 'Subject is required.');
                if (typeof showToast === 'function') showToast('error', 'Subject is required.');
                return false;
            }
            if (!description) {
                $textarea.addClass('is-invalid').focus();
                showFormFeedback('error', 'Description is required.');
                if (typeof showToast === 'function') showToast('error', 'Description is required.');
                return false;
            }
            if (description.length > DESC_MAX) {
                $textarea.addClass('is-invalid').focus();
                showFormFeedback('error', 'Description cannot exceed ' + DESC_MAX + ' characters.');
                if (typeof showToast === 'function') showToast('error', 'Description cannot exceed ' + DESC_MAX + ' characters.');
                return false;
            }

            var kept = countKeptExistingFiles();
            var newCount = $('#evidenceFile')[0].files ? $('#evidenceFile')[0].files.length : 0;
            if (kept + newCount > MAX_FILES) {
                showFormFeedback('error', 'Maximum ' + MAX_FILES + ' evidence files allowed per report.');
                if (typeof showToast === 'function') showToast('error', 'Maximum ' + MAX_FILES + ' files allowed.');
                return false;
            }

            $textarea.removeClass('is-invalid');
            $submitBtn.prop('disabled', true);
            $('#btnText').hide();
            $('#btnSpinner').show();

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    var msg = (response && response.message) ? response.message : 'Submitted successfully!';
                    showFormFeedback('success', msg);
                    if (typeof showToast === 'function') showToast('success', msg, '', 3000);
                    setTimeout(function() {
                        window.location.href = (response && response.redirect)
                            ? response.redirect
                            : '{{ route('staff-harassment-form') }}';
                    }, 700);
                },
                error: function(xhr) {
                    var msg = 'Error submitting report.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        if (xhr.responseJSON.errors) {
                            var errs = xhr.responseJSON.errors;
                            var first = Object.keys(errs)[0];
                            if (first && errs[first] && errs[first][0]) msg = errs[first][0];
                        }
                    }
                    showFormFeedback('error', msg);
                    if (typeof showToast === 'function') showToast('error', msg, '', 5000);
                    $submitBtn.prop('disabled', false);
                    $('#btnText').show();
                    $('#btnSpinner').hide();
                }
            });
        });

        @if(session('success'))
            if (typeof showToast === 'function') showToast('success', @json(session('success')));
        @endif
        @if(session('error'))
            if (typeof showToast === 'function') showToast('error', @json(session('error')));
        @endif
    });
</script>
@endsection
