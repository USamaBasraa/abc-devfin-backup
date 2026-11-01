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
.schol-header h3 { color: #fff; font-size: 20px; font-weight: 600; margin: 0; }
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
@media (max-width: 768px) {
    .filter-field--session { flex: 1 1 100%; max-width: none; }
}
.table-card {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;
}
.table-toolbar {
    padding: 14px 18px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;
    font-size: 14px; font-weight: 600; color: #1e293b;
}
#myMentorsTable thead th {
    font-size: 11px; font-weight: 700; color: #64748b;  text-transform: capitalize !important;
    padding: 11px 13px; border-bottom: 2px solid #e2e8f0; white-space: nowrap;
}
#myMentorsTable tbody td {
    padding: 11px 13px; font-size: 13px; vertical-align: top;
    border-bottom: 1px solid #f1f5f9; color: #334155;
}
.enrollment-chip {
    font-family: 'Courier New', monospace; font-size: 12px; color: #3b82f6;
    background: rgba(59,130,246,0.08); padding: 3px 9px; border-radius: 5px;
    border: 1px solid rgba(59,130,246,0.2); font-weight: 700;
}
.adm-status-pill {
    display: inline-block; font-size: 11px; font-weight: 700; padding: 3px 10px;
    border-radius: 999px;
}
.adm-status-pill.is-assigned { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
.adm-status-pill.is-non-assigned { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
.btn-act {
    display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px;
    border-radius: 6px; font-size: 12px; font-weight: 600; border: 1px solid #e2e8f0;
    background: #fff; color: #475569; text-decoration: none !important;
}
.btn-act:hover { border-color: #3b82f6; color: #2563eb; background: #eff6ff; }
.btn-detail { color: #0891b2; border-color: rgba(8,145,178,0.35); background: rgba(8,145,178,0.06); }
.btn-detail:hover { background: rgba(8,145,178,0.12); color: #0891b2; border-color: #0891b2; }
.btn-success-mod {
    display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px;
    border-radius: 8px; border: none; background: #059669; color: #fff;
    font-size: 13px; font-weight: 600; cursor: pointer;
}
.btn-success-mod:hover { background: #047857; }
.btn-success-mod:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-cancel-mod {
    display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px;
    border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; color: #475569;
    font-size: 13px; font-weight: 600; cursor: pointer;
}
.form-label-styled {
    font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;
    letter-spacing: 0.4px; margin-bottom: 6px; display: block;
}
.form-control-styled {
    width: 100%; padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 8px;
    font-size: 13px; color: #334155;
}
.form-control-styled:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }
.pm-feedback-radios { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 6px; }
.pm-feedback-radio {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px;
    border: 1px solid #e2e8f0; border-radius: 10px; background: #fff; cursor: pointer;
    font-size: 13px; font-weight: 600; color: #475569; margin: 0;
}
.pm-feedback-radio input { margin: 0; }
.pm-feedback-radio.is-selected-sat { border-color: #86efac; background: #f0fdf4; color: #166534; }
.pm-feedback-radio.is-selected-nosat { border-color: #fcd34d; background: #fffbeb; color: #92400e; }
.pm-detail-status {
    display: none; margin-top: 12px; padding: 10px 12px; border-radius: 8px; font-size: 13px;
}
.pm-detail-status.is-error { display: block; color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; }
.pm-detail-status.is-success { display: block; color: #047857; background: #f0fdf4; border: 1px solid #bbf7d0; }
#pmMentorFeedbackModal .modal-content { border-radius: 12px; border: 1px solid #e2e8f0; }
#pmMentorFeedbackModal .modal-header {
    background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 16px 20px;
}
#pmMentorFeedbackModal .modal-title { font-size: 15px; font-weight: 600; color: #1e293b; }
#pmMentorFeedbackModal .modal-footer {
    border-top: 1px solid #e2e8f0; padding: 14px 20px; background: #f8fafc;
}
.info-alert-blue {
    background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px;
    padding: 12px 14px; font-size: 13px; color: #1e40af; margin-bottom: 16px;
}
#pmStudentInfoModal .modal-content {
    border-radius: 16px; overflow: hidden; border: none;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.35);
}
#pmStudentInfoModal .modal-dialog { max-width: 820px; }
.pm-modal-top {
    background: linear-gradient(135deg, #4454c3 0%, #3b82f6 55%, #2563eb 100%);
    padding: 18px 22px 20px; position: relative; color: #fff;
}
.pm-modal-top-title {
    font-size: 1.15rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 10px;
}
.pm-modal-top-title .fa {
    width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.18);
    display: inline-flex; align-items: center; justify-content: center; font-size: 16px;
}
.pm-modal-close {
    position: absolute; top: 14px; right: 14px; width: 34px; height: 34px;
    border: none; border-radius: 10px; background: rgba(255,255,255,0.2); color: #fff;
    font-size: 22px; line-height: 1; cursor: pointer; transition: background 0.15s;
}
.pm-modal-close:hover { background: rgba(255,255,255,0.32); }
.pm-modal-body-inner { padding: 22px 24px 20px; background: #f8fafc; }
.pm-profile-card {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
    padding: 22px 16px; text-align: center; height: 100%;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
}
.pm-photo-wrap {
    width: 120px; height: 120px; margin: 0 auto 14px; padding: 4px;
    border-radius: 50%; background: linear-gradient(135deg, #93c5fd, #4454c3);
}
.pm-student-photo {
    width: 100%; height: 100%; border-radius: 50%; border: 3px solid #fff;
    object-fit: cover; background: #e2e8f0;
}
.pm-student-name {
    font-size: 1.05rem; font-weight: 800; color: #1e40af; text-transform: uppercase;
    letter-spacing: 0.3px; line-height: 1.3; margin-bottom: 8px;
}
.pm-enrollment-pill {
    display: inline-block; font-family: 'Courier New', monospace; font-size: 11px;
    font-weight: 700; color: #2563eb; background: #eff6ff; border: 1px solid #bfdbfe;
    padding: 4px 10px; border-radius: 6px; margin-bottom: 12px;
}
.pm-badge-row { display: flex; flex-wrap: wrap; gap: 6px; justify-content: center; }
.pm-badge {
    font-size: 11px; font-weight: 700; padding: 5px 12px; border-radius: 999px;
    border: 1px solid transparent;
}
.pm-badge--status { background: #dbeafe; color: #1d4ed8; border-color: #93c5fd; }
.pm-badge--semester { background: #fef3c7; color: #b45309; border-color: #fcd34d; }
.pm-details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
@media (max-width: 767px) {
    .pm-details-grid { grid-template-columns: 1fr; }
}
.pm-info-box {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
    padding: 12px 14px; display: flex; align-items: flex-start; gap: 12px;
    transition: border-color 0.15s, box-shadow 0.15s; min-height: 68px;
}
.pm-info-box:hover {
    border-color: #93c5fd; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
}
.pm-info-icon {
    width: 36px; height: 36px; border-radius: 10px; background: #eff6ff; color: #3b82f6;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 14px;
}
.pm-info-label {
    color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.4px; display: block; margin-bottom: 3px;
}
.pm-info-value { color: #0f172a; font-weight: 600; font-size: 13px; line-height: 1.35; word-break: break-word; }
.pm-modal-footer {
    padding: 12px 24px 18px; background: #fff; border-top: 1px solid #e2e8f0;
    text-align: right;
}
.pm-btn-close-modal {
    display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px;
    border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; color: #475569;
    font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.15s;
}
.pm-btn-close-modal:hover { background: #f1f5f9; border-color: #cbd5e1; color: #1e293b; }
.pm-loading-state, .pm-error-state {
    text-align: center; padding: 48px 20px; background: #fff; border-radius: 12px;
    border: 1px dashed #e2e8f0;
}
.pm-loading-state .fa { color: #3b82f6; }
.pm-error-state { color: #dc2626; }
.pm-error-state .fa { font-size: 2rem; margin-bottom: 10px; display: block; }
.schol-header-actions {
    display: flex; align-items: center; flex-wrap: wrap; gap: 8px;
}
.btn-header-act {
    display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px;
    border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;
    border: 1px solid rgba(255,255,255,0.25); background: rgba(255,255,255,0.1);
    color: #e2e8f0; transition: all 0.15s;
}
.btn-header-act:hover:not(:disabled) { background: rgba(255,255,255,0.18); color: #fff; border-color: rgba(255,255,255,0.4); }
.btn-header-act:disabled { opacity: 0.45; cursor: not-allowed; }
.btn-header-act.is-primary { background: #059669; border-color: #059669; color: #fff; }
.btn-header-act.is-primary:hover:not(:disabled) { background: #047857; }
.req-status-pill {
    display: inline-block; font-size: 11px; font-weight: 700; padding: 3px 10px;
    border-radius: 999px; white-space: nowrap;
}
.req-status-pill.is-pending { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
.req-status-pill.is-completed { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
#pmStaffRequestModal .modal-content,
#pmStaffRequestListModal .modal-content { border-radius: 12px; border: 1px solid #e2e8f0; }
#pmStaffRequestListModal .modal-dialog { max-width: 900px; }


/* ==========================================================
   FIGMA OVERRIDES — Peer Mentorship / My Assigned Mentors
   Visual layer only. Existing Blade/HTML/JS structure, IDs,
   routes, AJAX calls, DataTable and modal logic are unchanged.
   ========================================================== */

.container-fluid.p-0 {
    position: relative;
    /* min-height: 100vh; */
    /* padding: 30px 30px 48px !important; */
    /* background: linear-gradient(90deg, #ffffff 0%, rgba(23, 103, 95, 0.10) 100%); */
    font-family: 'Montserrat', 'Open Sans', sans-serif;
    color: #252525;
}

/* Page title area — Figma uses plain content, no dark banner */
.schol-header {
    position: static;
    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
    padding: 0 !important;
    margin: 0 0 22px !important;
    min-height: 58px;
    display: block !important;
    box-shadow: none !important;
}

.schol-header h3 {
    margin: 0 !important;
    color: #252525 !important;
    font-size: 20px !important;
    font-weight: 600 !important;
    line-height: 1.3;
}

.schol-header h3 .fa {
    display: none;
}

.schol-header-sub {
    margin-top: 3px !important;
    color: #252525 !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1.45;
}

/* Keep original action elements; visually place them on the filter row */
.schol-header-actions {
    position: absolute;
    top: 96px;
    right: 30px;
    z-index: 5;
    display: flex !important;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: nowrap;
    gap: 10px !important;
}

.btn-header-act {
    min-height: 40px;
    padding: 10px 18px !important;
    border-radius: 8px !important;
    font-family: 'Montserrat', 'Open Sans', sans-serif;
    font-size: 13px !important;
    font-weight: 600 !important;
    line-height: 1 !important;
    box-shadow: none !important;
}

.btn-header-act.is-primary {
    background: #17675f !important;
    border: 1px solid #17675f !important;
    color: #ffffff !important;
}

.btn-header-act.is-primary:hover:not(:disabled) {
    background: #12564f !important;
    border-color: #12564f !important;
}

.btn-header-act:not(.is-primary) {
    background: rgba(255,255,255,0.78) !important;
    border: 1px solid #17675f !important;
    color: #17675f !important;
}

.btn-header-act:not(.is-primary):hover:not(:disabled) {
    background: rgba(23,103,95,0.08) !important;
    color: #17675f !important;
    border-color: #17675f !important;
}

.btn-header-act:disabled {
    opacity: .45 !important;
}

.schol-badge-count {
    min-height: 40px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px !important;
    background: rgba(255,255,255,0.78) !important;
    color: #17675f !important;
    border: 1px solid #17675f !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    white-space: nowrap;
}

/* Session filter row */
.filters-card {
    position: relative;
    min-height: 72px;
    padding: 16px !important;
    margin: 0 0 10px !important;
    background: rgba(255,255,255,0.88) !important;
    border: 0 !important;
    border-radius: 12px !important;
    box-shadow: 0 1px 1px rgba(0,0,0,0.02) !important;
}

.filters-row {
    min-height: 40px;
    display: flex !important;
    align-items: center !important;
    gap: 16px !important;
}

.filter-field--session {
    flex: 0 0 auto !important;
    max-width: none !important;
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 14px !important;
}

.field-label {
    margin: 0 !important;
    color: #475569 !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    text-transform: none !important;
    letter-spacing: 0 !important;
    white-space: nowrap;
}

.field-label .fa {
    display: none;
}

.filters-card .filter-control {
    width: 160px !important;
    min-height: 40px !important;
}

.filters-card .textbox {
    width: 160px !important;
    height: 40px !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    box-shadow: none !important;
}

.filters-card .textbox-focused {
    border-color: #17675f !important;
    box-shadow: 0 0 0 3px rgba(23,103,95,0.08) !important;
}

.filters-card .textbox .textbox-text {
    height: 38px !important;
    line-height: 38px !important;
    padding: 0 32px 0 12px !important;
    color: #1e293b !important;
    font-family: 'Montserrat', 'Open Sans', sans-serif !important;
    font-size: 13px !important;
    font-weight: 500 !important;
}

/* Main table card */
.table-card {
    margin-top: 0 !important;
    background: #ffffff !important;
    border: 0 !important;
    border-radius: 12px !important;
    overflow: hidden !important;
    box-shadow: 0 1px 2px rgba(15,23,42,0.03) !important;
}

/* Figma has no separate grey table title strip */
.table-toolbar {
    display: none !important;
}

#myMentorsTableWrap {
    background: #ffffff;
}

/* DataTables controls remain functional, but are visually compact */
#myMentorsTable_wrapper {
    font-family: 'Montserrat', 'Open Sans', sans-serif;
    color: #475569;
}

#myMentorsTable_wrapper .dataTables_filter {
    padding: 12px 16px 8px;
    margin: 0 !important;
    text-align: right;
}

#myMentorsTable_wrapper .dataTables_filter label {
    margin: 0;
    color: #64748b;
    font-size: 12px;
    font-weight: 500;
}

#myMentorsTable_wrapper .dataTables_filter input {
    width: 210px;
    height: 36px;
    margin-left: 8px;
    padding: 7px 11px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #ffffff;
    color: #1e293b;
    font-family: 'Montserrat', 'Open Sans', sans-serif;
    font-size: 12px;
    outline: none;
    box-shadow: none;
}

#myMentorsTable_wrapper .dataTables_filter input:focus {
    border-color: #17675f;
    box-shadow: 0 0 0 3px rgba(23,103,95,0.08);
}

#myMentorsTable {
    margin: 0 !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    width: 100% !important;
    background: #ffffff;
}

#myMentorsTable thead th {
    padding: 14px 14px !important;
    background: #17675f !important;
    color: #ffffff !important;
    border: 0 !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    line-height: 1.25;
    text-transform: uppercase !important;
    white-space: nowrap;
    vertical-align: middle;
}

#myMentorsTable tbody tr:nth-child(odd) {
    background: #ffffff !important;
}

#myMentorsTable tbody tr:nth-child(even) {
    background: #f8fafc !important;
}

#myMentorsTable tbody tr:hover {
    background: rgba(23,103,95,0.045) !important;
}

#myMentorsTable tbody td {
    padding: 13px 14px !important;
    color: #1e293b !important;
    border-top: 0 !important;
    border-bottom: 1px solid #e2e8f0 !important;
    font-size: 12px !important;
    font-weight: 400 !important;
    line-height: 1.45;
    vertical-align: middle !important;
}

#myMentorsTable tbody tr:last-child td {
    border-bottom: 0 !important;
}

.enrollment-chip {
    padding: 0 !important;
    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
    color: #1e293b !important;
    font-family: 'Montserrat', 'Open Sans', sans-serif !important;
    font-size: 12px !important;
    font-weight: 400 !important;
}

.adm-status-pill,
.req-status-pill {
    padding: 4px 12px !important;
    border-radius: 99px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
}

.adm-status-pill.is-assigned,
.req-status-pill.is-completed {
    background: #dcfce7 !important;
    color: #16a34a !important;
    border: 0 !important;
}

.adm-status-pill.is-non-assigned,
.req-status-pill.is-pending {
    background: #fef3c7 !important;
    color: #b45309 !important;
    border: 0 !important;
}

.btn-act {
    min-height: 30px;
    padding: 6px 10px !important;
    border-radius: 6px !important;
    border: 1px solid #17675f !important;
    background: #ffffff !important;
    color: #17675f !important;
    font-family: 'Montserrat', 'Open Sans', sans-serif !important;
    font-size: 11px !important;
    font-weight: 500 !important;
    line-height: 1 !important;
    box-shadow: none !important;
}

.btn-act:hover {
    background: rgba(23,103,95,0.06) !important;
    border-color: #17675f !important;
    color: #17675f !important;
}

.btn-detail {
    border-color: #64748b !important;
    color: #64748b !important;
}

.btn-detail:hover {
    background: #f8fafc !important;
    border-color: #475569 !important;
    color: #475569 !important;
}

/* DataTables footer controls */
#myMentorsTable_wrapper .dataTables_info,
#myMentorsTable_wrapper .dataTables_paginate {
    padding: 12px 16px !important;
    color: #64748b !important;
    font-size: 11px !important;
}

#myMentorsTable_wrapper .dataTables_paginate .paginate_button {
    min-width: 32px;
    height: 32px;
    padding: 7px 10px !important;
    margin-left: 4px !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 7px !important;
    background: #ffffff !important;
    color: #475569 !important;
}

#myMentorsTable_wrapper .dataTables_paginate .paginate_button.current,
#myMentorsTable_wrapper .dataTables_paginate .paginate_button:hover {
    background: #17675f !important;
    border-color: #17675f !important;
    color: #ffffff !important;
}

/* Modals — retain all original markup/logic, align colors with Figma */
#pmStudentInfoModal .pm-modal-top {
    background: #17675f !important;
}

