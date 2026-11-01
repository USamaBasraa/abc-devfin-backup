@php
    // Count Paid & Unpaid
    $paidCount = $students->filter(function ($s) {
        return strtolower($s->sc_challan_status ?? '') === 'paid';
    })->count();

    $unpaidCount = $students->filter(function ($s) {
        return strtolower($s->sc_challan_status ?? '') === 'unpaid';
    })->count();

    // Detect duplicate names
    $nameCounts = $students
        ->groupBy(fn ($s) => strtolower(trim($s->api_name)))
        ->map->count();

    $duplicateNames = $nameCounts
        ->filter(fn ($count) => $count > 1)
        ->keys();
@endphp

<div class="card mt-4 shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Hostel Students</h5>

        <div>
            <span class="badge badge-success p-2 mr-2">
                Paid: {{ $paidCount }}
            </span>

            <span class="badge badge-danger p-2">
                Unpaid: {{ $unpaidCount }}
            </span>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="studentsTable" class="table table-bordered table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Applicant ID</th>
                        <th>Name</th>
                        <th>Roll No</th>
                        <th>Permanent Address</th>
                        <th>Challan No</th>
                        <th>Challan Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($students as $index => $student)
                        @php
                            $status = strtolower($student->sc_challan_status ?? '');
                            $isDuplicate = $duplicateNames->contains(
                                strtolower(trim($student->api_name))
                            );
                        @endphp

                        <tr class="{{ $isDuplicate ? 'table-warning' : '' }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $student->aai_id }}</td>

                            <td class="{{ $isDuplicate ? 'font-weight-bold' : '' }}">
                                {{ $student->api_name }}
                                @if($isDuplicate)
                                    <span class="badge badge-danger ml-1">Duplicate</span>
                                @endif
                            </td>

                            <td>{{ $student->cms_sai_enrollment_no }}</td>
                            <td>{{ $student->api_permanentaddress }}</td>
                            <td>{{ $student->challan_no }}</td>

                            <td>
                                @if($status === 'paid')
                                    <span class="badge badge-success">Paid</span>
                                @elseif($status === 'unpaid')
                                    <span class="badge badge-danger">Unpaid</span>
                                @else
                                    <span class="badge badge-secondary">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No records found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
