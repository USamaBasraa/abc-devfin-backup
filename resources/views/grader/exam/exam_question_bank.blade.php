<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
// print_r($sch_id);
// exit;  
$tile='';
$exam_adm='';  

// dd("122");
?>

@extends('layouts.app',['pageTitle' => 'Exam'])
{{-- @dd("123"); --}}
@section('content')

<?php
$co_id='';
$name='';
$course_id='';
$fc_co_id='';
$fc_course_id='';
if($exam_bank){
   foreach($exam_bank as $key_rec)
    $co_id=$key_rec->co_id;
    $course_id=$key_rec->fc_co_course_id;
    $fc_co_id=my_simple_crypt($co_id, 'e' );
    $fc_course_id=my_simple_crypt($course_id, 'e' );
    }else{

    $co_id='';
    $course_id='';
    $fc_co_id='';
    }

              foreach ($exam_bank as $key)
              {
                $tile=$key->title;
              }
              $name = $tile;

              if(isset($exam_adm2)){
                $name = $exam_adm2[0]->admission_sch_name;
              }

?>
<style>
/* ── Color palette ───────────────────────────────────── */
:root {
    /* Primary – deep ocean blue */
    --eqb-primary:        #1a4f7a;
    --eqb-primary-mid:    #2563a8;
    --eqb-primary-light:  #3b82f6;
    --eqb-primary-bg:     #eff6ff;
    --eqb-primary-border: #bfdbfe;
    --eqb-primary-soft:   #dbeafe;

    /* Legacy aliases */
    --eqb-blue:       var(--eqb-primary-mid);
    --eqb-blue-dark:  var(--eqb-primary);
    --eqb-blue-light: var(--eqb-primary-bg);

    /* Semantic */
    --eqb-success:        #047857;
    --eqb-success-light:  #10b981;
    --eqb-success-bg:     #ecfdf5;
    --eqb-success-border: #6ee7b7;
    --eqb-green:          var(--eqb-success-light);

    --eqb-danger:         #b91c1c;
    --eqb-danger-light:   #ef4444;
    --eqb-danger-bg:      #fef2f2;
    --eqb-danger-border:  #fca5a5;
    --eqb-red:            var(--eqb-danger-light);

    --eqb-warning:        #b45309;
    --eqb-warning-bg:     #fffbeb;
    --eqb-warning-border: #fcd34d;
    --eqb-warning-text:   #92400e;

    --eqb-info:           #0369a1;
    --eqb-info-bg:        #f0f9ff;

    /* Neutrals */
    --eqb-surface:        #ffffff;
    --eqb-surface-2:      #f8fafc;
    --eqb-surface-3:      #f1f5f9;
    --eqb-bg:             var(--eqb-surface-2);
    --eqb-border:         #e2e8f0;
    --eqb-border-input:   #cbd5e1;
    --eqb-text:           #1e293b;
    --eqb-text-secondary: #475569;
    --eqb-muted:          #94a3b8;

    /* Flat colors (no gradients) */
    --eqb-gradient-header:  var(--eqb-primary-mid);
    --eqb-gradient-table:   var(--eqb-primary-mid);
    --eqb-gradient-success: var(--eqb-success-light);

    --eqb-radius:     10px;
    --eqb-radius-sm:  6px;
    --eqb-shadow:     0 4px 20px rgba(26, 79, 122, 0.12);
    --eqb-shadow-sm:  0 1px 4px rgba(26, 79, 122, 0.08);
    --eqb-shadow-lg:  0 8px 32px rgba(26, 79, 122, 0.18);
}

/* ── Layout utilities ────────────────────────────────── */
.eqb-card {
    border: none;
    border-radius: var(--eqb-radius);
    box-shadow: var(--eqb-shadow);
    background: var(--eqb-surface);
    overflow: hidden;
}

.eqb-card-header {
    /* background: var(--eqb-primary-mid) !important; */
    /* color: #fff !important; */
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    padding: 14px 20px;
    border-radius: var(--eqb-radius) var(--eqb-radius) 0 0;
    /* border-bottom: 2px solid var(--eqb-primary); */
}

.eqb-card-body { padding: 20px; }
.eqb-card-body--compact { padding: 16px; }

.eqb-header-btn {
    background: rgba(255,255,255,.15) !important;
    color: #fff !important;
    border: 1px solid rgba(255,255,255,.35) !important;
    border-radius: var(--eqb-radius-sm);
    font-weight: 600;
    transition: background .15s;
}
.eqb-header-btn:hover {
    background: rgba(255,255,255,.28) !important;
    color: #fff !important;
}

.eqb-header-btn--solid {
    /* background: #fff !important; */
    color: var(--eqb-primary) !important;
    border: none !important;
    border-radius: var(--eqb-radius-sm);
    font-weight: 400;
    text-decoration: underline;
    color: #252525 !important;
    font-size: 18px;
}
.eqb-header-btn--solid:hover {
    /* background: var(--eqb-primary-bg) !important; */
    /* color: var(--eqb-primary) !important; */
    text-decoration: underline !important;
}

.eqb-badge-pill {
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.35);
    border-radius: 20px;
    padding: 4px 14px;
    font-size: 13px;
    font-weight: 700;
    color: #fff;
}

.eqb-warning-badge {
    background: var(--eqb-warning-bg);
    border: 1px solid var(--eqb-warning-border);
    border-radius: var(--eqb-radius-sm);
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 600;
    color: var(--eqb-warning-text);
}

.eqb-form-section-title {
    margin: 0;
    font-size: 13.5px;
    font-weight: 700;
    color: var(--eqb-primary);
    letter-spacing: .3px;
}

.eqb-form-divider {
    padding: 10px 0 14px;
    border-bottom: 1.5px solid var(--eqb-border);
    margin-bottom: 16px;
}

.eqb-table-header-row {
    background: #1F776E !important;
    color: #fff !important;
}

.eqb-table-header-row th,
.eqb-table-header-row td {
    color: #fff !important;
    border: none !important;
    background: var(--eqb-primary-mid) !important;
}

.eqb-table-header-row:hover,
.eqb-table-header-row:hover th,
.eqb-table-header-row:hover td {
    background: var(--eqb-primary-mid) !important;
    color: #fff !important;
}

.eqb-btn-submit {
    height: 42px;
    font-size: 14px;
    font-weight: 700;
    border-radius: 7px;
    letter-spacing: .2px;
    background: var(--eqb-primary-mid) !important;
    border: none !important;
    color: #fff !important;
    box-shadow: 0 2px 6px rgba(37, 99, 168, .25);
    transition: opacity .15s, transform .1s;
}
.eqb-btn-submit:hover {
    opacity: .92;
    color: #fff !important;
    transform: translateY(-1px);
}

.eqb-btn-reset {
    height: 42px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 7px;
    color: var(--eqb-text-secondary) !important;
    border-color: var(--eqb-border-input) !important;
    background: var(--eqb-surface) !important;
}
.eqb-btn-reset:hover {
    background: var(--eqb-surface-3) !important;
    color: var(--eqb-text) !important;
}

.eqb-alert-success {
    margin: 8px 0;
    padding: 10px 14px;
    background: var(--eqb-success-bg);
    border: 1px solid var(--eqb-success-border);
    border-radius: var(--eqb-radius-sm);
    color: var(--eqb-success);
    font-size: 13px;
    font-weight: 500;
}

.eqb-row-in-exam td:first-child,
.eqb-row-in-exam th:first-child {
    box-shadow: inset 3px 0 0 var(--eqb-success-light);
}

#simpletable .eqb-row-in-exam td,
#simpletable .eqb-row-in-exam th {
    background: #f0fdf4 !important;
}

#simpletable .eqb-row-in-exam:hover td,
#simpletable .eqb-row-in-exam:hover th {
    background: #ecfdf5 !important;
}

#simpletable .eqb-row-in-exam .eqb-question-content {
    background: #f0fdf4;
    border-color: var(--eqb-success-border);
}

.eqb-cqb-added-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: var(--eqb-success-bg);
    color: var(--eqb-success);
    border: 1px solid var(--eqb-success-border);
    border-radius: 20px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

#simpletable .eqb-paper-block {
    background: #fff;
    border: 1px solid var(--eqb-border);
    border-radius: 8px;
    margin-bottom: 16px;
    overflow: hidden;
}

#simpletable .eqb-paper-block-title {
    background: var(--eqb-surface-3);
    color: var(--eqb-primary);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .4px;
    text-transform: uppercase;
    padding: 10px 14px;
    border-bottom: 1px solid var(--eqb-border);
}

#simpletable table.eqb-cqb-table {
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
    margin-bottom: 0;
    background: #fff;
}

#simpletable table.eqb-cqb-table .eqb-table-header-row th,
#simpletable table.eqb-cqb-table .eqb-table-header-row td {
    background: #1F776E !important;
    color: #fff !important;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .4px;
    text-transform: uppercase;
    padding: 11px 10px !important;
    border: none !important;
    text-align: center;
}

#simpletable table.eqb-cqb-table .eqb-table-header-row:hover th,
#simpletable table.eqb-cqb-table .eqb-table-header-row:hover td {
    background: #1F776E !important;
    color: #fff !important;
}

#simpletable .eqb-cqb-empty {
    padding: 20px;
    text-align: center;
    color: var(--eqb-muted);
    font-size: 13px;
    background: #fff;
}

.eqb-icon-add    { color: var(--eqb-success) !important; cursor: pointer; }
.eqb-icon-edit   { color: var(--eqb-primary-mid) !important; cursor: pointer; }
.eqb-icon-delete { color: var(--eqb-danger-light) !important; cursor: pointer; }

.eqb-text-empty  { color: var(--eqb-danger-light); font-weight: 600; }
.eqb-text-disclaimer { color: var(--eqb-danger); }

.eqb-modal-content {
    border: none;
    border-radius: var(--eqb-radius);
    box-shadow: var(--eqb-shadow-lg);
}

.eqb-modal-header {
    background: var(--eqb-primary-mid);
    color: #fff;
    border-radius: var(--eqb-radius) var(--eqb-radius) 0 0;
    padding: 14px 20px;
    border-bottom: 2px solid var(--eqb-primary);
}

.eqb-modal-header .close {
    color: #fff;
    opacity: .85;
    text-shadow: none;
    font-size: 22px;
}
.eqb-modal-header .close:hover { opacity: 1; }

.eqb-search-wrap { position: relative; }
.eqb-search-wrap .fa-search {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--eqb-muted);
    font-size: 13px;
    pointer-events: none;
}

.eqb-search-input {
    padding-left: 34px !important;
    border: 1.5px solid var(--eqb-border-input) !important;
    border-radius: var(--eqb-radius-sm) !important;
    font-size: 13.5px !important;
    height: 40px !important;
}
.eqb-search-input:focus {
    border-color: var(--eqb-primary-light) !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, .18) !important;
}

/* Bootstrap primary override within this page */
.eqb-card .btn-primary,
.question-form-box .btn-primary {
    background: #1F776E !important;
    border-color: #1F776E !important;
}
.eqb-card .btn-primary:hover,
.question-form-box .btn-primary:hover {
    background: var(--eqb-primary);
    border-color: var(--eqb-primary);
}

.eqb-card .btn-outline-primary {
    color: var(--eqb-primary-mid);
    border-color: var(--eqb-primary-border);
}
.eqb-card .btn-outline-primary:hover {
    background: var(--eqb-primary-mid);
    border-color: var(--eqb-primary-mid);
    color: #fff;
}

.eqb-card .btn-outline-danger {
    color: var(--eqb-danger);
    border-color: var(--eqb-danger-border);
}
.eqb-card .btn-outline-danger:hover {
    background: var(--eqb-danger);
    border-color: var(--eqb-danger);
    color: #fff;
}

.cke_notification_warning { display: none !important; }

/* ── Add-Question form box ───────────────────────────── */
.question-form-box {
    background: var(--eqb-surface);
    /* padding: 20px 22px; */
    /* border-radius: var(--eqb-radius); */
    /* border: 1.5px solid var(--eqb-border); */
}

.question-form-box label {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--eqb-text-secondary);
    margin-bottom: 5px;
    letter-spacing: .2px;
}

.question-form-box .form-control {
    height: 40px;
    border: 1.5px solid var(--eqb-border-input);
    border-radius: var(--eqb-radius-sm);
    font-size: 13.5px;
    color: var(--eqb-text);
    background: var(--eqb-surface);
    box-shadow: none;
    transition: border-color .15s, box-shadow .15s;
}

.question-form-box .form-control:focus {
    border-color: var(--eqb-primary-light);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, .18);
}

/* .question-form-box textarea.form-control {
    height: auto;
    min-height: 52px;
    resize: vertical;
} */

#cke_qname {
    display: block !important;
    width: 100% !important;
    visibility: visible !important;
}

#cke_qname .cke_contents {
    min-height: 180px !important;
}

textarea#qname {
    min-height: 150px;
}

.question-form-box .form-control-file {
    height: 40px;
    border: 1.5px dashed var(--eqb-border-input);
    border-radius: var(--eqb-radius-sm);
    padding: 8px 10px;
    width: 100%;
    background: var(--eqb-surface-2);
    cursor: pointer;
    color: var(--eqb-text-secondary);
}

.question-form-box .btn {
    height: 40px;
    font-size: 13.5px;
    font-weight: 600;
    border-radius: var(--eqb-radius-sm);
    letter-spacing: .2px;
}

.msg-div {
    padding: 10px 14px;
    border-radius: var(--eqb-radius-sm);
    font-size: 13px;
    font-weight: 500;
    margin-top: 6px;
}

#error_msg   { background: var(--eqb-danger-bg); border: 1px solid var(--eqb-danger-border); color: var(--eqb-danger) !important; }
#success_msg { background: var(--eqb-success-bg); border: 1px solid var(--eqb-success-border); color: var(--eqb-success) !important; }
#saved_msg_div_file, #saved_msg_div_delete {
    padding: 10px 14px;
    border-radius: var(--eqb-radius-sm);
    font-size: 13px;
    font-weight: 500;
    background: var(--eqb-success-bg);
    border: 1px solid var(--eqb-success-border);
    color: var(--eqb-success);
}

.main-content {
    padding-left: 12px;
    padding-right: 12px;
    background: var(--eqb-surface-3);
}

/* ── Table container ─────────────────────────────────── */
.table-scroll {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: var(--eqb-radius);
}

#simpletabledelete {
    width: 100% !important;
    max-width: 100% !important;
    display: table !important;
    table-layout: fixed;
    border-collapse: collapse;
    background: #fff;
}

#simpletabledelete > tbody > tr > td {
    padding: 0;
    border: none !important;
    background: #fff !important;
}

#simpletabledelete table.eqb-paper-table {
    width: 100% !important;
    max-width: 100% !important;
    table-layout: fixed;
    border-collapse: collapse;
    margin-bottom: 0;
    background: #fff;
}

#simpletabledelete .eqb-paper-block {
    background: #fff;
    border: 1px solid var(--eqb-border);
    border-radius: 8px;
    margin-bottom: 16px;
    overflow: hidden;
}

#simpletabledelete .eqb-paper-block-title {
    background: var(--eqb-surface-3);
    color: var(--eqb-primary);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .4px;
    text-transform: uppercase;
    padding: 10px 14px;
    border-bottom: 1px solid var(--eqb-border);
}

#simpletabledelete .eqb-col-num    { width: 4%; }
#simpletabledelete .eqb-col-meta   { width: 8%; }
#simpletabledelete .eqb-col-section { width: 15%; }
#simpletabledelete .eqb-col-action { width: 9%; }
#simpletabledelete .eqb-col-upload { width: 8%; }

#simpletabledelete td,
#simpletabledelete th {
    word-wrap: break-word;
    overflow-wrap: anywhere;
    word-break: break-word;
    vertical-align: top;
    padding: 12px 10px !important;
    font-size: 13px;
    color: var(--eqb-text);
    border-color: #e8edf2 !important;
    background: #fff;
}