.pm-modal-close {
    background: rgba(255,255,255,0.16) !important;
}

.pm-modal-close:hover {
    background: rgba(255,255,255,0.26) !important;
}

.btn-success-mod {
    background: #17675f !important;
}

.btn-success-mod:hover {
    background: #12564f !important;
}

.form-control-styled:focus {
    border-color: #17675f !important;
    box-shadow: 0 0 0 3px rgba(23,103,95,0.08) !important;
}

/* Responsive: return actions to normal flow so nothing overlaps */
@media (max-width: 991px) {
    .container-fluid.p-0 {
        padding: 22px 18px 36px !important;
    }

    .schol-header {
        display: flex !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 18px;
        flex-wrap: wrap;
    }

    .schol-header-actions {
        position: static !important;
        width: 100%;
        justify-content: flex-start;
        flex-wrap: wrap;
    }

    .filters-card {
        min-height: auto;
    }
}

@media (max-width: 768px) {
    .filter-field--session {
        width: 100%;
        flex: 1 1 100% !important;
    }

    .filters-card .filter-control,
    .filters-card .textbox {
        width: 100% !important;
    }

    .btn-header-act,
    .schol-badge-count {
        min-height: 38px;
    }
}

@media (max-width: 576px) {
    .container-fluid.p-0 {
        padding: 16px 12px 28px !important;
    }

    .schol-header h3 {
        font-size: 18px !important;
    }

    .schol-header-sub {
        font-size: 12px !important;
    }

    .filter-field--session {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 7px !important;
    }

    .schol-header-actions {
        gap: 8px !important;
    }

    .btn-header-act,
    .schol-badge-count {
        width: 100%;
        justify-content: center;
    }

    #myMentorsTable_wrapper .dataTables_filter {
        text-align: left;
    }

    #myMentorsTable_wrapper .dataTables_filter input {
        width: 100%;
        margin: 6px 0 0;
    }
}



