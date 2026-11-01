<div class="hostel-stats-container mb-4">
    <!-- Session Allowance Analysis - Improved UI -->
    <div class="row">
        @foreach(['on_campus', 'off_campus'] as $key)
            @php 
                $stat = $allowanceStats[$key];
                $isOnCampus = $key == 'on_campus';
                $iconClass = $isOnCampus ? 'fa-university' : 'fa-building';
            @endphp
            
            {{-- Hidden fields to store stats for frontend validation --}}
            <input type="hidden" 
                   id="stats_{{ $gender }}_{{ $key }}_available" 
                   value="{{ $stat['available_seats'] }}">
            <input type="hidden" 
                   id="stats_{{ $gender }}_{{ $key }}_allowed" 
                   value="{{ $stat['allowed'] }}">
            
            <div class="col-md-6 mb-3">
                <div class="stats-card" style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; transition: all 0.3s ease;">
                    <!-- Card Header with Primary Color -->
                    <div class="card-header-gradient bg-primary" style="padding: 12px 20px;">
                        <div class="d-flex align-items-center">
                            <i class="fa {{ $iconClass }} text-white mr-2" style="font-size: 18px;"></i>
                            <h6 class="mb-0 text-white font-weight-bold text-uppercase" style="font-size: 13px; letter-spacing: 0.5px;">
                                {{ $stat['label'] }}
                            </h6>
                        </div>
                    </div>
                    
                    <!-- Stats Grid -->
                    <div class="card-body p-3">
                        <div class="row text-center">
                            <!-- Available Seats (Capacity) -->
                            <div class="col stat-item" style="border-right: 1px solid #f0f0f0;">
                                <div class="stat-icon mb-1">
                                    <i class="fa fa-university" style="color: #667eea; font-size: 16px;"></i>
                                </div>
                                <div class="stat-value font-weight-bold" style="font-size: 20px; color: #2d3748;">
                                    {{ $stat['available_seats'] }}
                                </div>
                                <div class="stat-label text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.3px;">
                                    Available Seats
                                </div>
                            </div>
                            
                            {{-- Allowed - COMMENTED OUT AS PER USER REQUEST
                            <div class="col stat-item" style="border-right: 1px solid #f0f0f0;">
                                <div class="stat-icon mb-1">
                                    <i class="fa fa-check-circle" style="color: #48bb78; font-size: 16px;"></i>
                                </div>
                                <div class="stat-value font-weight-bold" style="font-size: 20px; color: #2d3748;">
                                    {{ $stat['allowed'] }}
                                </div>
                                <div class="stat-label text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.3px;">
                                    Allowed
                                </div>
                            </div>
                            --}}

                            
                            <!-- UNPAID -->
                            <div class="col stat-item" style="border-right: 1px solid #f0f0f0;">
                                <div class="stat-icon mb-1">
                                    <i class="fa fa-exclamation-circle" style="color: #ed8936; font-size: 16px;"></i>
                                </div>
                                <div class="stat-value font-weight-bold" style="font-size: 20px; color: #2d3748;">
                                    {{ $stat['unpaid'] }}
                                </div>
                                <div class="stat-label text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.3px;">
                                    UNPAID
                                </div>
                            </div>
                            
                            <!-- PAID -->
                            <div class="col stat-item" style="border-right: 1px solid #f0f0f0;">
                                <div class="stat-icon mb-1">
                                    <i class="fa fa-check-circle" style="color: #48bb78; font-size: 16px;"></i>
                                </div>
                                <div class="stat-value font-weight-bold" style="font-size: 20px; color: #2d3748;">
                                    {{ $stat['paid'] }}
                                </div>
                                <div class="stat-label text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.3px;">
                                    PAID
                                </div>
                            </div>
                            
                            <!-- Remaining -->
                            <div class="col stat-item">
                                <div class="stat-icon mb-1">
                                    <i class="fa fa-calculator" style="color: #9f7aea; font-size: 16px;"></i>
                                </div>
                                <div class="stat-value font-weight-bold" style="font-size: 20px; color: {{ $stat['remaining'] > 0 ? '#48bb78' : '#f56565' }};">
                                    {{ $stat['remaining'] }}
                                </div>
                                <div class="stat-label text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.3px;">
                                    Remaining
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
.stats-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.12) !important;
    transform: translateY(-2px);
}

.stat-item {
    padding: 8px 4px;
}