/* header row – flat color, no hover change */
#simpletabledelete .eqb-table-header-row th,
#simpletabledelete .eqb-table-header-row td,
#simpletabledelete table.eqb-paper-table tr.eqb-table-header-row th,
#simpletabledelete table.eqb-paper-table tr.eqb-table-header-row td {
    background: var(--eqb-primary-mid) !important;
    color: #fff !important;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .5px;
    text-transform: uppercase;
    padding: 11px 10px !important;
    border: none !important;
    text-align: center;
}

#simpletabledelete .eqb-table-header-row:hover th,
#simpletabledelete .eqb-table-header-row:hover td {
    background: var(--eqb-primary-mid) !important;
    color: #fff !important;
}

/* data rows – white paper */
#simpletabledelete table.eqb-paper-table tbody tr:not(.eqb-table-header-row) td,
#simpletabledelete table.eqb-paper-table tbody tr:not(.eqb-table-header-row) th {
    background: #fff !important;
}

#simpletabledelete table.eqb-paper-table tbody tr:not(.eqb-table-header-row):hover td,
#simpletabledelete table.eqb-paper-table tbody tr:not(.eqb-table-header-row):hover th {
    background: #fafbfc !important;
}

#simpletabledelete .eqb-meta-cell {
    text-align: center;
    font-size: 12.5px;
    color: var(--eqb-text-secondary);
    vertical-align: middle;
}

/* ── Section cell ────────────────────────────────────── */
#simpletabledelete th.eqb-section-col,
#simpletabledelete .eqb-section-cell {
    width: 165px;
    min-width: 165px;
    max-width: 165px;
    padding: 10px 8px !important;
}

#simpletabledelete .eqb-section-label {
    display: block;
    font-size: 9.5px;
    font-weight: 700;
    color: var(--eqb-primary-mid);
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: .5px;
}

#simpletabledelete .eqb-section-box {
    background: var(--eqb-primary-bg);
    border: 1.5px solid var(--eqb-primary-border);
    border-radius: 7px;
    padding: 9px;
}

#simpletabledelete .eqb-section-name {
    font-size: 12px;
    font-weight: 500;
    line-height: 1.5;
    color: var(--eqb-text);
    margin-bottom: 7px;
    max-height: 5.8em;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    word-break: break-word;
    overflow-wrap: anywhere;
}

#simpletabledelete .eqb-section-change-btn {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 8px;
    width: 100%;
    border-radius: 5px;
}

#simpletabledelete .eqb-saved-msg {
    font-size: 11px;
    font-style: italic;
    color: var(--eqb-success);
    text-align: center;
    font-weight: 600;
    margin-top: 5px;
}

/* ── Section modal list ──────────────────────────────── */
.eqb-section-list {
    max-height: 360px;
    overflow-y: auto;
    padding-right: 2px;
}

.eqb-section-list::-webkit-scrollbar { width: 5px; }
.eqb-section-list::-webkit-scrollbar-track { background: var(--eqb-surface-3); border-radius: 4px; }
.eqb-section-list::-webkit-scrollbar-thumb { background: var(--eqb-muted); border-radius: 4px; }

.eqb-section-list-item {
    display: block;
    width: 100%;
    text-align: left;
    padding: 10px 14px;
    margin-bottom: 5px;
    border: 1.5px solid var(--eqb-border);
    border-radius: 7px;
    background: var(--eqb-surface);
    font-size: 13px;
    line-height: 1.5;
    word-break: break-word;
    overflow-wrap: anywhere;
    cursor: pointer;
    color: var(--eqb-text);
    transition: background .12s, border-color .12s, color .12s;
}

.eqb-section-list-item:hover {
    background: var(--eqb-primary-bg);
    border-color: var(--eqb-primary-light);
    color: var(--eqb-primary);
    outline: none;
}

.eqb-section-list-item.is-active {
    background: var(--eqb-primary-mid);
    border-color: var(--eqb-primary);
    color: #fff;
    font-weight: 600;
}

.eqb-section-list-empty {
    padding: 24px;
    text-align: center;
    color: var(--eqb-muted);
    font-size: 13px;
}

/* ── Action buttons ──────────────────────────────────── */
#simpletabledelete .eqb-action-cell {
    width: 88px !important;
    min-width: 88px !important;
    max-width: 88px !important;
    padding: 10px 6px !important;
    text-align: center;
}

#simpletabledelete .eqb-action-btns {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    width: 76px;
    margin: 0 auto;
}

#simpletabledelete .eqb-action-btns .btn {
    width: 76px !important;
    min-width: 76px !important;
    max-width: 76px !important;
    padding: 6px 4px !important;
    font-size: 11px !important;
    font-weight: 600;
    border-radius: 5px;
    white-space: nowrap;
    line-height: 1.2;
    transition: background .12s, color .12s;
}

/* ── Question cell ───────────────────────────────────── */
#simpletabledelete .eqb-question-cell { vertical-align: top; max-width: 100%; }

#simpletabledelete .eqb-question-content {
    max-height: 200px;
    max-width: 100%;
    overflow-y: auto;
    overflow-x: hidden;
    word-break: break-word;
    overflow-wrap: anywhere;
    padding: 12px 14px;
    font-size: 13.5px;
    line-height: 1.6;
    color: var(--eqb-text);
    background: #fff;
    border: 1px solid #e8edf2;
    border-radius: 6px;
}

#simpletabledelete .eqb-question-content,
#simpletabledelete .eqb-question-content * {
    max-width: 100% !important;
    white-space: normal !important;
    word-wrap: break-word !important;
    overflow-wrap: anywhere !important;
}

#simpletabledelete .eqb-question-content::-webkit-scrollbar { width: 4px; }
#simpletabledelete .eqb-question-content::-webkit-scrollbar-thumb { background: var(--eqb-muted); border-radius: 4px; }

/* ── Options ─────────────────────────────────────────── */
#simpletabledelete .eqb-options-cell {
    vertical-align: top;
    padding: 10px !important;
    max-width: 100%;
    background: #fff !important;
}

#simpletabledelete .eqb-options-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(115px, 1fr));
    gap: 6px;
    width: 100%;
    max-width: 100%;
}

#simpletabledelete .eqb-option-item {
    border: 1.5px solid var(--eqb-border);
    border-radius: 6px;
    padding: 7px 8px;
    font-size: 11.5px;
    line-height: 1.45;
    text-align: center;
    word-break: break-word;
    overflow-wrap: anywhere;
    white-space: normal;
    min-width: 0;
    max-width: 100%;
    background: var(--eqb-surface);
    color: var(--eqb-text);
}

#simpletabledelete .eqb-option-item strong {
    display: block;
    font-size: 10px;
    color: var(--eqb-primary-mid);
    margin-bottom: 2px;
    font-weight: 700;
}

#simpletabledelete .eqb-option-item--correct {
    background: var(--eqb-success-light) !important;
    border-color: var(--eqb-success);
    color: #fff !important;
    box-shadow: none;
}

#simpletabledelete .eqb-option-item--correct strong,
#simpletabledelete .eqb-option-item--correct span { color: #fff !important; }

/* ── Upload cell ─────────────────────────────────────── */
#simpletabledelete th.eqb-upload-col,
#simpletabledelete .eqb-upload-cell {
    width: 90px;
    min-width: 90px;
    max-width: 90px;
    text-align: center;
    padding: 10px 4px !important;
}

#simpletabledelete .eqb-upload-label {
    display: block;
    font-size: 9.5px;
    font-weight: 700;
    color: var(--eqb-primary-mid);
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: .5px;
}

#simpletabledelete .eqb-upload-wrap { max-width: 100%; text-align: center; }

#simpletabledelete .eqb-upload-wrap img {
    max-width: 72px;
    max-height: 72px;
    height: auto;
    border-radius: 6px;
    border: 2px solid var(--eqb-border);
    object-fit: cover;
    box-shadow: var(--eqb-shadow);
    transition: transform .15s;
}

#simpletabledelete .eqb-upload-wrap img:hover { transform: scale(1.08); }

/* ── Number cell ─────────────────────────────────────── */
#simpletabledelete th.eqb-num-col,
#simpletabledelete .eqb-num-cell {
    width: 40px;
    min-width: 40px;
    max-width: 44px;
    text-align: center;
    font-weight: 700;
    color: var(--eqb-muted);
    vertical-align: middle;
}

/* ── Course Question Bank table ──────────────────────── */
#simpletable {
    background: #fff !important;
    margin-bottom: 0;
}

#simpletable thead tr {
    background: var(--eqb-primary-mid) !important;
    color: #fff !important;
}

#simpletable thead tr:hover {
    background: var(--eqb-primary-mid) !important;
}

#simpletable thead td,
#simpletable thead th {
    color: #fff !important;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .4px;
    text-transform: uppercase;
    border: none !important;
    padding: 13px 14px !important;
}

#simpletable tbody tr {
    background: #1F776E !important;
}

#simpletable tbody td,
#simpletable tbody th {
    background: #fff !important;
    padding: 16px 14px !important;
    border-color: #e8edf2 !important;
    vertical-align: top;
    font-size: 13.5px;
    line-height: 1.55;
    color: var(--eqb-text);
}

#simpletable tbody tr:hover td,
#simpletable tbody tr:hover th {
    background: #fafbfc !important;
}

#simpletable .badge-success {
    background: var(--eqb-info-bg);
    color: var(--eqb-info);
    border: 1px solid #bae6fd;
    font-weight: 600;
}

#simpletable .eqb-question-cell {
    vertical-align: top;
    max-width: 480px;
    min-width: 260px;
    background: #fff !important;
}

#simpletable .eqb-question-content {
    max-height: 220px;
    max-width: 100%;
    overflow-y: auto;
    overflow-x: hidden;
    word-break: break-word;
    overflow-wrap: anywhere;
    padding: 14px 16px;
    font-size: 13.5px;
    line-height: 1.65;
    color: var(--eqb-text);
    margin-bottom: 10px;
    background: #fff;
    border: 1px solid #e8edf2;
    border-radius: 8px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}

#simpletable .eqb-question-content,
#simpletable .eqb-question-content * {
    max-width: 100% !important;
    white-space: normal !important;
    word-wrap: break-word !important;
    overflow-wrap: anywhere !important;
}

#simpletable .eqb-question-content::-webkit-scrollbar { width: 5px; }
#simpletable .eqb-question-content::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
#simpletable .eqb-question-content::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

#simpletable .eqb-cqb-actions {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    padding-top: 10px;
    margin-top: 2px;
}

#simpletable .eqb-options-cell {
    vertical-align: top;
    padding: 10px 14px 14px !important;
    max-width: 100%;
    background: #fff !important;
    border-top: 1px dashed #e2e8f0 !important;
}

#simpletable .eqb-cqb-options-row td.eqb-options-cell {
    background: #fff !important;
}

#simpletable .eqb-row-in-exam .eqb-options-cell {
    background: #f0fdf4 !important;
}

#simpletable .eqb-options-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 8px;
    width: 100%;
    max-width: 100%;
}

#simpletable .eqb-option-item {
    border: 1.5px solid var(--eqb-border);
    border-radius: 6px;
    padding: 8px 10px;
    font-size: 12px;
    line-height: 1.45;
    text-align: left;
    word-break: break-word;
    overflow-wrap: anywhere;
    white-space: normal;
    min-width: 0;
    max-width: 100%;
    background: var(--eqb-surface);
    color: var(--eqb-text);
}

#simpletable .eqb-option-item strong {
    display: inline-block;
    font-size: 10px;
    color: var(--eqb-primary-mid);
    margin-right: 4px;
    font-weight: 700;
}

#simpletable .eqb-option-item--correct {
    background: var(--eqb-success-light) !important;
    border-color: var(--eqb-success);
    color: #fff !important;
}

#simpletable .eqb-option-item--correct strong,
#simpletable .eqb-option-item--correct span { color: #fff !important; }

#simpletable .eqb-cqb-table--mcq .eqb-question-cell {
    max-width: none;
    min-width: 0;
    width: 100%;
}

/* ── Responsive ──────────────────────────────────────── */
@media (max-width: 768px) {
    #simpletabledelete .eqb-question-content { max-height: 130px; }
    #simpletabledelete th.eqb-section-col,
    #simpletabledelete .eqb-section-cell { width: 140px; min-width: 140px; max-width: 140px; }
    #simpletabledelete .eqb-options-grid { grid-template-columns: repeat(auto-fill, minmax(95px, 1fr)); }
    #simpletabledelete .eqb-action-cell { width: 76px; min-width: 76px; max-width: 80px; }
    #simpletabledelete .eqb-action-btns .btn { font-size: 10.5px; padding: 4px; }
    #simpletable .eqb-question-content { max-height: 130px; }
}

#simpletabledelete .eqb-table-header-row th,#simpletabledelete table.eqb-paper-table tr.eqb-table-header-row th {
    background: #1F776E !important;
}



/* =========================================================
   QUESTION BANK — FIGMA STYLE OVERRIDE

   Figma:
   Add Question / Exam Question Bank -> 527:473
   Course Question Bank             -> 505:426

   CSS ONLY
   Existing Blade / PHP / JS / AJAX unchanged
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');


/* =========================================================
   1. FIGMA TOKENS
   Override old blue EQB palette
   ========================================================= */

:root {
    --eqb-primary: #17675F;
    --eqb-primary-mid: #1F776E;
    --eqb-primary-light: #1F776E;

    --eqb-primary-bg: rgba(31, 119, 110, 0.10);
    --eqb-primary-border: #D9D9D9;
    --eqb-primary-soft: rgba(31, 119, 110, 0.10);

    --eqb-success: #01A46D;
    --eqb-success-light: #10B981;
    --eqb-success-bg: rgba(16, 185, 129, 0.08);
    --eqb-success-border: rgba(16, 185, 129, 0.20);

    --eqb-danger: #DC2626;
    --eqb-danger-light: #DC2626;
    --eqb-danger-bg: #FEF2F2;
    --eqb-danger-border: #FECACA;

    --eqb-warning: #FFBA49;
    --eqb-warning-bg: #FFBA49;
    --eqb-warning-border: #FFBA49;

    --eqb-surface: #FFFFFF;
    --eqb-surface-2: #F9FAFB;
    --eqb-surface-3: #F9FAFB;

    --eqb-border: #E5E7EB;
    --eqb-border-input: #D9D9D9;

    --eqb-text: #111827;
    --eqb-text-secondary: #374151;
    --eqb-muted: #9CA3AF;

    --eqb-radius: 5px;
    --eqb-radius-sm: 5px;

    --eqb-shadow: none;
    --eqb-shadow-sm: none;
    --eqb-shadow-lg: 0 14px 35px rgba(0, 0, 0, .12);
}


/* =========================================================
   2. PAGE
   ========================================================= */

.main-content {
    font-family: 'Montserrat', sans-serif !important;

    color: #252525 !important;

    background:
        linear-gradient(
            to left,
            rgba(23, 103, 95, 0.15),
            rgba(255, 255, 255, 0.15)
        ) !important;
}


.main-content *,
.main-content button,
.main-content input,
.main-content select,
.main-content textarea {
    font-family: 'Montserrat', sans-serif;
}


/* =========================================================
   3. TOP PAGE HEADING
   Existing H4 contains course/exam information.
   Figma adds "Question Bank" above it.
   ========================================================= */

div[style*="margin-bottom:30px"]
> div:first-child
> h4 {
    margin: 0 !important;

    font-size: 18px !important;
    font-weight: 400 !important;
    line-height: 1.3 !important;

    color: #252525 !important;
}


div[style*="margin-bottom:30px"]
> div:first-child
> h4::before {
    content: "Question Bank";

    display: block;

    margin-bottom: 5px;

    font-size: 20px;
    font-weight: 600;
    line-height: 1.2;

    color: #252525;
}


/* Existing exam name after course */
div[style*="margin-bottom:30px"]
> div:first-child
> h4 > span {
    font-size: 14px !important;
    font-weight: 400 !important;

    color: #6B7280 !important;

    opacity: 1 !important;
}


/* =========================================================
   4. ADD EXAM SECTION
   Figma = 18px regular underline
   ========================================================= */