#myMentorsTable thead th {
    /* padding: 14px 14px !important; */
    /* background: #17675f !important; */
    /* color: #ffffff !important; */
    /* border: 0 !important; */
    /* font-size: 10px !important; */
    /* font-weight: 700 !important; */
    /* line-height: 1.25; */
    text-transform: capitalize !important;
    /* white-space: nowrap; */
    /* vertical-align: middle; */
}
</style>

<input type="hidden" id="sess_id">

<div class="container-fluid p-0">
    <div class="schol-header">
        <div>
            <h3><i class="fa fa-user-circle mr-2"></i>My Assigned Mentors</h3>
            <div class="schol-header-sub">Student mentors assigned to you for the selected session</div>
        </div>
        <div class="schol-header-actions">
            <button type="button" class="btn-header-act is-primary" id="btnStaffMentorRequest" title="Request a student mentor for admin tasks">
                <i class="fa fa-plus-circle"></i> Request Mentor
            </button>
            <button type="button" class="btn-header-act" id="btnStaffMentorRequestList" title="View your submitted mentor requests">
                <i class="fa fa-list-alt"></i> My Requests
            </button>
            <span class="schol-badge-count" id="my-mentors-count-badge">
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
                                   if (suppressSessionRequest) return;
                                   fetchMyAssignedMentors();
                                   refreshStaffRequestButtonState();
                               }">
                </div>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-toolbar"><i class="fa fa-table mr-1"></i> Mentors assigned to me</div>
        <div id="myMentorsTableWrap">
            <div class="text-center p-4 text-muted">
                <i class="fa fa-spinner fa-spin fa-2x"></i>
                <div class="mt-2" style="font-size:13px;">Loading…</div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pmStudentInfoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="pm-modal-top">
                <button type="button" class="pm-modal-close" data-dismiss="modal" aria-label="Close">&times;</button>
                <h5 class="pm-modal-top-title">
                    <span class="fa fa-graduation-cap"></span>
                    Student Information
                </h5>
            </div>
            <div class="pm-modal-body-inner">
                <div id="pmStudentInfoBody">
                    <div class="pm-loading-state">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                        <div class="mt-2 text-muted" style="font-size:13px;">Loading student details…</div>
                    </div>
                </div>
            </div>
            <div class="pm-modal-footer">
                <button type="button" class="pm-btn-close-modal" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pmStaffRequestModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                <h5 class="modal-title" style="font-size:15px;font-weight:600;color:#1e293b;">
                    <i class="fa fa-paper-plane text-success mr-2"></i> Request Mentor
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="info-alert-blue">
                    <i class="fa fa-info-circle mr-1"></i>
                    Describe why you need a student mentor. You may submit multiple requests in the same session,
                    but each new request is allowed only after your previous one has been completed.
                </div>
                <div class="form-group mb-0">
                    <label class="form-label-styled" for="pm-staff-request-remarks">Why do you need a mentor? *</label>
                    <textarea id="pm-staff-request-remarks" class="form-control-styled" rows="5"
                              maxlength="200" placeholder="Explain your admin task or reason for requesting a mentor…"></textarea>
                    <small class="text-muted" style="font-size:11px;">Minimum 10 characters</small>
                </div>
                <div id="pm-staff-request-status" class="pm-detail-status"></div>
            </div>
            <div class="modal-footer" style="background:#f8fafc;border-top:1px solid #e2e8f0;">
                <button type="button" class="btn-cancel-mod" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn-success-mod" id="pmStaffRequestSubmitBtn">
                    <i class="fa fa-check"></i> Submit Request
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pmStaffRequestListModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                <h5 class="modal-title" style="font-size:15px;font-weight:600;color:#1e293b;">
                    <i class="fa fa-list-alt text-primary mr-2"></i> My Mentor Requests
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body" style="padding:0;">
                <div id="pmStaffRequestListBody">
                    <div class="text-center p-4 text-muted">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                        <div class="mt-2" style="font-size:13px;">Loading requests…</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#f8fafc;border-top:1px solid #e2e8f0;">
                <button type="button" class="btn-cancel-mod" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pmMentorFeedbackModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-commenting-o text-info mr-2"></i>
                    Mentor feedback — <span id="pm-fb-mentor-name" class="text-muted" style="font-size:13px;"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pm-fb-assignment-id">
                <input type="hidden" id="pm-fb-enrollment">
                <div class="info-alert-blue mb-3" style="margin-bottom:14px !important;">
                    <i class="fa fa-info-circle mr-1"></i>
                    Enrollment: <strong id="pm-fb-enrollment-display">—</strong>
                    — Submitting feedback will mark this assignment as <strong>non assigned</strong>.
                </div>
                <div class="form-group mb-3">
                    <label class="form-label-styled">Remarks about student mentor</label>
                    <textarea id="pm-fb-remarks" class="form-control-styled" rows="4"
                              placeholder="Your remarks regarding this student mentor…"></textarea>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label-styled">Satisfaction</label>
                    <div class="pm-feedback-radios">
                        <label class="pm-feedback-radio" id="pm-fb-label-sat">
                            <input type="radio" name="pm_fb_satisfaction" value="satisfaction"> Satisfaction
                        </label>
                        <label class="pm-feedback-radio" id="pm-fb-label-nosat">
                            <input type="radio" name="pm_fb_satisfaction" value="no satisfaction"> No satisfaction
                        </label>
                    </div>
                </div>
                <div id="pm-fb-status" class="pm-detail-status"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel-mod" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn-success-mod" id="pmFbSubmitBtn">
                    <i class="fa fa-check"></i> Submit feedback
                </button>
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
var myMentorsDt = null;

