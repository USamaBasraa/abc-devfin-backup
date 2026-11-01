@extends('layouts.app', ['pageTitle' => 'Transfer Exam Questions'])

@section('content')
<style>
    .transfer-section {
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        padding: 18px;
        margin-bottom: 20px;
        background: #fafafa;
    }

    .transfer-section h5 {
        margin-bottom: 16px;
        font-weight: 600;
        color: #2F3B4D;
    }

    .transfer-section.source-section {
        border-left: 4px solid #007bff;
    }

    .transfer-section.target-section {
        border-left: 4px solid #28a745;
    }

    .preview-box {
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        margin-bottom: 16px;
        overflow: hidden;
    }

    .preview-box-header {
        padding: 10px 14px;
        font-weight: 600;
        font-size: 14px;
    }

    .preview-existing { background: #fff8e6; }
    .preview-existing .preview-box-header { background: #ffe8a1; color: #856404; }

    .preview-transfer { background: #eef9f0; }
    .preview-transfer .preview-box-header { background: #c3e6cb; color: #155724; }

    .preview-skip { background: #f8f9fa; }
    .preview-skip .preview-box-header { background: #e2e3e5; color: #383d41; }

    .preview-table {
        width: 100%;
        margin-bottom: 0;
        font-size: 13px;
    }

    .preview-table th,
    .preview-table td {
        padding: 8px 10px;
        vertical-align: top;
        border-top: 1px solid #eee;
    }

    .preview-table thead th {
        background: rgba(255,255,255,0.7);
        border-top: 0;
    }

    .summary-cards {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .summary-card {
        flex: 1;
        min-width: 140px;
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        padding: 12px;
        text-align: center;
        background: #fff;
    }

    .summary-card .count {
        font-size: 24px;
        font-weight: 700;
        line-height: 1.2;
    }

    .summary-card .label {
        font-size: 12px;
        color: #666;
    }

    .target-readonly-value {
        padding: 8px 12px;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        font-size: 14px;
        color: #333;
        min-height: 38px;
    }
</style>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Transfer Exam Questions</h3>
        <a href="{{ !empty($targetContext) ? route('teacher/adm_online_exam', ['adm_sch_id' => $targetContext['sch_id']]) : route('teacher/admission_header_exam') }}" class="btn btn-light btn-sm">Back</a>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">
            Select the source (old) admission schedule and exam paper. Questions will be merged into the current schedule's target exam paper.
        </p>

        @if(empty($targetContext))
        <div class="alert alert-warning">
            Please open this page from <strong>Question Bank Transfer</strong> on the Online Exam screen.
        </div>
        @endif

        <div class="row">
            <div class="col-lg-6">
                <div class="transfer-section source-section">
                    <h5>Source (Old Exam Paper)</h5>
                    <div class="form-group">
                        <label>Session</label>
                        <select id="source_session" class="form-control"><option value="">Select Session</option></select>
                    </div>
                    <div class="form-group">
                        <label>School</label>
                        <select id="source_school" class="form-control"><option value="">Select School</option></select>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <select id="source_department" class="form-control"><option value="">Select Department</option></select>
                    </div>
                    <div class="form-group">
                        <label>Program</label>
                        <select id="source_program" class="form-control"><option value="">Select Program</option></select>
                    </div>
                    <div class="form-group">
                        <label>Admission Schedule</label>
                        <select id="source_schedule" class="form-control"><option value="">Select Admission Schedule</option></select>
                    </div>
                    <div class="form-group">
                        <label>Exam Paper</label>
                        <select id="source_exam_paper" class="form-control" disabled><option value="">Select exam paper</option></select>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="transfer-section target-section">
                    <h5>Target (New Exam Paper)</h5>
                    @if(!empty($targetContext))
                    <div class="form-group">
                        <label>Session</label>
                        <div class="target-readonly-value">{{ $targetContext['sess_name'] ?? '-' }}</div>
                    </div>
                    <div class="form-group">
                        <label>School</label>
                        <div class="target-readonly-value">{{ $targetContext['school_name'] ?? '-' }}</div>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <div class="target-readonly-value">{{ $targetContext['dept_name'] ?? '-' }}</div>
                    </div>
                    <div class="form-group">
                        <label>Program</label>
                        <div class="target-readonly-value">{{ $targetContext['prog_name'] ?? '-' }}</div>
                    </div>
                    <div class="form-group">
                        <label>Admission Schedule</label>
                        <div class="target-readonly-value">{{ $targetContext['schedule_name'] ?? '-' }}</div>
                    </div>
                    <div class="form-group">
                        <label>Exam Paper</label>
                        <select id="target_exam_paper" class="form-control" disabled>
                            <option value="">Select exam paper</option>
                        </select>
                    </div>
                    <input type="hidden" id="target_sch_id" value="{{ $targetContext['sch_id'] }}">
                    @else
                    <div class="alert alert-light mb-0">Target schedule not available.</div>
                    @endif
                </div>
            </div>
        </div>

        <div id="preview_area" style="display:none;">
            <hr>
            <h5 class="mb-3">Transfer Preview</h5>

            <div class="summary-cards" id="summary_cards"></div>

            <div class="preview-box preview-existing">
                <div class="preview-box-header">
                    Already in Target Paper (<span id="existing_count">0</span>)
                </div>
                <div class="table-responsive">
                    <table class="preview-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Marks</th>
                                <th>Question</th>
                            </tr>
                        </thead>
                        <tbody id="existing_questions_body"></tbody>
                    </table>
                </div>
            </div>

            <div class="preview-box preview-transfer">
                <div class="preview-box-header">
                    Will Be Transferred (<span id="transfer_count">0</span>)
                </div>
                <div class="table-responsive">
                    <table class="preview-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Marks</th>
                                <th>Question</th>
                            </tr>
                        </thead>
                        <tbody id="transfer_questions_body"></tbody>
                    </table>
                </div>
            </div>

            <div class="preview-box preview-skip" id="skip_box" style="display:none;">
                <div class="preview-box-header">
                    Already Exist in Target — Will Be Skipped (<span id="skip_count">0</span>)
                </div>
                <div class="table-responsive">
                    <table class="preview-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Marks</th>
                                <th>Question</th>
                            </tr>
                        </thead>
                        <tbody id="skip_questions_body"></tbody>
                    </table>
                </div>
            </div>

            <div class="text-center mt-3">
                <button type="button" id="btn_transfer" class="btn btn-success btn-lg" disabled>
                    Transfer Questions
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
(function () {
    var transferUrls = {
        sessions: '{{ route("teacher/transfer-exam/sessions") }}',
        schools: '{{ route("teacher/transfer-exam/schools") }}',
        departments: '{{ url("teacher/transfer-exam/departments") }}',
        programs: '{{ url("teacher/transfer-exam/programs") }}',
        sessionPrograms: '{{ url("teacher/transfer-exam/session-programs") }}',
        schedules: '{{ route("teacher/transfer-exam/admission-schedules") }}',
        examPapers: '{{ url("teacher/transfer-exam/exam-papers") }}',
        preview: '{{ route("teacher/transfer-exam/preview") }}',
        execute: '{{ route("teacher/transfer-exam/execute") }}'
    };
    var targetContext = @json($targetContext ?? null);

    function initTarget() {
        if (!targetContext) return;

        $.get(transferUrls.examPapers + '/' + targetContext.sch_id, function (papers) {
            var $paper = $('#target_exam_paper');
            $paper.empty().append('<option value="">Select exam paper</option>');

            if (papers.length === 0) {
                $paper.append('<option value="">No exam paper found</option>');
                return;
            }

            papers.forEach(function (item) {
                var label = item.title;
                $paper.append('<option value="' + item.id + '">' + label + '</option>');
            });

            $paper.val(targetContext.paper_id).prop('disabled', false);
            $paper.on('change', function () {
                tryLoadPreview();
            });
            tryLoadPreview();
        });
    }

    function initSource() {
        loadSessions('source');
        bindSdpoEvents('source');
    }

    function loadSessions(prefix) {
        $.get(transferUrls.sessions, function (data) {
            var $sel = $('#' + prefix + '_session');
            $sel.empty().append('<option value="">Select Session</option>');
            data.forEach(function (item) {
                $sel.append('<option value="' + item.sess_id + '">' + item.sess_name + '</option>');
            });
        });
    }

    function loadSchools(prefix) {
        $.get(transferUrls.schools, function (data) {
            var $sel = $('#' + prefix + '_school');
            $sel.empty().append('<option value="">Select School</option>');
            data.forEach(function (item) {
                $sel.append('<option value="' + item.school_id + '">' + item.school_name + '</option>');
            });
        });
    }

    function loadDepartments(prefix, schoolId) {
        var $sel = $('#' + prefix + '_department');
        $sel.empty().append('<option value="">Select Department</option>');
        resetPrograms(prefix);
        resetSchedules(prefix);
        resetExamPapers(prefix);

        if (!schoolId) return;

        $.get(transferUrls.departments + '/' + schoolId, function (data) {
            data.forEach(function (item) {
                $sel.append('<option value="' + item.dept_id + '">' + item.dept_name + '</option>');
            });
        });
    }

    function loadPrograms(prefix, schoolId, deptId) {
        var $sel = $('#' + prefix + '_program');
        $sel.empty().append('<option value="">Select Program</option>');
        resetSchedules(prefix);
        resetExamPapers(prefix);

        var sessId = $('#' + prefix + '_session').val();
        if (!sessId || !schoolId || !deptId) return;

        $.get(transferUrls.sessionPrograms + '/' + sessId + '/' + schoolId + '/' + deptId, function (data) {
            data.forEach(function (item) {
                $sel.append('<option value="' + item.prog_id + '">' + item.prog_name + '</option>');
            });
        });
    }

    function loadSchedules(prefix) {
        var params = {
            sess_id: $('#' + prefix + '_session').val(),
            school_id: $('#' + prefix + '_school').val(),
            dept_id: $('#' + prefix + '_department').val(),
            prog_id: $('#' + prefix + '_program').val()
        };

        var $sel = $('#' + prefix + '_schedule');
        $sel.empty().append('<option value="">Select Admission Schedule</option>');
        resetExamPapers(prefix);

        if (!params.sess_id || !params.school_id || !params.dept_id || !params.prog_id) return;

        $.get(transferUrls.schedules, params, function (data) {
            data.forEach(function (item) {
                $sel.append('<option value="' + item.admission_sch_id + '">' + item.admission_sch_name + '</option>');
            });
        });
    }

    function loadExamPapers(prefix, schId) {
        var $sel = $('#' + prefix + '_exam_paper');
        $sel.empty().append('<option value="">Select exam paper</option>').prop('disabled', true);

        if (!schId) return;

        $.get(transferUrls.examPapers + '/' + schId, function (data) {
            if (data.length === 0) {
                $sel.append('<option value="">No exam paper found</option>');
                return;
            }
            data.forEach(function (item) {
                var label = item.title;
                $sel.append('<option value="' + item.id + '">' + label + '</option>');
            });
            $sel.prop('disabled', false);
        });
    }

    function resetPrograms(prefix) {
        $('#' + prefix + '_program').empty().append('<option value="">Select Program</option>');
    }

    function resetSchedules(prefix) {
        $('#' + prefix + '_schedule').empty().append('<option value="">Select Admission Schedule</option>');
    }

    function resetExamPapers(prefix) {
        $('#' + prefix + '_exam_paper').empty().append('<option value="">Select exam paper</option>').prop('disabled', true);
        tryLoadPreview();
    }

    function bindSdpoEvents(prefix) {
        $('#' + prefix + '_session').on('change', function () {
            loadSchools(prefix);
            resetPrograms(prefix);
            resetSchedules(prefix);
            resetExamPapers(prefix);
        });

        $('#' + prefix + '_school').on('change', function () {
            loadDepartments(prefix, $(this).val());
        });

        $('#' + prefix + '_department').on('change', function () {
            loadPrograms(prefix, $('#' + prefix + '_school').val(), $(this).val());
        });

        $('#' + prefix + '_program').on('change', function () {
            loadSchedules(prefix);
        });

        $('#' + prefix + '_schedule').on('change', function () {
            loadExamPapers(prefix, $(this).val());
        });

        $('#' + prefix + '_exam_paper').on('change', function () {
            tryLoadPreview();
        });
    }

    function renderQuestionRows($tbody, questions) {
        $tbody.empty();
        if (!questions || questions.length === 0) {
            $tbody.append('<tr><td colspan="4" class="text-center text-muted">No questions</td></tr>');
            return;
        }
        questions.forEach(function (q, i) {
            $tbody.append(
                '<tr>' +
                '<td>' + (i + 1) + '</td>' +
                '<td>' + q.type_label + '</td>' +
                '<td>' + (q.question_marks || '-') + '</td>' +
                '<td>' + $('<div>').text(q.question).html() + '</td>' +
                '</tr>'
            );
        });
    }

    function tryLoadPreview() {
        var sourceId = $('#source_exam_paper').val();
        var targetId = $('#target_exam_paper').val();

        $('#preview_area').hide();
        $('#btn_transfer').prop('disabled', true);

        if (!sourceId || !targetId) return;

        $.get(transferUrls.preview, {
            source_paper_id: sourceId,
            target_paper_id: targetId
        }, function (res) {
            $('#preview_area').show();

            $('#summary_cards').html(
                '<div class="summary-card"><div class="count">' + res.summary.existing_count + '</div><div class="label">Already in Target</div></div>' +
                '<div class="summary-card"><div class="count text-success">' + res.summary.to_transfer_count + '</div><div class="label">Will Transfer</div></div>' +
                '<div class="summary-card"><div class="count text-muted">' + res.summary.already_in_target_count + '</div><div class="label">Will Skip (Duplicate)</div></div>' +
                '<div class="summary-card"><div class="count text-primary">' + res.summary.total_after_transfer + '</div><div class="label">Total After Transfer</div></div>'
            );

            $('#existing_count').text(res.summary.existing_count);
            $('#transfer_count').text(res.summary.to_transfer_count);
            $('#skip_count').text(res.summary.already_in_target_count);

            renderQuestionRows($('#existing_questions_body'), res.existing);
            renderQuestionRows($('#transfer_questions_body'), res.to_transfer);
            renderQuestionRows($('#skip_questions_body'), res.already_in_target);

            if (res.summary.already_in_target_count > 0) {
                $('#skip_box').show();
            } else {
                $('#skip_box').hide();
            }

            $('#btn_transfer').prop('disabled', res.summary.to_transfer_count === 0);
        }).fail(function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Failed to load preview.';
            alert(msg);
        });
    }

    $('#btn_transfer').on('click', function () {
        var sourceId = $('#source_exam_paper').val();
        var targetId = $('#target_exam_paper').val();

        if (!sourceId || !targetId) return;
        if (!confirm('Transfer ' + $('#transfer_count').text() + ' question(s) to the target exam paper?')) return;

        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            url: transferUrls.execute,
            data: {
                source_paper_id: sourceId,
                target_paper_id: targetId
            },
            success: function (res) {
                alert(res.message);
                tryLoadPreview();
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : ((xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Transfer failed.');
                alert(msg);
            }
        });
    });

    initSource();
    initTarget();
})();
</script>
@endsection
