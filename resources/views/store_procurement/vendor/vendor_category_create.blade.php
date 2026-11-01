@extends('layouts.app')

<link href="/css/bnu-layout.css" rel="stylesheet" />

<style>
    .action-menu {
        position: relative;
        display: inline-flex;
        justify-content: center;
    }

    .action-menu-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        padding: 0;
        border: none;
        border-radius: 50%;
        background-color: transparent;
        color: #4A47C7;
        font-size: 18px;
        cursor: pointer;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .action-menu-button:hover,
    .action-menu-button:focus {
        background-color: rgba(74, 71, 199, 0.12);
        color: #302fa3;
        outline: none;
    }

    .action-menu-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        min-width: 140px;
        margin-top: 6px;
        padding: 8px 0;
        border: none;
        border-radius: 10px;
        background-color: #ffffff;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        display: none;
        z-index: 5;
    }

    .action-menu-dropdown.show {
        display: block;
    }

    .action-menu-item {
        display: block;
        width: 100%;
        padding: 10px 16px;
        background: none;
        border: none;
        text-align: left;
        font-size: 14px;
        color: #252525;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .action-menu-item:hover {
        background-color: rgba(74, 71, 199, 0.08);
    }

    .action-menu-item.text-danger {
        color: #d9534f;
    }

    .action-menu-item.text-danger:hover {
        background-color: rgba(217, 83, 79, 0.12);
    }
   

    .content-card .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #17675F !important;
        border: none !important;
        color: #FFFFFF !important;
    }

    #categoryTable .select2-container {
        width: 100% !important;
        min-width: 220px;
    }

    #categoryTable .select2-container--default .select2-selection--multiple {
        min-height: 38px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 2px 4px;
    }

    #categoryTable .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #17675F !important;
        border: none !important;
        color: #FFFFFF !important;
        margin-top: 3px;
        line-height: 1.4;
    }

    #categoryTable .select2-container--default .select2-selection--multiple .select2-search__field {
        margin-top: 4px;
        line-height: 1.4;
    }

    /* Fix clipped text in department dropdown */
    #categoryTable td {
        vertical-align: middle !important;
        overflow: visible !important;
    }

    #categoryTable .row-dept-select {
        display: block;
        width: 100%;
        min-width: 220px;
        height: 40px !important;
        min-height: 40px !important;
        max-height: none !important;
        padding: 8px 32px 8px 12px !important;
        line-height: 1.4 !important;
        font-size: 13px !important;
        border: 1px solid #ced4da !important;
        border-radius: 8px !important;
        background-color: #f3f4f6 !important;
        color: #252525 !important;
        appearance: auto;
        -webkit-appearance: menulist;
        -moz-appearance: menulist;
        box-sizing: border-box;
        overflow: visible !important;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    #categoryTable .row-dept-select:focus {
        border-color: #17675F !important;
        outline: none;
        box-shadow: 0 0 0 2px rgba(69, 98, 195, 0.2);
        background-color: #fff !important;
    }

.datatable-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .datatable-footer .dataTables_info {
        margin-bottom: 0;
    }

    .datatable-footer .dataTables_paginate {
        margin-bottom: 0;
    }

    .sync-progress {
        height: 18px;
        border-radius: 8px;
        overflow: hidden;
    }

    .sync-progress .progress-bar {
        transition: width 0.25s ease;
    }

    .sync-actions {
        gap: 0.5rem;
    }

    .sync-actions .btn {
        min-width: 150px;
        height: 40px;
        font-weight: 600;
    }

    .sync-info-alert {
        background-color: #f8f9fc;
        border-color: #d8dff1;
    }

    .sync-stat-label {
        font-size: 0.75rem;
        color: #6c757d;
    }

    .hidden-existing-form {
        display: none !important;
    }

    .hidden-action-column {
        display: none !important;
    }



</style>


@section('content')

