@php
    $isApplicants = ($type ?? '') === 'applicants';
    $totalCount = count($list ?? []);
@endphp

{{-- @dd("123A"); --}}
<table border="1" cellpadding="6" cellspacing="0"
    style="border-collapse:collapse;width:100%;font-family:Arial,sans-serif;font-size:11px;">
    <thead>
        <tr>
            <th colspan="{{ $isApplicants ? 10 : 6 }}"
                style="background:#4454c3;color:#fff;font-size:14px;padding:10px;text-align:center;">
                {{ $reportTitle ?? 'Exam Participants' }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ $isApplicants ? 10 : 6 }}" style="background:#eef2ff;padding:8px;text-align:center;">
                {{ $titleName ?? '' }} &nbsp;|&nbsp; Total Records: {{ $totalCount }} &nbsp;|&nbsp; Generated:
                {{ date('d-M-Y h:i A') }}
            </th>
        </tr>
        <tr style="background:#f1f5f9;font-weight:bold;">
            <th>Sr.</th>
            @if($isApplicants)
                <th>Name</th>
                <th>Profile ID</th>
                <th>Applicant ID</th>
                <th>Email</th>
                <th>School</th>
                <th>Department</th>
                <th>Program</th>
            @else
                <th>Enrollment No</th>
                <th>Name</th>
                <th>Student Status</th>
                <th>Course Status</th>
                <th>Semester Status</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @if($isApplicants)
            @foreach($list as $index => $rec)
                <tr>
                    <td style="text-align:center;">{{ $index + 1 }}</td>
                    <td>{{ $rec->api_name }}</td>
                    <td style="text-align:center;">{{ $rec->api_id }}</td>
                    <td style="text-align:center;">{{ $rec->aai_id }}</td>
                    <td>{{ $rec->aai_api_email }}</td>
                    <td>{{ $rec->school_name }}</td>
                    <td>{{ $rec->dept_name }}</td>
                    <td>{{ $rec->prog_name }}</td>
                </tr>
            @endforeach
        @else
            @foreach($list as $index => $rec)
                <tr>
                    <td style="text-align:center;">{{ $index + 1 }}</td>
                    <td>{{ $rec->sai_enrollment_no }}</td>
                    <td>{{ $rec->spi_name }}</td>
                    <td style="text-align:center;">{{ $rec->sai_student_status }}</td>
                    <td style="text-align:center;">{{ $rec->sc_status == 'A' ? 'Enrolled' : $rec->sc_status }}</td>
                    <td style="text-align:center;">{{ $rec->ssr_semester_status }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>