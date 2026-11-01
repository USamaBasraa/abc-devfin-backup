@extends('layouts.app')

@section('content')
@include('components.toast-notifications')

<style>
.schol-header {
    background: linear-gradient(135deg, #1e293b 0%, #2d3f55 100%);
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.schol-header h3 { color: #fff; font-size: 20px; font-weight: 600; margin: 0; font-family: 'Times New Roman', Times, serif; }
.schol-header-sub { color: #94a3b8; font-size: 13px; margin-top: 3px; }
.schol-badge-count {
    background: rgba(59,130,246,0.2); color: #93c5fd; font-size: 12px; padding: 5px 12px;
    border-radius: 20px; border: 1px solid rgba(59,130,246,0.3); font-weight: 500;
}
.filters-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 16px;
}
.filters-row {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 12px 14px;
}
.filter-field {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.filter-field--session { flex: 0 0 200px; max-width: 220px; }
.filter-field--school { flex: 0 0 200px; max-width: 220px; }
.filter-field--course { flex: 1 1 260px; min-width: 220px; }
.filters-actions {
    flex: 0 0 auto;
    display: flex;
    align-items: flex-end;
    padding-bottom: 1px;
}
.field-label {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: block;
    margin-bottom: 5px;
    line-height: 1.2;
}
.field-label i { opacity: 0.85; }
.filters-card .filter-control {
    width: 100%;
    min-height: 38px;
}
.filters-card .textbox {
    width: 100% !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    box-shadow: none !important;
    background: #fff !important;
    height: 38px !important;
}
.filters-card .textbox-focused {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12) !important;
}
.filters-card .textbox .textbox-text {
    height: 36px !important;
    line-height: 36px !important;
    padding: 0 32px 0 10px !important;
    font-size: 13px !important;
    color: #334155 !important;
    border-radius: 8px !important;
    background: transparent !important;
}
.filters-card .textbox .textbox-addon {
    right: 2px !important;
}
.filters-card .textbox .combo-arrow {
    width: 28px !important;
    height: 34px !important;
    opacity: 0.55;
    background-position: center center !important;
}
.filters-card .select2-container {
    width: 100% !important;
}
.filters-card .select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #fff;
}
.filters-card .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    padding-left: 10px;
    padding-right: 28px;
    font-size: 13px;
    color: #334155;
}
.filters-card .select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #94a3b8;
}
.filters-card .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
    right: 6px;
}
.filters-card .select2-container--default.select2-container--focus .select2-selection--single,
.filters-card .select2-container--default.select2-container--open .select2-selection--single {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
}
.btn-reset {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-width: 88px;
    height: 38px;
    padding: 0 16px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    color: #475569;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    white-space: nowrap;
}
.btn-reset:hover {
    border-color: #3b82f6;
    color: #2563eb;
    background: #eff6ff;
}
@media (max-width: 768px) {
    .filter-field--session,
    .filter-field--school,
    .filter-field--course {
        flex: 1 1 100%;
        max-width: none;
    }
    .filters-actions {
        width: 100%;
    }
    .filters-actions .btn-reset {
        width: 100%;
    }
}
.table-card {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;
}
.table-toolbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 18px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;
}
.table-toolbar-title { font-size: 14px; font-weight: 600; color: #1e293b; }
#mentorsTable thead tr { background: #f8fafc; }
#mentorsTable thead th {
    font-size: 11px !important; font-weight: 700 !important; color: #64748b !important;
    text-transform: uppercase !important; letter-spacing: 0.6px !important;
    padding: 11px 13px !important; border-bottom: 2px solid #e2e8f0 !important; white-space: nowrap;
}
#mentorsTable tbody td {
    padding: 11px 13px !important; font-size: 13px !important;
    vertical-align: middle !important; border-bottom: 1px solid #f1f5f9 !important; color: #334155;
}
#mentorsTable tbody tr:hover td { background: #f8fafc; }
.enrollment-chip {
    font-family: 'Courier New', monospace; font-size: 12px; color: #3b82f6;
    background: rgba(59,130,246,0.08); padding: 3px 9px; border-radius: 5px;
    border: 1px solid rgba(59,130,246,0.2); font-weight: 700; white-space: nowrap; display: inline-block;
}
.btn-act {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;
    cursor: pointer; border: 1px solid; transition: all 0.15s;
    white-space: nowrap; text-decoration: none; margin: 2px;
}
.btn-act:hover { text-decoration: none; }
.btn-profile  { color: #7c3aed; border-color: rgba(124,58,237,0.3); background: rgba(124,58,237,0.06); }
.btn-profile:hover { background: rgba(124,58,237,0.13); color: #7c3aed; }
.btn-detail   { color: #0891b2; border-color: rgba(8,145,178,0.3); background: rgba(8,145,178,0.06); }
.btn-detail:hover { background: rgba(8,145,178,0.13); color: #0891b2; }
.actions-cell { display: flex; flex-wrap: wrap; gap: 3px; align-items: center; }

/* ----- Gradebook-style modal (same pattern as scholarship_students) ----- */
.gradebook-modal-dialog { max-width: 96vw; }
.gradebook-modal-body {
    padding: 20px;
    min-height: 52vh;
    background: #f8fafc;
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
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.08);
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
.gradebook-section { display: none; }
.gradebook-section.is-visible { display: block; }
.mentor-detail-section { display: none; }
.mentor-detail-section.is-visible { display: block; }
.section-head {
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}
#mentorGradebookModal .modal-content {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
}
#mentorGradebookModal .modal-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    border-radius: 12px 12px 0 0;
    padding: 16px 20px;
}
#mentorGradebookModal .modal-title {
    font-size: 15px;
    font-weight: 600;
    color: #1e293b;
}
#mentorGradebookModal .modal-footer {
    border-top: 1px solid #e2e8f0;
    padding: 14px 20px;
    background: #f8fafc;
    border-radius: 0 0 12px 12px;
}
#mentorGradebookModal #weekdaysTimingContainer .row {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 10px !important;
}

/* Mentor type modal — same light chrome as gradebook modal (no gradient) */
#mentorTypeModal .modal-dialog {
    max-width: 440px;
}
#mentorTypeModal .modal-content {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
}
#mentorTypeModal .modal-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    border-radius: 12px 12px 0 0;
    padding: 14px 18px;
    align-items: center;
}
#mentorTypeModal .modal-header .modal-title {
    font-size: 15px;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}
