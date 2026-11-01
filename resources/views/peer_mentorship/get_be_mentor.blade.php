@extends('layouts.app')

@section('content')
@include('components.toast-notifications')

<style>
.schol-header {
    background: linear-gradient(135deg, #1e293b 0%, #2d3f55 100%);
    border-radius: 12px; padding: 20px 24px; margin-bottom: 20px;
    display: flex; align-items: center; justify-content: space-between;
}
.schol-header h3 { color: #fff; font-size: 20px; font-weight: 600; margin: 0; }
.schol-header-sub { color: #94a3b8; font-size: 13px; margin-top: 3px; }
.schol-badge-count {
    background: rgba(59,130,246,0.2); color: #93c5fd; font-size: 12px; padding: 5px 12px;
    border-radius: 20px; border: 1px solid rgba(59,130,246,0.3); font-weight: 500;
}
.filters-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px; }
.filters-row { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 12px 14px; }
.filter-field { display: flex; flex-direction: column; min-width: 0; }
.filter-field--session, .filter-field--school { flex: 0 0 200px; max-width: 220px; }
.filters-actions { flex: 0 0 auto; display: flex; align-items: flex-end; padding-bottom: 1px; }
.field-label {
    font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase;
    letter-spacing: 0.5px; display: block; margin-bottom: 5px;
}
.filters-card .filter-control { width: 100%; min-height: 38px; }
.filters-card .textbox {
    width: 100% !important; border: 1px solid #e2e8f0 !important; border-radius: 8px !important;
    box-shadow: none !important; background: #fff !important; height: 38px !important;
}
.filters-card .textbox-focused { border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59,130,246,0.12) !important; }
.filters-card .textbox .textbox-text {
    height: 36px !important; line-height: 36px !important; padding: 0 32px 0 10px !important;
    font-size: 13px !important; color: #334155 !important;
}
.btn-reset {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    min-width: 88px; height: 38px; padding: 0 16px; background: #f8fafc;
    border: 1px solid #e2e8f0; border-radius: 8px; color: #475569;
    font-size: 13px; font-weight: 600; cursor: pointer;
}
.btn-reset:hover { border-color: #3b82f6; color: #2563eb; background: #eff6ff; }
.request-type-tabs {
    display: flex; gap: 10px; margin-bottom: 14px; flex-wrap: wrap;
}
.request-type-tab {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 700;
    cursor: pointer; border: 2px solid #e2e8f0; background: #fff; color: #475569;
    transition: all 0.15s;
}
.request-type-tab.pending { border-color: #fde68a; color: #b45309; background: #fffbeb; }
.request-type-tab.pending.active { border-color: #d97706; background: #f59e0b; color: #fff; }
.request-type-tab.declined { border-color: #fecaca; color: #b91c1c; background: #fef2f2; }
.request-type-tab.declined.active { border-color: #dc2626; background: #dc2626; color: #fff; }
.filter-tabs {
    display: flex; align-items: center; gap: 8px; margin-bottom: 14px; flex-wrap: wrap;
}
.filter-tab {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600;
    cursor: pointer; border: 1.5px solid; transition: all 0.15s; user-select: none; background: none;
}
.filter-tab.all { border-color: #cbd5e1; color: #475569; background: #f8fafc; }
.filter-tab.all.active { border-color: #1e293b; color: #fff !important; background: #1e293b !important; }
.filter-tab.sched { border-color: #bbf7d0; color: #059669; background: #f0fdf4; }
.filter-tab.sched.active { border-color: #059669; color: #fff !important; background: #059669 !important; }
.filter-tab.pending { border-color: #fecaca; color: #dc2626; background: #fef2f2; }
.filter-tab.pending.active { border-color: #dc2626; color: #fff !important; background: #dc2626 !important; }
.filter-tab.done { border-color: #bfdbfe; color: #2563eb; background: #eff6ff; }
.filter-tab.done.active { border-color: #2563eb; color: #fff !important; background: #2563eb !important; }
.filter-tab.passed { border-color: #fca5a5; color: #b91c1c; background: #fef2f2; }
.filter-tab.passed.active { border-color: #b91c1c; color: #fff !important; background: #b91c1c !important; }
.tab-count {
    background: rgba(0,0,0,0.12); border-radius: 10px; padding: 1px 7px;
    font-size: 11px; font-weight: 700;
}
.filter-tab.active .tab-count { background: rgba(255,255,255,0.3); }
.status-declined-pill {
    display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 700;
    padding: 3px 10px; border-radius: 999px; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;
}
.table-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
.table-toolbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 18px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;
}
.table-toolbar-title { font-size: 14px; font-weight: 600; color: #1e293b; }
#beMentorsTable thead th {
    font-size: 11px !important; font-weight: 700 !important; color: #64748b !important;
    text-transform: uppercase !important; letter-spacing: 0.6px !important;
    padding: 11px 13px !important; border-bottom: 2px solid #e2e8f0 !important; white-space: nowrap;
    background: #f8fafc;
}
#beMentorsTable tbody td {
    padding: 11px 13px !important; font-size: 13px !important;
    vertical-align: middle !important; border-bottom: 1px solid #f1f5f9 !important; color: #334155;
}
#beMentorsTable tbody tr:hover td { background: #f8fafc; }
.enrollment-chip {
    font-family: 'Courier New', monospace; font-size: 12px; color: #3b82f6;
    background: rgba(59,130,246,0.08); padding: 3px 9px; border-radius: 5px;
    border: 1px solid rgba(59,130,246,0.2); font-weight: 700; white-space: nowrap; display: inline-block;
}
.mentor-type-pill {
    display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 999px;
    font-size: 11px; font-weight: 700; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569;
}
.mentor-type-pill.is-both { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
.mentor-type-pill.is-academic { background: #f0fdf4; border-color: #bbf7d0; color: #047857; }
.mentor-type-pill.is-admin { background: #fef3c7; border-color: #fde68a; color: #b45309; }
.int-scheduled, .int-pending, .int-expired, .int-interviewed {
    display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600;
    padding: 3px 8px; border-radius: 5px; margin-bottom: 4px; white-space: nowrap;
}
.int-scheduled { color: #059669; background: #f0fdf4; border: 1px solid #bbf7d0; }
.int-pending { color: #dc2626; background: #fef2f2; border: 1px solid #fecaca; }
.int-expired { color: #b91c1c; background: #fef2f2; border: 1px solid #fca5a5; font-weight: 700; }
.int-interviewed { color: #2563eb; background: #eff6ff; border: 1px solid #bfdbfe; font-weight: 700; }
.btn-act {
    display: inline-flex; align-items: center; gap: 4px; padding: 5px 10px; border-radius: 6px;
    font-size: 12px; font-weight: 600; cursor: pointer; border: 1px solid;
    transition: all 0.15s; white-space: nowrap; text-decoration: none; margin: 2px;
}
.btn-act:hover { text-decoration: none; }
.btn-schedule { color: #2563eb; border-color: rgba(37,99,235,0.3); background: rgba(37,99,235,0.06); }
.btn-schedule:hover { background: rgba(37,99,235,0.13); color: #2563eb; }
.btn-interviewed { color: #059669; border-color: rgba(5,150,105,0.3); background: rgba(5,150,105,0.06); }
.btn-interviewed:hover { background: rgba(5,150,105,0.13); color: #059669; }
.btn-approve { color: #059669; border-color: rgba(5,150,105,0.3); background: rgba(5,150,105,0.06); }
.btn-approve:hover { background: rgba(5,150,105,0.13); color: #059669; }
.btn-profile { color: #7c3aed; border-color: rgba(124,58,237,0.3); background: rgba(124,58,237,0.06); }
.btn-profile:hover { background: rgba(124,58,237,0.13); color: #7c3aed; }
.btn-view { color: #0891b2; border-color: rgba(8,145,178,0.3); background: rgba(8,145,178,0.06); }
.btn-view:hover { background: rgba(8,145,178,0.13); color: #0891b2; }
.actions-cell { display: flex; flex-wrap: wrap; gap: 3px; align-items: center; }
.modal-content { border-radius: 12px !important; border: 1px solid #e2e8f0; box-shadow: 0 20px 60px rgba(0,0,0,0.12); }
.modal-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0 !important; padding: 16px 20px; }
.modal-title { font-size: 15px; font-weight: 600; color: #1e293b; }
.modal-body { padding: 20px; }
.modal-footer { border-top: 1px solid #e2e8f0; padding: 14px 20px; background: #f8fafc; border-radius: 0 0 12px 12px !important; }
.info-alert-blue {
    background: rgba(59,130,246,0.07); border: 1px solid rgba(59,130,246,0.2);
    border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #2563eb; margin-bottom: 16px;
}
.info-alert-green {
    background: rgba(5,150,105,0.07); border: 1px solid rgba(5,150,105,0.2);
    border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #059669; margin-bottom: 16px;
}
.section-head {
    font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;
    letter-spacing: 0.6px; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;
}
.form-label-styled {
    display: block; font-size: 11px; font-weight: 600; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
}
.form-control-styled {
    width: 100%; padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 8px;
    background: #f8fafc; font-size: 14px; color: #1e293b;
}
.form-control-styled:focus {
    outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); background: #fff;
}
.mini-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.mini-table thead th {
    padding: 7px 10px; background: #f8fafc; font-size: 11px; font-weight: 600; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; text-align: left;
}
.mini-table tbody td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; color: #334155; }
.timetable-list { display: grid; gap: 10px; }
.timetable-card {
    display: grid; grid-template-columns: minmax(220px, 32%) 1fr; gap: 14px;
    border: 1px solid #e2e8f0; border-radius: 10px; background: #fff; padding: 12px 14px;
}
.timetable-card-head { display: grid; grid-template-columns: 30px 1fr; gap: 10px; align-items: center; }
.timetable-number {
    width: 26px; height: 26px; border-radius: 50%; background: #eff6ff; color: #2563eb;
    font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center;
}
.timetable-course { font-size: 14px; color: #1e293b; font-weight: 700; text-align: center; }
.timetable-teacher { font-size: 12px; color: #64748b; margin-top: 4px; text-align: center; }
.time-slot-wrap { display: grid; gap: 8px; }
.time-slot-chip {
    display: grid; grid-template-columns: 86px 1fr; align-items: center; gap: 10px;
    padding: 8px 10px; border-radius: 10px; background: #f8fafc; border: 1px solid #dbeafe; font-size: 12px;
}
.time-slot-day {
    display: inline-flex; align-items: center; justify-content: center; gap: 5px;
    background: #eff6ff; color: #2563eb; border-radius: 999px; padding: 5px 8px; font-weight: 700;
}
.schedule-status-message {
    display: none; margin-top: 10px; padding: 9px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;
}
.schedule-status-message.is-error { display: block; color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; }
.schedule-status-message.is-ok { display: block; color: #047857; background: #f0fdf4; border: 1px solid #bbf7d0; }
.schedule-status-message.is-info { display: block; color: #2563eb; background: #eff6ff; border: 1px solid #bfdbfe; }
.modal-empty-state { text-align: center; color: #94a3b8; padding: 16px; font-size: 13px; }
.btn-primary-mod, .btn-success-mod, .btn-cancel-mod {
    display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: 8px;
    font-size: 13px; font-weight: 600; cursor: pointer; border: none;
}
.btn-primary-mod { background: #3b82f6; color: #fff; }
.btn-primary-mod:hover { background: #2563eb; }
.btn-success-mod { background: #059669; color: #fff; }
.btn-success-mod:hover { background: #047857; }
.btn-cancel-mod { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.gradebook-status {
    display: none; margin-top: 12px; border-radius: 8px; padding: 9px 12px; font-size: 12px; font-weight: 600;
}
.gradebook-status.is-error { display: block; color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; }
.gradebook-status.is-success { display: block; color: #047857; background: #f0fdf4; border: 1px solid #bbf7d0; }
@media (max-width: 768px) {
    .filter-field--session, .filter-field--school { flex: 1 1 100%; max-width: none; }
    .filters-actions { width: 100%; }
    .filters-actions .btn-reset { width: 100%; }
    .timetable-card { grid-template-columns: 1fr; }
}
</style>

<input type="hidden" id="sess_id">
<input type="hidden" id="school_id">

<div class="container-fluid p-0">
    <div class="schol-header">
        <div>
            <h3><i class="fa fa-user-plus mr-2"></i> Be a Mentor — Pending Requests</h3>
            <div class="schol-header-sub">Review mentor applications (Academic, Admin, or Both) and schedule interviews</div>
        </div>
        <span class="schol-badge-count" id="be-mentor-count-badge">
            <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
        </span>
    </div>

    <div class="filters-card">
        <div class="filters-row">
            <div class="filter-field filter-field--session">
                <label class="field-label" for="facultyWload_sess_id"><i class="fa fa-calendar mr-1"></i> Session</label>
                <div class="filter-control">
                    <input id="facultyWload_sess_id" name="facultyWload_sess_id" class="easyui-combobox"
                           data-options="
                               valueField: 'sess_id', textField: 'sess_name',
                               url: '{{ url('/sessions') }}', method: 'GET',
                               prompt: 'Select session…', panelHeight: 'auto',
                               onLoadSuccess: function() { setDefaultSession(); },
                               onSelect: function(rec) {
                                   $('#sess_id').val(rec.sess_id);
                                   $('#school_id').val('');
                                   $('#facultyWload_school_id').combobox('clear');
                                   if (suppressSessionRequest) return;
                                   fetchBeMentorsData();
                               }">
                </div>
            </div>
            <div class="filter-field filter-field--school">
                <label class="field-label" for="facultyWload_school_id"><i class="fa fa-building-o mr-1"></i> School</label>
                <div class="filter-control">
                    <input id="facultyWload_school_id" name="facultyWload_school_id" class="easyui-combobox"
                           data-options="
                               valueField: 'school_id', textField: 'school_name',
                               method: 'GET', url: '{{ url('/schools') }}',
                               prompt: 'All schools', panelHeight: 'auto',
                               onSelect: function(rec) {
                                   $('#school_id').val(rec.school_id);
                                   fetchBeMentorsData();
                               }">
                </div>
            </div>
            <div class="filters-actions">
                <button type="button" class="btn-reset" onclick="resetBeMentorFilters()">
                    <i class="fa fa-refresh" id="reset-icon-be"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <div class="request-type-tabs">
        <button type="button" class="request-type-tab pending active" data-request-type="pending">
            <i class="fa fa-hourglass-half"></i> Pending requests
            <span class="tab-count" id="request-count-pending">0</span>
        </button>
        <button type="button" class="request-type-tab declined" data-request-type="declined">
            <i class="fa fa-ban"></i> Declined requests
            <span class="tab-count" id="request-count-declined">0</span>
        </button>
    </div>

    <div class="filter-tabs" id="interview-filter-tabs" style="display:none;">
        <span style="font-size:12px;font-weight:600;color:#64748b;margin-right:2px;">Interview:</span>
        <button type="button" class="filter-tab all active" data-filter="all">All <span class="tab-count" id="tab-count-all">0</span></button>
        <button type="button" class="filter-tab sched" data-filter="scheduled">
            <i class="fa fa-calendar-check-o"></i> Scheduled <span class="tab-count" id="tab-count-sched">0</span>
        </button>
        <button type="button" class="filter-tab pending" data-filter="pending">
            <i class="fa fa-calendar-times-o"></i> Not scheduled <span class="tab-count" id="tab-count-pending">0</span>
        </button>
        <button type="button" class="filter-tab done" data-filter="interviewed">
            <i class="fa fa-check-circle"></i> Interviewed <span class="tab-count" id="tab-count-interviewed">0</span>
        </button>
        <button type="button" class="filter-tab passed" data-filter="passed">
            <i class="fa fa-exclamation-circle"></i> Passed date <span class="tab-count" id="tab-count-passed">0</span>
        </button>
    </div>

    <div class="table-card">
        <div class="table-toolbar">
            <span class="table-toolbar-title" id="be-table-title"><i class="fa fa-table mr-1"></i> Pending applications</span>
        </div>
        <div id="beMentorListTable">
            <div class="text-center p-4 text-muted">
                <i class="fa fa-spinner fa-spin fa-2x"></i>
                <div class="mt-2" style="font-size:13px;">Select session to load requests…</div>
            </div>
        </div>
    </div>
</div>

{{-- Schedule Interview Modal --}}
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-calendar text-primary mr-2"></i> Schedule Interview</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="info-alert-blue">
                    <i class="fa fa-info-circle mr-1"></i>
                    Enrollment: <strong id="modal-enrollment-display">—</strong>
                </div>
                <div class="section-head"><i class="fa fa-book"></i> Applied mentor courses</div>
                <div style="border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; margin-bottom:18px;">
                    <table class="mini-table">
                        <thead><tr><th style="width:50px;">#</th><th>Course</th><th style="width:90px;">Grade</th></tr></thead>
                        <tbody id="modal-courses-table"></tbody>
                    </table>
                </div>
                <hr style="border-color:#e2e8f0; margin:18px 0;">
                <div class="section-head"><i class="fa fa-clock-o"></i> Timetable (busy slots)</div>
                <div id="modal-timetable-table" class="timetable-list" style="margin-bottom:18px;"></div>
                <hr style="border-color:#e2e8f0; margin:18px 0;">
                <div class="section-head"><i class="fa fa-calendar-plus-o"></i> Set interview date &amp; time</div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label-styled">Interview date</label>
                        <input type="date" id="interview-date" class="form-control-styled">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-styled">Interview time</label>
                        <input type="time" id="interview-time" class="form-control-styled">
                    </div>
                </div>
                <div id="schedule-conflict-msg" class="schedule-status-message"></div>
                <input type="hidden" id="enrollment_id">
            </div>
            <div class="modal-footer">
                <span id="suMsg" class="mr-auto" style="font-size:13px;"></span>
                <button type="button" class="btn-cancel-mod" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" id="scheduleBtn" class="btn-primary-mod">
                    <i class="fa fa-calendar-check-o"></i> Confirm schedule
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Mark Interviewed Modal --}}
<div class="modal fade" id="markInterviewedModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-check-circle text-success mr-2"></i> Mark interviewed</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="markInterviewedEnrollment">
                <div class="info-alert-green">
                    <i class="fa fa-info-circle mr-1"></i>
                    Marking interview for: <strong id="mark-interviewed-enrollment-display">—</strong>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label-styled">Remarks <span class="text-danger">*</span></label>
                    <textarea id="markInterviewedRemarks" class="form-control-styled" rows="4"
                              placeholder="Enter interview remarks…" style="resize:vertical;"></textarea>
                </div>
                <div id="mark-interviewed-status" class="gradebook-status"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel-mod" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn-success-mod" id="confirmMarkInterviewedBtn">
                    <i class="fa fa-check"></i> Submit
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Approve / Decline Modal --}}
<div class="modal fade" id="remarks-popup" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="remarks-form" method="POST">
                @csrf
                <input type="hidden" name="enrollment" id="mentor-enrollment">
                <input type="hidden" name="sess_id" id="approval-sess-id">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-gavel text-warning mr-2"></i> Approve / Decline</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="info-alert-blue mb-3">
                        <i class="fa fa-info-circle mr-1"></i>
                        Enrollment: <strong id="approval-enrollment-display">—</strong>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label-styled">Status</label>
                            <select name="status" id="status" class="form-control-styled">
                                <option value="Approved">Approve</option>
                                <option value="Declined">Decline</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-styled">Mentor type</label>
                            <select name="mentor_type" id="approval-mentor-type" class="form-control-styled" required>
                                <option value="academic">Academic</option>
                                <option value="admin">Admin</option>
                                <option value="both">Both</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label-styled">Committee remarks <span class="text-danger">*</span></label>
                        <textarea name="remarks" id="remarks" rows="3" class="form-control-styled" required
                                  placeholder="Enter committee remarks…"></textarea>
                    </div>
                    <div id="approval-form-status" class="gradebook-status"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel-mod" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                    <button type="button" id="submitBtn" class="btn-success-mod"><i class="fa fa-check"></i> Submit decision</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script>
var beMentorsData = [];
var beMentorsDt = null;
var defaultSessionId = @json($currentSessionId ?? null);
var defaultSessionApplied = false;
var suppressSessionRequest = false;
var activeRequestType = 'pending';
var activeInterviewFilter = 'all';
var currentModalTimetable = [];
var currentModalTimetableLoaded = false;
var currentModalTimetableLoadFailed = false;

function escapeAttr(value) {
    return String(value || '')
        .replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;')
        .replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function mentorTypeLabel(t) {
    var x = String(t || '').toLowerCase();
    if (x === 'admin') return 'Admin';
    if (x === 'both') return 'Both';
    if (x === 'academic') return 'Academic';
    return t || '—';
}

function mentorTypePillClass(t) {
    var x = String(t || '').toLowerCase();
    if (x === 'both') return 'is-both';
    if (x === 'admin') return 'is-admin';
    return 'is-academic';
}

function normalizeMentorType(t) {
    var x = String(t || 'academic').toLowerCase();
    return (x === 'admin' || x === 'both') ? x : 'academic';
}

function getCourseGrade(c) {
    return c.course_grade || c.COURSE_GRADE || c.gh_grade || c.GH_GRADE || '';
}

function formatCourseGrade(c) {
    var g = String(getCourseGrade(c) || '').trim();
    if (!g) return '<span class="text-muted">—</span>';
    return '<span class="enrollment-chip" style="color:#047857;border-color:#bbf7d0;background:#f0fdf4;">' + escapeAttr(g) + '</span>';
}

function getEffectiveSessionId() {
    return $('#sess_id').val() || $('#facultyWload_sess_id').combobox('getValue') || defaultSessionId || '';
}

function syncBeFilterControlHeights() {
    ['#facultyWload_sess_id', '#facultyWload_school_id'].forEach(function (sel) {
        var $el = $(sel);
        if ($el.length && $el.data('combobox')) {
            try { $el.combobox('resize'); } catch (e) {}
        }
    });
}

function setDefaultSession() {
    if (defaultSessionApplied) return;
    defaultSessionApplied = true;
    if (defaultSessionId) {
        suppressSessionRequest = true;
        $('#facultyWload_sess_id').combobox('setValue', defaultSessionId);
        suppressSessionRequest = false;
        $('#sess_id').val(defaultSessionId);
    }
    syncBeFilterControlHeights();
    fetchBeMentorsData();
}

function resetBeMentorFilters() {
    var icon = document.getElementById('reset-icon-be');
    if (icon) {
        icon.style.transition = 'transform 0.6s ease';
        icon.style.transform = 'rotate(360deg)';
        setTimeout(function () {
            icon.style.transition = 'none';
            icon.style.transform = 'rotate(0deg)';
            setTimeout(function () { icon.style.transition = 'transform 0.6s ease'; }, 50);
        }, 650);
    }
    $('#school_id').val('');
    $('#facultyWload_school_id').combobox('clear');
    fetchBeMentorsData();
}

function fetchBeMentorsList(approvalStatus) {
    return $.ajax({
        url: '{{ route("get-be-mentors-list") }}',
        type: 'GET',
        dataType: 'json',
        data: {
            sess_id: getEffectiveSessionId(),
            school_id: $('#school_id').val() || '',
            approval_status: approvalStatus
        }
    });
}

function updateRequestTypeCounts() {
    $.when(
        fetchBeMentorsList('pending'),
        fetchBeMentorsList('declined')
    ).done(function (pendingRes, declinedRes) {
        var pending = pendingRes[0] || [];
        var declined = declinedRes[0] || [];
        document.getElementById('request-count-pending').textContent = pending.length;
        document.getElementById('request-count-declined').textContent = declined.length;
    });
}

function fetchBeMentorsData() {
    var approvalStatus = activeRequestType === 'declined' ? 'declined' : 'pending';
    var loadingLabel = activeRequestType === 'declined' ? 'declined requests' : 'pending requests';

    $('#be-mentor-count-badge').html('<i class="fa fa-spinner fa-spin mr-1"></i> Loading...');
    $('#beMentorListTable').html(
        '<div class="text-center p-4 text-muted">' +
        '<i class="fa fa-spinner fa-spin fa-2x"></i>' +
        '<div class="mt-2" style="font-size:13px;">Loading ' + loadingLabel + '…</div></div>'
    );

    fetchBeMentorsList(approvalStatus)
        .done(function (data) {
            beMentorsData = data || [];
            updateRequestTypeCounts();
            if (activeRequestType === 'pending') {
                updateInterviewTabCounts();
                document.getElementById('interview-filter-tabs').style.display = beMentorsData.length ? 'flex' : 'none';
                if (activeInterviewFilter !== 'all' && getRowsForInterviewFilter(activeInterviewFilter).length === 0) {
                    setInterviewTabFilter('all', false);
                }
            } else {
                document.getElementById('interview-filter-tabs').style.display = 'none';
            }
            renderBeMentorsTable(activeInterviewFilter);
        })
        .fail(function () {
            $('#be-mentor-count-badge').html('<i class="fa fa-exclamation-triangle mr-1"></i> Error');
            $('#beMentorListTable').html('<div class="text-center py-5 text-danger">Failed to load requests.</div>');
        });
}

function isStudentInterviewed(item) {
    return String(item.interviewed || '0') === '1';
}

function isStudentScheduled(item) {
    return !!item.interview_datetime && !isStudentInterviewed(item) && !isStudentPassedDate(item);
}

function getTodayDateValue() {
    var today = new Date();
    return today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
}

function parseTimeToMinutes(timeValue) {
    var normalized = String(timeValue || '').trim().toUpperCase().replace(/\./g, '');
    var match = normalized.match(/^(\d{1,2}):(\d{2})(?::\d{2})?\s*(AM|PM)?$/);
    if (!match) return null;
    var hours = parseInt(match[1], 10);
    var minutes = parseInt(match[2], 10);
    var meridiem = match[3];
    if (meridiem === 'AM' && hours === 12) hours = 0;
    if (meridiem === 'PM' && hours < 12) hours += 12;
    return (hours * 60) + minutes;
}

function isInterviewSlotPassed(dateValue, timeValue) {
    var today = getTodayDateValue();
    var date = String(dateValue || '').substring(0, 10);
    if (date < today) return true;
    if (date > today) return false;
    var interviewMinutes = parseTimeToMinutes(timeValue);
    if (interviewMinutes === null) return false;
    var now = new Date();
    return interviewMinutes < ((now.getHours() * 60) + now.getMinutes());
}

function isStudentPassedDate(item) {
    return !!item.interview_datetime && !isStudentInterviewed(item) && isInterviewSlotPassed(item.interview_date, item.interview_time);
}

function updateInterviewTabCounts() {
    document.getElementById('tab-count-all').textContent = beMentorsData.length;
    document.getElementById('tab-count-sched').textContent = beMentorsData.filter(isStudentScheduled).length;
    document.getElementById('tab-count-pending').textContent = beMentorsData.filter(function (d) { return !d.interview_datetime; }).length;
    document.getElementById('tab-count-interviewed').textContent = beMentorsData.filter(isStudentInterviewed).length;
    document.getElementById('tab-count-passed').textContent = beMentorsData.filter(isStudentPassedDate).length;
}

function setInterviewTabFilter(filter, doRender) {
    if (doRender === undefined) doRender = true;
    activeInterviewFilter = filter;
    document.querySelectorAll('#interview-filter-tabs .filter-tab').forEach(function (btn) {
        btn.classList.toggle('active', btn.dataset.filter === filter);
    });
    if (doRender) renderBeMentorsTable(filter);
}

function getRowsForInterviewFilter(filter) {
    if (filter === 'scheduled') return beMentorsData.filter(isStudentScheduled);
    if (filter === 'pending') return beMentorsData.filter(function (d) { return !d.interview_datetime; });
    if (filter === 'interviewed') return beMentorsData.filter(isStudentInterviewed);
    if (filter === 'passed') return beMentorsData.filter(isStudentPassedDate);
    return beMentorsData;
}

function setRequestType(type, doFetch) {
    if (doFetch === undefined) doFetch = true;
    activeRequestType = type;
    document.querySelectorAll('.request-type-tab').forEach(function (btn) {
        btn.classList.toggle('active', btn.dataset.requestType === type);
    });
    document.getElementById('be-table-title').innerHTML = type === 'declined'
        ? '<i class="fa fa-table mr-1"></i> Declined applications'
        : '<i class="fa fa-table mr-1"></i> Pending applications';
    if (doFetch) {
        activeInterviewFilter = 'all';
        fetchBeMentorsData();
    }
}

function buildInterviewStatusHtml(item) {
    var isInterviewed = isStudentInterviewed(item);
    var isPast = isStudentPassedDate(item);
    if (item.interview_datetime && isInterviewed) {
        return '<div class="int-interviewed"><i class="fa fa-check-circle"></i> Interviewed: ' + escapeAttr(item.interview_datetime) + '</div>';
    }
    if (item.interview_datetime && isPast) {
        return '<div class="int-expired"><i class="fa fa-exclamation-circle"></i> Date passed: ' + escapeAttr(item.interview_datetime) + '</div>';
    }
    if (item.interview_datetime) {
        return '<div class="int-scheduled"><i class="fa fa-calendar-check-o"></i> ' + escapeAttr(item.interview_datetime) + '</div>';
    }
    return '<div class="int-pending"><i class="fa fa-calendar-times-o"></i> Not scheduled</div>';
}

function renderBeMentorsTable(filter) {
    var isDeclinedView = activeRequestType === 'declined';
    var rows = isDeclinedView ? beMentorsData : getRowsForInterviewFilter(filter || activeInterviewFilter);
    var label = isDeclinedView ? 'declined' : 'pending';

    document.getElementById('be-mentor-count-badge').innerHTML =
        '<i class="fa fa-users mr-1"></i> ' + rows.length + ' ' + label;

    var tbody = '';
    if (!rows.length) {
        var emptyMsg = isDeclinedView
            ? 'No declined requests for this session.'
            : 'No requests match this filter.';
        tbody = '<tr><td colspan="7" class="text-center py-5 text-muted">' +
            '<i class="fa fa-inbox fa-2x mb-2 d-block"></i>' + emptyMsg + '</td></tr>';
    } else {
        rows.forEach(function (item, i) {
            var enr = item.m_enrollment || '';
            var intCol = buildInterviewStatusHtml(item);
            var actions = '<a href="{{ url("reports-generator-student-profile") }}/' + encodeURIComponent(enr) + '" target="_blank" class="btn-act btn-profile"><i class="fa fa-user"></i> Profile</a>';

            if (!isDeclinedView) {
                var isInterviewed = isStudentInterviewed(item);
                var schedLabel = item.interview_datetime ? 'Reschedule' : 'Schedule';
                var interviewTime = item.interview_time ? String(item.interview_time).substring(0, 5) : '';
                if (!isInterviewed) {
                    intCol += '<button type="button" class="btn-act btn-schedule schedule-interview-btn" ' +
                        'data-enrollment="' + escapeAttr(enr) + '" ' +
                        'data-interview-date="' + escapeAttr(item.interview_date) + '" ' +
                        'data-interview-time="' + escapeAttr(interviewTime) + '">' +
                        '<i class="fa fa-calendar"></i> ' + schedLabel + '</button>';
                }
                if (item.interview_datetime && !isInterviewed) {
                    intCol += '<button type="button" class="btn-act btn-interviewed mark-interviewed-btn" data-enrollment="' + escapeAttr(enr) + '">' +
                        '<i class="fa fa-check-circle"></i> Mark interviewed</button>';
                }
                actions = '<button type="button" class="btn-act btn-approve js-open-approval" data-enrollment="' + escapeAttr(enr) + '" data-mentor-type="' + escapeAttr(item.m_mentor_type) + '">' +
                    '<i class="fa fa-gavel"></i> Decide</button>' + actions;
            } else {
                intCol = '<span class="status-declined-pill"><i class="fa fa-ban"></i> Declined</span>';
            }

            tbody += '<tr>' +
                '<td class="text-center text-muted" style="font-size:12px;">' + (i + 1) + '</td>' +
                '<td><span class="enrollment-chip">' + escapeAttr(enr) + '</span></td>' +
                '<td>' + escapeAttr(item.full_name) + '</td>' +
                '<td style="max-width:200px;font-size:12px;">' + escapeAttr(item.m_committee_remarks || '—') + '</td>' +
                '<td><span class="mentor-type-pill ' + mentorTypePillClass(item.m_mentor_type) + '">' + escapeAttr(mentorTypeLabel(item.m_mentor_type)) + '</span></td>' +
                '<td>' + intCol + '</td>' +
                '<td><div class="actions-cell">' + actions + '</div></td></tr>';
        });
    }

    if (beMentorsDt) {
        try { beMentorsDt.destroy(); } catch (e) {}
        beMentorsDt = null;
    }

    $('#beMentorListTable').html(
        '<div style="overflow-x:auto;"><table class="table table-hover mb-0" id="beMentorsTable" style="width:100%">' +
        '<thead><tr>' +
        '<th class="text-center" style="width:40px;">#</th>' +
        '<th>Enrollment</th><th>Full name</th><th>Remarks</th><th>Mentor type</th>' +
        '<th>Interview</th><th>Actions</th>' +
        '</tr></thead><tbody>' + tbody + '</tbody></table></div>'
    );

    beMentorsDt = $('#beMentorsTable').DataTable({
        pageLength: 50,
        lengthMenu: [25, 50, 100],
        order: [[0, 'asc']],
        dom: 'frtip',
        language: { search: '', searchPlaceholder: 'Search…' }
    });
}

function setScheduleStatus(message, type) {
    var el = document.getElementById('schedule-conflict-msg');
    el.className = 'schedule-status-message' + (type ? ' is-' + type : '');
    el.innerHTML = message || '';
}

function setMarkInterviewedStatus(message, type) {
    var el = document.getElementById('mark-interviewed-status');
    el.className = 'gradebook-status' + (type ? ' is-' + type : '');
    el.innerHTML = message || '';
}

function setApprovalFormStatus(message, type) {
    var el = document.getElementById('approval-form-status');
    el.className = 'gradebook-status' + (type ? ' is-' + type : '');
    el.innerHTML = message || '';
}

function getDayNameFromDate(dateValue) {
    var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    return days[new Date(dateValue + 'T00:00:00').getDay()];
}

function findScheduleConflict(dateValue, timeValue) {
    var selectedDay = getDayNameFromDate(dateValue);
    var selectedMinutes = parseTimeToMinutes(timeValue);
    if (selectedMinutes === null) return null;
    for (var i = 0; i < currentModalTimetable.length; i++) {
        var slot = currentModalTimetable[i];
        if ((slot.tt_subject_day || '').toLowerCase() !== selectedDay.toLowerCase()) continue;
        var fromMinutes = parseTimeToMinutes(slot.tt_subject_time_from);
        var toMinutes = parseTimeToMinutes(slot.tt_subject_time_to);
        if (fromMinutes === null || toMinutes === null) continue;
        if (selectedMinutes >= fromMinutes && selectedMinutes < toMinutes) {
            return {
                course: slot.course_cd_name || 'N/A',
                teacher: slot.teachername || 'N/A',
                day: selectedDay,
                from: slot.tt_subject_time_from,
                to: slot.tt_subject_time_to
            };
        }
    }
    return null;
}

function validateSelectedScheduleSlot(showMessages) {
    var date = document.getElementById('interview-date').value;
    var time = document.getElementById('interview-time').value;
    setScheduleStatus('', '');
    if (!date || !time) return { valid: true };
    if (date < getTodayDateValue()) {
        if (showMessages) setScheduleStatus('<i class="fa fa-exclamation-circle"></i> Past dates cannot be selected.', 'error');
        return { valid: false };
    }
    if (!currentModalTimetableLoaded) {
        setScheduleStatus(currentModalTimetableLoadFailed
            ? '<i class="fa fa-exclamation-circle"></i> Timetable could not be loaded.'
            : '<i class="fa fa-info-circle"></i> Timetable is still loading…', currentModalTimetableLoadFailed ? 'error' : 'info');
        return { valid: false };
    }
    var conflict = findScheduleConflict(date, time);
    if (conflict) {
        if (showMessages) {
            setScheduleStatus(
                '<i class="fa fa-ban"></i> Student is busy: <strong>' + escapeAttr(conflict.course) + '</strong> with ' +
                escapeAttr(conflict.teacher) + ' on ' + escapeAttr(conflict.day) + ', ' +
                escapeAttr(conflict.from) + ' to ' + escapeAttr(conflict.to) + '.', 'error');
        }
        return { valid: false };
    }
    if (showMessages) setScheduleStatus('<i class="fa fa-check-circle"></i> Student is available at this time.', 'ok');
    return { valid: true };
}

function renderGroupedTimetable(timetable) {
    if (!timetable || !timetable.length) return '<div class="modal-empty-state">No timetable found.</div>';
    var dayOrder = { Monday:1, Tuesday:2, Wednesday:3, Thursday:4, Friday:5, Saturday:6, Sunday:7 };
    var grouped = [], groupIndex = {};
    timetable.forEach(function (t) {
        var key = (t.course_cd_name || 'N/A') + '|' + (t.teachername || 'N/A');
        if (groupIndex[key] === undefined) {
            groupIndex[key] = grouped.length;
            grouped.push({ course: t.course_cd_name || 'N/A', teacher: t.teachername || 'N/A', slots: [] });
        }
        grouped[groupIndex[key]].slots.push({
            day: t.tt_subject_day || 'N/A',
            from: t.tt_subject_time_from || 'N/A',
            to: t.tt_subject_time_to || 'N/A'
        });
    });
    return grouped.map(function (group, i) {
        group.slots.sort(function (a, b) {
            return (dayOrder[a.day] || 99) - (dayOrder[b.day] || 99) || String(a.from).localeCompare(String(b.from));
        });
        var slotsHtml = group.slots.map(function (slot) {
            return '<div class="time-slot-chip"><span class="time-slot-day"><i class="fa fa-calendar-o"></i> ' + escapeAttr(slot.day) + '</span>' +
                '<span><i class="fa fa-clock-o text-primary"></i> ' + escapeAttr(slot.from) + ' to ' + escapeAttr(slot.to) + '</span></div>';
        }).join('');
        return '<div class="timetable-card"><div class="timetable-card-head">' +
            '<span class="timetable-number">' + (i + 1) + '</span><div>' +
            '<div class="timetable-course">' + escapeAttr(group.course) + '</div>' +
            '<div class="timetable-teacher"><i class="fa fa-user-o"></i> ' + escapeAttr(group.teacher) + '</div></div></div>' +
            '<div class="time-slot-wrap">' + slotsHtml + '</div></div>';
    }).join('');
}

function openScheduleModal(enrollmentNo, interviewDate, interviewTime) {
    currentModalTimetable = [];
    currentModalTimetableLoaded = false;
    currentModalTimetableLoadFailed = false;
    document.getElementById('interview-date').setAttribute('min', getTodayDateValue());
    document.getElementById('enrollment_id').value = enrollmentNo;
    document.getElementById('modal-enrollment-display').textContent = enrollmentNo;
    document.getElementById('interview-date').value = interviewDate || '';
    document.getElementById('interview-time').value = interviewTime || '';
    document.getElementById('suMsg').textContent = '';
    setScheduleStatus('', '');
    document.getElementById('modal-courses-table').innerHTML =
        '<tr><td colspan="3" class="text-center text-muted py-3"><i class="fa fa-spinner fa-spin"></i> Loading…</td></tr>';
    document.getElementById('modal-timetable-table').innerHTML =
        '<div class="modal-empty-state"><i class="fa fa-spinner fa-spin"></i> Loading timetable…</div>';
    $('#detailModal').modal('show');

    fetch('{{ route("be_mentors_list_courses") }}?md_enrollment_no=' + encodeURIComponent(enrollmentNo))
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (!data.success) throw new Error(data.message || 'Failed');
            document.getElementById('modal-courses-table').innerHTML = !data.courses.length
                ? '<tr><td colspan="3" class="text-center text-muted py-3">No courses found.</td></tr>'
                : data.courses.map(function (c, i) {
                    return '<tr><td>' + (i + 1) + '</td><td>' + escapeAttr(c.course_title) + '</td><td>' + formatCourseGrade(c) + '</td></tr>';
                }).join('');
            currentModalTimetable = data.timetable || [];
            currentModalTimetableLoaded = true;
            document.getElementById('modal-timetable-table').innerHTML = renderGroupedTimetable(data.timetable);
            validateSelectedScheduleSlot(false);
        })
        .catch(function () {
            currentModalTimetableLoadFailed = true;
            document.getElementById('modal-courses-table').innerHTML =
                '<tr><td colspan="3" class="text-danger text-center py-2">Failed to load.</td></tr>';
            document.getElementById('modal-timetable-table').innerHTML =
                '<div class="modal-empty-state text-danger">Failed to load timetable.</div>';
        });
}

function openRemarksPopup(enrollment, mentorType) {
    document.getElementById('mentor-enrollment').value = enrollment;
    document.getElementById('approval-sess-id').value = getEffectiveSessionId();
    document.getElementById('approval-enrollment-display').textContent = enrollment;
    document.getElementById('approval-mentor-type').value = normalizeMentorType(mentorType);
    document.getElementById('remarks').value = '';
    document.getElementById('status').value = 'Approved';
    setApprovalFormStatus('', '');
    $('#remarks-popup').modal('show');
}

$(document).ready(function () {
    setTimeout(syncBeFilterControlHeights, 80);
    $(window).on('resize.beFilters', syncBeFilterControlHeights);
});

$(document).on('click', '.request-type-tab', function () {
    var type = $(this).attr('data-request-type');
    if (type && type !== activeRequestType) setRequestType(type);
});

$(document).on('click', '#interview-filter-tabs .filter-tab', function () {
    setInterviewTabFilter($(this).data('filter'));
});

$(document).on('click', '.schedule-interview-btn', function () {
    openScheduleModal($(this).data('enrollment'), $(this).data('interviewDate') || '', $(this).data('interviewTime') || '');
});

$(document).on('click', '.js-open-approval', function () {
    openRemarksPopup($(this).data('enrollment'), $(this).data('mentorType'));
});

$(document).on('click', '.mark-interviewed-btn', function () {
    var enr = $(this).data('enrollment');
    $('#markInterviewedEnrollment').val(enr);
    $('#mark-interviewed-enrollment-display').text(enr);
    $('#markInterviewedRemarks').val('');
    setMarkInterviewedStatus('', '');
    $('#markInterviewedModal').modal('show');
});

$('#interview-date, #interview-time').on('change input', function () {
    validateSelectedScheduleSlot(true);
});

$('#detailModal').on('hidden.bs.modal', function () {
    currentModalTimetable = [];
    currentModalTimetableLoaded = false;
    currentModalTimetableLoadFailed = false;
    document.getElementById('enrollment_id').value = '';
    document.getElementById('interview-date').value = '';
    document.getElementById('interview-time').value = '';
    document.getElementById('suMsg').textContent = '';
    setScheduleStatus('', '');
});

document.getElementById('scheduleBtn').addEventListener('click', function () {
    var date = document.getElementById('interview-date').value;
    var time = document.getElementById('interview-time').value;
    var enrollmentNo = document.getElementById('enrollment_id').value;
    var sessionId = getEffectiveSessionId();
    if (!date || !time) {
        if (typeof showToast === 'function') showToast('error', 'Please select both date and time.', 'Validation');
        else alert('Please select both date and time.');
        return;
    }
    var validation = validateSelectedScheduleSlot(true);
    if (!validation.valid) {
        if (typeof showToast === 'function') showToast('error', 'Student is busy or timetable not verified.', 'Validation');
        return;
    }
    fetch('{{ route("schedule-interview") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ data: { is_interview_date: date, is_interview_time: time, enrollmentNo: enrollmentNo, sess_id: sessionId } })
    })
    .then(function (r) { return r.json(); })
    .then(function (resp) {
        if (typeof showToast === 'function') showToast('success', resp.message || 'Interview scheduled.', 'Success');
        $('#detailModal').modal('hide');
        fetchBeMentorsData();
    })
    .catch(function () {
        if (typeof showToast === 'function') showToast('error', 'Failed to schedule interview.', 'Error');
    });
});

$('#confirmMarkInterviewedBtn').on('click', function () {
    var enrollmentNo = $('#markInterviewedEnrollment').val();
    var remarks = $('#markInterviewedRemarks').val().trim();
    var sessionId = getEffectiveSessionId();
    if (!remarks) {
        setMarkInterviewedStatus('<i class="fa fa-exclamation-circle"></i> Remarks are required.', 'error');
        return;
    }
    $.ajax({
        url: '{{ route("mark-interviewed") }}',
        type: 'POST',
        dataType: 'json',
        data: { _token: '{{ csrf_token() }}', enrollment_no: enrollmentNo, sess_id: sessionId, remarks: remarks },
        success: function (res) {
            if (res.success) {
                if (typeof showToast === 'function') showToast('success', res.message || 'Marked as interviewed.', 'Success');
                $('#markInterviewedModal').modal('hide');
                fetchBeMentorsData();
            } else {
                setMarkInterviewedStatus(escapeAttr(res.message || 'Failed.'), 'error');
            }
        },
        error: function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed.';
            setMarkInterviewedStatus(escapeAttr(msg), 'error');
        }
    });
});

document.getElementById('submitBtn').addEventListener('click', function (e) {
    e.preventDefault();
    var remarks = document.getElementById('remarks').value.trim();
    if (!remarks) {
        setApprovalFormStatus('<i class="fa fa-exclamation-circle"></i> Remarks are required.', 'error');
        return;
    }
    var formData = new FormData(document.getElementById('remarks-form'));
    fetch('{{ route("update-approval-status") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.success === false) {
            setApprovalFormStatus(escapeAttr(data.message || 'Failed.'), 'error');
            return;
        }
        if (typeof showToast === 'function') showToast('success', data.message || 'Updated.', 'Success');
        $('#remarks-popup').modal('hide');
        fetchBeMentorsData();
    })
    .catch(function () {
        setApprovalFormStatus('<i class="fa fa-exclamation-circle"></i> Failed to update status.', 'error');
    });
});
</script>
@endsection