.eqb-header-btn--solid {
    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    box-shadow: none !important;

    font-size: 18px !important;
    font-weight: 400 !important;
    line-height: 1.2 !important;

    color: #252525 !important;

    text-decoration: underline !important;

    text-underline-offset: 2px;
}


.eqb-header-btn--solid:hover,
.eqb-header-btn--solid:focus {
    background: transparent !important;

    color: #17675F !important;

    text-decoration: underline !important;
}


.eqb-header-btn--solid .fa-plus {
    margin-right: 5px !important;

    font-size: 14px !important;

    color: inherit !important;
}


/* =========================================================
   5. ADD QUESTION FORM CARD
   Figma = white / radius 5 / no shadow
   ========================================================= */

.eqb-card:not(.mt-3) {
    margin: 0 0 20px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    overflow: hidden !important;
}


.eqb-card:not(.mt-3) > .eqb-card-body {
    padding: 20px !important;

    background: #FFFFFF !important;
}


.question-form-box {
    width: 100% !important;

    padding: 0 !important;

    background: #FFFFFF !important;

    border: 0 !important;

    box-shadow: none !important;
}


/* =========================================================
   6. FORM GRID
   Figma uses two equal 510px columns
   ========================================================= */

.question-form-box .form-row {
    margin-left: -9px !important;
    margin-right: -9px !important;
}


.question-form-box .form-row > [class*="col-"] {
    padding-left: 9px !important;
    padding-right: 9px !important;
}


.question-form-box .form-group {
    margin-bottom: 20px !important;
}


/* =========================================================
   7. LABELS
   Figma = 16px Medium
   ========================================================= */

.question-form-box label {
    display: block !important;

    margin: 0 0 7px !important;

    font-size: 16px !important;
    font-weight: 500 !important;
    line-height: 1.2 !important;

    letter-spacing: 0 !important;

    color: #000000 !important;
}


/* =========================================================
   8. INPUT / SELECT
   Figma = 40px / border #D9D9D9 / radius 5
   ========================================================= */

.question-form-box .form-control {
    width: 100% !important;

    height: 40px !important;
    min-height: 40px !important;

    padding: 0 12px !important;

    background: #FFFFFF !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;

    letter-spacing: 0 !important;
}


.question-form-box .form-control::placeholder {
    color: #9F9F9F !important;

    opacity: 1;
}


.question-form-box .form-control:focus {
    outline: 0 !important;

    border-color: #1F776E !important;

    box-shadow:
        0 0 0 3px rgba(31, 119, 110, .08) !important;
}


/* Select visible text */
.question-form-box select.form-control {
    padding-right: 36px !important;

    color: #252525 !important;

    cursor: pointer;
}


/* =========================================================
   9. UPLOAD IMAGE
   Figma Choose File = 110 x 29
   ========================================================= */

.question-form-box .form-control-file {
    width: 100% !important;

    height: 40px !important;

    padding: 5px !important;

    background: #FFFFFF !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    font-size: 14px !important;

    color: #9F9F9F !important;

    box-shadow: none !important;
}


.question-form-box .form-control-file::file-selector-button {
    width: 110px;
    height: 29px;

    margin-right: 10px;
    padding: 0 10px;

    background: #F1F1F1;

    border: 1px solid #F0F0F0;
    border-radius: 5px;

    font-family: 'Montserrat', sans-serif;

    font-size: 14px;
    font-weight: 400;

    color: #000000;

    cursor: pointer;
}


.question-form-box
.form-control-file::-webkit-file-upload-button {
    width: 110px;
    height: 29px;

    margin-right: 10px;
    padding: 0 10px;

    background: #F1F1F1;

    border: 1px solid #F0F0F0;
    border-radius: 5px;

    font-family: 'Montserrat', sans-serif;

    font-size: 14px;

    color: #000000;
}


/* =========================================================
   10. QUESTION / CKEDITOR
   Keep editor functionality intact.
   ========================================================= */

#cke_qname {
    overflow: hidden !important;

    width: 100% !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;
}


#cke_qname .cke_top {
    background: #F9FAFB !important;

    border-bottom: 1px solid #E5E7EB !important;

    box-shadow: none !important;
}


#cke_qname .cke_contents {
    min-height: 100px !important;

    background: #FFFFFF !important;
}


#cke_qname .cke_bottom {
    background: #F9FAFB !important;

    border-top: 1px solid #E5E7EB !important;
}


/* If CKEditor has not initialized yet */
textarea#qname {
    width: 100% !important;

    min-height: 100px !important;

    padding: 10px 12px !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;
}


/* =========================================================
   11. PREVIEW IMAGE
   ========================================================= */

#preview_image img {
    max-width: 200px !important;

    height: auto !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 5px !important;

    box-shadow: none !important;
}


/* =========================================================
   12. SUBMIT / RESET
   Exact Figma = 136 x 50
   ========================================================= */

.question-form-box .form-row.mt-2 {
    margin-top: 0 !important;

    display: flex !important;

    gap: 8px !important;
}


.question-form-box .form-row.mt-2 .form-group {
    flex: 0 0 136px !important;

    width: 136px !important;
    max-width: 136px !important;

    margin: 0 !important;
    padding: 0 !important;
}


.question-form-box #btnsubmit,
.question-form-box .eqb-btn-reset {
    width: 136px !important;
    min-width: 136px !important;

    height: 50px !important;
    min-height: 50px !important;

    padding: 0 18px !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    background: #1F776E !important;

    border: 1px solid #1F776E !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-size: 18px !important;
    font-weight: 500 !important;
    line-height: 1 !important;

    letter-spacing: 0 !important;

    color: #FFFFFF !important;

    transform: none !important;
}


.question-form-box .eqb-btn-reset {
    background: #FFFFFF !important;

    border-color: #17675F !important;

    color: #17675F !important;
}


.question-form-box #btnsubmit:hover {
    background: #17675F !important;

    border-color: #17675F !important;

    opacity: 1 !important;
}


.question-form-box .eqb-btn-reset:hover {
    background: rgba(23, 103, 95, .05) !important;

    color: #17675F !important;
}


.question-form-box #btnsubmit .fa,
.question-form-box .eqb-btn-reset .fa {
    display: none !important;
}


/* =========================================================
   13. SUCCESS / ERROR
   ========================================================= */

.msg-div,
.eqb-alert-success {
    margin: 10px 0 !important;

    padding: 10px 14px !important;

    border-radius: 5px !important;

    font-size: 13px !important;
    font-weight: 400 !important;

    box-shadow: none !important;
}


/* =========================================================
   14. EXAM QUESTION BANK CARD
   Figma 527:473
   ========================================================= */

.card.mt-3.eqb-card:has(#simpletabledelete) {
    overflow: visible !important;

    margin-top: 20px !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    box-shadow: none !important;
}


/* Heading area is NOT green in Figma */
.card.mt-3.eqb-card:has(#simpletabledelete)
> .eqb-card-header {
    min-height: 34px !important;

    margin-bottom: 18px !important;
    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    box-shadow: none !important;
}


.card.mt-3.eqb-card:has(#simpletabledelete)
> .eqb-card-header h5 {
    margin: 0 !important;

    font-size: 20px !important;
    font-weight: 600 !important;
    line-height: 1.2 !important;

    color: #111827 !important;
}


.card.mt-3.eqb-card:has(#simpletabledelete)
> .eqb-card-header h5 .fa {
    display: none !important;
}


/* Figma orange question count */
.card.mt-3.eqb-card:has(#simpletabledelete)
.eqb-badge-pill {
    min-width: 142px !important;
    height: 28px !important;

    padding: 0 12px !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    background: #FFBA49 !important;

    border: 0 !important;
    border-radius: 99px !important;

    font-size: 13px !important;
    font-weight: 600 !important;

    color: #FFFFFF !important;
}


.card.mt-3.eqb-card:has(#simpletabledelete)
.eqb-badge-pill .fa {
    display: none !important;
}


/* Exam bank content card */
.card.mt-3.eqb-card:has(#simpletabledelete)
> .eqb-card-body--compact {
    padding: 0 !important;

    background: transparent !important;
}


.card.mt-3.eqb-card:has(#simpletabledelete)
.table-scroll {
    padding: 24px !important;

    background: #FFFFFF !important;

    border-radius: 16px !important;

    box-shadow: none !important;
}


/* =========================================================
   15. EXAM QUESTION BANK SECTIONS
   ========================================================= */

#simpletabledelete .eqb-paper-block {
    overflow: visible !important;

    margin: 0 0 14px !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;
}


#simpletabledelete .eqb-paper-block:last-child {
    margin-bottom: 0 !important;
}


#simpletabledelete .eqb-paper-block-title {
    margin: 0 0 12px !important;
    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;

    font-size: 14px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;

    letter-spacing: 0 !important;

    text-transform: none !important;

    color: #111827 !important;
}


/* =========================================================
   16. EXAM BANK TABLE
   Figma = bordered 12px rounded table
   ========================================================= */

#simpletabledelete table.eqb-paper-table {
    overflow: hidden !important;

    width: 100% !important;

    margin: 0 !important;

    background: #FFFFFF !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 12px !important;

    border-collapse: separate !important;
    border-spacing: 0 !important;

    box-shadow: none !important;
}


/* Soft green header */
#simpletabledelete
table.eqb-paper-table
tr.eqb-table-header-row th,

#simpletabledelete
table.eqb-paper-table
tr.eqb-table-header-row td {
    height: 37px !important;

    padding: 10px 10px !important;

    background: rgba(31, 119, 110, 0.10) !important;

    border: 0 !important;

    font-size: 11px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;

    letter-spacing: 0 !important;

    text-transform: uppercase !important;

    /* color: #6B7280 !important; */
}


/* Data */
#simpletabledelete
table.eqb-paper-table
tbody tr:not(.eqb-table-header-row) td,

#simpletabledelete
table.eqb-paper-table
tbody tr:not(.eqb-table-header-row) th {
    min-height: 56px !important;

    padding: 12px 10px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-top: 1px solid #E5E7EB !important;

    vertical-align: middle !important;

    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;

    color: #111827 !important;
}


/* =========================================================
   17. QUESTION CONTENT
   Remove old boxed/scroll-heavy appearance
   ========================================================= */

#simpletabledelete .eqb-question-content {
    max-height: none !important;

    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    box-shadow: none !important;

    font-size: 14px !important;
    font-weight: 500 !important;
    line-height: 1.45 !important;

    color: #111827 !important;
}


/* =========================================================
   18. EXAM SECTION
   Compact Figma-style control
   ========================================================= */

#simpletabledelete .eqb-section-label {
    display: none !important;
}


#simpletabledelete .eqb-section-box {
    padding: 7px 9px !important;

    background: #F9FAFB !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 10px !important;

    box-shadow: none !important;
}


#simpletabledelete .eqb-section-name {
    margin: 0 0 5px !important;

    overflow: hidden !important;

    font-size: 13px !important;
    font-weight: 400 !important;

    color: #111827 !important;

    white-space: nowrap !important;
    text-overflow: ellipsis !important;
}


#simpletabledelete .eqb-section-change-btn {
    width: 100% !important;

    padding: 3px 6px !important;

    background: transparent !important;

    border: 0 !important;

    font-size: 11px !important;
    font-weight: 500 !important;

    color: #17675F !important;

    box-shadow: none !important;
}


/* =========================================================
   19. EXAM BANK ACTIONS
   Figma = 32 x 32 icon buttons
   ========================================================= */

#simpletabledelete .eqb-action-cell {
    padding: 10px 6px !important;
}


#simpletabledelete .eqb-action-btns {
    width: auto !important;

    display: flex !important;
    flex-direction: row !important;

    align-items: center !important;
    justify-content: center !important;

    gap: 8px !important;
}


#simpletabledelete
.eqb-action-btns .btn {
    width: 32px !important;
    min-width: 32px !important;
    max-width: 32px !important;

    height: 32px !important;

    padding: 0 !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    background: #F3F4F6 !important;

    border: 0 !important;
    border-radius: 6px !important;

    font-size: 0 !important;

    color: #6B7280 !important;

    box-shadow: none !important;
}


#simpletabledelete
.eqb-action-btns .btn i {
    margin: 0 !important;

    font-size: 14px !important;

    color: #6B7280 !important;
}


#simpletabledelete
.eqb-action-btns .btn:hover {
    background: #E5E7EB !important;

    transform: none !important;
}


/* =========================================================
   20. MCQ OPTIONS
   ========================================================= */

#simpletabledelete .eqb-options-cell {
    padding: 10px !important;

    background: #FFFFFF !important;
}


#simpletabledelete .eqb-options-grid {
    gap: 6px !important;
}


#simpletabledelete .eqb-option-item {
    padding: 7px 8px !important;

    background: #F9FAFB !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 6px !important;

    font-size: 12px !important;

    color: #374151 !important;

    box-shadow: none !important;
}


#simpletabledelete
.eqb-option-item--correct {
    background: rgba(31, 119, 110, .10) !important;

    border-color: rgba(31, 119, 110, .25) !important;

    color: #17675F !important;
}


#simpletabledelete
.eqb-option-item--correct strong,

#simpletabledelete
.eqb-option-item--correct span {
    color: #17675F !important;
}


/* =========================================================
   21. UPLOAD CELL
   ========================================================= */

#simpletabledelete .eqb-upload-label {
    font-size: 10px !important;

    color: #6B7280 !important;
}


#simpletabledelete .eqb-upload-wrap img {
    max-width: 64px !important;
    max-height: 64px !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 6px !important;

    box-shadow: none !important;
}


/* =========================================================
   22. COURSE QUESTION BANK
   Exact Figma visual language from 505:426
   ========================================================= */

.card.mt-3.eqb-card:has(#simpletable) {
    overflow: hidden !important;

    margin-top: 20px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 12px !important;

    box-shadow: none !important;
}


/*
   Header is 57px green + 40px white warning row.
   Existing warning badge is repositioned into second row.
*/
.card.mt-3.eqb-card:has(#simpletable)
> .eqb-card-header {
    position: relative !important;

    min-height: 97px !important;

    padding: 0 20px !important;

    display: block !important;

    background:
        linear-gradient(
            to bottom,
            #1F776E 0,
            #1F776E 57px,
            #FFFFFF 57px,
            #FFFFFF 100%
        ) !important;

    border: 0 !important;
    border-radius: 12px 12px 0 0 !important;
}


/* Course Question Bank */
.card.mt-3.eqb-card:has(#simpletable)
> .eqb-card-header h5 {
    position: absolute !important;

    top: 20px !important;
    left: 20px !important;

    margin: 0 !important;

    font-size: 16px !important;
    font-weight: 600 !important;
    line-height: 1 !important;

    color: #FFFFFF !important;
}


.card.mt-3.eqb-card:has(#simpletable)
> .eqb-card-header h5 .fa {
    display: none !important;
}


/* Warning */
.card.mt-3.eqb-card:has(#simpletable)
.eqb-warning-badge {
    position: absolute !important;

    top: 57px !important;
    left: 20px !important;

    width: calc(100% - 40px) !important;
    height: 40px !important;

    padding: 0 !important;

    display: flex !important;

    align-items: center !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    font-size: 12px !important;
    font-weight: 400 !important;

    color: #6B7280 !important;
}


.card.mt-3.eqb-card:has(#simpletable)
.eqb-warning-badge .fa {
    margin-right: 7px !important;

    font-size: 14px !important;

    color: #17675F !important;
}


/* =========================================================
   23. COURSE BANK BODY
   ========================================================= */

.card.mt-3.eqb-card:has(#simpletable)
> .eqb-card-body {
    padding: 16px 0 0 !important;

    background: #FFFFFF !important;
}


#simpletable {
    background: #FFFFFF !important;
}


/* Individual type sections */
#simpletable .eqb-paper-block {
    overflow: hidden !important;

    margin: 0 0 16px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 0 !important;

    box-shadow: none !important;
}


#simpletable .eqb-paper-block-title {
    padding: 0 20px 10px !important;

    background: #FFFFFF !important;

    border: 0 !important;

    font-size: 14px !important;
    font-weight: 600 !important;

    letter-spacing: 0 !important;

    text-transform: none !important;

    color: #111827 !important;
}


/* =========================================================
   24. COURSE BANK TABLE HEADER
   Figma = 39px / soft green / 12px bold uppercase
   ========================================================= */

#simpletable table.eqb-cqb-table {
    width: 100% !important;

    margin: 0 !important;

    background: #FFFFFF !important;

    border: 0 !important;

    border-collapse: collapse !important;
}


#simpletable
table.eqb-cqb-table
.eqb-table-header-row th,

#simpletable
table.eqb-cqb-table
.eqb-table-header-row td {
    height: 39px !important;

    padding: 10px 12px !important;

    background: rgba(31, 119, 110, 0.10) !important;

    border: 0 !important;

    font-size: 12px !important;
    font-weight: 700 !important;

    letter-spacing: 0 !important;

    text-transform: uppercase !important;

    color: #FFF !important;
}


/* =========================================================
   25. COURSE BANK ROWS
   Figma = 64px white
   ========================================================= */

#simpletable tbody td,
#simpletable tbody th {
    min-height: 64px !important;

    padding: 14px 12px !important;

    vertical-align: middle !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-bottom: 1px solid #F1F1F1 !important;

    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;

    color: #374151 !important;
}


#simpletable tbody tr:hover td,
#simpletable tbody tr:hover th {
    background: #FFFFFF !important;
}