function syncFilterControlHeights() {
    var $el = $('#facultyWload_sess_id');
    if ($el.length && $el.data('combobox')) {
        try { $el.combobox('resize'); } catch (e) {}
    }
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
    fetchMyAssignedMentors();
    refreshStaffRequestButtonState();
}

function setStaffRequestStatus(message, type) {
    var el = document.getElementById('pm-staff-request-status');
    if (!el) return;
    el.className = 'pm-detail-status' + (type ? ' is-' + type : '');
    el.innerHTML = message || '';
}

function staffRequestStatusPill(status) {
    var s = String(status || '').toLowerCase().trim();
    var isCompleted = s === 'completed';
    return '<span class="req-status-pill ' + (isCompleted ? 'is-completed' : 'is-pending') + '">' +
        escapeHtml(isCompleted ? 'Completed' : 'Pending') + '</span>';
}

function formatRequestDate(value) {
    if (!value) return '—';
    var d = new Date(value);
    if (isNaN(d.getTime())) return escapeHtml(String(value));
    return escapeHtml(d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }));
}

function refreshStaffRequestButtonState() {
    var sessId = getEffectiveSessionId();
    var $btn = $('#btnStaffMentorRequest');

    $.ajax({
        url: '{{ route('staff-mentor-request-status') }}',
        method: 'GET',
        data: { sess_id: sessId },
        success: function (data) {
            var canSubmit = !!data.can_submit;
            $btn.prop('disabled', !canSubmit);
            if (!canSubmit) {
                $btn.attr('title', 'Your current request is still pending. Submit a new one after it is completed.');
            } else {
                $btn.attr('title', 'Request a student mentor for admin tasks');
            }
        },
        error: function () {
            $btn.prop('disabled', false);
        }
    });
}

