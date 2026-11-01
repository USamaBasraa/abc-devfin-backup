@extends('layouts.app')
{{-- @dd("working"); --}}
@section('content')
@include('components.toast-notifications')
<link href="/css/bnu-layout.css" rel="stylesheet" />

<style>
    @media (min-width: 576px) {
        div.dataTables_wrapper div.dataTables_filter input {
            width: 100%;
        }
    }

    div.dataTables_wrapper div.dataTables_filter input {
        width: 450px;
        height: 40px;
    }

    .dt-controls {
        gap: 0.75rem;
    }

    .dt-controls .dataTables_length label {
        margin-bottom: 0;
    }

    .dt-footer {
        width: 100%;
    }

    .dt-footer .dataTables_info {
        margin-bottom: 0;
    }

    .dt-footer .dataTables_paginate {
        margin: 0 !important;
    }

    .dataTables_wrapper .dt-buttons {
        margin-bottom: 0 !important;
    }

    table.dataTable thead th {
        padding-right: 1.75rem !important;
    }

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
        color: #17675F;
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

    .sync-progress {
        height: 18px;
        border-radius: 8px;
        overflow: hidden;
    }

    .sync-progress .progress-bar {
        transition: width 0.25s ease;
    }

    .sync-progress-low {
        background-color: #dc3545 !important;
    }

    .sync-progress-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }

    .sync-progress-mid {
        background-color: #17675F !important;
    }

    .sync-progress-complete {
        background-color: #28a745 !important;
    }

    .sync-actions {
        gap: 0.5rem;
    }

    .sync-actions .btn {
        min-width: 150px;
        height: 40px;
        font-weight: 600;
    }

    .sync-actions .btn:disabled {
        cursor: not-allowed;
        opacity: 0.75;
    }

    .edit-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 22px;
        padding: 0 8px;
        border-radius: 999px;
        background: #f1f4ff;
        color: #17675F;
        font-weight: 700;
        font-size: 11px;
        line-height: 1;
    }

    .vendor-name-cell {
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .vendor-name-text {
        flex: 1;
    }

    .vendor-table-status {
        padding: 24px 12px;
        text-align: center;
        color: #6c757d;
        font-weight: 500;
    }

    .vendor-category-controls {
        display: flex;
        align-items: stretch;
        gap: 8px;
    }

    .vendor-category-controls .form-control {
        flex: 1;
    }

    .vendor-category-btn {
        min-width: 40px;
        padding: 0 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .vendor-star-rating {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 1.4rem;
        color: #f0ad4e;
        cursor: pointer;
        min-height: 38px;
        width: 100%;
    }

    .vendor-star-rating .fa {
        transition: transform 0.15s ease, color 0.15s ease;
    }

    .vendor-star-rating .fa:hover {
        transform: scale(1.12);
    }

    .vendor-star-rating .fa.active {
        color: #f0ad4e;
    }
</style>
<meta name="api-token" content="{{ auth()->user()->api_token ?? '' }}">

<div class="container-fluid mt-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header" style="background: #17675F !important; color: #fff !important;">
            <h4 class="mb-0">Sync Vendors from Pinnacle</h4>
        </div>
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
                <div class="mb-2 mb-md-0">
                    <strong id="syncStatusText">Ready to sync</strong>
                </div>
                <div class="sync-actions d-flex flex-wrap">
                    <button type="button" class="btn" id="syncPinnacleVendorsBtn"
                        style="background: #17675F; color: white; border: none;">
                        Sync Vendors
                    </button>
                    <button type="button" class="btn btn-secondary" id="pausePinnacleSyncBtn" disabled>
                        Pause
                    </button>
                    <button type="button" class="btn btn-danger" id="stopPinnacleSyncBtn" disabled>
                        Stop
                    </button>
                </div>
            </div>

            <div class="progress sync-progress mb-3">
                <div class="progress-bar" id="syncProgressBar" role="progressbar"
                    style="width: 0%; background-color: #17675F;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    0%
                </div>
            </div>

            <div class="row text-center">
                <div class="col-md-2 col-6 mb-2">
                    <small class="text-muted d-block">Total</small>
                    <strong id="syncTotalCount">0</strong>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <small class="text-muted d-block">Synced</small>
                    <strong id="syncProcessedCount">0</strong>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <small class="text-muted d-block">Remaining</small>
                    <strong id="syncRemainingCount">0</strong>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <small class="text-muted d-block">Inserted</small>
                    <strong id="syncInsertedCount">0</strong>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <small class="text-muted d-block">Updated</small>
                    <strong id="syncUpdatedCount">0</strong>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <small class="text-muted d-block">Skipped</small>
                    <strong id="syncSkippedCount">0</strong>
                </div>
            </div>
        </div>
    </div>

    <form id="vendorForm" style="display: none;">
        @csrf
        <input type="hidden" id="vendor_id" name="vendor_id">

        <!-- ================= Add New Vendor ================= -->
        <div class="card shadow-sm mb-4">
            <div class="card-header" style="background: #17675F !important; color: #fff !important;">
                <h4 class="mb-0">
                    <span id="formTitle">Edit Vendor</span>
                </h4>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="vendor_name">Vendor Name</label>
                            <input type="text" class="form-control" name="vendor_name" id="vendor_name">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="vendor_contact_person">Contact Person</label>
                            <input type="text" class="form-control" name="vendor_contact_person" id="vendor_contact_person">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="vendor_phone">Phone</label>
                            <input type="text" class="form-control" name="vendor_phone" id="vendor_phone">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="vendor_email">Email</label>
                            <input type="email" class="form-control" name="vendor_email" id="vendor_email">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="vendor_category_id">Vendor Category <span class="text-danger">*</span></label>
                            <select class="form-control" name="vendor_category_id" id="vendor_category_id" required>
                                <option value="">-- Select Budgetary Head Category --</option>
                                @foreach (($categories ?? collect()) as $category)
                                    <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">
                                Same list as
                                <a href="{{ url('/vendor-category-create') }}" target="_blank">Budgetary Head Categories</a>.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="vendor_registration_date">Registration Date</label>
                            <input type="date" class="form-control" name="vendor_registration_date" id="vendor_registration_date">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="vendor_address">Address</label>
                    <textarea class="form-control" name="vendor_address" id="vendor_address" rows="2"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="vendor_rating" class="d-block">Rating</label>
                            <input type="hidden" name="vendor_rating" id="vendor_rating" value="">
                            <div class="vendor-star-rating" id="vendorStarRating" role="radiogroup" aria-label="Vendor rating">
                                <i class="fa fa-star-o" data-value="1" role="radio" aria-checked="false" title="1 star"></i>
                                <i class="fa fa-star-o" data-value="2" role="radio" aria-checked="false" title="2 stars"></i>
                                <i class="fa fa-star-o" data-value="3" role="radio" aria-checked="false" title="3 stars"></i>
                                <i class="fa fa-star-o" data-value="4" role="radio" aria-checked="false" title="4 stars"></i>
                                <i class="fa fa-star-o" data-value="5" role="radio" aria-checked="false" title="5 stars"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="vendor_status">Status</label>
                            <select class="form-control" name="vendor_status" id="vendor_status">
                                <option value="">-- Select --</option>
                                <option value="accepted">Accepted</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="vendor_remarks">Remarks</label>
                            <textarea class="form-control" name="vendor_remarks" id="vendor_remarks" rows="2"></textarea>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ================= Vendor Bank Info Card ================= -->
        <div class="card shadow-sm mb-4">
            <div class="card-header" style="background: #17675F !important; color: #fff !important;">
                <h4 class="mb-0">
                    Vendor Bank Information
                </h4>
            </div>

            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Supplier</label>
                        <input type="text" name="supplier" id="supplier" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>NTN/CNIC</label>
                        <input type="text" name="ntn_cnic" id="ntn_cnic" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Company Owner</label>
                        <input type="text" name="attn" id="attn" class="form-control">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Beneficiary Name</label>
                        <input type="text" name="beneficiary_name" id="beneficiary_name" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Bank Name</label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Bank IBAN</label>
                        <input type="text" name="bank_iban" id="bank_iban" class="form-control">
                    </div>
                </div>

            </div>
        </div>


        <div class="clearfix mt-3">
            <div class="float-left">
                <!-- <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Save Vendor
                </button> -->
                <button type="submit" class="btn" id="saveBtn"
                    style="background: #17675F; color: white; border: none;">
                     Save Vendor
                </button>
                <button type="button" class="btn btn-secondary d-none" id="cancelEditBtn">
                    Cancel Edit
                </button>
            </div>
        </div>

    </form>

    <div class="modal fade" id="vendorMasterCategoryModal" tabindex="-1" role="dialog" aria-labelledby="vendorMasterCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="vendorMasterCategoryForm">
                @csrf
                <input type="hidden" id="vendor_master_category_mode" value="create">
                <input type="hidden" id="vendor_master_category_id" value="">
                <div class="modal-content">
                    <div class="modal-header" style="background: #17675F !important; color: #fff !important;">
                        <h5 class="modal-title" id="vendorMasterCategoryModalLabel">Create Vendor Category</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-0">
                            <label for="vendor_master_category_title">Title</label>
                            <input type="text" class="form-control" id="vendor_master_category_title" name="title" maxlength="255" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn" id="vendorMasterCategorySaveBtn" style="background: #17675F; color: white; border: none;">Save Category</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Vendor Table -->
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h4 class="card-title mb-3">Vendor List</h4>

                <table class="table table-striped dt-responsive" id="vendorTable">
                <!-- <table class="table table-bordered table-hover" id="vendorTable"> -->
                    <thead>
                        <tr style="background-color: #17675F !important; color: white !important;">
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap; width: 200px;">#</th>
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Vendor Name </th>
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Contact Person</th>
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Email</th>
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Phone</th>
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Budgetary Head Category</th>
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Address</th>
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Supplier</th>
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">NTN/CNIC</th>
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Company Owner</th>
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Beneficiary Name</th>
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Bank Name</th>
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Bank IBAN</th>
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Rating</th>
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Status</th>
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Remarks</th>
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Registered</th>
                            <th style="background-color: #17675F !important; color: white !important; border-color: #17675F !important; white-space: nowrap;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="18" class="vendor-table-status">Loading vendors...</td>
                        </tr>
                    </tbody>
                </table>
        </div>
    </div>
</div>

<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function(){
        function numericInput(selector, maxLength) {
            $(selector).on('input', function() {
                // Remove non-digit characters
                this.value = this.value.replace(/[^0-9]/g, '');
                // Limit length
                if(this.value.length > maxLength) {
                    this.value = this.value.slice(0, maxLength);
                }
            });
        }

        // Apply to multiple fields
        numericInput('#vendor_phone', 12);
        numericInput('#ntn_cnic', 13);
    });

    let authToken = $('meta[name="api-token"]').attr('content') || '';
    const pinnacleSuppliersUrl = '{{ route("store-procurement.pinnacle.fetch", ["endpointKey" => "chart_of_accounts_suppliers"]) }}';
    const syncPinnacleChunkUrl = '{{ route("vendor.sync-pinnacle-chunk") }}';
    const vendorMasterCategoriesUrl = '{{ route("vendor.master-categories") }}';
    const vendorMasterCategoryStoreUrl = '{{ route("vendor.master-categories.store") }}';
    const vendorMasterCategoryUpdateBaseUrl = '{{ url("/vendor/master-categories") }}';
    const syncChunkSize = 100;
    let syncState = {
        records: [],
        offset: 0,
        summary: {
            inserted: 0,
            updated: 0,
            skipped: 0,
            processed: 0
        },
        isRunning: false,
        isPaused: false,
        isStopped: false,
        isStopping: false,
        sourceRequest: null,
        chunkRequest: null
    };

    function updateVendorMasterEditButtonState() {
        const selectedId = $('#vendor_master_id').val();
        $('#editVendorMasterBtn').prop('disabled', !selectedId || selectedId === '__create_new__');
    }

    function buildVendorMasterOptions(categories, selectedId) {
        let options = '<option value="">-- Select Category --</option>';
        options += '<option value="__create_new__">+ Create New</option>';

        (categories || []).forEach(function (category) {
            const isSelected = String(selectedId || '') === String(category.master_id) ? ' selected' : '';
            options += `<option value="${category.master_id}"${isSelected}>${category.title}</option>`;
        });

        return options;
    }

    function renderVendorMasterOptions(categories, selectedId) {
        $('#vendor_master_id').html(buildVendorMasterOptions(categories, selectedId));
        $('#vendor_master_id').data('previous', selectedId || '');
        updateVendorMasterEditButtonState();
    }

    function refreshVendorMasterCategories(selectedId) {
        return $.ajax({
            url: vendorMasterCategoriesUrl,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + authToken,
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).done(function (response) {
            renderVendorMasterOptions(response.data || [], selectedId);
        }).fail(function () {
            showToast('error', 'Unable to load vendor categories.');
        });
    }

    function openVendorMasterCategoryModal(mode, category) {
        $('#vendor_master_category_mode').val(mode);
        $('#vendor_master_category_id').val(category ? category.master_id : '');
        $('#vendor_master_category_title').val(category ? category.title : '');
        $('#vendorMasterCategoryModalLabel').text(mode === 'edit' ? 'Edit Vendor Category' : 'Create Vendor Category');
        $('#vendorMasterCategorySaveBtn').text(mode === 'edit' ? 'Update Category' : 'Save Category');
        $('#vendorMasterCategoryModal').modal('show');

        setTimeout(function () {
            $('#vendor_master_category_title').trigger('focus');
        }, 150);
    }

    function selectedVendorMasterCategory() {
        const selectedId = $('#vendor_master_id').val();

        if (!selectedId || selectedId === '__create_new__') {
            return null;
        }

        return {
            master_id: selectedId,
            title: $('#vendor_master_id option:selected').text().trim()
        };
    }

    function saveVendorMasterCategory() {
        const mode = $('#vendor_master_category_mode').val();
        const masterId = $('#vendor_master_category_id').val();

        $.ajax({
            url: mode === 'edit' ? `${vendorMasterCategoryUpdateBaseUrl}/${masterId}` : vendorMasterCategoryStoreUrl,
            type: mode === 'edit' ? 'PUT' : 'POST',
            data: {
                title: $('#vendor_master_category_title').val().trim()
            },
            headers: {
                'Authorization': 'Bearer ' + authToken,
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function (response) {
                const category = response.data || {};
                $('#vendorMasterCategoryModal').modal('hide');
                refreshVendorMasterCategories(category.master_id);
                showToast('success', response.message || 'Vendor category saved successfully.');
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const firstError = Object.values(xhr.responseJSON.errors || {})[0];
                    showToast('error', Array.isArray(firstError) ? firstError[0] : 'Validation error.');
                    return;
                }

                showToast('error', 'Unable to save vendor category.');
            }
        });
    }

    function resetSyncProgress() {
        updateSyncProgress(0, 0, 0, 0, 0, 0);
        $('#syncStatusText').text('Fetching vendors from Pinnacle');
    }

    function updateSyncProgress(total, processed, inserted, updated, skipped, percent) {
        let remaining = Math.max(total - processed, 0);
        let progressClass = 'sync-progress-low';

        if (percent >= 75) {
            progressClass = 'sync-progress-complete';
        } else if (percent >= 50) {
            progressClass = 'sync-progress-mid';
        } else if (percent >= 25) {
            progressClass = 'sync-progress-warning';
        }

        $('#syncTotalCount').text(total);
        $('#syncProcessedCount').text(processed);
        $('#syncRemainingCount').text(remaining);
        $('#syncInsertedCount').text(inserted);
        $('#syncUpdatedCount').text(updated);
        $('#syncSkippedCount').text(skipped);
        $('#syncProgressBar')
            .removeClass('sync-progress-low sync-progress-warning sync-progress-mid sync-progress-complete')
            .addClass(progressClass)
            .css('width', percent + '%')
            .attr('aria-valuenow', percent)
            .text(percent + '%');
    }

    function setSyncControls(state) {
        let $syncButton = $('#syncPinnacleVendorsBtn');
        let $pauseButton = $('#pausePinnacleSyncBtn');
        let $stopButton = $('#stopPinnacleSyncBtn');

        if (state === 'idle') {
            $syncButton.prop('disabled', false).text('Sync Vendors');
            $pauseButton.prop('disabled', true).text('Pause');
            $stopButton.prop('disabled', true).text('Stop');
            return;
        }

        if (state === 'fetching') {
            $syncButton.prop('disabled', true).text('Fetching Vendors');
            $pauseButton.prop('disabled', true).text('Pause');
            $stopButton.prop('disabled', false).text('Stop');
            return;
        }

        if (state === 'running') {
            $syncButton.prop('disabled', true).text('Sync in Progress');
            $pauseButton.prop('disabled', false).text('Pause');
            $stopButton.prop('disabled', false).text('Stop');
            return;
        }

        if (state === 'paused') {
            $syncButton.prop('disabled', true).text('Sync Paused');
            $pauseButton.prop('disabled', false).text('Resume');
            $stopButton.prop('disabled', false).text('Stop');
            return;
        }

        if (state === 'stopping') {
            $syncButton.prop('disabled', true).text('Stopping Sync');
            $pauseButton.prop('disabled', true).text('Pause');
            $stopButton.prop('disabled', true).text('Stopping');
        }
    }

    function currentSyncControlState() {
        if (syncState.isStopping || syncState.isStopped) {
            return 'stopping';
        }

        if (syncState.isPaused) {
            return 'paused';
        }

        if (syncState.isRunning) {
            return syncState.records.length > 0 ? 'running' : 'fetching';
        }

        return 'idle';
    }

    function resetSyncState(records) {
        syncState = {
            records: records || [],
            offset: 0,
            summary: {
                inserted: 0,
                updated: 0,
                skipped: 0,
                processed: 0
            },
            isRunning: true,
            isPaused: false,
            isStopped: false,
            isStopping: false,
            sourceRequest: null,
            chunkRequest: null
        };
    }

    function finishSync(statusText, shouldRefreshTable) {
        syncState.isRunning = false;
        syncState.isPaused = false;
        syncState.isStopped = false;
        syncState.isStopping = false;
        $('#syncStatusText').text(statusText);
        setSyncControls('idle');

        if (shouldRefreshTable) {
            fetchVendors();
        }
    }

    function syncPinnacleVendors() {
        resetSyncState([]);
        setSyncControls('fetching');
        resetSyncProgress();

        syncState.sourceRequest = $.get(pinnacleSuppliersUrl)
            .done(function(response) {
                let records = Array.isArray(response) ? response : (response.data || []);

                if (!Array.isArray(records)) {
                    showToast('error', 'Invalid Pinnacle vendors response.');
                    finishSync('Sync failed', false);
                    return;
                }

                resetSyncState(records);
                setSyncControls('running');
                processPinnacleVendorChunks();
            })
            .fail(function() {
                if (syncState.isStopped) {
                    finishSync('Sync stopped', true);
                    return;
                }

                showToast('error', 'Unable to fetch vendors from Pinnacle.');
                finishSync('Sync failed', false);
            });
    }

    function processPinnacleVendorChunks() {
        let records = syncState.records;
        let total = records.length;

        if (total === 0) {
            updateSyncProgress(0, 0, 0, 0, 0, 100);
            finishSync('No vendors found in Pinnacle', false);
            return;
        }

        if (syncState.isStopped) {
            finishSync('Sync stopped', true);
            return;
        }

        if (syncState.isPaused) {
            $('#syncStatusText').text('Sync paused');
            setSyncControls('paused');
            return;
        }

        if (syncState.offset >= total) {
            updateSyncProgress(total, total, syncState.summary.inserted, syncState.summary.updated, syncState.summary.skipped, 100);
            showToast('success', 'Pinnacle vendors synced successfully.');
            finishSync('Sync completed', true);
            return;
        }

        let chunk = records.slice(syncState.offset, syncState.offset + syncChunkSize);
        $('#syncStatusText').text('Syncing vendors ' + (syncState.offset + 1) + ' to ' + Math.min(syncState.offset + chunk.length, total));

        syncState.chunkRequest = $.ajax({
            url: syncPinnacleChunkUrl,
            type: 'POST',
            data: JSON.stringify({ records: chunk }),
            contentType: 'application/json',
            headers: {
                'Authorization': 'Bearer ' + authToken,
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                let chunkSummary = response.summary || {};

                syncState.summary.inserted += parseInt(chunkSummary.inserted || 0, 10);
                syncState.summary.updated += parseInt(chunkSummary.updated || 0, 10);
                syncState.summary.skipped += parseInt(chunkSummary.skipped || 0, 10);
                syncState.summary.processed += chunk.length;
                syncState.offset += syncChunkSize;

                let percent = Math.round((syncState.summary.processed / total) * 100);
                updateSyncProgress(total, syncState.summary.processed, syncState.summary.inserted, syncState.summary.updated, syncState.summary.skipped, percent);
                fetchVendors();

                processPinnacleVendorChunks();
            },
            error: function(xhr) {
                if (syncState.isStopped) {
                    finishSync('Sync stopped', true);
                    return;
                }

                let message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Vendor sync failed.';

                showToast('error', message);
                finishSync('Sync failed', true);
            }
        });
    }

    function pauseOrResumePinnacleSync() {
        if (!syncState.isRunning) {
            return;
        }

        syncState.isPaused = !syncState.isPaused;
        setSyncControls(currentSyncControlState());

        if (syncState.isPaused) {
            $('#syncStatusText').text('Pause requested. Current batch will finish first');
            return;
        }

        $('#syncStatusText').text('Resuming sync');
        setSyncControls('running');
        processPinnacleVendorChunks();
    }

    function stopPinnacleSync() {
        if (!syncState.isRunning) {
            return;
        }

        syncState.isStopped = true;
        syncState.isStopping = true;
        syncState.isPaused = false;
        $('#syncStatusText').text('Stop requested. Current batch will finish first');
        setSyncControls('stopping');

        if (syncState.sourceRequest && syncState.sourceRequest.readyState !== 4) {
            syncState.sourceRequest.abort();
        }
    }

    function setVendorRating(value) {
        const rating = parseInt(value, 10) || 0;

        $('#vendor_rating').val(rating > 0 ? rating : '');
        $('#vendorStarRating .fa').each(function () {
            const starValue = parseInt($(this).data('value'), 10);
            const isActive = starValue <= rating;

            $(this)
                .toggleClass('fa-star', isActive)
                .toggleClass('fa-star-o', !isActive)
                .toggleClass('active', isActive)
                .attr('aria-checked', isActive && starValue === rating ? 'true' : 'false');
        });
    }

    function submitVendorForm() {
        let vendorId = $('#vendor_id').val();
        const masterId = $('#vendor_category_id').val();
        if (!masterId) {
            showToast('error', 'Please select a Vendor Category.');
            $('#vendor_category_id').focus();
            return;
        }
        let url = vendorId ? '{{ route("vendor.update") }}' : '{{ route("vendor.store") }}';
        let method = vendorId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: $('#vendorForm').serialize(),
            headers: {
                'Authorization': 'Bearer ' + authToken,
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function (response) {
                showToast('success', response.message);

                hideVendorForm();
                fetchVendors();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    // ✅ Show only first error
                    let firstError = Object.values(xhr.responseJSON.errors)[0][0];
                    showToast('error', firstError);

                } else {
                    showToast('error', 'Something went wrong!');

                }
            }
        });
    }

    function formatVendorRatingDisplay(rating) {
        const value = parseInt(rating, 10) || 0;

        if (value < 1) {
            return '-';
        }

        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += `<i class="fa ${i <= value ? 'fa-star' : 'fa-star-o'}" style="color:#f0ad4e;"></i>`;
        }

        return `<span title="${value}/5">${stars}</span>`;
    }

    function formatVendorStatusDisplay(status) {
        if (!status) {
            return '-';
        }

        const normalized = String(status).toLowerCase();
        if (normalized === 'accepted') {
            return '<span class="badge badge-success">Accepted</span>';
        }
        if (normalized === 'rejected') {
            return '<span class="badge badge-danger">Rejected</span>';
        }

        return status;
    }

    function fetchVendors() {
        const loadingRow = '<tr><td colspan="18" class="vendor-table-status">Loading vendors...</td></tr>';
        const emptyRow = '<tr><td colspan="18" class="vendor-table-status">No vendors found.</td></tr>';
        const errorRow = '<tr><td colspan="18" class="vendor-table-status">Unable to load vendors right now.</td></tr>';

        $('#vendorTable tbody').html(loadingRow);

        $.get('{{ route("vendor.list") }}', function(data) {
            let rows = '';

            data.forEach((vendor, index) => {
                rows += `<tr>
                    <td>${index + 1}</td>
                    <td>
                        <div class="vendor-name-cell">
                            ${parseInt(vendor.is_edited ?? 0, 10) === 1 ? '<span class="edit-badge">E</span>' : ''}
                            <span class="vendor-name-text">${vendor.vendor_name}</span>
                        </div>
                    </td>
                    <td>${vendor.vendor_contact_person ?? ''}</td>
                    <td>${vendor.vendor_email ?? ''}</td>
                    <td>${vendor.vendor_phone ?? ''}</td>
                    <td>${vendor.vendor_category_name ? `${vendor.vendor_category_name}${vendor.vendor_category_pinnacle_account_id ? ` (${vendor.vendor_category_pinnacle_account_id})` : ''}` : '-'}</td>
                    <td>${vendor.vendor_address ?? '-'}</td>
                    <td>${vendor.supplier ?? ''}</td>
                    <td>${vendor.ntn_cnic ?? ''}</td>
                    <td>${vendor.attn ?? ''}</td>
                    <td>${vendor.beneficiary_name ?? ''}</td>
                    <td>${vendor.bank_name ?? ''}</td>
                    <td>${vendor.bank_iban ?? ''}</td>
                    <td>${formatVendorRatingDisplay(vendor.vendor_rating)}</td>
                    <td>${formatVendorStatusDisplay(vendor.vendor_status)}</td>
                    <td>${vendor.vendor_remarks ? vendor.vendor_remarks : '-'}</td>
                    <td>${vendor.vendor_registration_date ?? ''}</td>
                    <td>
                        <div class="action-menu">
                            <button type="button" class="action-menu-button" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-ellipsis-h"></i>
                            </button>
                            <div class="action-menu-dropdown" role="menu">
                                <button class="action-menu-item" type="button" onclick="editVendor(${vendor.vendor_id}); return false;">Edit</button>
                                <button class="action-menu-item text-danger" type="button" onclick="deleteVendor(${vendor.vendor_id}); return false;">Delete</button>
                            </div>
                        </div>
                    </td>
                </tr>`;
            });

            if ($.fn.DataTable.isDataTable('#vendorTable')) {
                $('#vendorTable').DataTable().destroy();
            }

            $('#vendorTable tbody').html(rows || emptyRow);

            $('#vendorTable').DataTable({
                responsive: true, // ✅ make table responsive
                columnDefs: [
                    { orderable: false, targets: -1 }
                ],
                lengthChange: true,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                pageLength: 10,
                buttons: [
                    {
                        extend: 'excel',
                         className: 'btn btn-success btn-sm mr-2',
                        exportOptions: { columns: ':not(:last-child)' }
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-danger btn-sm mr-2',
                        title: 'Vendor List',
                        orientation: 'landscape',
                        pageSize: { width: 2000, height: 900 },
                        exportOptions: { columns: ':not(:last-child)' },

                    }
                ],
                ordering: true,
                dom: "<'d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3'<'dt-controls d-flex align-items-center flex-wrap'Bl>f><'table-responsive'rt><'dt-footer d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-3'ip>"
            });


        }).fail(function () {
            if ($.fn.DataTable.isDataTable('#vendorTable')) {
                $('#vendorTable').DataTable().destroy();
            }

            $('#vendorTable tbody').html(errorRow);
            showToast('error', 'Unable to load vendor list.');
        });
    }


    function editVendor(id) {

        $.get(`{{ url('vendor-show') }}/${id}`, function(vendor) {
            let date = vendor.vendor_registration_date;

            if (date) {
                // Extract only the date part (ignore the time)
                let formattedDate = date.split(' ')[0]; // "2025-08-12"
                $('#vendor_registration_date').val(formattedDate);
            }

            const categoryId = vendor.vendor_category_id || vendor.VENDOR_CATEGORY_ID || '';

            // Prefill fields
            $('#vendor_id').val(vendor.vendor_id || vendor.VENDOR_ID || '');
            $('#vendor_name').val(vendor.vendor_name || vendor.VENDOR_NAME || '');
            $('#vendor_contact_person').val(vendor.vendor_contact_person || vendor.VENDOR_CONTACT_PERSON || '');
            $('#vendor_phone').val(vendor.vendor_phone || vendor.VENDOR_PHONE || '');
            $('#vendor_email').val(vendor.vendor_email || vendor.VENDOR_EMAIL || '');
            $('#vendor_address').val(vendor.vendor_address || vendor.VENDOR_ADDRESS || '');
            // 🏦 Prefill Vendor Bank Information
            $('#supplier').val(vendor.supplier || vendor.SUPPLIER || '');
            $('#ntn_cnic').val(vendor.ntn_cnic || vendor.NTN_CNIC || '');
            $('#attn').val(vendor.attn || vendor.ATTN || '');
            $('#beneficiary_name').val(vendor.beneficiary_name || vendor.BENEFICIARY_NAME || '');
            $('#bank_name').val(vendor.bank_name || vendor.BANK_NAME || '');
            $('#bank_iban').val(vendor.bank_iban || vendor.BANK_IBAN || '');
            setVendorRating(vendor.vendor_rating || vendor.VENDOR_RATING || 0);
            $('#vendor_remarks').val(vendor.vendor_remarks || vendor.VENDOR_REMARKS || '');
            $('#vendor_status').val(vendor.vendor_status || vendor.VENDOR_STATUS || '');

            if (categoryId) {
                $('#vendor_category_id').val(String(categoryId));
                if ($('#vendor_category_id').val() !== String(categoryId)) {
                    const fallbackName = vendor.vendor_category_name || vendor.category_name || ('Category ' + categoryId);
                    $('#vendor_category_id').append(
                        $('<option></option>').val(String(categoryId)).text(fallbackName)
                    );
                    $('#vendor_category_id').val(String(categoryId));
                }
            } else {
                $('#vendor_category_id').val('');
            }

            // Update form state
            $('#formTitle').text('Edit Vendor');
            $('#saveBtn').html('Update Vendor');
            $('#cancelEditBtn').removeClass('d-none');
            $('#vendorForm').stop(true, true).slideDown(180);

             // ✅ Focus on vendor_name input
             $('#vendor_name').focus();
        });
    }

    function hideVendorForm() {
        $('#vendorForm')[0].reset();
        $('#vendor_id').val('');
        $('#vendor_category_id').val('');
        setVendorRating(0);
        $('#vendor_remarks').val('');
        $('#vendor_status').val('');
        $('#formTitle').text('Edit Vendor');
        $('#saveBtn').html('Update Vendor');
        $('#cancelEditBtn').addClass('d-none');
        $('#vendorForm').stop(true, true).slideUp(180);
    }

    function deleteVendor(id) {
        if (!confirm('Are you sure you want to delete this vendor?')) return;

        $.ajax({
            url: `/vendor/delete/${id}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'POST' // Laravel interprets this as DELETE
            },
            success: function(response) {
                $('#vendorResponse').html(`<div class="alert alert-success">${response.message}</div>`);
                fetchVendors();
            },
            error: function() {
                alert('Error deleting vendor');
            }
        });
    }


    $(document).ready(function () {
        fetchVendors();

        $('#syncPinnacleVendorsBtn').on('click', function () {
            syncPinnacleVendors();
        });

        $('#pausePinnacleSyncBtn').on('click', function () {
            pauseOrResumePinnacleSync();
        });

        $('#stopPinnacleSyncBtn').on('click', function () {
            stopPinnacleSync();
        });

        $('#vendorForm').on('submit', function (e) {
            e.preventDefault();
            submitVendorForm();
        });

        $(document).on('click', '.action-menu-button', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const $dropdown = $(this).siblings('.action-menu-dropdown');
            $('.action-menu-dropdown').not($dropdown).removeClass('show');
            $dropdown.toggleClass('show');
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('.action-menu').length) {
                $('.action-menu-dropdown').removeClass('show');
            }
        });

        $(document).on('click', '.action-menu-item', function () {
            $('.action-menu-dropdown').removeClass('show');
        });

        $(document).on('click', '.editVendor', function () {
            let id = $(this).data('id');
            editVendor(id);
        });

        $(document).on('click', '.deleteVendor', function () {
            let id = $(this).data('id');
            deleteVendor(id);
        });

        $('#cancelEditBtn').on('click', function () {
            hideVendorForm();
        });

        $('#vendorStarRating').on('click', '.fa', function () {
            setVendorRating($(this).data('value'));
        });

        $('#vendorStarRating').on('mouseenter', '.fa', function () {
            const hoverValue = parseInt($(this).data('value'), 10);

            $('#vendorStarRating .fa').each(function () {
                const starValue = parseInt($(this).data('value'), 10);
                const fill = starValue <= hoverValue;
                $(this).toggleClass('fa-star', fill).toggleClass('fa-star-o', !fill);
            });
        }).on('mouseleave', function () {
            setVendorRating($('#vendor_rating').val());
        });
    });
</script>
@endsection