/* Question text */
#simpletable .eqb-question-content {
    max-height: none !important;

    margin: 0 !important;
    padding: 0 !important;

    overflow: hidden !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    box-shadow: none !important;

    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;

    color: #111827 !important;
}


/* =========================================================
   26. COURSE BANK OPTIONS
   ========================================================= */

#simpletable .eqb-options-cell {
    padding: 10px 12px 14px !important;

    background: #FFFFFF !important;

    border-top: 1px solid #F1F1F1 !important;
}


#simpletable .eqb-option-item {
    padding: 7px 8px !important;

    background: #FFFFFF !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 5px !important;

    font-size: 12px !important;

    box-shadow: none !important;
}


#simpletable .eqb-option-item--correct {
    background: rgba(31, 119, 110, .10) !important;

    border-color: rgba(31, 119, 110, .25) !important;

    color: #17675F !important;
}


#simpletable
.eqb-option-item--correct strong,

#simpletable
.eqb-option-item--correct span {
    color: #17675F !important;
}


/* =========================================================
   27. COURSE BANK ACTION ICONS
   Figma = 32 x 32 grey buttons
   ========================================================= */

#simpletable .eqb-cqb-actions {
    gap: 8px !important;

    padding-top: 8px !important;

    margin: 0 !important;
}


#simpletable .eqb-cqb-actions > span[title] {
    width: 32px !important;
    height: 32px !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    background: #F3F4F6 !important;

    border-radius: 6px !important;

    cursor: pointer;
}


#simpletable .eqb-cqb-actions > span[title] i {
    margin: 0 !important;

    font-size: 14px !important;

    color: #6B7280 !important;
}


#simpletable .eqb-cqb-actions > span[title]:hover {
    background: #E5E7EB !important;
}


/* Added state */
#simpletable .eqb-cqb-added-badge {
    min-height: 28px !important;

    padding: 0 9px !important;

    background: rgba(31, 119, 110, .10) !important;

    border: 0 !important;
    border-radius: 99px !important;

    font-size: 11px !important;

    color: #17675F !important;
}


/* =========================================================
   28. SECTION MODAL
   ========================================================= */

#eqbSectionModal .modal-content {
    overflow: hidden !important;

    border: 0 !important;
    border-radius: 10px !important;

    box-shadow: 0 14px 35px rgba(0, 0, 0, .12) !important;
}


#eqbSectionModal .modal-header {
    min-height: 56px !important;

    padding: 0 20px !important;

    background: #1F776E !important;

    border: 0 !important;
}


#eqbSectionModal .modal-title {
    font-size: 16px !important;
    font-weight: 600 !important;

    color: #FFFFFF !important;
}


#eqbSectionModal .close {
    color: #FFFFFF !important;

    opacity: 1 !important;
}


#eqbSectionModal .modal-body {
    padding: 20px !important;

    background: #FFFFFF !important;
}


.eqb-search-input {
    height: 40px !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;
}


.eqb-search-input:focus {
    border-color: #1F776E !important;

    box-shadow:
        0 0 0 3px rgba(31, 119, 110, .08) !important;
}


.eqb-section-list-item {
    border: 1px solid #E5E7EB !important;
    border-radius: 5px !important;

    background: #FFFFFF !important;

    color: #252525 !important;
}


.eqb-section-list-item:hover {
    background: rgba(31, 119, 110, .06) !important;

    border-color: rgba(31, 119, 110, .25) !important;
}


.eqb-section-list-item.is-active {
    background: #1F776E !important;

    border-color: #1F776E !important;

    color: #FFFFFF !important;
}


/* =========================================================
   29. RESPONSIVE
   ========================================================= */

@media (max-width: 991px) {

    .card.mt-3.eqb-card:has(#simpletabledelete)
    .table-scroll,

    .card.mt-3.eqb-card:has(#simpletable)
    .eqb-card-body {
        overflow-x: auto !important;
    }


    #simpletabledelete table.eqb-paper-table,
    #simpletable table.eqb-cqb-table {
        min-width: 900px !important;
    }

}


@media (max-width: 767px) {

    div[style*="margin-bottom:30px"] {
        align-items: flex-start !important;

        gap: 12px;

        flex-direction: column !important;
    }


    div[style*="margin-bottom:30px"]
    > div:first-child
    > h4::before {
        font-size: 20px;
    }


    .eqb-header-btn--solid {
        font-size: 14px !important;
    }


    .eqb-card:not(.mt-3)
    > .eqb-card-body {
        padding: 16px !important;
    }


    .question-form-box .form-row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }


    .question-form-box
    .form-row > [class*="col-"] {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }


    .question-form-box .form-row.mt-2 {
        display: grid !important;

        grid-template-columns: 1fr 1fr !important;

        gap: 8px !important;
    }


    .question-form-box
    .form-row.mt-2 .form-group {
        width: 100% !important;
        max-width: 100% !important;

        flex: none !important;
    }


    .question-form-box #btnsubmit,
    .question-form-box .eqb-btn-reset {
        width: 100% !important;
        min-width: 100% !important;
    }


    .card.mt-3.eqb-card:has(#simpletabledelete)
    .table-scroll {
        padding: 16px !important;
    }

}
/* FIX: preserve Font Awesome icon font */
.main-content .fa,
.main-content i.fa {
    font-family: FontAwesome !important;
}


/* =========================================================
   FINAL FIX — EXAM QUESTION BANK
   Figma 527:473
   CSS ONLY - No Blade / PHP / JS / AJAX changes
   ========================================================= */


/* ---------------------------------------------------------
   1. EXAM QUESTION BANK MAIN CARD
   --------------------------------------------------------- */

body .main-content
.card.mt-3.eqb-card:has(
    > .eqb-card-body--compact #simpletabledelete
) {
    margin-top: 20px !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    box-shadow: none !important;

    overflow: visible !important;
}


/* ---------------------------------------------------------
   2. REMOVE GREEN HEADER
   Figma: title sits directly on page background
   --------------------------------------------------------- */

body .main-content
.card.mt-3.eqb-card:has(
    > .eqb-card-body--compact #simpletabledelete
)
> .eqb-card-header {

    width: 100% !important;

    min-height: 34px !important;
    height: auto !important;

    margin: 0 0 18px 0 !important;
    padding: 0 !important;

    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    box-shadow: none !important;
}


/* Exam Question Bank title */

body .main-content
.card.mt-3.eqb-card:has(
    > .eqb-card-body--compact #simpletabledelete
)
> .eqb-card-header h5 {

    position: static !important;

    margin: 0 !important;

    font-size: 20px !important;
    font-weight: 600 !important;
    line-height: 1.2 !important;

    color: #111827 !important;
}


/* Remove old title icon */

body .main-content
.card.mt-3.eqb-card:has(
    > .eqb-card-body--compact #simpletabledelete
)
> .eqb-card-header h5 .fa {

    display: none !important;
}


/* ---------------------------------------------------------
   3. QUESTION COUNT BADGE
   Figma: orange badge right side
   --------------------------------------------------------- */

body .main-content
.card.mt-3.eqb-card:has(
    > .eqb-card-body--compact #simpletabledelete
)
.eqb-badge-pill {

    position: static !important;

    min-width: 122px !important;
    width: auto !important;

    height: 28px !important;

    margin: 0 !important;
    padding: 0 12px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #FFBA49 !important;

    border: 0 !important;
    border-radius: 99px !important;

    font-size: 13px !important;
    font-weight: 600 !important;
    line-height: 28px !important;

    color: #FFFFFF !important;

    white-space: nowrap !important;
}


/* hide badge icon */

body .main-content
.card.mt-3.eqb-card:has(
    > .eqb-card-body--compact #simpletabledelete
)
.eqb-badge-pill .fa {

    display: none !important;
}


/* ---------------------------------------------------------
   4. REMOVE EXTRA GAP / OUTER BODY
   --------------------------------------------------------- */

body .main-content
.card.mt-3.eqb-card:has(
    > .eqb-card-body--compact #simpletabledelete
)
> .eqb-card-body--compact {

    margin: 0 !important;
    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;
}


/* ---------------------------------------------------------
   5. WHITE CONTENT CARD
   --------------------------------------------------------- */

body .main-content
.card.mt-3.eqb-card:has(
    > .eqb-card-body--compact #simpletabledelete
)
.table-scroll {

    width: 100% !important;

    margin: 0 !important;
    padding: 20px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 16px !important;

    box-shadow: none !important;

    overflow-x: auto !important;
}


/* ---------------------------------------------------------
   6. PART TITLES
   --------------------------------------------------------- */

#simpletabledelete .eqb-paper-block {

    margin: 0 0 18px 0 !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    overflow: visible !important;
}


#simpletabledelete .eqb-paper-block:last-child {

    margin-bottom: 0 !important;
}


#simpletabledelete .eqb-paper-block-title {

    margin: 0 0 12px 0 !important;
    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;

    font-size: 14px !important;
    font-weight: 600 !important;
    line-height: 1.3 !important;

    text-transform: none !important;
    letter-spacing: 0 !important;

    color: #111827 !important;
}


/* ---------------------------------------------------------
   7. TABLE CONTAINER
   --------------------------------------------------------- */

#simpletabledelete table.eqb-paper-table {

    width: 100% !important;

    margin: 0 !important;

    background: #FFFFFF !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 12px !important;

    border-collapse: separate !important;
    border-spacing: 0 !important;

    overflow: hidden !important;

    box-shadow: none !important;
}


/* ---------------------------------------------------------
   8. IMPORTANT:
   Override old DARK GREEN header
   Figma = LIGHT GREEN
   --------------------------------------------------------- */

body .main-content
#simpletabledelete
table.eqb-paper-table
> tbody
> tr.eqb-table-header-row
> th,

body .main-content
#simpletabledelete
table.eqb-paper-table
> tbody
> tr.eqb-table-header-row
> td {

    height: 39px !important;

    padding: 10px 12px !important;

    background: rgba(31, 119, 110, 0.10) !important;

    border: 0 !important;

    font-size: 11px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;

    text-transform: uppercase !important;
    letter-spacing: 0 !important;

    text-align: left !important;

    /* color: #6B7280 !important; */
}


/* Header hover must remain same */

body .main-content
#simpletabledelete
table.eqb-paper-table
> tbody
> tr.eqb-table-header-row:hover
> th,

body .main-content
#simpletabledelete
table.eqb-paper-table
> tbody
> tr.eqb-table-header-row:hover
> td {

    background: rgba(31, 119, 110, 0.10) !important;

    color: #FFF !important;
}


/* ---------------------------------------------------------
   9. DATA ROWS
   --------------------------------------------------------- */

body .main-content
#simpletabledelete
table.eqb-paper-table
> tbody
> tr:not(.eqb-table-header-row)
> td,

body .main-content
#simpletabledelete
table.eqb-paper-table
> tbody
> tr:not(.eqb-table-header-row)
> th {

    min-height: 56px !important;

    padding: 12px 10px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-top: 1px solid #F1F1F1 !important;

    vertical-align: middle !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #374151 !important;
}


/* Do not change row color on hover */

body .main-content
#simpletabledelete
table.eqb-paper-table
> tbody
> tr:not(.eqb-table-header-row):hover
> td,

body .main-content
#simpletabledelete
table.eqb-paper-table
> tbody
> tr:not(.eqb-table-header-row):hover
> th {

    background: #FFFFFF !important;
}


/* ---------------------------------------------------------
   10. QUESTION TEXT
   --------------------------------------------------------- */

#simpletabledelete .eqb-question-content {

    max-height: none !important;

    margin: 0 !important;
    padding: 0 !important;

    overflow: visible !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    box-shadow: none !important;

    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1.45 !important;

    color: #111827 !important;
}


/* ---------------------------------------------------------
   11. EXAM SECTION BOX
   --------------------------------------------------------- */

#simpletabledelete .eqb-section-label {

    display: none !important;
}


#simpletabledelete .eqb-section-box {

    padding: 7px 9px !important;

    background: #F9FAFB !important;

    border: 1px solid #E5E7EB !important;
    border-radius: 10px !important;

    box-shadow: none !important;
}


#simpletabledelete .eqb-section-name {

    margin: 0 0 5px 0 !important;

    font-size: 13px !important;
    font-weight: 400 !important;

    color: #374151 !important;
}


/* ---------------------------------------------------------
   12. EDIT / DELETE ACTION BUTTONS
   --------------------------------------------------------- */

#simpletabledelete .eqb-action-btns {

    width: auto !important;

    display: flex !important;
    flex-direction: row !important;

    align-items: center !important;
    justify-content: center !important;

    gap: 8px !important;

    margin: 0 auto !important;
}


#simpletabledelete .eqb-action-btns .btn {

    width: 32px !important;
    min-width: 32px !important;
    max-width: 32px !important;

    height: 32px !important;

    padding: 0 !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #F3F4F6 !important;

    border: 0 !important;
    border-radius: 6px !important;

    box-shadow: none !important;

    font-size: 0 !important;

    color: #6B7280 !important;
}


#simpletabledelete .eqb-action-btns .btn i {

    margin: 0 !important;

    font-size: 14px !important;

    color: #6B7280 !important;
}


/* ---------------------------------------------------------
   13. UPLOAD
   --------------------------------------------------------- */

#simpletabledelete .eqb-upload-label {

    font-size: 10px !important;
    font-weight: 600 !important;

    color: #6B7280 !important;
}


/* ---------------------------------------------------------
   14. RESPONSIVE
   --------------------------------------------------------- */

@media (max-width: 991px) {

    body .main-content
    .card.mt-3.eqb-card:has(
        > .eqb-card-body--compact #simpletabledelete
    )
    .table-scroll {

        padding: 16px !important;
    }


    #simpletabledelete table.eqb-paper-table {

        min-width: 900px !important;
    }
}
/* FINAL FIX — Exam Question Bank table header */

body .main-content
#simpletabledelete
table.eqb-paper-table
tr.eqb-table-header-row > th,

body .main-content
#simpletabledelete
table.eqb-paper-table
tr.eqb-table-header-row > td {

    background: rgba(31, 119, 110, 0.10) !important;

    /* color: #6B7280 !important; */

    border: 0 !important;

    font-size: 11px !important;
    font-weight: 700 !important;

    text-transform: uppercase !important;

    box-shadow: none !important;
}


/* hover par bhi color same rahe */

body .main-content
#simpletabledelete
table.eqb-paper-table
tr.eqb-table-header-row:hover > th,

body .main-content
#simpletabledelete
table.eqb-paper-table
tr.eqb-table-header-row:hover > td {

    background: rgba(31, 119, 110, 0.10) !important;

    color: #6B7280 !important;
    /* color: #FFFFFF !important; */
}
/* Exam Question Bank header text always white */