#mentorTypeModal .modal-header .close {
    color: #64748b;
    opacity: 1;
    text-shadow: none;
    padding: 0;
    margin: 0;
}
#mentorTypeModal .modal-body {
    padding: 18px 20px;
    background: #fff;
}
#mentorTypeModal .modal-footer {
    border-top: 1px solid #e2e8f0;
    padding: 12px 18px;
    background: #f8fafc;
    border-radius: 0 0 12px 12px;
    justify-content: flex-end;
    gap: 10px;
}
#mentorGradebookModal .mm-gradebook-course-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #2563eb;
}
#mentorGradebookModal .mm-gradebook-course-checkbox:disabled {
    cursor: not-allowed;
    opacity: 0.85;
}
#mentorGradebookModal tr.mm-gradebook-row-assigned td {
    background: #f0fdf4;
}
#mentorGradebookModal .mm-assigned-course-hint {
    display: block;
    font-size: 10px;
    color: #059669;
    font-weight: 600;
    margin-top: 2px;
}
.mm-detail-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 10px;
    font-size: 12px;
    color: #64748b;
}
.mm-detail-toolbar-actions {
    display: flex;
    gap: 8px;
}
.mm-detail-toolbar-actions button {
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #475569;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
}
.mm-detail-toolbar-actions button:hover {
    border-color: #3b82f6;
    color: #2563eb;
    background: #eff6ff;
}
.mm-courses-slots-scroll {
    max-height: min(52vh, 480px);
    overflow-y: auto;
    overflow-x: hidden;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #fff;
}
.mm-unlinked-box {
    border-bottom: 1px solid #fde68a;
    background: #fffbeb;
    padding: 10px 12px;
}
.mm-unlinked-box .mm-unlinked-hint {
    font-size: 11px;
    color: #92400e;
    margin: 0 0 8px;
}
.mm-course-acc {
    border-bottom: 1px solid #f1f5f9;
}
.mm-course-acc:last-child {
    border-bottom: none;
}
.mm-course-acc-head {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    border: none;
    background: #f8fafc;
    text-align: left;
    cursor: pointer;
    transition: background 0.15s;
}
.mm-course-acc-head:hover,
.mm-course-acc.is-open .mm-course-acc-head {
    background: #eff6ff;
}
.mm-course-acc-title {
    flex: 1;
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mm-course-acc-meta {
    font-size: 11px;
    color: #64748b;
    white-space: nowrap;
}
.mm-slot-count-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 999px;
    background: #e0e7ff;
    color: #3730a3;
    white-space: nowrap;
}
.mm-slot-count-badge.is-empty {
    background: #f1f5f9;
    color: #94a3b8;
}
.mm-course-acc-chevron {
    color: #64748b;
    font-size: 12px;
    width: 14px;
    text-align: center;
}
.mm-course-acc-chevron i {
    display: inline-block;
    transition: transform 0.15s ease;
}
.mm-course-acc-body {
    display: none;
    padding: 0 10px 10px;
    background: #fff;
}
.mm-course-acc.is-open .mm-course-acc-body {
    display: block;
}
.mm-course-acc.is-open .mm-course-acc-chevron i {
    transform: rotate(90deg);
}
.mm-slots-table .mm-col-course {
    width: 36%;
}
.mm-slots-table-unlinked {
    margin-bottom: 0;
}
.mm-slots-table {
    width: 100%;
    margin-bottom: 6px;
    font-size: 12px;
}
.mm-slots-table thead th {
    padding: 6px 6px;
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.mm-slots-table tbody td {
    padding: 4px 4px;
    vertical-align: middle;
    border-bottom: 1px solid #f8fafc;
}
.mm-slots-table select {
    width: 100%;
    padding: 5px 8px;
    border: 1px solid #e2e8f0;
    border-radius: 5px;
    font-size: 12px;
    background: #fff;
    min-width: 0;
}
.mm-slots-table .mm-col-actions {
    width: 36px;
    text-align: center;
}
.mm-slots-table .btn-remove-slot {
    width: 28px;
    height: 28px;
    padding: 0;
    border: 1px solid #fecaca;
    background: #fef2f2;
    color: #b91c1c;
    border-radius: 5px;
    cursor: pointer;
    line-height: 26px;
}
.mm-slots-table tr.mm-empty-slots td {
    padding: 10px;
    text-align: center;
    color: #94a3b8;
    font-size: 12px;
    border-bottom: none;
}
.mm-add-slot-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 600;
    color: #2563eb;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 6px;
    cursor: pointer;
}
.mm-add-slot-btn:hover {
    background: #dbeafe;
}
.mm-detail-status {
    display: none;
    margin-top: 12px;
    border-radius: 8px;
    padding: 9px 12px;
    font-size: 12px;
    font-weight: 600;
}
.mm-detail-status.is-error { display: block; color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; }
.mm-detail-status.is-success { display: block; color: #047857; background: #f0fdf4; border: 1px solid #bbf7d0; }

.mentor-type-pill {
    border: 1px solid #cbd5e1; background: #f8fafc; color: #334155;
    border-radius: 999px; padding: 5px 12px; font-size: 12px; font-weight: 600;
    cursor: pointer; transition: all 0.15s;
}
.mentor-type-pill:hover {
    border-color: #3b82f6; color: #1d4ed8; background: #eff6ff;
}
#mentorGradebookModal .gradebook-placeholder {
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    background: #fff;
    padding: 40px 28px;
    text-align: center;
    color: #64748b;
    min-height: 260px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
}
#mentorGradebookModal .gradebook-placeholder i {
    color: #2563eb;
    font-size: 2.5rem;
    line-height: 1;
}
#mentorGradebookModal .gradebook-placeholder .gradebook-placeholder-title {
    color: #1e293b;
    font-size: 17px;
    font-weight: 700;
    margin: 0;
}
#mentorGradebookModal .gradebook-placeholder > div:last-of-type {
    font-size: 13px;
    max-width: 360px;
    line-height: 1.45;
}
.gradebook-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: 12px;
    flex-wrap: wrap;
}
.gradebook-table-wrap { border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; background: #fff; }
.mentor-panel {
    display: none; margin-top: 18px; padding: 16px; border: 1px solid #dbeafe;
    border-radius: 12px; background: #eff6ff;
}
.mentor-panel.is-visible { display: block; }
.selected-course-list { margin: 0 0 12px; padding-left: 18px; color: #334155; font-size: 13px; }
.timing-row {
    display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 10px;
    align-items: end; margin-bottom: 10px;
}
.mentor-panel-grid { display: grid; grid-template-columns: 160px 1fr; gap: 12px; }
.gradebook-status {
    display: none; margin-top: 12px; border-radius: 8px; padding: 9px 12px;
    font-size: 12px; font-weight: 600;
}
.gradebook-status.is-error { display: block; color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; }
.gradebook-status.is-success { display: block; color: #047857; background: #f0fdf4; border: 1px solid #bbf7d0; }
.form-label-styled {
    display: block; font-size: 11px; font-weight: 600; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
}
.form-control-styled {
    width: 100%; padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 8px;
    background: #f8fafc; font-size: 14px; color: #1e293b; transition: all 0.2s;
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
.mini-table tbody tr:last-child td { border-bottom: none; }
.info-alert-blue {
    background: rgba(59,130,246,0.07); border: 1px solid rgba(59,130,246,0.2);
    border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #2563eb; margin-bottom: 16px;
}
.btn-primary-mod {
    display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px;
    background: #3b82f6; color: #fff; border: none; border-radius: 8px;
    font-size: 13px; font-weight: 600; cursor: pointer;
}
.btn-success-mod {
    display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px;
    background: #059669; color: #fff; border: none; border-radius: 8px;
    font-size: 13px; font-weight: 600; cursor: pointer;
}
.btn-cancel-mod {
    display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px;
    background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px;
    font-size: 13px; font-weight: 500; cursor: pointer;
}
@media (max-width: 768px) {
    .timing-row, .mentor-panel-grid { grid-template-columns: 1fr; }
}
</style>

<input type="hidden" id="sess_id">
<input type="hidden" id="school_id">

<div class="container-fluid p-0">

    <div class="schol-header">
        <div>
            <h3><i class="fa fa-users mr-2"></i>Academic Mentors</h3>
            <div class="schol-header-sub">Browse mentors by session, school, and course — details &amp; academic mentor in one place</div>
        </div>
        <span class="schol-badge-count" id="mentor-count-badge">
            <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
        </span>
    </div>

    <div class="filters-card">
        <div class="filters-row">
            <div class="filter-field filter-field--session">
                <label class="field-label" for="facultyWload_sess_id"><i class="fa fa-calendar mr-1"></i> Session</label>
                <div class="filter-control">
                    <input id="facultyWload_sess_id"
                           name="facultyWload_sess_id"
                           class="easyui-combobox"
                           data-options="
                               valueField: 'sess_id',
                               textField:  'sess_name',
                               url:        '{{ url('/sessions') }}',
                               method:     'GET',
                               prompt:     'Select session…',
                               panelHeight:'auto',
                               onLoadSuccess: function() { setDefaultSession(); },
                               onSelect: function(rec) {
                                   $('#sess_id').val(rec.sess_id);
                                   $('#school_id').val('');
                                   $('#facultyWload_school_id').combobox('clear');
                                   if (suppressSessionRequest) return;
                                   fetchMentorsData();
                               }">
                </div>
            </div>
            <div class="filter-field filter-field--school">
                <label class="field-label" for="facultyWload_school_id"><i class="fa fa-building-o mr-1"></i> School</label>
                <div class="filter-control">
                    <input id="facultyWload_school_id"
                           name="facultyWload_school_id"
                           class="easyui-combobox"
                           data-options="
                               valueField: 'school_id',
                               textField:  'school_name',
                               method:     'GET',
                               url:        '{{ url('/schools') }}',
                               prompt:     'All schools',
                               panelHeight:'auto',
                               onSelect: function(rec) {
                                   $('#school_id').val(rec.school_id);
                                   fetchMentorsData();
                               }">
                </div>
            </div>
            <div class="filter-field filter-field--course">
                <label class="field-label" for="courseSelect"><i class="fa fa-book mr-1"></i> Course</label>
                <div class="filter-control">
                    <select id="courseSelect" class="form-control"></select>
                </div>
            </div>
            <div class="filters-actions">
                <button type="button" class="btn-reset" onclick="resetMentorFilters()">
                    <i class="fa fa-refresh" id="reset-icon-mentors"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-toolbar">
            <span class="table-toolbar-title"><i class="fa fa-table mr-1"></i> Mentor list</span>
        </div>
        <div id="mentorListTable">
            <div class="text-center p-4 text-muted">
                <i class="fa fa-spinner fa-spin fa-2x"></i>
                <div class="mt-2" style="font-size:13px;">Select session to load mentors…</div>
            </div>
        </div>
    </div>
</div>

{{-- Same layout as scholarship gradebook modal: Academic Mentor | Mentor Detail --}}
<div class="modal fade" id="mentorGradebookModal" tabindex="-1" role="dialog" aria-labelledby="mentorGradebookModalLabel" aria-hidden="true">
    <div class="modal-dialog gradebook-modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mentorGradebookModalLabel">
                    <i class="fa fa-info-circle text-info mr-2"></i> Detail
                    <span id="mm-gb-modal-enrollment" class="text-muted ml-2" style="font-size:12px;"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeDetails()">&times;</button>
            </div>
            <div class="modal-body gradebook-modal-body">
                <div class="info-alert-blue">
                    <i class="fa fa-info-circle mr-1"></i>
                    Enrollment: <strong id="mm-gb-modal-enrollment-display">—</strong>
                </div>

                <div id="mmMentorTypeSelection" class="mentor-type-grid">
                    <button type="button" class="mentor-type-card" data-mentor-type="academic">
                        <i class="fa fa-graduation-cap"></i>
                        <div class="mentor-type-title">Academic Mentor</div>
                        <div class="mentor-type-sub">Select gradebook courses, weekdays, timings, monthly hours, and remarks.</div>
                    </button>
                    <button type="button" class="mentor-type-card" data-mentor-type="detail">
                        <i class="fa fa-list-alt"></i>
                        <div class="mentor-type-title">Mentor Detail</div>
                        <div class="mentor-type-sub">View weekly availability, mentor courses, and add weekday timing slots.</div>
                    </button>
                </div>

                <form id="mmGradebookMentoringForm" class="gradebook-section">
                    @csrf
                    <input type="hidden" name="enrollment_no" id="mmGradebookEnrollmentInput">

                    <div id="mm-gradebook-loading" class="gradebook-placeholder d-none">
                        <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                        <div class="gradebook-placeholder-title">Loading gradebook…</div>
                        <div>Please wait while the student courses are loaded.</div>
                    </div>

                    <div id="mm-gradebook-content" class="d-none">
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
                                <tbody id="mm-gradebook-records-table"></tbody>
                            </table>
                        </div>

                        <div class="gradebook-actions">
                            <span class="text-muted" style="font-size:12px;">Select one or more courses to configure academic mentor details.</span>
                        </div>

                        <div id="mm-mentorAssignmentPanel" class="mentor-panel">
                            <div class="section-head"><i class="fa fa-calendar"></i> Mentor Timetable</div>
                            <div style="font-size:12px; color:#475569; font-weight:600; margin-bottom:6px;">Selected Courses</div>
                            <ul id="mm-selectedCoursesList" class="selected-course-list"></ul>

                            <div id="mm-mentorTimingRows"></div>
                            <button type="button" class="btn-act btn-detail" id="mm-addMentorTimingRow">
                                <i class="fa fa-plus"></i> Add Weekday and Time
                            </button>

                            <div class="mentor-panel-grid mt-3">
                                <div>
                                    <label class="form-label-styled">Monthly Hours</label>
                                    <input type="number" name="hourse" id="mm-gradebookMonthlyHours" class="form-control-styled" value="8" placeholder="Hours per month">
                                </div>
                                <div>
                                    <label class="form-label-styled">Remarks</label>
                                    <input type="text" name="remarks" id="mm-gradebookRemarks" class="form-control-styled" placeholder="Remarks">
                                </div>
                            </div>
                        </div>

                        <div id="mm-gradebook-status" class="gradebook-status"></div>
                    </div>
                </form>

                <div id="mmMentorDetailSection" class="mentor-detail-section">
                    <form id="mentoringForm" method="POST">
                        @csrf
                        <input type="hidden" name="enrollment_no" id="mentorDetailEnrollment">
                        <input type="hidden" name="sess_id" id="mentorDetailSessId">
                        <div id="mm-deleted-timing-ids"></div>

                        <div class="section-head"><i class="fa fa-book"></i> Mentor courses &amp; weekly slots</div>
                        <div class="mm-detail-toolbar">
                            <span id="mm-detail-summary">—</span>
                            <div class="mm-detail-toolbar-actions">
                                <button type="button" id="mm-expand-all-courses">Expand all</button>
                                <button type="button" id="mm-collapse-all-courses">Collapse all</button>
                            </div>
                        </div>

                        <div class="mm-courses-slots-scroll">
                            <div id="mentor-courses-slots-root"></div>
                        </div>
                        <div id="mm-mentor-detail-status" class="mm-detail-status"></div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel-mod" data-dismiss="modal" onclick="closeDetails()">
                    <i class="fa fa-times"></i> Close
                </button>
                <button type="button" class="btn-success-mod" id="mmSaveGradebookMentorBtn" style="display:none;">
                    <i class="fa fa-save"></i> Save Changes
                </button>
                <button type="button" class="btn-success-mod" id="saveChangesButton" style="display:none;">
                    <i class="fa fa-save"></i> Save slots
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Update mentor type — simple modal (matches gradebook modal chrome) --}}
<div class="modal fade" id="mentorTypeModal" tabindex="-1" role="dialog" aria-labelledby="mentorTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mentorTypeModalLabel">Update mentor type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="mt-modal-enrollment">
                <div class="info-alert-blue mb-3">
                    <i class="fa fa-info-circle mr-1"></i> Enrollment: <strong id="mt-modal-enrollment-display">—</strong>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label-styled" for="mt-modal-role">Mentor type</label>
                    <select id="mt-modal-role" class="form-control-styled">
                        <option value="academic">Academic</option>
                        <option value="admin">Admin</option>
                        <option value="both">Both</option>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label-styled" for="mt-modal-remarks">Remarks <span class="text-muted font-weight-normal">(optional)</span></label>
                    <textarea id="mt-modal-remarks" class="form-control-styled" rows="3" placeholder="Optional notes…"></textarea>
                </div>
                <div id="mt-modal-status" class="gradebook-status"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel-mod" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn-success-mod" id="mt-modal-save"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script>
var defaultSessionId = @json($currentSessionId ?? null);
var defaultSessionApplied = false;
var suppressSessionRequest = false;
var mentorsDt = null;
var currentMentorEnrollmentForSlot = '';
var mmGbTimeslots = [];
var mmGbLoadedForEnrollment = '';
var mmGbMentorCoIds = [];
var mmGbCachedRecords = [];
var mmGbCachedMentorCoIds = [];
var mmMentorDetailCourses = [];
var mmMentorUnassignedSlots = [];
var mmTimeOptionsInner = '@foreach ($timeslots as $row)<option value="{{ $row->ts_id }}">{{ $row->ts_name }}</option>@endforeach';

function syncMmFilterControlHeights() {
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
    syncMmFilterControlHeights();
    fetchMentorsData();
}

function resetMentorFilters() {
    var icon = document.getElementById('reset-icon-mentors');
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
    $('#courseSelect').val(null).trigger('change');
    fetchMentorsData();
}

function escapeAttr(value) {
    return String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function escapeHtml(value) {
    return escapeAttr(value);
}

function mentorTypeLabel(t) {
    var x = String(t || '').toLowerCase();
    if (x === 'admin') return 'Admin';
    if (x === 'both') return 'Both';
    if (x === 'academic') return 'Academic';
    return t || '—';
}

function getEffectiveSessionId() {
    return $('#sess_id').val() || $('#facultyWload_sess_id').combobox('getValue') || defaultSessionId || '';
}

function fetchMentorsData() {
    var courseId = $('#courseSelect').val() || '';
    var sessId = getEffectiveSessionId();
    var schoolId = $('#school_id').val() || '';

    $('#mentorListTable').html(
        '<div class="text-center p-4 text-muted">' +
        '<i class="fa fa-spinner fa-spin fa-2x"></i>' +
        '<div class="mt-2" style="font-size:13px;">Loading mentors…</div></div>'
    );

    $.ajax({
        url: "{{ route('get-mentors-list') }}",
        method: 'GET',
        data: { course_id: courseId, sess_id: sessId, school_id: schoolId },
        success: function (data) {
            var mentors = data.mentors || [];
            document.getElementById('mentor-count-badge').innerHTML =
                '<i class="fa fa-users mr-1"></i> ' + mentors.length + ' mentor' + (mentors.length !== 1 ? 's' : '');

            var tbody = '';
            if (mentors.length === 0) {
                tbody = '<tr><td colspan="9" class="text-center py-5 text-muted">' +
                    '<i class="fa fa-inbox fa-2x mb-2 d-block"></i>No mentors for this filter.</td></tr>';
            } else {
                mentors.forEach(function (row, i) {
                    var enr = row.m_enrollment || '';
                    tbody +=
                        '<tr>' +
                        '<td class="text-center text-muted" style="font-size:12px;">' + (i + 1) + '</td>' +
                        '<td><span class="enrollment-chip">' + escapeHtml(enr) + '</span></td>' +
                        '<td>' + escapeHtml(row.full_name) + '</td>' +
                        '<td style="max-width:220px; font-size:12px;">' + escapeHtml(row.m_committee_remarks) + '</td>' +
                        '<td>' +
                            '<button type="button" class="mentor-type-pill js-open-mentor-type" ' +
                                'data-enrollment="' + escapeAttr(enr) + '" data-type="' + escapeAttr(row.m_mentor_type) + '">' +
                                mentorTypeLabel(row.m_mentor_type) +
                            '</button>' +
                        '</td>' +
                        '<td>' + escapeHtml(row.programs) + '</td>' +
                        '<td class="text-muted" style="font-size:12px;">' + escapeHtml(row.department) + '</td>' +
                        '<td>' +
                            '<a href="{{ url('reports-generator-student-profile') }}/' + escapeAttr(enr) + '" target="_blank" class="btn-act btn-profile">' +
                            '<i class="fa fa-user"></i> Profile</a>' +
                        '</td>' +
                        '<td>' +
                            '<button type="button" class="btn-act btn-detail js-show-mentor-details" data-enrollment="' + escapeAttr(enr) + '">' +
                            '<i class="fa fa-info-circle"></i> Details</button>' +
                        '</td>' +
                        '</tr>';
                });
            }

            if (mentorsDt) {
                try { mentorsDt.destroy(); } catch (e) {}
                mentorsDt = null;
            }

            $('#mentorListTable').html(
                '<div style="overflow-x:auto;">' +
                '<table class="table table-hover mb-0" id="mentorsTable" style="width:100%">' +
                '<thead><tr>' +
                '<th class="text-center" style="width:40px;">#</th>' +
                '<th>Enrollment</th><th>Full name</th><th>Remarks</th><th>Mentor type</th>' +
                '<th>Program</th><th>Department</th><th>Profile</th><th>Actions</th>' +
                '</tr></thead><tbody>' + tbody + '</tbody></table></div>'
            );

            mentorsDt = $('#mentorsTable').DataTable({
                scrollX: false,
                responsive: true,
                pageLength: 50,
                lengthMenu: [25, 50, 100, 200],
                order: [[0, 'asc']],
                dom: 'frtip',
                language: {
                    search: '',
                    searchPlaceholder: 'Search…',
                    paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Previous' }
                }
            });
        },
        error: function () {
            document.getElementById('mentor-count-badge').innerHTML =
                '<i class="fa fa-exclamation-triangle mr-1"></i> Error';
            $('#mentorListTable').html(
                '<div class="text-center py-5 text-danger">Failed to load mentors.</div>'
            );
        }
    });
}

$(document).ready(function () {
    $('#courseSelect').select2({
        placeholder: 'Type to search course…',
        allowClear: true,
        width: '100%',
        minimumInputLength: 3,
        ajax: {
            url: "{{ route('search-courses') }}",
            dataType: 'json',
            method: 'GET',
            data: function (params) {
                return { searchTerm: params.term };
            },
            processResults: function (data) {
                return {
                    results: (data || []).map(function (course) {
                        return {
                            id: course.course_id,
                            text: course.course_code + ' — ' + course.course_cd_name
                        };
                    })
                };
            },
            cache: true
        }
    }).on('select2:select select2:clear', function () {
        fetchMentorsData();
    });

    setTimeout(syncMmFilterControlHeights, 80);
    $(window).on('resize.mmFilters', syncMmFilterControlHeights);
});

function setMmDetailStatus(message, type) {
    var el = document.getElementById('mm-mentor-detail-status');
    if (!el) return;
    el.className = 'mm-detail-status' + (type ? ' is-' + type : '');
    el.innerHTML = message || '';
}

function buildMmWeekdaySelect(selected) {
    var days = [
        { v: '1', n: 'Monday' }, { v: '2', n: 'Tuesday' }, { v: '3', n: 'Wednesday' },
        { v: '4', n: 'Thursday' }, { v: '5', n: 'Friday' }, { v: '6', n: 'Saturday' }, { v: '7', n: 'Sunday' }
    ];
    var html = '<option value="">Weekday</option>';
    days.forEach(function (d) {
        html += '<option value="' + d.v + '"' + (String(selected) === d.v ? ' selected' : '') + '>' + d.n + '</option>';
    });
    return html;
}

function buildMmTimeSelect(emptyLabel, selectedId) {
    var html = '<option value="">' + emptyLabel + '</option>' + mmTimeOptionsInner;
    return html;
}

function updateMmDetailSummary(courses, unassignedSlots) {
    var el = document.getElementById('mm-detail-summary');
    if (!el) return;
    courses = courses || [];
    unassignedSlots = unassignedSlots || [];
    var totalSlots = unassignedSlots.length;
    courses.forEach(function (c) {
        totalSlots += (c.slots || []).length;
    });
    var parts = [courses.length + ' course' + (courses.length === 1 ? '' : 's')];
    parts.push(totalSlots + ' slot' + (totalSlots === 1 ? '' : 's'));
    if (unassignedSlots.length) {
        parts.push(unassignedSlots.length + ' unlinked');
    }
    el.textContent = parts.join(' · ');
}

function appendMmEmptySlotsRow(tbody, colSpan, message) {
    var tr = document.createElement('tr');
    tr.className = 'mm-empty-slots';
    tr.innerHTML = '<td colspan="' + colSpan + '">' + escapeHtml(message || 'No slots yet.') + '</td>';
    tbody.appendChild(tr);
}

function appendMmSlotRow(tbody, mdId, slot, coursesForPick) {
    if (!tbody) return;
    var emptyRow = tbody.querySelector('tr.mm-empty-slots');
    if (emptyRow) emptyRow.remove();

    slot = slot || {};
    var timingId = slot.timing_id || slot.id || '';
    var weekday = slot.pmt_weekday != null ? String(slot.pmt_weekday) : '';
    var fromId = slot.from_time_id != null ? String(slot.from_time_id) : '';
    var toId = slot.to_time_id != null ? String(slot.to_time_id) : '';
    var isUnlinked = coursesForPick && coursesForPick.length;

    var courseCell = '';
    if (isUnlinked) {
        courseCell = '<td class="mm-col-course"><select name="md_id[]" class="mm-md-select" required>' +
            '<option value="">Course</option>' +
            coursesForPick.map(function (c) {
                var sel = String(mdId) === String(c.id) ? ' selected' : '';
                return '<option value="' + escapeAttr(c.id) + '"' + sel + '>' + escapeAttr(c.course_name) + '</option>';
            }).join('') +
            '</select></td>';
    }

    var hidden = '<input type="hidden" name="timing_id[]" value="' + escapeAttr(timingId) + '">';
    if (!isUnlinked) {
        hidden += '<input type="hidden" name="md_id[]" value="' + escapeAttr(mdId) + '">';
    }

    var tr = document.createElement('tr');
    tr.className = 'mm-slot-row';
    tr.setAttribute('data-timing-id', timingId);
    tr.innerHTML =
        courseCell +
        '<td>' + hidden + '<select name="weekdays[]" class="mm-slot-weekday" required>' + buildMmWeekdaySelect(weekday) + '</select></td>' +
        '<td><select name="fromtiming[]" class="mm-slot-from" required>' + buildMmTimeSelect('From', fromId) + '</select></td>' +
        '<td><select name="totiming[]" class="mm-slot-to" required>' + buildMmTimeSelect('To', toId) + '</select></td>' +
        '<td class="mm-col-actions"><button type="button" class="btn-remove-slot js-remove-mm-slot" title="Remove slot"><i class="fa fa-trash"></i></button></td>';

    tbody.appendChild(tr);

    if (fromId) tr.querySelector('.mm-slot-from').value = fromId;
    if (toId) tr.querySelector('.mm-slot-to').value = toId;
}

function renderMentorCoursesSlots(courses, unassignedSlots) {
    var root = document.getElementById('mentor-courses-slots-root');
    var deletedBox = document.getElementById('mm-deleted-timing-ids');
    if (!root) return;

    root.innerHTML = '';
    if (deletedBox) deletedBox.innerHTML = '';

    courses = courses || [];
    unassignedSlots = unassignedSlots || [];
    updateMmDetailSummary(courses, unassignedSlots);

    if (!courses.length && !unassignedSlots.length) {
        root.innerHTML = '<p class="text-center text-muted py-3 mb-0">No mentor courses for this session.</p>';
        return;
    }

    if (unassignedSlots.length) {
        var unBox = document.createElement('div');
        unBox.className = 'mm-unlinked-box';
        unBox.innerHTML = '<p class="mm-unlinked-hint">These slots are not linked to a course. Select a course and save to link them.</p>';
        var unTable = document.createElement('table');
        unTable.className = 'mm-slots-table mm-slots-table-unlinked';
        unTable.innerHTML =
            '<thead><tr>' +
            '<th class="mm-col-course">Course</th><th>Day</th><th>From</th><th>To</th><th class="mm-col-actions"></th>' +
            '</tr></thead>';
        var unTbody = document.createElement('tbody');
        unTable.appendChild(unTbody);
        unassignedSlots.forEach(function (slot) {
            appendMmSlotRow(unTbody, '', slot, courses);
        });
        unBox.appendChild(unTable);
        root.appendChild(unBox);
    }

    courses.forEach(function (course, index) {
        var slots = course.slots || [];
        var isFirst = index === 0;
        var acc = document.createElement('div');
        acc.className = 'mm-course-acc' + (isFirst ? ' is-open' : '');
        acc.setAttribute('data-md-id', course.id);

        var badgeClass = slots.length ? 'mm-slot-count-badge' : 'mm-slot-count-badge is-empty';
        var badgeText = slots.length ? slots.length + ' slot' + (slots.length === 1 ? '' : 's') : 'No slots';

        acc.innerHTML =
            '<button type="button" class="mm-course-acc-head" aria-expanded="' + (isFirst ? 'true' : 'false') + '">' +
                '<span class="mm-course-acc-chevron"><i class="fa fa-chevron-right"></i></span>' +
                '<span class="mm-course-acc-title">' + escapeHtml(course.course_name) + '</span>' +
                '<span class="mm-course-acc-meta">' + escapeHtml(course.md_time_alot != null ? course.md_time_alot : '0') + 'h</span>' +
                '<span class="' + badgeClass + '">' + badgeText + '</span>' +
            '</button>' +
            '<div class="mm-course-acc-body">' +
                '<table class="mm-slots-table">' +
                    '<thead><tr><th>Day</th><th>From</th><th>To</th><th class="mm-col-actions"></th></tr></thead>' +
                    '<tbody></tbody>' +
                '</table>' +
                '<button type="button" class="mm-add-slot-btn js-add-slot-row" data-md-id="' + escapeAttr(course.id) + '">' +
                    '<i class="fa fa-plus"></i> Add slot' +
                '</button>' +
            '</div>';

        root.appendChild(acc);
        var tbody = acc.querySelector('tbody');
        if (!slots.length) {
            appendMmEmptySlotsRow(tbody, 4);
        } else {
            slots.forEach(function (slot) {
                appendMmSlotRow(tbody, course.id, slot, null);
            });
        }
    });
}

function loadMentorDetailView() {
    $('#mentorDetailEnrollment').val(currentMentorEnrollmentForSlot);
    $('#mentorDetailSessId').val(getEffectiveSessionId());
    renderMentorCoursesSlots(mmMentorDetailCourses, mmMentorUnassignedSlots);
    setMmDetailStatus('', '');
}

document.getElementById('saveChangesButton').addEventListener('click', function () {
    var form = document.getElementById('mentoringForm');
    var formData = new FormData(form);
    formData.set('enrollment_no', currentMentorEnrollmentForSlot);
    formData.set('sess_id', getEffectiveSessionId());

    var hasSlot = false;
    form.querySelectorAll('select[name="weekdays[]"]').forEach(function (el) {
        if (el.value) hasSlot = true;
    });
    if (!hasSlot) {
        setMmDetailStatus('Add at least one slot with day and time.', 'error');
        return;
    }

    fetch('{{ route('mentor_slot') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.success === true || data.success === 'Added' || data.success === 'Saved') {
            if (typeof showToast === 'function') {
                showToast('success', 'Slots saved.', 'Success');
            }
            $('#mentorGradebookModal').modal('hide');
            fetchMentorsData();
        } else {
            var msg = (data && data.error) ? data.error : 'Could not save slots.';
            setMmDetailStatus(escapeHtml(msg), 'error');
        }
    })
    .catch(function () {
        setMmDetailStatus('Unexpected error while saving.', 'error');
    });
});

$(document).on('click', '.js-add-slot-row', function () {
    var mdId = $(this).data('md-id');
    var acc = $(this).closest('.mm-course-acc');
    var tbody = acc.find('tbody')[0];
    appendMmSlotRow(tbody, mdId, null, null);
    acc.addClass('is-open');
    acc.find('.mm-course-acc-head').attr('aria-expanded', 'true');
    var badge = acc.find('.mm-slot-count-badge');
    var count = acc.find('tr.mm-slot-row').length;
    badge.removeClass('is-empty').text(count + ' slot' + (count === 1 ? '' : 's'));
});

$(document).on('click', '.js-remove-mm-slot', function () {
    var row = $(this).closest('tr.mm-slot-row');
    var timingId = row.attr('data-timing-id') || row.find('input[name="timing_id[]"]').val();
    if (timingId) {
        $('#mm-deleted-timing-ids').append(
            '<input type="hidden" name="deleted_timing_ids[]" value="' + escapeAttr(timingId) + '">'
        );
    }
    var tbody = row.closest('tbody');
    var acc = row.closest('.mm-course-acc');
    row.remove();
    if (tbody.length && tbody.find('tr.mm-slot-row').length === 0) {
        var colSpan = tbody.closest('table').find('thead th').length || 4;
        appendMmEmptySlotsRow(tbody[0], colSpan);
        if (acc.length) {
            acc.find('.mm-slot-count-badge').addClass('is-empty').text('No slots');
        }
    } else if (acc.length) {
        var n = acc.find('tr.mm-slot-row').length;
        acc.find('.mm-slot-count-badge').removeClass('is-empty').text(n + ' slot' + (n === 1 ? '' : 's'));
    }
});

$(document).on('click', '.mm-course-acc-head', function () {
    var acc = $(this).closest('.mm-course-acc');
    var open = !acc.hasClass('is-open');
    acc.toggleClass('is-open');
    $(this).attr('aria-expanded', open ? 'true' : 'false');
});

$(document).on('click', '#mm-expand-all-courses', function () {
    $('#mentor-courses-slots-root .mm-course-acc').addClass('is-open')
        .find('.mm-course-acc-head').attr('aria-expanded', 'true');
});

$(document).on('click', '#mm-collapse-all-courses', function () {
    $('#mentor-courses-slots-root .mm-course-acc').removeClass('is-open')
        .find('.mm-course-acc-head').attr('aria-expanded', 'false');
});

$(document).on('click', '.js-show-mentor-details', function () {
    showDetails($(this).data('enrollment'));
});

function activateMmMentorDetailTab() {
    $('#mentorGradebookModal .mentor-type-card').removeClass('is-active');
    $('#mentorGradebookModal .mentor-type-card[data-mentor-type="detail"]').addClass('is-active');
    $('#mmGradebookMentoringForm').removeClass('is-visible');
    $('#mm-mentorAssignmentPanel').removeClass('is-visible');
    $('#mmSaveGradebookMentorBtn').hide();
    $('#mmMentorDetailSection').addClass('is-visible');
    $('#saveChangesButton').show();
    setMmGbStatus('', '');
}

function showDetails(enrollment) {
    currentMentorEnrollmentForSlot = enrollment || '';
    resetMentorGradebookModal(currentMentorEnrollmentForSlot);
    activateMmMentorDetailTab();
    $('#mentorGradebookModal').modal('show');

    var sessId = getEffectiveSessionId();
    var url = '/get-mentor-details?mentor_enrollment=' + encodeURIComponent(enrollment);
    if (sessId) url += '&sess_id=' + encodeURIComponent(sessId);

    fetch(url)
        .then(function (r) { return r.json(); })
        .then(function (data) {
            mmMentorDetailCourses = data.courses || [];
            mmMentorUnassignedSlots = data.unassigned_slots || [];
            loadMentorDetailView();
        })
        .catch(function () {
            if (typeof showToast === 'function') {
                showToast('error', 'Failed to load mentor details.', 'Error');
            } else {
                alert('Failed to load mentor details.');
            }
        });
}

function closeDetails() {
    $('#mentorGradebookModal').modal('hide');
}

$(document).on('click', '.js-open-mentor-type', function () {
    var enr = $(this).data('enrollment');
    var typ = ($(this).data('type') || 'academic').toString().toLowerCase();
    $('#mt-modal-enrollment').val(enr);
    $('#mt-modal-enrollment-display').text(enr || '—');
    $('#mt-modal-role').val(['academic', 'admin', 'both'].indexOf(typ) >= 0 ? typ : 'academic');
    $('#mt-modal-remarks').val('');
    $('#mt-modal-status').removeClass('is-error is-success').html('');
    $('#mentorTypeModal').modal('show');
});

function setMtModalStatus(msg, type) {
    var el = document.getElementById('mt-modal-status');
    el.className = 'gradebook-status' + (type ? ' is-' + type : '');
    el.innerHTML = msg || '';
}

$('#mt-modal-save').on('click', function () {
    var enrollment = $('#mt-modal-enrollment').val();
    var role = $('#mt-modal-role').val();
    var remarks = $('#mt-modal-remarks').val();
    var sessId = getEffectiveSessionId();

    if (!sessId) {
        setMtModalStatus('Please select a session.', 'error');
        return;
    }

    $.ajax({
        url: "{{ route('update-mentor-type') }}",
        method: 'POST',
        dataType: 'json',
        data: {
            _token: '{{ csrf_token() }}',
            enrollment: enrollment,
            role: role,
            remarks: remarks,
            sess_id: sessId
        },
        success: function () {
            if (typeof showToast === 'function') {
                showToast('success', 'Mentor type updated.', 'Success');
            }
            $('#mentorTypeModal').modal('hide');
            fetchMentorsData();
        },
        error: function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Update failed.';
            setMtModalStatus(escapeHtml(msg), 'error');
        }
    });
});

