@extends('layouts.app', ['pageTitle' => $type === 'applicants' ? 'Admission Applicants' : 'Enrolled Students'])
@section('content')

@php
    $isApplicants = ($type ?? '') === 'applicants';
    $list = $isApplicants ? ($applicantsList ?? []) : ($studentsList ?? []);
    $totalCount = count($list);
    $titleName = $isApplicants ? ($schedule_name ?? 'Admission Exam') : ($course_name ?? '');
@endphp
{{-- @dd("123AA"); --}}
<style>
.exam-participants-page {
    padding-bottom: 32px;
}

.ep-hero {
    background: #1F776E;
    border-radius: 12px;
    padding: 24px 28px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 20px;
    box-shadow: 0 4px 20px rgba(68, 84, 195, 0.25);
}

.ep-hero-title {
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 4px;
}

.ep-hero-subtitle {
    font-size: 14px;
    opacity: 0.9;
    margin: 0;
}

.ep-hero-badge {
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(4px);
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-align: center;
}

.ep-hero-badge strong {
    display: block;
    font-size: 22px;
    font-weight: 700;
    line-height: 1.2;
}

.ep-toolbar-card {
    background: #fff;
    border: 1px solid #e8edf3;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    box-shadow: 0 1px 4px rgba(15,23,42,0.04);
}

.ep-search-box {
    position: relative;
    flex: 1;
    min-width: 300px;
    max-width: 560px;
}

.ep-search-box input {
    width: 100%;
    height: 44px;
    padding: 0 16px 0 44px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    background: #f8fafc;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
}

.ep-search-box input:focus {
    outline: none;
    background: #fff;
    border-color: #4454c3;
    box-shadow: 0 0 0 3px rgba(68, 84, 195, 0.12);
}

.ep-search-box .search-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
}

.ep-filter-info {
    font-size: 13px;
    color: #64748b;
    margin-bottom: 12px;
    padding-left: 4px;
}

.ep-table-card {
    background: #fff;
    border: 1px solid #e8edf3;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(15,23,42,0.04);
}

.ep-table-card .table-responsive {
    overflow-x: auto;
}

.ep-table {
    width: 100%;
    margin: 0;
    border-collapse: collapse;
    min-width: {{ $isApplicants ? '1100px' : '800px' }};
}

.ep-table thead th {
    background: #17675F;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 13px 14px;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 1;
}