function openStaffRequestModal() {
    var $btn = $('#btnStaffMentorRequest');
    if ($btn.prop('disabled')) {
        if (typeof showToast === 'function') {
            showToast(
                'warning',
                'Your previous request is still pending. You can submit a new request once it has been completed.',
                'Request pending'
            );
        }
        return;
    }
    $('#pm-staff-request-remarks').val('');
    setStaffRequestStatus('', '');
    $('#pmStaffRequestSubmitBtn').prop('disabled', false);
    $('#pmStaffRequestModal').modal('show');
}

function loadStaffRequestList() {
    $('#pmStaffRequestListBody').html(
        '<div class="text-center p-4 text-muted">' +
        '<i class="fa fa-spinner fa-spin fa-2x"></i>' +
        '<div class="mt-2" style="font-size:13px;">Loading requests…</div></div>'
    );
    $('#pmStaffRequestListModal').modal('show');

    $.ajax({
        url: '{{ route('my-staff-mentor-requests') }}',
        method: 'GET',
        success: function (data) {
            var rows = data.requests || [];
            if (!rows.length) {
                $('#pmStaffRequestListBody').html(
                    '<div class="empty-state" style="padding:40px 20px;">' +
                    '<i class="fa fa-inbox"></i>No mentor requests submitted yet.</div>'
                );
                return;
            }

            var tbody = '';
            rows.forEach(function (row, i) {
                tbody +=
                    '<tr>' +
                    '<td class="text-center text-muted">' + (i + 1) + '</td>' +
                    '<td>' + formatRequestDate(row.request_date) + '</td>' +
                    '<td class="text-center">' + escapeHtml(row.session_name || row.session_id || '—') + '</td>' +
                    '<td style="max-width:320px;white-space:normal;">' + escapeHtml(row.remarks || '—') + '</td>' +
                    '<td class="text-center">' + escapeHtml(row.requested_by || '—') + '</td>' +
                    '<td class="text-center">' + staffRequestStatusPill(row.status) + '</td>' +
                    '</tr>';
            });

            $('#pmStaffRequestListBody').html(
                '<div style="overflow-x:auto;">' +
                '<table class="table table-hover mb-0" style="width:100%;font-size:13px;">' +
                '<thead style="background:#f8fafc;">' +
                '<tr>' +
                '<th class="text-center">#</th>' +
                '<th>Date</th>' +
                '<th class="text-center">Session</th>' +
                '<th>Remarks</th>' +
                '<th class="text-center">Type</th>' +
                '<th class="text-center">Status</th>' +
                '</tr></thead><tbody>' + tbody + '</tbody></table></div>'
            );
        },
        error: function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to load requests.';
            $('#pmStaffRequestListBody').html(
                '<div class="text-center py-5 text-danger" style="font-size:13px;">' + escapeHtml(msg) + '</div>'
            );
        }
    });
}

