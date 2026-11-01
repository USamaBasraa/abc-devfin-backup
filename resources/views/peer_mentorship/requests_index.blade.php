@extends('layouts.app')

@section('content')
@include('components.toast-notifications')

<style>
.pm-req-header {
    background: linear-gradient(135deg, #1e293b 0%, #2d3f55 100%);
    border-radius: 12px; padding: 20px 24px; margin-bottom: 20px;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
}
.pm-req-header h3 { color: #fff; font-size: 20px; font-weight: 600; margin: 0; }
.pm-req-header-sub { color: #94a3b8; font-size: 13px; margin-top: 3px; }
.pm-req-badge {
    background: rgba(59,130,246,0.2); color: #93c5fd; font-size: 12px; padding: 5px 12px;
    border-radius: 20px; border: 1px solid rgba(59,130,246,0.3); font-weight: 500; white-space: nowrap;
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
.status-tabs { display: flex; gap: 10px; margin-bottom: 14px; flex-wrap: wrap; }
.status-tab {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 700;
    cursor: pointer; border: 2px solid #e2e8f0; background: #fff; color: #475569;
    transition: all 0.15s;
}
.status-tab.active { border-color: #1e293b; background: #1e293b; color: #fff; }
.status-tab.active-hold { border-color: #d97706; background: #f59e0b; color: #fff; }
.tab-count {
    background: rgba(0,0,0,0.12); border-radius: 10px; padding: 1px 7px;
    font-size: 11px; font-weight: 700;
}
.status-tab.active .tab-count,
.status-tab.active-hold .tab-count { background: rgba(255,255,255,0.3); }
.table-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
.table-toolbar {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
    padding: 14px 18px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;
}
.table-toolbar-title { font-size: 14px; font-weight: 600; color: #1e293b; }
#studentRequestsTable thead th {
    font-size: 11px !important; font-weight: 700 !important; color: #64748b !important;
    text-transform: uppercase !important; letter-spacing: 0.6px !important;
    padding: 11px 13px !important; border-bottom: 2px solid #e2e8f0 !important; white-space: nowrap;
    background: #f8fafc;
}
#studentRequestsTable tbody td {
    padding: 11px 13px !important; font-size: 13px !important;
    vertical-align: middle !important; border-bottom: 1px solid #f1f5f9 !important; color: #334155;
}
#studentRequestsTable tbody tr:hover td { background: #f8fafc; }
.enrollment-chip {
    font-family: 'Courier New', monospace; font-size: 12px; color: #3b82f6;
    background: rgba(59,130,246,0.08); padding: 3px 9px; border-radius: 5px;
    border: 1px solid rgba(59,130,246,0.2); font-weight: 700; white-space: nowrap; display: inline-block;
}
.btn-act {
    display: inline-flex; align-items: center; gap: 4px; padding: 5px 10px; border-radius: 6px;
    font-size: 12px; font-weight: 600; cursor: pointer; border: 1px solid;
    transition: all 0.15s; white-space: nowrap; text-decoration: none; margin: 2px;
}
.btn-act:hover { text-decoration: none; }
.btn-assign { color: #2563eb; border-color: rgba(37,99,235,0.3); background: rgba(37,99,235,0.06); }
.btn-assign:hover { background: rgba(37,99,235,0.13); color: #2563eb; }
.btn-hold { color: #b45309; border-color: rgba(217,119,6,0.3); background: rgba(245,158,11,0.08); }
.btn-hold:hover { background: rgba(245,158,11,0.15); color: #b45309; }
.btn-unhold { color: #059669; border-color: rgba(5,150,105,0.3); background: rgba(5,150,105,0.06); }
.btn-unhold:hover { background: rgba(5,150,105,0.13); color: #059669; }
.btn-profile { color: #7c3aed; border-color: rgba(124,58,237,0.3); background: rgba(124,58,237,0.06); }
.btn-profile:hover { background: rgba(124,58,237,0.13); color: #7c3aed; }
.actions-cell { display: flex; flex-wrap: wrap; gap: 3px; align-items: center; }
.empty-state {
    text-align: center; padding: 40px 20px; color: #94a3b8; font-size: 13px;
}
.empty-state i { font-size: 28px; margin-bottom: 10px; display: block; }
#studentRequestsTable td.sr-col { text-align: center; color: #64748b; font-weight: 600; width: 48px; }
.hold-confirm-modal { border-radius: 14px !important; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 20px 60px rgba(15,23,42,0.18); }
.hold-confirm-modal .modal-body { padding: 28px 24px 20px; text-align: center; }
.hold-confirm-icon {
    width: 64px; height: 64px; border-radius: 50%; margin: 0 auto 16px;
    display: flex; align-items: center; justify-content: center; font-size: 26px;
}
.hold-confirm-icon.is-hold { background: #fffbeb; color: #d97706; border: 2px solid #fde68a; }
.hold-confirm-icon.is-resume { background: #f0fdf4; color: #059669; border: 2px solid #bbf7d0; }
.hold-confirm-title { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.hold-confirm-text { font-size: 13px; color: #64748b; line-height: 1.5; margin-bottom: 14px; }
.hold-confirm-enrollment {
    display: inline-block; font-family: 'Courier New', monospace; font-size: 13px; font-weight: 700;
    color: #2563eb; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 6px 14px;
}
.hold-confirm-modal .modal-footer {
    border-top: 1px solid #e2e8f0; background: #f8fafc; padding: 14px 20px;
    justify-content: center; gap: 10px;
}
.btn-confirm-cancel {
    min-width: 100px; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600;
    background: #fff; border: 1px solid #e2e8f0; color: #475569; cursor: pointer;
}
.btn-confirm-cancel:hover { background: #f8fafc; border-color: #cbd5e1; }
.btn-confirm-action {
    min-width: 130px; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600;
    border: none; color: #fff; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 6px;
}
.btn-confirm-action.is-hold { background: #f59e0b; }
.btn-confirm-action.is-hold:hover { background: #d97706; }
.btn-confirm-action.is-resume { background: #059669; }
.btn-confirm-action.is-resume:hover { background: #047857; }
.btn-confirm-action:disabled { opacity: 0.65; cursor: not-allowed; }
.btn-view-details { color: #0891b2; border-color: rgba(8,145,178,0.3); background: rgba(8,145,178,0.06); }
.btn-view-details:hover { background: rgba(8,145,178,0.13); color: #0891b2; }
.btn-marks-history { color: #7c3aed; border-color: rgba(124,58,237,0.3); background: rgba(124,58,237,0.06); }
.btn-marks-history:hover { background: rgba(124,58,237,0.13); color: #7c3aed; }
.rc-modal .modal-dialog { max-width: 1140px; margin: 1.25rem auto; }
.rc-modal .modal-content {
    border-radius: 14px; border: none; overflow: hidden;
    box-shadow: 0 24px 64px rgba(15,23,42,0.2);
}
.rc-modal-header {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px;
}
.rc-modal-header .modal-title { color: #fff; font-size: 16px; font-weight: 600; margin: 0; }
.rc-modal-header .close { color: #fff; opacity: 0.85; text-shadow: none; font-size: 26px; margin: 0; }
.rc-modal-header .close:hover { opacity: 1; color: #fff; }
.rc-modal-enrollment {
    font-family: 'Courier New', monospace; font-size: 12px; font-weight: 700;
    color: #93c5fd; background: rgba(59,130,246,0.15); border: 1px solid rgba(147,197,253,0.35);
    border-radius: 8px; padding: 4px 10px; margin-left: 8px;
}
.rc-modal-body { padding: 0; background: #f8fafc; max-height: calc(100vh - 140px); overflow: hidden; display: flex; flex-direction: column; }
.rc-panel { display: none; flex-direction: column; flex: 1; min-height: 0; overflow: hidden; }
.rc-panel.is-active { display: flex; }
.rc-panel-toolbar {
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
    padding: 12px 18px; background: #fff; border-bottom: 1px solid #e2e8f0;
}
.rc-back-btn {
    display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px;
    border-radius: 8px; border: 1px solid #e2e8f0; background: #fff;
    font-size: 12px; font-weight: 600; color: #475569; cursor: pointer;
}
.rc-back-btn:hover { border-color: #3b82f6; color: #2563eb; background: #eff6ff; }
.rc-panel-title { font-size: 14px; font-weight: 700; color: #1e293b; margin: 0; flex: 1; }
.rc-panel-title small { display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-top: 2px; }
.rc-panel-scroll { flex: 1; overflow: auto; padding: 0; }
.rc-table-wrap { background: #fff; margin: 14px 18px; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
#rcCoursesTable { margin: 0; }
#rcCoursesTable thead th {
    font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase;
    letter-spacing: 0.55px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;
    white-space: nowrap; padding: 10px 12px;
}
#rcCoursesTable tbody td {
    font-size: 13px; vertical-align: middle; border-bottom: 1px solid #f1f5f9;
    padding: 10px 12px; color: #334155;
}
#rcCoursesTable tbody tr:hover td { background: #f8fafc; }
#rcCoursesTable tbody tr:last-child td { border-bottom: none; }
.rc-marks-panel { padding: 14px 18px 18px; }
.rc-marks-card {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
    padding: 0; min-height: 120px; overflow: hidden;
}
.rc-marks-card #rcMarksDetails { overflow-x: auto; }
#rcMarksDetails .modal-header,
#rcMarksDetails .close,
#rcMarksDetails button.close { display: none !important; }
#rcMarksDetails .pm-marks-detail { margin: 0; }
#rcMarksDetails .pm-marks-table {
    width: 100%; border-collapse: separate; border-spacing: 0;
    border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;
    background: #fff; font-size: 13px;
}
#rcMarksDetails .pm-marks-table thead th {
    background: #f1f5f9; color: #475569; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.5px;
    padding: 11px 14px; border-bottom: 2px solid #e2e8f0; white-space: nowrap;
}
#rcMarksDetails .pm-marks-table tbody td {
    padding: 11px 14px; color: #334155; border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
#rcMarksDetails .pm-marks-table tbody tr:last-child td { border-bottom: none; }
#rcMarksDetails .pm-marks-table tbody tr:hover td { background: #f8fafc; }
#rcMarksDetails .pm-marks-table tbody tr.is-muted td {
    background: #f8fafc; color: #94a3b8;
}
#rcMarksDetails .pm-marks-obtained {
    display: inline-block; min-width: 36px; font-weight: 700; color: #1e293b;
}
#rcMarksDetails .card-block,
#rcMarksDetails .table-border-style,
#rcMarksDetails .table-responsive { padding: 0; margin: 0; border: none; background: transparent; }
#rcMarksDetails .table.table-bordered { border: none; margin: 0; }
#rcMarksDetails .table-primary th { background: #f1f5f9 !important; color: #475569 !important; }
.rc-mentors-panel { padding: 0 18px 18px; }
.rc-mentors-card {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;
}
#rcMentorsTable thead th {
    font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase;
    letter-spacing: 0.5px; background: #f8fafc; padding: 10px 12px;
}
#rcMentorsTable tbody td { padding: 10px 12px; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
.rc-mentor-row { cursor: pointer; transition: background 0.12s; }
.rc-mentor-row:hover td { background: #f8fafc; }
.rc-mentor-row.is-expanded td { background: #eff6ff; }
.rc-mentor-detail-row td { background: #f8fafc !important; padding: 0 !important; }
.rc-mentor-detail-inner { padding: 16px 18px; border-top: 1px dashed #cbd5e1; }
.rc-availability-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
}
@media (max-width: 900px) {
    .rc-availability-grid { grid-template-columns: 1fr; }
}
.rc-avail-col-title {
    font-size: 13px; font-weight: 700; color: #1e293b; margin: 0 0 6px;
    display: flex; align-items: center; gap: 6px;
}
.rc-avail-hint { font-size: 11px; color: #64748b; margin: 0 0 10px; line-height: 1.4; }
.rc-slot-legend {
    display: flex; flex-wrap: wrap; gap: 10px 16px; margin-bottom: 14px;
    padding: 10px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
    font-size: 11px; color: #475569;
}
.rc-slot-legend span { display: inline-flex; align-items: center; gap: 6px; }
.rc-slot-legend i {
    width: 12px; height: 12px; border-radius: 3px; display: inline-block; border: 1px solid rgba(0,0,0,0.08);
}
.rc-slot-legend .lg-free { background: #bbf7d0; }
.rc-slot-legend .lg-busy { background: #fecaca; }
.rc-slot-legend .lg-partial { background: #fef08a; }
.rc-slot-legend .lg-student { background: #e2e8f0; }
.rc-avail-mini-table {
    width: 100%; border-collapse: separate; border-spacing: 0;
    border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; font-size: 12px;
}
.rc-avail-mini-table thead th {
    background: #f8fafc; color: #64748b; font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.4px; padding: 8px 10px;
    border-bottom: 1px solid #e2e8f0;
}
.rc-avail-mini-table tbody td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.rc-avail-mini-table tbody tr:last-child td { border-bottom: none; }
.rc-avail-mini-table tr.slot-free td { background: #f0fdf4; color: #166534; }
.rc-avail-mini-table tr.slot-busy td { background: #fef2f2; color: #991b1b; }
.rc-avail-mini-table tr.slot-partial td { background: #fefce8; color: #854d0e; }
.rc-avail-mini-table tr.slot-student-busy td { background: #fef2f2; color: #991b1b; }
.rc-avail-mini-table tr.slot-student-partial td { background: #fefce8; color: #854d0e; }
.rc-avail-mini-table tr.slot-student-free td { background: #f1f5f9; color: #64748b; }
.rc-avail-mini-table tr.slot-student-class td { background: #f1f5f9; color: #475569; }
.rc-intimation-bar {
    margin-top: 14px; padding-top: 14px; border-top: 1px dashed #cbd5e1;
    display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px;
}
.rc-intimation-hint { font-size: 12px; color: #64748b; margin: 0; }
.rc-btn-intimation {
    display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px;
    font-size: 12px; font-weight: 600; border: none; cursor: pointer;
    background: #2563eb; color: #fff;
}
.rc-btn-intimation:disabled { opacity: 0.55; cursor: not-allowed; }
.rc-slot-schedule { max-height: 320px; overflow-y: auto; padding-right: 4px; }
.rc-day-group {
    border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 10px; overflow: hidden; background: #fff;
}
.rc-day-group:last-child { margin-bottom: 0; }
.rc-day-head {
    background: #f1f5f9; padding: 8px 12px; font-size: 12px; font-weight: 700; color: #334155;
    border-bottom: 1px solid #e2e8f0;
}
.rc-day-slot {
    display: flex; align-items: center; gap: 10px; padding: 8px 12px; border-bottom: 1px solid #f1f5f9;
    font-size: 12px;
}
.rc-day-slot:last-child { border-bottom: none; }
.rc-day-slot.slot-free { background: #f0fdf4; }
.rc-day-slot.slot-busy { background: #fef2f2; }
.rc-day-slot.slot-partial { background: #fefce8; }
.rc-day-slot.slot-student-busy { background: #fef2f2; }
.rc-day-slot.slot-student-partial { background: #fefce8; }
.rc-day-slot.slot-student-free { background: #f0fdf4; }
.rc-day-slot-check { flex: 0 0 18px; display: flex; align-items: center; justify-content: center; }
.rc-day-slot-time {
    flex: 1; display: flex; align-items: center; justify-content: space-between; gap: 8px;
    color: #334155; font-weight: 500; flex-wrap: wrap;
}
.rc-day-slot-time .rc-time-sep { color: #94a3b8; margin: 0 6px; }
.rc-slot-badge {
    flex: 0 0 auto; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 10px;
    text-transform: uppercase; letter-spacing: 0.3px;
}
.rc-slot-badge.b-free { background: #dcfce7; color: #166534; }
.rc-slot-badge.b-busy { background: #fee2e2; color: #991b1b; }
.rc-slot-badge.b-partial { background: #fef9c3; color: #854d0e; }
.rc-slot-summary {
    display: flex; flex-wrap: wrap; gap: 8px 12px; margin-bottom: 10px;
    padding: 8px 10px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
    font-size: 11px; color: #475569;
}
.rc-slot-summary strong { color: #1e293b; font-weight: 700; }
.rc-slot-summary .sum-free strong { color: #166534; }
.rc-slot-summary .sum-partial strong { color: #854d0e; }
.rc-slot-summary .sum-busy strong { color: #991b1b; }
.rc-loading, .rc-preloader { text-align: center; padding: 40px 20px; color: #94a3b8; font-size: 13px; }
.rc-modal-footer {
    border-top: 1px solid #e2e8f0; background: #fff; padding: 12px 18px;
    display: flex; justify-content: flex-end;
}
@media (max-width: 768px) {
    .filter-field--session, .filter-field--school { flex: 1 1 100%; max-width: none; }
    .filters-actions { width: 100%; }
    .filters-actions .btn-reset { width: 100%; }
}
</style>

<input type="hidden" id="sess_id">
<input type="hidden" id="school_id">

<div class="container-fluid p-0">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="pm-req-header">
        <div>
            <h3><i class="fa fa-users mr-2"></i> Students Request for Mentor</h3>
            <div class="pm-req-header-sub">Review mentor requests by session and school — view details or put on hold</div>
        </div>
        <span class="pm-req-badge" id="requests-count-badge">
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
                                   loadRequests(activeStatus);
                                   updateStatusCounts();
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
                                   loadRequests(activeStatus);
                               }">
                </div>
            </div>
            <div class="filters-actions">
                <button type="button" class="btn-reset" onclick="resetRequestFilters()">
                    <i class="fa fa-refresh" id="reset-icon-req"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <div class="status-tabs">
        <button type="button" class="status-tab active" data-status="E" id="tab-active">
            <i class="fa fa-check-circle"></i> Active
            <span class="tab-count" id="count-active">0</span>
        </button>
        <button type="button" class="status-tab" data-status="H" id="tab-hold">
            <i class="fa fa-pause-circle"></i> On Hold
            <span class="tab-count" id="count-hold">0</span>
        </button>
    </div>

    <div class="table-card">
        <div class="table-toolbar">
            <span class="table-toolbar-title" id="table-toolbar-title">Active mentor requests</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="studentRequestsTable" style="width:100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Enrollment</th>
                        <th>Student name</th>
                        <th>Program</th>
                        <th>Department</th>
                        <th>Profile</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="studentRequestsBody"></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="holdConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:400px;">
        <div class="modal-content hold-confirm-modal">
            <div class="modal-body">
                <div id="holdConfirmIcon" class="hold-confirm-icon is-hold">
                    <i class="fa fa-pause" id="holdConfirmIconFa"></i>
                </div>
                <div id="holdConfirmTitle" class="hold-confirm-title">Put request on hold?</div>
                <p id="holdConfirmText" class="hold-confirm-text">
                    This student will move to the On Hold tab until you resume the request.
                </p>
                <span id="holdConfirmEnrollment" class="hold-confirm-enrollment">—</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-confirm-cancel" data-dismiss="modal">Cancel</button>
                <button type="button" id="holdConfirmBtn" class="btn-confirm-action is-hold">
                    <i class="fa fa-pause" id="holdConfirmBtnIcon"></i>
                    <span id="holdConfirmBtnLabel">Yes, put on hold</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade rc-modal" id="rcCoursesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="rc-modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-book mr-2"></i> Requested courses
                    <span class="rc-modal-enrollment" id="rcModalEnrollmentBadge">—</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="rc-modal-body">
                <div id="rcPanelCourses" class="rc-panel is-active">
                    <div class="rc-panel-scroll">
                        <div class="rc-table-wrap">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="rcCoursesTable">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Course</th>
                                            <th>Faculty</th>
                                            <th>Request</th>
                                            <th>Reason</th>
                                            <th>Assigned</th>
                                            <th class="text-center">Mentors</th>
                                            <th class="text-center">Marks</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rcCoursesTableBody">
                                        <tr><td colspan="8" class="rc-loading"><i class="fa fa-spinner fa-spin"></i> Loading courses…</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="rcPanelMarks" class="rc-panel">
                    <div class="rc-panel-toolbar">
                        <button type="button" class="rc-back-btn" id="rcBackFromMarks">
                            <i class="fa fa-arrow-left"></i> Back to courses
                        </button>
                        <div class="rc-panel-title">
                            <span id="rcMarksPanelTitle">Marks history</span>
                            <small id="rcMarksPanelSubtitle">Assessment breakdown</small>
                        </div>
                    </div>
                    <div class="rc-panel-scroll rc-marks-panel">
                        <div class="rc-marks-card">
                            <div id="rcMarksLoading" class="rc-preloader" style="display:none;">
                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                <div style="margin-top:10px;">Loading marks…</div>
                            </div>
                            <div id="rcMarksDetails"></div>
                        </div>
                    </div>
                </div>
                <div id="rcPanelMentors" class="rc-panel">
                    <div class="rc-panel-toolbar">
                        <button type="button" class="rc-back-btn" id="rcBackFromMentors">
                            <i class="fa fa-arrow-left"></i> Back to courses
                        </button>
                        <div class="rc-panel-title">
                            <span id="rcMentorsPanelTitle">Available mentors</span>
                            <small>Click a mentor row to view availability</small>
                        </div>
                    </div>
                    <div class="rc-panel-scroll rc-mentors-panel">
                        <div class="rc-mentors-card">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="rcMentorsTable">
                                    <thead>
                                        <tr>
                                            <th>Enrollment</th>
                                            <th>Full name</th>
                                            <th>Monthly hours</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rcViewMentorsBody">
                                        <tr><td colspan="3" class="rc-loading"><i class="fa fa-spinner fa-spin"></i> Loading mentors…</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rc-modal-footer">
                <button type="button" class="btn-confirm-cancel" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
var requestsDt = null;
var defaultSessionId = @json($currentSessionId ?? null);
var defaultSessionApplied = false;
var suppressSessionRequest = false;
var activeStatus = 'E';
var pendingHoldAction = null;
var rcStudentEnrollment = '';
var rcCourseId = '';

function escapeAttr(value) {
    return String(value || '')
        .replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;')
        .replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function rowVal(row, key) {
    if (!row) return '';
    if (row[key] !== undefined && row[key] !== null) return row[key];
    var upper = key.toUpperCase();
    if (row[upper] !== undefined && row[upper] !== null) return row[upper];
    var lower = key.toLowerCase();
    return row[lower] !== undefined ? row[lower] : '';
}

function getEffectiveSessionId() {
    return $('#sess_id').val() || $('#facultyWload_sess_id').combobox('getValue') || defaultSessionId || '';
}

function syncFilterControlHeights() {
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
    syncFilterControlHeights();
    loadRequests(activeStatus);
    updateStatusCounts();
}

function resetRequestFilters() {
    var icon = document.getElementById('reset-icon-req');
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
    loadRequests(activeStatus);
    updateStatusCounts();
}

function fetchRequests(status) {
    var sessId = getEffectiveSessionId();
    if (!sessId) {
        return $.Deferred().resolve([]).promise();
    }
    return $.ajax({
        url: '{{ url('/get-student-requests') }}',
        type: 'GET',
        dataType: 'json',
        data: {
            status: status,
            session_id: sessId,
            school_id: $('#school_id').val() || ''
        }
    });
}

function updateStatusCounts() {
    $.when(fetchRequests('E'), fetchRequests('H')).done(function (activeRes, holdRes) {
        var active = activeRes[0] || [];
        var hold = holdRes[0] || [];
        document.getElementById('count-active').textContent = active.length;
        document.getElementById('count-hold').textContent = hold.length;
    });
}

function setActiveTab(status) {
    activeStatus = status;
    var isActive = status === 'E';
    document.getElementById('tab-active').className = 'status-tab' + (isActive ? ' active' : '');
    document.getElementById('tab-hold').className = 'status-tab' + (!isActive ? ' active-hold' : '');
    document.getElementById('table-toolbar-title').textContent = isActive
        ? 'Active mentor requests'
        : 'On hold mentor requests';
}

function destroyDataTable() {
    if ($.fn.DataTable.isDataTable('#studentRequestsTable')) {
        $('#studentRequestsTable').DataTable().clear().destroy();
    }
    requestsDt = null;
}

function renumberTableRows() {
    if (!requestsDt) return;
    var info = requestsDt.page.info();
    requestsDt.column(0, { page: 'current', order: 'applied' }).nodes().each(function (cell, i) {
        cell.innerHTML = info.start + i + 1;
    });
}

function loadRequests(statusType) {
    setActiveTab(statusType);
    var sessId = getEffectiveSessionId();

    $('#requests-count-badge').html('<i class="fa fa-spinner fa-spin mr-1"></i> Loading...');
    destroyDataTable();
    $('#studentRequestsBody').html(
        '<tr><td colspan="7" class="text-center text-muted py-4">' +
        '<i class="fa fa-spinner fa-spin"></i> Loading requests…</td></tr>'
    );

    if (!sessId) {
        $('#requests-count-badge').html('<i class="fa fa-calendar mr-1"></i> Select session');
        $('#studentRequestsBody').html(
            '<tr><td colspan="7"><div class="empty-state">' +
            '<i class="fa fa-calendar"></i>Please select an academic session.</div></td></tr>'
        );
        return;
    }

    fetchRequests(statusType)
        .done(function (data) {
            var rows = Array.isArray(data) ? data : [];
            $('#requests-count-badge').html(
                '<i class="fa fa-list mr-1"></i> ' + rows.length + ' record' + (rows.length === 1 ? '' : 's')
            );

            if (!rows.length) {
                $('#studentRequestsBody').html(
                    '<tr><td colspan="7"><div class="empty-state">' +
                    '<i class="fa fa-inbox"></i>No requests found for the selected filters.</div></td></tr>'
                );
                return;
            }

            var sessIdVal = getEffectiveSessionId();
            var html = rows.map(function (item, index) {
                var enrollment = rowVal(item, 'mr_enrollment');
                var fullName = rowVal(item, 'full_name');
                var program = rowVal(item, 'programs');
                var department = rowVal(item, 'department') || '—';
                var actions = '';

                if (statusType === 'E') {
                    actions =
                        '<button type="button" class="btn-act btn-view-details js-view-requested-courses" data-enrollment="' + escapeAttr(enrollment) + '">' +
                        '<i class="fa fa-list-alt"></i> View Details</button>' +
                        '<button type="button" class="btn-act btn-hold js-hold-action" data-action="hold" data-enrollment="' + escapeAttr(enrollment) + '">' +
                        '<i class="fa fa-pause"></i> On hold</button>';
                } else {
                    actions =
                        '<button type="button" class="btn-act btn-unhold js-hold-action" data-action="resume" data-enrollment="' + escapeAttr(enrollment) + '">' +
                        '<i class="fa fa-play"></i> Remove hold</button>';
                }

                return '<tr>' +
                    '<td class="sr-col"></td>' +
                    '<td><span class="enrollment-chip">' + escapeAttr(enrollment) + '</span></td>' +
                    '<td>' + escapeAttr(fullName) + '</td>' +
                    '<td>' + escapeAttr(program) + '</td>' +
                    '<td>' + escapeAttr(department) + '</td>' +
                    '<td><a target="_blank" class="btn-act btn-profile" href="{{ url('reports-generator-student-profile') }}/' + encodeURIComponent(enrollment) + '">' +
                    '<i class="fa fa-id-card-o"></i> Profile</a></td>' +
                    '<td><div class="actions-cell">' + actions + '</div></td>' +
                    '</tr>';
            }).join('');

            $('#studentRequestsBody').html(html);

            requestsDt = $('#studentRequestsTable').DataTable({
                pageLength: 50,
                ordering: true,
                searching: true,
                order: [[3, 'asc']],
                stateSave: false,
                displayStart: 0,
                dom: 'Bfrtip',
                columnDefs: [
                    { targets: 0, orderable: false, searchable: false, className: 'sr-col text-center' }
                ],
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        exportOptions: { columns: [0, 1, 2, 3, 4] }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fa fa-file-pdf-o"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        exportOptions: { columns: [0, 1, 2, 3, 4] }
                    }
                ],
                lengthChange: true,
                language: { emptyTable: 'No requests found.' },
                drawCallback: function () {
                    renumberTableRows();
                }
            });
            renumberTableRows();
        })
        .fail(function () {
            $('#requests-count-badge').html('<i class="fa fa-exclamation-triangle mr-1"></i> Error');
            $('#studentRequestsBody').html(
                '<tr><td colspan="7" class="text-danger text-center py-3">Failed to load requests. Please try again.</td></tr>'
            );
        });
}

function openHoldConfirm(type, enrollment) {
    var isHold = type === 'hold';
    pendingHoldAction = { type: type, enrollment: enrollment };

    var $icon = $('#holdConfirmIcon');
    $icon.attr('class', 'hold-confirm-icon ' + (isHold ? 'is-hold' : 'is-resume'));
    $('#holdConfirmIconFa').attr('class', isHold ? 'fa fa-pause' : 'fa fa-play');

    $('#holdConfirmTitle').text(isHold ? 'Put request on hold?' : 'Resume active request?');
    $('#holdConfirmText').text(isHold
        ? 'This student will move to the On Hold tab. You can resume the request anytime from there.'
        : 'This student will return to the Active tab and appear in the main requests list again.');
    $('#holdConfirmEnrollment').text(enrollment);

    var $btn = $('#holdConfirmBtn');
    $btn.prop('disabled', false)
        .attr('class', 'btn-confirm-action ' + (isHold ? 'is-hold' : 'is-resume'));
    $('#holdConfirmBtnIcon').attr('class', isHold ? 'fa fa-pause' : 'fa fa-play');
    $('#holdConfirmBtnLabel').text(isHold ? 'Yes, put on hold' : 'Yes, resume');

    $('#holdConfirmModal').modal('show');
}

function courseField(c, key) {
    return rowVal(c, key);
}

function rcShowPanel(panel) {
    $('#rcPanelCourses, #rcPanelMarks, #rcPanelMentors').removeClass('is-active');
    if (panel === 'marks') {
        $('#rcPanelMarks').addClass('is-active');
    } else if (panel === 'mentors') {
        $('#rcPanelMentors').addClass('is-active');
    } else {
        $('#rcPanelCourses').addClass('is-active');
    }
}

function openRequestedCoursesModal(enrollment) {
    rcStudentEnrollment = enrollment;
    rcShowPanel('courses');
    $('#rcModalEnrollmentBadge').text(enrollment);
    $('#rcMarksDetails').empty();
    $('#rcMarksLoading').hide();
    $('#rcCoursesTableBody').html(
        '<tr><td colspan="8" class="rc-loading"><i class="fa fa-spinner fa-spin"></i> Loading courses…</td></tr>'
    );
    $('#rcCoursesModal').modal('show');

    $.get('{{ route('get-requested-courses') }}', {
        enrollment: enrollment,
        session_id: getEffectiveSessionId()
    }).done(function (res) {
        if (!res.success || !res.courses || !res.courses.length) {
            $('#rcCoursesTableBody').html(
                '<tr><td colspan="8" class="text-center text-muted py-4">No requested courses found.</td></tr>'
            );
            return;
        }
        var html = res.courses.map(function (c) {
            var coId = courseField(c, 'sc_co_id');
            var courseId = courseField(c, 'sc_course_id');
            var title = courseField(c, 'sc_course_title');
            var mrdEnrollment = courseField(c, 'mrd_enrollment') || enrollment;
            return '<tr>' +
                '<td>' + escapeAttr(courseField(c, 'sc_course_code')) + '</td>' +
                '<td>' + escapeAttr(title) + '</td>' +
                '<td>' + escapeAttr(courseField(c, 'sc_faculty_name')) + '</td>' +
                '<td>' + escapeAttr(courseField(c, 'mrd_student_request')) + '</td>' +
                '<td>' + escapeAttr(courseField(c, 'mrd_student_request')) + '</td>' +
                '<td>' + escapeAttr(courseField(c, 'mentor_id') || '—') + '</td>' +
                '<td class="text-center">' +
                '<button type="button" class="btn-act btn-assign btn-sm rc-view-mentors" data-course-id="' + escapeAttr(courseId) + '" data-course-title="' + escapeAttr(title) + '">' +
                '<i class="fa fa-users"></i> View Mentors</button></td>' +
                '<td class="text-center">' +
                '<button type="button" class="btn-act btn-marks-history btn-sm rc-view-marks" data-co-id="' + escapeAttr(coId) + '" data-title="' + escapeAttr(title) + '" data-enrollment="' + escapeAttr(mrdEnrollment) + '">' +
                '<i class="fa fa-bar-chart"></i> Marks history</button></td>' +
                '</tr>';
        }).join('');
        $('#rcCoursesTableBody').html(html);
    }).fail(function () {
        $('#rcCoursesTableBody').html(
            '<tr><td colspan="8" class="text-danger text-center py-3">Failed to load courses.</td></tr>'
        );
    });
}

function rcViewGrades(coId, courseTitle, rollNo) {
    $('#rcMarksPanelTitle').text(courseTitle || 'Marks history');
    $('#rcMarksPanelSubtitle').text('Enrollment: ' + (rollNo || rcStudentEnrollment));
    $('#rcMarksDetails').empty();
    $('#rcMarksLoading').show();
    rcShowPanel('marks');

    $.get('{{ url('/marksdetailMentor') }}/' + coId + '/' + rollNo)
        .done(function (data) {
            $('#rcMarksLoading').hide();
            var $wrap = $('<div>').html(data);
            $wrap.find('.modal-header, .close, button.close').remove();
            $('#rcMarksDetails').html($wrap.contents());
        })
        .fail(function () {
            $('#rcMarksLoading').hide();
            $('#rcMarksDetails').html('<p class="text-muted text-center py-3">No record found.</p>');
        });
}

function rcViewMentors(courseId, courseTitle) {
    rcCourseId = courseId;
    $('#rcMentorsPanelTitle').text(courseTitle ? ('Mentors — ' + courseTitle) : 'Available mentors');
    $('#rcViewMentorsBody').html(
        '<tr><td colspan="3" class="rc-loading"><i class="fa fa-spinner fa-spin"></i> Loading mentors…</td></tr>'
    );
    rcShowPanel('mentors');

    $.ajax({
        url: '{{ url('/getCourseMentors') }}/' + courseId,
        type: 'GET',
        dataType: 'json',
        data: { sess_id: getEffectiveSessionId() }
    }).done(function (data) {
        var list = Array.isArray(data) ? data : [];
        if (!list.length) {
            $('#rcViewMentorsBody').html('<tr><td colspan="3" class="text-center text-muted py-4">No mentors found.</td></tr>');
            return;
        }
        var html = [];
        $.each(list, function (i, item) {
            var enr = rcApiField(item, 'md_enrollment');
            var name = rcApiField(item, 'full_name');
            var hours = rcApiField(item, 'm_monthly_hours');
            var safeId = rcMentorSafeId(enr);
            html.push(
                '<tr class="rc-mentor-row" data-mentor="' + escapeAttr(enr) + '" data-detail-id="' + escapeAttr(safeId) + '">' +
                '<td><span class="enrollment-chip">' + escapeAttr(enr) + '</span></td>' +
                '<td>' + escapeAttr(name) + '</td>' +
                '<td>' + escapeAttr(hours) + '</td></tr>' +
                '<tr class="rc-mentor-detail-row" id="rc-detail-' + safeId + '" style="display:none;">' +
                '<td colspan="3"><div class="rc-mentor-detail-inner" id="rc-detail-content-' + safeId + '">' +
                '<div class="rc-preloader"><i class="fa fa-spinner fa-spin"></i> Loading availability…</div></div></td></tr>'
            );
        });
        $('#rcViewMentorsBody').html(html.join(''));
    }).fail(function () {
        $('#rcViewMentorsBody').html('<tr><td colspan="3" class="text-danger text-center py-3">Failed to load mentors.</td></tr>');
    });
}

function rcApiField(obj, key) {
    if (!obj) return '';
    if (obj[key] !== undefined && obj[key] !== null) return obj[key];
    var upper = key.toUpperCase();
    if (obj[upper] !== undefined && obj[upper] !== null) return obj[upper];
    var lower = key.toLowerCase();
    return obj[lower] !== undefined ? obj[lower] : '';
}

function rcMentorSafeId(enrollment) {
    return String(enrollment || '').replace(/[^a-zA-Z0-9_-]/g, '_');
}

function rcSlotStatusClass(status) {
    if (status === 'Busy') return 'slot-busy';
    if (status === 'Partially Busy') return 'slot-partial';
    return 'slot-free';
}

function rcSlotBadgeClass(status) {
    if (status === 'Busy') return 'b-busy';
    if (status === 'Partially Busy') return 'b-partial';
    return 'b-free';
}

function rcSlotStatusLabel(slot) {
    var label = rcApiField(slot, 'status_label');
    if (label) return label;
    var status = rcApiField(slot, 'availability_status') || rcApiField(slot, 'between_exists');
    if (status === 'Busy') return 'Busy';
    if (status === 'Partially Busy') return 'Partial';
    return 'Free';
}

function rcGroupSlotsByDay(slots) {
    var groups = {};
    var order = [];
    $.each(slots || [], function (i, slot) {
        var day = rcApiField(slot, 'week_day') || rcApiField(slot, 'tt_subject_day') || 'Unknown';
        if (!groups[day]) {
            groups[day] = [];
            order.push(day);
        }
        groups[day].push(slot);
    });
    return { order: order, groups: groups };
}

function rcRenderSlotTimeRange(slot) {
    var fromT = rcApiField(slot, 'from_time') || rcApiField(slot, 'tt_subject_time_from');
    var toT = rcApiField(slot, 'to_time') || rcApiField(slot, 'tt_subject_time_to');
    return escapeAttr(fromT) + '<span class="rc-time-sep">–</span>' + escapeAttr(toT);
}

function rcRenderDaySlotRow(slot, options) {
    options = options || {};
    var status = rcApiField(slot, 'availability_status') || rcApiField(slot, 'between_exists') || 'Free';
    var rowClass = rcSlotStatusClass(status);
    if (options.studentView) {
        rowClass = status === 'Free' ? 'slot-student-free' : (status === 'Partially Busy' ? 'slot-student-partial' : 'slot-student-busy');
    }
    var badge = '<span class="rc-slot-badge ' + rcSlotBadgeClass(status) + '">' + escapeAttr(rcSlotStatusLabel(slot)) + '</span>';
    var checkHtml = '<span class="text-muted">—</span>';
    if (options.showCheckbox && (slot.is_selectable === true || status === 'Free' || status === 'Partially Busy')) {
        var val = escapeAttr(rcApiField(slot, 'week_day') + '|' + rcApiField(slot, 'from_time') + '|' + rcApiField(slot, 'to_time'));
        checkHtml = '<input type="checkbox" class="rc-mentor-slot-cb" data-mentor="' + escapeAttr(options.safeMentorId) + '" value="' + val + '">';
    }
    return '' +
        '<div class="rc-day-slot ' + rowClass + '">' +
        '<div class="rc-day-slot-check">' + checkHtml + '</div>' +
        '<div class="rc-day-slot-time">' + rcRenderSlotTimeRange(slot) + badge + '</div>' +
        '</div>';
}

function rcRenderGroupedMentorSlots(slots, safeMentorId) {
    if (!slots.length) {
        return '<p class="text-muted text-center py-3 mb-0">No mentor slots defined for this course.</p>';
    }
    var grouped = rcGroupSlotsByDay(slots);
    var html = [];
    $.each(grouped.order, function (i, day) {
        var daySlots = grouped.groups[day] || [];
        var slotRows = daySlots.map(function (slot) {
            return rcRenderDaySlotRow(slot, { showCheckbox: true, safeMentorId: safeMentorId, studentView: false });
        }).join('');
        html.push('<div class="rc-day-group"><div class="rc-day-head">' + escapeAttr(day) + '</div>' + slotRows + '</div>');
    });
    return html.join('');
}

function rcRenderGroupedStudentSlots(slots) {
    if (!slots.length) {
        return '<p class="text-muted text-center py-3 mb-0">No class schedule found.</p>';
    }
    var grouped = rcGroupSlotsByDay(slots);
    var html = [];
    $.each(grouped.order, function (i, day) {
        var daySlots = grouped.groups[day] || [];
        var slotRows = daySlots.map(function (slot) {
            return rcRenderDaySlotRow(slot, { showCheckbox: false, studentView: true });
        }).join('');
        html.push('<div class="rc-day-group"><div class="rc-day-head">' + escapeAttr(day) + '</div>' + slotRows + '</div>');
    });
    return html.join('');
}

function rcCountSlotsByStatus(slots) {
    var counts = { free: 0, partial: 0, busy: 0 };
    $.each(slots || [], function (i, slot) {
        var status = rcApiField(slot, 'availability_status') || rcApiField(slot, 'between_exists');
        if (status === 'Busy') counts.busy++;
        else if (status === 'Partially Busy') counts.partial++;
        else counts.free++;
    });
    return counts;
}

function rcRenderStudentSummaryHtml(summary) {
    summary = summary || {};
    return (
        '<div class="rc-slot-summary">' +
        '<span class="sum-free">Free: <strong>' + (summary.student_free != null ? summary.student_free : 0) + '</strong></span>' +
        '<span class="sum-partial">Partial: <strong>' + (summary.student_partial != null ? summary.student_partial : 0) + '</strong></span>' +
        '<span class="sum-busy">Busy: <strong>' + (summary.student_busy != null ? summary.student_busy : 0) + '</strong></span>' +
        '</div>'
    );
}

function rcRenderMentorSummaryHtml(summary) {
    summary = summary || {};
    return (
        '<div class="rc-slot-summary">' +
        '<span class="sum-free">Free: <strong>' + (summary.mentor_free != null ? summary.mentor_free : 0) + '</strong></span>' +
        '<span class="sum-partial">Partial: <strong>' + (summary.mentor_partial != null ? summary.mentor_partial : 0) + '</strong></span>' +
        '<span class="sum-busy">Busy: <strong>' + (summary.mentor_busy != null ? summary.mentor_busy : 0) + '</strong></span>' +
        '</div>'
    );
}

function rcRenderMentorAvailabilityHtml(mentorEnrollment, studentRoll, data) {
    var mentorDetails = data.mentorSlots || data.mentorDetails || [];
    var studentSlots = data.studentTimetable || data.studentSlots || [];
    var summary = data.slotSummary || {};
    var selectableCount = summary.selectable != null
        ? summary.selectable
        : ((data.selectableSlots || []).length || (data.freeSlots || []).length + (data.partialSlots || []).length);
    var safeMentorId = rcMentorSafeId(mentorEnrollment);

    if (summary.mentor_free == null && mentorDetails.length) {
        var mc = rcCountSlotsByStatus(mentorDetails);
        summary.mentor_free = mc.free;
        summary.mentor_partial = mc.partial;
        summary.mentor_busy = mc.busy;
    }
    if (summary.student_free == null && studentSlots.length) {
        var sc = rcCountSlotsByStatus(studentSlots);
        summary.student_free = sc.free;
        summary.student_partial = sc.partial;
        summary.student_busy = sc.busy;
    }

    var mentorHtml = rcRenderGroupedMentorSlots(mentorDetails, safeMentorId);
    var studentHtml = rcRenderGroupedStudentSlots(studentSlots);
    var mentorSummaryHtml = rcRenderMentorSummaryHtml(summary);
    var studentSummaryHtml = rcRenderStudentSummaryHtml(summary);

    var intimationBar = selectableCount > 0
        ? '<div class="rc-intimation-bar"><p class="rc-intimation-hint"><strong>' + selectableCount + '</strong> free/partial slot(s). Select one or more, then send intimation.</p><button type="button" class="rc-btn-intimation" onclick="rcSendIntimation(\'' + escapeAttr(mentorEnrollment) + '\')"><i class="fa fa-paper-plane"></i> Send Intimation</button></div>'
        : '<div class="rc-intimation-bar"><p class="rc-intimation-hint text-muted mb-0">No free or partial slots available for intimation.</p></div>';

    return (
        '<div class="rc-slot-legend">' +
        '<span><i class="lg-free"></i> Free</span>' +
        '<span><i class="lg-busy"></i> Busy / In class</span>' +
        '<span><i class="lg-partial"></i> Partial overlap</span>' +
        '</div>' +
        '<div class="rc-availability-grid">' +
        '<div class="rc-avail-col">' +
        '<h6 class="rc-avail-col-title"><i class="fa fa-calendar-check-o text-success"></i> Mentor availability</h6>' +
        '<p class="rc-avail-hint">Check free or partial slots to send intimation.</p>' +
        mentorSummaryHtml +
        '<div class="rc-slot-schedule">' + mentorHtml + '</div></div>' +
        '<div class="rc-avail-col">' +
        '<h6 class="rc-avail-col-title"><i class="fa fa-user text-primary"></i> Student: ' + escapeAttr(studentRoll) + '</h6>' +
        '<p class="rc-avail-hint">Slot counts for this student.</p>' +
        studentSummaryHtml +
        '<div class="rc-slot-schedule">' + studentHtml + '</div></div>' + intimationBar
    );
}

function rcSendIntimation(mentorEnrollment) {
    var safeMentorId = rcMentorSafeId(mentorEnrollment);
    var $checked = $('.rc-mentor-slot-cb[data-mentor="' + safeMentorId + '"]:checked');

    if (!$checked.length) {
        if (typeof showToast === 'function') {
            showToast('warning', 'Please select at least one free or partial mentor timeslot.', 'Select slot');
        } else {
            alert('Please select at least one free or partial mentor timeslot.');
        }
        return;
    }
    if (!rcCourseId) {
        alert('Course context is missing. Open mentors from a course row.');
        return;
    }

    var slots = $checked.toArray();
    var $btn = $('.rc-btn-intimation');
    var sentKeys = {};
    var sent = 0;
    var skipped = 0;
    var failed = 0;
    $btn.prop('disabled', true);

    function slotKey(parts) {
        return (parts[0] || '') + '|' + (parts[1] || '') + '|' + (parts[2] || '');
    }

    function sendAt(index) {
        if (index >= slots.length) {
            $btn.prop('disabled', false);
            var msg;
            if (failed > 0) {
                msg = 'Sent: ' + sent + ', skipped (duplicate): ' + skipped + ', failed: ' + failed + '.';
                if (typeof showToast === 'function') {
                    showToast('warning', msg, 'Intimation');
                } else {
                    alert(msg);
                }
            } else if (skipped > 0 && sent === 0) {
                msg = 'Intimation already sent for the selected slot(s).';
                if (typeof showToast === 'function') {
                    showToast('warning', msg, 'Already sent');
                } else {
                    alert(msg);
                }
            } else if (skipped > 0) {
                msg = 'Sent: ' + sent + '. Skipped (already sent): ' + skipped + '.';
                if (typeof showToast === 'function') {
                    showToast('success', msg, 'Intimation');
                } else {
                    alert(msg);
                }
            } else {
                msg = sent > 1 ? ('Intimations sent for ' + sent + ' slots.') : 'Intimation sent successfully.';
                if (typeof showToast === 'function') {
                    showToast('success', msg, 'Success');
                } else {
                    alert(msg);
                }
            }
            $('.rc-mentor-slot-cb[data-mentor="' + safeMentorId + '"]').prop('checked', false);
            return;
        }
        var parts = ($(slots[index]).val() || '').split('|');
        var key = slotKey(parts);
        if (sentKeys[key]) {
            skipped++;
            sendAt(index + 1);
            return;
        }
        $.post('{{ route('send-intimation') }}', {
            _token: '{{ csrf_token() }}',
            course_id: rcCourseId,
            mentor_enrollment: mentorEnrollment,
            student_enrollment: rcStudentEnrollment,
            week_day: parts[0] || '',
            from_time: parts[1] || '',
            to_time: parts[2] || ''
        }).done(function (res) {
            if (res && res.duplicate) {
                skipped++;
            } else if (res && res.success === false) {
                failed++;
            } else {
                sent++;
                sentKeys[key] = true;
            }
        }).fail(function () {
            failed++;
        }).always(function () {
            sendAt(index + 1);
        });
    }

    sendAt(0);
}

function rcLoadMentorDetails(mentorEnrollment, studentRoll) {
    var safeId = rcMentorSafeId(mentorEnrollment);
    var $container = $('#rc-detail-content-' + safeId);
    $container.html('<div class="rc-preloader"><i class="fa fa-spinner fa-spin"></i> Loading availability…</div>');

    $.ajax({
        url: '{{ url('/get-mentor-details') }}',
        method: 'GET',
        dataType: 'json',
        data: {
            mentor_enrollment: mentorEnrollment,
            student_enrollment: studentRoll,
            sess_id: getEffectiveSessionId(),
            course_id: rcCourseId
        }
    }).done(function (data) {
        $container.html(rcRenderMentorAvailabilityHtml(mentorEnrollment, studentRoll, data));
    }).fail(function () {
        $container.html('<span class="text-danger">Failed to load mentor availability.</span>');
    });
}

function executeHoldAction() {
    if (!pendingHoldAction) return;

    var isHold = pendingHoldAction.type === 'hold';
    var url = isHold ? '{{ route('putOnHoldRequest') }}' : '{{ url('/removeHoldRequest') }}';
    var $btn = $('#holdConfirmBtn');

    $btn.prop('disabled', true);
    $('#holdConfirmBtnLabel').html('<i class="fa fa-spinner fa-spin"></i> Please wait…');

    $.post(url, {
        enrollment: pendingHoldAction.enrollment,
        session_id: getEffectiveSessionId(),
        _token: '{{ csrf_token() }}'
    }).done(function () {
        $('#holdConfirmModal').modal('hide');
        pendingHoldAction = null;
        updateStatusCounts();
        loadRequests(activeStatus);
        if (typeof showToast === 'function') {
            showToast('success', isHold ? 'Request put on hold.' : 'Request resumed successfully.');
        }
    }).fail(function () {
        if (typeof showToast === 'function') {
            showToast('error', isHold ? 'Could not put request on hold.' : 'Could not resume request.');
        } else {
            alert(isHold ? 'Could not put request on hold.' : 'Could not resume request.');
        }
    }).always(function () {
        $btn.prop('disabled', false);
        $('#holdConfirmBtnLabel').text(isHold ? 'Yes, put on hold' : 'Yes, resume');
    });
}

$(document).ready(function () {
    $('.status-tab').on('click', function () {
        var status = $(this).data('status');
        if (status === activeStatus) return;
        loadRequests(status);
    });

    $(document).on('click', '.js-hold-action', function () {
        var action = $(this).data('action');
        var enrollment = $(this).data('enrollment');
        if (!enrollment) return;
        openHoldConfirm(action === 'resume' ? 'resume' : 'hold', enrollment);
    });

    $(document).on('click', '.js-view-requested-courses', function () {
        var enrollment = $(this).data('enrollment');
        if (enrollment) openRequestedCoursesModal(enrollment);
    });

    $(document).on('click', '.rc-view-marks', function () {
        rcViewGrades($(this).data('co-id'), $(this).data('title'), $(this).data('enrollment'));
    });

    $(document).on('click', '.rc-view-mentors', function () {
        rcViewMentors($(this).data('course-id'), $(this).data('course-title'));
    });

    $('#rcBackFromMarks, #rcBackFromMentors').on('click', function () {
        rcShowPanel('courses');
    });

    $('#rcCoursesModal').on('hidden.bs.modal', function () {
        rcShowPanel('courses');
        $('#rcMarksDetails').empty();
    });

    $(document).on('click', '.rc-mentor-row', function () {
        var mentorEnr = $(this).data('mentor');
        var detailId = $(this).data('detail-id') || rcMentorSafeId(mentorEnr);
        var $detailRow = $('#rc-detail-' + detailId);
        if ($detailRow.is(':visible')) {
            $detailRow.hide();
            $(this).removeClass('is-expanded');
            return;
        }
        $('.rc-mentor-detail-row').hide();
        $('.rc-mentor-row').removeClass('is-expanded');
        $detailRow.show();
        $(this).addClass('is-expanded');
        rcLoadMentorDetails(mentorEnr, rcStudentEnrollment);
    });

    $('#holdConfirmBtn').on('click', executeHoldAction);

    $('#holdConfirmModal').on('hidden.bs.modal', function () {
        pendingHoldAction = null;
        $('#holdConfirmBtn').prop('disabled', false);
    });

    setTimeout(syncFilterControlHeights, 80);
    $(window).on('resize.pmReqFilters', syncFilterControlHeights);
});
</script>
@endsection