/* ---------- Gradebook modal (scholarship-style): Academic mentor + Mentor detail ---------- */

function setMmGbStatus(message, type) {
    var el = document.getElementById('mm-gradebook-status');
    if (!el) return;
    el.className = 'gradebook-status' + (type ? ' is-' + type : '');
    el.innerHTML = message || '';
}

function resetMentorGradebookModal(enrollmentNo) {
    mmGbTimeslots = [];
    mmGbLoadedForEnrollment = '';
    mmGbMentorCoIds = [];
    var display = enrollmentNo || '';

    $('#mm-gb-modal-enrollment').text(display ? '(' + display + ')' : '');
    $('#mm-gb-modal-enrollment-display').text(display || '—');
    $('#mmGradebookEnrollmentInput').val(display);

    $('#mm-gradebook-records-table').html('');
    $('#mm-selectedCoursesList').html('');
    $('#mm-mentorTimingRows').html('');
    $('#mm-mentorAssignmentPanel').removeClass('is-visible');
    $('#mmGradebookMentoringForm').removeClass('is-visible');
    $('#mmMentorDetailSection').removeClass('is-visible');
    $('#mentorGradebookModal .mentor-type-card').removeClass('is-active');
    $('#mmSaveGradebookMentorBtn').hide();
    $('#saveChangesButton').hide();
    $('#mm-gradebookMonthlyHours').val(8);
    $('#mm-gradebookRemarks').val('');
    setMmGbStatus('', '');
    $('#mm-gradebook-content').addClass('d-none');
    $('#mm-gradebook-loading').addClass('d-none');

    mmMentorDetailCourses = [];
    mmMentorUnassignedSlots = [];
    $('#mentor-courses-slots-root').html('');
    $('#mm-deleted-timing-ids').html('');
    setMmDetailStatus('', '');
}

