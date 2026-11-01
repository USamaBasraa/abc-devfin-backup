<div class="pm-marks-detail">
    <table class="pm-marks-table">
        <thead>
            <tr>
                <th>Assessment</th>
                <th class="text-center">Total marks</th>
                <th class="text-center">Obtained marks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rsMarksDetails as $rec)
                @php
                    $status = $rec->cam_grading_status ?? '';
                    $isExcluded = ($status . $status) !== 'EE';
                @endphp
                <tr class="{{ $isExcluded ? 'is-muted' : '' }}">
                    <td>{{ $rec->assessment_name }}#{{ $rec->cam_number }}</td>
                    <td class="text-center">{{ $rec->total_marks }}</td>
                    <td class="text-center">
                        <span class="pm-marks-obtained">{{ $rec->obtained_marks }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
