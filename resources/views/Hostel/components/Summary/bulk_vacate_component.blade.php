
<!-- Bulk Vacate / Session Rollover Modal -->
<div class="modal fade" id="bulkVacateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title font-weight-bold text-dark">
                    <i class="fa fa-refresh mr-2 text-primary"></i> Vacate Hostel Seats
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                
                <div class="alert alert-light border-0 mb-4 p-3 bg-light rounded text-center">
                    <p class="mb-0 text-dark font-weight-bold">
                        <i class="fa fa-info-circle text-primary mr-1"></i> 
                        Vacate hostel seats except for students who have <span class="text-success border-bottom border-success">PAID</span> for the selected session.
                    </p>
                </div>

                <div class="row mb-4">
                    <div class="col-md-8 mx-auto">
                        <label class="font-weight-bold text-dark h6">Select Session:</label>
                        <div class="input-group">
                            <select class="form-control form-control-lg shadow-sm" id="bulk_target_session">
                                <option value="">Select Session...</option>
                                @foreach($sessions as $session)
                                <option value="{{ $session->sess_id ?? $session->SESS_ID }}">
                                    {{ $session->sess_name ?? $session->SESS_NAME }}
                                </option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-primary font-weight-bold shadow-sm" type="button" onclick="analyzeBulkVacate()">
                                    <i class="fa fa-calculator mr-2"></i> Analyze
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="bulkAnalysisLoading" class="text-center py-5" style="display: none;">
                    <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;"></div>
                    <p class="mt-3 font-weight-bold text-muted h5">Analyzing Hostel Occupancy...</p>
                </div>

                <!-- Analysis Result (Accordion) -->
                <div id="bulkAnalysisResult" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-weight-bold m-0 text-dark">Hostel Analysis</h5>
                    </div>

                    <div class="row" id="vacateGrid">
                        <!-- Grid Items will be injected here -->
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-secondary font-weight-bold px-4" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal (Professional) -->
<div class="modal fade" id="vacationConfirmModal" tabindex="-1" role="dialog" data-backdrop="static" style="z-index: 100000;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fa fa-info-circle mr-2"></i>Confirm Bulk Vacate</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="fa fa-bed fa-4x text-primary opacity-50"></i>
                </div>
                <h5 class="font-weight-bold mb-3" id="confModalHostelName">Hostel Name</h5>
                <p class="mb-4">
                    Are you sure you want to vacate <strong class="text-primary" id="confModalCount">0</strong> students?
                    <br>This will remove all students who have <span class="text-primary font-weight-bold">NOT PAID</span> for the selected session.
                </p>
                <div class="alert alert-light border small text-left px-3 shadow-sm rounded">
                    <i class="fa fa-info-circle text-primary mr-1"></i> <strong>Note:</strong> Associated Unpaid Hostel Challans will be automatically <strong>CANCELLED</strong>.
                </div>
            </div>
            <div class="modal-footer bg-light border-0 justify-content-center">
                <button type="button" class="btn btn-light border font-weight-bold px-4" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary font-weight-bold px-4 shadow-sm" onclick="proceedWithVacate()">
                    <i class="fa fa-check mr-2"></i> Confirm & Vacate
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Professional Loading Overlay -->
<div id="vacateProcessingOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.9); z-index: 100001; backdrop-filter: blur(2px);">
    <div class="d-flex flex-column justify-content-center align-items-center h-100">
        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
        <h4 class="font-weight-bold text-dark">Processing Vacate Operation...</h4>
        <p class="text-muted mb-0">Freeing Beds & Cancelling Challans</p>
        <p class="text-success font-weight-bold small mt-2"><i class="fa fa-check-circle mr-1"></i> Safe List Protected</p>
    </div>
</div>