function renderMmGbRows(records, mentorCoIds) {
    mmGbMentorCoIds = (mentorCoIds || []).map(function (id) {
        return String(id);
    });

    if (!records || records.length === 0) {
        $('#mm-gradebook-records-table').html(
            '<tr><td colspan="5" class="text-center text-muted py-3">No gradebook records.</td></tr>'
        );
        return;
    }

    var assignedSet = {};
    mmGbMentorCoIds.forEach(function (id) {
        assignedSet[id] = true;
    });

    var rows = records.map(function (record, i) {
        var courseId = String(record.gh_co_id || record.GH_CO_ID || '');
        var course = record.course || record.COURSE || '-';
        var sessionName = record.session_name || record.SESSION_NAME || '-';
        var grade = record.grade || record.GRADE || '-';
        var isAssigned = !!assignedSet[courseId];
        var rowClass = isAssigned ? ' class="mm-gradebook-row-assigned"' : '';
        var checked = isAssigned ? ' checked' : '';
        var disabled = isAssigned ? ' disabled' : '';
        var assignedHint = isAssigned
            ? '<span class="mm-assigned-course-hint">Already mentor</span>'
            : '';

        return '<tr' + rowClass + '>' +
            '<td>' + (i + 1) + '</td>' +
            '<td>' + escapeAttr(sessionName) + '</td>' +
            '<td>' + escapeAttr(course) + '</td>' +
            '<td><span class="enrollment-chip">' + escapeAttr(grade) + '</span></td>' +
            '<td class="text-center">' +
            '<input type="checkbox" class="mm-gradebook-course-checkbox" name="course_id[]" value="' + escapeAttr(courseId) + '" data-course="' + escapeAttr(course) + '" data-assigned="' + (isAssigned ? '1' : '0') + '"' + checked + disabled + '>' +
            assignedHint +
            '</td></tr>';
    }).join('');
    $('#mm-gradebook-records-table').html(rows);
}

