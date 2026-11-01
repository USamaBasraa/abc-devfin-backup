@extends('layouts.app')

@section('content')
@include('components.toast-notifications')
<link href="/css/bnu-layout.css" rel="stylesheet" />

<style>
    .cost-center-page .section-header {
        background: none !important;
        border: none !important;
        color: #252525 !important;
    }

    .cost-center-page .section-title {
        color: #2d3748;
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .cost-center-page .content-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        padding: 24px;
    }

    .cost-center-page .table thead th {
        background-color: #4562C3 !important;
        color: #fff !important;
        border-color: #4562C3 !important;
        white-space: nowrap;
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 700;
    }

    .cost-center-page .table td {
        vertical-align: middle;
    }

    .cost-center-page .search-input {
        border-radius: 999px;
        height: 42px;
        padding: 0 18px;
        border: 1px solid #d9deea;
        box-shadow: none;
    }

    .cost-center-page .primary-btn {
        background: #4562C3;
        border-color: #4562C3;
        color: #fff;
    }

    .cost-center-page .primary-btn:hover,
    .cost-center-page .primary-btn:focus {
        background: #3d56ab;
        border-color: #3d56ab;
        color: #fff;
    }

    .cost-center-page .secondary-btn {
        border-color: #d9deea;
        color: #374151;
        background: #fff;
    }

    .cost-center-page .table-status {
        text-align: center;
        padding: 22px;
        color: #6c757d;
        font-weight: 500;
    }

    .cost-center-page .mapping-summary {
        color: #5a6c7d;
        font-size: 14px;
        font-weight: 500;
    }

    .cost-center-page .badge-mapped {
        background: rgba(40, 167, 69, 0.12);
        color: #28a745;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 700;
    }

    .cost-center-page .badge-unmapped {
        background: rgba(217, 83, 79, 0.12);
        color: #d9534f;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 700;
    }

    .cost-center-page .pinnacle-school {
        color: #5a6c7d;
        font-size: 13px;
    }
</style>