body .main-content
#simpletabledelete
table.eqb-paper-table
tr.eqb-table-header-row > th,

body .main-content
#simpletabledelete
table.eqb-paper-table
tr.eqb-table-header-row > td,

body .main-content
#simpletabledelete
table.eqb-paper-table
tr.eqb-table-header-row:hover > th,

body .main-content
#simpletabledelete
table.eqb-paper-table
tr.eqb-table-header-row:hover > td {
    color: #FFFFFF !important;
}
#question_form select option:checked {
    background-color: #17675F !important;
    color: #ffffff !important;
}

#question_form select option:hover,
#question_form select option:focus {
    background-color: #17675F !important;
    color: #ffffff !important;
}
option:hover {
    background: #17675F !important;
}
.green-select-dropdown
.select2-results__option--highlighted[aria-selected] {
    background-color: #17675F !important;
    color: #ffffff !important;
}

.green-select-dropdown
.select2-results__option[aria-selected="true"] {
    background-color: #17675F !important;
    color: #ffffff !important;
}
.green-select-dropdown
.select2-results__option--highlighted[aria-selected],
.green-select-dropdown
.select2-results__option[aria-selected="true"] {
    background-color: #17675F !important;
    color: #ffffff !important;
}
</style>

<div  style="display:flex; justify-content:space-between; align-items:center; width:100%;margin-bottom:30px;">
     <div style="display:flex; align-items:center;">
            <h4 style="margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: #2d2d2d;
    line-height: 1.2;">
                {{$course_name}} <span style="font-size:14px; font-weight:400; opacity:.85;">(<?=$name;?>)</span>
            </h4>
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
            {{-- <?php if($sch_id != 0){ ?>
                <button type="button" onclick="adm_go_back('<?=$sch_id;?>')" class="btn btn-sm eqb-header-btn"><i class="fa fa-arrow-left"></i> Go Back</button>
            <?php }else{?>
                <button type="button" onclick="history.back()" class="btn btn-sm eqb-header-btn"><i class="fa fa-arrow-left"></i> Go Back</button>
            <?php } ?> --}}
            <a href="/exam-sections" class="btn btn-sm eqb-header-btn--solid"><i class="fa fa-plus"></i> Add Exam Section</a>
        </div>
</div>

<div class="card eqb-card">
    <div class="card-header eqb-card-header" style="display: none;">
        <div>
            <h4 style="margin:0; font-weight:700; font-size:16px; letter-spacing:.2px;">
                {{$course_name}} <span style="font-size:14px; font-weight:400; opacity:.85;">(<?=$name;?>)</span>
            </h4>
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
            <?php if($sch_id != 0){ ?>
                <button type="button" onclick="adm_go_back('<?=$sch_id;?>')" class="btn btn-sm eqb-header-btn"><i class="fa fa-arrow-left"></i> Go Back</button>
            <?php }else{?>
                <button type="button" onclick="history.back()" class="btn btn-sm eqb-header-btn"><i class="fa fa-arrow-left"></i> Go Back</button>
            <?php } ?>
            <a href="/exam-sections" class="btn btn-sm eqb-header-btn--solid"><i class="fa fa-plus"></i> Add Exam Section</a>
        </div>
    </div>
    <div class="card-body eqb-card-body">
    <form enctype="multipart/form-data" method="post" name="question_form"  id="question_form">


    <input type="hidden" name="co_id" id="co_id" value="<?php echo $co_id;?>" >
    <input type="hidden" name="course_id" id="course_id" value="<?php echo $course_id;?>" >
    <input type="hidden" name="countoptions" id="countoptions" value="4" >
    <input type="hidden" name="adm_sch_id" id="adm_sch_id" value="<?=$sch_id;?> " >
    <input type="hidden" name="countexamtype" id="countexamtype"  value="1">
    <input type="hidden" name="adm_exam_id" id="adm_exam_id"  value="<?=$exam_id;?>">
    <input type="hidden"   name="current_date_time" id="current_date_time" >
    <input type="hidden"   name="dashboard" id="dashboard" value="<?=$dashboard?>" >

     @csrf

   <div class="question-form-box">
   {{-- <div class="eqb-form-divider">
       <h6 class="eqb-form-section-title"><i class="fa fa-plus-circle" style="margin-right:7px;"></i>Add New Question</h6>
   </div> --}}

    <div style="display:none">
        <input type="hidden" name="edit_id" id="edit_id" class="form-control">
        <input type="hidden" name="select_option_val" id="select_option_val">
    </div>

    <div style="display:none">
        <input type="text" name="master_exam_section" id="master_exam_section" class="form-control form-control-md" maxlength="500">
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Exam Type</label>
            <select onchange="exam_type_func(this)" name="exam_type" id="exam_type" class="form-control" required="required">
    <option value="1">Multiple-Choice Questions (MCQs)</option>
    <option value="2">Short Answer Questions</option>
    <option value="3">True/False</option>
    <option value="4">Long Answer Questions</option>