function getMmTimeslotOptions() {
    return mmGbTimeslots.map(function (slot) {
        var id = slot.ts_id || slot.TS_ID;
        var name = slot.ts_name || slot.TS_NAME;
        return '<option value="' + escapeAttr(id) + '">' + escapeAttr(name) + '</option>';
    }).join('');
}

function addMmMentorTimingRow() {
    var options = getMmTimeslotOptions();
    var row =
        '<div class="timing-row">' +
        '<div><label class="form-label-styled">Weekday</label>' +
        '<select name="weekdays[]" class="form-control-styled mm-mentor-required-field">' +
        '<option value="">Select</option>' +
        '<option value="1">Monday</option><option value="2">Tuesday</option><option value="3">Wednesday</option>' +
        '<option value="4">Thursday</option><option value="5">Friday</option><option value="6">Saturday</option><option value="7">Sunday</option>' +
        '</select></div>' +
        '<div><label class="form-label-styled">From</label>' +
        '<select name="fromtiming[]" class="form-control-styled mm-mentor-required-field"><option value="">From</option>' + options + '</select></div>' +
        '<div><label class="form-label-styled">To</label>' +
        '<select name="totiming[]" class="form-control-styled mm-mentor-required-field"><option value="">To</option>' + options + '</select></div>' +
        '<div><button type="button" class="btn-act btn-reset remove-mm-mentor-timing-row"><i class="fa fa-trash"></i></button></div>' +
        '</div>';
    $('#mm-mentorTimingRows').append(row);
}

