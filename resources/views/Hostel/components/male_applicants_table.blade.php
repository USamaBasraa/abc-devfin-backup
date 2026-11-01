<style>
    /* Male Applicants Table Styles */
    .table tbody tr.allowed-row {
        background-color: rgba(16, 185, 129, 0.05);
    }

    #male_aplicants_wrapper .dataTables_scrollHead thead th {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    #male_aplicants_wrapper .dataTables_scrollBody {
        overflow-x: auto !important;
        overflow-y: visible !important;
        max-height: none !important;
        height: auto !important;
    }

    #male_aplicants_wrapper .hostel-dt-table-wrap {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
</style>

<script>
    function populateMaleApplicants(applicants) {
        const tbody = $('#male-applicants-tbody');
        tbody.empty();

        // Convert object to array if needed
        const applicantsArray = Array.isArray(applicants) ? applicants : Object.values(applicants);

        applicantsArray.forEach(function (applicant) {
            const rowClass = applicant.allowed_status == 'Allowed' ? 'allowed-row' : '';
            const applyDate = applicant.aai_apply_date ? formatDate(applicant.aai_apply_date) : 'N/A';

            // Handle checkbox state based on server data
            let checkboxAttributes = '';
            if (applicant.allowed_status == 'Allowed') {
                // If allowed, checkbox should be disabled
                checkboxAttributes = applicant.ha_disable_map_display == 'Yes' ? 'checked disabled' : 'disabled';
            } else {
                // If not allowed, checkbox should be enabled
                checkboxAttributes = applicant.ha_disable_map_display == 'Yes' ? 'checked' : '';
            }

            // Check if challan is paid
            const isPaid = applicant.sc_challan_status === 'PAID';

            let actionButton = '';
            if (applicant.allowed_status == 'Allowed') {
                if (isPaid) {
                    actionButton = `<span class="badge badge-seat-reserved"><i class="fa fa-bed mr-1"></i>Seat Reserved</span>`;
                } else {
                    actionButton = `<button type="button" class="btn btn-outline-danger btn-sm" onclick="showRejectModal('${applicant.aai_id}', 'new')">
                    <i class="fa fa-times-circle mr-1"></i>Reject
                   </button>`;
                }
            } else {
                // Check if this is external hostel type
                const hostelType = $('#hostel_type').val();
                if (hostelType === 'regular_outside') {
                    actionButton = `<button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                    <i class="fa fa-check-circle mr-1"></i>Allow
                   </button>`;
                } else {
                    // Always show Allow button enabled
                    actionButton = `<button type="button" class="btn btn-outline-success btn-sm" onclick="showAllowModal('${applicant.aai_id}', 'new')">
                    <i class="fa fa-check-circle mr-1"></i>Allow
                   </button>`;
                }
            }

            const row = `
            <tr class="${rowClass}" data-challan-status="${applicant.sc_challan_status || ''}" data-location="${applicant.hostel_location_preference || ''}">
                <td class="text-nowrap">${applicant.aai_id || 'N/A'}</td>
                <td class="text-nowrap">${applicant.cms_sai_enrollment_no || 'N/A'}</td>
                <td>${applicant.admission_sch_round || 'N/A'}</td>
                <td class="col-name">${applicant.api_name || 'N/A'}</td>
                <td>${applicant.api_permanentcity || 'N/A'}</td>
                <td class="col-address">${applicant.api_permanentaddress || 'N/A'}</td>
                <td class="text-nowrap">${applyDate}</td>
                <td>
                    <span class="badge ${applicant.allowed_status === 'Allowed' ? 'badge-allowed' : applicant.allowed_status === 'Rejected' ? 'badge-danger' : 'badge-secondary'}">
                        <i class="fa ${applicant.allowed_status === 'Allowed' ? 'fa-check-circle' : applicant.allowed_status === 'Rejected' ? 'fa-times-circle' : 'fa-question-circle'}"></i>
                        ${applicant.allowed_status || 'N/A'}
                    </span>
                </td>
                <td>${applicant.hostel_name || 'N/A'}</td>
                <td>${(applicant.floor_name && applicant.room_no && applicant.bed_no)
                    ? applicant.floor_name + ', R' + applicant.room_no + ', B' + applicant.bed_no
                    : 'N/A'}</td>
                <td class="col-scholarship">${applicant.scholarship_details || 'N/A'}</td>
                <td>${applicant.last_degree_marks || 'N/A'}</td>
                <td>
                    <span class="badge ${applicant.sc_challan_status === 'PAID' ? 'badge-paid' : applicant.sc_challan_status === 'UNPAID' ? 'badge-unpaid' : 'badge-secondary'}">
                        <i class="fa ${applicant.sc_challan_status === 'PAID' ? 'fa-credit-card' : applicant.sc_challan_status === 'UNPAID' ? 'fa-exclamation-triangle' : 'fa-question-circle'}"></i>
                        ${applicant.sc_challan_status || 'N/A'}
                    </span>
                </td>
                <td class="text-center">
                    <div class="form-check d-inline-flex justify-content-center">
                        <input class="form-check-input" type="checkbox" name="seat_reservation_map_display" id="dummy_${applicant.aai_id}" ${checkboxAttributes}>
                    </div>
                </td>
                <td class="text-center">
                    ${actionButton}
                </td>
            </tr>
        `;
            tbody.append(row);
        });

        // Initialize DataTable for male applicants
        if ($.fn.DataTable.isDataTable('#male_aplicants')) {
            $('#male_aplicants').DataTable().destroy();
        }

        $('#male_aplicants').DataTable({
            scrollX: true,
            autoWidth: false,
            scrollCollapse: true,
            dom: '<"row hostel-dt-toolbar"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
                '<"row hostel-dt-controls"<"col-sm-12 col-md-6"l>>' +
                '<"row"<"col-sm-12"<"custom-filters">>>' +
                '<"hostel-dt-table-wrap"tr>' +
                '<"row hostel-dt-footer"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            columnDefs: [
                { className: 'text-nowrap', targets: [0, 1, 6] },
                { className: 'text-center', targets: [13, 14] },
                { width: '85px', targets: 0 },
                { width: '105px', targets: 1 },
                { width: '95px', targets: 2 },
                { width: '120px', targets: 3 },
                { width: '100px', targets: 4 },
                { width: '170px', targets: 5 },
                { width: '95px', targets: 6 },
                { width: '115px', targets: 7 },
                { width: '95px', targets: 8 },
                { width: '140px', targets: 9 },
                { width: '130px', targets: 10 },
                { width: '80px', targets: 11 },
                { width: '100px', targets: 12 },
                { width: '75px', targets: 13 },
                { width: '110px', targets: 14 }
            ],
            drawCallback: function () {
                this.api().columns.adjust();
            },
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Male Hostel Applicants',
                    text: '<i class="fa fa-file-excel-o mr-1"></i>Excel',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] // Include all columns except Map Display and Actions
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Male Hostel Applicants',
                    text: '<i class="fa fa-file-pdf-o mr-1"></i>PDF',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] // Include all columns except Map Display and Actions
                    }
                },
                {
                    extend: 'print',
                    title: 'Male Hostel Applicants',
                    text: '<i class="fa fa-print mr-1"></i>Print',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] // Include all columns except Map Display and Actions
                    }
                }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            responsive: false,
            language: {
                search: "Search applicants:",
                lengthMenu: "Show _MENU_ applicants per page",
                info: "Showing _START_ to _END_ of _TOTAL_ applicants",
                infoEmpty: "Showing 0 to 0 of 0 applicants",
                infoFiltered: "(filtered from _MAX_ total applicants)"
            },
            initComplete: function () {
                var api = this.api();
                api.columns.adjust();
                setTimeout(function () {
                    api.columns.adjust();
                }, 150);

                $(window).on('resize.hostelMaleTable', function () {
                    if ($.fn.DataTable.isDataTable('#male_aplicants')) {
                        $('#male_aplicants').DataTable().columns.adjust();
                    }
                });

                // Create professional filter container
                var filterContainer = $('<div class="filter-section hostel-dt-filters">' +
                    '<div class="filter-content">' +
                    '<div class="row align-items-end">' +
                    '<div class="col-md-6 col-lg-5">' +
                    '<div class="filter-group">' +
                    '<label class="filter-label"><i class="fa fa-credit-card mr-1"></i> Payment</label>' +
                    '<div class="select-wrapper">' +
                    '<select class="form-control filter-select" id="male-challan-filter">' +
                    '<option value="">All Records</option>' +
                    '<option value="PAID">Paid</option>' +
                    '<option value="UNPAID">Unpaid</option>' +
                    '</select>' +
                    '<i class="fa fa-chevron-down select-arrow"></i>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-6 col-lg-5">' +
                    '<div class="filter-group">' +
                    '<label class="filter-label"><i class="fa fa-map-marker mr-1"></i> Location</label>' +
                    '<div class="select-wrapper">' +
                    '<select class="form-control filter-select" id="male-location-filter">' +
                    '<option value="">All Locations</option>' +
                    '<option value="on_campus">On Campus</option>' +
                    '<option value="off_campus">Off Campus</option>' +
                    '</select>' +
                    '<i class="fa fa-chevron-down select-arrow"></i>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');

                // Insert filters after the table header
                $(api.table().container()).find('.custom-filters').html(filterContainer);

                // Filters
                var challanFilter = $('#male-challan-filter');
                var locationFilter = $('#male-location-filter');

                if (window.maleHostelTableFilter) {
                    $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function (fn) {
                        return fn !== window.maleHostelTableFilter;
                    });
                }

                window.maleHostelTableFilter = function (settings, data, dataIndex) {
                    if (settings.nTable.id !== 'male_aplicants') {
                        return true;
                    }

                    var paymentValue = challanFilter.val();
                    var locationValue = locationFilter.val();
                    var row = api.row(dataIndex).node();
                    var $row = $(row);

                    var paymentMatch = true;
                    if (paymentValue !== '') {
                        var challanText = data[12];
                        var div = document.createElement("div");
                        div.innerHTML = challanText;
                        challanText = (div.textContent || div.innerText || "").trim();

                        if (paymentValue === 'UNPAID') {
                            var allowedStatus = data[7];
                            div.innerHTML = allowedStatus;
                            var allowedText = (div.textContent || div.innerText || "").trim();
                            paymentMatch = (challanText === 'UNPAID' ||
                                (allowedText === 'Allowed' && (challanText === 'N/A' || challanText === '')));
                        } else {
                            paymentMatch = (challanText === paymentValue);
                        }
                    }

                    var locationMatch = true;
                    if (locationValue !== '') {
                        var rowLocation = $row.attr('data-location') || '';
                        locationMatch = (rowLocation === 'both' || rowLocation === locationValue);
                    }

                    return paymentMatch && locationMatch;
                };

                $.fn.dataTable.ext.search.push(window.maleHostelTableFilter);

                function applyMaleFilters() {
                    api.draw();
                }

                challanFilter.on('change', applyMaleFilters);
                locationFilter.on('change', applyMaleFilters);
            }
        });
    }
</script>