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
        background-color: #17675F !important;
        color: #fff !important;
        border-color: #17675F !important;
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
        background: #17675F !important;
        border-color: #17675F !important;
        color: #fff !important;
        border-radius: 999px !important;
        padding: 8px 18px !important;
        font-weight: 600 !important;
        box-shadow: none !important;
    }

    .cost-center-page .primary-btn:hover,
    .cost-center-page .primary-btn:focus {
        background: #0f4d47 !important;
        border-color: #0f4d47 !important;
        color: #fff !important;
    }

    .cost-center-page .secondary-btn {
        border-color: #17675F !important;
        color: #17675F !important;
        background: #fff !important;
        border-radius: 999px !important;
        padding: 8px 18px !important;
        font-weight: 600 !important;
        box-shadow: none !important;
    }

    .cost-center-page .secondary-btn:hover,
    .cost-center-page .secondary-btn:focus {
        background: #e7f3f1 !important;
        border-color: #17675F !important;
        color: #0f4d47 !important;
    }

    .cost-center-page .action-btns {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
        justify-content: flex-start;
    }

    .cost-center-page .action-btns .btn {
        border-radius: 999px !important;
        padding: 5px 14px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        line-height: 1.3 !important;
        white-space: nowrap;
        box-shadow: none !important;
    }

    .cost-center-page .action-btns .btn-save {
        background: #17675F !important;
        border-color: #17675F !important;
        color: #fff !important;
    }

    .cost-center-page .action-btns .btn-save:hover {
        background: #0f4d47 !important;
        border-color: #0f4d47 !important;
        color: #fff !important;
    }

    .cost-center-page .action-btns .btn-remove {
        background: #fff !important;
        border: 1px solid #cfd8d6 !important;
        color: #4a5b58 !important;
    }

    .cost-center-page .action-btns .btn-remove:hover {
        background: #f4faf9 !important;
        border-color: #17675F !important;
        color: #17675F !important;
    }

    .cost-center-page .header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cost-center-page select.form-control {
        height: 42px !important;
        min-height: 42px !important;
        border-radius: 20px !important;
        padding: 8px 40px 8px 16px !important;
        background-color: #e9ecef !important;
        border: none !important;
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

    .cost-center-page .dept-multi-select {
        position: relative;
        width: 100%;
        min-width: 200px;
    }

    .cost-center-page .dept-multi-toggle {
        width: 100%;
        height: 42px;
        border: none;
        border-radius: 20px;
        background: #e9ecef;
        color: #2d3748;
        padding: 8px 36px 8px 16px;
        text-align: left;
        font-size: 14px;
        font-weight: 500;
        position: relative;
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cost-center-page .dept-multi-toggle:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    .cost-center-page .dept-multi-toggle::after {
        content: '';
        position: absolute;
        right: 14px;
        top: 50%;
        width: 0;
        height: 0;
        margin-top: -2px;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 6px solid #5a6c7d;
    }

    .cost-center-page .dept-multi-menu {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        z-index: 30;
        background: #fff;
        border: 1px solid #d9deea;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        max-height: 240px;
        overflow-y: auto;
        padding: 8px 0;
    }

    .cost-center-page .dept-multi-select.open .dept-multi-menu {
        display: block;
    }

    .cost-center-page .dept-multi-option {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 8px 14px;
        margin: 0;
        cursor: pointer;
        font-size: 13px;
        color: #2d3748;
        font-weight: 500;
    }

    .cost-center-page .dept-multi-option:hover {
        background: #e7f3f1;
    }

    .cost-center-page .dept-multi-option input {
        margin-top: 2px;
        flex-shrink: 0;
    }

    .cost-center-page .dept-multi-empty {
        padding: 12px 14px;
        color: #6c757d;
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
            <div class="header-actions mt-3 mt-md-0">
                <button class="btn primary-btn" type="button" id="refreshCostCenters">
                    <i class="fa fa-refresh"></i> Refresh from API
                </button>
            </div>
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

        function normalizeDeptIds(value) {
            if (Array.isArray(value)) {
                return value.map(function (id) {
                    return String(id);
                }).filter(Boolean);
            }

            if (value === null || value === undefined || value === '') {
                return [];
            }

            return String(value)
                .split(',')
                .map(function (id) {
                    return id.trim();
                })
                .filter(Boolean);
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

        function departmentsForSchool(schoolId) {
            if (!schoolId) {
                return [];
            }

            return departments.filter(function (department) {
                return String(department.dept_school_id) === String(schoolId);
            });
        }

        function departmentDropdownLabel(selectedIds) {
            const ids = normalizeDeptIds(selectedIds);

            if (!ids.length) {
                return '-- Select Department(s) --';
            }

            if (ids.length === 1) {
                const department = departments.find(function (entry) {
                    return String(entry.dept_id) === String(ids[0]);
                });

                return department ? department.dept_name : (ids.length + ' selected');
            }

            return ids.length + ' departments selected';
        }

        function departmentMultiSelect(schoolId, selectedIds) {
            const ids = normalizeDeptIds(selectedIds);
            const schoolDepartments = departmentsForSchool(schoolId);
            const disabled = schoolId ? '' : ' disabled';
            let menuHtml = '';

            if (!schoolId) {
                menuHtml = '<div class="dept-multi-empty">Select a school first</div>';
            } else if (!schoolDepartments.length) {
                menuHtml = '<div class="dept-multi-empty">No departments found</div>';
            } else {
                schoolDepartments.forEach(function (department) {
                    const deptId = String(department.dept_id);
                    const checked = ids.indexOf(deptId) !== -1 ? ' checked' : '';
                    menuHtml +=
                        '<label class="dept-multi-option">' +
                            '<input type="checkbox" class="dept-checkbox" value="' + escapeHtml(deptId) + '"' + checked + '>' +
                            '<span>' + escapeHtml(department.dept_name) + '</span>' +
                        '</label>';
                });
            }

            return '' +
                '<div class="dept-multi-select">' +
                    '<button type="button" class="dept-multi-toggle"' + disabled + '>' +
                        escapeHtml(departmentDropdownLabel(ids)) +
                    '</button>' +
                    '<div class="dept-multi-menu">' + menuHtml + '</div>' +
                '</div>';
        }

        function getSelectedDeptIds(row) {
            const ids = [];

            row.find('.dept-checkbox:checked').each(function () {
                ids.push(String($(this).val()));
            });

            return ids;
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
                const selectedDeptIds = normalizeDeptIds(
                    item.cms_dept_ids && item.cms_dept_ids.length
                        ? item.cms_dept_ids
                        : item.cms_dept_id
                );

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
                            departmentMultiSelect(item.cms_school_id, selectedDeptIds) +
                        '</td>' +
                        '<td><span class="' + badgeClass + '">' + badgeText + '</span></td>' +
                        '<td>' +
                            '<div class="action-btns">' +
                                '<button type="button" class="btn btn-sm btn-save save-mapping"><i class="fa fa-save"></i> Save</button>' +
                                '<button type="button" class="btn btn-sm btn-remove remove-mapping"><i class="fa fa-times"></i> Remove</button>' +
                            '</div>' +
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

            const requestData = { _: Date.now() };
            // refresh=1 syncs Pinnacle metadata only; do not send on normal page load
            if (forceRefresh) { requestData.refresh = 1; }

            $.ajax({ url: dataUrl, method: 'GET', data: requestData, cache: false })
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

        function syncRowMapping(costCenterId, cmsSchoolId, cmsDeptIds) {
            const selectedDeptIds = normalizeDeptIds(cmsDeptIds);

            costCenters = costCenters.map(function (item) {
                if (String(item.cost_center_id) !== String(costCenterId)) {
                    return item;
                }

                const school = schools.find(function (entry) {
                    return String(entry.school_id) === String(cmsSchoolId);
                });

                const selectedDepartments = departments.filter(function (entry) {
                    return selectedDeptIds.indexOf(String(entry.dept_id)) !== -1;
                });

                item.cms_school_id = cmsSchoolId || null;
                item.cms_dept_ids = selectedDeptIds;
                item.cms_dept_id = selectedDeptIds.length ? selectedDeptIds[0] : null;
                item.cms_school_name = school ? school.school_name : null;
                item.cms_dept_name = selectedDepartments.length
                    ? selectedDepartments.map(function (department) {
                        return department.dept_name;
                    }).join(', ')
                    : null;

                return item;
            });

            applySearch();
        }

        function saveMapping(row, cmsSchoolId, cmsDeptIds) {
            const costCenterId = row.attr('data-cost-center-id'); // keep IDs like 03006 intact
            const selectedDeptIds = normalizeDeptIds(cmsDeptIds);
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
                    cms_dept_id: selectedDeptIds.length ? selectedDeptIds[0] : '',
                    cms_dept_ids: selectedDeptIds
                }
            })
                .done(function (response) {
                    syncRowMapping(costCenterId, cmsSchoolId, selectedDeptIds);
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
            loadCostCenters(true); // explicit user action: import/update Pinnacle metadata only
        });

        $(document).on('click', function (event) {
            if (!$(event.target).closest('.dept-multi-select').length) {
                $('.dept-multi-select').removeClass('open');
            }
        });

        $('#costCenterTable').on('click', '.dept-multi-toggle', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if ($(this).prop('disabled')) {
                return;
            }

            const wrapper = $(this).closest('.dept-multi-select');
            $('.dept-multi-select').not(wrapper).removeClass('open');
            wrapper.toggleClass('open');
        });

        $('#costCenterTable').on('click', '.dept-multi-menu', function (event) {
            event.stopPropagation();
        });

        $('#costCenterTable').on('change', '.dept-checkbox', function () {
            const wrapper = $(this).closest('.dept-multi-select');
            const selectedIds = [];

            wrapper.find('.dept-checkbox:checked').each(function () {
                selectedIds.push(String($(this).val()));
            });

            wrapper.find('.dept-multi-toggle').text(departmentDropdownLabel(selectedIds));
        });

        $('#costCenterTable').on('change', '.school-select', function () {
            const row = $(this).closest('tr');
            const schoolId = $(this).val();
            row.find('td').eq(5).html(departmentMultiSelect(schoolId, []));
        });

        $('#costCenterTable').on('click', '.save-mapping', function () {
            const row = $(this).closest('tr');
            const cmsSchoolId = row.find('.school-select').val();
            const cmsDeptIds = getSelectedDeptIds(row);

            if (!cmsSchoolId) {
                showPageToast('warning', 'School Required', 'Please select a school before saving.');
                return;
            }

            if (!cmsDeptIds.length) {
                showPageToast('warning', 'Department Required', 'Please select at least one department before saving.');
                return;
            }

            saveMapping(row, cmsSchoolId, cmsDeptIds);
        });

        $('#costCenterTable').on('click', '.remove-mapping', function () {
            const row = $(this).closest('tr');
            row.find('.school-select').val('');
            row.find('td').eq(5).html(departmentMultiSelect('', []));
            saveMapping(row, '', []);
        });

        $(document).ready(function () {
            loadCostCenters(false); // no API sync on normal page open — preserves CMS mappings
        });
    })();
</script>
@endsection