<script>
    var globalAnalysisData = [];

    function openBulkVacateModal() {
        $('#bulkVacateModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
        $('#bulkAnalysisResult').hide();
        $('#bulkAnalysisLoading').hide();
    }

    function analyzeBulkVacate() {
        var sessionId = $('#bulk_target_session').val();
        
        if (!sessionId) {
            showToast('error', 'Please select the Target Session first.');
            return;
        }

        $('#bulkAnalysisResult').hide();
        $('#bulkAnalysisLoading').show();

        $.ajax({
            url: '/api/hostel-summary/get-bulk-analysis',
            type: 'GET',
            data: { session_id: sessionId },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
                'Accept': 'application/json'
            },
            dataType: 'json',
            success: function(response) {
                $('#bulkAnalysisLoading').hide();
                if (response.success) {
                    globalAnalysisData = response.analysis;
                    renderBulkAccordion(response.analysis, sessionId);
                    $('#bulkAnalysisResult').slideDown();
                } else {
                    showToast('error', response.message || 'Analysis failed');
                }
            },
            error: function() {
                $('#bulkAnalysisLoading').hide();
                showToast('error', 'Failed to connect to server.');
            }
        });
    }

    function renderBulkAccordion(data, sessionId) {
        globalAnalysisData = data;
        var container = $('#vacateGrid');
        container.empty();

        if (data.length === 0) {
            container.html('<div class="col-12"><div class="alert alert-warning">No data available.</div></div>');
            return;
        }

        $.each(data, function(index, hostel) {
            
            // Stats
            var total = hostel.total_beds > 0 ? hostel.total_beds : 1;
            var countFree = hostel.free_beds;
            var countSafe = hostel.safe_count; 
            var countVacate = hostel.vacate_count;

            var html = `
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border shadow-sm" style="border-radius: 8px;">
                    <div class="card-body p-3">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                             <div class="d-flex align-items-center" style="min-width: 0;">
                                <div class="rounded p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(0,0,0,0.05); color: #333;">
                                    <i class="fa fa-building"></i>
                                </div>
                                <div class="text-truncate">
                                    <h5 class="font-weight-bold text-dark mb-0 text-truncate" title="${hostel.hostel_name}">${hostel.hostel_name}</h5>
                                    <small class="text-muted" style="font-size: 13px;">Total Beds: ${hostel.total_beds}</small>
                                </div>
                             </div>
                             <div class="pl-2">
                                ${countVacate > 0 ? 
                                    `<span class="badge badge-light border text-danger" style="font-size: 12px;">To Vacate: ${countVacate}</span>` : 
                                    `<span class="badge badge-light border text-success" style="font-size: 12px;">Clean</span>`
                                }
                             </div>
                        </div>

                        <!-- Simple Grid Stats (No Colors) -->
                        <div class="row text-center mb-3">
                            <div class="col-6 border-right">
                                <div class="h4 font-weight-bold text-dark mb-0">${countFree}</div>
                                <small class="text-muted" style="font-size: 12px;">Free</small>
                            </div>
                            <div class="col-6">
                                <div class="h4 font-weight-bold text-dark mb-0">${countSafe}</div>
                                <small class="text-muted" style="font-size: 12px;">PAID</small>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="row">
                             <div class="col-6 pr-2">
                                <button class="btn btn-sm btn-outline-secondary btn-block" type="button" data-toggle="collapse" data-target="#listDetails${hostel.hostel_id}">
                                    Show List (PAID: ${countSafe})
                                </button>
                             </div>
                             <div class="col-6 pl-2">
                                ${countVacate > 0 ? 
                                    `<button class="btn btn-sm btn-primary btn-block" onclick="confirmVacateSingle(${hostel.hostel_id}, '${hostel.hostel_name}', ${countVacate})">
                                        Vacate (${countVacate})
                                    </button>` : 
                                    `<button class="btn btn-sm btn-light btn-block text-muted border disabled" disabled>Empty</button>`
                                }
                             </div>
                        </div>

                        <!-- Collapsible List -->
                         <div class="collapse mt-3" id="listDetails${hostel.hostel_id}">
                            <div class="bg-light border rounded p-0" style="max-height: 200px; overflow-y: auto;">
                                ${renderSafeList(hostel.safe_students)}
                            </div>
                         </div>
                    </div>
                </div>
            </div>
            `;
            container.append(html);
        });
        
        // Init Tooltips
        if(typeof $().tooltip === 'function') $('[data-toggle="tooltip"]').tooltip();
    }

    function renderSafeList(students) {
        if (!students || students.length === 0) return '<div class="text-muted text-center small p-1">No students.</div>';
        
        var list = '<ul class="list-group list-group-flush">';
        $.each(students, function(i, std) {
            list += `
                <li class="list-group-item px-2 py-1 bg-transparent border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="mr-2" style="min-width: 0;">
                            <div class="text-truncate font-weight-bold text-dark" style="font-size: 13px;">${std.student_name || 'N/A'}</div>
                            <div class="text-muted" style="font-size: 11px;">${std.ha_enrolment_no || std.ha_aai_id}</div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="badge badge-light border text-dark" style="font-size: 11px;">${std.hr_room_no} / Bed ${std.hb_bed_number}</div>
                            <div class="text-muted small" style="font-size: 11px;">${std.hostel_name || ''} - ${std.floor_name || ''}</div>
                        </div>
                    </div>
                </li>
            `;
        });
        list += '</ul>';
        return list;
    }

    var pendingVacateHostelId = null;

    function confirmVacateSingle(hostelId, hostelName, vacateCount) {
        pendingVacateHostelId = hostelId;
        $('#confModalHostelName').text(hostelName);
        $('#confModalCount').text(vacateCount);
        $('#vacationConfirmModal').modal('show');
    }

    function proceedWithVacate() {
        $('#vacationConfirmModal').modal('hide');
        if(pendingVacateHostelId) {
             performVacate(pendingVacateHostelId);
        }
    }

    // executeVacateAll Removed - User requested Specific Hostel Only Scope

    function performVacate(hostelId) {
        var sessionId = $('#bulk_target_session').val();
        
        // Show Professional Overlay
        $('#vacateProcessingOverlay').fadeIn();
        
        $.ajax({
            url: '/api/hostel-summary/rollover-vacate',
            type: 'POST',
            data: {
                session_id: sessionId,
                hostel_id: hostelId, // Specific Hostel ID Required
                remarks: 'Bulk Vacate via Session Rollover Tool'
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
                'Accept': 'application/json'
            },
            success: function(response) {
                $('#vacateProcessingOverlay').fadeOut();
                if (response.success) {
                    showToast('success', response.message);
                    // Refresh analysis
                    analyzeBulkVacate();
                    // Also refresh main summary if visible
                    if (typeof loadSummary === 'function') loadSummary();
                } else {
                    showToast('error', response.message);
                }
            },
            error: function(xhr) {
                $('#vacateProcessingOverlay').fadeOut();
                var msg = 'Error executing vacate.';
                if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                showToast('error', msg);
            }
        });
    }
</script>