.ep-table tbody td {
    padding: 12px 14px;
    font-size: 13px;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.ep-table tbody tr:last-child td {
    border-bottom: none;
}

.ep-table tbody tr:hover {
    background: #f5f7ff;
}

.ep-table tbody tr:nth-child(even) {
    background: #fafbfc;
}

.ep-table tbody tr:nth-child(even):hover {
    background: #f5f7ff;
}

.ep-name {
    font-weight: 600;
    color: #0f172a;
    white-space: nowrap;
}

.ep-id-badge {
    display: inline-block;
    background: #f1f5f9;
    color: #475569;
    font-size: 12px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 6px;
    font-family: 'SF Mono', 'Consolas', monospace;
}

.ep-email {
    color: #64748b;
    font-size: 12px;
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: block;
}

.ep-text-clamp {
    max-width: 180px;
    font-size: 12px;
    line-height: 1.4;
    color: #475569;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.ep-status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    background: #eef2ff;
    color: #4338ca;
    white-space: nowrap;
}

.ep-status-badge.enrolled {
    background: #dcfce7;
    color: #15803d;
}

.ep-pwd-cell {
    min-width: 220px;
    text-align: center;
}

.ep-pwd-actions {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

.ep-pwd-btn-row {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    justify-content: center;
}

.ep-pwd-btn {
    background: #4454c3;
    border: none;
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.2s;
}

.ep-pwd-btn:hover:not(:disabled) {
    background: #3a47a8;
}

.ep-pwd-btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.ep-pwd-btn.generated {
    background: #94a3b8;
}

.ep-email-btn {
    background: #fff;
    border: 1px solid #4454c3;
    color: #4454c3;
    font-size: 11px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.2s;
}

.ep-email-btn:hover:not(:disabled) {
    background: #4454c3;
    color: #fff;
}

.ep-email-btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.ep-email-btn.sent {
    background: #dcfce7;
    border-color: #86efac;
    color: #15803d;
}

.ep-password-box {
    display: none;
    margin-top: 8px;
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 8px;
    padding: 8px 10px;
    text-align: center;
}

.ep-password-value {
    font-family: 'SF Mono', 'Consolas', monospace;
    font-size: 14px;
    font-weight: 700;
    color: #b45309;
    letter-spacing: 0.5px;
    word-break: break-all;
}

.ep-password-timer {
    font-size: 10px;
    color: #92400e;
    margin-top: 4px;
    opacity: 0.8;
}

.ep-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    color: #475569;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none !important;
    transition: all 0.2s;
    white-space: nowrap;
}

.ep-back-btn:hover {
    background: #f8fafc;
    border-color: #4454c3;
    color: #4454c3;
}

.ep-export-btns {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.ep-export-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none !important;
    white-space: nowrap;
    transition: all 0.2s;
}

.ep-export-btn.pdf {
    background: #1F776E;
    color: #fff !important;
    border: 1px solid #1F776E;
}

.ep-export-btn.pdf:hover {
    background: #1F776E;
    color: #fff !important;
}

.ep-export-btn.excel {
    background: #fff;
    color: #15803d !important;
    border: 1px solid #86efac;
}

.ep-export-btn.excel:hover {
    background: #f0fdf4;
}

.ep-toolbar-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.ep-empty {
    text-align: center;
    padding: 60px 24px;
    color: #64748b;
}

.ep-empty-icon {
    font-size: 40px;
    margin-bottom: 12px;
    opacity: 0.4;
}

.ep-sr {
    color: #94a3b8;
    font-weight: 600;
    font-size: 12px;
    width: 40px;
    text-align: center;
}
</style>

<div class="exam-participants-page">
    <div class="ep-hero">
        <div>
            <h1 class="ep-hero-title">{{ $isApplicants ? 'Admission Applicants' : 'Enrolled Students' }}</h1>
            @if($titleName)
                <p class="ep-hero-subtitle">{{ $titleName }}</p>
            @endif
        </div>
        <div class="ep-hero-badge">
            <strong id="totalCount">{{ $totalCount }}</strong>
            Total {{ $isApplicants ? 'Applicants' : 'Students' }}
        </div>
    </div>

    <div class="ep-toolbar-card">
        <div class="ep-search-box">
            <span class="search-icon">&#128269;</span>
            <input type="text"
                   id="participantSearch"
                   placeholder="{{ $isApplicants ? 'Search name, profile id, applicant id, email, school, department, program...' : 'Search name, enrollment no, student status, course status, semester status...' }}"
                   autocomplete="off">
        </div>
        <div class="ep-toolbar-right">
            <div class="ep-export-btns">
                @if($isApplicants)
                    <a href="{{ route('teacher/exam_participants_pdf', ['type' => 'applicants', 'id' => $adm_sch_id]) }}" class="ep-export-btn pdf" target="_blank" style="background: #1F776E;">&#128196; Download PDF</a>
                    <a href="{{ route('teacher/exam_participants_excel', ['type' => 'applicants', 'id' => $adm_sch_id]) }}" class="ep-export-btn excel">&#128202; Download Excel</a>
                @else
                    <a href="{{ route('teacher/exam_participants_pdf', ['type' => 'students', 'id' => $co_id, 'course_id' => $course_id ?? $co_id]) }}" class="ep-export-btn pdf" target="_blank">&#128196; Download PDF</a>
                    <a href="{{ route('teacher/exam_participants_excel', ['type' => 'students', 'id' => $co_id, 'course_id' => $course_id ?? $co_id]) }}" class="ep-export-btn excel">&#128202; Download Excel</a>
                @endif
            </div>
            @if($isApplicants)
                <a href="{{ route('teacher/adm_online_exam', ['adm_sch_id' => $adm_sch_id]) }}" class="ep-back-btn">&#8592; Back to Online Exam</a>
            @else
                <a href="{{ route('teacher/online_exam', ['co_id' => $co_id, 'course_id' => $course_id ?? $co_id]) }}" class="ep-back-btn">&#8592; Back to Online Exam</a>
            @endif
        </div>
    </div>

    <div class="ep-filter-info">
        Showing <strong id="visibleCount">{{ $totalCount }}</strong> of <strong>{{ $totalCount }}</strong> records
    </div>

    <div class="ep-table-card">
        <div class="table-responsive">
            @if($totalCount > 0)
                <table class="ep-table" id="participantsTable">
                    <thead>
                        <tr>
                            <th class="ep-sr">#</th>
                            @if($isApplicants)
                                <th>Name</th>
                                <th>Profile ID</th>
                                <th>Applicant ID</th>
                                <th>Email</th>
                                <th>School</th>
                                <th>Department</th>
                                <th>Program</th>
                                <th>Password</th>
                            @else
                                <th>Enrollment No</th>
                                <th>Name</th>
                                <th class="text-center">Student Status</th>
                                <th class="text-center">Course Status</th>
                                <th class="text-center">Semester Status</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if($isApplicants)
                            @foreach($applicantsList as $index => $rec)
                                <tr data-search="{{ strtolower(trim(($rec->api_name ?? '').' '.($rec->api_id ?? '').' '.($rec->aai_id ?? '').' '.($rec->aai_api_email ?? '').' '.($rec->school_name ?? '').' '.($rec->dept_name ?? '').' '.($rec->prog_name ?? ''))) }}">
                                    <td class="row-sr ep-sr">{{ $index + 1 }}</td>
                                    <td><span class="ep-name">{{ $rec->api_name }}</span></td>
                                    <td><span class="ep-id-badge">{{ $rec->api_id }}</span></td>
                                    <td><span class="ep-id-badge">{{ $rec->aai_id }}</span></td>
                                    <td><span class="ep-email" title="{{ $rec->aai_api_email }}">{{ $rec->aai_api_email }}</span></td>
                                    <td><span class="ep-text-clamp" title="{{ $rec->school_name }}">{{ $rec->school_name }}</span></td>
                                    <td><span class="ep-text-clamp" title="{{ $rec->dept_name }}">{{ $rec->dept_name }}</span></td>
                                    <td><span class="ep-text-clamp" title="{{ $rec->prog_name }}">{{ $rec->prog_name }}</span></td>
                                    <td class="ep-pwd-cell">
                                        <div class="ep-pwd-actions">
                                            <div class="ep-pwd-btn-row">
                                                <button type="button"
                                                        class="ep-pwd-btn"
                                                        onclick="showApplicantPassword({{ $rec->aai_id }}, this)">
                                                    Generate Password
                                                </button>
                                                <button type="button"
                                                        class="ep-email-btn"
                                                        onclick="sendApplicantEmail({{ $rec->aai_id }}, this)">
                                                    Send Email
                                                </button>
                                            </div>
                                            <div class="ep-password-box">
                                                <div class="ep-password-value"></div>
                                                <div class="ep-password-timer"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            @foreach($studentsList as $index => $rec)
                                <tr data-search="{{ strtolower(trim(($rec->spi_name ?? '').' '.($rec->sai_enrollment_no ?? '').' '.($rec->sai_student_status ?? '').' '.($rec->sc_status == 'A' ? 'enrolled' : ($rec->sc_status ?? '')).' '.($rec->ssr_semester_status ?? ''))) }}">
                                    <td class="row-sr ep-sr">{{ $index + 1 }}</td>
                                    <td><span class="ep-id-badge">{{ $rec->sai_enrollment_no }}</span></td>
                                    <td><span class="ep-name">{{ $rec->spi_name }}</span></td>
                                    <td class="text-center"><span class="ep-status-badge">{{ $rec->sai_student_status }}</span></td>
                                    <td class="text-center">
                                        <span class="ep-status-badge {{ $rec->sc_status == 'A' ? 'enrolled' : '' }}">
                                            {{ $rec->sc_status == 'A' ? 'Enrolled' : $rec->sc_status }}
                                        </span>
                                    </td>
                                    <td class="text-center"><span class="ep-status-badge">{{ $rec->ssr_semester_status }}</span></td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <div id="noSearchResults" class="ep-empty" style="display:none;">
                    <div class="ep-empty-icon">&#128269;</div>
                    <h5 style="color:#334155;margin-bottom:6px;">No matching records found</h5>
                    <p style="margin:0;">Try a different search term.</p>
                </div>
            @else
                <div class="ep-empty">
                    <div class="ep-empty-icon">&#128100;</div>
                    <h5 style="color:#334155;margin-bottom:6px;">No {{ $isApplicants ? 'Applicant' : 'Student' }} Found</h5>
                    <p style="margin:0;">No records available for this exam.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@stop

@section('javascript')
<script type="text/javascript">
var passwordTimers = {};
var passwordIntervals = {};
var PASSWORD_HIDE_SECONDS = 180;

function filterParticipants() {
    var query = $('#participantSearch').val().toLowerCase().trim();
    var visible = 0;
    var sr = 1;

    $('#participantsTable tbody tr').each(function() {
        var searchText = $(this).data('search') || $(this).text().toLowerCase();
        var show = !query || searchText.indexOf(query) !== -1;
        $(this).toggle(show);
        if (show) {
            visible++;
            $(this).find('.row-sr').text(sr++);
        }
    });

    $('#visibleCount').text(visible);
    $('#noSearchResults').toggle(visible === 0 && $('#participantsTable tbody tr').length > 0);
    $('#participantsTable').toggle(visible > 0 || !query);
}

$('#participantSearch').on('input', filterParticipants);

function clearPasswordTimer(aaiId) {
    if (passwordTimers[aaiId]) {
        clearTimeout(passwordTimers[aaiId]);
        delete passwordTimers[aaiId];
    }
    if (passwordIntervals[aaiId]) {
        clearInterval(passwordIntervals[aaiId]);
        delete passwordIntervals[aaiId];
    }
}

function hidePassword(aaiId, $btn, $box) {
    $box.slideUp(200, function() {
        $box.find('.ep-password-value').text('');
        $box.find('.ep-password-timer').text('');
    });
    $btn.removeClass('generated').prop('disabled', false).text('Generate Password');
    clearPasswordTimer(aaiId);
}

function startPasswordCountdown(aaiId, $btn, $box) {
    var remaining = PASSWORD_HIDE_SECONDS;
    var $timer = $box.find('.ep-password-timer');

    function updateTimer() {
        var min = Math.floor(remaining / 60);
        var sec = remaining % 60;
        $timer.text('Auto-hides in ' + min + ':' + (sec < 10 ? '0' : '') + sec);
    }

    updateTimer();
    passwordIntervals[aaiId] = setInterval(function() {
        remaining--;
        if (remaining <= 0) {
            hidePassword(aaiId, $btn, $box);
        } else {
            updateTimer();
        }
    }, 1000);

    passwordTimers[aaiId] = setTimeout(function() {
        hidePassword(aaiId, $btn, $box);
    }, PASSWORD_HIDE_SECONDS * 1000);
}

function showApplicantPassword(aaiId, btn) {
    var $btn = $(btn);
    var $box = $btn.closest('.ep-pwd-actions').find('.ep-password-box');

    if ($box.is(':visible')) {
        return;
    }

    clearPasswordTimer(aaiId);
    $btn.prop('disabled', true).text('Loading...');

    $.ajax({
        type: 'GET',
        url: '{{ url('teacher/applicant_password') }}/{{ $adm_sch_id ?? 0 }}/' + aaiId,
        success: function(data) {
            if (data.success) {
                $box.find('.ep-password-value').text(data.password);
                $box.slideDown(200);
                $btn.addClass('generated').text('Generated').prop('disabled', true);
                startPasswordCountdown(aaiId, $btn, $box);
            } else {
                alert(data.message || 'Password not found');
                $btn.prop('disabled', false).text('Generate Password');
            }
        },
        error: function() {
            alert('Unable to fetch password. Please try again.');
            $btn.prop('disabled', false).text('Generate Password');
        }
    });
}

function sendApplicantEmail(aaiId, btn) {
    var $btn = $(btn);

    if (!confirm('Send login credentials email to this applicant?')) {
        return;
    }

    $btn.prop('disabled', true).text('Sending...');

    $.ajax({
        type: 'GET',
        url: '{{ url('teacher/send_applicant_exam_email') }}/{{ $adm_sch_id ?? 0 }}/' + aaiId,
        success: function(data) {
            if (data.success) {
                $btn.addClass('sent').text('Email Sent');
                setTimeout(function() {
                    $btn.removeClass('sent').prop('disabled', false).text('Send Email');
                }, 4000);
            } else {
                alert(data.message || 'Failed to send email');
                $btn.prop('disabled', false).text('Send Email');
            }
        },
        error: function() {
            alert('Unable to send email. Please try again.');
            $btn.prop('disabled', false).text('Send Email');
        }
    });
}
</script>
@stop