</select>
        </div>

        <div class="form-group col-md-6" id="file_upload_td">
            <label>Upload Image</label>
            <input type="file" class="form-control-file" id="file" name="file" accept=".jpg, .jpeg, .png">
        </div>
    </div>

    <?php if(!isset($dashboard)){ ?>
        <div class="form-row" style="display:none">
            <div class="form-group col-md-6">
                <label>Exam Name</label>
                <select name="exam_id" id="exam_id" class="form-control" required="required">
                    @foreach($exam_bank as $exam_rec)
                        <option value="{{$exam_rec->id}}">{{$exam_rec->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    <?php } ?>

    <div class="form-row">
        <div class="form-group col-md-12">
            <label>Question</label>
            <textarea class="form-control" rows="3" name="qname" id="qname" placeholder="Add Question" maxlength="2000"></textarea>
        </div>

        <div class="form-group col-md-12">
            <div id="preview_image" style="padding-top: 6px; width:max-content;"></div>
            <input hidden type="text" class="form-control" id="file-name" name="file-name">
        </div>
    </div>

    <div class="form-row" id="wordlimit" style="display:none;">
        <div class="form-group col-md-6">
            <label>Word Limit</label>
            <input type="number" name="word_limit" id="word_limit" class="form-control form-control-md">
        </div>

        <div class="form-group col-md-6 d-flex align-items-end">
            <div id="lq_disclaimer" class="eqb-text-disclaimer" style="display:none;">
                Disclaimer: Only 1000 word limit allowed
            </div>
        </div>
    </div>

    <div class="form-row" id="lq_file_require" style="display:none">
        <div class="form-group col-md-6">
            <label>File Required</label>
            <select name="file_require" id="file_require" class="form-control" required="required">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>
    </div>

    <div class="form-row" id="sqwordlimit" style="display:none;">
        <div class="form-group col-md-6">
            <label>Word Limit</label>
            <input type="number" name="sq_word_limit" id="sq_word_limit" class="form-control form-control-md">
        </div>

        <div class="form-group col-md-6 d-flex align-items-end">
            <div id="sq_disclaimer" class="eqb-text-disclaimer" style="display:none;">
                Disclaimer: Only 500 word limit allowed
            </div>
        </div>
    </div>

    <div class="form-row" id="sq_file_require" style="display:none">
        <div class="form-group col-md-6">
            <label>File Required</label>
            <select name="sqfile_require" id="sqfile_require" class="form-control" required="required">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6" id="a">
            <label>Option A</label>
            <input type="text" name="option_arr[]" id="option_a" class="form-control form-control-md" maxlength="1000">
        </div>

        <div class="form-group col-md-6" id="b">
            <label>Option B</label>
            <input type="text" name="option_arr[]" id="option_b" class="form-control form-control-md" maxlength="1000">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6" id="c">
            <label>Option C</label>
            <input type="text" name="option_arr[]" id="option_c" class="form-control" maxlength="1000">
        </div>

        <div class="form-group col-md-6" id="d">
            <label>Option D</label>
            <input type="text" name="option_arr[]" id="option_d" class="form-control form-control-md keypress" maxlength="1000">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6" id="e" style="display:none;">
            <label>Option E</label>
            <input type="text" name="option_arr[]" id="option_e" class="form-control form-control-md keypress" maxlength="1000">
        </div>

        <div class="form-group col-md-6" id="f" style="display:none;">
            <label>Option F</label>
            <input type="text" name="option_arr[]" id="option_f" class="form-control form-control-md keypress" maxlength="1000">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6" id="g" style="display:none;">
            <label>Option G</label>
            <input type="text" name="option_arr[]" id="option_g" class="form-control form-control-md keypress" maxlength="1000">
        </div>

        <div class="form-group col-md-6" id="h" style="display:none;">
            <label>Option H</label>
            <input type="text" name="option_arr[]" id="option_h" class="form-control form-control-md keypress" maxlength="1000">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6" id="i" style="display:none;">
            <label>Option I</label>
            <input type="text" name="option_arr[]" id="option_i" class="form-control form-control-md keypress" maxlength="1000">
        </div>
    </div>

    <div class="form-row" id="correctanswer">
        <div class="form-group col-md-6">
            <label>Correct Answer</label>
            <select name="correct_option" id="correct_option" class="form-control">
                <option value="">Select</option>
                <option>A</option>
                <option>B</option>
                <option>C</option>
                <option>D</option>
                <option>E</option>
                <option>F</option>
                <option>G</option>
                <option>H</option>
                <option>I</option>
            </select>
        </div>
    </div>

    <div class="form-row" id="truefalse" style="display:none;">
        <div class="form-group col-md-6">
            <label>Correct Answer</label>
            <select name="true_false" class="form-control">
                <option value="T">True</option>
                <option value="F">False</option>
            </select>
        </div>
    </div>

    <div class="form-row mt-2">
        <div class="form-group col-md-2">
            <button type="button" class="btn btn-primary w-100 eqb-btn-submit" id="btnsubmit" onclick="add_question()"><i class="fa fa-check" style="margin-right:5px;"></i>Submit</button>
        </div>

        <div class="form-group col-md-2">
            <button type="button" class="btn btn-outline-secondary w-100 eqb-btn-reset" onclick="add_question_reset()"><i class="fa fa-refresh" style="margin-right:5px;"></i>Reset</button>
        </div>
    </div>

</div>
    </form>
     <div id="error_msg" class="msg-div" style="display:none;"><span></span></div>
     <div id="success_msg" class="msg-div" style="display:none;"><span></span></div>
    </div>
</div>
    {{-- {{ ddd($exam_questinbank) }} --}}
 <div id="saved_msg_div_file" class="eqb-alert-success" style="display:none;"><span><i class="fa fa-check-circle"></i> Question Added Successfully In Exam</span></div>
    <div id="saved_msg_div_delete" class="eqb-alert-success" style="display:none;"><span><i class="fa fa-check-circle"></i> Question Deleted Successfully</span></div>
<!-- ///////////////////////////////////////////////////////////////////////////////// -->
<?php    if($exam_questinbank != 1){ ?>
    <div class="card mt-3 eqb-card">

        <div class="card-header eqb-card-header">
            <h5 style="margin:0; font-weight:700; letter-spacing:.2px;"><i class="fa fa-list-alt" style="margin-right:8px; opacity:.85;"></i>Exam Question Bank</h5>
            <span id="ques_count" class="eqb-badge-pill">
                <i class="fa fa-check-circle" style="margin-right:4px;"></i>{{ count($exam_questinbank) }} Questions
            </span>
        </div>

    <div class="card-body eqb-card-body--compact" style="background:#fff;">
      <?php
        function display_options($option_name,$option_text,$corr_opt) {
        if (trim(strip_tags($option_text ?? '')) === '') {
            return;
        }
        $is_correct = ($option_name == $corr_opt);
        ?>
               <div class="eqb-option-item <?= $is_correct ? 'eqb-option-item--correct' : '' ?>">
                <strong>({{$option_name}})</strong><?= $is_correct ? '<i class="fa fa-check"></i>' : '' ?><br>
                {{$option_text}}
              </div>
       <?php
        }

        function render_exam_section_cell($quiz_id, $current_section_id, $examSectionResults, $rowspan = '') {
            $current_name = 'Not Selected';
            foreach ($examSectionResults as $row) {
                if ($row->id == $current_section_id) {
                    $current_name = $row->section_name;
                    break;
                }
            }
            $rowspan_attr = $rowspan ? ' rowspan="'.$rowspan.'"' : '';
            ob_start();
        ?>
        <td class="eqb-section-cell"<?= $rowspan_attr ?>>
            <span class="eqb-section-label">Section</span>
            <div class="eqb-section-box">
                <div class="eqb-section-name" id="section_name_<?= $quiz_id ?>" title="<?= htmlspecialchars($current_name, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($current_name, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary eqb-section-change-btn"
                    onclick="openSectionModal(<?= (int)$quiz_id ?>, <?= (int)$current_section_id ?>)">
                    <i class="fa fa-search"></i> Change Section
                </button>
            </div>
            <div id="saved_div<?= $quiz_id ?>" class="eqb-saved-msg" style="display:none;">Saved</div>
        </td>
        <?php
            return ob_get_clean();
        }

       ?>
       {{-- {{ dd($exam_questinbank) }} --}}
       <div class="card-body table-scroll" style="padding: 0px;">
<table id="simpletabledelete" class="table table-bordered" style="width: 100% !important; display: table !important; background:#fff;border: none;">
    <tr>
        <td style="padding:0 !important; border:none;">
            <div class="eqb-paper-block">
            <div class="eqb-paper-block-title">Part 1 — Multiple Choice Questions (MCQ)</div>
            <table class="eqb-paper-table" width="100%">
            <colgroup>
                <col class="eqb-col-num">
                <col span="5">
                <col class="eqb-col-meta">
                <col class="eqb-col-meta">
                <col class="eqb-col-section">
                <col class="eqb-col-action" span="2">
                <col class="eqb-col-upload" span="2">
            </colgroup>
  <tr class="eqb-table-header-row">
    <th scope="col" class="eqb-num-col">#</th>
    <th colspan="5">MCQ Question</th>
    <th class="eqb-meta-col"></th>
    <th class="eqb-meta-col"></th>
    <th scope="col" class="eqb-section-col">Exam Section</th>
    <th colspan="2">Action</th>
    <th colspan="2" class="eqb-upload-col">Uploads</th>
  </tr>
      <?php
      $count=1;

      ?>
      @foreach($exam_questinbank as $exam_bank_rec)
      <?php
if($exam_bank_rec->exam_ques_type == 1){
      ?>

      <tr>
      <th scope="row" class="eqb-num-cell" style="text-align: center" rowspan="2">{{$count}}</th>
      <td colspan="5" class="eqb-question-cell">
            <div class="eqb-question-content">
            @if($exam_bank_rec->question != strip_tags($exam_bank_rec->question))
                {!! $exam_bank_rec->question !!}
            @else
                {{ $exam_bank_rec->question }}
            @endif
            </div>
        </td>
      <td class="eqb-meta-cell" rowspan="2">—</td>
      <td class="eqb-meta-cell" rowspan="2">—</td>
      <?= render_exam_section_cell($exam_bank_rec->id, $exam_bank_rec->exam_section_id, $examSectionResults, 2); ?>
      <td class="eqb-action-cell" colspan="2" rowspan="2">
        <div class="eqb-action-btns">
          <button type="button" class="btn btn-sm btn-outline-primary" onclick="edit_question1(
    '<?= $exam_bank_rec->exam_question_bank_id; ?>',
    '<?= $exam_bank_rec->oa; ?>',
    '<?= $exam_bank_rec->ob; ?>',
    '<?= $exam_bank_rec->oc; ?>',
    '<?= $exam_bank_rec->od; ?>',
    '<?= $exam_bank_rec->oe; ?>',
    '<?= $exam_bank_rec->correct; ?>',
    '<?= $exam_bank_rec->exam_ques_type; ?>',
    '<?= $exam_bank_rec->word_limit; ?>',
    '<?= $exam_bank_rec->file_required ?? ''; ?>',
    '<?= $exam_bank_rec->option_f ?? ''; ?>',
    '<?= $exam_bank_rec->og ?? ''; ?>',
    '<?= $exam_bank_rec->oh ?? ''; ?>',
    '<?= $exam_bank_rec->oi ?? ''; ?>',
    '<?= $exam_bank_rec->upload_file ?? ''; ?>'
    )"><i class="fa fa-edit"></i> Edit</button>
          <button type="button" class="btn btn-sm btn-outline-danger" onclick="delete_question_exam('<?=$exam_bank_rec->id ;?>', {{ count($exam_questinbank) }})"><i class="fa fa-trash"></i> Remove</button>
        </div>
        <input type="text" name="ques" id="quest<?=$exam_bank_rec->exam_question_bank_id;?>" class="form-control" value="{{$exam_bank_rec->question}}" style="display:none;">
      </td>
      <td colspan="2" class="eqb-upload-cell" rowspan="2">
        <span class="eqb-upload-label">Upload</span>
        <?php if (isset($exam_bank_rec->upload_file) && !empty($exam_bank_rec->upload_file)): ?>
        <div class="eqb-upload-wrap" id="download_div">
          <a href="{{ config('app.app_url') . 'docs/' . $exam_bank_rec->upload_file }}"
           style="text-decoration: none; color: inherit" target="_blank" title="View Image">
            <img src="{{ config('app.app_url') . 'docs/' . $exam_bank_rec->upload_file }}" alt="Upload" />
          </a>
        </div>
        <?php else: ?>
        <span style="font-size:11px;color:#adb5bd;">—</span>
        <?php endif; ?>
      </td>

      </tr>

      <tr>
        <td colspan="5" class="eqb-options-cell">
        <div class="eqb-options-grid">
        <?=display_options('A',$exam_bank_rec->oa,$exam_bank_rec->correct);?>
        <?=display_options('B',$exam_bank_rec->ob,$exam_bank_rec->correct);?>
        <?=display_options('C',$exam_bank_rec->oc,$exam_bank_rec->correct);?>
        <?=display_options('D',$exam_bank_rec->od,$exam_bank_rec->correct);?>
        <?=display_options('E',$exam_bank_rec->oe,$exam_bank_rec->correct);?>
        <?=display_options('F',$exam_bank_rec->option_f,$exam_bank_rec->correct);?>
        <?=display_options('G',$exam_bank_rec->og,$exam_bank_rec->correct);?>
        <?=display_options('H',$exam_bank_rec->oh,$exam_bank_rec->correct);?>
        <?=display_options('I',$exam_bank_rec->oi,$exam_bank_rec->correct);?>
        </div>
      </td>
    </tr>

     <?php
    $count++;
}
    ?>
      @endforeach
            </table>
            </div>
        </td>
    </tr>
    {{-- Part 2: True / False --}}
    <tr>
        <td style="padding:0 !important; border:none;">
            <div class="eqb-paper-block">
            <div class="eqb-paper-block-title">Part 2 — True / False</div>
            <table class="eqb-paper-table" width="100%">
            <colgroup>
                <col class="eqb-col-num">
                <col span="5">
                <col class="eqb-col-meta">
                <col class="eqb-col-meta">
                <col class="eqb-col-section">
                <col class="eqb-col-action" span="2">
                <col class="eqb-col-upload" span="2">
            </colgroup>
    <tr class="eqb-table-header-row">
      <th scope="col" class="eqb-num-col">#</th>
      <th colspan="5">True / False</th>
      <th class="eqb-meta-col">Correct</th>
      <th class="eqb-meta-col"></th>
      <th scope="col" class="eqb-section-col">Exam Section</th>
      <th colspan="2">Action</th>
      <th colspan="2" class="eqb-upload-col">Uploads</th>
    </tr>
    <?php $count = 1; ?>
    @foreach($exam_questinbank as $exam_bank_rec)
    <?php if($exam_bank_rec->exam_ques_type == 3){ ?>
    <tr>
    <th scope="row" class="eqb-num-cell">{{$count}}</th>
    <td colspan="5" class="eqb-question-cell">
        <div class="eqb-question-content">
        @if($exam_bank_rec->question != strip_tags($exam_bank_rec->question))
            {!! $exam_bank_rec->question !!}
        @else
            {{ $exam_bank_rec->question }}
        @endif
        </div>
    </td>
    <td class="eqb-meta-cell">{{$exam_bank_rec->correct}}</td>
    <td class="eqb-meta-cell">—</td>
    <?= render_exam_section_cell($exam_bank_rec->id, $exam_bank_rec->exam_section_id, $examSectionResults); ?>
    <td class="eqb-action-cell" colspan="2">
    <div class="eqb-action-btns">
    <button type="button" class="btn btn-sm btn-outline-primary" onclick="edit_question1(
    '<?= $exam_bank_rec->exam_question_bank_id; ?>',
    '<?= $exam_bank_rec->oa; ?>',
    '<?= $exam_bank_rec->ob; ?>',
    '<?= $exam_bank_rec->oc; ?>',
    '<?= $exam_bank_rec->od; ?>',
    '<?= $exam_bank_rec->oe; ?>',
    '<?= $exam_bank_rec->correct; ?>',
    '<?= $exam_bank_rec->exam_ques_type; ?>',
    '<?= $exam_bank_rec->word_limit; ?>',
    '<?= $exam_bank_rec->file_required ?? ''; ?>',
    '<?= $exam_bank_rec->option_f ?? ''; ?>',
    '<?= $exam_bank_rec->og ?? ''; ?>',
    '<?= $exam_bank_rec->oh ?? ''; ?>',
    '<?= $exam_bank_rec->oi ?? ''; ?>',
    '<?= $exam_bank_rec->upload_file ?? ''; ?>'
)"><i class="fa fa-edit"></i> Edit</button>
    <button type="button" class="btn btn-sm btn-outline-danger" onclick="delete_question_exam('<?=$exam_bank_rec->id ;?>', {{ count($exam_questinbank) }})"><i class="fa fa-trash"></i> Remove</button>
    </div>
    <input type="text" name="ques" id="quest<?=$exam_bank_rec->exam_question_bank_id;?>" class="form-control" value="{{$exam_bank_rec->question}}" style="display:none;">
    </td>
    <td colspan="2" class="eqb-upload-cell">
        <span class="eqb-upload-label">Upload</span>
        <?php if (isset($exam_bank_rec->upload_file) && !empty($exam_bank_rec->upload_file)): ?>
            <div class="eqb-upload-wrap" id="download_div">
                <a href="{{ config('app.app_url') . 'docs/' . $exam_bank_rec->upload_file }}"
                style="text-decoration: none; color: inherit" target="_blank" title="View Image">
                    <img src="{{ config('app.app_url') . 'docs/' . $exam_bank_rec->upload_file }}" alt="Upload" />
                </a>
            </div>
        <?php else: ?>
            <span style="font-size:11px;color:#adb5bd;">—</span>
        <?php endif; ?>
    </td>
  </tr>
   <?php $count++; } ?>
  @endforeach
      </table>
      </div>
    </td>
  </tr>
    {{-- Part 3: Short Question --}}
    <tr>
        <td style="padding:0 !important; border:none;">
            <div class="eqb-paper-block">
            <div class="eqb-paper-block-title">Part 3 — Short Questions</div>
            <table class="eqb-paper-table" width="100%">
            <colgroup>
                <col class="eqb-col-num">
                <col span="5">
                <col class="eqb-col-meta">
                <col class="eqb-col-meta">
                <col class="eqb-col-section">
                <col class="eqb-col-action" span="2">
                <col class="eqb-col-upload" span="2">
            </colgroup>
<tr class="eqb-table-header-row">
  <th scope="col" class="eqb-num-col">#</th>
  <th colspan="5">Short Question</th>
  <th class="eqb-meta-col">Word Limit</th>
  <th class="eqb-meta-col">Negative Marks %</th>
  <th scope="col" class="eqb-section-col">Exam Section</th>
  <th colspan="2">Action</th>
  <th colspan="2" class="eqb-upload-col">Uploads</th>
  </tr>
  <?php $count = 1; ?>
@foreach($exam_questinbank as $exam_bank_rec)
<?php if($exam_bank_rec->exam_ques_type == 2){ ?>

<tr>
<th scope="row" class="eqb-num-cell">{{$count}}</th>
<td colspan="5" class="eqb-question-cell">
    <div class="eqb-question-content">
    @if($exam_bank_rec->question != strip_tags($exam_bank_rec->question))
        {!! $exam_bank_rec->question !!}
    @else
        {{ $exam_bank_rec->question }}
    @endif
    </div>
</td>
<td class="eqb-meta-cell">{{$exam_bank_rec->word_limit}}</td>
<td class="eqb-meta-cell">{{$exam_bank_rec->neg_marks}}</td>
<?= render_exam_section_cell($exam_bank_rec->id, $exam_bank_rec->exam_section_id, $examSectionResults); ?>
<td class="eqb-action-cell" colspan="2">
<div class="eqb-action-btns">
<button type="button" class="btn btn-sm btn-outline-primary" onclick="edit_question1(
    '<?= $exam_bank_rec->exam_question_bank_id; ?>',
    '<?= $exam_bank_rec->oa; ?>',
    '<?= $exam_bank_rec->ob; ?>',
    '<?= $exam_bank_rec->oc; ?>',
    '<?= $exam_bank_rec->od; ?>',
    '<?= $exam_bank_rec->oe; ?>',
    '<?= $exam_bank_rec->correct; ?>',
    '<?= $exam_bank_rec->exam_ques_type; ?>',
    '<?= $exam_bank_rec->word_limit; ?>',
    '<?= $exam_bank_rec->file_required ?? ''; ?>',
    '<?= $exam_bank_rec->option_f ?? ''; ?>',
    '<?= $exam_bank_rec->og ?? ''; ?>',
    '<?= $exam_bank_rec->oh ?? ''; ?>',
    '<?= $exam_bank_rec->oi ?? ''; ?>',
    '<?= $exam_bank_rec->upload_file ?? ''; ?>'
)"><i class="fa fa-edit"></i> Edit</button>
<button type="button" class="btn btn-sm btn-outline-danger" onclick="delete_question_exam('<?=$exam_bank_rec->id ;?>', {{ count($exam_questinbank) }})"><i class="fa fa-trash"></i> Remove</button>
</div>
<input type="text" name="ques" id="quest<?=$exam_bank_rec->exam_question_bank_id;?>" class="form-control" value="{{$exam_bank_rec->question}}" style="display:none;">
</td>
<td colspan="2" class="eqb-upload-cell">
    <span class="eqb-upload-label">Upload</span>
    <?php if (isset($exam_bank_rec->upload_file) && !empty($exam_bank_rec->upload_file)): ?>
        <div class="eqb-upload-wrap" id="download_div">
            <a href="{{ config('app.app_url') . 'docs/' . $exam_bank_rec->upload_file }}"
            style="text-decoration: none; color: inherit" target="_blank" title="View Image">
                <img src="{{ config('app.app_url') . 'docs/' . $exam_bank_rec->upload_file }}" alt="Upload" />
            </a>
        </div>
    <?php else: ?>
        <span style="font-size:11px;color:#adb5bd;">—</span>
    <?php endif; ?>
</td>

</tr>
<?php $count++; } ?>
@endforeach
            </table>
            </div>
        </td>
    </tr>
    {{-- Part 4: Long Question --}}
<tr>
    <td style="padding:0 !important; border:none;">
        <div class="eqb-paper-block">
        <div class="eqb-paper-block-title">Part 4 — Long Questions</div>
        <table class="eqb-paper-table" width="100%">
        <colgroup>
            <col class="eqb-col-num">
            <col span="5">
            <col class="eqb-col-meta">
            <col class="eqb-col-meta">
            <col class="eqb-col-section">
            <col class="eqb-col-action" span="2">
            <col class="eqb-col-upload" span="2">
        </colgroup>
          <tr class="eqb-table-header-row">
            <th scope="col" class="eqb-num-col">#</th>
            <th colspan="5">Long Question</th>
            <th class="eqb-meta-col">Word Limit</th>
            <th class="eqb-meta-col">Negative Marks %</th>
            <th scope="col" class="eqb-section-col">Exam Section</th>
            <th colspan="2">Action</th>
            <th colspan="2" class="eqb-upload-col">Uploads</th>
          </tr>

            <?php $count = 1; ?>
            @foreach($exam_questinbank as $exam_bank_rec)
            <?php if($exam_bank_rec->exam_ques_type == 4){ ?>
           <tr>
            <th scope="row" class="eqb-num-cell">{{$count}}</th>
            <td colspan="5" class="eqb-question-cell">
    <div class="eqb-question-content">
    @if($exam_bank_rec->question != strip_tags($exam_bank_rec->question))
        {!! $exam_bank_rec->question !!}
    @else
        {{ $exam_bank_rec->question }}
    @endif
    </div>
</td>
            <td class="eqb-meta-cell">{{$exam_bank_rec->word_limit}}</td>
            <td class="eqb-meta-cell">{{$exam_bank_rec->neg_marks}}</td>
            <?= render_exam_section_cell($exam_bank_rec->id, $exam_bank_rec->exam_section_id, $examSectionResults); ?>
            <td class="eqb-action-cell" colspan="2">
            <div class="eqb-action-btns">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="edit_question1(
                '<?= $exam_bank_rec->exam_question_bank_id; ?>',
                '<?= $exam_bank_rec->oa; ?>',
                '<?= $exam_bank_rec->ob; ?>',
                '<?= $exam_bank_rec->oc; ?>',
                '<?= $exam_bank_rec->od; ?>',
                '<?= $exam_bank_rec->oe; ?>',
                '<?= $exam_bank_rec->correct; ?>',
                '<?= $exam_bank_rec->exam_ques_type; ?>',
                '<?= $exam_bank_rec->word_limit; ?>',
                '<?= $exam_bank_rec->file_required ?? ''; ?>',
                '<?= $exam_bank_rec->option_f ?? ''; ?>',
                '<?= $exam_bank_rec->og ?? ''; ?>',
                '<?= $exam_bank_rec->oh ?? ''; ?>',
                '<?= $exam_bank_rec->oi ?? ''; ?>',
                '<?= $exam_bank_rec->upload_file ?? ''; ?>'
            )"><i class="fa fa-edit"></i> Edit</button>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="delete_question_exam('<?=$exam_bank_rec->id ;?>', {{ count($exam_questinbank) }})"><i class="fa fa-trash"></i> Remove</button>
            </div>
            <input type="text" name="ques" id="quest<?=$exam_bank_rec->exam_question_bank_id;?>" class="form-control" value="{{$exam_bank_rec->question}}" style="display:none;">
            </td>
            <td colspan="2" class="eqb-upload-cell">
                <span class="eqb-upload-label">Upload</span>
                <?php if (isset($exam_bank_rec->upload_file) && !empty($exam_bank_rec->upload_file)): ?>
                    <div class="eqb-upload-wrap" id="download_div">
                        <a href="{{ config('app.app_url') . 'docs/' . $exam_bank_rec->upload_file }}"
                        style="text-decoration: none; color: inherit" target="_blank" title="View Image">
                            <img src="{{ config('app.app_url') . 'docs/' . $exam_bank_rec->upload_file }}" alt="Upload" />
                        </a>
                    </div>
                <?php else: ?>
                    <span style="font-size:11px;color:#adb5bd;">—</span>
                <?php endif; ?>
            </td>
          </tr>

           <?php $count++; } ?>
          @endforeach
        </table>
        </div>
    </td>
</tr>
</table>
</div>
</div>
<?php } ?>