function getEffectiveSessionId() {
    return $('#sess_id').val() || $('#facultyWload_sess_id').combobox('getValue') || defaultSessionId || '';
}

function escapeHtml(value) {
    return String(value || '')
        .replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;')
        .replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function escapeAttr(value) {
    return escapeHtml(value);
}

function statusPill(status) {
    var s = String(status || '').toLowerCase().trim();
    var isAssigned = s === 'assigned';
    return '<span class="adm-status-pill ' + (isAssigned ? 'is-assigned' : 'is-non-assigned') + '">' +
        escapeHtml(isAssigned ? 'Assigned' : 'Non assigned') + '</span>';
}

function formatSlots(slots) {
    slots = slots || [];
    if (!slots.length) return '<span class="text-muted">—</span>';
    return slots.map(function (s) {
        return escapeHtml((s.week_day || '—') + ' · ' + (s.from_time || '') + ' – ' + (s.to_time || ''));
    }).join('<br>');
}

function fetchMyAssignedMentors() {
    var sessId = getEffectiveSessionId();
    $('#myMentorsTableWrap').html(
        '<div class="text-center p-4 text-muted"><i class="fa fa-spinner fa-spin fa-2x"></i></div>'
    );

    $.ajax({
        url: '{{ route('get-my-assigned-mentors') }}',
        method: 'GET',
        data: { sess_id: sessId },
        success: function (data) {
            var rows = data.mentors || [];
            document.getElementById('my-mentors-count-badge').innerHTML =
                '<i class="fa fa-graduation-cap mr-1"></i> ' + rows.length + ' mentor' + (rows.length !== 1 ? 's' : '');

            var tbody = '';
            if (!rows.length) {
                tbody = '<tr><td colspan="8" class="text-center py-5 text-muted">' +
                    '<i class="fa fa-inbox fa-2x mb-2 d-block"></i>No mentor assigned to you in this session.</td></tr>';
            } else {
                rows.forEach(function (row, i) {
                    var enr = row.mentor_enrollment || '';
                    tbody +=
                        '<tr>' +
                        '<td class="text-center text-muted">' + (i + 1) + '</td>' +
                        '<td><span class="enrollment-chip">' + escapeHtml(enr) + '</span></td>' +
                        '<td>' + escapeHtml(row.mentor_student_name || '—') + '</td>' +
                        '<td>' + escapeHtml(row.mentor_department || '—') + '</td>' +
                        '<td>' + statusPill(row.status) + '</td>' +
                        '<td style="font-size:12px;">' + formatSlots(row.slots) + '</td>' +
                        '<td style="max-width:180px; font-size:12px;">' +
                            (row.description ? escapeHtml(row.description) : '<span class="text-muted">—</span>') +
                        '</td>' +
                        '<td class="text-nowrap">' +
                            '<button type="button" class="btn-act js-pm-student-profile" data-enrollment="' + escapeHtml(enr) + '">' +
                            '<i class="fa fa-user"></i> Profile</button> ' +
                            '<button type="button" class="btn-act btn-detail js-pm-mentor-feedback" ' +
                                'data-assignment-id="' + escapeAttr(row.assignment_id) + '" ' +
                                'data-enrollment="' + escapeAttr(enr) + '" ' +
                                'data-mentor-name="' + escapeAttr(row.mentor_student_name || '') + '">' +
                                '<i class="fa fa-info-circle"></i> Details</button>' +
                        '</td>' +
                        '</tr>';
                });
            }

            if (myMentorsDt) {
                try { myMentorsDt.destroy(); } catch (e) {}
                myMentorsDt = null;
            }

            $('#myMentorsTableWrap').html(
                '<div style="overflow-x:auto;">' +
                '<table class="table table-hover mb-0" id="myMentorsTable" style="width:100%">' +
                '<thead><tr>' +
                '<th>#</th><th>Enrollment</th><th>Mentor name</th><th>Department</th>' +
                '<th>Status</th><th>Day &amp; time</th><th>Remarks</th><th>Action</th>' +
                '</tr></thead><tbody>' + tbody + '</tbody></table></div>'
            );

            myMentorsDt = $('#myMentorsTable').DataTable({
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                order: [[2, 'asc']],
                dom: 'frtip',
                language: { searchPlaceholder: 'Search…' }
            });
        },
        error: function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to load.';
            document.getElementById('my-mentors-count-badge').innerHTML = '<i class="fa fa-exclamation-triangle"></i> Error';
            $('#myMentorsTableWrap').html('<div class="text-center py-5 text-danger">' + escapeHtml(msg) + '</div>');
        }
    });
}