function getSelectedMmGradebookCourses() {
    return $('#mentorGradebookModal .mm-gradebook-course-checkbox:checked').filter(function () {
        return $(this).data('assigned') !== 1 && $(this).data('assigned') !== '1';
    }).map(function () {
        return { id: $(this).val(), course: $(this).data('course') };
    }).get();
}

function showMmMentorAssignmentPanel() {
    var selectedCourses = getSelectedMmGradebookCourses();
    if (selectedCourses.length === 0) {
        $('#mm-selectedCoursesList').html('');
        $('#mm-mentorAssignmentPanel').removeClass('is-visible');
        $('#mmSaveGradebookMentorBtn').hide();
        setMmGbStatus('', '');
        return;
    }
    $('#mm-selectedCoursesList').html(selectedCourses.map(function (item) {
        return '<li><strong>' + escapeAttr(item.course) + '</strong></li>';
    }).join(''));
    if ($('#mm-mentorTimingRows .timing-row').length === 0) {
        addMmMentorTimingRow();
    }
    $('#mm-mentorAssignmentPanel').addClass('is-visible');
    $('#mmSaveGradebookMentorBtn').show();
    setMmGbStatus('', '');
}

function validateMmAcademicForm() {
    var newCourses = getSelectedMmGradebookCourses();
    if (newCourses.length === 0) {
        var hasAssigned = $('#mentorGradebookModal .mm-gradebook-course-checkbox[data-assigned="1"]').length > 0;
        setMmGbStatus(
            hasAssigned
                ? 'Select at least one course that is not already assigned as mentor.'
                : 'Select at least one course.',
            'error'
        );
        return false;
    }
    if ($('#mm-mentorTimingRows .timing-row').length === 0) {
        setMmGbStatus('Add at least one weekday and time row.', 'error');
        return false;
    }
    var ok = true;
    $('#mmGradebookMentoringForm .mm-mentor-required-field').each(function () {
        if (!$(this).val()) ok = false;
    });
    if (!ok) {
        setMmGbStatus('Complete all weekday and time fields.', 'error');
        return false;
    }
    return true;
}

