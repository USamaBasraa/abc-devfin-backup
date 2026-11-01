@php
    $colspan = 35;
    $totalCount = count($list ?? []);
@endphp
<table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse;font-family:Arial,sans-serif;font-size:11px;">
    <tr>
        <td colspan="{{ $colspan }}" style="border:none;background-color:#6CC;text-align:center;font-size:18px;font-weight:bold;">
            Beaconhouse National University
        </td>
    </tr>
    <tr>
        <td colspan="{{ $colspan }}" style="border:none;background-color:#6C6;text-align:center;font-size:14px;font-weight:bold;height:28px;">
            Applicants List
        </td>
    </tr>
    <tr style="background-color:#999;font-weight:bold;">
        <th>Sr #</th>
        <th>Session</th>
        <th>BNU School</th>
        <th>Email</th>
        <th>Program Applied For</th>
        <th>App Id</th>
        <th>Name</th>
        <th>Gender</th>
        <th>Contact</th>
        <th>City</th>
        <th>Current Address</th>
        <th>Permanent Address</th>
        <th>Guardian Name</th>
        <th>Guardian Relationship</th>
        <th>Guardian Number</th>
        <th>School Name</th>
        <th>Matric / O-Level</th>
        <th>Marks Obtained</th>
        <th>Total Marks</th>
        <th>Intermediate / A-Level</th>
        <th>Institute Name</th>
        <th>Marks Obtained</th>
        <th>Total Marks</th>
        <th>Scholarship Candidate</th>
        <th>BS</th>
        <th>Obtained</th>
        <th>Total Marks</th>
        <th>GAT</th>
        <th>Marks Obtained</th>
        <th>Total</th>
        <th>Remarks</th>
        <th>Last Institute Attended</th>
        <th>Obtained Test Marks</th>
        <th>Obtained Interview Marks</th>
        <th>Applicant Status</th>
    </tr>
    @foreach($list as $index => $row)
        <tr>
            <td style="text-align:center;">{{ $index + 1 }}</td>
            <td>{{ $row->sess_name ?? '' }}</td>
            <td>{{ $row->school_code ?? '' }}</td>
            <td>{{ $row->aai_api_email ?? '' }}</td>
            <td>{{ $row->prog_name ?? '' }}</td>
            <td style="text-align:center;">{{ $row->aai_id ?? '' }}</td>
            <td>{{ $row->api_name ?? '' }}</td>
            <td>{{ $row->api_gender ?? '' }}</td>
            <td>{{ $row->api_mobile ?? '' }}</td>
            <td>{{ $row->api_presentcity ?? '' }}</td>
            <td>{{ $row->api_presentaddress ?? '' }}</td>
            <td>{{ $row->api_permanentaddress ?? '' }}</td>
            <td>{{ $row->api_guardianname ?? '' }}</td>
            <td>{{ $row->api_guardianrelation ?? '' }}</td>
            <td>{{ $row->api_guardian_mobile ?? '' }}</td>
            <td>{{ $row->aq_school_name_matric ?? '' }}</td>
            <td>{{ $row->aq_degree_name ?? '' }}</td>
            <td style="text-align:center;">{{ $row->aq_obtained_marks_matric ?? '' }}</td>
            <td style="text-align:center;">{{ $row->aq_total_marks_matric ?? '' }}</td>
            <td>{{ $row->aq_degree_name_int ?? '' }}</td>
            <td>{{ $row->aq_school_name_int ?? '' }}</td>
            <td style="text-align:center;">{{ $row->aq_obtained_marks_inter ?? '' }}</td>
            <td style="text-align:center;">{{ $row->aq_total_marks_inter ?? '' }}</td>
            <td style="text-align:center;">{{ $row->api_needbase_scholarship ?? '' }}</td>
            <td>{{ $row->aq_degree_name_gra ?? '' }}</td>
            <td style="text-align:center;">{{ $row->aq_obtained_marks_gra ?? '' }}</td>
            <td style="text-align:center;">{{ $row->aq_total_marks_gra ?? '' }}</td>
            <td>{{ $row->aq_degree_name_gat ?? '' }}</td>
            <td style="text-align:center;">{{ $row->aq_obtained_marks_gat ?? '' }}</td>
            <td style="text-align:center;">{{ $row->aq_total_marks_gat ?? '' }}</td>
            <td>{{ $row->aai_pf_remarks ?? '' }}</td>
            <td>{{ $row->aq_degree_institute ?? '' }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    @endforeach
    <tr>
        <td colspan="{{ $colspan }}" style="text-align:right;border:none;">
            Total Records: {{ $totalCount }} | Generated: {{ date('d-M-Y h:i A') }}
        </td>
    </tr>
</table>
