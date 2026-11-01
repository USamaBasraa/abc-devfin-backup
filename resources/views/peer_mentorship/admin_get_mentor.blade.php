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
    background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
    padding: 14px 16px; margin-bottom: 16px;
}
.filters-row { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 12px 14px; }
.filter-field { display: flex; flex-direction: column; min-width: 0; }
.filter-field--session { flex: 0 0 200px; max-width: 220px; }
.filter-field--school { flex: 0 0 200px; max-width: 220px; }
.filters-actions { flex: 0 0 auto; display: flex; align-items: flex-end; padding-bottom: 1px; }
.field-label {
    font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase;
    letter-spacing: 0.5px; display: block; margin-bottom: 5px; line-height: 1.2;
}
.filters-card .filter-control { width: 100%; min-height: 38px; }
.filters-card .textbox {
    width: 100% !important; border: 1px solid #e2e8f0 !important; border-radius: 8px !important;
    box-shadow: none !important; background: #fff !important; height: 38px !important;
}
.filters-card .textbox-focused {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12) !important;
}
.filters-card .textbox .textbox-text {
    height: 36px !important; line-height: 36px !important;
    padding: 0 32px 0 10px !important; font-size: 13px !important; color: #334155 !important;
}
.btn-reset {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    min-width: 88px; height: 38px; padding: 0 16px; background: #f8fafc;
    border: 1px solid #e2e8f0; border-radius: 8px; color: #475569;
    font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.15s;
}
.btn-reset:hover { border-color: #3b82f6; color: #2563eb; background: #eff6ff; }
.table-card {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;
}
.table-toolbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 18px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;
}
.table-toolbar-title { font-size: 14px; font-weight: 600; color: #1e293b; }
#adminMentorsTable thead tr { background: #f8fafc; }
#adminMentorsTable thead th {
    font-size: 11px !important; font-weight: 700 !important; color: #64748b !important;
    text-transform: uppercase !important; letter-spacing: 0.6px !important;
    padding: 11px 13px !important; border-bottom: 2px solid #e2e8f0 !important; white-space: nowrap;
}
#adminMentorsTable tbody td {
    padding: 11px 13px !important; font-size: 13px !important;
    vertical-align: middle !important; border-bottom: 1px solid #f1f5f9 !important; color: #334155;
}
#adminMentorsTable tbody tr:hover td { background: #f8fafc; }
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
.btn-assign { color: #2563eb; border-color: rgba(37,99,235,0.3); background: rgba(37,99,235,0.06); }
.btn-assign:hover { background: rgba(37,99,235,0.13); color: #2563eb; }
.mentor-type-pill {
    border: 1px solid #cbd5e1; background: #f8fafc; color: #334155;
    border-radius: 999px; padding: 5px 12px; font-size: 12px; font-weight: 600;
    cursor: pointer; transition: all 0.15s;
}
.mentor-type-pill:hover {
    border-color: #3b82f6; color: #1d4ed8; background: #eff6ff;
}
.gradebook-modal-dialog { max-width: 96vw; }
.gradebook-modal-body { padding: 20px; min-height: 52vh; background: #f8fafc; }
#adminMentorDetailModal .modal-content {
    border-radius: 12px; border: 1px solid #e2e8f0;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
}
#adminMentorDetailModal .modal-header {
    background: #f8fafc; border-bottom: 1px solid #e2e8f0;
    border-radius: 12px 12px 0 0; padding: 16px 20px;
}
#adminMentorDetailModal .modal-title { font-size: 15px; font-weight: 600; color: #1e293b; }
#adminMentorDetailModal .modal-footer {
    border-top: 1px solid #e2e8f0; padding: 14px 20px; background: #f8fafc;
    border-radius: 0 0 12px 12px;
}
#mentorTypeModal .modal-dialog { max-width: 440px; }
#mentorTypeModal .modal-content {
    border-radius: 12px; border: 1px solid #e2e8f0;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
}
#mentorTypeModal .modal-header {
    background: #f8fafc; border-bottom: 1px solid #e2e8f0;
    border-radius: 12px 12px 0 0; padding: 14px 18px;
}
#mentorTypeModal .modal-header .modal-title { font-size: 15px; font-weight: 600; color: #1e293b; margin: 0; }
#mentorTypeModal .modal-body { padding: 18px 20px; background: #fff; }
#mentorTypeModal .modal-footer {
    border-top: 1px solid #e2e8f0; padding: 12px 18px; background: #f8fafc;
    border-radius: 0 0 12px 12px; justify-content: flex-end; gap: 10px;
}
.section-head {
    font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;
    letter-spacing: 0.6px; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;
}
.mm-detail-toolbar {
    display: flex; align-items: center; justify-content: space-between;
    gap: 10px; flex-wrap: wrap; margin-bottom: 10px; font-size: 12px; color: #64748b;
}
.mm-detail-toolbar-actions { display: flex; gap: 8px; }
.mm-detail-toolbar-actions button {
    border: 1px solid #e2e8f0; background: #fff; color: #475569; border-radius: 6px;
    padding: 4px 10px; font-size: 11px; font-weight: 600; cursor: pointer;
}
.mm-detail-toolbar-actions button:hover {
    border-color: #3b82f6; color: #2563eb; background: #eff6ff;
}
.mm-courses-slots-scroll {
    max-height: min(52vh, 480px); overflow-y: auto; overflow-x: hidden;
    border: 1px solid #e2e8f0; border-radius: 10px; background: #fff;
}
.mm-unlinked-box { border-bottom: 1px solid #fde68a; background: #fffbeb; padding: 10px 12px; }
.mm-unlinked-box .mm-unlinked-hint { font-size: 11px; color: #92400e; margin: 0 0 8px; }
.mm-course-acc { border-bottom: 1px solid #f1f5f9; }
.mm-course-acc:last-child { border-bottom: none; }
.mm-course-acc-head {
    width: 100%; display: flex; align-items: center; gap: 8px; padding: 10px 12px;
    border: none; background: #f8fafc; text-align: left; cursor: pointer; transition: background 0.15s;
}
.mm-course-acc-head:hover, .mm-course-acc.is-open .mm-course-acc-head { background: #eff6ff; }
.mm-course-acc-title {
    flex: 1; font-size: 13px; font-weight: 600; color: #1e293b;
    min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.mm-course-acc-meta { font-size: 11px; color: #64748b; white-space: nowrap; }
.mm-slot-count-badge {
    font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 999px;
    background: #e0e7ff; color: #3730a3; white-space: nowrap;
}
.mm-slot-count-badge.is-empty { background: #f1f5f9; color: #94a3b8; }
.mm-course-acc-chevron { color: #64748b; font-size: 12px; width: 14px; text-align: center; }
.mm-course-acc-chevron i { display: inline-block; transition: transform 0.15s ease; }
.mm-course-acc-body { display: none; padding: 0 10px 10px; background: #fff; }
.mm-course-acc.is-open .mm-course-acc-body { display: block; }
.mm-course-acc.is-open .mm-course-acc-chevron i { transform: rotate(90deg); }
.mm-slots-table { width: 100%; margin-bottom: 6px; font-size: 12px; }
.mm-slots-table thead th {
    padding: 6px 6px; font-size: 10px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.4px; background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.mm-slots-table tbody td { padding: 4px 4px; vertical-align: middle; border-bottom: 1px solid #f8fafc; }
.mm-slots-table select {
    width: 100%; padding: 5px 8px; border: 1px solid #e2e8f0; border-radius: 5px;
    font-size: 12px; background: #fff; min-width: 0;
}
.mm-slots-table .mm-col-course { width: 36%; }
.mm-slots-table .mm-col-actions { width: 36px; text-align: center; }
.mm-slots-table .btn-remove-slot {
    width: 28px; height: 28px; padding: 0; border: 1px solid #fecaca;
    background: #fef2f2; color: #b91c1c; border-radius: 5px; cursor: pointer; line-height: 26px;
}
.mm-slots-table tr.mm-empty-slots td {
    padding: 10px; text-align: center; color: #94a3b8; font-size: 12px; border-bottom: none;
}
.mm-add-slot-btn {
    display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px;
    font-size: 11px; font-weight: 600; color: #2563eb; background: #eff6ff;
    border: 1px solid #bfdbfe; border-radius: 6px; cursor: pointer;
}
.mm-add-slot-btn:hover { background: #dbeafe; }
.mm-detail-status {
    display: none; margin-top: 12px; border-radius: 8px; padding: 9px 12px;
    font-size: 12px; font-weight: 600;
}
.mm-detail-status.is-error { display: block; color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; }
.mm-detail-status.is-success { display: block; color: #047857; background: #f0fdf4; border: 1px solid #bbf7d0; }
.info-alert-blue {
    background: rgba(59,130,246,0.07); border: 1px solid rgba(59,130,246,0.2);
    border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #2563eb; margin-bottom: 16px;
}
.form-label-styled {
    display: block; font-size: 11px; font-weight: 600; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
}
.form-control-styled {
    width: 100%; padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 8px;
    background: #f8fafc; font-size: 14px; color: #1e293b; transition: all 0.2s;
}
.form-control-styled:focus {
    outline: none; border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1); background: #fff;
}
.gradebook-status {
    display: none; margin-top: 12px; border-radius: 8px; padding: 9px 12px;
    font-size: 12px; font-weight: 600;
}
.gradebook-status.is-error { display: block; color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; }
.gradebook-status.is-success { display: block; color: #047857; background: #f0fdf4; border: 1px solid #bbf7d0; }
.btn-primary-mod, .btn-success-mod, .btn-cancel-mod {
    display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px;
    border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;
}
.btn-success-mod { background: #059669; color: #fff; border: none; }
.btn-cancel-mod { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.assign-panel {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin-bottom: 16px;
}
.assign-grid {
    display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px;
}
.assign-field--wide { grid-column: 1 / -1; }
.assign-actions { margin-top: 12px; display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap; }
.adm-pending-wrap {
    margin-top: 14px; border: 1px dashed #cbd5e1; border-radius: 10px;
    background: #f8fafc; padding: 12px; min-height: 60px;
}
.adm-pending-empty { text-align: center; color: #94a3b8; font-size: 13px; padding: 16px 8px; }
.adm-pending-card {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
    padding: 12px 14px; margin-bottom: 10px;
}
.adm-pending-card:last-child { margin-bottom: 0; }
.adm-pending-head {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 10px; margin-bottom: 10px;
}
.adm-pending-name { font-size: 14px; font-weight: 600; color: #1e293b; line-height: 1.35; }
.adm-pending-remove {
    border: 1px solid #fecaca; background: #fef2f2; color: #b91c1c;
    border-radius: 6px; width: 28px; height: 28px; cursor: pointer; flex-shrink: 0;
}
.adm-pending-fields { display: grid; grid-template-columns: 1fr 100px 100px 100px; gap: 8px; }
.adm-pending-fields .assign-field--wide { grid-column: 1 / -1; }
.adm-add-row { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; }
.adm-add-row .filter-control { flex: 1; min-width: 220px; }
.btn-add-emp {
    display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px;
    background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;
    border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; height: 38px;
}
.btn-add-emp:hover { background: #dbeafe; }
.gradebook-table-wrap { border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; background: #fff; }
@media (max-width: 768px) {
    .adm-pending-fields { grid-template-columns: 1fr; }
}
.mini-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.mini-table thead th {
    padding: 7px 10px; background: #f8fafc; font-size: 11px; font-weight: 600; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; text-align: left;
}
.mini-table tbody td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: top; }
.mini-table tbody tr:last-child td { border-bottom: none; }
.adm-status-pill {
    display: inline-block; font-size: 11px; font-weight: 700; padding: 3px 10px;
    border-radius: 999px; text-transform: capitalize; white-space: nowrap;
}
.adm-status-pill.is-assigned { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
.adm-status-pill.is-non-assigned { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
.adm-status-pill.is-clickable {
    cursor: pointer; border: none; transition: opacity 0.15s, transform 0.1s;
}
.adm-status-pill.is-clickable:hover { opacity: 0.88; transform: scale(1.03); }
.adm-status-pill.is-clickable:disabled { opacity: 0.55; cursor: wait; transform: none; }
.adm-remarks-block { font-size: 12px; line-height: 1.45; }
.adm-remarks-block + .adm-remarks-block { margin-top: 8px; padding-top: 8px; border-top: 1px dashed #e2e8f0; }
.adm-remarks-label {
    display: block; font-size: 10px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 3px;
}
.adm-fb-sat {
    display: inline-block; font-size: 10px; font-weight: 700; padding: 2px 8px;
    border-radius: 999px; margin-top: 4px;
}
.adm-fb-sat--yes { background: #dcfce7; color: #166534; }
.adm-fb-sat--no { background: #fef3c7; color: #92400e; }
@media (max-width: 768px) {
    .assign-grid { grid-template-columns: 1fr; }
    .assign-field--wide { grid-column: auto; }
    .filter-field--session, .filter-field--school { flex: 1 1 100%; max-width: none; }
    .filters-actions { width: 100%; }
    .filters-actions .btn-reset { width: 100%; }
}
.schol-header-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
.btn-header-staff-req {
    display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px;
    border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;
    border: 1px solid rgba(255,255,255,0.25); background: rgba(245,158,11,0.22);
    color: #fde68a; transition: all 0.15s;
}
.btn-header-staff-req:hover { background: rgba(245,158,11,0.35); color: #fff; border-color: rgba(253,224,71,0.5); }
.staff-req-count {
    background: rgba(0,0,0,0.18); border-radius: 10px; padding: 1px 7px;
    font-size: 11px; font-weight: 700; min-width: 20px; text-align: center;
}
#staffFacultyRequestsModal .modal-dialog { max-width: 1080px; }
#staffFacultyRequestsModal .modal-content {
    border-radius: 14px; border: none; overflow: hidden;
    box-shadow: 0 24px 64px rgba(15,23,42,0.22);
}
.sfr-modal-header {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px;
}
.sfr-modal-header .modal-title { color: #fff; font-size: 16px; font-weight: 600; margin: 0; }
.sfr-modal-header .close { color: #fff; opacity: 0.85; text-shadow: none; font-size: 26px; margin: 0; }
.sfr-modal-header .close:hover { opacity: 1; color: #fff; }
.sfr-modal-body { padding: 0; background: #f8fafc; min-height: 320px; }
.sfr-tabs { display: flex; gap: 0; padding: 14px 18px 0; background: #fff; border-bottom: 1px solid #e2e8f0; }
.sfr-tab {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px;
    border: none; border-bottom: 2px solid transparent; background: transparent;
    font-size: 13px; font-weight: 700; color: #64748b; cursor: pointer; margin-bottom: -1px;
}
.sfr-tab.active { color: #1e293b; border-bottom-color: #3b82f6; }
.sfr-tab-count {
    background: #e2e8f0; color: #475569; border-radius: 999px; padding: 1px 8px; font-size: 11px;
}
.sfr-tab.active .sfr-tab-count { background: #dbeafe; color: #1d4ed8; }
.sfr-panel { padding: 16px 18px 18px; }
.sfr-table-wrap { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
.sfr-table { width: 100%; margin: 0; font-size: 13px; }
.sfr-table thead th {
    font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase;
    letter-spacing: 0.5px; padding: 11px 13px; border-bottom: 2px solid #e2e8f0; background: #f8fafc;
    white-space: nowrap;
}
.sfr-table tbody td { padding: 11px 13px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
.sfr-table tbody tr:hover td { background: #f8fafc; }
.sfr-type-pill {
    display: inline-block; font-size: 11px; font-weight: 700; padding: 3px 10px;
    border-radius: 999px; text-transform: capitalize;
}
.sfr-type-pill.is-faculty { background: #ede9fe; color: #6d28d9; border: 1px solid #c4b5fd; }
.sfr-type-pill.is-staff { background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc; }
.sfr-status-pill {
    display: inline-block; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 999px;
}
.sfr-status-pill.is-pending { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
.sfr-status-pill.is-completed { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
.sfr-desc { max-width: 260px; white-space: normal; font-size: 12px; color: #475569; line-height: 1.45; }
.sfr-mentor-chip {
    font-family: 'Courier New', monospace; font-size: 11px; font-weight: 700; color: #2563eb;
    background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 3px 8px;
    display: inline-block;
}
.sfr-empty { text-align: center; padding: 48px 20px; color: #94a3b8; font-size: 13px; }
.sfr-empty i { font-size: 28px; margin-bottom: 10px; display: block; }
.sfr-picker-panel { display: none; padding: 16px 18px 18px; }
.sfr-picker-panel.is-visible { display: block; }
.sfr-picker-back {
    display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; margin-bottom: 12px;
    border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; color: #475569;
    font-size: 12px; font-weight: 600; cursor: pointer;
}
.sfr-picker-back:hover { border-color: #3b82f6; color: #2563eb; background: #eff6ff; }
.sfr-request-summary {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; margin-bottom: 14px;
}
.sfr-request-summary-title { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 8px; }
.sfr-request-summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 10px 16px; font-size: 13px; }
.sfr-request-summary-grid span { display: block; font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 2px; }
#adm-staff-request-banner {
    display: none; background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px;
    padding: 12px 14px; margin-bottom: 14px; font-size: 13px; color: #92400e;
}
#adm-staff-request-banner.is-visible { display: block; }
.adm-add-row.is-staff-request-hidden { display: none !important; }
</style>

<input type="hidden" id="sess_id">
<input type="hidden" id="school_id">

<div class="container-fluid p-0">
    <div class="schol-header">
        <div>
            <h3><i class="fa fa-user-secret mr-2"></i>Admin Mentors</h3>
            <div class="schol-header-sub">Browse admin mentors by session and school — assign employee mentor, day &amp; time</div>
        </div>
        <div class="schol-header-actions">
            <button type="button" class="btn-header-staff-req" id="btnStaffFacultyRequests" title="View staff/faculty mentor requests">
                <i class="fa fa-inbox"></i> Staff/Faculty Requests
                <span class="staff-req-count" id="staff-req-pending-badge">0</span>
            </button>
            <span class="schol-badge-count" id="admin-mentor-count-badge">
                <i class="fa fa-spinner fa-spin mr-1"></i> Loading…
            </span>
        </div>
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
                               textField: 'sess_name',
                               url: '{{ url('/sessions') }}',
                               method: 'GET',
                               prompt: 'Select session…',
                               panelHeight: 'auto',
                               onLoadSuccess: function() { setDefaultSession(); },
                               onSelect: function(rec) {
                                   $('#sess_id').val(rec.sess_id);
                                   $('#school_id').val('');
                                   $('#facultyWload_school_id').combobox('clear');
                                   if (suppressSessionRequest) return;
                                   fetchAdminMentorsData();
                                   refreshStaffRequestPendingBadge();
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
                               textField: 'school_name',
                               method: 'GET',
                               url: '{{ url('/schools') }}',
                               prompt: 'All schools',
                               panelHeight: 'auto',
                               onSelect: function(rec) {
                                   $('#school_id').val(rec.school_id);
                                   fetchAdminMentorsData();
                               }">
                </div>
            </div>
            <div class="filters-actions">
                <button type="button" class="btn-reset" onclick="resetAdminMentorFilters()">
                    <i class="fa fa-refresh" id="reset-icon-admin-mentors"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-toolbar">
            <span class="table-toolbar-title"><i class="fa fa-table mr-1"></i> Admin mentor list</span>
        </div>
        <div id="adminMentorListTable">
            <div class="text-center p-4 text-muted">
                <i class="fa fa-spinner fa-spin fa-2x"></i>
                <div class="mt-2" style="font-size:13px;">Select session to load mentors…</div>
            </div>
        </div>
    </div>
</div>

{{-- Assign employee mentor + day/time (admin mentor student) --}}
<div class="modal fade" id="adminMentorDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog gradebook-modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-user-plus text-info mr-2"></i> Assign employee mentor
                    <span id="adm-modal-enrollment" class="text-muted ml-2" style="font-size:12px;"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body gradebook-modal-body">
                <div id="adm-staff-request-banner"></div>
                <div class="info-alert-blue">
                    <i class="fa fa-info-circle mr-1"></i>
                    Admin mentor: <strong id="adm-modal-enrollment-display">—</strong>
                </div>

                <div class="assign-panel">
                    <div class="section-head"><i class="fa fa-link"></i> Add employees (each with own remarks &amp; time)</div>
                    <div class="adm-add-row" id="admEmployeeSearchRow">
                        <div class="filter-control">
                            <label class="form-label-styled" for="admEmployeeSelect">Search employee</label>
                            <select id="admEmployeeSelect" class="form-control-styled" style="width:100%;"></select>
                        </div>
                        <button type="button" class="btn-add-emp" id="admAddEmployeeBtn">
                            <i class="fa fa-plus"></i> Add to list
                        </button>
                    </div>

                    <div class="section-head mt-3 mb-2" style="font-size:11px;">
                        <i class="fa fa-users"></i> Pending (<span id="adm-pending-count">0</span>)
                    </div>
                    <div id="admPendingList" class="adm-pending-wrap">
                        <div class="adm-pending-empty" id="adm-pending-empty">Search and add employees — configure each row below.</div>
                    </div>

                    <div class="assign-actions">
                        <button type="button" class="btn-cancel-mod" id="admClearPendingBtn" style="display:none;">
                            <i class="fa fa-times"></i> Clear list
                        </button>
                        <button type="button" class="btn-success-mod" id="admAssignBatchBtn" disabled>
                            <i class="fa fa-check"></i> <span id="adm-assign-batch-label">Assign all</span>
                        </button>
                    </div>
                    <div id="adm-assign-status" class="mm-detail-status"></div>
                </div>

                <div class="section-head mt-3"><i class="fa fa-list"></i> Current assignments</div>
                <div class="gradebook-table-wrap">
                    <table class="mini-table" id="admAssignmentsTable">
                        <thead>
                            <tr>
                                <th>Employee mentor</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Remarks / Description</th>
                                <th>Day &amp; time</th>
                            </tr>
                        </thead>
                        <tbody id="adm-assignments-body">
                            <tr><td colspan="5" class="text-center text-muted py-3">Loading…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel-mod" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Staff / Faculty mentor requests --}}
<div class="modal fade" id="staffFacultyRequestsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="sfr-modal-header">
                <h5 class="modal-title"><i class="fa fa-inbox mr-2"></i> Staff / Faculty Mentor Requests</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="sfr-modal-body">
                <div id="sfrListView">
                    <div class="sfr-tabs">
                        <button type="button" class="sfr-tab active" data-sfr-tab="pending">
                            Pending <span class="sfr-tab-count" id="sfr-pending-tab-count">0</span>
                        </button>
                        <button type="button" class="sfr-tab" data-sfr-tab="completed">
                            Completed <span class="sfr-tab-count" id="sfr-completed-tab-count">0</span>
                        </button>
                    </div>
                    <div class="sfr-panel">
                        <div id="sfrRequestsBody">
                            <div class="sfr-empty"><i class="fa fa-spinner fa-spin"></i>Loading requests…</div>
                        </div>
                    </div>
                </div>
                <div id="sfrPickerView" class="sfr-picker-panel">
                    <button type="button" class="sfr-picker-back" id="sfrBackToList"><i class="fa fa-arrow-left"></i> Back to requests</button>
                    <div class="sfr-request-summary" id="sfrPickerSummary"></div>
                    <div class="section-head mb-2"><i class="fa fa-graduation-cap"></i> Select admin mentor to assign</div>
                    <div id="sfrMentorPickerBody">
                        <div class="sfr-empty"><i class="fa fa-spinner fa-spin"></i>Loading admin mentors…</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#f8fafc;border-top:1px solid #e2e8f0;">
                <button type="button" class="btn-cancel-mod" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Update mentor type — same as get_mentor --}}
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
var adminMentorsDt = null;
var currentAdminMentorEnrollment = '';
var admSelect2Initialized = false;
var admPendingEmployees = [];
var admTimeOptionsHtml = '@foreach ($timeslots as $row)<option value="{{ $row->ts_id }}">{{ $row->ts_name }}</option>@endforeach';
var admWeekdayOptionsHtml =
    '<option value="">—</option>' +
    '<option value="1">Monday</option><option value="2">Tuesday</option><option value="3">Wednesday</option>' +
    '<option value="4">Thursday</option><option value="5">Friday</option><option value="6">Saturday</option><option value="7">Sunday</option>';
var currentStaffRequestContext = null;
var sfrActiveTab = 'pending';

function syncAdminFilterControlHeights() {
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
    syncAdminFilterControlHeights();
    fetchAdminMentorsData();
    refreshStaffRequestPendingBadge();
}

function resetAdminMentorFilters() {
    var icon = document.getElementById('reset-icon-admin-mentors');
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
    fetchAdminMentorsData();
}

function getEffectiveSessionId() {
    return $('#sess_id').val() || $('#facultyWload_sess_id').combobox('getValue') || defaultSessionId || '';
}

function escapeHtml(value) {
    return String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function escapeAttr(value) {
    return escapeHtml(value);
}

function mentorTypeLabel(t) {
    var x = String(t || '').toLowerCase();
    if (x === 'admin') return 'Admin';
    if (x === 'both') return 'Both';
    if (x === 'academic') return 'Academic';
    return t || '—';
}

function fetchAdminMentorsData() {
    var sessId = getEffectiveSessionId();
    var schoolId = $('#school_id').val() || '';

    $('#adminMentorListTable').html(
        '<div class="text-center p-4 text-muted">' +
        '<i class="fa fa-spinner fa-spin fa-2x"></i>' +
        '<div class="mt-2" style="font-size:13px;">Loading mentors…</div></div>'
    );

    $.ajax({
        url: '{{ route('get-admin-mentors-list') }}',
        method: 'GET',
        data: { sess_id: sessId, school_id: schoolId },
        success: function (data) {
            var mentors = data.mentors || [];
            document.getElementById('admin-mentor-count-badge').innerHTML =
                '<i class="fa fa-users mr-1"></i> ' + mentors.length + ' mentor' + (mentors.length !== 1 ? 's' : '');

            var tbody = '';
            if (mentors.length === 0) {
                tbody = '<tr><td colspan="8" class="text-center py-5 text-muted">' +
                    '<i class="fa fa-inbox fa-2x mb-2 d-block"></i>No admin mentors for this filter.</td></tr>';
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
                        '<td>' + adminStaffStatusPill(row.staff_assign_status) + '</td>' +
                        '<td class="text-muted" style="font-size:12px;">' + escapeHtml(row.department) + '</td>' +
                        '<td>' +
                            '<a href="{{ url('reports-generator-student-profile') }}/' + encodeURIComponent(enr) + '" target="_blank" class="btn-act btn-profile">' +
                            '<i class="fa fa-user"></i> Profile</a>' +
                            '<button type="button" class="btn-act btn-detail js-show-admin-details" data-enrollment="' + escapeAttr(enr) + '">' +
                            '<i class="fa fa-info-circle"></i> Details</button>' +
                        '</td>' +
                        '</tr>';
                });
            }

            if (adminMentorsDt) {
                try { adminMentorsDt.destroy(); } catch (e) {}
                adminMentorsDt = null;
            }

            $('#adminMentorListTable').html(
                '<div style="overflow-x:auto;">' +
                '<table class="table table-hover mb-0" id="adminMentorsTable" style="width:100%">' +
                '<thead><tr>' +
                '<th class="text-center" style="width:40px;">#</th>' +
                '<th>Enrollment</th><th>Full name</th><th>Remarks</th><th>Mentor type</th>' +
                '<th>Status</th><th>Department</th><th>Actions</th>' +
                '</tr></thead><tbody>' + tbody + '</tbody></table></div>'
            );

            adminMentorsDt = $('#adminMentorsTable').DataTable({
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
            document.getElementById('admin-mentor-count-badge').innerHTML =
                '<i class="fa fa-exclamation-triangle mr-1"></i> Error';
            $('#adminMentorListTable').html(
                '<div class="text-center py-5 text-danger">Failed to load mentors.</div>'
            );
        }
    });
}

function setAdmAssignStatus(message, type) {
    var el = document.getElementById('adm-assign-status');
    if (!el) return;
    el.className = 'mm-detail-status' + (type ? ' is-' + type : '');
    el.innerHTML = message || '';
}

function initAdminAssignSelect2() {
    if (admSelect2Initialized) return;
    admSelect2Initialized = true;

    var select2Opts = {
        width: '100%',
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            delay: 300,
            dataType: 'json',
            data: function (params) {
                return { q: params.term || '' };
            },
            processResults: function (data) {
                return { results: data.results || [] };
            }
        }
    };

    $('#admEmployeeSelect').select2($.extend({}, select2Opts, {
        placeholder: 'Search employee by name or ID…',
        ajax: $.extend({}, select2Opts.ajax, { url: '{{ route('pm-search-employees') }}' })
    }));
}

function resetAdminAssignForm() {
    admPendingEmployees = [];
    $('#admEmployeeSelect').val(null).trigger('change');
    renderAdmPendingList();
    setAdmAssignStatus('', '');
    clearStaffRequestContext();
}

function clearStaffRequestContext() {
    currentStaffRequestContext = null;
    $('#adm-staff-request-banner').removeClass('is-visible').empty();
    $('#admEmployeeSearchRow').removeClass('is-staff-request-hidden');
}

function setStaffRequestContext(ctx) {
    currentStaffRequestContext = ctx || null;
    if (!ctx) {
        clearStaffRequestContext();
        return;
    }
    var typeLabel = String(ctx.request_type || 'staff').toLowerCase() === 'faculty' ? 'Faculty' : 'Staff';
    $('#adm-staff-request-banner').html(
        '<strong><i class="fa fa-link mr-1"></i> Staff request assignment</strong><br>' +
        'Assigning mentor for <strong>' + escapeHtml(ctx.employee_name || ('Employee ' + ctx.employee_id)) + '</strong> ' +
        '(ID: ' + escapeHtml(ctx.employee_id) + ', ' + escapeHtml(typeLabel) + '). ' +
        'Request: <em>' + escapeHtml(ctx.description || '—') + '</em>'
    ).addClass('is-visible');
    $('#admEmployeeSearchRow').addClass('is-staff-request-hidden');
}

function refreshStaffRequestPendingBadge() {
    var sessId = getEffectiveSessionId();
    $.ajax({
        url: '{{ route('admin-staff-mentor-requests') }}',
        method: 'GET',
        data: { sess_id: sessId, status: 'pending' },
        success: function (data) {
            var count = data.pending_count || 0;
            $('#staff-req-pending-badge').text(String(count));
        }
    });
}

function formatSfrDate(value) {
    if (!value) return '—';
    var d = new Date(value);
    if (isNaN(d.getTime())) return escapeHtml(String(value));
    return escapeHtml(d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }));
}

function sfrTypePill(type) {
    var t = String(type || 'staff').toLowerCase();
    var isFaculty = t === 'faculty';
    return '<span class="sfr-type-pill ' + (isFaculty ? 'is-faculty' : 'is-staff') + '">' +
        escapeHtml(isFaculty ? 'Faculty' : 'Staff') + '</span>';
}

function sfrStatusPill(status) {
    var s = String(status || '').toLowerCase();
    var isCompleted = s === 'completed';
    return '<span class="sfr-status-pill ' + (isCompleted ? 'is-completed' : 'is-pending') + '">' +
        escapeHtml(isCompleted ? 'Completed' : 'Pending') + '</span>';
}

function showSfrListView() {
    $('#sfrListView').show();
    $('#sfrPickerView').removeClass('is-visible');
}

function showSfrPickerView() {
    $('#sfrListView').hide();
    $('#sfrPickerView').addClass('is-visible');
}

function loadStaffFacultyRequests(status) {
    sfrActiveTab = status || 'pending';
    $('.sfr-tab').removeClass('active');
    $('.sfr-tab[data-sfr-tab="' + sfrActiveTab + '"]').addClass('active');

    $('#sfrRequestsBody').html('<div class="sfr-empty"><i class="fa fa-spinner fa-spin"></i>Loading requests…</div>');

    $.ajax({
        url: '{{ route('admin-staff-mentor-requests') }}',
        method: 'GET',
        data: { sess_id: getEffectiveSessionId(), status: sfrActiveTab },
        success: function (data) {
            $('#sfr-pending-tab-count').text(data.pending_count || 0);
            $('#sfr-completed-tab-count').text(data.completed_count || 0);
            $('#staff-req-pending-badge').text(String(data.pending_count || 0));

            var rows = data.requests || [];
            if (!rows.length) {
                $('#sfrRequestsBody').html(
                    '<div class="sfr-empty"><i class="fa fa-inbox"></i>' +
                    (sfrActiveTab === 'pending'
                        ? 'No pending staff/faculty requests for this session.'
                        : 'No completed requests for this session.') +
                    '</div>'
                );
                return;
            }

            var tbody = '';
            rows.forEach(function (row, i) {
                var mentorCell = '—';
                if (sfrActiveTab === 'completed') {
                    if (row.assigned_mentor_enrollment) {
                        mentorCell =
                            '<div class="sfr-mentor-chip">' + escapeHtml(row.assigned_mentor_enrollment) + '</div>' +
                            (row.assigned_mentor_name
                                ? '<div style="font-size:12px;margin-top:4px;">' + escapeHtml(row.assigned_mentor_name) + '</div>'
                                : '');
                    } else {
                        mentorCell = '<span class="text-muted">—</span>';
                    }
                }

                tbody +=
                    '<tr>' +
                    '<td class="text-center text-muted">' + (i + 1) + '</td>' +
                    '<td><span class="enrollment-chip">' + escapeHtml(row.employee_id) + '</span></td>' +
                    '<td>' + escapeHtml(row.employee_name || '—') + '</td>' +
                    '<td>' + escapeHtml(row.department || '—') + '</td>' +
                    '<td class="text-center">' + sfrTypePill(row.request_type) + '</td>' +
                    '<td><div class="sfr-desc">' + escapeHtml(row.description || '—') + '</div></td>' +
                    '<td>' + formatSfrDate(row.request_date) + '</td>' +
                    (sfrActiveTab === 'completed'
                        ? '<td>' + mentorCell + '</td>'
                        : '<td class="text-center">' + sfrStatusPill(row.status) + '</td>') +
                    (sfrActiveTab === 'pending'
                        ? '<td class="text-nowrap">' +
                            '<button type="button" class="btn-act btn-detail js-sfr-open-detail" ' +
                                'data-request-id="' + escapeAttr(row.request_id) + '" ' +
                                'data-employee-id="' + escapeAttr(row.employee_id) + '" ' +
                                'data-employee-name="' + escapeAttr(row.employee_name || '') + '" ' +
                                'data-department="' + escapeAttr(row.department || '') + '" ' +
                                'data-request-type="' + escapeAttr(row.request_type || 'staff') + '" ' +
                                'data-description="' + escapeAttr(row.description || '') + '">' +
                                '<i class="fa fa-info-circle"></i> Detail</button>' +
                          '</td>'
                        : '') +
                    '</tr>';
            });

            var actionHeader = sfrActiveTab === 'pending' ? '<th>Action</th>' : '';
            var mentorHeader = sfrActiveTab === 'completed' ? '<th>Assigned mentor</th>' : '<th>Status</th>';

            $('#sfrRequestsBody').html(
                '<div class="sfr-table-wrap"><div style="overflow-x:auto;">' +
                '<table class="sfr-table"><thead><tr>' +
                '<th class="text-center">#</th><th>Employee ID</th><th>Name</th><th>Department</th>' +
                '<th class="text-center">Type</th><th>Description</th><th>Date</th>' +
                mentorHeader + actionHeader +
                '</tr></thead><tbody>' + tbody + '</tbody></table></div></div>'
            );
        },
        error: function () {
            $('#sfrRequestsBody').html('<div class="sfr-empty text-danger"><i class="fa fa-exclamation-circle"></i>Failed to load requests.</div>');
        }
    });
}

function openStaffFacultyRequestsModal() {
    showSfrListView();
    sfrActiveTab = 'pending';
    $('.sfr-tab').removeClass('active');
    $('.sfr-tab[data-sfr-tab="pending"]').addClass('active');
    loadStaffFacultyRequests('pending');
    $('#staffFacultyRequestsModal').modal('show');
}

function openStaffRequestMentorPicker(ctx) {
    currentStaffRequestContext = ctx;
    showSfrPickerView();

    $('#sfrPickerSummary').html(
        '<div class="sfr-request-summary-title">Request details</div>' +
        '<div class="sfr-request-summary-grid">' +
        '<div><span>Employee</span>' + escapeHtml(ctx.employee_name || '—') + ' (' + escapeHtml(ctx.employee_id) + ')</div>' +
        '<div><span>Department</span>' + escapeHtml(ctx.department || '—') + '</div>' +
        '<div><span>Type</span>' + escapeHtml(String(ctx.request_type || 'staff')) + '</div>' +
        '<div style="grid-column:1/-1;"><span>Description</span>' + escapeHtml(ctx.description || '—') + '</div>' +
        '</div>'
    );

    $('#sfrMentorPickerBody').html('<div class="sfr-empty"><i class="fa fa-spinner fa-spin"></i>Loading admin mentors…</div>');

    $.ajax({
        url: '{{ route('get-admin-mentors-list') }}',
        method: 'GET',
        data: { sess_id: getEffectiveSessionId() },
        success: function (data) {
            var mentors = data.mentors || [];
            if (!mentors.length) {
                $('#sfrMentorPickerBody').html('<div class="sfr-empty"><i class="fa fa-inbox"></i>No admin mentors available for this session.</div>');
                return;
            }

            var tbody = '';
            mentors.forEach(function (row, i) {
                var enr = row.m_enrollment || '';
                tbody +=
                    '<tr>' +
                    '<td class="text-center text-muted">' + (i + 1) + '</td>' +
                    '<td><span class="enrollment-chip">' + escapeHtml(enr) + '</span></td>' +
                    '<td>' + escapeHtml(row.full_name || '—') + '</td>' +
                    '<td>' + escapeHtml(row.department || '—') + '</td>' +
                    '<td>' + adminStaffStatusPill(row.staff_assign_status) + '</td>' +
                    '<td class="text-nowrap">' +
                        '<button type="button" class="btn-act btn-assign js-sfr-pick-mentor" data-enrollment="' + escapeAttr(enr) + '">' +
                        '<i class="fa fa-user-plus"></i> Select &amp; assign</button>' +
                    '</td></tr>';
            });

            $('#sfrMentorPickerBody').html(
                '<div class="sfr-table-wrap"><div style="overflow-x:auto;">' +
                '<table class="sfr-table"><thead><tr>' +
                '<th class="text-center">#</th><th>Enrollment</th><th>Name</th><th>Department</th><th>Status</th><th>Action</th>' +
                '</tr></thead><tbody>' + tbody + '</tbody></table></div></div>'
            );
        },
        error: function () {
            $('#sfrMentorPickerBody').html('<div class="sfr-empty text-danger"><i class="fa fa-exclamation-circle"></i>Failed to load admin mentors.</div>');
        }
    });
}

function openAssignFromStaffRequest(mentorEnrollment) {
    if (!currentStaffRequestContext) return;

    var ctx = currentStaffRequestContext;
    currentAdminMentorEnrollment = mentorEnrollment || '';
    var display = currentAdminMentorEnrollment;
    $('#adm-modal-enrollment').text(display ? '(' + display + ')' : '');
    $('#adm-modal-enrollment-display').text(display || '—');
    $('#adm-modal-session-label').text(getEffectiveSessionId() || '—');

    initAdminAssignSelect2();
    admPendingEmployees = [];
    $('#admEmployeeSelect').val(null).trigger('change');

    var empLabel = (ctx.employee_name || ('Employee ' + ctx.employee_id)) +
        (ctx.department ? ' — ' + ctx.department : '');

    admPendingEmployees.push({
        employee_id: String(ctx.employee_id),
        employee_name: empLabel,
        description: ctx.description || '',
        weekday: '',
        from_time: '',
        to_time: ''
    });

    setStaffRequestContext(ctx);
    renderAdmPendingList();
    setAdmAssignStatus('', '');
    loadAdminMentorAssignments();

    $('#staffFacultyRequestsModal').modal('hide');
    $('#adminMentorDetailModal').modal('show');
}

function updateAdmPendingUi() {
    var n = admPendingEmployees.length;
    document.getElementById('adm-pending-count').textContent = String(n);
    document.getElementById('adm-assign-batch-label').textContent = n ? ('Assign all (' + n + ')') : 'Assign all';
    $('#admAssignBatchBtn').prop('disabled', n === 0);
    $('#admClearPendingBtn').toggle(n > 0);
}

function renderAdmPendingList() {
    var wrap = document.getElementById('admPendingList');
    if (!wrap) return;

    updateAdmPendingUi();

    if (!admPendingEmployees.length) {
        wrap.innerHTML = '<div class="adm-pending-empty">Search and add employees — configure each row below.</div>';
        return;
    }
    var html = '';
    admPendingEmployees.forEach(function (emp) {
        html +=
            '<div class="adm-pending-card" data-employee-id="' + escapeAttr(emp.employee_id) + '">' +
                '<div class="adm-pending-head">' +
                    '<div class="adm-pending-name">' + escapeHtml(emp.employee_name) + '</div>' +
                    '<button type="button" class="adm-pending-remove js-remove-pending" data-employee-id="' + escapeAttr(emp.employee_id) + '" title="Remove">&times;</button>' +
                '</div>' +
                '<div class="adm-pending-fields">' +
                    '<div class="assign-field assign-field--wide">' +
                        '<label class="form-label-styled">Remarks / Description <span class="text-muted font-weight-normal">(optional)</span></label>' +
                        '<textarea class="form-control-styled js-pending-desc" rows="2" placeholder="Notes for this employee…">' + escapeHtml(emp.description || '') + '</textarea>' +
                    '</div>' +
                    '<div class="assign-field">' +
                        '<label class="form-label-styled">Day <span class="text-muted font-weight-normal">(opt.)</span></label>' +
                        '<select class="form-control-styled js-pending-weekday">' + admWeekdayOptionsHtml + '</select>' +
                    '</div>' +
                    '<div class="assign-field">' +
                        '<label class="form-label-styled">From <span class="text-muted font-weight-normal">(opt.)</span></label>' +
                        '<select class="form-control-styled js-pending-from"><option value="">—</option>' + admTimeOptionsHtml + '</select>' +
                    '</div>' +
                    '<div class="assign-field">' +
                        '<label class="form-label-styled">To <span class="text-muted font-weight-normal">(opt.)</span></label>' +
                        '<select class="form-control-styled js-pending-to"><option value="">—</option>' + admTimeOptionsHtml + '</select>' +
                    '</div>' +
                '</div>' +
            '</div>';
    });
    wrap.innerHTML = html;

    admPendingEmployees.forEach(function (emp) {
        var card = wrap.querySelector('[data-employee-id="' + emp.employee_id + '"]');
        if (!card) return;
        if (emp.weekday) card.querySelector('.js-pending-weekday').value = emp.weekday;
        if (emp.from_time) card.querySelector('.js-pending-from').value = emp.from_time;
        if (emp.to_time) card.querySelector('.js-pending-to').value = emp.to_time;
    });
}

function collectAdmPendingPayload() {
    var employees = [];
    var errors = [];

    admPendingEmployees.forEach(function (emp, index) {
        var card = document.querySelector('#admPendingList [data-employee-id="' + emp.employee_id + '"]');
        if (!card) return;

        var weekday = card.querySelector('.js-pending-weekday').value;
        var fromTime = card.querySelector('.js-pending-from').value;
        var toTime = card.querySelector('.js-pending-to').value;
        var description = card.querySelector('.js-pending-desc').value;

        var hasAny = !!(weekday || fromTime || toTime);
        var hasFull = !!(weekday && fromTime && toTime);
        if (hasAny && !hasFull) {
            errors.push((emp.employee_name || ('#' + (index + 1))) + ': provide day, from, and to together, or leave all empty.');
            return;
        }
        if (hasFull && parseInt(fromTime, 10) >= parseInt(toTime, 10)) {
            errors.push((emp.employee_name || ('#' + (index + 1))) + ': end time must be after start time.');
            return;
        }

        employees.push({
            employee_id: emp.employee_id,
            description: description,
            weekday: weekday,
            from_time: fromTime,
            to_time: toTime
        });
    });

    return { employees: employees, errors: errors };
}

$('#admAddEmployeeBtn').on('click', function () {
    var data = $('#admEmployeeSelect').select2('data');
    if (!data || !data.length || !data[0].id) {
        setAdmAssignStatus('Select an employee from search first.', 'error');
        return;
    }

    var item = data[0];
    var empId = String(item.id);
    if (admPendingEmployees.some(function (e) { return String(e.employee_id) === empId; })) {
        setAdmAssignStatus('This employee is already in the list.', 'error');
        return;
    }

    admPendingEmployees.push({
        employee_id: empId,
        employee_name: item.text || ('Employee ' + empId),
        description: '',
        weekday: '',
        from_time: '',
        to_time: ''
    });

    $('#admEmployeeSelect').val(null).trigger('change');
    setAdmAssignStatus('', '');
    renderAdmPendingList();
});

$(document).on('click', '.js-remove-pending', function () {
    var empId = String($(this).data('employee-id'));
    admPendingEmployees = admPendingEmployees.filter(function (e) {
        return String(e.employee_id) !== empId;
    });
    renderAdmPendingList();
});

$('#admClearPendingBtn').on('click', function () {
    resetAdminAssignForm();
});

function adminStaffStatusLabel(status) {
    var s = String(status || '').toLowerCase().trim();
    if (s === 'assigned') return 'Assigned';
    if (s === 'non assigned' || s === 'non-assigned' || s === 'nonassigned') return 'Non assigned';
    return 'Non assigned';
}

function adminStaffStatusPill(status) {
    var s = String(status || '').toLowerCase().trim();
    var isAssigned = s === 'assigned';
    var cls = isAssigned ? 'is-assigned' : 'is-non-assigned';
    return '<span class="adm-status-pill ' + cls + '">' + escapeHtml(adminStaffStatusLabel(status)) + '</span>';
}

function adminAssignmentStatusPill(assignmentId, status) {
    var s = String(status || '').toLowerCase().trim();
    var isAssigned = s === 'assigned';
    var cls = isAssigned ? 'is-assigned' : 'is-non-assigned';
    var nextLabel = isAssigned ? 'Non assigned' : 'Assigned';
    return '<button type="button" class="adm-status-pill is-clickable ' + cls + ' js-adm-assign-status" ' +
        'data-assignment-id="' + escapeAttr(assignmentId) + '" ' +
        'data-status="' + escapeAttr(s || 'non assigned') + '" ' +
        'title="Click to change to ' + escapeAttr(nextLabel) + '">' +
        escapeHtml(adminStaffStatusLabel(status)) +
        ' <i class="fa fa-exchange" style="font-size:9px; opacity:0.75;"></i></button>';
}

function formatAssignmentRemarks(row) {
    var parts = [];
    if (row.description) {
        parts.push(
            '<div class="adm-remarks-block">' +
            '<span class="adm-remarks-label">Assign note</span>' +
            escapeHtml(row.description) +
            '</div>'
        );
    }
    if (row.feedback_remarks || row.feedback_satisfaction) {
        var fbBody = '';
        if (row.feedback_remarks) {
            fbBody += escapeHtml(row.feedback_remarks);
        }
        if (row.feedback_satisfaction) {
            var satCls = row.feedback_satisfaction === 'satisfaction' ? 'yes' : 'no';
            var satLabel = row.feedback_satisfaction === 'satisfaction' ? 'Satisfaction' : 'No satisfaction';
            fbBody += (fbBody ? '<br>' : '') +
                '<span class="adm-fb-sat adm-fb-sat--' + satCls + '">' + escapeHtml(satLabel) + '</span>';
        }
        parts.push(
            '<div class="adm-remarks-block">' +
            '<span class="adm-remarks-label">Employee feedback</span>' +
            fbBody +
            '</div>'
        );
    }
    if (!parts.length) {
        return '<span class="text-muted">—</span>';
    }
    return parts.join('');
}

function renderAdminAssignments(assignments) {
    var tbody = document.getElementById('adm-assignments-body');
    if (!tbody) return;

    assignments = assignments || [];
    if (!assignments.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No employee assignments yet.</td></tr>';
        return;
    }

    var html = '';
    assignments.forEach(function (row) {
        var slots = row.slots || [];
        var slotText = slots.length
            ? slots.map(function (s) {
                return escapeHtml((s.week_day || '—') + ' · ' + (s.from_time || '') + ' – ' + (s.to_time || ''));
            }).join('<br>')
            : '<span class="text-muted">—</span>';
        var displayStatus = row.status || 'assigned';

        html +=
            '<tr>' +
            '<td>' + escapeHtml(row.employee_name || '—') +
                (row.employee_id ? ' <span class="text-muted">(' + escapeHtml(row.employee_id) + ')</span>' : '') +
                (row.school_code ? '<br><small class="text-muted">' + escapeHtml(row.school_code) + '</small>' : '') +
            '</td>' +
            '<td>' + escapeHtml(row.dept_name || '—') + '</td>' +
            '<td>' + adminAssignmentStatusPill(row.md_id, displayStatus) + '</td>' +
            '<td style="max-width:240px;">' + formatAssignmentRemarks(row) + '</td>' +
            '<td style="font-size:12px;">' + slotText + '</td>' +
            '</tr>';
    });
    tbody.innerHTML = html;
}

function loadAdminMentorAssignments() {
    var sessId = getEffectiveSessionId();
    $('#adm-assignments-body').html('<tr><td colspan="5" class="text-center text-muted py-3">Loading…</td></tr>');

    $.ajax({
        url: '{{ route('admin-mentor-assignments') }}',
        method: 'GET',
        data: {
            mentor_enrollment: currentAdminMentorEnrollment,
            sess_id: sessId
        },
        success: function (data) {
            renderAdminAssignments(data.assignments || []);
        },
        error: function () {
            $('#adm-assignments-body').html(
                '<tr><td colspan="5" class="text-center text-danger py-3">Failed to load assignments.</td></tr>'
            );
        }
    });
}

function showAdminDetails(enrollment) {
    clearStaffRequestContext();
    currentAdminMentorEnrollment = enrollment || '';
    var display = currentAdminMentorEnrollment;
    $('#adm-modal-enrollment').text(display ? '(' + display + ')' : '');
    $('#adm-modal-enrollment-display').text(display || '—');
    $('#adm-modal-session-label').text(getEffectiveSessionId() || '—');

    initAdminAssignSelect2();
    resetAdminAssignForm();
    loadAdminMentorAssignments();
    $('#adminMentorDetailModal').modal('show');
}

$(document).on('click', '.js-show-admin-details', function () {
    showAdminDetails($(this).data('enrollment'));
});

$(document).on('click', '.js-adm-assign-status', function () {
    var $btn = $(this);
    if ($btn.prop('disabled')) return;

    var assignmentId = $btn.data('assignment-id');
    var current = String($btn.data('status') || '').toLowerCase().trim();
    var next = current === 'assigned' ? 'non assigned' : 'assigned';
    var nextLabel = next === 'assigned' ? 'Assigned' : 'Non assigned';

    if (!confirm('Change status to "' + nextLabel + '" for this employee mentor?')) {
        return;
    }

    $btn.prop('disabled', true);

    $.ajax({
        url: '{{ route('admin-mentor-assignment-status') }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            assignment_id: assignmentId,
            admin_mentor_enrollment: currentAdminMentorEnrollment,
            sess_id: getEffectiveSessionId(),
            status: next
        },
        success: function (res) {
            if (typeof showToast === 'function') {
                showToast('success', res.message || 'Status updated.', 'Success');
            }
            loadAdminMentorAssignments();
            fetchAdminMentorsData();
        },
        error: function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Status update failed.';
            if (typeof showToast === 'function') {
                showToast('error', msg, 'Error');
            } else {
                alert(msg);
            }
            $btn.prop('disabled', false);
        }
    });
});

$('#admAssignBatchBtn').on('click', function () {
    var sessId = getEffectiveSessionId();
    if (!sessId) {
        setAdmAssignStatus('Please select a session.', 'error');
        return;
    }

    var payload = collectAdmPendingPayload();
    if (payload.errors.length) {
        setAdmAssignStatus(escapeHtml(payload.errors.join('<br>')), 'error');
        return;
    }
    if (!payload.employees.length) {
        setAdmAssignStatus('Add at least one employee to the list.', 'error');
        return;
    }

    var $btn = $('#admAssignBatchBtn');
    $btn.prop('disabled', true);

    var formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('admin_mentor_enrollment', currentAdminMentorEnrollment);
    formData.append('sess_id', sessId);
    if (currentStaffRequestContext && currentStaffRequestContext.request_id) {
        formData.append('staff_request_id', currentStaffRequestContext.request_id);
    }
    payload.employees.forEach(function (emp, i) {
        formData.append('employees[' + i + '][employee_id]', emp.employee_id);
        formData.append('employees[' + i + '][description]', emp.description || '');
        formData.append('employees[' + i + '][weekday]', emp.weekday || '');
        formData.append('employees[' + i + '][from_time]', emp.from_time || '');
        formData.append('employees[' + i + '][to_time]', emp.to_time || '');
    });

    $.ajax({
        url: '{{ route('admin-mentor-assign-batch') }}',
        method: 'POST',
        dataType: 'json',
        processData: false,
        contentType: false,
        data: formData,
        success: function (res) {
            if (typeof showToast === 'function') {
                showToast('success', res.message || 'Assigned.', 'Success');
            }
            var skipped = res.skipped || [];
            var wasStaffRequest = !!(currentStaffRequestContext && currentStaffRequestContext.request_id);
            if (skipped.length) {
                var skipHtml = skipped.map(function (s) {
                    return 'ID ' + escapeHtml(s.employee_id) + ': ' + escapeHtml(s.message);
                }).join('<br>');
                setAdmAssignStatus('Skipped:<br>' + skipHtml, 'error');
            } else {
                setAdmAssignStatus('', '');
            }
            resetAdminAssignForm();
            loadAdminMentorAssignments();
            fetchAdminMentorsData();
            refreshStaffRequestPendingBadge();
            if (wasStaffRequest) {
                $('#adminMentorDetailModal').modal('hide');
            }
        },
        error: function (xhr) {
            var res = xhr.responseJSON || {};
            var msg = res.message || 'Assignment failed.';
            if (res.errors && typeof res.errors === 'object') {
                var parts = [];
                Object.keys(res.errors).forEach(function (key) {
                    var val = res.errors[key];
                    if (Array.isArray(val)) {
                        parts = parts.concat(val);
                    } else if (val) {
                        parts.push(String(val));
                    }
                });
                if (parts.length) {
                    msg = parts.join(' ');
                }
            }
            if (res.skipped && res.skipped.length && !res.message) {
                msg = res.skipped.map(function (s) {
                    return 'ID ' + s.employee_id + ': ' + s.message;
                }).join(' ');
            }
            setAdmAssignStatus(escapeHtml(msg), 'error');
        },
        complete: function () {
            updateAdmPendingUi();
        }
    });
});

$('#btnStaffFacultyRequests').on('click', openStaffFacultyRequestsModal);

$(document).on('click', '.sfr-tab', function () {
    loadStaffFacultyRequests($(this).data('sfr-tab'));
});

$('#sfrBackToList').on('click', function () {
    showSfrListView();
    loadStaffFacultyRequests(sfrActiveTab);
});

$(document).on('click', '.js-sfr-open-detail', function () {
    openStaffRequestMentorPicker({
        request_id: $(this).data('request-id'),
        employee_id: $(this).data('employee-id'),
        employee_name: $(this).data('employee-name'),
        department: $(this).data('department'),
        request_type: $(this).data('request-type'),
        description: $(this).data('description')
    });
});

$(document).on('click', '.js-sfr-pick-mentor', function () {
    openAssignFromStaffRequest($(this).data('enrollment'));
});

$('#staffFacultyRequestsModal').on('hidden.bs.modal', function () {
    showSfrListView();
});

$(document).on('click', '.js-open-mentor-type', function () {
    var enr = $(this).data('enrollment');
    var typ = ($(this).data('type') || 'admin').toString().toLowerCase();
    $('#mt-modal-enrollment').val(enr);
    $('#mt-modal-enrollment-display').text(enr || '—');
    $('#mt-modal-role').val(['academic', 'admin', 'both'].indexOf(typ) >= 0 ? typ : 'admin');
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
            fetchAdminMentorsData();
        },
        error: function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Update failed.';
            setMtModalStatus(escapeHtml(msg), 'error');
        }
    });
});

$(document).ready(function () {
    setTimeout(syncAdminFilterControlHeights, 80);
    $(window).on('resize.adminFilters', syncAdminFilterControlHeights);
});
</script>
@endsection