<div class="container-fluid px-4 py-4">

    <div class="content-card mb-4">
        <div class="mb-4">
            <h4 class="mb-0" style="color: #252525;">Budgetary Head Categories</h4>
        </div>

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
            <div class="mb-2 mb-md-0">
                <strong id="syncStatusText">Ready to sync budget annexure categories</strong>
            </div>
            <div class="sync-actions d-flex flex-wrap">
                <button type="button" class="btn btn-primary" id="syncPinnacleCategoriesBtn">Sync Categories</button>
                <button type="button" class="btn btn-secondary" id="pausePinnacleCategorySyncBtn" disabled>Pause</button>
                <button type="button" class="btn btn-danger" id="stopPinnacleCategorySyncBtn" disabled>Stop</button>
            </div>
        </div>

        <div class="alert alert-light border sync-info-alert mb-3 py-2 px-3">
            <small class="text-muted mb-0">
                <i class="fa fa-info-circle text-primary"></i>
                Pinnacle returns budget annexure <strong>line items</strong>. Each unique <strong>Account ID</strong> is saved as one category in the table below.
            </small>
        </div>

        <div class="progress sync-progress mb-3">
            <div class="progress-bar" id="syncProgressBar" role="progressbar" style="width: 0%; background-color: #17675F;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>

        <div class="row text-center">
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <small class="sync-stat-label d-block">API Rows</small>
                <strong id="syncApiRowCount">0</strong>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <small class="sync-stat-label d-block">Unique Categories</small>
                <strong id="syncUniqueCount">0</strong>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <small class="sync-stat-label d-block">Processed</small>
                <strong id="syncProcessedCount">0</strong>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <small class="sync-stat-label d-block">Inserted</small>
                <strong id="syncInsertedCount">0</strong>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <small class="sync-stat-label d-block">Updated</small>
                <strong id="syncUpdatedCount">0</strong>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <small class="sync-stat-label d-block">Skipped</small>
                <strong id="syncSkippedCount">0</strong>
            </div>
        </div>

        <div id="syncSummaryCard" class="alert alert-success d-none mt-2 mb-3 py-2 px-3" role="alert">
            <i class="fa fa-check-circle"></i>
            <span id="syncSummaryMessage"></span>
        </div>

    {{-- <div class="card-header" style="background: #17675F; color: white;">
        <h4 class="page-header" style="font-family: 'Montserrat', sans-serif;">Budgetary Head Categories</h4>
    </div> --}}
    <!-- Add/Edit Category Form (Moved to Top) -->
    <div class="content-card mb-4 hidden-existing-form">
        <h5 class="mb-4" style="font-weight: 600; color: #252525;" id="formTitle">Add Category</h5>
        <form id="categoryForm">
            @csrf
            <input type="hidden" name="category_id" id="category_id">
            <!-- Row 1: 3 fields -->
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="budget_type">Budget Type</label>
                    <select name="budget_type" id="budget_type" class="form-control" required>
                        <option value=""> Select Budget Type </option>
                        <option value="CapEx">Capital Expenditure (CapEx)</option>
                        <option value="OpEx">Operational Expenditure (OpEx)</option>
                    </select>
                    <div class="invalid-feedback" id="budget_type_error"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="parent_department_id">Relevant Executing Department</label>
                    <select name="parent_department_id" id="parent_department_id" class="form-control" required>
                        <option value=""> Select Department </option>
                        <option value="0">By Self</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="parent_department_id_error"></div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="category_name">Name</label>
                    <input type="text" name="category_name" id="category_name" class="form-control" placeholder="Enter category name" required style="height: 2.375rem;">
                    <div class="invalid-feedback" id="category_name_error"></div>
                </div>
            </div>

            <!-- Row 2: Description -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="category_description">Description</label>
                    <textarea name="category_description" id="category_description" class="form-control" placeholder="Enter description" rows="3"></textarea>
                    <div class="invalid-feedback" id="category_description_error"></div>
                </div>
            </div>

            <!-- Row 4: Both checkboxes -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="d-flex align-items-center" style="gap: 40px;">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="q_parent_dept_required" name="q_parent_dept_required" value="1">
                            <label class="form-check-label" for="q_parent_dept_required">Require Relevant Executing Department Review for quotation requests</label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="q_head_approval_required" name="q_head_approval_required" value="1">
                            <label class="form-check-label" for="q_head_approval_required">Require approval from department head for quotation requests</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 5: Approvers (Hidden by default) -->
            <div class="row" id="approvers_row" style="display: none;">
                <div class="col-md-6 mb-3" id="parent_dept_users_col" style="display: none;">
                    <label for="q_parent_dept_users" class="form-label">Approver (Relevant Executing Department Review) <span class="text-danger">*</span></label>
                    <select class="form-control select2-show-search" id="q_parent_dept_users" name="q_parent_dept_users[]" multiple>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ trim($user->prefix . ' ' . $user->full_name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3" id="head_approval_users_col" style="display: none;">
                    <label for="q_head_approval_users" class="form-label">Approver (Head Approval) <span class="text-danger">*</span></label>
                    <select class="form-control select2-show-search" id="q_head_approval_users" name="q_head_approval_users[]" multiple>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ trim($user->prefix . ' ' . $user->full_name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-5">
                <button type="submit" class="btn btn-primary" id="submitBtn" style="margin-right: 10px;">Save</button>
                <button type="button" class="btn btn-danger" onclick="resetForm()" >Reset</button>
            </div>
        </form>
    </div>

    <!-- Categories Table -->
    <div class="mt-4">
        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table" id="categoryTable" style="width:100%">
                <thead>
                    <tr style="background-color: #17675F !important; color: white !important;">
                        <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Sr.</th>
                        <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Pinnacle ID</th>
                        <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Source</th>
                        <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Name</th>
                        <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Description</th>
                        <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap; min-width: 220px;">Relevant Executing Department</th>
                        <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap; min-width: 120px;">Budget Type</th>
                        <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap; min-width: 240px;">Relevant Executing Dept Review</th>
                        <th class="hidden-action-column" style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div id="categoryTableFooter" class="datatable-footer mt-3"></div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #17675F;">
                <h5 class="modal-title" id="deleteConfirmModalLabel" style="color: #FFFFFF;">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: #FFFFFF;">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this category? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" onclick="confirmDeleteCategory()">Delete</button>
            </div>
        </div>
    </div>
    </div>

@include('components.toast-notifications')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@php
    $usersListForJs = collect($users)->map(function ($u) {
        $id = isset($u->id) ? $u->id : (isset($u->ID) ? $u->ID : null);
        $prefix = isset($u->prefix) ? $u->prefix : (isset($u->PREFIX) ? $u->PREFIX : '');
        $fullName = isset($u->full_name) ? $u->full_name : (isset($u->FULL_NAME) ? $u->FULL_NAME : '');
        $deptId = isset($u->emp_dept_id) ? $u->emp_dept_id : (isset($u->EMP_DEPT_ID) ? $u->EMP_DEPT_ID : '');
        return [
            'id' => (string) $id,
            'name' => trim($prefix . ' ' . $fullName),
            'dept_id' => ($deptId !== null && $deptId !== '') ? (string) $deptId : '',
        ];
    })->values();
@endphp

<script>
    $(document).ready(function () {
        let authToken = $('meta[name="api-token"]').attr('content') || '';
        const pinnacleCategoriesUrl = '{{ route("store-procurement.pinnacle.fetch", ["endpointKey" => "budget_annexure"]) }}';
        const syncPinnacleCategoryChunkUrl = '{{ route("vendor-category.sync-pinnacle-chunk") }}';
        const quickUpdateUrl = '{{ url("/vendor-category") }}';
        const departmentsList = @json($departments);
        const usersList = @json($usersListForJs);
        const syncChunkSize = 100;

        function buildReviewerOptionsHtml(deptId, selectedIds) {
            const selected = (selectedIds || []).map(String);
            let opts = '';
            // Only employees of selected Relevant Executing Department
            if (!deptId || deptId === '0') {
                return opts;
            }
            (usersList || []).forEach(function(u) {
                if (String(u.dept_id) !== String(deptId)) {
                    return;
                }
                const isSelected = selected.indexOf(String(u.id)) !== -1;
                opts += `<option value="${u.id}" ${isSelected ? 'selected' : ''}>${u.name}</option>`;
            });
            return opts;
        }

        let syncState = {
            records: [],
            apiRowCount: 0,
            offset: 0,
            summary: { inserted: 0, updated: 0, skipped: 0, processed: 0 },
            isRunning: false,
            isPaused: false,
            isStopped: false,
            sourceRequest: null,
            chunkRequest: null
        };

        function extractPinnacleRecords(response) {
            let records = Array.isArray(response) ? response : (response.data || response.Data || []);

            if (!Array.isArray(records) && records && typeof records === 'object') {
                if (Array.isArray(records.data)) {
                    records = records.data;
                } else if (Array.isArray(records.Data)) {
                    records = records.Data;
                } else {
                    records = [];
                }
            }

            return Array.isArray(records) ? records : [];
        }

        function dedupeRecordsByAccountId(records) {
            const uniqueMap = new Map();

            records.forEach(function(record) {
                const accountId = String(record.Account_ID ?? record.account_id ?? '').trim();
                const accountTitle = String(record.Account_Title ?? record.account_title ?? '').trim();

                if (!accountId || !accountTitle) {
                    return;
                }

                uniqueMap.set(accountId, record);
            });

            return {
                records: Array.from(uniqueMap.values()),
                apiRowCount: records.length,
                uniqueCount: uniqueMap.size
            };
        }

        function resetSyncProgress() {
            $('#syncApiRowCount').text(0);
            $('#syncUniqueCount').text(0);
            updateSyncProgress(0, 0, 0, 0, 0);
            $('#syncSummaryCard').addClass('d-none');
            $('#syncSummaryMessage').text('');
        }

        function updateSyncProgress(total, processed, inserted, updated, skipped) {
            const percent = total > 0 ? Math.round((processed / total) * 100) : 0;
            $('#syncUniqueCount').text(total);
            $('#syncProcessedCount').text(processed);
            $('#syncInsertedCount').text(inserted);
            $('#syncUpdatedCount').text(updated);
            $('#syncSkippedCount').text(skipped);
            $('#syncProgressBar')
                .css('width', percent + '%')
                .attr('aria-valuenow', percent)
                .text(percent + '%');
        }

        function buildSyncSummaryMessage() {
            const summary = syncState.summary;
            const apiRows = syncState.apiRowCount || 0;
            const unique = syncState.records.length;

            return apiRows + ' API rows fetched, ' + unique + ' unique categories synced (' +
                summary.inserted + ' new, ' + summary.updated + ' updated, ' + summary.skipped + ' skipped).';
        }

        function showSyncSummary() {
            $('#syncSummaryMessage').text(buildSyncSummaryMessage());
            $('#syncSummaryCard').removeClass('d-none');
        }

        function setSyncControls(state) {
            if (state === 'idle') {
                $('#syncPinnacleCategoriesBtn').prop('disabled', false).text('Sync Categories');
                $('#pausePinnacleCategorySyncBtn').prop('disabled', true).text('Pause');
                $('#stopPinnacleCategorySyncBtn').prop('disabled', true).text('Stop');
                return;
            }

            if (state === 'fetching') {
                $('#syncPinnacleCategoriesBtn').prop('disabled', true).text('Fetching Categories');
                $('#pausePinnacleCategorySyncBtn').prop('disabled', true).text('Pause');
                $('#stopPinnacleCategorySyncBtn').prop('disabled', false).text('Stop');
                return;
            }

            if (state === 'running') {
                $('#syncPinnacleCategoriesBtn').prop('disabled', true).text('Sync in Progress');
                $('#pausePinnacleCategorySyncBtn').prop('disabled', false).text('Pause');
                $('#stopPinnacleCategorySyncBtn').prop('disabled', false).text('Stop');
                return;
            }

            if (state === 'paused') {
                $('#syncPinnacleCategoriesBtn').prop('disabled', true).text('Sync Paused');
                $('#pausePinnacleCategorySyncBtn').prop('disabled', false).text('Resume');
                $('#stopPinnacleCategorySyncBtn').prop('disabled', false).text('Stop');
                return;
            }
        }

        function finishSync(statusText, showSummary) {
            syncState.isRunning = false;
            syncState.isPaused = false;
            syncState.isStopped = false;
            $('#syncStatusText').text(statusText);
            setSyncControls('idle');

            if (showSummary) {
                showSyncSummary();
            }

            fetchCategories();
        }

        function processCategoryChunks() {
            const total = syncState.records.length;

            if (syncState.isStopped) {
                finishSync('Sync stopped');
                return;
            }

            if (syncState.isPaused) {
                $('#syncStatusText').text('Sync paused');
                setSyncControls('paused');
                return;
            }

            if (syncState.offset >= total) {
                updateSyncProgress(
                    total,
                    total,
                    syncState.summary.inserted,
                    syncState.summary.updated,
                    syncState.summary.skipped
                );
                showToast('success', buildSyncSummaryMessage());
                finishSync('Sync completed', true);
                return;
            }

            const chunk = syncState.records.slice(syncState.offset, syncState.offset + syncChunkSize);
            $('#syncStatusText').text('Syncing categories ' + (syncState.offset + 1) + ' to ' + Math.min(syncState.offset + chunk.length, total));

            syncState.chunkRequest = $.ajax({
                url: syncPinnacleCategoryChunkUrl,
                type: 'POST',
                data: JSON.stringify({ records: chunk }),
                contentType: 'application/json',
                headers: {
                    'Authorization': 'Bearer ' + authToken,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    const summary = response.summary || {};
                    syncState.summary.inserted += parseInt(summary.inserted || 0, 10);
                    syncState.summary.updated += parseInt(summary.updated || 0, 10);
                    syncState.summary.skipped += parseInt(summary.skipped || 0, 10);
                    syncState.summary.processed += chunk.length;
                    syncState.offset += syncChunkSize;

                    updateSyncProgress(
                        total,
                        syncState.summary.processed,
                        syncState.summary.inserted,
                        syncState.summary.updated,
                        syncState.summary.skipped
                    );
                    fetchCategories();
                    processCategoryChunks();
                },
                error: function(xhr) {
                    showToast('error', xhr.responseJSON?.message || 'Category sync failed.');
                    finishSync('Sync failed');
                }
            });
        }

        function syncPinnacleCategories() {
            syncState = {
                records: [],
                apiRowCount: 0,
                offset: 0,
                summary: { inserted: 0, updated: 0, skipped: 0, processed: 0 },
                isRunning: true,
                isPaused: false,
                isStopped: false,
                sourceRequest: null,
                chunkRequest: null
            };

            resetSyncProgress();
            $('#syncStatusText').text('Fetching categories from Pinnacle');
            setSyncControls('fetching');

            syncState.sourceRequest = $.get(pinnacleCategoriesUrl)
                .done(function(response) {
                    if (syncState.isStopped) {
                        finishSync('Sync stopped');
                        return;
                    }

                    const rawRecords = extractPinnacleRecords(response);
                    const deduped = dedupeRecordsByAccountId(rawRecords);

                    syncState.apiRowCount = deduped.apiRowCount;
                    syncState.records = deduped.records;
                    $('#syncApiRowCount').text(deduped.apiRowCount);

                    if (!syncState.records.length) {
                        finishSync('No valid categories found in Pinnacle');
                        return;
                    }

                    $('#syncStatusText').text(
                        'Fetched ' + deduped.apiRowCount + ' API rows, syncing ' + deduped.uniqueCount + ' unique categories'
                    );
                    updateSyncProgress(deduped.uniqueCount, 0, 0, 0, 0);
                    setSyncControls('running');
                    processCategoryChunks();
                })
                .fail(function(xhr, status) {
                    if (status === 'abort') {
                        finishSync('Sync stopped');
                        return;
                    }

                    showToast('error', 'Unable to fetch categories from Pinnacle.');
                    finishSync('Sync failed');
                });
        }

        // Initialize DataTable
        var categoryTable = $('#categoryTable').DataTable({
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12 col-md-12"B>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm mr-2',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                }
            ],
            ajax: {
                url: '{{ route("vendor-category.data") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataSrc: 'data'
            },
            processing: true,
            serverSide: true,
            columns: [
                {
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'pinnacle_account_id',
                    render: function(data) {
                        return data ?? '-';
                    }
                },
                { data: 'source', defaultContent: '-' },
                { data: 'category_name' },
                {
                    data: 'category_description',
                    render: function(data) {
                        return data ?? '-';
                    }
                },
                {
                    data: 'parent_department_id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let opts = '<option value="0">By Self</option>';
                        (departmentsList || []).forEach(function(d) {
                            const selected = String(data) === String(d.dept_id) ? 'selected' : '';
                            opts += `<option value="${d.dept_id}" ${selected}>${d.dept_name}</option>`;
                        });
                        return `<select class="form-control row-dept-select" data-id="${row.category_id}">${opts}</select>`;
                    }
                },
                { data: 'budget_type', defaultContent: '-' },
                {
                    data: 'q_parent_dept_users',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        const selected = String(data || '').split(',').map(function(v) { return v.trim(); }).filter(Boolean);
                        const deptId = row.parent_department_id != null ? String(row.parent_department_id) : '';
                        return `<select class="form-control form-control-sm row-reviewer-select" multiple data-id="${row.category_id}" data-dept-id="${deptId}" style="width:100%;min-width:220px;">${buildReviewerOptionsHtml(deptId, selected)}</select>`;
                    }
                },
                {
                    data: 'category_id',
                    defaultContent: '',
                    render: function(data) {
                        if (!data) {
                            return '';
                        }
                        return `
                            <div class="action-menu hidden-action-column">
                                <button type="button" class="action-menu-button" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <div class="action-menu-dropdown" role="menu">
                                    <button class="action-menu-item" type="button" onclick="editCategory(${data}); return false;">Edit</button>
                                    <button class="action-menu-item text-danger" type="button" onclick="deleteCategory(${data}); return false;">Delete</button>
                                </div>
                            </div>
                        `;
                    },
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[1, 'asc']],
            language: {
                emptyTable: "No categories found",
                loadingRecords: "Loading...",
                processing: "Processing...",
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            pageLength: 10,
            responsive: true
        });

        var footerContainer = $('#categoryTableFooter');

        function moveDataTableFooter() {
            var wrapper = $('#categoryTable').closest('.dataTables_wrapper');
            footerContainer.append(wrapper.find('.dataTables_info'));
            footerContainer.append(wrapper.find('.dataTables_paginate'));
        }

        function saveRowQuickUpdate(id, payload) {
            $.ajax({
                url: quickUpdateUrl + '/' + id + '/quick-update',
                type: 'POST',
                data: Object.assign({ _token: '{{ csrf_token() }}' }, payload),
                success: function(res) {
                    if (typeof showToast === 'function') {
                        showToast('success', res.message || 'Updated');
                    }
                },
                error: function(xhr) {
                    if (typeof showToast === 'function') {
                        showToast('error', (xhr.responseJSON && xhr.responseJSON.message) || 'Update failed');
                    }
                }
            });
        }

        function initRowReviewerSelect2($scope) {
            const $selects = ($scope || $(document)).find('.row-reviewer-select');
            $selects.each(function() {
                const $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }
                $el.select2({
                    placeholder: 'Search department employee...',
                    allowClear: true,
                    width: '100%',
                    multiple: true,
                    closeOnSelect: false,
                    dropdownParent: $('body'),
                    matcher: function(params, data) {
                        if ($.trim(params.term) === '') {
                            return data;
                        }
                        if (typeof data.text === 'undefined') {
                            return null;
                        }
                        if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                            return data;
                        }
                        return null;
                    }
                });
            });
        }

        function refreshRowReviewers($row, deptId, clearSelection) {
            const $reviewer = $row.find('.row-reviewer-select');
            if (!$reviewer.length) {
                return;
            }
            const keepSelected = clearSelection ? [] : ($reviewer.val() || []);
            // Keep only selected users that still belong to this dept
            const validSelected = keepSelected.filter(function(id) {
                const user = (usersList || []).find(function(u) { return String(u.id) === String(id); });
                return user && String(user.dept_id) === String(deptId);
            });

            if ($reviewer.hasClass('select2-hidden-accessible')) {
                $reviewer.select2('destroy');
            }
            $reviewer.attr('data-dept-id', deptId || '');
            $reviewer.html(buildReviewerOptionsHtml(deptId, validSelected));
            initRowReviewerSelect2($row);

            // If selection changed due to dept filter, persist
            const newVal = $reviewer.val() || [];
            if (JSON.stringify(newVal.map(String).sort()) !== JSON.stringify((keepSelected || []).map(String).sort())) {
                saveRowQuickUpdate($reviewer.data('id'), { 'q_parent_dept_users': newVal });
            }
        }

        $(document).on('change', '.row-dept-select', function() {
            const id = $(this).data('id');
            const deptId = $(this).val();
            const $row = $(this).closest('tr');
            saveRowQuickUpdate(id, { parent_department_id: deptId });
            // Reload employees for this department only
            refreshRowReviewers($row, deptId, true);
        });

        $(document).on('change', '.row-reviewer-select', function() {
            const id = $(this).data('id');
            const users = $(this).val() || [];
            saveRowQuickUpdate(id, { 'q_parent_dept_users': users });
        });

        categoryTable.on('init', function () {
            moveDataTableFooter();
            initRowReviewerSelect2($('#categoryTable'));
        });

        categoryTable.on('draw', function () {
            moveDataTableFooter();
            initRowReviewerSelect2($('#categoryTable'));
        });

        // Toggle action menus
        $(document).on('click', '.action-menu-button', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $menu = $(this).siblings('.action-menu-dropdown');
            $('.action-menu-dropdown').not($menu).removeClass('show');
            $menu.toggleClass('show');
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.action-menu').length) {
                $('.action-menu-dropdown').removeClass('show');
            }
        });

        // Function to reload DataTable
        function fetchCategories() {
            categoryTable.ajax.reload(null, false);
        }

        // Function to initialize select2 on a specific element
        function initializeSelect2(selector) {
            if (!$(selector).hasClass('select2-hidden-accessible')) {
                $(selector).select2({
                    placeholder: 'Select Users',
                    allowClear: true,
                    width: '100%',
                    theme: 'default',
                    templateResult: formatUser,
                    templateSelection: formatUser,
                    escapeMarkup: function(markup) { return markup; }
                });
            }
        }

        // Format function for displaying users properly
        function formatUser(user) {
            if (!user.id) {
                return user.text;
            }
            return user.text;
        }

        // Handle checkbox change for Parent Department Users
        $('#q_parent_dept_required').on('change', function() {
            if ($(this).is(':checked')) {
                $('#approvers_row').show();
                $('#parent_dept_users_col').show();
                initializeSelect2('#q_parent_dept_users');
                $('#q_parent_dept_users').prop('required', true);
            } else {
                $('#parent_dept_users_col').hide();
                $('#q_parent_dept_users').prop('required', false);
                $('#q_parent_dept_users').val(null).trigger('change');
                // Hide main row if both columns are hidden
                if (!$('#q_head_approval_required').is(':checked')) {
                    $('#approvers_row').hide();
                }
            }
        });

        // Handle checkbox change for Head Approval Users
        $('#q_head_approval_required').on('change', function() {
            if ($(this).is(':checked')) {
                $('#approvers_row').show();
                $('#head_approval_users_col').show();
                initializeSelect2('#q_head_approval_users');
                $('#q_head_approval_users').prop('required', true);
            } else {
                $('#head_approval_users_col').hide();
                $('#q_head_approval_users').prop('required', false);
                $('#q_head_approval_users').val(null).trigger('change');
                // Hide main row if both columns are hidden
                if (!$('#q_parent_dept_required').is(':checked')) {
                    $('#approvers_row').hide();
                }
            }
        });

        function clearValidationErrors() {
            const fields = ['budget_type','parent_department_id','category_name','category_description'];
            fields.forEach(function(field){
                const $input = $('#' + field);
                $input.removeClass('is-invalid');
                $('#' + field + '_error').text('');
            });
        }

        function applyValidationErrors(errors) {
            Object.keys(errors || {}).forEach(function(key){
                const messages = errors[key];
                const $input = $('#' + key);
                const $error = $('#' + key + '_error');
                if ($input.length) {
                    $input.addClass('is-invalid');
                }
                if ($error.length) {
                    $error.text(Array.isArray(messages) ? messages[0] : String(messages));
                }
            });
        }

        function firstErrorMessage(errors) {
            if (!errors) return '';
            const firstKey = Object.keys(errors)[0];
            if (!firstKey) return '';
            const msgs = errors[firstKey];
            return Array.isArray(msgs) ? msgs[0] : String(msgs);
        }

        // Handle form submission for both create and edit
        $('#categoryForm').on('submit', function(e) {
            e.preventDefault();

            const categoryId = $('#category_id').val();
            const isEdit = categoryId !== '';
            clearValidationErrors();

            if (isEdit) {
                // Update existing category
                $.ajax({
                    url: `/vendor-category/${categoryId}/update`,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (typeof showToast === 'function') {
                            showToast('success', res.message || 'Category updated successfully');
                        }
                        resetForm();
                        fetchCategories();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            applyValidationErrors(xhr.responseJSON.errors);
                            if (typeof showToast === 'function') {
                                showToast('error', firstErrorMessage(xhr.responseJSON.errors) || 'Validation error');
                            }
                        } else {
                            if (typeof showToast === 'function') {
                                showToast('error', (xhr.responseJSON?.message || 'Failed to update category'));
                            }
                        }
                    }
                });
            } else {
                // Create new category
                $.post('{{ route("vendor-category.store") }}', $(this).serialize(), function(res) {
                    if (typeof showToast === 'function') {
                        showToast('success', res.message || 'Category created successfully');
                    }
                    resetForm();
                    fetchCategories();
                }).fail(function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        applyValidationErrors(xhr.responseJSON.errors);
                        if (typeof showToast === 'function') {
                            showToast('error', firstErrorMessage(xhr.responseJSON.errors) || 'Validation error');
                        }
                    } else {
                        if (typeof showToast === 'function') {
                            showToast('error', (xhr.responseJSON?.message || 'Failed to create category'));
                        }
                    }
                });
            }
        });

        // Edit category - populate form with data
        window.editCategory = function(id) {
            $.get(`/vendor-category/${id}/edit`, function(cat) {
                // Change form title and button
                $('#formTitle').text('Edit Category');
                $('#submitBtn').text('Update');

                // Populate form fields
                $('#category_id').val(cat.category_id);
                $('#category_name').val(cat.category_name);
                $('#category_description').val(cat.category_description);
                $('#budget_type').val(cat.budget_type);

                // Handle parent_department_id: if null/undefined = '', if 0 = '0', else convert to string
                let parentDeptId = '';
                if (cat.parent_department_id !== null && cat.parent_department_id !== undefined) {
                    parentDeptId = String(cat.parent_department_id);
                }
                $('#parent_department_id').val(parentDeptId);

                $('#q_parent_dept_required').prop('checked', String(cat.q_parent_dept_required) === '1');
                $('#q_head_approval_required').prop('checked', String(cat.q_head_approval_required) === '1');

                // Show/hide user dropdowns based on checkbox values
                var showApproversRow = false;

                if (String(cat.q_parent_dept_required) === '1') {
                    showApproversRow = true;
                    $('#parent_dept_users_col').show();
                    initializeSelect2('#q_parent_dept_users');
                    $('#q_parent_dept_users').prop('required', true);

                    // Populate parent dept users
                    if (cat.q_parent_dept_users) {
                        setTimeout(function() {
                            var parentUsers = cat.q_parent_dept_users.split(',').map(function(id) { return id.trim(); });
                            $('#q_parent_dept_users').val(parentUsers).trigger('change');
                        }, 100);
                    }
                } else {
                    $('#parent_dept_users_col').hide();
                    $('#q_parent_dept_users').prop('required', false);
                }

                if (String(cat.q_head_approval_required) === '1') {
                    showApproversRow = true;
                    $('#head_approval_users_col').show();
                    initializeSelect2('#q_head_approval_users');
                    $('#q_head_approval_users').prop('required', true);

                    // Populate head approval users
                    if (cat.q_head_approval_users) {
                        setTimeout(function() {
                            var headUsers = cat.q_head_approval_users.split(',').map(function(id) { return id.trim(); });
                            $('#q_head_approval_users').val(headUsers).trigger('change');
                        }, 100);
                    }
                } else {
                    $('#head_approval_users_col').hide();
                    $('#q_head_approval_users').prop('required', false);
                }

                // Show approvers row if either column should be visible
                if (showApproversRow) {
                    $('#approvers_row').show();
                } else {
                    $('#approvers_row').hide();
                }

                // Scroll to form
                $('html, body').animate({
                    scrollTop: $('#categoryForm').offset().top - 100
                }, 500);
            }).fail(function(xhr) {
                if (typeof showToast === 'function') {
                    showToast('error', (xhr.responseJSON?.message || 'Failed to load category'));
                }
            });
        }

        let pendingDeleteCategoryId = null;

        window.deleteCategory = function(id) {
            pendingDeleteCategoryId = id;
            $('#deleteConfirmModal').modal('show');
        }

        window.confirmDeleteCategory = function() {
            if (!pendingDeleteCategoryId) return;
            const id = pendingDeleteCategoryId;
            // disable button to prevent double click
            const $btn = $('#confirmDeleteBtn');
            $btn.prop('disabled', true);

            $.ajax({
                url: `/vendor-category/${id}/delete`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    if (typeof showToast === 'function') {
                        showToast('success', res.message || 'Category deleted successfully');
                    }
                    $('#deleteConfirmModal').modal('hide');
                    fetchCategories();
                },
                error: function(xhr) {
                    if (typeof showToast === 'function') {
                        showToast('error', (xhr.responseJSON?.message || 'Failed to delete category'));
                    }
                },
                complete: function() {
                    pendingDeleteCategoryId = null;
                    $btn.prop('disabled', false);
                }
            });
        };

        window.resetForm = function() {
            $('#categoryForm')[0].reset();
            $('#category_id').val('');
            $('#formTitle').text('Add Category');
            $('#submitBtn').text('Save');

            // Reset and hide user dropdowns
            if ($('#q_parent_dept_users').hasClass('select2-hidden-accessible')) {
                $('#q_parent_dept_users').val(null).trigger('change');
            }
            $('#parent_dept_users_col').hide();
            $('#q_parent_dept_users').prop('required', false);

            if ($('#q_head_approval_users').hasClass('select2-hidden-accessible')) {
                $('#q_head_approval_users').val(null).trigger('change');
            }
            $('#head_approval_users_col').hide();
            $('#q_head_approval_users').prop('required', false);

            // Hide approvers row
            $('#approvers_row').hide();

            $('#q_parent_dept_required').prop('checked', false);
            $('#q_head_approval_required').prop('checked', false);
        }

        $('#syncPinnacleCategoriesBtn').on('click', function () {
            syncPinnacleCategories();
        });

        $('#pausePinnacleCategorySyncBtn').on('click', function () {
            if (!syncState.isRunning) return;
            syncState.isPaused = !syncState.isPaused;
            if (!syncState.isPaused) {
                setSyncControls('running');
                processCategoryChunks();
            } else {
                setSyncControls('paused');
            }
        });

        $('#stopPinnacleCategorySyncBtn').on('click', function () {
            if (!syncState.isRunning) return;
            syncState.isStopped = true;
            syncState.isPaused = false;

            if (syncState.sourceRequest) {
                syncState.sourceRequest.abort();
            }

            if (syncState.chunkRequest) {
                syncState.chunkRequest.abort();
            }

            $('#syncStatusText').text('Sync stopped');
            setSyncControls('idle');
            syncState.isRunning = false;
            fetchCategories();
        });
    });
</script>
@endsection
