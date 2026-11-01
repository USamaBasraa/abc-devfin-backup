@php
    use App\Http\Controllers\Grader\Hostel\AdmissionNewApplicantsHostelReportController as HostelReportHelper;

    $multiChallanKeys = $rows
        ->filter(fn ($row) => filled($row->challan_id))
        ->groupBy(function ($row) {
            return strtolower((string) ($row->student_type ?? '')) . '|' . strtolower((string) ($row->enrollment_no ?? $row->applicant_id ?? ''));
        })
        ->filter(fn ($group) => $group->count() > 1)
        ->keys();

    $formatDate = function ($value) {
        if (blank($value)) {
            return '-';
        }
        try {
            return \Carbon\Carbon::parse($value)->format('d M Y');
        } catch (\Exception $e) {
            return (string) $value;
        }
    };
@endphp

<input type="hidden" id="reportExportTitle" value="Hostel Report - {{ $sessionName ?? $sessionId }}">

<div class="row mb-3">
    <div class="col-6 col-lg-2 mb-2">
        <div class="hostel-report-stat stat-students">
            <small>Students</small>
            <h4 class="text-primary">{{ $stats['total_students'] ?? 0 }}</h4>
            <div class="stat-sub">{{ $stats['total_rows'] ?? 0 }} challan rows</div>
        </div>
    </div>
    <div class="col-6 col-lg-2 mb-2">
        <div class="hostel-report-stat stat-new">
            <small>New Admission</small>
            <h4 class="text-info">{{ $stats['new_admission'] ?? 0 }}</h4>
        </div>
    </div>
    <div class="col-6 col-lg-2 mb-2">
        <div class="hostel-report-stat stat-continuing">
            <small>Continuing</small>
            <h4 class="text-secondary">{{ $stats['existing_students'] ?? 0 }}</h4>
        </div>
    </div>
    <div class="col-6 col-lg-2 mb-2">
        <div class="hostel-report-stat stat-paid">
            <small>Paid Students</small>
            <h4 class="text-success">{{ $stats['paid'] ?? 0 }}</h4>
            <div class="stat-sub">{{ $stats['paid_rows'] ?? 0 }} paid rows</div>
        </div>
    </div>
    <div class="col-6 col-lg-2 mb-2">
        <div class="hostel-report-stat stat-unpaid">
            <small>Unpaid Students</small>
            <h4 class="text-danger">{{ $stats['unpaid'] ?? 0 }}</h4>
            <div class="stat-sub">{{ $stats['unpaid_rows'] ?? 0 }} unpaid rows</div>
        </div>
    </div>
    <div class="col-6 col-lg-2 mb-2">
        <div class="hostel-report-stat stat-bed">
            <small>Bed Assigned</small>
            <h4 style="color:#8b5cf6;">{{ $stats['bed_assigned'] ?? 0 }}</h4>
        </div>
    </div>
</div>

@if(($stats['no_challan'] ?? 0) > 0 || ($stats['multi_challan'] ?? 0) > 0)
<div class="row mb-3">
    @if(($stats['no_challan'] ?? 0) > 0)
    <div class="col-auto">
        <span class="badge badge-warning p-2"><i class="fa fa-exclamation-circle"></i> No Challan: {{ $stats['no_challan'] }} students</span>
    </div>
    @endif
    @if(($stats['multi_challan'] ?? 0) > 0)
    <div class="col-auto">
        <span class="badge badge-warning p-2" style="background:#f59e0b;"><i class="fa fa-copy"></i> Multi Challan: {{ $stats['multi_challan'] }} students</span>
    </div>
    @endif
</div>
@endif