function renderPmStudentInfoModal(student) {
    student = student || {};
    var status = student.student_status || '—';
    var semester = student.semester_status || 'N/A';
    var photoUrl = student.photo_url || '';
    var fallbackPhoto = 'https://cms.bnu.edu.pk/students_pics/noimage.png';
    var enrollment = student.enrollment_no || '—';

    var detailsHtml =
        infoBox('fa-user', "Father's Name", student.father_name) +
        infoBox('fa-phone', 'Mobile', student.mobile_number) +
        infoBox('fa-envelope', 'Email', student.email) +
        infoBox('fa-building', 'School', student.school_name) +
        infoBox('fa-university', 'Department', student.department_name) +
        infoBox('fa-book', 'Program', student.program_name) +
        infoBox('fa-graduation-cap', 'Degree Level', student.degree_level);

    var html =
        '<div class="row">' +
        '<div class="col-md-4 mb-3 mb-md-0">' +
        '<div class="pm-profile-card">' +
        '<div class="pm-photo-wrap">' +
        '<img src="' + escapeHtml(photoUrl) + '" alt="Student photo" class="pm-student-photo" ' +
        'onerror="this.onerror=null;this.src=\'' + fallbackPhoto + '\';">' +
        '</div>' +
        '<div class="pm-student-name">' + escapeHtml(student.student_name || '—') + '</div>' +
        '<div class="pm-enrollment-pill">' + escapeHtml(enrollment) + '</div>' +
        '<div class="pm-badge-row">' +
        '<span class="pm-badge pm-badge--status">Status: ' + escapeHtml(status) + '</span>' +
        '<span class="pm-badge pm-badge--semester">Semester: ' + escapeHtml(semester) + '</span>' +
        '</div></div></div>' +
        '<div class="col-md-8"><div class="pm-details-grid">' + detailsHtml + '</div></div>' +
        '</div>';

    $('#pmStudentInfoBody').html(html);
}

function infoBox(icon, label, value) {
    return '<div class="pm-info-box">' +
        '<span class="pm-info-icon"><i class="fa ' + icon + '"></i></span>' +
        '<div class="pm-info-text">' +
        '<span class="pm-info-label">' + escapeHtml(label) + '</span>' +
        '<span class="pm-info-value">' + escapeHtml(value || '—') + '</span>' +
        '</div></div>';
}