@media (max-width: 768px) {
    .stat-value {
        font-size: 18px !important;
    }
    .stat-icon i {
        font-size: 14px !important;
    }
}
</style>
    {{-- Commented out detailed hostel breakdown tables - keeping only summary strip above
    <hr class="mt-0 mb-4">
    <!-- Side-by-Side Tables -->
    <div class="row">
        @foreach(['ON CAMPUS', 'OFF CAMPUS'] as $location)
            @php
                $hostels = $dashboardData[$location] ?? [];
                $stats = $locationStats[$location] ?? ['capacity'=>0, 'occupied'=>0, 'paid_unassigned'=>0, 'net_available'=>0];
                
                // Header colors
                $headerBg = '#e3f2fd'; 
            @endphp
            <div class="col-md-6">
                <!-- Table Card -->
                <div class="card border-0 mb-4" style="background: transparent !important; box-shadow: none !important; border: none !important;">
                    <div class="card-header py-2 border-0" style="background: {{ $headerBg }} !important; border-radius: 8px !important; margin-bottom: 5px !important;">
                        <h6 class="mb-0 font-weight-bold" style="font-size: 12px; color: #444 !important; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fa fa-map-marker mr-2"></i> {{ $location }}
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0" style="background: transparent;">
                                <thead style="background: transparent; font-size: 10px; text-transform: uppercase; color: #999; border-bottom: 1px solid #eee;">
                                    <tr>
                                        <th class="pl-3 py-2 border-0">Name</th>
                                        <th class="text-center py-2 border-0" title="Total Capacity">Total</th>
                                        <th class="text-center py-2 border-0" title="Occupied Beds">All Occupied</th>
                                        <th class="text-center py-2 border-0">Reserved (Occupied)</th>
                                        <th class="text-center py-2 border-0">Paid (Occupied)</th>
                                        <th class="text-center py-2 border-0" title="Available Beds">Vacant</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 12px;">
                                    @if(count($hostels) > 0)
                                        @foreach($hostels as $h)
                                            <tr style="border-bottom: 1px solid #f5f5f5; background: transparent;">
                                                <td class="pl-3 py-2 font-weight-bold text-dark">
                                                    {{ $h['name'] }}
                                                </td>
                                                <td class="text-center py-2 font-weight-bold">{{ $h['total_seats'] }}</td>
                                                <td class="text-center py-2 font-weight-bold">
                                                    {{ $h['physical_occupied'] }}
                                                </td>
                                                <td class="text-center py-2">
                                                    <a href="javascript:void(0)" class="drill-down-trigger font-weight-bold" 
                                                       data-hostel-id="{{ $h['id'] }}" data-type="reserved" data-hostel-name="{{ $h['name'] }}"
                                                       data-gender="{{ $gender }}"
                                                       style="color: inherit; text-decoration: underline;">
                                                        {{ $h['reserved_unpaid'] }}
                                                    </a>
                                                </td>
                                                <td class="text-center py-2">
                                                    <a href="javascript:void(0)" class="drill-down-trigger font-weight-bold" 
                                                       data-hostel-id="{{ $h['id'] }}" data-type="paid" data-hostel-name="{{ $h['name'] }}"
                                                       data-gender="{{ $gender }}"
                                                       style="color: inherit; text-decoration: underline;">
                                                        {{ $h['confirmed_paid'] }}
                                                    </a>
                                                </td>
                                                <td class="text-center py-2 font-weight-bold">{{ $h['physical_vacant'] }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">No hostels found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    --}}
</div>

<script>
/**
 * Handle Dashboard Drill-down List
 */
(function() {
    // Prevent multiple bindings
    $(document).off('click', '.drill-down-trigger').on('click', '.drill-down-trigger', function(e) {
        e.preventDefault();
        
        const btn = $(this);
        const hostelId = btn.data('hostel-id');
        const type = btn.data('type');
        const hostelName = btn.data('hostel-name');
        const sessionId = $('#session_id').val();
        
        if (!hostelId || !sessionId) return;
        
        const typeTitle = (type === 'paid') ? 'Confirmed (Paid)' : 'Reserved (Unpaid)';
        $('#drillDownTitle').text(`${typeTitle} Students - ${hostelName}`);
        $('#drillDownBody').empty();
        $('#drillDownLoading').show();
        $('#drillDownTable').hide();
        
        // Show modal stable way
        $('#drillDownModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
        
        $.ajax({
            url: '/api/hostel-dashboard/drill-down-list',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}'
            },
            data: {
                hostel_id: hostelId,
                type: type,
                session_id: sessionId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#drillDownLoading').hide();
                if (response.success && response.students && response.students.length > 0) {
                    let html = '';
                    response.students.forEach((s, index) => {
                        const statusBadge = (type === 'paid') ? 'badge-success' : 'badge-warning';
                        const statusText = (type === 'paid') ? 'PAID' : 'PENDING';
                        const applicantId = s.ha_aai_id || s.ha_enrolment_no || 'N/A';
                        
                        const floorRoomBed = (s.floor_name && s.room_no && s.bed_no) 
                            ? `${s.floor_name}, Room No# ${s.room_no}, Bed No# ${s.bed_no}` 
                            : 'N/A';
                        
                        html += `
                            <tr>
                                <td class="pl-4 py-3 font-weight-bold">${index + 1}</td>
                                <td class="py-3 font-weight-bold text-muted">${applicantId}</td>
                                <td class="py-3 font-weight-bold">${s.student_name || 'N/A'}</td>
                                <td class="py-3">${s.student_email || 'N/A'}</td>
                                <td class="py-3">${floorRoomBed}</td>
                                <td class="text-center py-3">
                                    <span class="badge ${statusBadge} px-3 py-2" style="font-size: 10px;">${statusText}</span>
                                </td>
                            </tr>
                        `;
                    });
                    $('#drillDownBody').html(html);
                    $('#drillDownTable').show();
                } else {
                    $('#drillDownBody').html('<tr><td colspan="6" class="text-center py-5 font-weight-bold text-muted">No students found.</td></tr>');
                    $('#drillDownTable').show();
                }
            },
            error: function() {
                $('#drillDownLoading').hide();
                $('#drillDownModal').modal('hide');
                if (typeof showToast === 'function') {
                    showToast('error', 'Failed to load student list.');
                }
            }
        });
    });
})();
</script>