function loadMmGradebook(enrollmentNo) {
    $('#mmGradebookMentoringForm').addClass('is-visible');
    $('#mmMentorDetailSection').removeClass('is-visible');
    $('#saveChangesButton').hide();

    if (mmGbLoadedForEnrollment === enrollmentNo) {
        $('#mm-gradebook-loading').addClass('d-none');
        $('#mm-gradebook-content').removeClass('d-none');
        showMmMentorAssignmentPanel();
        return;
    }

    $('#mm-gradebook-content').addClass('d-none');
    $('#mm-gradebook-loading').removeClass('d-none');
    setMmGbStatus('', '');

    $.ajax({
        url: '{{ route("pm-gradebook-details") }}',
        type: 'POST',
        dataType: 'json',
        data: {
            _token: '{{ csrf_token() }}',
            enrollment_no: enrollmentNo,
            sess_id: getEffectiveSessionId()
        },
        success: function (resp) {
            mmGbTimeslots = resp.timeslots || [];
            renderMmGbRows(resp.gradebook || [], resp.mentor_co_ids || []);
            mmGbLoadedForEnrollment = enrollmentNo;
            $('#mm-gradebook-loading').addClass('d-none');
            $('#mm-gradebook-content').removeClass('d-none');
            showMmMentorAssignmentPanel();
        },
        error: function () {
            $('#mm-gradebook-loading').addClass('d-none');
            $('#mm-gradebook-content').removeClass('d-none');
            $('#mm-gradebook-records-table').html(
                '<tr><td colspan="5" class="text-center text-danger py-3">Failed to load gradebook.</td></tr>'
            );
            setMmGbStatus('Failed to load gradebook.', 'error');
        }
    });
}

