@extends('layouts.app')

@section('content')

<style>
.is-header {
    background: linear-gradient(135deg, #1e293b 0%, #2d3f55 100%);
    border-radius: 12px; padding: 20px 24px; margin-bottom: 18px;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
}
.is-header h3 { color: #fff; font-size: 20px; font-weight: 600; margin: 0; }
.is-header-sub { color: #94a3b8; font-size: 13px; margin-top: 3px; }
.is-header-badge {
    background: rgba(59,130,246,0.2); color: #93c5fd; font-size: 12px; padding: 5px 12px;
    border-radius: 20px; border: 1px solid rgba(59,130,246,0.3); font-weight: 600; white-space: nowrap;
}

.is-filters { background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; margin-bottom:14px; }
.is-filters-row { display:flex; flex-wrap:wrap; align-items:flex-end; gap:12px 14px; }
.is-filter-field { display:flex; flex-direction:column; min-width:0; }
.is-filter-field--session, .is-filter-field--school { flex:0 0 220px; max-width:240px; }
.is-filter-label { font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:5px; display:block; }
.is-filter-control { width:100%; min-height:38px; }
.is-filters .textbox { width:100% !important; border:1px solid #e2e8f0 !important; border-radius:8px !important; box-shadow:none !important; background:#fff !important; height:38px !important; }
.is-filters .textbox-focused { border-color:#3b82f6 !important; box-shadow:0 0 0 3px rgba(59,130,246,0.12) !important; }
.is-filters .textbox .textbox-text { height:36px !important; line-height:36px !important; padding:0 32px 0 10px !important; font-size:13px !important; color:#334155 !important; }
.is-reset {
    display:inline-flex; align-items:center; justify-content:center; gap:6px;
    min-width:88px; height:38px; padding:0 16px; background:#f8fafc;
    border:1px solid #e2e8f0; border-radius:8px; color:#475569;
    font-size:13px; font-weight:600; cursor:pointer;
}
.is-reset:hover { border-color:#3b82f6; color:#2563eb; background:#eff6ff; }

.is-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin-bottom:14px; }
.is-stat {
    background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px;
    display:flex; align-items:center; gap:12px; cursor:pointer; transition: all 0.15s;
}
.is-stat:hover { border-color:#cbd5e1; transform: translateY(-1px); }
.is-stat .ic {
    width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center;
    font-size:18px;
}
.is-stat-meta .lbl { font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; font-weight:700; }
.is-stat-meta .val { font-size:22px; font-weight:700; color:#0f172a; line-height:1.1; }
.is-stat.active { border-color:#1e293b; box-shadow:0 0 0 3px rgba(30,41,59,0.08); }
.is-stat-total .ic { background:#eef2ff; color:#4338ca; }
.is-stat-pending .ic { background:#fef9c3; color:#854d0e; }
.is-stat-present .ic { background:#dcfce7; color:#166534; }
.is-stat-absent .ic { background:#fee2e2; color:#991b1b; }

.is-card { background:#fff; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; }
.is-card-toolbar {
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;
    padding:14px 18px; border-bottom:1px solid #e2e8f0; background:#f8fafc;
}
.is-card-title { font-size:14px; font-weight:700; color:#1e293b; }
.is-card-hint { color:#64748b; font-size:12px; }

.is-search { position: relative; display:flex; align-items:center; }
.is-search input {
    width: 260px; height: 36px; border: 1px solid #e2e8f0; border-radius: 8px;
    padding: 0 32px 0 34px; font-size: 13px; color: #1e293b; background: #fff;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.is-search input:focus {
    outline: none; border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
}
.is-search input::placeholder { color: #94a3b8; }
.is-search .is-search-icon {
    position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
    color: #94a3b8; font-size: 13px; pointer-events: none;
}
.is-search .is-search-clear {
    position: absolute; right: 6px; top: 50%; transform: translateY(-50%);
    width: 24px; height: 24px; border-radius: 50%; border: none; background: transparent;
    color: #94a3b8; cursor: pointer; display: none; align-items: center; justify-content: center;
}
.is-search .is-search-clear:hover { background: #f1f5f9; color: #475569; }
.is-search.has-value .is-search-clear { display: inline-flex; }

.is-search-empty { text-align:center; padding:32px 20px; color:#94a3b8; font-size:13px; display:none; }
.is-search-empty i { font-size:22px; display:block; margin-bottom:6px; color:#cbd5e1; }
@media (max-width: 600px) {
    .is-search input { width: 100%; }
    .is-search { flex: 1 1 100%; }
}

.is-table { width:100%; margin:0; }
.is-table thead th {
    font-size:11px !important; font-weight:700 !important; color:#64748b !important;
    text-transform:uppercase !important; letter-spacing:0.6px !important;
    padding:11px 13px !important; border-bottom:2px solid #e2e8f0 !important; white-space:nowrap;
    background:#f8fafc;
}
.is-table tbody td {
    padding:11px 13px !important; font-size:13px !important;
    vertical-align:middle !important; border-bottom:1px solid #f1f5f9 !important; color:#334155;
}
.is-table tbody tr:hover td { background:#f8fafc; }
.is-enrollment {
    font-family:'Courier New', monospace; font-size:12px; color:#3b82f6;
    background:rgba(59,130,246,0.08); padding:3px 9px; border-radius:5px;
    border:1px solid rgba(59,130,246,0.2); font-weight:700; white-space:nowrap; display:inline-block;
}
.is-enrollment.is-mentor { color:#7c3aed; background:rgba(124,58,237,0.08); border-color:rgba(124,58,237,0.2); }
.is-course-name { font-weight:600; color:#1e293b; }
.is-time-chip {
    display:inline-flex; align-items:center; gap:5px; font-size:12px; color:#475569;
    background:#f1f5f9; padding:4px 9px; border-radius:6px; white-space:nowrap; font-weight:600;
}
.is-time-chip i { color:#94a3b8; }
.is-date {
    display:inline-flex; flex-direction:column; line-height:1.2;
}
.is-date .d { font-size:13px; font-weight:700; color:#1e293b; }
.is-date .wd { font-size:10px; color:#64748b; text-transform:uppercase; letter-spacing:0.4px; }

.is-badge {
    display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:700;
    padding:3px 9px; border-radius:12px; text-transform:uppercase; letter-spacing:0.4px;
}
.is-badge.b-pending { background:#fef9c3; color:#854d0e; }
.is-badge.b-present { background:#dcfce7; color:#166534; }
.is-badge.b-absent  { background:#fee2e2; color:#991b1b; }

.is-actions { display:flex; gap:6px; flex-wrap:wrap; }
.is-session-stack { display:flex; flex-direction:column; gap:8px; }
.is-session-row {
    display:grid; grid-template-columns: minmax(120px,140px) minmax(170px,200px) minmax(90px,110px) 1fr;
    align-items:center; gap:10px;
    background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;
    padding:8px 10px;
}
.is-session-row + .is-session-row { margin-top:0; }
.is-session-row .is-date .d { font-size:12.5px; }
.is-session-row .is-time-chip { font-size:11.5px; padding:3px 8px; }
.is-session-row .is-badge { font-size:10px; padding:2px 7px; }
.is-session-row .is-actions .is-btn { padding:4px 8px; font-size:11.5px; }
.is-course-cell { display:flex; flex-direction:column; gap:4px; }
.is-course-cell .is-course-name { font-size:13.5px; }
.is-count-chip {
    display:inline-flex; align-items:center; gap:4px; align-self:flex-start;
    font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:12px;
    background:#eef2ff; color:#4338ca; text-transform:uppercase; letter-spacing:0.4px;
}
.is-group-row td { background:#fff !important; }
@media (max-width: 992px) {
    .is-session-row {
        grid-template-columns: 1fr 1fr;
        row-gap: 6px;
    }
    .is-session-row .is-actions { grid-column: 1 / -1; }
}
.is-btn {
    display:inline-flex; align-items:center; gap:5px; padding:5px 10px; border-radius:6px;
    font-size:12px; font-weight:600; cursor:pointer; border:1px solid;
    transition:all 0.15s; white-space:nowrap; text-decoration:none;
}
.is-btn:hover { text-decoration:none; }
.is-btn-schedule  { color:#2563eb; border-color:rgba(37,99,235,0.3); background:rgba(37,99,235,0.06); }
.is-btn-schedule:hover  { background:rgba(37,99,235,0.13); color:#2563eb; }
.is-btn-resched   { color:#b45309; border-color:rgba(217,119,6,0.3); background:rgba(245,158,11,0.08); }
.is-btn-resched:hover   { background:rgba(245,158,11,0.15); color:#b45309; }
.is-btn-feedback  { color:#059669; border-color:rgba(5,150,105,0.3); background:rgba(5,150,105,0.06); }
.is-btn-feedback:hover  { background:rgba(5,150,105,0.13); color:#059669; }
.is-btn-delete    { color:#991b1b; border-color:rgba(153,27,27,0.3); background:rgba(239,68,68,0.06); }
.is-btn-delete:hover    { background:rgba(239,68,68,0.13); color:#991b1b; }

.is-empty { text-align:center; padding:46px 20px; color:#94a3b8; font-size:13px; }
.is-empty i { font-size:30px; display:block; margin-bottom:10px; color:#cbd5e1; }

.is-modal .modal-content {
    border: none; border-radius: 14px; overflow: hidden;
    box-shadow: 0 18px 50px -20px rgba(15,23,42,0.25);
}
.is-modal .modal-header {
    background: linear-gradient(135deg, #1e293b 0%, #2d3f55 100%);
    color: #fff; border: none; padding: 16px 22px; align-items: center;
}
.is-modal .modal-header h5 {
    color: #fff; font-size: 15px; font-weight: 700; margin: 0;
    display: flex; align-items: center; gap: 8px;
}
.is-modal .modal-header .close { color: #fff; opacity: 0.85; text-shadow: none; }
.is-modal .modal-header .close:hover { opacity: 1; }
.is-modal .modal-header.danger { background: linear-gradient(135deg, #991b1b 0%, #b91c1c 100%); }
.is-modal .modal-header.warning { background: linear-gradient(135deg, #b45309 0%, #d97706 100%); }
.is-modal .modal-header.success { background: linear-gradient(135deg, #166534 0%, #16a34a 100%); }
.is-modal .modal-header .hdr-sub { font-size: 11.5px; color: rgba(255,255,255,0.75); font-weight: 500; margin-top: 2px; }
.is-modal .modal-body { padding: 20px 22px; background: #fff; }
.is-modal .modal-footer { border-top: 1px solid #e2e8f0; padding: 14px 22px; background: #f8fafc; }

.is-info-card {
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;
    padding: 12px 14px; display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 10px 16px;
    margin-bottom: 16px;
}
.is-info-card.cols-2 { grid-template-columns: repeat(2, minmax(0,1fr)); }
.is-info-card .info-item { display: flex; flex-direction: column; min-width: 0; }
.is-info-card .info-label {
    font-size: 10px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;
}
.is-info-card .info-value { font-size: 13px; font-weight: 600; color: #1e293b; word-break: break-word; }

.is-form-grid { display: grid; gap: 14px; }
.is-form-grid.cols-2 { grid-template-columns: repeat(2, minmax(0,1fr)); }
.is-form-field { display: flex; flex-direction: column; gap: 6px; }
.is-form-label {
    font-size: 11px; font-weight: 700; color: #475569;
    text-transform: uppercase; letter-spacing: 0.5px;
}
.is-form-control {
    height: 40px; border: 1px solid #e2e8f0; border-radius: 8px;
    padding: 0 12px; font-size: 13px; color: #1e293b; background: #fff;
    transition: border-color 0.15s, box-shadow 0.15s; width: 100%;
}
.is-form-control:focus {
    border-color: #3b82f6; outline: none;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
}
textarea.is-form-control { min-height: 70px; height: auto; padding: 10px 12px; resize: vertical; }

.is-modal-btn {
    display: inline-flex; align-items: center; gap: 6px; height: 38px;
    padding: 0 16px; border-radius: 8px; font-size: 13px; font-weight: 600;
    cursor: pointer; border: 1px solid; transition: all 0.15s;
}
.is-modal-btn-cancel  { color: #475569; background: #fff;  border-color: #e2e8f0; }
.is-modal-btn-cancel:hover  { border-color: #94a3b8; color: #1e293b; }
.is-modal-btn-primary { color: #fff; background: #2563eb; border-color: #2563eb; }
.is-modal-btn-primary:hover { background: #1d4ed8; border-color: #1d4ed8; }
.is-modal-btn-success { color: #fff; background: #16a34a; border-color: #16a34a; }
.is-modal-btn-success:hover { background: #15803d; border-color: #15803d; }
.is-modal-btn-danger  { color: #fff; background: #dc2626; border-color: #dc2626; }
.is-modal-btn-danger:hover  { background: #b91c1c; border-color: #b91c1c; }

.is-danger-banner {
    display: flex; align-items: flex-start; gap: 14px;
    padding: 16px; border-radius: 10px;
    background: #fef2f2; border: 1px solid #fecaca; color: #7f1d1d;
}
.is-danger-banner .ic {
    width: 44px; height: 44px; flex-shrink: 0; border-radius: 50%;
    background: #fee2e2; color: #dc2626;
    display: flex; align-items: center; justify-content: center; font-size: 20px;
}
.is-danger-banner h6 { font-size: 14px; font-weight: 700; color: #7f1d1d; margin: 0 0 4px; }
.is-danger-banner p  { font-size: 12.5px; margin: 0; color: #991b1b; line-height: 1.5; }

.is-schedule-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.is-schedule-table thead th {
    font-size: 10.5px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px;
    background: #f1f5f9; padding: 10px 12px; border-bottom: 1px solid #e2e8f0;
    text-align: left;
}
.is-schedule-table thead th:first-child { border-top-left-radius: 8px; }
.is-schedule-table thead th:last-child  { border-top-right-radius: 8px; }
.is-schedule-table tbody td {
    padding: 8px 10px; border-bottom: 1px solid #f1f5f9; background: #fff; vertical-align: middle;
}
.is-schedule-table tbody tr:last-child td { border-bottom: none; }
.is-schedule-table tbody tr td:first-child { padding-left: 12px; }
.is-schedule-table tbody tr td:last-child  { padding-right: 12px; text-align: center; }
.is-schedule-wrap { border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 12px; }
.is-icon-btn {
    width: 32px; height: 32px; border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;
    cursor: pointer; transition: all 0.15s;
}
.is-icon-btn:hover { background: #fecaca; color: #7f1d1d; }
.is-add-row-btn {
    display: inline-flex; align-items: center; gap: 6px; height: 36px;
    padding: 0 14px; border-radius: 8px; font-size: 12.5px; font-weight: 600;
    background: #ecfdf5; color: #166534; border: 1px dashed #86efac; cursor: pointer;
    transition: all 0.15s;
}
.is-add-row-btn:hover { background: #d1fae5; border-style: solid; }

.is-fb-section {
    border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px;
    background: #fff; margin-bottom: 14px;
}
.is-fb-section + .is-fb-section { margin-bottom: 0; }
.is-fb-section.fb-mentor { border-left: 4px solid #7c3aed; }
.is-fb-section.fb-student { border-left: 4px solid #3b82f6; }
.is-fb-head {
    display: flex; align-items: center; justify-content: space-between;
    gap: 10px; margin-bottom: 12px; flex-wrap: wrap;
}
.is-fb-title {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: 13px; font-weight: 700; color: #1e293b;
}
.is-fb-title .av {
    width: 28px; height: 28px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 13px; color: #fff;
}
.is-fb-title .av.mentor { background: #7c3aed; }
.is-fb-title .av.student { background: #3b82f6; }
.is-fb-grid {
    display: grid; gap: 10px 14px;
    grid-template-columns: repeat(2, minmax(0, 1fr));
}
.is-fb-grid .full { grid-column: 1 / -1; }
.is-fb-item { display: flex; flex-direction: column; min-width: 0; }
.is-fb-label {
    font-size: 10px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;
}
.is-fb-value {
    font-size: 13px; color: #1e293b; font-weight: 500; line-height: 1.5;
    word-break: break-word;
}
.is-fb-value.is-empty-val { color: #94a3b8; font-style: italic; font-weight: 400; }
.is-fb-pill {
    display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700;
    padding: 3px 9px; border-radius: 12px;
    background: #eef2ff; color: #4338ca; text-transform: uppercase; letter-spacing: 0.4px;
}
.is-fb-stars { color: #f59e0b; font-size: 14px; letter-spacing: 2px; }
.is-fb-stars .empty { color: #e2e8f0; }
.is-approve-btn {
    display: inline-flex; align-items: center; gap: 6px;
    height: 32px; padding: 0 12px; border-radius: 8px;
    font-size: 12px; font-weight: 600; cursor: pointer;
    color: #fff; background: #16a34a; border: 1px solid #16a34a; transition: all 0.15s;
}
.is-approve-btn:hover { background: #15803d; border-color: #15803d; }
.is-fb-loading { text-align: center; padding: 28px 12px; color: #94a3b8; font-size: 13px; }
.is-fb-loading i { font-size: 22px; margin-bottom: 6px; display: block; color: #cbd5e1; }

.is-proposed-wrap {
    border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;
    background: #fff; margin-top: 6px;
}
.is-proposed-head {
    display: flex; align-items: center; justify-content: space-between; gap: 8px;
    padding: 10px 12px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
    font-size: 11px; font-weight: 700; color: #475569;
    text-transform: uppercase; letter-spacing: 0.5px;
}
.is-proposed-list { display: flex; flex-direction: column; }
.is-proposed-row {
    display: grid;
    grid-template-columns: minmax(120px,140px) minmax(150px,170px) auto 1fr;
    align-items: center; gap: 10px;
    padding: 10px 12px; border-bottom: 1px solid #f1f5f9;
}
.is-proposed-row:last-child { border-bottom: none; }
.is-proposed-row.is-pending { background: #fffaf2; }
.is-proposed-row.is-approved { background: #f0fdf4; }
.is-proposed-row.is-declined { background: #fef2f2; }
.is-proposed-row .is-date .d { font-size: 13px; }
.is-proposed-row .is-time-chip { font-size: 11.5px; padding: 3px 8px; }
.is-decision-group {
    display: inline-flex; gap: 4px; padding: 2px;
    background: #f1f5f9; border-radius: 8px; align-items: center;
}
.is-decision-btn {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 10px; font-size: 11.5px; font-weight: 700;
    border: 1px solid transparent; border-radius: 6px;
    background: transparent; color: #64748b; cursor: pointer; transition: all 0.15s;
    text-transform: uppercase; letter-spacing: 0.3px;
}
.is-decision-btn:hover { background: #fff; color: #1e293b; }
.is-decision-btn.is-on-approve { background: #16a34a; color: #fff; border-color: #16a34a; box-shadow: 0 2px 4px -1px rgba(22,163,74,0.3); }
.is-decision-btn.is-on-decline { background: #dc2626; color: #fff; border-color: #dc2626; box-shadow: 0 2px 4px -1px rgba(220,38,38,0.3); }
.is-decision-btn.is-on-pending { background: #fff; color: #475569; border-color: #cbd5e1; }
.is-status-pill {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px;
    text-transform: uppercase; letter-spacing: 0.4px;
}
.is-status-pill.pending  { background: #fef9c3; color: #854d0e; }
.is-status-pill.approved { background: #dcfce7; color: #166534; }
.is-status-pill.declined { background: #fee2e2; color: #991b1b; }
.is-bulk-bar {
    display: flex; align-items: center; gap: 8px; padding: 8px 12px;
    background: #f8fafc; border-top: 1px solid #e2e8f0;
    flex-wrap: wrap; justify-content: space-between;
}
.is-bulk-actions { display: flex; gap: 6px; }
.is-bulk-actions .is-decision-btn { font-size: 11px; padding: 4px 9px; }
.is-empty-proposed { padding: 18px 12px; text-align: center; color: #94a3b8; font-size: 12.5px; }

@media (max-width: 768px) {
    .is-proposed-row {
        grid-template-columns: 1fr 1fr;
        row-gap: 6px;
    }
    .is-proposed-row .is-decision-group { grid-column: 1 / -1; }
    .is-proposed-row .is-status-pill { grid-column: 1 / -1; justify-self: flex-end; }
}

@media (max-width: 992px) {
    .is-stats { grid-template-columns:repeat(2,minmax(0,1fr)); }
}
@media (max-width: 600px) {
    .is-stats { grid-template-columns:1fr; }
    .is-filter-field--session, .is-filter-field--school { flex:1 1 100%; max-width:none; }
}
</style>

<input type="hidden" id="sess_id" value="{{ $currentSessionId }}">
<input type="hidden" id="school_id" value="{{ $schoolId }}">
<input type="hidden" id="attendance_filter" value="{{ $attendanceFilter }}">

<div class="container-fluid p-0">

    <div class="is-header">
        <div>
            <h3><i class="fa fa-handshake-o mr-2"></i> Introductory Sessions</h3>
            <div class="is-header-sub">Mentor &amp; student session schedule — filter by session and school</div>
        </div>
        <span class="is-header-badge"><i class="fa fa-calendar-check-o mr-1"></i> {{ $sessionCounts['total'] }} sessions</span>
    </div>

    <div class="is-filters">
        <div class="is-filters-row">
            <div class="is-filter-field is-filter-field--session">
                <label class="is-filter-label" for="filter_sess_id"><i class="fa fa-calendar mr-1"></i> Session</label>
                <div class="is-filter-control">
                    <input id="filter_sess_id" name="filter_sess_id" class="easyui-combobox"
                           data-options="
                               valueField:'sess_id', textField:'sess_name',
                               url:'{{ url('/sessions') }}', method:'GET',
                               prompt:'Select session…', panelHeight:'auto',
                               onLoadSuccess:function(){ setDefaultSession(); },
                               onSelect:function(rec){
                                   if (isSyncingSession) return;
                                   if (String(rec.sess_id) === String(defaultSessionId)) return;
                                   $('#sess_id').val(rec.sess_id);
                                   $('#school_id').val('');
                                   applyFilters();
                               }">
                </div>
            </div>
            <div class="is-filter-field is-filter-field--school">
                <label class="is-filter-label" for="filter_school_id"><i class="fa fa-building-o mr-1"></i> School</label>
                <div class="is-filter-control">
                    <input id="filter_school_id" name="filter_school_id" class="easyui-combobox"
                           data-options="
                               valueField:'school_id', textField:'school_name',
                               method:'GET', url:'{{ url('/schools') }}',
                               prompt:'All schools', panelHeight:'auto',
                               onLoadSuccess:function(){ setDefaultSchool(); },
                               onSelect:function(rec){
                                   if (isSyncingSchool) return;
                                   if (String(rec.school_id) === String(defaultSchoolId || '')) return;
                                   $('#school_id').val(rec.school_id);
                                   applyFilters();
                               }">
                </div>
            </div>
            <div class="ml-auto" style="display:flex;align-items:flex-end;gap:8px;">
                <button type="button" class="is-reset" onclick="resetIntroFilters()">
                    <i class="fa fa-refresh" id="is-reset-icon"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <div class="is-stats">
        <div class="is-stat is-stat-total {{ $attendanceFilter === 'All' ? 'active' : '' }}" data-status="All">
            <div class="ic"><i class="fa fa-list-ul"></i></div>
            <div class="is-stat-meta">
                <div class="lbl">Total</div>
                <div class="val">{{ $sessionCounts['total'] }}</div>
            </div>
        </div>
        <div class="is-stat is-stat-pending {{ $attendanceFilter === 'Pending' ? 'active' : '' }}" data-status="Pending">
            <div class="ic"><i class="fa fa-hourglass-half"></i></div>
            <div class="is-stat-meta">
                <div class="lbl">Pending</div>
                <div class="val">{{ $sessionCounts['pending'] }}</div>
            </div>
        </div>
        <div class="is-stat is-stat-present {{ $attendanceFilter === 'Present' ? 'active' : '' }}" data-status="Present">
            <div class="ic"><i class="fa fa-check-circle"></i></div>
            <div class="is-stat-meta">
                <div class="lbl">Present</div>
                <div class="val">{{ $sessionCounts['present'] }}</div>
            </div>
        </div>
        <div class="is-stat is-stat-absent {{ $attendanceFilter === 'Absent' ? 'active' : '' }}" data-status="Absent">
            <div class="ic"><i class="fa fa-times-circle"></i></div>
            <div class="is-stat-meta">
                <div class="lbl">Absent</div>
                <div class="val">{{ $sessionCounts['absent'] }}</div>
            </div>
        </div>
    </div>

    <div class="is-card">
        <div class="is-card-toolbar">
            <div class="is-card-title"><i class="fa fa-users mr-1"></i> Sessions list</div>
            <div class="is-search" id="sessionSearchWrap">
                <i class="fa fa-search is-search-icon"></i>
                <input type="text" id="sessionSearchInput" placeholder="Search by mentor or student enrollment…" autocomplete="off">
                <button type="button" class="is-search-clear" id="sessionSearchClear" title="Clear search">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>

        @php
            $groupedSessions = collect($sessions)->groupBy(function ($s) {
                return strtoupper(trim($s->mentor_m_id ?? '')) . '||'
                    . strtoupper(trim($s->student_id ?? '')) . '||'
                    . ($s->course_id ?? '');
            });
        @endphp

        @if($groupedSessions->isEmpty())
            <div class="is-empty">
                <i class="fa fa-calendar-times-o"></i>
                No sessions found for the selected filters.
            </div>
        @else
        <div class="table-responsive">
            <table class="table is-table mb-0">
                <thead>
                    <tr>
                        <th style="width:120px;">Mentor</th>
                        <th style="width:120px;">Student</th>
                        <th style="width:220px;">Course</th>
                        <th>Sessions</th>
                        <th class="text-center" style="width:140px;">Group Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupedSessions as $groupKey => $rows)
                        @php
                            $first = $rows->first();
                            $sessionCount = $rows->count();
                        @endphp
                        <tr class="is-group-row" data-search="{{ strtolower(trim(($first->mentor_m_id ?? '').' '.($first->student_id ?? '').' '.($first->course_name ?? ''))) }}">
                            <td><span class="is-enrollment is-mentor">{{ $first->mentor_m_id }}</span></td>
                            <td><span class="is-enrollment">{{ $first->student_id }}</span></td>
                            <td>
                                <div class="is-course-cell">
                                    <span class="is-course-name">{{ $first->course_name ?: '—' }}</span>
                                    <span class="is-count-chip"><i class="fa fa-list-ol"></i> {{ $sessionCount }} session{{ $sessionCount > 1 ? 's' : '' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="is-session-stack">
                                    @foreach($rows as $session)
                                        @php
                                            $att = $session->pm_attendance ?: 'Pending';
                                            $attClass = $att === 'Present' ? 'b-present' : ($att === 'Absent' ? 'b-absent' : 'b-pending');
                                            $weekday = $session->weekday ?: '';
                                            $dateLabel = $session->session_date
                                                ? \Carbon\Carbon::parse($session->session_date)->format('d M Y')
                                                : '-';
                                        @endphp
                                        <div class="is-session-row">
                                            <span class="is-date">
                                                <span class="d">{{ $dateLabel }}</span>
                                                @if($weekday)
                                                    <span class="wd">{{ $weekday }}</span>
                                                @endif
                                            </span>
                                            <span class="is-time-chip"><i class="fa fa-clock-o"></i> {{ $session->from_time }} &mdash; {{ $session->to_time }}</span>
                                            <span class="is-badge {{ $attClass }}">{{ $att }}</span>
                                            <div class="is-actions" style="justify-content:flex-end;">
                                                @if($att === 'Present')
                                                    <button type="button" class="is-btn is-btn-feedback btn-feedback" data-session_id="{{ $session->id }}">
                                                        <i class="fa fa-comment-o"></i> Feedback
                                                    </button>
                                                @endif
                                                <button type="button" class="is-btn is-btn-resched"
                                                        data-session_id="{{ $session->id }}"
                                                        data-mentor="{{ $session->mentor_m_id }}"
                                                        data-student="{{ $session->student_id }}"
                                                        data-course="{{ $session->course_name }}"
                                                        data-date="{{ $dateLabel }}"
                                                        data-raw-date="{{ $session->session_date ? \Carbon\Carbon::parse($session->session_date)->format('Y-m-d') : '' }}"
                                                        data-from-time="{{ $session->from_time }}"
                                                        data-to-time="{{ $session->to_time }}"
                                                        data-time="{{ $session->from_time }} — {{ $session->to_time }}"
                                                        onclick="reschedual(this)">
                                                    <i class="fa fa-refresh"></i> Re-Schedule
                                                </button>
                                                <button type="button" class="is-btn is-btn-delete"
                                                        data-session_id="{{ $session->id }}"
                                                        data-mentor="{{ $session->mentor_m_id }}"
                                                        data-student="{{ $session->student_id }}"
                                                        data-course="{{ $session->course_name }}"
                                                        data-date="{{ $dateLabel }}"
                                                        data-time="{{ $session->from_time }} — {{ $session->to_time }}"
                                                        data-attendance="{{ $att }}"
                                                        onclick="confirmDelete(this)">
                                                    <i class="fa fa-trash-o"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="is-btn is-btn-schedule"
                                        onclick="openWeeklyScheduleModal('{{ $first->mentor_m_id }}', '{{ $first->student_id }}', '{{ $first->course_id }}', '{{ $first->course_name }}')">
                                    <i class="fa fa-calendar-plus-o"></i> Add Schedule
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="is-search-empty" id="sessionSearchEmpty">
                <i class="fa fa-search"></i>
                No sessions match your search.
            </div>
        </div>
        @endif
    </div>

</div>


<div class="modal fade is-modal" id="deleteSessionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header danger">
                <div>
                    <h5><i class="fa fa-trash-o"></i> Delete Session</h5>
                    <div class="hdr-sub">This action cannot be undone</div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="is-danger-banner mb-3">
                    <div class="ic"><i class="fa fa-exclamation-triangle"></i></div>
                    <div>
                        <h6>Confirm deletion</h6>
                        <p>This session record will be permanently removed. Mentor and student will keep their other sessions.</p>
                    </div>
                </div>
                <div class="is-info-card" id="delete_session_info" style="display:none;">
                    <div class="info-item"><span class="info-label">Mentor</span><span class="info-value" id="delete_mentor">—</span></div>
                    <div class="info-item"><span class="info-label">Student</span><span class="info-value" id="delete_student">—</span></div>
                    <div class="info-item"><span class="info-label">Course</span><span class="info-value" id="delete_course">—</span></div>
                    <div class="info-item"><span class="info-label">Date</span><span class="info-value" id="delete_date">—</span></div>
                    <div class="info-item"><span class="info-label">Timing</span><span class="info-value" id="delete_time">—</span></div>
                    <div class="info-item"><span class="info-label">Attendance</span><span class="info-value" id="delete_attendance">—</span></div>
                </div>
                <input type="hidden" id="delete_session_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="is-modal-btn is-modal-btn-cancel" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" class="is-modal-btn is-modal-btn-danger" id="confirmDeleteBtn" onclick="deleteSession()">
                    <i class="fa fa-trash-o"></i> Delete Session
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade is-modal" id="feedbackModal" tabindex="-1" role="dialog" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 id="feedbackModalLabel"><i class="fa fa-comments-o"></i> Session Feedback</h5>
                    <div class="hdr-sub">Session ID: <span id="pmsf_pms_id">—</span></div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="feedbackModalBody">

                <div class="is-fb-loading" id="feedbackLoading" style="display:none;">
                    <i class="fa fa-spinner fa-spin"></i>
                    Fetching feedback…
                </div>

                <div id="feedbackContent">
                    <div class="is-fb-section fb-mentor">
                        <div class="is-fb-head">
                            <div class="is-fb-title">
                                <span class="av mentor"><i class="fa fa-user-md"></i></span>
                                Mentor Feedback
                            </div>
                            <button type="button" class="is-approve-btn" id="saveApprovalsBtn">
                                <i class="fa fa-check"></i> Save Decisions
                            </button>
                        </div>
                        <div class="is-fb-grid">
                            <div class="is-fb-item">
                                <span class="is-fb-label">Required Sessions</span>
                                <span class="is-fb-value"><span class="is-fb-pill"><i class="fa fa-hashtag"></i> <span id="mentor_hours">N/A</span></span></span>
                            </div>
                            <div class="is-fb-item">
                                <span class="is-fb-label">Submitted At</span>
                                <span class="is-fb-value" id="mentor_entry_time">N/A</span>
                            </div>
                            <div class="is-fb-item full">
                                <span class="is-fb-label">Remarks</span>
                                <span class="is-fb-value" id="mentor_remarks">No remarks provided.</span>
                            </div>
                            <div class="is-fb-item full">
                                <span class="is-fb-label">Proposed Sessions</span>
                                <div class="is-proposed-wrap">
                                    <div class="is-proposed-head">
                                        <span><i class="fa fa-list"></i> Approve or decline each proposed slot</span>
                                        <span id="proposedSummary"></span>
                                    </div>
                                    <div class="is-proposed-list" id="proposedSessionsList">
                                        <div class="is-empty-proposed">No proposed sessions.</div>
                                    </div>
                                    <div class="is-bulk-bar" id="proposedBulkBar" style="display:none;">
                                        <span style="font-size:11.5px;color:#64748b;">Quick action:</span>
                                        <div class="is-bulk-actions">
                                            <button type="button" class="is-decision-btn is-on-approve" onclick="bulkSetDecision('approved')">
                                                <i class="fa fa-check"></i> Approve all
                                            </button>
                                            <button type="button" class="is-decision-btn is-on-decline" onclick="bulkSetDecision('declined')">
                                                <i class="fa fa-times"></i> Decline all
                                            </button>
                                            <button type="button" class="is-decision-btn is-on-pending" onclick="bulkSetDecision('pending')">
                                                <i class="fa fa-undo"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div id="approvalsMsg" class="mt-2" style="font-size:12.5px;font-weight:600;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="is-fb-section fb-student">
                        <div class="is-fb-head">
                            <div class="is-fb-title">
                                <span class="av student"><i class="fa fa-user"></i></span>
                                Student Feedback
                            </div>
                        </div>
                        <div class="is-fb-grid">
                            <div class="is-fb-item">
                                <span class="is-fb-label">Satisfaction</span>
                                <span class="is-fb-value">
                                    <span class="is-fb-stars" id="student_satisfaction_stars"></span>
                                    <span id="student_satisfaction" class="ml-1"></span>
                                </span>
                            </div>
                            <div class="is-fb-item">
                                <span class="is-fb-label">Submitted At</span>
                                <span class="is-fb-value" id="student_entry_time">N/A</span>
                            </div>
                            <div class="is-fb-item full">
                                <span class="is-fb-label">Remarks</span>
                                <span class="is-fb-value" id="student_remarks">No remarks provided.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="is-modal-btn is-modal-btn-cancel" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mentorModal" tabindex="-1" role="dialog" aria-labelledby="mentorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mentorModalLabel">Mark as Mentored</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="accordion" id="weekdaysAccordion">
                    <form id="pmTimetable" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="weeks" class="form-label">Number of Weeks</label>
                            <input type="hidden" id="dayname" name="dayname">
                            <input type="hidden" id="fromtime" name="fromtime">
                            <input type="hidden" id="totime" name="totime">
                            <input type="hidden" id="courseID" name="courseID">
                            <input type="number" name="weeks" id="weeks" class="form-control" placeholder="Number of Weeks">
                        </div>
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <input type="text" name="remarks" id="remarks" class="form-control" placeholder="Remarks">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" onclick="saveChangesButton()">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade is-modal" id="weeklyScheduleModal" tabindex="-1" role="dialog" aria-labelledby="weeklyScheduleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <form id="weeklyScheduleForm" action="{{ route('schedule-submit') }}" method="POST">
      @csrf
      <input type="hidden" name="mentor_id" id="modal_mentor_id">
      <input type="hidden" name="student_id" id="modal_student_id">
      <input type="hidden" name="course_id" id="modal_course_id">
      <div class="modal-content">
        <div class="modal-header success">
          <div>
            <h5 id="weeklyScheduleModalLabel"><i class="fa fa-calendar-plus-o"></i> Create Schedule</h5>
            <div class="hdr-sub">Add one or more session dates for this mentor &amp; student</div>
          </div>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="is-info-card">
            <div class="info-item"><span class="info-label">Mentor</span><span class="info-value" id="mentor_id_display">—</span></div>
            <div class="info-item"><span class="info-label">Student</span><span class="info-value" id="student_id_display">—</span></div>
            <div class="info-item"><span class="info-label">Course</span><span class="info-value" id="course_name_display">—</span></div>
          </div>

          <div class="is-schedule-wrap">
            <table class="is-schedule-table" id="scheduleTable">
              <thead>
                <tr>
                  <th style="width:30%;">Date</th>
                  <th style="width:28%;">From Time</th>
                  <th style="width:28%;">To Time</th>
                  <th style="width:14%; text-align:center;">Action</th>
                </tr>
              </thead>
              <tbody id="scheduleBody">
                <tr>
                  <td><input type="date" name="sessions[0][date]" class="is-form-control" required></td>
                  <td>
                    <select name="sessions[0][from_time]" class="is-form-control" required>
                      <option value="">Select From Time</option>
                      @foreach ($ressqlTimeslots as $row)
                        <option value="{{ $row->ts_name }}">{{ $row->ts_name }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td>
                    <select name="sessions[0][to_time]" class="is-form-control" required>
                      <option value="">Select To Time</option>
                      @foreach ($ressqlTimeslots as $row)
                        <option value="{{ $row->ts_name }}">{{ $row->ts_name }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td><button type="button" class="is-icon-btn" onclick="removeRow(this)" title="Remove row"><i class="fa fa-trash-o"></i></button></td>
                </tr>
              </tbody>
            </table>
          </div>

          <button type="button" class="is-add-row-btn" onclick="addRow()">
            <i class="fa fa-plus"></i> Add another date
          </button>
        </div>
        <div class="modal-footer">
          <button type="button" class="is-modal-btn is-modal-btn-cancel" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
          </button>
          <button type="submit" class="is-modal-btn is-modal-btn-success">
            <i class="fa fa-check"></i> Submit Schedule
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade is-modal" id="reschedual" tabindex="-1" role="dialog" aria-labelledby="reschedualLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header warning">
                <div>
                    <h5 id="reschedualLabel"><i class="fa fa-refresh"></i> Re-Schedule Session</h5>
                    <div class="hdr-sub">Move this session to a new date</div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="is-info-card cols-2" id="reschedule_session_info" style="display:none;">
                    <div class="info-item"><span class="info-label">Mentor</span><span class="info-value" id="resched_mentor">—</span></div>
                    <div class="info-item"><span class="info-label">Student</span><span class="info-value" id="resched_student">—</span></div>
                    <div class="info-item"><span class="info-label">Course</span><span class="info-value" id="resched_course">—</span></div>
                    <div class="info-item"><span class="info-label">Current Date / Time</span><span class="info-value" id="resched_current">—</span></div>
                </div>
                <form id="reschedualTimetable" method="POST">
                    @csrf
                    <input type="hidden" name="rowID" id="rowID">
                    <div class="is-form-grid cols-2">
                        <div class="is-form-field">
                            <label class="is-form-label" for="resched_date"><i class="fa fa-calendar mr-1"></i> New Date</label>
                            <input type="date" name="date" id="resched_date" class="is-form-control" required>
                        </div>
                        <div class="is-form-field">
                            <label class="is-form-label" for="resched_remarks"><i class="fa fa-pencil mr-1"></i> Remarks</label>
                            <input type="text" name="remarks" id="resched_remarks" class="is-form-control" placeholder="Reason for rescheduling…">
                        </div>
                        <div class="is-form-field">
                            <label class="is-form-label" for="resched_from_time"><i class="fa fa-clock-o mr-1"></i> From Time</label>
                            <select name="from_time" id="resched_from_time" class="is-form-control">
                                <option value="">Keep current</option>
                                @foreach ($ressqlTimeslots as $row)
                                    <option value="{{ $row->ts_name }}">{{ $row->ts_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="is-form-field">
                            <label class="is-form-label" for="resched_to_time"><i class="fa fa-clock-o mr-1"></i> To Time</label>
                            <select name="to_time" id="resched_to_time" class="is-form-control">
                                <option value="">Keep current</option>
                                @foreach ($ressqlTimeslots as $row)
                                    <option value="{{ $row->ts_name }}">{{ $row->ts_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
                <div id="successMsg" class="mt-2" style="font-size:12.5px; font-weight:600;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="is-modal-btn is-modal-btn-cancel" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" class="is-modal-btn is-modal-btn-success" onclick="saveChangesReSchedualButton()">
                    <i class="fa fa-check"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
var defaultSessionId = @json($currentSessionId);
var defaultSchoolId = @json($schoolId);
var defaultSessionApplied = false;
var defaultSchoolApplied = false;
var isSyncingSession = false;
var isSyncingSchool = false;

function setDefaultSession() {
    if (defaultSessionApplied) return;
    defaultSessionApplied = true;
    if (!defaultSessionId) return;
    isSyncingSession = true;
    try { $('#filter_sess_id').combobox('setValue', defaultSessionId); } catch (e) {}
    setTimeout(function () { isSyncingSession = false; }, 0);
}

function setDefaultSchool() {
    if (defaultSchoolApplied) return;
    defaultSchoolApplied = true;
    if (!defaultSchoolId) return;
    isSyncingSchool = true;
    try { $('#filter_school_id').combobox('setValue', defaultSchoolId); } catch (e) {}
    setTimeout(function () { isSyncingSchool = false; }, 0);
}

function applyFilters() {
    var sess = $('#sess_id').val() || defaultSessionId || '';
    var school = $('#school_id').val() || '';
    var att = $('#attendance_filter').val() || 'All';
    var params = [];
    if (sess) params.push('sess_id=' + encodeURIComponent(sess));
    if (school) params.push('school_id=' + encodeURIComponent(school));
    if (att && att !== 'All') params.push('attendance=' + encodeURIComponent(att));
    window.location.href = '{{ url('/intro-session') }}' + (params.length ? ('?' + params.join('&')) : '');
}

function resetIntroFilters() {
    var icon = document.getElementById('is-reset-icon');
    if (icon) {
        icon.style.transition = 'transform 0.6s ease';
        icon.style.transform = 'rotate(360deg)';
        setTimeout(function () { icon.style.transition = 'none'; icon.style.transform = 'rotate(0deg)'; setTimeout(function(){ icon.style.transition = 'transform 0.6s ease'; }, 50); }, 650);
    }
    $('#school_id').val('');
    $('#attendance_filter').val('All');
    window.location.href = '{{ url('/intro-session') }}';
}

$(document).on('click', '.is-stat', function () {
    var status = $(this).data('status') || 'All';
    $('#attendance_filter').val(status);
    applyFilters();
});

function applySessionSearch() {
    var q = ($('#sessionSearchInput').val() || '').trim().toLowerCase();
    var $rows = $('.is-group-row');
    var visible = 0;

    if (!q) {
        $rows.show();
        $('#sessionSearchWrap').removeClass('has-value');
        $('#sessionSearchEmpty').hide();
        return;
    }

    $('#sessionSearchWrap').addClass('has-value');
    $rows.each(function () {
        var hay = ($(this).attr('data-search') || '').toLowerCase();
        if (hay.indexOf(q) !== -1) {
            $(this).show();
            visible++;
        } else {
            $(this).hide();
        }
    });

    $('#sessionSearchEmpty').toggle(visible === 0);
}

$(document).on('input', '#sessionSearchInput', applySessionSearch);
$(document).on('click', '#sessionSearchClear', function () {
    $('#sessionSearchInput').val('');
    applySessionSearch();
    $('#sessionSearchInput').focus();
});
$(document).on('keydown', '#sessionSearchInput', function (e) {
    if (e.key === 'Escape') {
        $('#sessionSearchInput').val('');
        applySessionSearch();
    }
});

function confirmDelete(btnOrId) {
    var id, mentor, student, course, date, time, attendance, hasContext = false;
    if (typeof btnOrId === 'object' && btnOrId !== null) {
        id = btnOrId.getAttribute('data-session_id');
        mentor = btnOrId.getAttribute('data-mentor') || '—';
        student = btnOrId.getAttribute('data-student') || '—';
        course = btnOrId.getAttribute('data-course') || '—';
        date = btnOrId.getAttribute('data-date') || '—';
        time = btnOrId.getAttribute('data-time') || '—';
        attendance = btnOrId.getAttribute('data-attendance') || '—';
        hasContext = true;
    } else {
        id = btnOrId;
    }

    $('#delete_session_id').val(id);
    if (hasContext) {
        $('#delete_mentor').text(mentor);
        $('#delete_student').text(student);
        $('#delete_course').text(course);
        $('#delete_date').text(date);
        $('#delete_time').text(time);
        $('#delete_attendance').text(attendance);
        $('#delete_session_info').show();
    } else {
        $('#delete_session_info').hide();
    }
    $('#deleteSessionModal').modal('show');
}

function deleteSession() {
    var id = $('#delete_session_id').val();
    var $btn = $('#confirmDeleteBtn');
    var original = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting…');

    $.ajax({
        url: '/pm-delete-session/' + id,
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function (response) {
            if (response.success) {
                $('#deleteSessionModal').modal('hide');
                location.reload();
            } else {
                alert(response.message || 'Failed to delete session.');
                $btn.prop('disabled', false).html(original);
            }
        },
        error: function () {
            alert('Error deleting session.');
            $btn.prop('disabled', false).html(original);
        }
    });
}

function openWeeklyScheduleModal(mentorId, studentId, courseId, courseName) {
    document.getElementById('modal_mentor_id').value = mentorId;
    document.getElementById('modal_student_id').value = studentId;
    document.getElementById('modal_course_id').value = courseId;
    document.getElementById('mentor_id_display').textContent = mentorId;
    document.getElementById('student_id_display').textContent = studentId;
    document.getElementById('course_name_display').textContent = courseName || '—';
    $('#successMsg').text('');
    $('#weeklyScheduleModal').modal('show');
}

function reschedual(btnOrId) {
    var id, mentor, student, course, date, time, rawDate, fromTime, toTime, hasContext = false;
    if (typeof btnOrId === 'object' && btnOrId !== null) {
        id = btnOrId.getAttribute('data-session_id');
        mentor = btnOrId.getAttribute('data-mentor') || '—';
        student = btnOrId.getAttribute('data-student') || '—';
        course = btnOrId.getAttribute('data-course') || '—';
        date = btnOrId.getAttribute('data-date') || '—';
        time = btnOrId.getAttribute('data-time') || '—';
        rawDate = btnOrId.getAttribute('data-raw-date') || '';
        fromTime = btnOrId.getAttribute('data-from-time') || '';
        toTime = btnOrId.getAttribute('data-to-time') || '';
        hasContext = true;
    } else {
        id = btnOrId;
        rawDate = '';
        fromTime = '';
        toTime = '';
    }

    $('#rowID').val(id);
    $('#resched_date').val(rawDate);
    $('#resched_remarks').val('');
    $('#successMsg').text('');

    var $from = $('#resched_from_time');
    var $to = $('#resched_to_time');
    $from.val('');
    $to.val('');
    if (fromTime && $from.find('option[value="' + fromTime + '"]').length) {
        $from.val(fromTime);
    }
    if (toTime && $to.find('option[value="' + toTime + '"]').length) {
        $to.val(toTime);
    }

    if (hasContext) {
        $('#resched_mentor').text(mentor);
        $('#resched_student').text(student);
        $('#resched_course').text(course);
        $('#resched_current').text(date + ' • ' + time);
        $('#reschedule_session_info').show();
    } else {
        $('#reschedule_session_info').hide();
    }
    $('#reschedual').modal('show');
}

$(document).ready(function () {
    $('#saveApprovalsBtn').on('click', saveFeedbackApprovals);

    document.querySelectorAll('.btn-feedback').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var sessionId = this.dataset.session_id;
            openFeedbackModal(sessionId);
        });
    });
});

var currentFeedbackSessionId = null;
var currentProposedDecisions = {};

function escapeHtml(value) {
    return String(value == null ? '' : value)
        .replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;')
        .replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function formatProposedDate(raw) {
    if (!raw) return '—';
    var d = new Date(raw);
    if (isNaN(d.getTime())) return raw;
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var weekdays = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    return {
        d: ('0' + d.getDate()).slice(-2) + ' ' + months[d.getMonth()] + ' ' + d.getFullYear(),
        wd: weekdays[d.getDay()]
    };
}

function renderProposedSessions(proposedSessions) {
    var $list = $('#proposedSessionsList');
    var $bulk = $('#proposedBulkBar');
    var $summary = $('#proposedSummary');
    currentProposedDecisions = {};

    if (!proposedSessions || !proposedSessions.length) {
        $list.html('<div class="is-empty-proposed">No proposed sessions submitted by mentor yet.</div>');
        $bulk.hide();
        $summary.text('');
        return;
    }

    var pendingCount = 0, approvedCount = 0, declinedCount = 0;
    var html = '';

    proposedSessions.forEach(function (ps) {
        var status = (ps.approval_status || 'pending').toLowerCase();
        currentProposedDecisions[ps.feedback_id] = status;
        if (status === 'approved') approvedCount++;
        else if (status === 'declined') declinedCount++;
        else pendingCount++;

        var dateObj = formatProposedDate(ps.date);
        var rowClass = 'is-' + status;
        var pillClass = status;

        html += '<div class="is-proposed-row ' + rowClass + '" data-fb-id="' + escapeHtml(ps.feedback_id) + '">' +
            '<span class="is-date">' +
                '<span class="d">' + escapeHtml(dateObj.d || ps.date) + '</span>' +
                (dateObj.wd ? '<span class="wd">' + escapeHtml(dateObj.wd) + '</span>' : '') +
            '</span>' +
            '<span class="is-time-chip"><i class="fa fa-clock-o"></i> ' +
                escapeHtml(ps.from_time || '—') + ' &mdash; ' + escapeHtml(ps.to_time || '—') +
            '</span>' +
            '<span class="is-status-pill ' + pillClass + '">' + escapeHtml(status) + '</span>' +
            '<div style="display:flex;justify-content:flex-end;">' +
                '<div class="is-decision-group">' +
                    '<button type="button" class="is-decision-btn ' + (status === 'approved' ? 'is-on-approve' : '') + '" onclick="setDecision(' + ps.feedback_id + ', \'approved\')"><i class="fa fa-check"></i> Approve</button>' +
                    '<button type="button" class="is-decision-btn ' + (status === 'declined' ? 'is-on-decline' : '') + '" onclick="setDecision(' + ps.feedback_id + ', \'declined\')"><i class="fa fa-times"></i> Decline</button>' +
                    '<button type="button" class="is-decision-btn ' + (status === 'pending' ? 'is-on-pending' : '') + '" onclick="setDecision(' + ps.feedback_id + ', \'pending\')"><i class="fa fa-clock-o"></i> Pending</button>' +
                '</div>' +
            '</div>' +
        '</div>';
    });

    $list.html(html);
    $bulk.toggle(proposedSessions.length > 1);
    $summary.html(
        '<span class="is-status-pill pending">Pending ' + pendingCount + '</span> ' +
        '<span class="is-status-pill approved">Approved ' + approvedCount + '</span> ' +
        '<span class="is-status-pill declined">Declined ' + declinedCount + '</span>'
    );
}

function setDecision(feedbackId, status) {
    currentProposedDecisions[feedbackId] = status;
    var $row = $('.is-proposed-row[data-fb-id="' + feedbackId + '"]');
    $row.removeClass('is-pending is-approved is-declined').addClass('is-' + status);
    $row.find('.is-status-pill').removeClass('pending approved declined').addClass(status).text(status);
    $row.find('.is-decision-btn').removeClass('is-on-approve is-on-decline is-on-pending');
    if (status === 'approved') $row.find('.is-decision-btn').eq(0).addClass('is-on-approve');
    else if (status === 'declined') $row.find('.is-decision-btn').eq(1).addClass('is-on-decline');
    else $row.find('.is-decision-btn').eq(2).addClass('is-on-pending');
    refreshProposedSummary();
}

function bulkSetDecision(status) {
    Object.keys(currentProposedDecisions).forEach(function (fbId) {
        setDecision(parseInt(fbId, 10), status);
    });
}

function refreshProposedSummary() {
    var pendingCount = 0, approvedCount = 0, declinedCount = 0;
    Object.keys(currentProposedDecisions).forEach(function (k) {
        var s = currentProposedDecisions[k];
        if (s === 'approved') approvedCount++;
        else if (s === 'declined') declinedCount++;
        else pendingCount++;
    });
    $('#proposedSummary').html(
        '<span class="is-status-pill pending">Pending ' + pendingCount + '</span> ' +
        '<span class="is-status-pill approved">Approved ' + approvedCount + '</span> ' +
        '<span class="is-status-pill declined">Declined ' + declinedCount + '</span>'
    );
}

function saveFeedbackApprovals() {
    if (!currentFeedbackSessionId) return;
    var decisions = Object.keys(currentProposedDecisions).map(function (fbId) {
        return { feedback_id: parseInt(fbId, 10), status: currentProposedDecisions[fbId] };
    });
    if (!decisions.length) {
        $('#approvalsMsg').text('No proposed sessions to save.').css('color', '#b91c1c');
        return;
    }
    var hasNonPending = decisions.some(function (d) { return d.status === 'approved' || d.status === 'declined'; });
    if (!hasNonPending) {
        $('#approvalsMsg').text('Please mark at least one slot as Approved or Declined.').css('color', '#b91c1c');
        return;
    }

    var $btn = $('#saveApprovalsBtn');
    var originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving…');
    $('#approvalsMsg').text('');

    $.ajax({
        url: '{{ url('/feedback/save-approvals') }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            session_id: currentFeedbackSessionId,
            decisions: decisions
        },
        success: function (res) {
            if (res.success) {
                $('#approvalsMsg').html('<i class="fa fa-check-circle"></i> ' + (res.message || 'Saved.')).css('color', '#16a34a');
                setTimeout(function () {
                    $('#feedbackModal').modal('hide');
                    location.reload();
                }, 1200);
            } else {
                $('#approvalsMsg').text(res.message || 'Save failed.').css('color', '#b91c1c');
                $btn.prop('disabled', false).html(originalHtml);
            }
        },
        error: function (xhr) {
            var msg = 'Save failed. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            $('#approvalsMsg').text(msg).css('color', '#b91c1c');
            $btn.prop('disabled', false).html(originalHtml);
        }
    });
}

function renderSatisfactionStars(value) {
    var raw = (value || '').toString().trim();
    if (!raw) return { stars: '', text: '' };
    var num = parseInt(raw, 10);
    if (!isNaN(num) && num >= 0 && num <= 5) {
        var filled = '';
        for (var i = 0; i < num; i++) filled += '<i class="fa fa-star"></i>';
        var empty = '';
        for (var j = 0; j < (5 - num); j++) empty += '<i class="fa fa-star-o empty"></i>';
        return { stars: filled + empty, text: num + ' / 5' };
    }
    return { stars: '', text: raw };
}

function setFbValue(id, value, emptyText) {
    var el = document.getElementById(id);
    if (!el) return;
    var v = (value == null || value === '') ? null : String(value);
    if (v) {
        el.textContent = v;
        el.classList.remove('is-empty-val');
    } else {
        el.textContent = emptyText || 'N/A';
        el.classList.add('is-empty-val');
    }
}

function openFeedbackModal(sessionId) {
    currentFeedbackSessionId = sessionId;
    currentProposedDecisions = {};
    document.getElementById('pmsf_pms_id').textContent = sessionId || '—';
    document.getElementById('feedbackContent').style.display = 'none';
    document.getElementById('feedbackLoading').style.display = 'block';
    $('#approvalsMsg').text('');
    $('#proposedSessionsList').html('<div class="is-empty-proposed">Loading…</div>');
    $('#proposedSummary').text('');
    $('#proposedBulkBar').hide();
    $('#feedbackModal').modal('show');

    $.ajax({
        url: '/feedback/' + sessionId,
        method: 'GET',
        success: function (response) {
            var mentor = response.mentor || {};
            var student = response.student || {};
            var proposed = response.proposed_sessions || [];

            setFbValue('mentor_remarks', mentor.remarks, 'No remarks provided.');
            document.getElementById('mentor_hours').textContent = mentor.hours_required || (proposed.length || 'N/A');
            setFbValue('mentor_entry_time', mentor.entry_datetime, 'N/A');

            renderProposedSessions(proposed);

            setFbValue('student_remarks', student.remarks, 'No remarks provided.');
            setFbValue('student_entry_time', student.entry_datetime, 'N/A');

            var sat = renderSatisfactionStars(student.satisfaction);
            document.getElementById('student_satisfaction_stars').innerHTML = sat.stars;
            var satText = document.getElementById('student_satisfaction');
            if (sat.text) {
                satText.textContent = sat.text;
                satText.classList.remove('is-empty-val');
            } else {
                satText.textContent = 'Not provided';
                satText.classList.add('is-empty-val');
            }

            document.getElementById('feedbackLoading').style.display = 'none';
            document.getElementById('feedbackContent').style.display = 'block';
        },
        error: function () {
            document.getElementById('feedbackLoading').style.display = 'none';
            document.getElementById('feedbackContent').style.display = 'block';
            alert('Failed to fetch feedback data. Please try again.');
        }
    });
}

function saveChangesReSchedualButton() {
    var newDate = $('#resched_date').val();
    if (!newDate) {
        $('#successMsg').text('Please choose a new date.').css('color', '#b91c1c');
        return;
    }
    var fromT = $('#resched_from_time').val();
    var toT = $('#resched_to_time').val();
    if ((fromT && !toT) || (!fromT && toT)) {
        $('#successMsg').text('Please choose both From and To time, or leave both empty to keep current.').css('color', '#b91c1c');
        return;
    }
    var formData = new FormData(document.getElementById('reschedualTimetable'));
    $.ajax({
        url: '/re-timetable', type: 'POST', data: formData,
        processData: false, contentType: false,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function (response) {
            if (response) {
                $('#successMsg').html('<i class="fa fa-check-circle"></i> Rescheduled successfully!').css('color', '#16a34a');
                setTimeout(function () { $('#reschedual').modal('hide'); location.reload(); }, 1200);
            } else {
                $('#successMsg').text('There was an error saving the changes.').css('color', '#b91c1c');
            }
        },
        error: function () {
            $('#successMsg').text('An error occurred while saving.').css('color', '#b91c1c');
        }
    });
}

function saveChangesButton() {
    var formData = new FormData(document.getElementById('pmTimetable'));
    $.ajax({
        url: '/schedual-sessions', type: 'POST', data: formData,
        processData: false, contentType: false,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function (response) {
            if (response.success) { alert('Changes saved successfully!'); $('#mentorModal').modal('hide'); }
            else { alert('There was an error saving the changes.'); }
        },
        error: function () { alert('An error occurred while saving.'); }
    });
}

var rowCount = 1;
function addRow() {
    var tableBody = document.getElementById("scheduleBody");
    var newRow = document.createElement("tr");
    newRow.innerHTML =
        '<td><input type="date" name="sessions[' + rowCount + '][date]" class="is-form-control" required></td>' +
        '<td><select name="sessions[' + rowCount + '][from_time]" class="is-form-control" required>' +
        '<option value="">Select From Time</option>' +
        @foreach ($ressqlTimeslots as $row)
        '<option value="{{ $row->ts_name }}">{{ $row->ts_name }}</option>' +
        @endforeach
        '</select></td>' +
        '<td><select name="sessions[' + rowCount + '][to_time]" class="is-form-control" required>' +
        '<option value="">Select To Time</option>' +
        @foreach ($ressqlTimeslots as $row)
        '<option value="{{ $row->ts_name }}">{{ $row->ts_name }}</option>' +
        @endforeach
        '</select></td>' +
        '<td><button type="button" class="is-icon-btn" onclick="removeRow(this)" title="Remove row"><i class="fa fa-trash-o"></i></button></td>';
    tableBody.appendChild(newRow);
    rowCount++;
}

function removeRow(button) {
    var tbody = document.getElementById("scheduleBody");
    if (tbody && tbody.children.length <= 1) {
        alert('At least one schedule row is required.');
        return;
    }
    button.closest("tr").remove();
}
</script>

@endsection