<div class="card mt-3 eqb-card">
    <div class="card-header eqb-card-header">
        <h5 style="margin:0; font-weight:700; font-size:15px; letter-spacing:.2px;"><i class="fa fa-database" style="margin-right:8px; opacity:.85;"></i>Course Question Bank</h5>
        <span class="eqb-warning-badge"><i class="fa fa-exclamation-triangle" style="margin-right:5px;"></i>Repeated questions cannot be removed from the Bank</span>
    </div>
    <div class="card-body eqb-card-body" style="overflow-x:auto; background:#fff;">
        <?php
        $cqbSections = [
            ['type' => 1, 'part' => 1, 'title' => 'Multiple Choice Questions (MCQ)', 'mode' => 'mcq'],
            ['type' => 3, 'part' => 2, 'title' => 'True / False', 'mode' => 'tf'],
            ['type' => 2, 'part' => 3, 'title' => 'Short Questions', 'mode' => 'word'],
            ['type' => 4, 'part' => 4, 'title' => 'Long Questions', 'mode' => 'word'],
        ];

        function render_cqb_mcq_options($rec) {
            $options = [
                'A' => $rec->oa ?? '',
                'B' => $rec->ob ?? '',
                'C' => $rec->oc ?? '',
                'D' => $rec->od ?? '',
                'E' => $rec->oe ?? '',
                'F' => $rec->option_f ?? '',
                'G' => $rec->og ?? '',
                'H' => $rec->oh ?? '',
                'I' => $rec->oi ?? '',
            ];
            ob_start();
        ?>
        <td class="eqb-options-cell">
            <div class="eqb-options-grid">
            <?php foreach ($options as $letter => $text):
                if (trim(strip_tags($text)) === '') {
                    continue;
                }
                $is_correct = ($letter === ($rec->correct ?? ''));
            ?>
                <div class="eqb-option-item <?= $is_correct ? 'eqb-option-item--correct' : '' ?>">
                    <strong>(<?= $letter ?>)</strong><?= $is_correct ? ' <i class="fa fa-check"></i>' : '' ?><br>
                    <?php if ($text !== strip_tags($text)): ?>
                        <?= $text ?>
                    <?php else: ?>
                        <?= htmlspecialchars($text, ENT_QUOTES, 'UTF-8') ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            </div>
        </td>
        <?php
            return ob_get_clean();
        }

        function render_cqb_actions($rec) {
            $in_exam = ((int)($rec->count ?? 0)) > 0;
            ob_start();
        ?>
        <div class="eqb-cqb-actions">
            <?php if (!$in_exam): ?>
            <span title="Add to Exam">
                <i class="fa fa-plus fa-lg eqb-icon-add" onclick="add_question_exam('<?= $rec->id ?>')"></i>
            </span>
            <?php else: ?>
            <span class="eqb-cqb-added-badge"><i class="fa fa-check-circle"></i> In Exam</span>
            <?php endif; ?>
            <span title="Edit">
                <i class="fa fa-edit fa-lg eqb-icon-edit" onclick="edit_question('<?= $rec->id ?>','<?= $rec->oa ?>','<?= $rec->ob ?>','<?= $rec->oc ?>','<?= $rec->od ?>','<?= $rec->oe ?>','<?= $rec->correct ?>','<?= $rec->exam_ques_type ?>','<?= $rec->word_limit ?>','<?= $rec->file_required ?>','<?= $rec->option_f ?? '' ?>','<?= $rec->og ?>','<?= $rec->oh ?>','<?= $rec->oi ?>')"></i>
            </span>
            <?php if (($rec->all_count ?? 0) > 0): ?>
            <span class="badge badge-success">Repeated : <?= $rec->all_count ?> Time</span>
            <?php else: ?>
            <span title="Delete">
                <i class="fa fa-trash fa-lg eqb-icon-delete" onclick="delete_question('<?= $rec->id ?>')"></i>
            </span>
            <?php endif; ?>
        </div>
        <?php
            return ob_get_clean();
        }

        function render_cqb_question_cell($rec) {
            ob_start();
        ?>
        <td class="eqb-question-cell">
            <div class="eqb-question-content">
            <?php if ($rec->question !== strip_tags($rec->question)): ?>
                <?= $rec->question ?>
            <?php else: ?>
                <?= htmlspecialchars($rec->question, ENT_QUOTES, 'UTF-8') ?>
            <?php endif; ?>
            </div>
            <?= render_cqb_actions($rec) ?>
            <input type="text" name="ques" id="ques<?= $rec->id ?>" class="form-control" value="<?= htmlspecialchars($rec->question, ENT_QUOTES, 'UTF-8') ?>" style="display:none;">
        </td>
        <?php
            return ob_get_clean();
        }
        ?>
        <div id="simpletable">
        <?php foreach ($cqbSections as $section):
            $sectionItems = array_filter($question_bank, function($r) use ($section) {
                return (int)($r->exam_ques_type ?? 0) === $section['type'];
            });
            $count = 1;
        ?>
        <div class="eqb-paper-block">
            <div class="eqb-paper-block-title">Part <?= $section['part'] ?> — <?= $section['title'] ?></div>
            <?php if (empty($sectionItems)): ?>
                <div class="eqb-cqb-empty">No questions in this category.</div>
            <?php else: ?>
            <table class="table table-bordered eqb-cqb-table<?= $section['mode'] === 'mcq' ? ' eqb-cqb-table--mcq' : '' ?>" width="100%">
            <tr class="eqb-table-header-row">
                <th style="width:4%">#</th>
                <th><?= $section['mode'] === 'mcq' ? 'MCQ Question' : 'Question' ?></th>
                <?php if ($section['mode'] === 'tf'): ?>
                    <th style="width:8%">Correct</th>
                <?php elseif ($section['mode'] === 'word'): ?>
                    <th style="width:10%">Word Limit</th>
                <?php endif; ?>
            </tr>
            <?php foreach ($sectionItems as $question_bank_rec):
                $in_exam = ((int)($question_bank_rec->count ?? 0)) > 0;
                $rowClass = $in_exam ? 'eqb-row-in-exam' : '';
            ?>
            <?php if ($section['mode'] === 'mcq'): ?>
            <tr class="<?= $rowClass ?>">
                <th scope="row" rowspan="2" style="text-align:center; vertical-align:top;">{{ $count }}</th>
                <?= render_cqb_question_cell($question_bank_rec) ?>
            </tr>
            <tr class="<?= $rowClass ?> eqb-cqb-options-row">
                <?= render_cqb_mcq_options($question_bank_rec) ?>
            </tr>
            <?php else: ?>
            <tr class="<?= $rowClass ?>">
                <th scope="row" style="text-align:center">{{ $count }}</th>
                <?= render_cqb_question_cell($question_bank_rec) ?>
                <?php if ($section['mode'] === 'tf'): ?>
                    <td style="text-align:center">{{ $question_bank_rec->correct }}</td>
                <?php else: ?>
                    <td style="text-align:center">{{ $question_bank_rec->word_limit }}</td>
                <?php endif; ?>
            </tr>
            <?php endif; ?>
            <?php $count++; endforeach; ?>
            </table>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        </div>

    </div>
    </div>
</div>

<div class="modal fade" id="eqbSectionModal" tabindex="-1" role="dialog" aria-labelledby="eqbSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content eqb-modal-content">
            <div class="modal-header eqb-modal-header">
                <h5 class="modal-title" id="eqbSectionModalLabel" style="font-weight:700; font-size:15px;"><i class="fa fa-bookmark" style="margin-right:8px;"></i>Select Exam Section</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding:20px;">
                <div class="eqb-search-wrap">
                    <i class="fa fa-search"></i>
                    <input type="text" id="eqbSectionSearch" class="form-control eqb-search-input" placeholder="Search section by name..." autocomplete="off">
                </div>
                <div id="eqbSectionList" class="eqb-section-list mt-3"></div>
            </div>
        </div>
    </div>
</div>
@stop
{{-- ck editor code start --}}
 @section('javascript')
<script>
    // window.CKEDITOR_BASEPATH = "{{ asset('ckeditor/') }}/";
    window.CKEDITOR_BASEPATH = "/ckeditor/";
</script>

{{-- <script src="{{ asset('ckeditor/ckeditor.js') }}"></script> --}}
<script src="/ckeditor/ckeditor.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (
        typeof CKEDITOR !== 'undefined' &&
        document.getElementById('qname') &&
        !CKEDITOR.instances.qname
    ) {
        CKEDITOR.replace('qname', {
            height: 180,
            versionCheck: false,
            removePlugins: 'scayt,wsc'
        });
    }
});
</script>
{{-- Your remaining JavaScript stays below --}}





{{-- ck editior code end --}}

<script>
var eqbExamSections = {!! json_encode(collect($examSectionResults)->map(function($r) {
    return ['id' => $r->id, 'name' => $r->section_name];
})->values()) !!};
var eqbSectionModalQuizId = null;
var eqbSectionModalCurrentId = null;

function openSectionModal(quizId, currentSectionId) {
    eqbSectionModalQuizId = quizId;
    eqbSectionModalCurrentId = currentSectionId;
    $('#eqbSectionSearch').val('');
    renderSectionList('');
    $('#eqbSectionModal').modal('show');
    setTimeout(function() { $('#eqbSectionSearch').focus(); }, 300);
}

function renderSectionList(filter) {
    var q = (filter || '').toLowerCase().trim();
    var html = '';
    var count = 0;
    eqbExamSections.forEach(function(s) {
        if (q && s.name.toLowerCase().indexOf(q) === -1) return;
        count++;
        var activeClass = (parseInt(s.id, 10) === parseInt(eqbSectionModalCurrentId, 10)) ? ' is-active' : '';
        html += '<button type="button" class="eqb-section-list-item'+activeClass+'" data-id="'+s.id+'">'+$('<div>').text(s.name).html()+'</button>';
    });
    if (!count) {
        html = '<div class="eqb-section-list-empty">No section found</div>';
    }
    $('#eqbSectionList').html(html);
}

$(document).on('click', '.eqb-section-list-item', function() {
    pickExamSection($(this).data('id'), $(this).text());
});

function pickExamSection(sectionId, sectionName) {
    var quizId = eqbSectionModalQuizId;
    $('#eqbSectionModal').modal('hide');
    updateExamSection(sectionId, quizId, sectionName);
}

function updateExamSection(sectionId, quizId, sectionName) {
    var fd = new FormData();
    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        url: '{{url('teacher/updateexamsection')}}/' + sectionId + '/' + quizId,
        data: fd,
        processData: false,
        contentType: false,
        success: function() {
            $('#section_name_' + quizId).text(sectionName).attr('title', sectionName);
            $('#saved_div' + quizId).show();
            setTimeout(function() { $('#saved_div' + quizId).hide(); }, 2000);
        }
    });
}

$(document).on('input', '#eqbSectionSearch', function() {
    renderSectionList(this.value);
});

function reloadQuestionBankTable() {
    $("#simpletabledelete").load(window.location + " #simpletabledelete");
}

$('#correct_option').on('change', function() {
    var correct_option = this.value.toLowerCase();
    var option_details = $("#option_"+correct_option).val();
   // alert(correct_option+"___"+option_details);
    if(option_details.trim()==""){
        alert('Please enter detail of option '+correct_option);
        $(this).val('');
    }
});

  $( document ).ready(function() {
const formatter = new Intl.DateTimeFormat('eng', { month: 'short' });
const month1 = formatter.format(new Date());

setInterval(function(){
  var currentdate = new Date();
  var year = currentdate.getFullYear();
  year = year.toString().substr(-2);
  var datetime = year + "-"
              + month1  + "-"
              + currentdate.getDate() + " "
              + currentdate.getHours() + ":"
              + currentdate.getMinutes() + ":"
              + currentdate.getSeconds();

  // var dt = new Date();
  // var time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
  $("#current_date_time").val(datetime);
},3000);
//alert(time);
});
function go_back(co_id,course_id){

  window.location = '<?=url('/teacher/online_exam/')?>/'+co_id+'/'+course_id;

}
function adm_go_back(adm_sch_id){
//alert(co_id);
 window.location = '<?=url('/teacher/adm_online_exam/')?>/'+adm_sch_id;

}
function getSection(selectObject,QuizId) {
  var id = selectObject.value;
  //alert(SectionId+"_____"+QuizId);
   //alert(QuizId);
  // return false;
  var fd = new FormData();

            $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:'POST',
                url: '{{url('teacher/updateexamsection')}}/'+id+'/'+QuizId,
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){

            $("#saved_div"+QuizId).show();
          // $("#simpletable").load(window.location + " #simpletable");
          setTimeout(function(){
                  $("#saved_div"+QuizId).hide();

                  }, 2000);

                },
                complete: function(){
                }
            });
}
$(".keypress").keyup(function() {
  var optD = $('#option_d').val();
  var optE = $('#option_e').val();
  var optF = $('#option_f').val();
  var optG = $('#option_g').val();
  var optH = $('#option_h').val();
  var optI = $('#option_i').val();
  if(optD == ''){
    $('#e').hide();
    $('#option_e').val('');
  }else{
    $('#e').show();
  } 
  if(optE == ''){
    $('#f').hide();
    $('#option_f').val('');
  }else{
    $('#f').show();
  }
  if(optF == ''){
    $('#g').hide();
    $('#option_g').val('');
  }else{
    $('#g').show();
  }
  if(optG == ''){
    $('#h').hide();
    $('#option_h').val('');
  }else{
    $('#h').show();
  }
  if(optH == ''){
    $('#i').hide();
    $('#option_i').val('');
  }else{
    $('#i').show();
  }
});


function addmore(){
 var countopt =  $("#countoptions").val();

  if(countopt < 5){
    countopt++;
  }

  $('#e').show();
  $("#countoptions").val(countopt);
}
function removerow(){
  var countopt =  $("#countoptions").val();


 if(countopt > 4){
   countopt--;
  }
$('#e').hide();
$("#option_e").val('');
$("#countoptions").val(countopt);

}
function add_question_reset(){
  // alert('reset');
  $('#edit_id').val('');
  $("#select_option_val").val('');
//   $("#exam_type").val('1');
  // $("#qname").val('');
  if (CKEDITOR.instances.qname) {
    CKEDITOR.instances.qname.setData('');
} else {
    $("#qname").val('');
}
  $("#sq_word_limit").val('');
  $("#correct_option").val('');
  $('input[name="option_arr[]"]').val(''); removeImage();
}
function exam_type_func(sel){
 // alert('ok');
var get_edit_id = parseInt($('#edit_id').val());
if(get_edit_id>0){
    $('#exam_type').val($("#select_option_val").val());
    alert('Quetion Type can not be changed');
    return false;
}

  var value = sel.value;
  $("#countexamtype").val(value);
 // alert(value);
  if(value == 2){
    $('#error_msg span').html("");
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#f').hide();
    $('#g').hide();
    $('#h').hide();
    $('#i').hide();
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').hide();
    $('#wordlimit').hide();
    $('#sqwordlimit').show();
    $('#sq_disclaimer').show();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').show();
    $('#file_upload_td').show();

  }else if(value == 1){
    $('#error_msg span').html("");
    $('#a').show();
    $('#b').show();
    $('#c').show();
    $('#d').show();
    $('#f').show();
    $('#g').show();
    $('#h').show();
    $('#i').show();
    $('#correctanswer').show();
    $('#addremove').show();
    $('#truefalse').hide();
    $('#wordlimit').hide();
    $('#sqwordlimit').hide();
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').hide();
    $('#file_upload_td').show();
  }else if(value == 3){
    $('#error_msg span').html("");
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#f').hide();
    $('#g').hide();
    $('#h').hide();
    $('#i').hide();
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').show();
    $('#wordlimit').hide();
    $('#sqwordlimit').hide();
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').hide();
    $('#file_upload_td').hide();
  }else{
    $('#error_msg span').html("");
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#f').hide();
    $('#g').hide();
    $('#h').hide();
    $('#i').hide();
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').hide();
    $('#wordlimit').show();
    $('#sqwordlimit').hide();
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').show();
    $('#lq_file_require').show();
    $('#sq_file_require').hide();
    $('#file_upload_td').show();
  }
}