$('#mentorGradebookModal').on('hidden.bs.modal', function () {
    resetMentorGradebookModal('');
    currentMentorEnrollmentForSlot = '';
});

$('#mentorGradebookModal').on('click', '.mentor-type-card', function () {
    var type = $(this).data('mentorType');
    $('#mentorGradebookModal .mentor-type-card').removeClass('is-active');
    $(this).addClass('is-active');

    var enr = currentMentorEnrollmentForSlot;
    if (!enr) return;

    if (type === 'academic') {
        loadMmGradebook(enr);
        return;
    }

    if (type === 'detail') {
        activateMmMentorDetailTab();
        loadMentorDetailView();
    }
});

$('#mm-addMentorTimingRow').on('click', function () {
    addMmMentorTimingRow();
});

$(document).on('change', '#mentorGradebookModal .mm-gradebook-course-checkbox', function () {
    showMmMentorAssignmentPanel();
});

$(document).on('click', '#mentorGradebookModal .remove-mm-mentor-timing-row', function () {
    $(this).closest('.timing-row').remove();
    showMmMentorAssignmentPanel();
});

$('#mmSaveGradebookMentorBtn').on('click', function () {
    if (!validateMmAcademicForm()) return;

    var sessId = getEffectiveSessionId();
    if (!sessId) {
        setMmGbStatus('Please select a session.', 'error');
        return;
    }

    var form = document.getElementById('mmGradebookMentoringForm');
    var formData = new FormData(form);
    formData.set('sess_id', sessId);

    $.ajax({
        url: '{{ route("makeMentor") }}',
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function () {
            setMmGbStatus('Saved successfully.', 'success');
            if (typeof showToast === 'function') {
                showToast('success', 'Academic mentor details saved.', 'Success');
            }
            setTimeout(function () {
                $('#mentorGradebookModal').modal('hide');
                fetchMentorsData();
            }, 500);
        },
        error: function () {
            setMmGbStatus('Error while saving.', 'error');
        }
    });
});
</script>
@endsection
