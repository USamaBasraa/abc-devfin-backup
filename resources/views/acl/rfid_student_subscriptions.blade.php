@extends('layouts.app')

@section('title')
    BNU CMS - RFID Student Subscriptions
@stop

@section('content')
@php
    $currentMonth = now()->format('m');
@endphp
<style>
    .app-content {
        padding-left: 24px !important;
    padding-right: 24px !important;
    padding-bottom: 24px !important;
    }
</style>
<div class="card">
    <div class="card" style=" margin-right:20px; margin-top:20px; padding:20px;">
        <form id="rfid-subscription-filter-form" method="POST" action="">
            @csrf
            <div class="row">
                <h3>RFID Student Subscription Management</h3>
            </div>
            <div class="row">
                <p id="categoryFlowHint" class="text-muted small col-12 mb-2" style="display:none;"></p>
            </div>

            <div class="row filter-row" style="margin-right:10px;">
                <div class="form-group col-sm-3">
                    <label for="session_sel">Session</label>
                    <select name="session" id="session_sel" class="form-control">
                        <option value="">Select Session</option>
                        @foreach ($sessions as $value)
                            <option value="{{ $value->sess_id }}">{{ $value->sess_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-sm-3">
                    <label for="category_id">Category Type</label>
                    <select name="category_id" id="category_id" class="form-control">
                        <option value="">Select Category Type</option>
                        <option value="all" data-name-lower="all">All (all groups — same list as Gate)</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" data-name-lower="{{ strtolower($category->name) }}" {{ (string) $defaultCategoryId === (string) $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-sm-3" id="monthFieldGroup">
                    <label for="month" id="monthFieldLabel">Month</label>
                    <select name="month" id="month" class="form-control" title="">
                        <option value="">Select Month</option>
                        @foreach ($months as $month)
                            <option value="{{ $month['value'] }}" {{ $currentMonth === $month['value'] ? 'selected' : '' }}>
                                {{ $month['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-sm-6" id="gateStatusFieldGroup" style="display:none;">
                    <label for="student_apply_status">Student status (from students_apply_info)</label>
                    <select name="student_apply_status" id="student_apply_status" class="form-control">
                        <option value="">Select Student Status</option>
                        @foreach ($studentApplyStatuses as $stuStatus)
                            <option value="{{ $stuStatus }}">{{ $stuStatus }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-sm-3" id="gateMachineActionGroup" style="display:none;">
                    <label for="rfid_machine_action">On Gate Machines</label>
                    <select name="rfid_machine_action" id="rfid_machine_action" class="form-control">
                        <option value="">Select Action</option>
                        <option value="active">Active (normal access)</option>
                        <option value="blacklist">Blacklist (no access)</option>
                    </select>
                </div>

                <div class="form-group col-sm-3" id="paymentStatusFieldGroup">
                    <label for="payment_status">Payment status</label>
                    <select name="payment_status" id="payment_status" class="form-control">
                        <option value="">Select Payment Status</option>
                        <option value="Paid">Paid</option>
                        <option value="Unpaid">Unpaid</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="form-group col-sm-3" id="subscriptionStatusFieldGroup">
                    <label for="subscription_status" id="subscription_status_label">Current status</label>
                    <select name="subscription_status" id="subscription_status" class="form-control">
                        <option value="">Select Current Status</option>
                        <option value="Active">Active</option>
                        <option value="Pending">Pending</option>
                        <option value="Declined">Declined</option>
                        <option value="Expired">Expired</option>
                    </select>
                </div>
            </div>

            <div class="form-group col-sm-12" style="display: flex; justify-content: center;">
                <button type="button" id="filterBtn" style="width: 150px;" class="btn btn-primary">Filter</button>
                <button type="button" id="updateBtn" style="width: 150px;margin-left:50px;" class="btn btn-success" disabled>Update</button>
                <button type="button" id="clearBtn" style="width: 100px; margin-left:50px;" class="btn btn-secondary">Clear</button>
                <button type="button" id="syncPaidUtilityBtn" data-default-label-paid="Sync Paid Utility (Gym — subscription month)" data-default-label-hostel="Sync Hostel RFID (paid challan month)" data-default-label-gate="Sync Gate RFID (by student status)" data-default-label-all="Sync ALL categories RFID (by student status)" style="min-width: 430px; width: auto; white-space: nowrap; margin-left:50px; display:none;" class="btn btn-success">
                    Sync Paid Utility (Enable Paid + Disable Extra)
                </button>
                <button type="button" id="openSyncLogBtn" style="width: 160px; margin-left:20px; display:none;" class="btn btn-outline-primary">
                    Open Live Log
                </button>
                <button type="button" id="checkGateMachinesBtn" title="students_apply_info list → pehle Gate device par ek-ek kar ke verify" style="min-width: 220px; width: auto; white-space: nowrap; margin-left:20px; display:none;" class="btn btn-info">
                    Check on Gate machine
                </button>
            </div>
        </form>
    </div>
    <div class="row" style="margin:10px;">
        <div class="col-lg-12 col-md-12">
            <div id="syncResultBox" class="alert" style="display:none;"></div>
        </div>
    </div>
    <div class="row" style="margin:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                <table id="rfid-student-subscription-table" class="table display">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Session</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Plan Type</th>
                            <th>Subscription Status</th>
                            <th>Payment Status</th>
                            <th>RFID Access</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    #syncLiveLog {
        margin: 0;
        white-space: pre-wrap;
        font-family: Consolas, Monaco, monospace;
        line-height: 1.45;
    }
    #syncLiveLog .log-line {
        margin-bottom: 3px;
    }
    #syncRunStatus {
        margin-right: auto;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
        max-width: 100%;
    }
    .status-chip {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 12px;
        line-height: 1;
        border: 1px solid #d1d5db;
        background: #f8fafc;
        color: #334155;
    }
    .status-main {
        font-weight: 600;
    }
    .status-running {
        background: #ecfdf3;
        border-color: #86efac;
        color: #166534;
    }
    .status-paused {
        background: #fff7ed;
        border-color: #fdba74;
        color: #9a3412;
    }
    .status-stopped, .status-failed {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #b91c1c;
    }
    .status-completed {
        background: #eff6ff;
        border-color: #93c5fd;
        color: #1d4ed8;
    }
    .status-key {
        font-weight: 600;
        margin-right: 4px;
    }
    .just-updated-row {
        background: #d1f7d6 !important;
        transition: background 0.3s ease-in-out;
    }
</style>

<div class="modal fade" id="bulkSyncModal" tabindex="-1" role="dialog" aria-labelledby="bulkSyncModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#f8f9fa;">
                <h5 class="modal-title" id="bulkSyncModalLabel">Live Utility Tracking</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="bulkSyncCloseBtn">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding:0;">
                <div style="max-height:360px; overflow:auto; background:#0f172a; color:#e2e8f0; padding:16px;">
                    <div id="syncLiveLog">
                        <div class="log-line">Ready.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div id="syncRunStatus"><span class="status-chip status-main">Idle</span></div>
                <button type="button" class="btn btn-warning" id="pauseResumeSyncBtn" disabled>Pause</button>
                <button type="button" class="btn btn-danger" id="stopSyncBtn" disabled>Stop</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="bulkSyncFooterCloseBtn">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="gateMachineCheckModal" tabindex="-1" role="dialog" aria-labelledby="gateMachineCheckModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gateMachineCheckModalLabel">Gate device check (one machine)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="gateMachineCheckSummary" class="mb-2"></p>
                <p id="gateMachineCheckProgress" class="font-weight-bold text-primary mb-2 small"></p>
                <p class="text-muted small mb-2">Flow: <strong>students_apply_info</strong> list (session optional) → <strong>har student par alag request</strong> (ek ISAPI Search). Network tab mein <code>gate-machine-check/step</code> bar bar dikhe ga. Pehla Gate device use hota hai.</p>
                <label for="gateMachineCheckRegistered" class="font-weight-bold">Machine pe <span class="text-success">registered</span> (<span id="gateMachineCheckRegisteredCount">0</span>)</label>
                <textarea id="gateMachineCheckRegistered" class="form-control mb-3" rows="8" readonly style="font-family: Consolas, Monaco, monospace; font-size: 13px;"></textarea>
                <label for="gateMachineCheckNotRegistered" class="font-weight-bold">Machine pe <span class="text-danger">registered nahi</span> (<span id="gateMachineCheckNotRegisteredCount">0</span>)</label>
                <textarea id="gateMachineCheckNotRegistered" class="form-control" rows="8" readonly style="font-family: Consolas, Monaco, monospace; font-size: 13px;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" id="stopGateMachineCheckBtn" disabled>Stop</button>
                <button type="button" class="btn btn-primary" id="copyGateMachineRegisteredBtn">Copy registered</button>
                <button type="button" class="btn btn-outline-primary" id="copyGateMachineNotRegisteredBtn">Copy not registered</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('javascript')
<script>
    
    $('#updateBtn').on('click', function () {

    if (!confirm('Are you sure you want to update displayed records?')) {
        return;
    }

    let table = $('#rfid-student-subscription-table').DataTable();

    let ids = [];

    table.rows({ search: 'applied' }).every(function () {

        let row = this.data();

        if (row.id) {
            ids.push(row.id);
        }

    });

    if (ids.length === 0) {

        alert('No records found.');

        return;
    }

    $.ajax({

        type: 'POST',

        url: "{{ url('rfid-student-subscriptions/update-status') }}",

        data: {

            _token: $('meta[name="csrf-token"]').attr('content'),

            ids: ids,

            subscription_status: $('#subscription_status').val()

        },

        success: function (response) {

            alert(response.message);

            loadRfidStudentSubscriptions();

        },

        error: function () {

            alert('Something went wrong.');

        }

    });

});
    function evaluateUpdateButton() {

    const paymentStatus = $('#payment_status').val();
    const subscriptionStatus = $('#subscription_status').val();

    if (paymentStatus && subscriptionStatus) {

        $('#updateBtn')
            .prop('disabled', false)
            .css({
                'opacity': '1',
                'cursor': 'pointer'
            });

    } else {

        $('#updateBtn')
            .prop('disabled', true)
            .css({
                'opacity': '0.6',
                'cursor': 'not-allowed'
            });
    }
}

    const bulkSyncState = {
        runId: null,
        paused: false,
        stopped: false,
        inFlight: false,
        timer: null,
        machineColors: {},
        startedAtMs: 0,
        totalOps: 0
    };

    const gateCheckState = {
        runId: null,
        total: 0,
        inFlight: false,
        timer: null,
        stopped: false,
        regCount: 0,
        nregCount: 0,
        lineBuffers: {}
    };

    $(document).ready(function () {
        applyCategoryFilterFlow();
        loadRfidStudentSubscriptions();
        evaluateUtilityButtonVisibility();
         evaluateUpdateButton();

        $('#filterBtn').on('click', function () {
            loadRfidStudentSubscriptions();
            evaluateUtilityButtonVisibility();
        });

        $('#clearBtn').on('click', function () {
            refreshFilters();
            applyCategoryFilterFlow();
            loadRfidStudentSubscriptions();
            evaluateUtilityButtonVisibility();
        });

        $('#syncPaidUtilityBtn').on('click', function () {
            startBulkSyncUtility();
        });
        $('#pauseResumeSyncBtn').on('click', function () {
            togglePauseResume();
        });
        $('#stopSyncBtn').on('click', function () {
            stopBulkSyncUtility();
        });
        $('#openSyncLogBtn').on('click', function () {
            $('#bulkSyncModal').modal('show');
        });
        $('#checkGateMachinesBtn').on('click', function () {
            runGateMachineCheck();
        });
        function copyTextareaToClipboard(textareaId) {
            const ta = document.getElementById(textareaId);
            if (!ta) {
                return;
            }
            ta.select();
            try {
                document.execCommand('copy');
            } catch (e) {}
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(ta.value).catch(function () {});
            }
        }
        $('#copyGateMachineRegisteredBtn').on('click', function () {
            copyTextareaToClipboard('gateMachineCheckRegistered');
        });
        $('#copyGateMachineNotRegisteredBtn').on('click', function () {
            copyTextareaToClipboard('gateMachineCheckNotRegistered');
        });
        $('#stopGateMachineCheckBtn').on('click', function () {
            stopGateMachineCheckRun();
        });
        $('#gateMachineCheckModal').on('hidden.bs.modal', function () {
            if (gateCheckState.runId && !gateCheckState.stopped) {
                $('#syncResultBox').removeClass('alert-success alert-danger').addClass('alert-info')
                    .text('Gate machine check is still running. Open this dialog again to watch progress, or click Stop inside the dialog.')
                    .show();
            }
        });

        // Allow closing the modal while utility keeps running in background.
        $('#bulkSyncModal').on('hidden.bs.modal', function () {
            if (isRunActive()) {
                $('#syncResultBox').removeClass('alert-success alert-danger').addClass('alert-info')
                    .text('Utility is still running in background. Click "Open Live Log" to reopen tracking.')
                    .show();
            }
        });

        $('#session_sel, #category_id, #month, #payment_status, #student_apply_status, #rfid_machine_action, #subscription_status').on('change', function () {
            if ($(this).attr('id') === 'category_id') {
                applyCategoryFilterFlow();
            }
            evaluateUtilityButtonVisibility();

            evaluateUpdateButton();
        });
    });

    function isGateCategorySelected() {
        const opt = $('#category_id option:selected');
        return String(opt.data('name-lower') || '').toLowerCase() === 'gate';
    }

    function isAllCategoriesSelected() {
        const opt = $('#category_id option:selected');
        return String(opt.data('name-lower') || '').toLowerCase() === 'all';
    }

    function isHostelCategorySelected() {
        const opt = $('#category_id option:selected');
        return String(opt.data('name-lower') || '').toLowerCase() === 'hostel';
    }

    function getCategoryFlow() {
        if (isAllCategoriesSelected()) {
            return 'all';
        }
        if (isGateCategorySelected()) {
            return 'gate';
        }
        if (isHostelCategorySelected()) {
            return 'hostel';
        }
        return 'gym';
    }

    function applyCategoryFilterFlow() {
        const flow = getCategoryFlow();
        const currentMonth = "{{ now()->format('m') }}";
        const $hint = $('#categoryFlowHint');
        const $monthGrp = $('#monthFieldGroup');
        const $payGrp = $('#paymentStatusFieldGroup');
        const $subGrp = $('#subscriptionStatusFieldGroup');
        const $gateSt = $('#gateStatusFieldGroup');
        const $gateAct = $('#gateMachineActionGroup');

        if (flow === 'gate' || flow === 'all') {
            $monthGrp.hide();
            $payGrp.hide();
            $subGrp.hide();
            $gateSt.show();
            $gateAct.show();
            $('#month').val('');
            $('#payment_status').val('');
            $('#subscription_status').val('');
            $('#month').attr('title', '');
            $('#monthFieldLabel').text('Month');
            if (flow === 'all') {
                $hint.text('All: same student list as Gate (students_apply_info). Active/Blacklist runs on every group\'s machines (Gate, Gym, Hostel, Lab) that have devices. Table stays empty — pick one category to browse rows.')
                    .show();
            } else {
                $hint.text('Gate flow: list and sync use students_apply_info (student status + machine action). Month, payment and subscription filters are not used here.')
                    .show();
            }
        } else if (flow === 'hostel') {
            $monthGrp.hide();
            $payGrp.show();
            $subGrp.show();
            $gateSt.hide();
            $gateAct.hide();
            $('#student_apply_status').val('');
            $('#rfid_machine_action').val('');
            $('#subscription_status_label').text('Student status');
            $('#monthFieldLabel').text('Month (fee paid on challan)');
            $('#month').attr('title', 'Optional for the grid: limits rows where linked challan CALLBACK_PAID_ON is in this month. Leave empty to list all assignments in session. Sync still needs a month + Paid.');
            $('#month').val('');
            $hint.text('Hostel flow: hostel_assign + challan. Payment filters challan. Student status uses students_apply_info when joined. Month is optional for the table (empty = all in session); paid sync still requires month + Paid.')
                .show();
        } else {
            $monthGrp.show();
            $payGrp.show();
            $subGrp.show();
            $gateSt.hide();
            $gateAct.hide();
            $('#student_apply_status').val('');
            $('#rfid_machine_action').val('');
            $('#subscription_status_label').text('Subscription status');
            $('#monthFieldLabel').text('Month (subscription entry)');
            $('#month').attr('title', 'Gym / other: SUBSCRIPTIONS.ENTRY_DATE month.');
            if (!$('#month').val()) {
                $('#month').val(currentMonth);
            }
            $hint.text('Gym / utilities flow: data from SUBSCRIPTIONS + plans. Month is subscription entry month. Payment and subscription status apply to that table.')
                .show();
        }

        updateSyncPaidButtonLabel();
    }

    function updateSyncPaidButtonLabel() {
        const btn = $('#syncPaidUtilityBtn');
        const labelPaid = btn.attr('data-default-label-paid') || 'Sync Paid Utility';
        const labelGate = btn.attr('data-default-label-gate') || 'Sync Gate RFID';
        const labelHostel = btn.attr('data-default-label-hostel') || 'Sync Hostel RFID';
        const labelAll = btn.attr('data-default-label-all') || 'Sync ALL categories RFID';
        let pick = labelPaid;
        if (getCategoryFlow() === 'gate') {
            pick = labelGate;
        } else if (getCategoryFlow() === 'all') {
            pick = labelAll;
        } else if (getCategoryFlow() === 'hostel') {
            pick = labelHostel;
        }
        btn.attr('data-default-label', pick);
        if (!btn.prop('disabled')) {
            btn.text(btn.attr('data-default-label'));
        }
    }

    function getFilterParams() {
        return {
            session: $('#session_sel').val(),
            category_id: $('#category_id').val(),
            month: $('#month').val(),
            payment_status: $('#payment_status').val(),
            subscription_status: $('#subscription_status').val(),
            student_apply_status: $('#student_apply_status').val(),
            rfid_machine_action: $('#rfid_machine_action').val()
        };
    }

    function refreshFilters() {
        const currentMonth = "{{ now()->format('m') }}";
        const defaultCategoryId = "{{ $defaultCategoryId }}";
        $('#session_sel').val('');
        $('#category_id').val(defaultCategoryId);
        $('#month').val(currentMonth);
        $('#payment_status').val('');
        $('#subscription_status').val('');
        $('#student_apply_status').val('');
        $('#rfid_machine_action').val('');
    }

    function evaluateUtilityButtonVisibility() {
        const f = getFilterParams();
        const flow = getCategoryFlow();
        let isReady;
        if (flow === 'gate' || flow === 'all') {
            isReady = !!f.session && !!f.category_id && !!f.student_apply_status && !!f.rfid_machine_action;
        } else {
            isReady = !!f.session && !!f.category_id && !!f.month && f.payment_status === 'Paid';
        }
        $('#syncPaidUtilityBtn').toggle(isReady);
        const showCheck = flow === 'gate' && !!f.category_id && !!f.student_apply_status;
        $('#checkGateMachinesBtn').toggle(showCheck);
    }

    function resetGateCheckModalForNewRun() {
        gateCheckState.runId = null;
        gateCheckState.total = 0;
        gateCheckState.inFlight = false;
        gateCheckState.stopped = false;
        gateCheckState.regCount = 0;
        gateCheckState.nregCount = 0;
        if (gateCheckState.timer) {
            clearTimeout(gateCheckState.timer);
            gateCheckState.timer = null;
        }
        $('#gateMachineCheckRegistered').val('');
        $('#gateMachineCheckNotRegistered').val('');
        $('#gateMachineCheckRegisteredCount').text('0');
        $('#gateMachineCheckNotRegisteredCount').text('0');
        $('#gateMachineCheckProgress').text('');
        $('#stopGateMachineCheckBtn').prop('disabled', false);
        gateCheckState.lineBuffers = {};
    }

    function flushGateCheckLineBuffer(textareaId) {
        const buf = gateCheckState.lineBuffers[textareaId];
        if (!buf || !buf.length) {
            return;
        }
        const block = buf.join('\n');
        gateCheckState.lineBuffers[textareaId] = [];
        const $ta = $('#' + textareaId);
        const v = $ta.val();
        $ta.val(v + (v ? '\n' : '') + block);
        const el = $ta.get(0);
        if (el) {
            el.scrollTop = el.scrollHeight;
        }
    }

    function flushAllGateCheckLineBuffers() {
        flushGateCheckLineBuffer('gateMachineCheckRegistered');
        flushGateCheckLineBuffer('gateMachineCheckNotRegistered');
    }

    /** Batched appends: full string copy every step was O(n²) and stalled the tab around ~500+ lines. */
    function appendGateCheckLine(textareaId, line) {
        if (!gateCheckState.lineBuffers[textareaId]) {
            gateCheckState.lineBuffers[textareaId] = [];
        }
        gateCheckState.lineBuffers[textareaId].push(line);
        if (gateCheckState.lineBuffers[textareaId].length >= 25) {
            flushGateCheckLineBuffer(textareaId);
        }
    }

    function runGateMachineCheck() {
        const filterParams = getFilterParams();
        if (!isGateCategorySelected() || isAllCategoriesSelected() || !filterParams.category_id || !filterParams.student_apply_status) {
            $('#syncResultBox').removeClass('alert-success alert-info').addClass('alert-danger')
                .text('Select Gate category (not All) and student status (session optional).')
                .show();
            return;
        }
        const btn = $('#checkGateMachinesBtn');
        btn.prop('disabled', true);
        $('#syncResultBox').hide();
        resetGateCheckModalForNewRun();
        $('#gateMachineCheckModal').modal('show');

        $.ajax({
            type: 'POST',
            url: "{{ url('rfid-student-subscriptions/gate-machine-check/start') }}",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                session: filterParams.session,
                category_id: filterParams.category_id,
                student_apply_status: filterParams.student_apply_status,
                subscription_status: filterParams.subscription_status
            },
            success: function (response) {
                gateCheckState.runId = response.run_id;
                gateCheckState.total = Number(response.total || 0);
                const ip = response.machine_ip || '—';
                $('#gateMachineCheckSummary').html(
                    '<strong>Device IP:</strong> ' + escapeHtml(String(ip)) + '<br>'
                    + '<strong>List total:</strong> ' + gateCheckState.total + ' (one <code>step</code> request per student)'
                );
                $('#gateMachineCheckProgress').text('Starting…');
                $('#stopGateMachineCheckBtn').prop('disabled', false);
                scheduleGateMachineCheckStep();
            },
            error: function (xhr) {
                let msg = 'Could not start machine check.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                $('#syncResultBox').removeClass('alert-success alert-info').addClass('alert-danger').text(msg).show();
                btn.prop('disabled', false);
                $('#gateMachineCheckModal').modal('hide');
            }
        });
    }

    function scheduleGateMachineCheckStep() {
        if (gateCheckState.stopped || !gateCheckState.runId) {
            return;
        }
        if (gateCheckState.timer) {
            clearTimeout(gateCheckState.timer);
        }
        gateCheckState.timer = setTimeout(processGateMachineCheckStepOne, 200);
    }

    function processGateMachineCheckStepOne() {
        if (gateCheckState.stopped || !gateCheckState.runId || gateCheckState.inFlight) {
            return;
        }
        gateCheckState.inFlight = true;
        $.ajax({
            type: 'POST',
            url: "{{ url('rfid-student-subscriptions/gate-machine-check/step') }}",
            timeout: 120000,
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                run_id: gateCheckState.runId
            },
            success: function (r) {
                gateCheckState.inFlight = false;
                if (r.stopped) {
                    flushAllGateCheckLineBuffers();
                    $('#gateMachineCheckProgress').text('Stopped by user.');
                    finishGateMachineCheckRun();
                    return;
                }
                if (r.completed) {
                    flushAllGateCheckLineBuffers();
                    $('#gateMachineCheckProgress').text('Done — checked ' + (r.progress && r.progress.total) + ' students.');
                    finishGateMachineCheckRun();
                    return;
                }
                if (r.step) {
                    $('#gateMachineCheckProgress').text(
                        'Checked ' + (r.progress.just_checked || r.progress.current) + ' / ' + r.progress.total
                        + ': ' + r.step.enrollment
                    );
                }
                if (r.append_registered) {
                    appendGateCheckLine('gateMachineCheckRegistered', r.append_registered);
                    gateCheckState.regCount++;
                    $('#gateMachineCheckRegisteredCount').text(String(gateCheckState.regCount));
                }
                if (r.append_not_registered) {
                    appendGateCheckLine('gateMachineCheckNotRegistered', r.append_not_registered);
                    gateCheckState.nregCount++;
                    $('#gateMachineCheckNotRegisteredCount').text(String(gateCheckState.nregCount));
                }
                if (!gateCheckState.stopped) {
                    scheduleGateMachineCheckStep();
                }
            },
            error: function (xhr) {
                gateCheckState.inFlight = false;
                flushAllGateCheckLineBuffers();
                let msg = 'Step failed.';
                if (xhr.statusText === 'timeout') {
                    msg = 'Step timed out (device slow or network). You can start again; consider a smaller session filter.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                $('#gateMachineCheckProgress').text(msg);
                $('#syncResultBox').removeClass('alert-success alert-info').addClass('alert-danger').text(msg).show();
                finishGateMachineCheckRun();
            }
        });
    }

    function finishGateMachineCheckRun() {
        flushAllGateCheckLineBuffers();
        gateCheckState.runId = null;
        if (gateCheckState.timer) {
            clearTimeout(gateCheckState.timer);
            gateCheckState.timer = null;
        }
        $('#checkGateMachinesBtn').prop('disabled', false);
        $('#stopGateMachineCheckBtn').prop('disabled', true);
    }

    function stopGateMachineCheckRun() {
        if (!gateCheckState.runId) {
            return;
        }
        gateCheckState.stopped = true;
        if (gateCheckState.timer) {
            clearTimeout(gateCheckState.timer);
            gateCheckState.timer = null;
        }
        const rid = gateCheckState.runId;
        $.ajax({
            type: 'POST',
            url: "{{ url('rfid-student-subscriptions/gate-machine-check/stop') }}",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                run_id: rid
            },
            complete: function () {
                $('#gateMachineCheckProgress').text('Stop requested…');
                finishGateMachineCheckRun();
            }
        });
    }

    function getPaymentBadge(row) {
        if (row.callback_paid_on && row.sc_challan_status === 'PAID' && row.response_status === 'Success') {
            return '<span class="badge badge-success">Paid</span>';
        }
        if (row.sc_challan_status === 'CANCELLED') {
            return '<span class="badge badge-danger">Cancelled</span>';
        }
        return '<span class="badge badge-warning">Unpaid</span>';
    }

    function getSubscriptionBadge(status) {
        if (status === 'Active') return '<span class="badge badge-success">Active</span>';
        if (status === 'Expired' || status === 'Declined' || status === 'Deactive') return '<span class="badge badge-danger">' + status + '</span>';
        return '<span class="badge badge-warning">' + (status || 'Pending') + '</span>';
    }

    function getRfidAccessBadge(status) {
        return status === 'Active'
            ? '<span class="badge badge-success">Enabled</span>'
            : '<span class="badge badge-secondary">Disabled</span>';
    }

    function loadRfidStudentSubscriptions() {
        let filterParams = getFilterParams();

        $('#rfid-student-subscription-table').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            destroy: true,
            lengthChange: true,
            paging: true,
            pageLength: 100,
            lengthMenu: [100, 200, 300, 400],
            order: [[0, 'desc']],
            ajax: {
                dataType: 'json',
                method: 'post',
                url: "{{ url('rfid-student-subscriptions/list') }}",
                data: function (d) {
                    return $.extend({}, d, filterParams, {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    });
                }
            },
            columns: [
                { data: 'user_id', defaultContent: '' },
                { data: 'sess_name', defaultContent: '' },
                { data: 'student_full_name', defaultContent: '' },
                { data: 'sp_category', defaultContent: '' },
                { data: 'sp_plan_tenure', defaultContent: '' },
                {
                    data: 'current_status',
                    render: function (data) { return getSubscriptionBadge(data); }
                },
                {
                    data: null,
                    render: function (data, type, row) { return getPaymentBadge(row); }
                },
                {
                    data: 'current_status',
                    render: function (data) { return getRfidAccessBadge(data); }
                }
            ]
        });

        $('#rfid-student-subscription-table').off('xhr.dt.hostelEmpty').on('xhr.dt.hostelEmpty', function (e, settings, json) {
            if (!json) {
                return;
            }
            if (json.all_categories_hint) {
                // Do not replace a just-shown "Bulk utility completed" (alert-success) after reload.
                if (!$('#syncResultBox').hasClass('alert-success')) {
                    $('#syncResultBox').removeClass('alert-success alert-danger').addClass('alert-info')
                        .text(json.all_categories_hint).show();
                }
            } else if (json.hostel_empty_hint) {
                if (!$('#syncResultBox').hasClass('alert-success')) {
                    $('#syncResultBox').removeClass('alert-success alert-danger').addClass('alert-info')
                        .text(json.hostel_empty_hint).show();
                }
            } else if (isHostelCategorySelected() && json.data && json.data.length > 0) {
                $('#syncResultBox').hide();
            } else if (!isAllCategoriesSelected() && $('#syncResultBox').hasClass('alert-info')) {
                // Clear stale filter hints (e.g. switched away from "All") without hiding sync success/error (alert-success / alert-danger).
                $('#syncResultBox').hide();
            }
        });
    }

    function resetBulkSyncState() {
        if (bulkSyncState.timer) {
            clearTimeout(bulkSyncState.timer);
            bulkSyncState.timer = null;
        }
        bulkSyncState.runId = null;
        bulkSyncState.paused = false;
        bulkSyncState.stopped = false;
        bulkSyncState.inFlight = false;
        bulkSyncState.machineColors = {};
        bulkSyncState.startedAtMs = 0;
        bulkSyncState.totalOps = 0;
        $('#openSyncLogBtn').hide();
    }

    function isRunActive() {
        return !!bulkSyncState.runId && !bulkSyncState.stopped;
    }

    function escapeHtml(text) {
        return $('<div>').text(text == null ? '' : String(text)).html();
    }

    function getStatusToneClass(label) {
        const l = String(label || '').toLowerCase();
        if (l.indexOf('running') === 0) return 'status-running';
        if (l.indexOf('paused') === 0) return 'status-paused';
        if (l.indexOf('stopped') === 0) return 'status-stopped';
        if (l.indexOf('failed') === 0) return 'status-failed';
        if (l.indexOf('completed') === 0) return 'status-completed';
        return '';
    }

    function setRunStatus(label) {
        const text = String(label || '');
        const parts = text.split('|').map(function (p) { return p.trim(); }).filter(function (p) { return p !== ''; });
        const toneClass = getStatusToneClass(parts[0] || text);

        if (!parts.length) {
            $('#syncRunStatus').html('<span class="status-chip status-main">Idle</span>');
            return;
        }

        let html = '<span class="status-chip status-main ' + toneClass + '">' + escapeHtml(parts[0]) + '</span>';
        for (let i = 1; i < parts.length; i++) {
            const item = parts[i];
            const idx = item.indexOf(':');
            if (idx > -1) {
                const key = item.substring(0, idx).trim();
                const value = item.substring(idx + 1).trim();
                html += '<span class="status-chip"><span class="status-key">' + escapeHtml(key) + ':</span> ' + escapeHtml(value) + '</span>';
            } else {
                html += '<span class="status-chip">' + escapeHtml(item) + '</span>';
            }
        }
        $('#syncRunStatus').html(html);
    }

    function formatEta(ms) {
        const seconds = Math.max(0, Math.ceil(ms / 1000));
        const min = Math.floor(seconds / 60);
        const sec = seconds % 60;
        return min + 'm ' + String(sec).padStart(2, '0') + 's';
    }

    function updateProgressStatus(summary) {
        if (!summary) {
            return;
        }
        const processed = Number(summary.processed || 0);
        const total = Number(bulkSyncState.totalOps || 0);
        const remaining = Math.max(0, total - processed);

        if (processed <= 0 || bulkSyncState.startedAtMs <= 0 || total <= 0) {
            setRunStatus('Running | Total: ' + total + ' | Processed: ' + processed + ' | Remaining: ' + remaining + ' | ETA: calculating...');
            return;
        }

        const elapsedMs = Date.now() - bulkSyncState.startedAtMs;
        const avgPerStepMs = elapsedMs / processed;
        const etaMs = avgPerStepMs * remaining;

        setRunStatus(
            'Running | Total: ' + total
            + ' | Processed: ' + processed
            + ' | Remaining: ' + remaining
            + ' | ETA: ' + formatEta(etaMs)
        );
    }

    function setRunControlButtons(isRunning) {
        $('#pauseResumeSyncBtn').prop('disabled', !isRunning).text('Pause');
        $('#stopSyncBtn').prop('disabled', !isRunning);
        $('#openSyncLogBtn').toggle(!!bulkSyncState.runId);
    }

    function setSyncPaidButtonLoading(isLoading) {
        const btn = $('#syncPaidUtilityBtn');
        updateSyncPaidButtonLabel();
        const defaultLabel = btn.attr('data-default-label') || 'Sync';

        if (isLoading) {
            btn.prop('disabled', true);
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="margin-right:8px;"></span>Running utility...');
            $('#openSyncLogBtn').show();
            return;
        }

        btn.prop('disabled', false);
        btn.text(defaultLabel);
        if (!bulkSyncState.runId) {
            $('#openSyncLogBtn').hide();
        }
    }

    function getMachineColor(machineIp) {
        if (!machineIp) {
            return '#93c5fd';
        }
        if (!bulkSyncState.machineColors[machineIp]) {
            const hue = Math.floor(Math.random() * 360);
            bulkSyncState.machineColors[machineIp] = 'hsl(' + hue + ', 85%, 70%)';
        }
        return bulkSyncState.machineColors[machineIp];
    }

    function appendLiveLog(message, color) {
        const parent = $('#syncLiveLog').parent();
        const distanceFromBottom = parent[0].scrollHeight - parent.scrollTop() - parent.innerHeight();
        const shouldStickToBottom = distanceFromBottom < 40;

        const line = $('<div class="log-line"></div>');
        line.text(message);
        if (color) {
            line.css('color', color);
        }
        $('#syncLiveLog').append(line);

        // Auto-scroll only when user is already near bottom.
        // If user scrolls up to inspect old logs, keep that position.
        if (shouldStickToBottom) {
            parent.scrollTop(parent[0].scrollHeight);
        }
    }

    function appendMachineLogs(machineResults) {
        if (!machineResults || !machineResults.length) {
            return;
        }
        machineResults.forEach(function (item) {
            if ((item.outcome || '') === 'not_found') {
                // Skip "not found" entries from live log as requested.
                return;
            }
            const ip = item.machine_ip || 'unknown-machine';
            const color = getMachineColor(ip);
            const status = (item.status || 'info').toUpperCase();
            const outcomeMap = {
                search_failed: 'Search failed',
                not_found: 'Student not found',
                already_disabled: 'Already disabled',
                disabled: 'Disabled successfully',
                disable_failed: 'Disable failed',
                enabled: 'Enabled successfully',
                enable_failed: 'Enable failed'
            };
            const outcome = outcomeMap[item.outcome] || (item.outcome || 'Step update');
            const message = item.message || '';
            appendLiveLog('[' + ip + '] ' + status + ' | ' + outcome + ' | ' + message, color);
        });
    }

    function normalizeEnrollment(value) {
        return (value || '').toString().replace(/-/g, '').trim().toUpperCase();
    }

    function highlightUpdatedStudentRow(studentId) {
        const table = $('#rfid-student-subscription-table').DataTable();
        const normalizedTarget = normalizeEnrollment(studentId);
        table.rows().every(function () {
            const row = this.data() || {};
            const normalizedUserId = normalizeEnrollment(row.user_id);
            if (normalizedUserId === normalizedTarget) {
                const node = $(this.node());
                node.addClass('just-updated-row');
                setTimeout(function () {
                    node.removeClass('just-updated-row');
                }, 2000);
            }
        });
    }

    function scheduleNextStep() {
        if (bulkSyncState.paused || bulkSyncState.stopped || !bulkSyncState.runId) {
            return;
        }
        if (bulkSyncState.timer) {
            clearTimeout(bulkSyncState.timer);
        }
        bulkSyncState.timer = setTimeout(function () {
            processBulkSyncStep();
        }, 180);
    }

    function togglePauseResume() {
        if (!bulkSyncState.runId || bulkSyncState.stopped) {
            return;
        }
        bulkSyncState.paused = !bulkSyncState.paused;
        if (bulkSyncState.paused) {
            setRunStatus('Paused');
            $('#pauseResumeSyncBtn').text('Resume');
            appendLiveLog('Run paused by user.');
            if (bulkSyncState.timer) {
                clearTimeout(bulkSyncState.timer);
                bulkSyncState.timer = null;
            }
        } else {
            setRunStatus('Running');
            $('#pauseResumeSyncBtn').text('Pause');
            appendLiveLog('Run resumed by user.');
            scheduleNextStep();
        }
    }

    function stopBulkSyncUtility() {
        if (!bulkSyncState.runId || bulkSyncState.stopped) {
            return;
        }
        if (!confirm('Stop current sync run?')) {
            return;
        }
        bulkSyncState.stopped = true;
        if (bulkSyncState.timer) {
            clearTimeout(bulkSyncState.timer);
            bulkSyncState.timer = null;
        }
        setRunStatus('Stopping...');
        $('#pauseResumeSyncBtn').prop('disabled', true);
        $('#stopSyncBtn').prop('disabled', true);
        $.ajax({
            type: 'POST',
            url: "{{ url('rfid-student-subscriptions/bulk-sync/stop') }}",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                run_id: bulkSyncState.runId
            },
            success: function (response) {
                appendLiveLog(response.message || 'Run stopped.');
                setRunStatus('Stopped');
                setSyncPaidButtonLoading(false);
                setRunControlButtons(false);
            },
            error: function (xhr) {
                let msg = 'Unable to stop run.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                appendLiveLog('ERROR: ' + msg);
                setRunStatus('Stop failed');
                setSyncPaidButtonLoading(false);
                setRunControlButtons(false);
            }
        });
    }

    function startBulkSyncUtility() {
        resetBulkSyncState();
        const filterParams = getFilterParams();

        if (getCategoryFlow() === 'gate' || getCategoryFlow() === 'all') {
            if (!filterParams.session || !filterParams.category_id || !filterParams.student_apply_status || !filterParams.rfid_machine_action) {
                $('#syncResultBox').removeClass('alert-success alert-info').addClass('alert-danger')
                    .text('Please select Session, category (Gate or All), Student Status, and Machine action (Active or Blacklist).')
                    .show();
                return;
            }
        } else {
            if (!filterParams.session || !filterParams.category_id || !filterParams.month || filterParams.payment_status !== 'Paid') {
                const flow = getCategoryFlow();
                const extra = flow === 'hostel'
                    ? ' Hostel: month = paid challan month.'
                    : ' Gym: month = subscription entry month.';
                $('#syncResultBox').removeClass('alert-success alert-info').addClass('alert-danger')
                    .text('Please select Session, Category, Month and Payment Status = Paid.' + extra)
                    .show();
                return;
            }
        }

        let confirmMsg = 'This will sync paid users to machines and disable extra active users. Continue?';
        if (getCategoryFlow() === 'all') {
            confirmMsg = 'This will set all matching students on Gate, Gym, Hostel, and Lab machines (where configured) to '
                + (filterParams.rfid_machine_action === 'blacklist' ? 'BLACKLIST' : 'ACTIVE') + '. Continue?';
        } else if (getCategoryFlow() === 'gate') {
            confirmMsg = 'This will set all matching students on the selected category machines to '
                + (filterParams.rfid_machine_action === 'blacklist' ? 'BLACKLIST' : 'ACTIVE') + '. Continue?';
        } else if (getCategoryFlow() === 'hostel') {
            confirmMsg = 'Hostel: enable students with paid hostel challan in the selected month, and disable other active ACL flags for this category where needed. Continue?';
        }
        if (!confirm(confirmMsg)) {
            return;
        }

        setSyncPaidButtonLoading(true);
        $('#syncLiveLog').html('<div class="log-line">Starting bulk utility...</div>');
        $('#bulkSyncModal').modal('show');
        setRunStatus('Preparing run...');
        setRunControlButtons(false);
        $('#syncResultBox').removeClass('alert-danger alert-success').addClass('alert-info')
            .text('Creating sync plan...')
            .show();

        $.ajax({
            type: 'POST',
            url: "{{ url('rfid-student-subscriptions/bulk-sync/start') }}",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                session: filterParams.session,
                category_id: filterParams.category_id,
                month: filterParams.month,
                payment_status: filterParams.payment_status,
                subscription_status: filterParams.subscription_status,
                student_apply_status: filterParams.student_apply_status,
                rfid_machine_action: filterParams.rfid_machine_action
            },
            success: function (response) {
                bulkSyncState.runId = response.run_id;
                bulkSyncState.startedAtMs = Date.now();
                bulkSyncState.totalOps = Number((response.summary.to_disable_count || 0) + (response.summary.to_enable_count || 0));
                appendLiveLog('Run ID: ' + response.run_id);
                appendLiveLog('Category: ' + (response.category_name || '-'));
                appendLiveLog('Total RFID Machines Available for ' + (response.category_name || 'Selected Category') + ': ' + response.machine_count);
                if (response.gate_mode) {
                    appendLiveLog('Gate sync: Student status = ' + (response.student_apply_status || '-') + ' | Machine action = ' + (response.gate_action || '-'));
                    if (response.all_categories_mode) {
                        appendLiveLog('All-categories mode: operations run per student × each group with machines (see log prefixes).');
                        appendLiveLog('Distinct machine IPs in this run: ' + (response.machine_count || 0));
                    } else {
                        appendLiveLog('Scope: Session + Gate subscriptions + selected filters where applicable');
                    }
                    appendLiveLog('Students in scope for this run: ' + response.summary.target_paid_count);
                    appendLiveLog('Students queued for Active: ' + (response.summary.to_enable_count || 0));
                    appendLiveLog('Students queued for Blacklist: ' + (response.summary.to_disable_count || 0));
                } else {
                    appendLiveLog('Disable scope: All sessions (overall active records)');
                    appendLiveLog('Enable scope: Selected session and filters only');
                    appendLiveLog('Students currently active in database (disable step): ' + (response.summary.disable_source_count || 0));
                    appendLiveLog('Paid students found in selected filters: ' + response.summary.target_paid_count);
                    appendLiveLog('Students queued for enable step: ' + response.summary.to_enable_count);
                    appendLiveLog('Students queued for disable step: ' + response.summary.to_disable_count);
                }
                appendLiveLog('Total student operations in this run: ' + bulkSyncState.totalOps);
                appendLiveLog('Remaining student operations: ' + bulkSyncState.totalOps);
                setRunStatus('Running');
                updateProgressStatus(response.summary || {});
                setRunControlButtons(true);
                scheduleNextStep();
            },
            error: function (xhr) {
                let msg = 'Unable to start bulk utility.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                appendLiveLog('ERROR: ' + msg);
                $('#syncResultBox').removeClass('alert-info alert-success').addClass('alert-danger').text(msg).show();
                setSyncPaidButtonLoading(false);
                setRunStatus('Failed to start');
                setRunControlButtons(false);
            }
        });
    }

    function processBulkSyncStep() {
        if (!bulkSyncState.runId || bulkSyncState.paused || bulkSyncState.stopped || bulkSyncState.inFlight) {
            return;
        }
        bulkSyncState.inFlight = true;
        $.ajax({
            type: 'POST',
            url: "{{ url('rfid-student-subscriptions/bulk-sync/step') }}",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                run_id: bulkSyncState.runId
            },
            success: function (response) {
                bulkSyncState.inFlight = false;
                appendLiveLog(response.log_line);
                appendMachineLogs(response.machine_results || []);
                if (response.row_updated && response.student) {
                    highlightUpdatedStudentRow(response.student);
                }
                updateProgressStatus(response.summary || {});

                if (response.completed) {
                    const summary = response.summary || {};
                    appendLiveLog('--- SUMMARY ---');
                    appendLiveLog('Total processed steps: ' + (summary.processed || 0));
                    appendLiveLog('Students enabled on machine: ' + (summary.enabled_done || 0));
                    appendLiveLog('Students disabled on machine: ' + (summary.disabled_done || 0));
                    appendLiveLog('Students found active during disable checks: ' + (summary.machine_active_found || 0));
                    appendLiveLog('Students already disabled on machine: ' + (summary.already_disabled || 0));
                    appendLiveLog('Steps with machine errors: ' + (summary.failed || 0));
                    $('#syncResultBox').removeClass('alert-info alert-danger').addClass('alert-success')
                        .text(response.stopped ? 'Bulk utility stopped.' : 'Bulk utility completed.')
                        .show();
                    setSyncPaidButtonLoading(false);
                    setRunControlButtons(false);
                    setRunStatus(
                        (response.stopped ? 'Stopped' : 'Completed')
                        + ' | Total: ' + (bulkSyncState.totalOps || 0)
                        + ' | Processed: ' + (summary.processed || 0)
                        + ' | Remaining: ' + Math.max(0, (bulkSyncState.totalOps || 0) - Number(summary.processed || 0))
                    );
                    loadRfidStudentSubscriptions();
                    bulkSyncState.runId = null;
                    $('#openSyncLogBtn').hide();
                } else {
                    scheduleNextStep();
                }
            },
            error: function (xhr) {
                bulkSyncState.inFlight = false;
                let msg = 'Bulk utility step failed.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                appendLiveLog('ERROR: ' + msg);
                $('#syncResultBox').removeClass('alert-info alert-success').addClass('alert-danger').text(msg).show();
                setSyncPaidButtonLoading(false);
                setRunControlButtons(false);
                setRunStatus('Failed');
            }
        });
    }
</script>
@stop