<div class="card hostel-report-card">
    <div class="card-header text-white d-flex justify-content-between align-items-center flex-wrap">
        <h5 class="mb-0 font-weight-bold">
            <i class="fa fa-calendar mr-2"></i>{{ $sessionName ?? 'Session ' . $sessionId }}
        </h5>
        <div class="mt-1 mt-md-0">
            <span class="badge badge-light text-dark mr-1">{{ $stats['total_students'] ?? 0 }} students</span>
            <span class="badge badge-light text-dark mr-1">{{ $stats['total_rows'] ?? 0 }} rows</span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="studentsTable" class="table table-bordered table-hover table-sm hostel-report-table mb-0">
                <thead>
                    <tr class="group-row group-admission">
                        <th colspan="12"><i class="fa fa-graduation-cap mr-1"></i> Admission</th>
                        <th colspan="7"><i class="fa fa-money mr-1"></i> Finance</th>
                        <th colspan="2"><i class="fa fa-bed mr-1"></i> Hostel</th>
                    </tr>
                    <tr class="column-row">
                        <th>#</th>
                        <th>Type</th>
                        <th>App ID</th>
                        <th>Enrollment</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>School</th>
                        <th>Program</th>
                        <th>City</th>
                        <th>Mobile</th>
                        <th>Allowed</th>
                        <th>Status</th>
                        <th>Pref.</th>
                        <th>Location</th>
                        <th>B/G</th>
                        <th>Challan #</th>
                        <th>Amount</th>
                        <th>Scholarship</th>
                        <th>Status</th>
                        <th>Due</th>
                        <th>Paid</th>
                        <th>Bed Info</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $index => $row)
                        @php
                            $status = strtoupper((string) ($row->challan_status ?? ''));
                            $rowKey = strtolower((string) ($row->student_type ?? '')) . '|' . strtolower((string) ($row->enrollment_no ?? $row->applicant_id ?? ''));
                            $hasMultipleChallans = $multiChallanKeys->contains($rowKey);
                            $floorRoomBed = ($row->hostel_name || $row->floor_name || $row->room_no || $row->bed_no)
                                ? collect([$row->hostel_name, $row->floor_name, $row->room_no, $row->bed_no])->filter()->implode(' / ')
                                : '-';
                            $allowedClass = strtolower((string) ($row->allowed_status ?? '')) === 'allowed' ? 'badge-success' : 'badge-secondary';
                        @endphp
                        <tr class="{{ $hasMultipleChallans ? 'row-multi' : '' }}">
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td class="col-center">
                                <span class="badge badge-pill {{ $row->student_type === 'New Admission' ? 'badge-info' : 'badge-secondary' }} badge-status">
                                    {{ $row->student_type === 'New Admission' ? 'New' : 'Old' }}
                                </span>
                            </td>
                            <td><code class="small">{{ $row->applicant_id ?? '-' }}</code></td>
                            <td><small>{{ $row->enrollment_no ?: '-' }}</small></td>
                            <td class="col-name">
                                {{ $row->student_name ?? '-' }}
                                @if($hasMultipleChallans)
                                    <span class="badge badge-warning badge-status ml-1">×2</span>
                                @endif
                            </td>
                            <td class="col-center">
                                @if(strtolower((string) ($row->gender ?? '')) === 'male')
                                    <i class="fa fa-male text-primary" title="Male"></i>
                                @elseif(strtolower((string) ($row->gender ?? '')) === 'female')
                                    <i class="fa fa-female text-danger" title="Female"></i>
                                @else
                                    {{ $row->gender ?? '-' }}
                                @endif
                            </td>
                            <td><small>{{ $row->school_name ?? '-' }}</small></td>
                            <td class="col-program"><small>{{ $row->program_name ?? '-' }}</small></td>
                            <td>{{ $row->city ?? '-' }}</td>
                            <td><small>{{ $row->mobile ?? '-' }}</small></td>
                            <td class="col-center">
                                <span class="badge {{ $allowedClass }} badge-status">{{ $row->allowed_status ?? '-' }}</span>
                            </td>
                            <td><small>{{ $row->admission_status ?? '-' }}</small></td>
                            <td><small>{{ $row->location_preference ? ucwords(str_replace('_', ' ', $row->location_preference)) : '-' }}</small></td>
                            <td>
                                @php $loc = HostelReportHelper::formatSessionType($row->session_type ?? null); @endphp
                                @if($loc === 'On Campus')
                                    <span class="badge badge-primary badge-status">On</span>
                                @elseif($loc === 'Off Campus')
                                    <span class="badge badge-warning badge-status">Off</span>
                                @else
                                    <small class="text-muted">{{ $loc }}</small>
                                @endif
                            </td>
                            <td class="col-center"><small>{{ HostelReportHelper::formatHostelCategory($row->hostel_category ?? null) }}</small></td>
                            <td><strong>{{ $row->challan_no ?? '-' }}</strong></td>
                            <td class="col-amount">{{ filled($row->challan_amount) ? number_format((float) $row->challan_amount) : '-' }}</td>
                            <td class="col-amount">{{ ((float) ($row->scholarship_amount ?? 0)) > 0 ? number_format((float) $row->scholarship_amount) : '-' }}</td>
                            <td class="col-center" data-export="{{ $status ?: 'NO CHALLAN' }}">
                                @if($status === 'PAID')
                                    <span class="badge badge-success badge-status">Paid</span>
                                @elseif($status === 'UNPAID')
                                    <span class="badge badge-danger badge-status">Unpaid</span>
                                @elseif($status === '')
                                    <span class="badge badge-light text-muted badge-status">None</span>
                                @else
                                    <span class="badge badge-secondary badge-status">{{ $status }}</span>
                                @endif
                            </td>
                            <td><small>{{ $formatDate($row->due_date ?? null) }}</small></td>
                            <td><small>{{ $formatDate($row->paid_date ?? null) }}</small></td>
                            <td><small class="text-primary">{{ $floorRoomBed }}</small></td>
                            <td><small class="text-muted">{{ \Illuminate\Support\Str::limit($row->permanent_address ?? '-', 40) }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="23" class="text-center text-muted py-5">
                                <i class="fa fa-inbox fa-3x d-block mb-3 text-secondary"></i>
                                <strong>No records found</strong>
                                @if(($filters['student_type'] ?? '') === 'existing')
                                    <p class="mb-0 mt-2 small">Continuing students use <code>hostel_applicants</code>. Try <strong>New Admission</strong> or <strong>All</strong>.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
