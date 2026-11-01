@extends('layouts.app')

@section('content')
@include('components.toast-notifications')

<style>
/* ============================================================
   PAGE HEADER
============================================================ */
.schol-header {
    background: linear-gradient(135deg, #1e293b 0%, #2d3f55 100%);
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.schol-header h3 {
    color: #fff;
    font-size: 20px;
    font-weight: 600;
    margin: 0;
    font-family: 'Times New Roman', Times, serif;
}
.schol-header-sub { color: #94a3b8; font-size: 13px; margin-top: 3px; }
.schol-badge-count {
    background: rgba(59,130,246,0.2);
    color: #93c5fd;
    font-size: 12px;
    padding: 5px 12px;
    border-radius: 20px;
    border: 1px solid rgba(59,130,246,0.3);
    font-weight: 500;
}

/* ============================================================
   STATS
============================================================ */
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.stat-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 18px;
}
.stat-label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 6px; font-weight: 500; }
.stat-value { font-size: 26px; font-weight: 600; color: #1e293b; }
.stat-value.blue  { color: #3b82f6; }
.stat-value.green { color: #10b981; }
.stat-value.amber { color: #f59e0b; }

/* ============================================================
   FILTERS CARD
============================================================ */
.filters-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 18px 20px;
    margin-bottom: 16px;
}
.filters-grid {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 14px;
    align-items: end;
}
.field-label {
    font-size: 11px; font-weight: 600; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.6px;
    display: block; margin-bottom: 6px;
}
.btn-reset {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 0 18px; background: #f1f5f9;
    border: 1px solid #e2e8f0; border-radius: 8px;
    color: #64748b; font-size: 13px; font-weight: 500;
    cursor: pointer; transition: all 0.2s; white-space: nowrap;
    height: 34px;
}
.btn-reset:hover { border-color: #3b82f6; color: #3b82f6; background: #eff6ff; }

/* ============================================================
   TAG FILTER TABS
============================================================ */
.filter-tabs {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.filter-tab {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 14px; border-radius: 20px;
    font-size: 12px; font-weight: 600; cursor: pointer;
    border: 1.5px solid; transition: all 0.15s;
    user-select: none; background: none;
}
.filter-tab.all     { border-color: #cbd5e1; color: #475569; background: #f8fafc; }
.filter-tab.all.active     { border-color: #1e293b; color: #fff !important; background: #1e293b !important; }
.filter-tab.sched   { border-color: #bbf7d0; color: #059669; background: #f0fdf4; }
.filter-tab.sched.active   { border-color: #059669; color: #fff !important; background: #059669 !important; }
.filter-tab.pending { border-color: #fecaca; color: #dc2626; background: #fef2f2; }
.filter-tab.pending.active { border-color: #dc2626; color: #fff !important; background: #dc2626 !important; }
.filter-tab.done { border-color: #bfdbfe; color: #2563eb; background: #eff6ff; }
.filter-tab.done.active { border-color: #2563eb; color: #fff !important; background: #2563eb !important; }
.filter-tab.passed { border-color: #fca5a5; color: #b91c1c; background: #fef2f2; }
.filter-tab.passed.active { border-color: #b91c1c; color: #fff !important; background: #b91c1c !important; }
.tab-count {
    background: rgba(0,0,0,0.12);
    border-radius: 10px;
    padding: 1px 7px;
    font-size: 11px;
    font-weight: 700;
}
.filter-tab.active .tab-count { background: rgba(255,255,255,0.3); }

/* ============================================================
   TABLE CARD
============================================================ */
.table-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
}
.table-toolbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 18px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;
    flex-wrap: wrap; gap: 10px;
}
.table-toolbar-title { font-size: 14px; font-weight: 600; color: #1e293b; }
.export-btns { display: flex; gap: 8px; }
.btn-export {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 13px; border-radius: 7px;
    font-size: 12px; font-weight: 500;
    cursor: pointer; border: 1px solid; transition: all 0.15s;
}
.btn-excel { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
.btn-excel:hover { background: #dcfce7; }
.btn-pdf   { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
.btn-pdf:hover   { background: #fee2e2; }

/* ============================================================
   TABLE ROWS
============================================================ */
#scholarshipTable thead tr { background: #f8fafc; }
#scholarshipTable thead th {
    font-size: 11px !important; font-weight: 700 !important;
    color: #64748b !important; text-transform: uppercase !important;
    letter-spacing: 0.6px !important; padding: 11px 13px !important;
    border-bottom: 2px solid #e2e8f0 !important; white-space: nowrap;
}
#scholarshipTable tbody td {
    padding: 11px 13px !important; font-size: 13px !important;
    vertical-align: middle !important;
    border-bottom: 1px solid #f1f5f9 !important; color: #334155;
}
#scholarshipTable tbody tr:hover td { background: #f8fafc; }
#scholarshipTable tbody tr:last-child td { border-bottom: none !important; }

/* ============================================================
   CELL COMPONENTS
============================================================ */
.enrollment-chip {
    font-family: 'Courier New', monospace;
    font-size: 12px; color: #3b82f6;
    background: rgba(59,130,246,0.08);
    padding: 3px 9px; border-radius: 5px;
    border: 1px solid rgba(59,130,246,0.2);
    font-weight: 700; white-space: nowrap; display: inline-block;
}
.school-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 600; padding: 3px 9px;
    border-radius: 5px; background: #f1f5f9;
    color: #475569; border: 1px solid #e2e8f0; white-space: nowrap;
}
.dept-text { font-size: 12px; color: #64748b; }
.prog-text { font-size: 13px; color: #1e293b; font-weight: 500; line-height: 1.3; }
.pct-wrap  { display: flex; align-items: center; gap: 8px; }
.pct-bar   { height: 5px; background: #e2e8f0; border-radius: 3px; width: 50px; overflow: hidden; flex-shrink: 0; }
.pct-fill      { height: 100%; border-radius: 3px; background: #3b82f6; }
.pct-fill.high { background: #10b981; }
.pct-fill.mid  { background: #f59e0b; }
.pct-fill.low  { background: #ef4444; }
.pct-text      { font-size: 13px; font-weight: 700; color: #1e293b; white-space: nowrap; min-width: 42px; }
.int-scheduled {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; color: #059669; font-weight: 600;
    background: #f0fdf4; padding: 3px 8px; border-radius: 5px;
    border: 1px solid #bbf7d0; margin-bottom: 5px; white-space: nowrap;
}
.int-pending {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; color: #dc2626; font-weight: 600;
    background: #fef2f2; padding: 3px 8px; border-radius: 5px;
    border: 1px solid #fecaca; margin-bottom: 5px;
}
.int-expired {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; color: #b91c1c; font-weight: 700;
    background: #fef2f2; padding: 3px 8px; border-radius: 5px;
    border: 1px solid #fca5a5; margin-bottom: 5px; white-space: nowrap;
}
.int-interviewed {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; color: #2563eb; font-weight: 700;
    background: #eff6ff; padding: 3px 8px; border-radius: 5px;
    border: 1px solid #bfdbfe; margin-bottom: 5px; white-space: nowrap;
}

/* ============================================================
   ACTION BUTTONS
============================================================ */
.btn-act {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 5px 10px; border-radius: 6px;
    font-size: 12px; font-weight: 600; cursor: pointer;
    border: 1px solid; transition: all 0.15s;
    white-space: nowrap; text-decoration: none; margin: 2px;
}
.btn-act:active { transform: scale(0.97); }
.btn-act:hover  { text-decoration: none; }
.btn-schedule { color: #2563eb; border-color: rgba(37,99,235,0.3); background: rgba(37,99,235,0.06); }
.btn-schedule:hover { background: rgba(37,99,235,0.13); color: #2563eb; }
.btn-interviewed { color: #059669; border-color: rgba(5,150,105,0.3); background: rgba(5,150,105,0.06); }
.btn-interviewed:hover { background: rgba(5,150,105,0.13); color: #059669; }
.btn-profile  { color: #7c3aed; border-color: rgba(124,58,237,0.3); background: rgba(124,58,237,0.06); }
.btn-profile:hover { background: rgba(124,58,237,0.13); color: #7c3aed; }
.btn-detail   { color: #0891b2; border-color: rgba(8,145,178,0.3); background: rgba(8,145,178,0.06); }
.btn-detail:hover { background: rgba(8,145,178,0.13); color: #0891b2; }
.btn-mentor   { color: #059669; border-color: rgba(5,150,105,0.3); background: rgba(5,150,105,0.06); }
.btn-mentor:hover { background: rgba(5,150,105,0.13); color: #059669; }
.actions-cell { display: flex; flex-wrap: wrap; gap: 3px; align-items: center; }

/* ============================================================
   MODALS
============================================================ */
.modal-content { border-radius: 12px !important; border: 1px solid #e2e8f0; box-shadow: 0 20px 60px rgba(0,0,0,0.12); }
.modal-header  { background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0 !important; padding: 16px 20px; }
.modal-title   { font-size: 15px; font-weight: 600; color: #1e293b; }
.modal-body    { padding: 20px; }
.modal-footer  { border-top: 1px solid #e2e8f0; padding: 14px 20px; background: #f8fafc; border-radius: 0 0 12px 12px !important; }
.gradebook-modal-dialog {
    max-width: 96vw;
}
.gradebook-modal-body {
    padding: 20px;
    min-height: 62vh;
    background: #f8fafc;
}
.gradebook-placeholder {
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    background: #fff;
    padding: 28px;
    text-align: center;
    color: #64748b;
}
.gradebook-placeholder i {
    color: #0891b2;
    margin-bottom: 10px;
}
.gradebook-placeholder-title {
    color: #1e293b;
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 6px;
}
.gradebook-table-wrap {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
}
.gradebook-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-top: 14px;
    flex-wrap: wrap;
}
.mentor-panel {
    display: none;
    margin-top: 18px;
    padding: 16px;
    border: 1px solid #dbeafe;
    border-radius: 12px;
    background: #eff6ff;
}
.mentor-panel.is-visible {
    display: block;
}
.selected-course-list {
    margin: 0 0 12px;
    padding-left: 18px;
    color: #334155;
    font-size: 13px;
}
.timing-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr auto;
    gap: 10px;
    align-items: end;
    margin-bottom: 10px;
}
.mentor-panel-grid {
    display: grid;
    grid-template-columns: 160px 1fr;
    gap: 12px;
}
.gradebook-status {
    display: none;
    margin-top: 12px;
    border-radius: 8px;
    padding: 9px 12px;
    font-size: 12px;
    font-weight: 600;
}
.gradebook-status.is-error {
    display: block;
    color: #b91c1c;
    background: #fef2f2;
    border: 1px solid #fecaca;
}
.gradebook-status.is-success {
    display: block;
    color: #047857;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
}
.mentor-type-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 16px;
}
.mentor-type-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #fff;
    padding: 16px;
    text-align: left;
    cursor: pointer;
    transition: all 0.18s ease;
}
.mentor-type-card:hover,
.mentor-type-card.is-active {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.08);
}
.mentor-type-card i {
    color: #2563eb;
    font-size: 20px;
    margin-bottom: 8px;
}
.mentor-type-title {
    color: #1e293b;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 4px;
}
.mentor-type-sub {
    color: #64748b;
    font-size: 12px;
    line-height: 1.35;
}
.gradebook-section,
.admin-mentor-section {
    display: none;
}
.gradebook-section.is-visible,
.admin-mentor-section.is-visible {
    display: block;
}
.form-label-styled {
    display: block; font-size: 11px; font-weight: 600; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
}
.form-control-styled {
    width: 100%; padding: 9px 12px;
    border: 1px solid #e2e8f0; border-radius: 8px;
    background: #f8fafc; font-size: 14px; color: #1e293b; transition: all 0.2s;
}
.form-control-styled:focus {
    outline: none; border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1); background: #fff;
}
.info-alert-blue {
    background: rgba(59,130,246,0.07); border: 1px solid rgba(59,130,246,0.2);
    border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #2563eb; margin-bottom: 16px;
}
.info-alert-green {
    background: rgba(5,150,105,0.07); border: 1px solid rgba(5,150,105,0.2);
    border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #059669; margin-bottom: 16px;
}
.section-head {
    font-size: 12px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.6px;
    margin-bottom: 10px; display: flex; align-items: center; gap: 6px;
}
.mini-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.mini-table thead th {
    padding: 7px 10px; background: #f8fafc;
    font-size: 11px; font-weight: 600; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px;
    border-bottom: 1px solid #e2e8f0; text-align: left;
}
.mini-table tbody td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; color: #334155; }
.mini-table tbody tr:last-child td { border-bottom: none; }
.timetable-list {
    display: grid;
    gap: 10px;
}
.timetable-card {
    display: grid;
    grid-template-columns: minmax(220px, 32%) 1fr;
    gap: 14px;
    align-items: start;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #fff;
    padding: 12px 14px;
    box-shadow: 0 1px 2px rgba(15,23,42,0.04);
}
.timetable-card-head {
    display: grid;
    grid-template-columns: 30px 1fr;
    gap: 10px;
    align-items: center;
    margin-bottom: 0;
    min-height: 100%;
}
.timetable-number {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #eff6ff;
    color: #2563eb;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.timetable-course {
    font-size: 14px;
    color: #1e293b;
    font-weight: 700;
    line-height: 1.25;
    text-align: center;
}
.timetable-teacher {
    font-size: 12px;
    color: #64748b;
    margin-top: 4px;
    line-height: 1.35;
    text-align: center;
}
.slot-count {
    font-size: 11px;
    color: #475569;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    padding: 3px 8px;
    white-space: nowrap;
}
.time-slot-wrap {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
    padding-left: 0;
}
.busy-slots-label {
    padding-left: 40px;
    margin: -2px 0 8px;
    font-size: 11px;
    color: #64748b;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.time-slot-chip {
    display: grid;
    grid-template-columns: 86px 1fr;
    align-items: center;
    gap: 10px;
    padding: 8px 10px;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #dbeafe;
    color: #334155;
    font-size: 12px;
}
.time-slot-day {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    background: #eff6ff;
    color: #2563eb;
    border-radius: 999px;
    padding: 5px 8px;
    font-weight: 700;
}
.time-slot-time {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #1e293b;
    font-weight: 700;
    line-height: 1.25;
}
.time-slot-time small {
    color: #64748b;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.modal-empty-state {
    border: 1px dashed #cbd5e1;
    border-radius: 10px;
    padding: 18px;
    text-align: center;
    color: #64748b;
    background: #f8fafc;
}
.schedule-status-message {
    display: none;
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 12px;
    font-weight: 600;
    margin: -2px 0 14px;
}
.schedule-status-message.is-error {
    display: block;
    color: #b91c1c;
    background: #fef2f2;
    border: 1px solid #fecaca;
}
.schedule-status-message.is-ok {
    display: block;
    color: #047857;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
}
.schedule-status-message.is-info {
    display: block;
    color: #2563eb;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
}

.btn-primary-mod {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; background: #3b82f6; color: #fff;
    border: none; border-radius: 8px; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: background 0.2s;
}
.btn-primary-mod:hover { background: #2563eb; }
.btn-success-mod {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; background: #059669; color: #fff;
    border: none; border-radius: 8px; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: background 0.2s;
}
.btn-success-mod:hover { background: #047857; }
.btn-cancel-mod {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; background: #f1f5f9; color: #475569;
    border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px;
    font-weight: 500; cursor: pointer; transition: all 0.2s;
}
.btn-cancel-mod:hover { background: #e2e8f0; color: #1e293b; }

@media (max-width: 768px) {
    .stats-row { grid-template-columns: repeat(2, 1fr); }
    .filters-grid { grid-template-columns: 1fr; }
    .timetable-card { grid-template-columns: 1fr; }
    .timetable-card-head { grid-template-columns: 30px 1fr; }
    .slot-count { grid-column: 2; justify-self: start; }
    .busy-slots-label,
    .time-slot-wrap { padding-left: 0; }
    .time-slot-chip { grid-template-columns: 1fr; }
    .timing-row,
    .mentor-panel-grid,
    .mentor-type-grid { grid-template-columns: 1fr; }
}
</style>

{{-- Hidden fields for combobox selected values --}}
<input type="hidden" id="sess_id">
<input type="hidden" id="school_id">

<div class="container-fluid p-0">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="schol-header">
        <div>
            <h3><i class="fa fa-graduation-cap mr-2"></i>Scholarship Students</h3>
            <div class="schol-header-sub">Manage scholarship applicants, interviews &amp; mentor assignments</div>
        </div>
        <span class="schol-badge-count" id="student-count-badge">
            <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
        </span>
    </div>

    {{-- ===== STATS ROW ===== --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Total Students</div>
            <div class="stat-value blue" id="stat-total">—</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Interviews Scheduled</div>
            <div class="stat-value green" id="stat-scheduled">—</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pending Interview</div>
            <div class="stat-value amber" id="stat-pending">—</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Avg. Percentage</div>
            <div class="stat-value" id="stat-avg">—</div>
        </div>
    </div>

    {{-- ===== FILTERS ===== --}}
    <div class="filters-card">
        <div class="filters-grid">

            {{-- SESSION — FIX: onSelect clears school combobox --}}
            <div>
                <span class="field-label"><i class="fa fa-calendar mr-1"></i> Session</span>
                <input id="facultyWload_sess_id"
                       name="facultyWload_sess_id"
                       style="width:100%;"
                       class="easyui-combobox"
                       placeholder="Select session..."
                       data-options="
                           valueField: 'sess_id',
                           textField:  'sess_name',
                           url:        '{{ url('/sessions') }}',
                           method:     'GET',
                           onLoadSuccess: function() {
                               setDefaultSession();
                           },
                           onSelect: function(rec) {
                               $('#sess_id').val(rec.sess_id);
                               $('#school_id').val('');
                               $('#facultyWload_school_id').combobox('clear');
                               if (suppressSessionRequest) return;
                               getRequest();
                           }">
            </div>

            {{-- SCHOOL --}}
            <div>
                <span class="field-label"><i class="fa fa-building-o mr-1"></i> School</span>
                <input id="facultyWload_school_id"
                       name="facultyWload_school_id"
                       style="width:100%;"
                       class="easyui-combobox"
                       placeholder="All schools..."
                       data-options="
                           valueField: 'school_id',
                           textField:  'school_name',
                           method:     'GET',
                           url:        '{{ url('/schools') }}',
                           onSelect: function(rec) {
                               $('#school_id').val(rec.school_id);
                               getRequest();
                           }">
            </div>

            {{-- RESET --}}
            <button class="btn-reset" onclick="resetFilters()">
                <i class="fa fa-refresh" id="reset-icon"></i>&nbsp;Reset
            </button>

        </div>
    </div>

    {{-- ===== INTERVIEW STATUS FILTER TABS ===== --}}
    <div class="filter-tabs" id="filter-tabs" style="display:none;">
        <span style="font-size:12px; font-weight:600; color:#64748b; margin-right:2px;">Show:</span>
        <button class="filter-tab all active" data-filter="all" onclick="setTabFilter('all')">
            <i class="fa fa-list"></i> All Students
            <span class="tab-count" id="tab-count-all">0</span>
        </button>
        <button class="filter-tab sched" data-filter="scheduled" onclick="setTabFilter('scheduled')">
            <i class="fa fa-calendar-check-o"></i> Interview Scheduled
            <span class="tab-count" id="tab-count-sched">0</span>
        </button>
        <button class="filter-tab pending" data-filter="pending" onclick="setTabFilter('pending')">
            <i class="fa fa-calendar-times-o"></i> Not Scheduled
            <span class="tab-count" id="tab-count-pending">0</span>
        </button>
        <button class="filter-tab done" data-filter="interviewed" onclick="setTabFilter('interviewed')">
            <i class="fa fa-check-circle"></i> Interviewed
            <span class="tab-count" id="tab-count-interviewed">0</span>
        </button>
        <button class="filter-tab passed" data-filter="passed" onclick="setTabFilter('passed')">
            <i class="fa fa-exclamation-circle"></i> Passed Date
            <span class="tab-count" id="tab-count-passed">0</span>
        </button>
    </div>

    {{-- ===== TABLE CARD ===== --}}
    <div class="table-card">
        <div class="table-toolbar">
            <span class="table-toolbar-title">
                <i class="fa fa-table mr-1"></i> Students List
            </span>
            <div class="export-btns" id="export-btns" style="display:none;">
                <button class="btn-export btn-excel" onclick="exportTable('excel')">
                    <i class="fa fa-file-excel-o"></i> Excel
                </button>
                <button class="btn-export btn-pdf" onclick="exportTable('pdf')">
                    <i class="fa fa-file-pdf-o"></i> PDF
                </button>
            </div>
        </div>
        <div id="tableContainer">
            <div class="text-center p-4 text-muted">
                <i class="fa fa-spinner fa-spin fa-2x"></i>
                <div class="mt-2" style="font-size:13px;">Loading data...</div>
            </div>
        </div>
    </div>

</div>


{{-- ===================================================
     SCHEDULE INTERVIEW MODAL
===================================================== --}}
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-calendar text-primary mr-2"></i> Schedule Interview
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <div class="info-alert-blue">
                    <i class="fa fa-info-circle mr-1"></i>
                    Enrollment: <strong id="modal-enrollment-display">—</strong>
                </div>

                <div class="section-head"><i class="fa fa-book"></i> Applied Courses</div>
                <div style="border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; margin-bottom:18px;">
                    <table class="mini-table">
                        <thead><tr><th style="width:50px;">#</th><th>Course Name</th></tr></thead>
                        <tbody id="modal-courses-table"></tbody>
                    </table>
                </div>

                <hr style="border-color:#e2e8f0; margin:18px 0;">

                <div class="section-head"><i class="fa fa-clock-o"></i> Timetable</div>
                <div id="modal-timetable-table" class="timetable-list" style="margin-bottom:18px;"></div>

                <hr style="border-color:#e2e8f0; margin:18px 0;">

                <div class="section-head"><i class="fa fa-calendar-plus-o"></i> Set Interview Date &amp; Time</div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-styled">Interview Date</label>
                            <input type="date" id="interview-date" class="form-control-styled">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-styled">Interview Time</label>
                            <input type="time" id="interview-time" class="form-control-styled">
                        </div>
                    </div>
                </div>
                <div id="schedule-conflict-msg" class="schedule-status-message"></div>

                <input type="hidden" id="enrollment_id">

            </div>

            <div class="modal-footer">
                <span id="suMsg" class="mr-auto" style="font-size:13px;"></span>
                <button type="button" class="btn-cancel-mod" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
                <button type="button" id="scheduleBtn" class="btn-primary-mod">
                    <i class="fa fa-calendar-check-o"></i> Confirm Schedule
                </button>
            </div>

        </div>
    </div>
</div>


{{-- ===================================================
     MARK INTERVIEWED MODAL
===================================================== --}}
<div class="modal fade" id="markInterviewedModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-check-circle text-success mr-2"></i> Mark Interviewed
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="markInterviewedEnrollment">

                <div class="info-alert-green">
                    <i class="fa fa-info-circle mr-1"></i>
                    Marking interview for: <strong id="mark-interviewed-enrollment-display">—</strong>
                </div>

                <div class="form-group">
                    <label class="form-label-styled">
                        Remarks <span class="text-danger">*</span>
                    </label>
                    <textarea id="markInterviewedRemarks"
                              class="form-control-styled"
                              rows="4"
                              placeholder="Enter interview remarks..."
                              style="resize:vertical;"></textarea>
                </div>

                <div id="mark-interviewed-status" class="gradebook-status"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel-mod" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" class="btn-success-mod" id="confirmMarkInterviewedBtn">
                    <i class="fa fa-check"></i> Submit
                </button>
            </div>

        </div>
    </div>
</div>


{{-- ===================================================
     GRADEBOOK DETAIL MODAL
===================================================== --}}
<div class="modal fade" id="gradebookDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog gradebook-modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-info-circle text-info mr-2"></i> Gradebook Detail
                    <span id="gradebook-modal-enrollment" class="text-muted ml-2" style="font-size:12px;"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body gradebook-modal-body">
                <div class="info-alert-blue">
                    <i class="fa fa-info-circle mr-1"></i>
                    Enrollment: <strong id="gradebook-modal-enrollment-display">—</strong>
                </div>

                <div id="mentorTypeSelection" class="mentor-type-grid">
                    <button type="button" class="mentor-type-card" data-mentor-type="academic">
                        <i class="fa fa-graduation-cap"></i>
                        <div class="mentor-type-title">Academic Mentor</div>
                        <div class="mentor-type-sub">Select gradebook courses, weekdays, timings, monthly hours, and remarks.</div>
                    </button>
                    <button type="button" class="mentor-type-card" data-mentor-type="admin">
                        <i class="fa fa-user-plus"></i>
                        <div class="mentor-type-title">Admin Mentor</div>
                        <div class="mentor-type-sub">Assign the student as an admin mentor with committee remarks.</div>
                    </button>
                </div>

                <form id="gradebookMentoringForm" class="gradebook-section">
                    @csrf
                    <input type="hidden" name="enrollment_no" id="gradebookEnrollmentInput">

                    <div id="gradebook-loading" class="gradebook-placeholder">
                        <i class="fa fa-spinner fa-spin fa-2x d-block"></i>
                        <div class="gradebook-placeholder-title">Loading gradebook...</div>
                        <div>Please wait while the student courses are loaded.</div>
                    </div>

                    <div id="gradebook-content" style="display:none;">
                        <div class="gradebook-table-wrap">
                            <table class="mini-table">
                                <thead>
                                    <tr>
                                        <th style="width:45px;">#</th>
                                        <th>Session Name</th>
                                        <th>Course</th>
                                        <th style="width:90px;">Grade</th>
                                        <th style="width:80px;">Select</th>
                                    </tr>
                                </thead>
                                <tbody id="gradebook-records-table"></tbody>
                            </table>
                        </div>

                        <div class="gradebook-actions">
                            <span class="text-muted" style="font-size:12px;">Select one or more courses to configure academic mentor details.</span>
                        </div>

                        <div id="mentorAssignmentPanel" class="mentor-panel">
                            <div class="section-head"><i class="fa fa-calendar"></i> Mentor Timetable</div>
                            <div style="font-size:12px; color:#475569; font-weight:600; margin-bottom:6px;">Selected Courses</div>
                            <ul id="selectedGradebookCoursesList" class="selected-course-list"></ul>

                            <div id="mentorTimingRows"></div>
                            <button type="button" class="btn-act btn-detail" id="addMentorTimingRow">
                                <i class="fa fa-plus"></i> Add Weekday and Time
                            </button>

                            <div class="mentor-panel-grid mt-3">
                                <div>
                                    <label class="form-label-styled">Monthly Hours</label>
                                    <input type="number" name="hourse" id="gradebookMonthlyHours" class="form-control-styled" value="8" placeholder="Hours per month">
                                </div>
                                <div>
                                    <label class="form-label-styled">Remarks</label>
                                    <input type="text" name="remarks" id="gradebookRemarks" class="form-control-styled" placeholder="Remarks">
                                </div>
                            </div>
                        </div>

                        <div id="gradebook-status" class="gradebook-status"></div>
                    </div>
                </form>

                <div id="adminMentorDetailSection" class="admin-mentor-section">
                    <input type="hidden" id="detailAdminMentorEnrollment">

                    <div class="info-alert-green">
                        <i class="fa fa-info-circle mr-1"></i>
                        Assigning admin mentor role to: <strong id="detail-admin-mentor-enrollment-display">—</strong>
                    </div>

                    <div class="form-group">
                        <label class="form-label-styled">
                            Committee Remarks <span class="text-danger">*</span>
                        </label>
                        <textarea id="detailAdminMentorRemarks"
                                  class="form-control-styled"
                                  rows="4"
                                  placeholder="Enter committee remarks for this assignment..."
                                  style="resize:vertical;"></textarea>
                    </div>

                    <div id="admin-mentor-status" class="gradebook-status"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel-mod" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
                <button type="button" class="btn-success-mod" id="saveGradebookMentorBtn" style="display:none;">
                    <i class="fa fa-save"></i> Save Changes
                </button>
                <button type="button" class="btn-success-mod" id="saveDetailAdminMentorBtn" style="display:none;">
                    <i class="fa fa-check"></i> Confirm Assignment
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@section('javascript')
<script>

/* ============================================================
   GLOBALS
============================================================ */
var allData      = [];       // full data from AJAX
var activeFilter = 'all';    // current tag filter: all / scheduled / pending
var dtInstance   = null;     // DataTable reference
var defaultSessionId = @json($currentSessionId ?? null);
var defaultSessionApplied = false;
var suppressSessionRequest = false;
var currentModalTimetable = [];
var currentModalTimetableLoaded = false;
var currentModalTimetableLoadFailed = false;
var gradebookTimeslots = [];
var currentGradebookRecords = [];
var currentGradebookEnrollment = '';

/* ============================================================
   DEFAULT SESSION
============================================================ */
function setDefaultSession() {
    if (defaultSessionApplied) return;

    defaultSessionApplied = true;

    if (defaultSessionId) {
        suppressSessionRequest = true;
        $('#facultyWload_sess_id').combobox('setValue', defaultSessionId);
        suppressSessionRequest = false;
        $('#sess_id').val(defaultSessionId);
    }

    getRequest();
}

/* ============================================================
   RESET FILTERS
   Clears school, keeps session, resets tab to 'all'
============================================================ */
function resetFilters() {
    var icon = document.getElementById('reset-icon');
    icon.style.transition = 'transform 0.6s ease';
    icon.style.transform  = 'rotate(360deg)';
    setTimeout(function () {
        icon.style.transition = 'none';
        icon.style.transform  = 'rotate(0deg)';
        setTimeout(function () { icon.style.transition = 'transform 0.6s ease'; }, 50);
    }, 650);

    // Clear school only
    $('#school_id').val('');
    $('#facultyWload_school_id').combobox('clear');

    // Reset tab
    setTabFilter('all', false);

    getRequest();
}

/* ============================================================
   GET REQUEST
============================================================ */
function getRequest() {
    var sessId   = $('#sess_id').val();
    var schoolId = $('#school_id').val();

    // Reset UI to loading state
    ['stat-total','stat-scheduled','stat-pending','stat-avg'].forEach(function(id) {
        document.getElementById(id).textContent = '—';
    });
    document.getElementById('student-count-badge').innerHTML =
        '<i class="fa fa-spinner fa-spin mr-1"></i> Loading...';
    document.getElementById('export-btns').style.display  = 'none';
    document.getElementById('filter-tabs').style.display  = 'none';

    $('#tableContainer').html(
        '<div class="text-center p-4 text-muted">' +
            '<i class="fa fa-spinner fa-spin fa-2x"></i>' +
            '<div class="mt-2" style="font-size:13px;">Loading data...</div>' +
        '</div>'
    );

    $.ajax({
        url:      '{{ url('get-scholarship-students') }}',
        type:     'GET',
        dataType: 'json',
        data:     { sess_id: sessId, school_id: schoolId },
        success: function (data) {
            allData = data;
            updateStats();
            updateTabCounts();
            if (activeFilter !== 'all' && getRowsForFilter(activeFilter).length === 0) {
                activeFilter = 'all';
            }
            setTabFilter(activeFilter, false);
            renderTable(activeFilter);
            document.getElementById('filter-tabs').style.display = 'flex';
            document.getElementById('export-btns').style.display = 'flex';
        },
        error: function () {
            $('#tableContainer').html(
                '<div class="text-center py-5 text-danger">' +
                    '<i class="fa fa-exclamation-triangle fa-2x mb-2 d-block"></i>' +
                    'Failed to load data. Please try again.' +
                '</div>'
            );
            document.getElementById('student-count-badge').innerHTML =
                '<i class="fa fa-exclamation-triangle mr-1"></i> Error';
        }
    });
}

/* ============================================================
   UPDATE STATS
============================================================ */
function updateStats() {
    var total     = allData.length;
    var scheduled = allData.filter(isStudentScheduled).length;
    var pending   = allData.filter(function(d) { return !d.interview_datetime; }).length;
    var sum       = allData.reduce(function(s, d) { return s + parseFloat(d.total_percentage || 0); }, 0);
    var avgPct    = total > 0 ? (sum / total).toFixed(1) : '0.0';

    document.getElementById('stat-total').textContent     = total;
    document.getElementById('stat-scheduled').textContent = scheduled;
    document.getElementById('stat-pending').textContent   = pending;
    document.getElementById('stat-avg').textContent       = avgPct + '%';
    document.getElementById('student-count-badge').innerHTML =
        '<i class="fa fa-users mr-1"></i> ' + total + ' student' + (total !== 1 ? 's' : '');
}

/* ============================================================
   UPDATE TAB COUNTS
============================================================ */
function updateTabCounts() {
    var scheduled = allData.filter(isStudentScheduled).length;
    var pending   = allData.filter(function(d) { return !d.interview_datetime; }).length;
    var interviewed = allData.filter(isStudentInterviewed).length;
    var passed = allData.filter(isStudentPassedDate).length;
    document.getElementById('tab-count-all').textContent     = allData.length;
    document.getElementById('tab-count-sched').textContent   = scheduled;
    document.getElementById('tab-count-pending').textContent = pending;
    document.getElementById('tab-count-interviewed').textContent = interviewed;
    document.getElementById('tab-count-passed').textContent = passed;
}

/* ============================================================
   SET TAB FILTER
============================================================ */
function setTabFilter(filter, doRender) {
    if (doRender === undefined) doRender = true;
    activeFilter = filter;
    document.querySelectorAll('.filter-tab').forEach(function(btn) {
        btn.classList.toggle('active', btn.dataset.filter === filter);
    });
    if (doRender) renderTable(filter);
}

function getRowsForFilter(filter) {
    if (filter === 'scheduled') return allData.filter(isStudentScheduled);
    if (filter === 'pending') return allData.filter(function(d) { return !d.interview_datetime; });
    if (filter === 'interviewed') return allData.filter(isStudentInterviewed);
    if (filter === 'passed') return allData.filter(isStudentPassedDate);

    return allData;
}

/* ============================================================
   RENDER TABLE
   Table column order: # | Enrollment | School | Dept | Program | % | Interview | Profile | Actions
============================================================ */
function renderTable(filter) {
    // Apply tag filter
    var rows = getRowsForFilter(filter);

    var tbody = '';

    if (rows.length > 0) {
        rows.forEach(function(item, i) {
            var pct      = parseFloat(item.total_percentage || 0);
            var pctClass = pct >= 80 ? 'high' : (pct >= 65 ? 'mid' : 'low');
            var isInterviewed = isStudentInterviewed(item);
            var isPastInterview = isStudentPassedDate(item);

            var intStatus = '<div class="int-pending"><i class="fa fa-calendar-times-o"></i> Not Scheduled</div>';
            if (item.interview_datetime && isInterviewed) {
                intStatus = '<div class="int-interviewed"><i class="fa fa-check-circle"></i> Interviewed: ' + item.interview_datetime + '</div>';
            } else if (item.interview_datetime && isPastInterview) {
                intStatus = '<div class="int-expired"><i class="fa fa-exclamation-circle"></i> Date Passed: ' + item.interview_datetime + '</div>';
            } else if (item.interview_datetime) {
                intStatus = '<div class="int-scheduled"><i class="fa fa-calendar-check-o"></i> ' + item.interview_datetime + '</div>';
            }

            var schedLabel = item.interview_datetime ? 'Reschedule' : 'Schedule';
            var interviewTime = item.interview_time ? String(item.interview_time).substring(0, 5) : '';
            var scheduleAction = isInterviewed
                ? ''
                : '<div><button type="button" class="btn-act btn-schedule schedule-interview-btn" ' +
                    'data-enrollment="' + escapeAttr(item.sai_enrollment_no) + '" ' +
                    'data-interview-date="' + escapeAttr(item.interview_date) + '" ' +
                    'data-interview-time="' + escapeAttr(interviewTime) + '">' +
                    '<i class="fa fa-calendar"></i> ' + schedLabel +
                '</button></div>';
            var markInterviewedAction = item.interview_datetime && !isInterviewed
                ? '<div><button type="button" class="btn-act btn-interviewed mark-interviewed-btn" ' +
                    'data-enrollment="' + escapeAttr(item.sai_enrollment_no) + '">' +
                    '<i class="fa fa-check-circle"></i> Mark Interviewed' +
                '</button></div>'
                : '';

            tbody +=
                '<tr>' +
                    '<td class="text-center" style="font-size:12px;color:#94a3b8;">' + (i + 1) + '</td>' +
                    '<td><span class="enrollment-chip">' + (item.sai_enrollment_no || '-') + '</span></td>' +
                    '<td><span class="school-badge"><i class="fa fa-building-o" style="font-size:11px;"></i> ' + (item.school_code || '-') + '</span></td>' +
                    '<td class="dept-text">' + (item.dept_name || '-') + '</td>' +
                    '<td class="prog-text">' + (item.prog_name || '-') + '</td>' +
                    '<td>' +
                        '<div class="pct-wrap">' +
                            '<span class="pct-text">' + pct.toFixed(1) + '%</span>' +
                            '<div class="pct-bar"><div class="pct-fill ' + pctClass + '" style="width:' + Math.min(pct, 100) + '%;"></div></div>' +
                        '</div>' +
                    '</td>' +
                    '<td>' +
                        intStatus +
                        scheduleAction +
                        markInterviewedAction +
                    '</td>' +
                    '<td>' +
                        '<a href="{{ url('reports-generator-student-profile') }}/' + item.sai_enrollment_no + '" target="_blank" class="btn-act btn-profile">' +
                            '<i class="fa fa-user"></i> Profile' +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<div class="actions-cell">' +
                            '<button type="button" class="btn-act btn-detail gradebook-detail-btn" ' +
                                'data-enrollment="' + escapeAttr(item.sai_enrollment_no) + '">' +
                                '<i class="fa fa-info-circle"></i> Detail' +
                            '</button>' +
                        '</div>' +
                    '</td>' +
                '</tr>';
        });
    } else {
        tbody =
            '<tr><td colspan="9" class="text-center py-5 text-muted">' +
                '<i class="fa fa-inbox fa-2x mb-2 d-block"></i>' +
                'No students found for this filter.' +
            '</td></tr>';
    }

    // Safely destroy previous DataTable
    if (dtInstance !== null) {
        try { dtInstance.destroy(); } catch(e) {}
        dtInstance = null;
    }

    $('#tableContainer').html(
        '<div style="overflow-x:auto;">' +
            '<table class="table table-hover mb-0" id="scholarshipTable" style="width:100%">' +
                '<thead>' +
                    '<tr>' +
                        '<th class="text-center" style="width:40px;">#</th>' +
                        '<th>Enrollment No</th>' +
                        '<th>School</th>' +
                        '<th>Department</th>' +
                        '<th>Program</th>' +
                        '<th>Percentage</th>' +
                        '<th>Interview</th>' +
                        '<th>Profile</th>' +
                        '<th>Actions</th>' +
                    '</tr>' +
                '</thead>' +
                '<tbody>' + tbody + '</tbody>' +
            '</table>' +
        '</div>'
    );

    dtInstance = $('#scholarshipTable').DataTable({
        scrollX:    false,
        responsive: true,
        pageLength: 100,
        lengthMenu: [10, 25, 50, 100, 500],
        order:      [[0, 'asc']],
        dom:        'frtip',
        language: {
            search:            '',
            searchPlaceholder: 'Search...',
            paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Previous' }
        }
    });
}

/* ============================================================
   EXPORT
============================================================ */
function exportTable(type) {
    if (!dtInstance) return;
    if (type === 'excel') dtInstance.button('.buttons-excel').trigger();
    if (type === 'pdf')   dtInstance.button('.buttons-pdf').trigger();
}

function escapeAttr(value) {
    return String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function setGradebookStatus(message, type) {
    var el = document.getElementById('gradebook-status');
    el.className = 'gradebook-status' + (type ? ' is-' + type : '');
    el.innerHTML = message || '';
}

function setAdminMentorStatus(message, type) {
    var el = document.getElementById('admin-mentor-status');
    el.className = 'gradebook-status' + (type ? ' is-' + type : '');
    el.innerHTML = message || '';
}

function setMarkInterviewedStatus(message, type) {
    var el = document.getElementById('mark-interviewed-status');
    el.className = 'gradebook-status' + (type ? ' is-' + type : '');
    el.innerHTML = message || '';
}

function resetGradebookModal(enrollmentNo) {
    currentGradebookRecords = [];
    gradebookTimeslots = [];
    currentGradebookEnrollment = enrollmentNo || '';

    $('#gradebook-modal-enrollment').text(enrollmentNo ? '(' + enrollmentNo + ')' : '');
    $('#gradebook-modal-enrollment-display').text(enrollmentNo || '—');
    $('#gradebookEnrollmentInput').val(enrollmentNo || '');
    $('#detailAdminMentorEnrollment').val(enrollmentNo || '');
    $('#detail-admin-mentor-enrollment-display').text(enrollmentNo || '—');
    $('#gradebook-records-table').html('');
    $('#selectedGradebookCoursesList').html('');
    $('#mentorTimingRows').html('');
    $('#mentorAssignmentPanel').removeClass('is-visible');
    $('#gradebookMentoringForm').removeClass('is-visible');
    $('#adminMentorDetailSection').removeClass('is-visible');
    $('.mentor-type-card').removeClass('is-active');
    $('#saveGradebookMentorBtn').hide();
    $('#saveDetailAdminMentorBtn').hide();
    $('#gradebookMonthlyHours').val(8);
    $('#gradebookRemarks').val('');
    $('#detailAdminMentorRemarks').val('');
    setGradebookStatus('', '');
    setAdminMentorStatus('', '');
    $('#gradebook-content').hide();
    $('#gradebook-loading').hide();
}

function renderGradebookRows(records) {
    if (!records || records.length === 0) {
        $('#gradebook-records-table').html(
            '<tr><td colspan="5" class="text-center text-muted py-3">No records found.</td></tr>'
        );
        return;
    }

    var rows = records.map(function(record, i) {
        var courseId = record.gh_co_id || record.GH_CO_ID || '';
        var course = record.course || record.COURSE || '-';
        var sessionName = record.session_name || record.SESSION_NAME || '-';
        var grade = record.grade || record.GRADE || '-';

        return '<tr>' +
            '<td>' + (i + 1) + '</td>' +
            '<td>' + escapeAttr(sessionName) + '</td>' +
            '<td>' + escapeAttr(course) + '</td>' +
            '<td><span class="enrollment-chip">' + escapeAttr(grade) + '</span></td>' +
            '<td class="text-center">' +
                '<input type="checkbox" class="gradebook-course-checkbox" name="course_id[]" ' +
                    'value="' + escapeAttr(courseId) + '" data-course="' + escapeAttr(course) + '">' +
            '</td>' +
        '</tr>';
    }).join('');

    $('#gradebook-records-table').html(rows);
}

function getTimeslotOptions() {
    return gradebookTimeslots.map(function(slot) {
        var id = slot.ts_id || slot.TS_ID;
        var name = slot.ts_name || slot.TS_NAME;

        return '<option value="' + escapeAttr(id) + '">' + escapeAttr(name) + '</option>';
    }).join('');
}

function addMentorTimingRow() {
    var options = getTimeslotOptions();
    var row =
        '<div class="timing-row">' +
            '<div>' +
                '<label class="form-label-styled">Weekday</label>' +
                '<select name="weekdays[]" class="form-control-styled mentor-required-field">' +
                    '<option value="">Select Weekday</option>' +
                    '<option value="1">Monday</option>' +
                    '<option value="2">Tuesday</option>' +
                    '<option value="3">Wednesday</option>' +
                    '<option value="4">Thursday</option>' +
                    '<option value="5">Friday</option>' +
                    '<option value="6">Saturday</option>' +
                    '<option value="7">Sunday</option>' +
                '</select>' +
            '</div>' +
            '<div>' +
                '<label class="form-label-styled">From Time</label>' +
                '<select name="fromtiming[]" class="form-control-styled mentor-required-field">' +
                    '<option value="">Select From Time</option>' + options +
                '</select>' +
            '</div>' +
            '<div>' +
                '<label class="form-label-styled">To Time</label>' +
                '<select name="totiming[]" class="form-control-styled mentor-required-field">' +
                    '<option value="">Select To Time</option>' + options +
                '</select>' +
            '</div>' +
            '<div>' +
                '<button type="button" class="btn-act btn-reset remove-mentor-timing-row">' +
                    '<i class="fa fa-trash"></i> Remove' +
                '</button>' +
            '</div>' +
        '</div>';

    $('#mentorTimingRows').append(row);
}

function getSelectedGradebookCourses() {
    return $('.gradebook-course-checkbox:checked').map(function() {
        return {
            id: $(this).val(),
            course: $(this).data('course')
        };
    }).get();
}

function showMentorAssignmentPanel() {
    var selectedCourses = getSelectedGradebookCourses();

    if (selectedCourses.length === 0) {
        $('#selectedGradebookCoursesList').html('');
        $('#mentorAssignmentPanel').removeClass('is-visible');
        $('#saveGradebookMentorBtn').hide();
        setGradebookStatus('', '');
        return;
    }

    $('#selectedGradebookCoursesList').html(selectedCourses.map(function(item) {
        return '<li><strong>' + escapeAttr(item.course) + '</strong> <span class="text-muted">(Course ID: ' + escapeAttr(item.id) + ')</span></li>';
    }).join(''));

    if ($('#mentorTimingRows .timing-row').length === 0) {
        addMentorTimingRow();
    }

    $('#mentorAssignmentPanel').addClass('is-visible');
    $('#saveGradebookMentorBtn').show();
    setGradebookStatus('', '');
}

function validateMentorAssignmentForm() {
    if (getSelectedGradebookCourses().length === 0) {
        setGradebookStatus('<i class="fa fa-exclamation-circle"></i> Please select at least one course.', 'error');
        return false;
    }

    if ($('#mentorTimingRows .timing-row').length === 0) {
        setGradebookStatus('<i class="fa fa-exclamation-circle"></i> Please add at least one weekday and time slot.', 'error');
        return false;
    }

    var hasEmptyField = false;
    $('.mentor-required-field').each(function() {
        if (!$(this).val()) {
            hasEmptyField = true;
        }
    });

    if (hasEmptyField) {
        setGradebookStatus('<i class="fa fa-exclamation-circle"></i> Please complete all weekday and timing fields.', 'error');
        return false;
    }

    return true;
}

function loadGradebookDetails(enrollmentNo) {
    $('#gradebookMentoringForm').addClass('is-visible');
    $('#adminMentorDetailSection').removeClass('is-visible');
    $('#saveDetailAdminMentorBtn').hide();
    $('#gradebook-content').hide();
    $('#gradebook-loading').show();
    setGradebookStatus('', '');

    $.ajax({
        url: '{{ route("pm-gradebook-details") }}',
        type: 'POST',
        dataType: 'json',
        data: {
            _token: '{{ csrf_token() }}',
            enrollment_no: enrollmentNo
        },
        success: function(resp) {
            currentGradebookRecords = resp.gradebook || [];
            gradebookTimeslots = resp.timeslots || [];
            renderGradebookRows(currentGradebookRecords);
            $('#gradebook-loading').hide();
            $('#gradebook-content').show();
        },
        error: function() {
            $('#gradebook-loading').hide();
            $('#gradebook-content').show();
            $('#gradebook-records-table').html(
                '<tr><td colspan="5" class="text-center text-danger py-3">Failed to load gradebook details.</td></tr>'
            );
            setGradebookStatus('<i class="fa fa-exclamation-circle"></i> Failed to load gradebook details. Please try again.', 'error');
        }
    });
}

function getTodayDateValue() {
    var today = new Date();
    var month = String(today.getMonth() + 1).padStart(2, '0');
    var day = String(today.getDate()).padStart(2, '0');

    return today.getFullYear() + '-' + month + '-' + day;
}

function setInterviewDateMin() {
    document.getElementById('interview-date').setAttribute('min', getTodayDateValue());
}

function getDayNameFromDate(dateValue) {
    var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    var date = new Date(dateValue + 'T00:00:00');

    return days[date.getDay()];
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
    var currentMinutes = (now.getHours() * 60) + now.getMinutes();

    return interviewMinutes < currentMinutes;
}

function isStudentInterviewed(item) {
    return String(item.interviewed || '0') === '1';
}

function isStudentPassedDate(item) {
    return !!item.interview_datetime && !isStudentInterviewed(item) && isInterviewSlotPassed(item.interview_date, item.interview_time);
}

function isStudentScheduled(item) {
    return !!item.interview_datetime && !isStudentInterviewed(item) && !isStudentPassedDate(item);
}

function setScheduleStatus(message, type) {
    var el = document.getElementById('schedule-conflict-msg');
    el.className = 'schedule-status-message' + (type ? ' is-' + type : '');
    el.innerHTML = message || '';
}

function findScheduleConflict(dateValue, timeValue) {
    var selectedDay = getDayNameFromDate(dateValue);
    var selectedMinutes = parseTimeToMinutes(timeValue);

    if (selectedMinutes === null) return null;

    for (var i = 0; i < currentModalTimetable.length; i++) {
        var slot = currentModalTimetable[i];

        if ((slot.tt_subject_day || '').toLowerCase() !== selectedDay.toLowerCase()) {
            continue;
        }

        var fromMinutes = parseTimeToMinutes(slot.tt_subject_time_from);
        var toMinutes = parseTimeToMinutes(slot.tt_subject_time_to);

        if (fromMinutes === null || toMinutes === null) {
            continue;
        }

        if (selectedMinutes >= fromMinutes && selectedMinutes < toMinutes) {
            return {
                day: selectedDay,
                course: slot.course_cd_name || 'N/A',
                teacher: slot.teachername || 'N/A',
                from: slot.tt_subject_time_from || 'N/A',
                to: slot.tt_subject_time_to || 'N/A'
            };
        }
    }

    return null;
}

function validateSelectedScheduleSlot(showMessages) {
    var date = document.getElementById('interview-date').value;
    var time = document.getElementById('interview-time').value;
    var today = getTodayDateValue();

    setScheduleStatus('', '');

    if (!date || !time) {
        return { valid: true };
    }

    if (date < today) {
        if (showMessages) {
            setScheduleStatus('<i class="fa fa-exclamation-circle"></i> Past dates cannot be selected. Please choose today or a future date.', 'error');
        }
        return { valid: false };
    }

    if (!currentModalTimetableLoaded) {
        var loadMsg = currentModalTimetableLoadFailed
            ? '<i class="fa fa-exclamation-circle"></i> The timetable could not be loaded, so the busy slot cannot be verified. Please close the modal and try again.'
            : '<i class="fa fa-info-circle"></i> The timetable is still loading. Please wait.';

        setScheduleStatus(loadMsg, currentModalTimetableLoadFailed ? 'error' : 'info');
        return { valid: false };
    }

    var conflict = findScheduleConflict(date, time);
    if (conflict) {
        setScheduleStatus(
            '<i class="fa fa-ban"></i> The student is busy: <strong>' + escapeAttr(conflict.course) + '</strong> with ' +
            escapeAttr(conflict.teacher) + ' on ' + escapeAttr(conflict.day) + ', ' +
            escapeAttr(conflict.from) + ' to ' + escapeAttr(conflict.to) + '.',
            'error'
        );
        return { valid: false, conflict: conflict };
    }

    if (showMessages) {
        setScheduleStatus('<i class="fa fa-check-circle"></i> The student is available at the selected date and time.', 'ok');
    }

    return { valid: true };
}

setInterviewDateMin();

function renderGroupedTimetable(timetable) {
    if (!timetable || timetable.length === 0) {
        return '<div class="modal-empty-state">No timetable found.</div>';
    }

    var dayOrder = {
        Monday: 1,
        Tuesday: 2,
        Wednesday: 3,
        Thursday: 4,
        Friday: 5,
        Saturday: 6,
        Sunday: 7
    };
    var grouped = [];
    var groupIndex = {};

    timetable.forEach(function(t) {
        var course = t.course_cd_name || 'N/A';
        var teacher = t.teachername || 'N/A';
        var key = course + '|' + teacher;

        if (groupIndex[key] === undefined) {
            groupIndex[key] = grouped.length;
            grouped.push({
                course: course,
                teacher: teacher,
                slots: []
            });
        }

        grouped[groupIndex[key]].slots.push({
            day: t.tt_subject_day || 'N/A',
            from: t.tt_subject_time_from || 'N/A',
            to: t.tt_subject_time_to || 'N/A'
        });
    });

    return grouped.map(function(group, i) {
        group.slots.sort(function(a, b) {
            var dayDiff = (dayOrder[a.day] || 99) - (dayOrder[b.day] || 99);
            if (dayDiff !== 0) return dayDiff;
            return String(a.from).localeCompare(String(b.from));
        });

        var slotsHtml = group.slots.map(function(slot) {
            return '<div class="time-slot-chip">' +
                '<span class="time-slot-day"><i class="fa fa-calendar-o"></i> ' + escapeAttr(slot.day) + '</span>' +
                '<span class="time-slot-time">' +
                    '<i class="fa fa-clock-o text-primary"></i>' +
                    '<span><small>Busy</small><br>' + escapeAttr(slot.from) + ' to ' + escapeAttr(slot.to) + '</span>' +
                '</span>' +
            '</div>';
        }).join('');

        return '<div class="timetable-card">' +
            '<div class="timetable-card-head">' +
                '<span class="timetable-number">' + (i + 1) + '</span>' +
                '<div>' +
                    '<div class="timetable-course">' + escapeAttr(group.course) + '</div>' +
                    '<div class="timetable-teacher"><i class="fa fa-user-o"></i> ' + escapeAttr(group.teacher) + '</div>' +
                '</div>'+
            '</div>' +
            '<div class="time-slot-wrap">' + slotsHtml + '</div>' +
        '</div>';
    }).join('');
}

/* ============================================================
   VIEW DETAILS
============================================================ */
function viewDetails(enrollmentNo, interviewDate, interviewTime) {
    // ✅ Clear ALL previous values
    currentModalTimetable = [];
    currentModalTimetableLoaded = false;
    currentModalTimetableLoadFailed = false;
    setInterviewDateMin();

    document.getElementById('enrollment_id').value             = '';
    document.getElementById('interview-date').value            = '';
    document.getElementById('interview-time').value            = '';
    document.getElementById('modal-enrollment-display').textContent = '—';
    document.getElementById('suMsg').textContent               = '';
    setScheduleStatus('', '');
    document.getElementById('modal-courses-table').innerHTML   =
        '<tr><td colspan="2" class="text-center text-muted py-3"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>';
    document.getElementById('modal-timetable-table').innerHTML =
        '<div class="modal-empty-state"><i class="fa fa-spinner fa-spin"></i> Loading timetable...</div>';

    // ✅ Set new enrollment
    document.getElementById('enrollment_id').value             = enrollmentNo;
    document.getElementById('modal-enrollment-display').textContent = enrollmentNo;
    document.getElementById('interview-date').value            = interviewDate || '';
    document.getElementById('interview-time').value            = interviewTime || '';

    $('#detailModal').modal('show');

    fetch('/be_mentors_list_courses?md_enrollment_no=' + enrollmentNo)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                document.getElementById('modal-courses-table').innerHTML =
                    data.courses.length === 0
                        ? '<tr><td colspan="2" class="text-center text-muted py-3">No courses found.</td></tr>'
                        : data.courses.map(function(c, i) {
                            return '<tr><td style="width:40px;">' + (i+1) + '</td><td>' + c.course_title + '</td></tr>';
                          }).join('');

                currentModalTimetable = data.timetable || [];
                currentModalTimetableLoaded = true;
                document.getElementById('modal-timetable-table').innerHTML = renderGroupedTimetable(data.timetable);
                validateSelectedScheduleSlot(false);
            } else {
                currentModalTimetableLoadFailed = true;
                var msg = data.message || 'Failed to fetch.';
                document.getElementById('modal-courses-table').innerHTML   =
                    '<tr><td colspan="2" class="text-danger text-center py-2">' + msg + '</td></tr>';
                document.getElementById('modal-timetable-table').innerHTML =
                    '<div class="modal-empty-state text-danger">' + msg + '</div>';
            }
        })
        .catch(function() {
            currentModalTimetableLoadFailed = true;
            document.getElementById('modal-courses-table').innerHTML   =
                '<tr><td colspan="2" class="text-danger text-center py-2">Error fetching data.</td></tr>';
            document.getElementById('modal-timetable-table').innerHTML =
                '<div class="modal-empty-state text-danger">Error fetching timetable.</div>';
        });
}

$(document).on('click', '.schedule-interview-btn', function () {
    viewDetails(
        $(this).data('enrollment'),
        $(this).data('interviewDate') || '',
        $(this).data('interviewTime') || ''
    );
});

/* ============================================================
   CLEAR MODAL — on hide (X button, backdrop, or after schedule)
============================================================ */
$('#detailModal').on('hidden.bs.modal', function () {
    currentModalTimetable = [];
    currentModalTimetableLoaded = false;
    currentModalTimetableLoadFailed = false;

    document.getElementById('enrollment_id').value             = '';
    document.getElementById('interview-date').value            = '';
    document.getElementById('interview-time').value            = '';
    document.getElementById('suMsg').textContent               = '';
    setScheduleStatus('', '');
    document.getElementById('modal-enrollment-display').textContent = '—';
    document.getElementById('modal-courses-table').innerHTML   = '';
    document.getElementById('modal-timetable-table').innerHTML = '';
});

$('#interview-date, #interview-time').on('change input', function () {
    validateSelectedScheduleSlot(true);
});

$(document).on('click', '.gradebook-detail-btn', function () {
    var enrollmentNo = $(this).data('enrollment');

    resetGradebookModal(enrollmentNo);
    $('#gradebookDetailModal').modal('show');
});

$('#gradebookDetailModal').on('hidden.bs.modal', function () {
    resetGradebookModal('');
});

$(document).on('click', '.mentor-type-card', function () {
    var type = $(this).data('mentorType');

    $('.mentor-type-card').removeClass('is-active');
    $(this).addClass('is-active');

    if (type === 'academic') {
        loadGradebookDetails(currentGradebookEnrollment);
        return;
    }

    $('#gradebookMentoringForm').removeClass('is-visible');
    $('#mentorAssignmentPanel').removeClass('is-visible');
    $('#saveGradebookMentorBtn').hide();
    $('#adminMentorDetailSection').addClass('is-visible');
    $('#saveDetailAdminMentorBtn').show();
    setGradebookStatus('', '');
    setAdminMentorStatus('', '');
});

$('#addMentorTimingRow').on('click', function () {
    addMentorTimingRow();
});

$(document).on('change', '.gradebook-course-checkbox', function () {
    showMentorAssignmentPanel();
});

$(document).on('click', '.remove-mentor-timing-row', function () {
    $(this).closest('.timing-row').remove();
});

$(document).on('click', '.mark-interviewed-btn', function () {
    var enrollmentNo = $(this).data('enrollment');

    $('#markInterviewedEnrollment').val(enrollmentNo);
    $('#mark-interviewed-enrollment-display').text(enrollmentNo || '—');
    $('#markInterviewedRemarks').val('');
    setMarkInterviewedStatus('', '');
    $('#markInterviewedModal').modal('show');
});

$('#markInterviewedModal').on('hidden.bs.modal', function () {
    $('#markInterviewedEnrollment').val('');
    $('#mark-interviewed-enrollment-display').text('—');
    $('#markInterviewedRemarks').val('');
    setMarkInterviewedStatus('', '');
});

$('#confirmMarkInterviewedBtn').on('click', function () {
    var enrollmentNo = $('#markInterviewedEnrollment').val();
    var remarks = $('#markInterviewedRemarks').val().trim();
    var sessionId = $('#sess_id').val() || $('#facultyWload_sess_id').combobox('getValue');

    if (!remarks) {
        setMarkInterviewedStatus('<i class="fa fa-exclamation-circle"></i> Remarks are required.', 'error');
        return;
    }

    $.ajax({
        url: '{{ route("mark-interviewed") }}',
        type: 'POST',
        dataType: 'json',
        data: {
            _token: '{{ csrf_token() }}',
            enrollment_no: enrollmentNo,
            sess_id: sessionId,
            remarks: remarks
        },
        success: function (res) {
            setMarkInterviewedStatus('<i class="fa fa-check-circle"></i> Interview marked successfully.', 'success');
            showToast('success', res.message || 'Interview marked successfully.', 'Success');

            setTimeout(function () {
                $('#markInterviewedModal').modal('hide');
                getRequest();
            }, 600);
        },
        error: function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to mark interview.';
            setMarkInterviewedStatus('<i class="fa fa-exclamation-circle"></i> ' + escapeAttr(msg), 'error');
            showToast('error', msg, 'Error');
        }
    });
});

$('#saveGradebookMentorBtn').on('click', function () {
    if (!validateMentorAssignmentForm()) {
        return;
    }

    var form = document.getElementById('gradebookMentoringForm');
    var formData = new FormData(form);
    var sessionId = $('#sess_id').val() || $('#facultyWload_sess_id').combobox('getValue');
    formData.set('sess_id', sessionId);

    $.ajax({
        url: '{{ route("makeMentor") }}',
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function () {
            setGradebookStatus('<i class="fa fa-check-circle"></i> Mentor details saved successfully.', 'success');
            showToast('success', 'Mentor details saved successfully.', 'Success');

            setTimeout(function () {
                $('#gradebookDetailModal').modal('hide');
                getRequest();
            }, 600);
        },
        error: function () {
            setGradebookStatus('<i class="fa fa-exclamation-circle"></i> An error occurred while saving mentor details.', 'error');
            showToast('error', 'An error occurred while saving mentor details.', 'Error');
        }
    });
});

$('#saveDetailAdminMentorBtn').on('click', function () {
    var enrollmentNo = $('#detailAdminMentorEnrollment').val();
    var remarks = $('#detailAdminMentorRemarks').val().trim();
    var sessionId = $('#sess_id').val() || $('#facultyWload_sess_id').combobox('getValue');

    if (!remarks) {
        setAdminMentorStatus('<i class="fa fa-exclamation-circle"></i> Committee remarks are required.', 'error');
        return;
    }

    $.ajax({
        url:  '{{ url("make-admin-mentor") }}',
        type: 'POST',
        data: {
            _token:             '{{ csrf_token() }}',
            enrollment_no:       enrollmentNo,
            sess_id:             sessionId,
            m_committee_remarks: remarks
        },
        success: function (res) {
            setAdminMentorStatus('<i class="fa fa-check-circle"></i> Admin mentor assigned successfully.', 'success');
            showToast('success', res.message || 'Admin mentor assigned successfully.', 'Success');

            setTimeout(function () {
                $('#gradebookDetailModal').modal('hide');
                getRequest();
            }, 600);
        },
        error: function () {
            setAdminMentorStatus('<i class="fa fa-exclamation-circle"></i> Failed to assign admin mentor.', 'error');
            showToast('error', 'Failed to assign admin mentor.', 'Error');
        }
    });
});

/* ============================================================
   SCHEDULE SUBMIT
============================================================ */
document.getElementById('scheduleBtn').addEventListener('click', function () {
    var date         = document.getElementById('interview-date').value;
    var time         = document.getElementById('interview-time').value;
    var enrollmentNo = document.getElementById('enrollment_id').value;
    var sessionId    = $('#sess_id').val() || $('#facultyWload_sess_id').combobox('getValue');

    if (!date || !time) {
        showToast('error', 'Please select both date and time.', 'Validation');
        return;
    }

    var validation = validateSelectedScheduleSlot(true);
    if (!validation.valid) {
        showToast('error', 'The student is busy at the selected slot, or the timetable could not be verified.', 'Validation');
        return;
    }

    fetch('/schedule-interview', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ data: { is_interview_date: date, is_interview_time: time, enrollmentNo: enrollmentNo, sess_id: sessionId } })
    })
    .then(function(r) { return r.json(); })
    .then(function(resp) {
        showToast('success', resp.message || 'Interview scheduled successfully!', 'Success');
        $('#detailModal').modal('hide'); // hidden.bs.modal will auto-clear fields
        getRequest();
    })
    .catch(function() {
        showToast('error', 'An error occurred while scheduling.', 'Error');
    });
});

</script>
@endsection