<div class="container-fluid px-4 py-4 cost-center-page">
    <div class="card-header section-header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <div>
                <h4 class="mb-0 section-title">Cost Center Mapping</h4>
                <p class="text-muted mb-0">Map Pinnacle cost centers to CMS schools and departments.</p>
            </div>
            <button class="btn primary-btn mt-3 mt-md-0" type="button" id="refreshCostCenters">
                Refresh from API
            </button>
        </div>
    </div>

    <div class="content-card">
        <div class="row align-items-center mb-3">
            <div class="col-md-6 mb-2 mb-md-0">
                <input type="text" class="form-control search-input" id="costCenterSearchInput" placeholder="Search cost center, school or department...">
            </div>
            <div class="col-md-6 text-md-right">
                <span class="mapping-summary" id="costCenterSummary">Loading cost centers...</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table" id="costCenterTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 70px;">#</th>
                        <th style="min-width: 100px;">Code</th>
                        <th style="min-width: 140px;">Title</th>
                        <th style="min-width: 220px;">Pinnacle School</th>
                        <th style="min-width: 200px;">CMS School</th>
                        <th style="min-width: 200px;">CMS Department</th>
                        <th style="width: 110px;">Status</th>
                        <th style="width: 170px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="8" class="table-status">Loading cost centers...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    (function () {
        const schools = @json($schools);
        const departments = @json($departments);
        const dataUrl = '{{ route("cost-center-mapping.data") }}';
        const saveUrl = '{{ route("cost-center-mapping.store") }}';
        const csrfToken = '{{ csrf_token() }}';

        let costCenters = [];
        let filteredCostCenters = [];

        function escapeHtml(value) {
            return $('<div>').text(value || '').html();
        }

        function showPageToast(type, title, message) {
            if (typeof showToast === 'function') {
                showToast(type, message, title);
                return;
            }

            alert(message);
        }

        function schoolOptions(selectedId) {
            const selected = selectedId ? String(selectedId) : '';
            let options = '<option value="">-- Select School --</option>';

            schools.forEach(function (school) {
                const schoolId = String(school.school_id);
                const isSelected = schoolId === selected ? ' selected' : '';
                options += '<option value="' + escapeHtml(schoolId) + '"' + isSelected + '>' + escapeHtml(school.school_name) + '</option>';
            });

            return options;
        }

        function departmentOptions(schoolId, selectedId) {
            const selected = selectedId ? String(selectedId) : '';
            let options = '<option value="">-- Select Department --</option>';

            if (!schoolId) {
                return options;
            }

            departments.forEach(function (department) {
                if (String(department.dept_school_id) !== String(schoolId)) {
                    return;
                }

                const deptId = String(department.dept_id);
                const isSelected = deptId === selected ? ' selected' : '';
                options += '<option value="' + escapeHtml(deptId) + '"' + isSelected + '>' + escapeHtml(department.dept_name) + '</option>';
            });

            return options;
        }

        function isMapped(item) {
            return !!item.cms_school_id;
        }

        function updateSummary() {
            const mapped = costCenters.filter(isMapped).length;

            $('#costCenterSummary').text(
                filteredCostCenters.length + ' shown / ' + costCenters.length + ' total, ' + mapped + ' mapped'
            );
        }

        function renderTable() {
            const tbody = $('#costCenterTable tbody');
            tbody.empty();

            if (!filteredCostCenters.length) {
                tbody.append('<tr><td colspan="8" class="table-status">No cost centers found. Click "Refresh from API" to import.</td></tr>');
                updateSummary();
                return;
            }

            filteredCostCenters.forEach(function (item, index) {
                const mapped = isMapped(item);
                const badgeClass = mapped ? 'badge-mapped' : 'badge-unmapped';
                const badgeText = mapped ? 'Mapped' : 'Unmapped';
                const pinnacleSchool = item.school_title || '';
                const pinnacleSchoolId = item.school_id ? ' (' + item.school_id + ')' : '';

                tbody.append(
                    '<tr data-cost-center-id="' + escapeHtml(item.cost_center_id) + '">' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td>' + escapeHtml(item.cost_center_code) + '</td>' +
                        '<td>' + escapeHtml(item.cost_center_title) + '</td>' +
                        '<td class="pinnacle-school">' + escapeHtml(pinnacleSchool + pinnacleSchoolId) + '</td>' +
                        '<td>' +
                            '<select class="form-control school-select">' +
                                schoolOptions(item.cms_school_id) +
                            '</select>' +
                        '</td>' +
                        '<td>' +
                            '<select class="form-control department-select">' +
                                departmentOptions(item.cms_school_id, item.cms_dept_id) +
                            '</select>' +
                        '</td>' +
                        '<td><span class="' + badgeClass + '">' + badgeText + '</span></td>' +
                        '<td>' +
                            '<button type="button" class="btn btn-sm primary-btn save-mapping mr-2">Save</button>' +
                            '<button type="button" class="btn btn-sm secondary-btn remove-mapping">Remove</button>' +
                        '</td>' +
                    '</tr>'
                );
            });

            updateSummary();
        }

        function applySearch() {
            const search = ($('#costCenterSearchInput').val() || '').toLowerCase();

            if (!search) {
                filteredCostCenters = costCenters.slice();
                renderTable();
                return;
            }

            filteredCostCenters = costCenters.filter(function (item) {
                const haystack = [
                    item.cost_center_id,
                    item.cost_center_code,
                    item.cost_center_title,
                    item.school_id,
                    item.school_title,
                    item.cms_school_name,
                    item.cms_dept_name
                ].join(' ').toLowerCase();

                return haystack.indexOf(search) !== -1;
            });

            renderTable();
        }

        function loadCostCenters(forceRefresh) {
            $('#costCenterSummary').text('Loading cost centers...');
            $('#costCenterTable tbody').html('<tr><td colspan="8" class="table-status">Loading cost centers...</td></tr>');

            const requestData = forceRefresh ? { refresh: 1 } : {};

            $.get(dataUrl, requestData)
                .done(function (response) {
                    costCenters = response.data || [];
                    filteredCostCenters = costCenters.slice();
                    applySearch();

                    if (response.message) {
                        showPageToast(
                            response.status === 'error' ? 'error' : 'warning',
                            response.status === 'error' ? 'Error' : 'Notice',
                            response.message
                        );
                    }
                })
                .fail(function (xhr) {
                    const message = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Unable to load cost centers.';

                    costCenters = [];
                    filteredCostCenters = [];
                    renderTable();
                    showPageToast('error', 'Error', message);
                });
        }

        function syncRowMapping(costCenterId, cmsSchoolId, cmsDeptId) {
            costCenters = costCenters.map(function (item) {
                if (String(item.cost_center_id) !== String(costCenterId)) {
                    return item;
                }

                const school = schools.find(function (entry) {
                    return String(entry.school_id) === String(cmsSchoolId);
                });

                const department = departments.find(function (entry) {
                    return String(entry.dept_id) === String(cmsDeptId);
                });

                item.cms_school_id = cmsSchoolId || null;
                item.cms_dept_id = cmsDeptId || null;
                item.cms_school_name = school ? school.school_name : null;
                item.cms_dept_name = department ? department.dept_name : null;

                return item;
            });

            applySearch();
        }

        function saveMapping(row, cmsSchoolId, cmsDeptId) {
            const costCenterId = row.data('cost-center-id');
            const item = costCenters.find(function (costCenter) {
                return String(costCenter.cost_center_id) === String(costCenterId);
            });

            row.find('button').prop('disabled', true);

            $.ajax({
                url: saveUrl,
                method: 'POST',
                data: {
                    _token: csrfToken,
                    cost_center_id: costCenterId,
                    cost_center_title: item ? item.cost_center_title : '',
                    school_id: item ? item.school_id : '',
                    school_title: item ? item.school_title : '',
                    cms_school_id: cmsSchoolId || '',
                    cms_dept_id: cmsDeptId || ''
                }
            })
                .done(function (response) {
                    syncRowMapping(costCenterId, cmsSchoolId, cmsDeptId);
                    showPageToast('success', 'Success', response.message || 'Mapping saved successfully.');
                })
                .fail(function (xhr) {
                    const message = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Unable to save mapping.';

                    showPageToast('error', 'Error', message);
                })
                .always(function () {
                    row.find('button').prop('disabled', false);
                });
        }

        $('#costCenterSearchInput').on('input', applySearch);
        $('#refreshCostCenters').on('click', function () {
            loadCostCenters(true);
        });

        $('#costCenterTable').on('change', '.school-select', function () {
            const row = $(this).closest('tr');
            const schoolId = $(this).val();
            row.find('.department-select').html(departmentOptions(schoolId, ''));
        });

        $('#costCenterTable').on('click', '.save-mapping', function () {
            const row = $(this).closest('tr');
            const cmsSchoolId = row.find('.school-select').val();
            const cmsDeptId = row.find('.department-select').val();

            if (!cmsSchoolId) {
                showPageToast('warning', 'School Required', 'Please select a school before saving.');
                return;
            }

            saveMapping(row, cmsSchoolId, cmsDeptId);
        });

        $('#costCenterTable').on('click', '.remove-mapping', function () {
            const row = $(this).closest('tr');
            row.find('.school-select').val('');
            row.find('.department-select').html(departmentOptions('', ''));
            saveMapping(row, '', '');
        });

        $(document).ready(function () {
            loadCostCenters(true);
        });
    })();
</script>
@endsection