function openPmStudentInfoModal(enrollment) {
    if (!enrollment) return;

    $('#pmStudentInfoBody').html(
        '<div class="pm-loading-state">' +
        '<i class="fa fa-spinner fa-spin fa-2x"></i>' +
        '<div class="mt-2 text-muted" style="font-size:13px;">Loading student details…</div></div>'
    );
    $('#pmStudentInfoModal').modal('show');

    $.ajax({
        url: '{{ route('get-pm-student-basic-info') }}',
        method: 'GET',
        data: {
            enrollment: enrollment,
            sess_id: getEffectiveSessionId()
        },
        success: function (data) {
            renderPmStudentInfoModal(data.student || {});
        },
        error: function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to load student info.';
            $('#pmStudentInfoBody').html(
                '<div class="pm-error-state">' +
                '<i class="fa fa-exclamation-circle"></i>' +
                '<div>' + escapeHtml(msg) + '</div></div>'
            );
        }
    });
}

$(document).on('click', '.js-pm-student-profile', function () {
    openPmStudentInfoModal($(this).data('enrollment'));
});

var currentFeedbackRow = null;

function setPmFbStatus(message, type) {
    var el = document.getElementById('pm-fb-status');
    if (!el) return;
    el.className = 'pm-detail-status' + (type ? ' is-' + type : '');
    el.innerHTML = message || '';
}

function updatePmFbRadioStyles() {
    var val = $('input[name="pm_fb_satisfaction"]:checked').val();
    $('#pm-fb-label-sat').toggleClass('is-selected-sat', val === 'satisfaction');
    $('#pm-fb-label-nosat').toggleClass('is-selected-nosat', val === 'no satisfaction');
}

function openPmMentorFeedbackModal(assignmentId, enrollment, mentorName) {
    currentFeedbackRow = { assignment_id: assignmentId, enrollment: enrollment };
    $('#pm-fb-assignment-id').val(assignmentId);
    $('#pm-fb-enrollment').val(enrollment);
    $('#pm-fb-enrollment-display').text(enrollment || '—');
    $('#pm-fb-mentor-name').text(mentorName || enrollment || '');
    $('#pm-fb-remarks').val('');
    $('input[name="pm_fb_satisfaction"]').prop('checked', false);
    updatePmFbRadioStyles();
    setPmFbStatus('', '');
    $('#pmFbSubmitBtn').prop('disabled', false);
    $('#pmMentorFeedbackModal').modal('show');
}

$(document).on('change', 'input[name="pm_fb_satisfaction"]', updatePmFbRadioStyles);

$(document).on('click', '.js-pm-mentor-feedback', function () {
    openPmMentorFeedbackModal(
        $(this).data('assignment-id'),
        $(this).data('enrollment'),
        $(this).data('mentor-name')
    );
});

$('#pmFbSubmitBtn').on('click', function () {
    var assignmentId = $('#pm-fb-assignment-id').val();
    var sessId = getEffectiveSessionId();
    var satisfaction = $('input[name="pm_fb_satisfaction"]:checked').val();
    var remarks = $('#pm-fb-remarks').val().trim();

    if (!satisfaction) {
        setPmFbStatus('Please select Satisfaction or No satisfaction.', 'error');
        return;
    }

    var $btn = $(this);
    $btn.prop('disabled', true);
    setPmFbStatus('Submitting…', '');

    $.ajax({
        url: '{{ route('submit-employee-mentor-feedback') }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            assignment_id: assignmentId,
            sess_id: sessId,
            satisfaction: satisfaction,
            remarks: remarks
        },
        success: function (data) {
            setPmFbStatus(escapeHtml(data.message || 'Feedback submitted.'), 'success');
            $('#pmMentorFeedbackModal').modal('hide');
            fetchMyAssignedMentors();
        },
        error: function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Submit failed.';
            setPmFbStatus(escapeHtml(msg), 'error');
            $btn.prop('disabled', false);
        }
    });
});

$('#btnStaffMentorRequest').on('click', openStaffRequestModal);
$('#btnStaffMentorRequestList').on('click', loadStaffRequestList);

$('#pmStaffRequestSubmitBtn').on('click', function () {
    var remarks = $('#pm-staff-request-remarks').val().trim();
    var sessId = getEffectiveSessionId();

    if (remarks.length < 10) {
        setStaffRequestStatus('Please enter at least 10 characters describing why you need a mentor.', 'error');
        return;
    }

    var $btn = $(this);
    $btn.prop('disabled', true);
    setStaffRequestStatus('Submitting…', '');

    $.ajax({
        url: '{{ route('submit-staff-mentor-request') }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            remarks: remarks,
            sess_id: sessId
        },
        success: function (data) {
            $('#pmStaffRequestModal').modal('hide');
            refreshStaffRequestButtonState();
            if (typeof showToast === 'function') {
                showToast('success', data.message || 'Mentor request submitted successfully.', 'Success');
            }
        },
        error: function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message)
                ? xhr.responseJSON.message
                : 'Request could not be submitted. Please try again.';
            setStaffRequestStatus(escapeHtml(msg), 'error');
            if (typeof showToast === 'function') {
                showToast('error', msg, 'Error');
            }
            $btn.prop('disabled', false);
        }
    });
});

$('#pmStaffRequestModal').on('hidden.bs.modal', function () {
    setStaffRequestStatus('', '');
    $('#pmStaffRequestSubmitBtn').prop('disabled', false);
});

$(function () {
    setTimeout(syncFilterControlHeights, 300);
});
</script>
@endsection