function add_question(){

  // ck editor code start

     if (CKEDITOR.instances.qname) {
        CKEDITOR.instances.qname.updateElement();
    }

  // ck editior code end 

    var bi_form = document.querySelector('#question_form');
    var fd = new FormData(bi_form);


  if($("#exam_type").val() == 1){

      var exam_id       = $("#exam_id").val();
      var qname       = $("#qname").val();
            var option_a        = $("#option_a").val();
             var option_b        = $("#option_b").val();
              var option_c        = $("#option_c").val();
               var option_d        = $("#option_d").val();
                var option_e        = $("#option_e").val();
                $('.form-control').removeClass('is-invalid');
        var error = 0;
        $('#error_msg').hide();
        if(qname == ''){
          $('#qname').addClass('is-invalid');
           $('#error_msg span').html('Please provide question!');
                error++;
            } if(option_a== ''){
              $('#option_a').addClass('is-invalid');
              error++;
            }if(option_b== ''){
        $('#option_b').addClass('is-invalid');
        error++;
            }if(option_c == ''){
      $('#option_c').addClass('is-invalid');
      error++;
            }if(option_d== ''){
        $('#option_d').addClass('is-invalid');
        error++;
        }

    if( error > 0 ){
                $('#error_msg').show();
                return false;
            }
  }else if($("#exam_type").val() == 2){

    var exam_id       = $("#exam_id").val();
    var qname       = $("#qname").val();
    var option_a        = $("#option_a").val();
    var sq_word_limit        = $("#sq_word_limit").val();
    $('.form-control').removeClass('is-invalid');
    var error = 0;
    $('#error_msg').hide();
    if(qname == ''){
          $('#qname').addClass('is-invalid');
           $('#error_msg span').html('Please provide question!');
                error++;
            }
            if(sq_word_limit == ''){
          $('#sq_word_limit').addClass('is-invalid');
           $('#error_msg span').html('Please provide Word Limit!');
                error++;
            }

            if( error > 0 ){
                $('#error_msg').show();
                return false;
            }

  }else if($("#exam_type").val() == 3){

var exam_id       = $("#exam_id").val();
var qname       = $("#qname").val();
var option_a        = $("#option_a").val();
var option_b        = $("#option_b").val();
$('.form-control').removeClass('is-invalid');
var error = 0;
$('#error_msg').hide();
if(qname == ''){
      $('#qname').addClass('is-invalid');
       $('#error_msg span').html('Please provide question!');
            error++;
         }
        // if(option_a== ''){
        //   $('#option_a').addClass('is-invalid');
        //   error++;
        // }if(option_b== ''){
        // $('#option_b').addClass('is-invalid');
        // error++;
        //     }
        if( error > 0 ){
            $('#error_msg').show();
            return false;
        }

}else if($("#exam_type").val() == 4){

var exam_id       = $("#exam_id").val();
var qname       = $("#qname").val();
var word_limit        = $("#word_limit").val();

$('.form-control').removeClass('is-invalid');
var error = 0;
$('#error_msg').hide();
if(qname == ''){
      $('#qname').addClass('is-invalid');
       $('#error_msg span').html('Please provide question!');
            error++;
        }
        if(word_limit == ''){
      $('#word_limit').addClass('is-invalid');
       $('#error_msg span').html('Please provide Word Limit!');
            error++;
        }

        if( error > 0 ){
            $('#error_msg').show();
            return false;
        }
}
//alert($("#word_limit").val());

  if($("#word_limit").val() > 1000){
    $('#error_msg').show();
   $('#error_msg span').html('Word Limit cannot be greater than 1000!');
   $("#word_limit").val('');
   setTimeout(function(){
                  $("#error_msg").hide();
      }, 3000);
    //return false;
  }else if($("#sq_word_limit").val() > 500){
    $('#error_msg').show();
   $('#error_msg span').html('Word Limit cannot be greater than 500!');
   $("#sq_word_limit").val('');
   setTimeout(function(){
                  $("#error_msg").hide();
      }, 3000);
  }else{

    var bi_form = document.querySelector('#question_form');
    var fd = new FormData(bi_form);
    //  fd.append("option_a", option_a)
    $.ajax({
        type:'POST',
        url: '{{url('teacher/add_question')}}',
        data:fd,
        processData:false,
        contentType:false,
        success:function(data){

           if(data.message==2){
              //alert('No Question Added in this Exam greater then Total Question');
              alert('No Question Added in Exam greater then Total Question Due to Marks Changed');
              return false;
            }
            if(data.message==1){
              //alert(' Question Update Successfully');
            }

            $("#btnsubmit").addClass("btn-success");
            $("#success_msg").html('Question Add Successfully In Exam');
            $("#saved_msg_div_file").show();
            // $("#qname").val('');
            if (CKEDITOR.instances.qname) {
    CKEDITOR.instances.qname.setData('');
} else {
    $("#qname").val('');
}
            $("#option_a").val('');
            $("#option_b").val('');
            $("#option_c").val('');
            $("#option_d").val('');
            $("#option_e").val('');
            $("#option_f").val('');
            $("#option_g").val('');
            $("#option_h").val('');
            $("#option_i").val('');
            $("#word_limit").val('');
            $("#sq_word_limit").val('');
            $("#lq_file_require").val('0');
            $("#sq_file_require").val('0');
            $('#correct_option').val('');
            $('.form-control').removeClass('is-invalid');
            $("#simpletable").load(window.location + " #simpletable");
            reloadQuestionBankTable();
            setTimeout(function(){
              $("#saved_msg_div_file").hide();
              $("#btnsubmit").html('Submit');
              $("#btnsubmit").removeClass("btn-success");
              add_question_reset();
            }, 2000);
        },
        complete: function(){
        }
    });
  }
}

////// add question in exam///

function add_question_exam(id){

    var exam_id = $("#adm_exam_id").val();
    //alert(exam_id);adm_exam_id
   // var bi_form = document.querySelector('#question_form');
   var fd = new FormData();
    $.ajax({
        type:'get',
        url: '{{url('teacher/exam_question')}}/'+id+'/'+exam_id,
        data:fd,
        processData:false,
        contentType:false,
        success:function(data){
        		if(data.status){
        			$("#saved_msg_div_file").show();
        			 $("#simpletable").load(window.location + " #simpletable");
                reloadQuestionBankTable();
        		}else if(data.message==0){
				        alert('Question already Added in this Exam');
                //alert('No Question Added in Exam greater then Total Question Due to Marks Changed');

        		} else  if(data.message==2){
              alert('No Question Added in Exam greater then Total Question Due to Marks Changed');
              return false;
            }
        		setTimeout(function(){
                    $("#saved_msg_div_file").hide();

                    }, 2000);
        },

        complete: function(){

        }
    });
}

////// delete question in exam///

function delete_question_exam(id, totalQues){
	//alert(id);
   // var tt_new_content_id = $("#tt_new_content_id").val();
   // var bi_form = document.querySelector('#question_form');
   var fd = new FormData();
    $.ajax({
        type:'get',
        url: '{{url('teacher/delete_examquestion')}}/'+id,
        data:fd,
        processData:false,
        contentType:false,
        success:function(data){

        			$("#saved_msg_div_delete").show();
                    count = totalQues-1
        			$("#ques_count").text('Questions Added: '+ count);
        			 reloadQuestionBankTable();
               $("#simpletable").load(window.location + " #simpletable");

        		setTimeout(function(){
                    $("#saved_msg_div_delete").hide();

                    }, 2000);

        },

        complete: function(){

        }
    });
}

////// delete question in exam///

function delete_question(id){
  //alert(id);
   // var tt_new_content_id = $("#tt_new_content_id").val();
   // var bi_form = document.querySelector('#question_form');
   var fd = new FormData();
    $.ajax({
        type:'get',
        url: '{{url('teacher/delete_question')}}/'+id,
        data:fd,
        processData:false,
        contentType:false,
        success:function(data){

              $("#saved_msg_div_delete").show();
               $("#simpletable").load(window.location + " #simpletable");
              reloadQuestionBankTable();

            setTimeout(function(){
                    $("#saved_msg_div_delete").hide();

            }, 2000);
        },

        complete: function(){

        }
    });
}
document.getElementById('file').addEventListener('change', function(event) {
    const fileInput = event.target;
    const file = fileInput.files[0];

    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();

        reader.onload = function(e) {
            const imgSrc = e.target.result;

            const previewHTML = `
                <div style="position: relative; display: inline-block;">
                    <img src="${imgSrc}" width="200px" alt="Preview Image" />
                    <span
                        onclick="removeImage()" 
                        style="
                            position: absolute;
                            top: -8px;
                            right: -8px;
                            background: red;
                            color: white;
                            border-radius: 50%;
                            width: 20px;
                            height: 20px;
                            text-align: center;
                            cursor: pointer;
                            font-weight: bold;
                            line-height: 20px;
                        ">
                        &times;
                    </span>
                </div>
            `;

            document.getElementById('preview_image').innerHTML = previewHTML;
            document.getElementById('file-name').value = ''; // Clear file input
        };

        reader.readAsDataURL(file); // Read image as base64
    } else {
        document.getElementById('preview_image').innerHTML = '';
        document.getElementById('file-name').value = ''; // Clear file input
    }
});
// Add this function to your script
function removeImage() {
    document.getElementById('preview_image').innerHTML = '';
    document.getElementById('file').value = ''; // Clear file input
    document.getElementById('file-name').value = ''; // Clear file input
}
function setQuestionInEditor(quest) {
    if (CKEDITOR.instances.qname) {
        CKEDITOR.instances.qname.setData(quest);
    } else {
        $('#qname').val(quest);
    }
}
function edit_question1(id,a,b,c,d,e,correc,type,word_limit,file_req,f,g,h,i,image){
   
    const baseUrl = "{{ config('app.app_url') }}";
    const fileUrl = baseUrl + "docs/" + image;

    let previewHTML = '';
    if (image) {
        previewHTML = `
            <div style="position: relative; display: inline-block;">
            <a href="${fileUrl}" style="text-decoration: underline !important; color: black" target="_blank">
                <img src="${fileUrl}" width="200px" alt="Download Image" />
            </a>
            <span
                onclick="removeImage()"
                style="
                    position: absolute;
                    top: -8px;
                    right: -8px;
                    background: red;
                    color: white;
                    border-radius: 50%;
                    width: 20px;
                    height: 20px;
                    text-align: center;
                    cursor: pointer;
                    font-weight: bold;
                    line-height: 20px;
                ">
                &times;
            </span>
        </div>`;
    }
    document.getElementById('file').value = ''; // Clear file input
    document.getElementById('preview_image').innerHTML = previewHTML;
    document.getElementById('file-name').value = image; // Clear file input
$('#select_option_val').val(type);
$("#qname").focus();
$('#exam_type').val(type);
var value = type;
if(value == 2){
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#f').hide();
    $('#g').hide();
    $('#h').hide();
    $('#i').hide();
    $('#edit_id').val(id);
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').hide();
    $('#sqwordlimit').show();
    $('#wordlimit').hide();
    $('#sq_word_limit').val(word_limit);
    $('#sq_disclaimer').show();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').show();
    $('[name=sqfile_require]').val( file_req);
    $('#file_upload_td').show();

    var quest       = $("#quest"+id).val();
      // $('#qname').val(quest);
      setQuestionInEditor(quest);
  }else if(value == 1){
    //alert(f);
    $('#a').show();
    $('#b').show();
    $('#c').show();
    $('#d').show();
    $('#e').show();
    $('#f').show();
    $('#g').show();
    $('#h').show();
    $('#i').show();
    $('#correctanswer').show();
    $('#addremove').show();
    $('#truefalse').hide();
    $('#wordlimit').hide();
    $('#sqwordlimit').hide();
    $('#edit_id').val(id);
    $('#option_a').val(a);
    $('#option_b').val(b);
    $('#option_c').val(c);
    $('#option_d').val(d);
    $('#option_e').val(e);
    $('#option_f').val(file_req);
    $('#option_g').val(f);
    $('#option_h').val(g);
    $('#option_i').val(h);
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').hide();
    $('#file_upload_td').show();
      var quest       = $("#quest"+id).val();
     // alert(quest)
      // $('#qname').val(quest);
      setQuestionInEditor(quest);
      $('[name=correct_option]').val( correc);
  }else if(value == 3){
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#f').hide();
    $('#g').hide();
    $('#h').hide();
    $('#i').hide();
    $('#edit_id').val(id);
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').show();
    $('#wordlimit').hide();
    $('#sqwordlimit').hide();
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').hide();
    $('#file_upload_td').hide();
    var quest = $("#quest"+id).val();
      // $('#qname').val(quest);
      setQuestionInEditor(quest);
      $('[name=true_false]').val( correc);
  }else{
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#f').hide();
    $('#g').hide();
    $('#h').hide();
    $('#i').hide();
    $('#edit_id').val(id);
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').hide();
    $('#wordlimit').show();
    $('#sqwordlimit').hide();
    $('#word_limit').val(word_limit);
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').show();
    $('#lq_file_require').show();
    $('[name=file_require]').val( file_req);
    $('#sq_file_require').hide();
    $('#file_upload_td').show();

    var quest = $("#quest"+id).val();
      // $('#qname').val(quest);
      setQuestionInEditor(quest);

  }

      document.getElementById('question_form').scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });

}

function edit_question(id,a,b,c,d,e,correc,type,word_limit,file_req,f,g,h,i){
//alert(type);word_limit
$("#qname").focus();
$('#exam_type').val(type);
var value = type;
if(value == 2){
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#f').hide();
    $('#g').hide();
    $('#h').hide();
    $('#i').hide();
    $('#edit_id').val(id);
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').hide();
    $('#sqwordlimit').show();
    $('#wordlimit').hide();
    $('#sq_word_limit').val(word_limit);
    $('#sq_disclaimer').show();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').show();
    $('[name=sqfile_require]').val( file_req);

    var quest = $("#ques"+id).val();
      // $('#qname').val(quest);
      setQuestionInEditor(quest);
  }else if(value == 1){
    $('#a').show();
    $('#b').show();
    $('#c').show();
    $('#d').show();
    $('#e').show();
    $('#f').show();
    $('#g').show();
    $('#h').show();
    $('#i').show();
    $('#correctanswer').show();
    $('#addremove').show();
    $('#truefalse').hide();
    $('#wordlimit').hide();
    $('#sqwordlimit').hide();
    $('#edit_id').val(id);
    $('#option_a').val(a);
    $('#option_b').val(b);
    $('#option_c').val(c);
    $('#option_d').val(d);
    $('#option_e').val(e);
    $('#option_f').val(f);
    $('#option_g').val(g);
    $('#option_h').val(h);
    $('#option_i').val(i);
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').hide();
      var quest       = $("#ques"+id).val();
      // $('#qname').val(quest);
      setQuestionInEditor(quest);
      $('[name=correct_option]').val( correc);
  }else if(value == 3){
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#f').hide();
    $('#g').hide();
    $('#h').hide();
    $('#i').hide();
    $('#edit_id').val(id);
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').show();
    $('#wordlimit').hide();
    $('#sqwordlimit').hide();
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').hide();
    $('#lq_file_require').hide();
    $('#sq_file_require').hide();
    var quest       = $("#ques"+id).val();
      // $('#qname').val(quest);
      setQuestionInEditor(quest);
      $('[name=true_false]').val( correc);
  }else{
    $('#a').hide();
    $('#b').hide();
    $('#c').hide();
    $('#d').hide();
    $('#e').hide();
    $('#f').hide();
    $('#g').hide();
    $('#h').hide();
    $('#i').hide();
    $('#edit_id').val(id);
    $('#correctanswer').hide();
    $('#addremove').hide();
    $('#truefalse').hide();
    $('#wordlimit').show();
    $('#sqwordlimit').hide();
    $('#word_limit').val(word_limit);
    $('#sq_disclaimer').hide();
    $('#lq_disclaimer').show();
    $('#lq_file_require').show();
    $('[name=file_require]').val( file_req);
    $('#sq_file_require').hide();

    var quest = $("#ques"+id).val();
      // $('#qname').val(quest);
      setQuestionInEditor(quest);

  }

    }
$(document).ready(function () {
       $('#exam_type, #correct_option').select2({
        width: '100%',
        minimumResultsForSearch: Infinity,
        dropdownCssClass: 'green-select-dropdown'
    });
});
</script>
@endsection
{{-- @stop --}}