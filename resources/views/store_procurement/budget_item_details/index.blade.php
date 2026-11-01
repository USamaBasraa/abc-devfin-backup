@extends('layouts.app')
{{-- @dd("working"); --}}

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        <div class="bg-white border-0" style="padding: 20px !important">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                <div>
                    <h4 class="mb-0 text-primary">Budget Item Insights</h4>
                    <small class="text-muted d-block">Overview of budget heads and their associated items across all departments.</small>
                </div>
                <div class="text-md-right mt-3 mt-md-0">
                    <span class="badge badge-pill badge-primary mr-md-2 mb-2 mb-md-0">
                        {{ number_format($totalItems) }} Items
                    </span>
                    <span class="badge badge-pill badge-light text-dark">
                        {{ number_format(count($budgets)) }} Budget Heads
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('budget-item-details.index') }}" class="mb-4">
                <div class="form-row">
                    <div class="form-group col-lg-4 col-md-6">
                        <label class="font-weight-semibold text-muted mb-1">Academic Year</label>
                        <select name="academic_year" class="form-control">
                            <option value="">All Academic Years</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->ay_id }}"
                                    {{ ($selectedFilters['academic_year_id'] ?? null) == $year->ay_id ? 'selected' : '' }}>
                                    {{ $year->ay_name ?? ('Academic Year ' . $year->ay_id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-4 col-md-6">
                        <label class="font-weight-semibold text-muted mb-1">Department</label>
                        <select name="department" class="form-control">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->dept_id }}"
                                    {{ ($selectedFilters['department_id'] ?? null) == $department->dept_id ? 'selected' : '' }}>
                                    {{ $department->dept_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-4 col-md-6">
                        <label class="font-weight-semibold text-muted mb-1">Budget Head Category</label>
                        <select name="category" class="form-control">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->category_id }}"
                                    {{ ($selectedFilters['category_id'] ?? null) == $category->category_id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="d-flex flex-column flex-md-row justify-content-md-end">
                    <button type="submit" class="btn btn-primary mr-md-2 mb-2 mb-md-0">
                         Apply Filters
                    </button>
                    <a href="{{ route('budget-item-details.index') }}" class="btn btn-outline-secondary">
                         Reset
                    </a>
                </div>
            </form>

            @if(empty($budgets))
                <div class="text-center text-muted py-5">
                    <i class="fa fa-info-circle fa-2x mb-3"></i>
                    <p class="mb-0">No budget items found for the selected filters.</p>
                </div>
            @else
                <div class="budget-sections" id="budgetAccordion">
                    @foreach($budgets as $budgetIndex => $budget)
                        @php $items = $budget['items']; @endphp
                        <div class="card shadow-sm mb-3 border-0">
                            <div class="card-header bg-white border-0">
                                <div class="text-muted mb-1">
                                    <div class="font-weight-semibold text-dark">{{ $budget['head_title'] }}</div>
                                    <div>
                                        <i class="fa fa-university mr-1"></i>{{ $budget['department_name'] ?? '-' }}
                                        @if(!empty($budget['parent_department_name']) && $budget['parent_department_name'] !== '-')
                                            <span class="ml-3"><i class="fa fa-sitemap mr-1"></i>{{ $budget['parent_department_name'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mb-0 budget-items-table">
                                        <thead>
                                            <tr>
                                                <th class="text-left">Item Title</th>
                                                <th class="text-left">Brand / Model</th>
                                                <th class="text-left">Quantity</th>
                                                <th class="text-left">Rate (PKR)</th>
                                                <th class="text-left">Total (PKR)</th>
                                                <th class="text-left">Budget Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($items as $itemIndex => $item)
                                                @php $collapseId = 'item-detail-'.$budgetIndex.'-'.$itemIndex; @endphp
                                                <tr class="item-row">
                                                    <td class="align-middle">
                                                        <button class="btn btn-link p-0 d-flex align-items-center item-toggle"
                                                            type="button"
                                                            data-toggle="collapse"
                                                            data-target="#{{ $collapseId }}"
                                                            data-item-id="{{ $item['item_id'] ?? '' }}"
                                                            data-head-id="{{ $budget['bh_id'] ?? '' }}"
                                                            data-dept-id="{{ $budget['dept_id'] ?? '' }}"
                                                            aria-expanded="false"
                                                            aria-controls="{{ $collapseId }}">
                                                            <i class="fa fa-angle-right mr-2 toggle-icon"></i>
                                                            <span class="font-weight-bold">{{ $item['item_title'] ?? '-' }}</span>
                                                        </button>
                                                        @if(!empty($item['remarks']))
                                                            <small class="d-block text-muted">{{ $item['remarks'] }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle">
                                                        <span>{{ $item['brand'] ?? '-' }}</span>
                                                        <small class="d-block text-muted">{{ $item['model'] ?? '-' }}</small>
                                                    </td>
                                                    <td class="text-center align-middle">{{ $item['quantity'] ?? 0 }}</td>
                                                    <td class="text-right align-middle">{{ number_format((float) ($item['rate'] ?? 0), 2) }}</td>
                                                    <td class="text-right align-middle">
                                                        {{ number_format((float) (($item['quantity'] ?? 0) * ($item['rate'] ?? 0)), 2) }}
                                                    </td>
                                                    <td class="align-middle">
                                                        <span class="badge badge-primary text-uppercase">
                                                            {{ !empty($item['budget_type']) ? str_replace('_', ' ', $item['budget_type']) : 'N/A' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr class="item-detail-row">
                                                    <td colspan="6" class="bg-light border-top-0">
                                                        <div id="{{ $collapseId }}" class="collapse item-detail">
                                                            <div class="item-detail-wrapper py-3 px-3 border rounded detail-placeholder">
                                                                <div class="text-center py-4">
                                                                    <i class="fa fa-spinner fa-spin fa-2x text-primary mb-2"></i>
                                                                    <p class="mb-0 text-muted">Loading item details...</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="itemDetailModal" tabindex="-1" role="dialog" aria-labelledby="itemDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-0">
                <h5 class="modal-title text-primary" id="itemDetailModalLabel">
                    <i class="fa fa-eye"></i> Item Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Budget Head</h6>
                        <p class="mb-0 font-weight-bold" data-detail="head">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Department</h6>
                        <p class="mb-0" data-detail="department">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Parent Department</h6>
                        <p class="mb-0" data-detail="parent">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Budget Type</h6>
                        <p class="mb-0 font-weight-bold" data-detail="budget-type">-</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Item Title</h6>
                        <p class="mb-0 font-weight-bold" data-detail="item">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Brand &amp; Model</h6>
                        <p class="mb-0" data-detail="brand-model">-</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6 class="text-muted mb-1">Quantity</h6>
                        <p class="mb-0" data-detail="quantity">-</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6 class="text-muted mb-1">Rate (PKR)</h6>
                        <p class="mb-0" data-detail="rate">-</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6 class="text-muted mb-1">Total (PKR)</h6>
                        <p class="mb-0 font-weight-bold" data-detail="total">-</p>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-1">Remarks</h6>
                    <p class="mb-0" data-detail="remarks">-</p>
                </div>

            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Table Styling */
    .budget-items-table {
        border-collapse: separate;
        border-spacing: 0;
        border: none !important;
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }

    .budget-items-table thead {
        background: #17675F !important;
    }

    .budget-items-table thead th {
        background: #17675F !important;
        color: white !important;
        font-weight: normal;
        text-align: center;
        padding: 15px;
        border: none !important;
        font-size: 14px;
        text-transform: none;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .budget-items-table tbody tr {
        background: white;
        border-bottom: 1px solid #f0f0f0;
    }

    .budget-items-table tbody td {
        padding: 12px 15px;
        border: none !important;
        color: #333;
        font-size: 14px;
        text-align: left !important;
    }

    .toggle-icon {
        transition: color 0.2s ease;
    }
    .item-toggle {
        color: #252525;
    }
    .item-toggle:hover,
    .item-toggle:focus {
        text-decoration: none;
        color: #17675F;
    }
    .item-detail-row td {
        padding: 0;
        border-top: none;
    }
    .item-detail {
        transition: height 0.45s ease;
    }
    .item-detail.collapsing {
        transition: height 0.45s ease;
    }
    .item-detail-wrapper {
        border-color: #dee2e6;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }
    .item-detail-wrapper.is-open {
        border-color: #17675F;
        box-shadow: 0 4px 16px rgba(69, 85, 195, 0.15);
    }

    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
</style>

<!-- jquery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(function () {
        var $accordion = $('#budgetAccordion');

        $accordion.on('show.bs.collapse', '.item-detail', function () {
            var $detail = $(this);
            var $row = $detail.closest('tr').prev('.item-row');
            var $icon = $row.find('.toggle-icon');
            var $wrapper = $detail.find('.item-detail-wrapper');
            var $button = $row.find('.item-toggle');

            $icon.removeClass('fa-angle-right').addClass('fa-angle-down');
            $wrapper.addClass('is-open');

            if ($wrapper.data('loaded')) {
                return;
            }

            var itemId = $button.attr('data-item-id');
            var headId = $button.attr('data-head-id');
            var deptId = $button.attr('data-dept-id');

            if (!itemId) {
                $wrapper.html('<div class="py-4 text-center text-danger">Unable to determine item identifier.</div>');
                return;
            }

            $.get('{{ route('budget-item-details.show') }}', {
                item_id: itemId,
                head_id: headId,
                department_id: deptId
            }, function (response) {
                var html = response && response.view ? response.view : response;
                $wrapper.html(html);
                $wrapper.removeClass('detail-placeholder');
                $wrapper.data('loaded', true);
            }).fail(function (xhr) {
                var message = 'Unable to load details. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                $wrapper.html('<div class="py-4 text-center text-danger">' + message + '</div>');
                $wrapper.removeClass('detail-placeholder');
            });
        });

        $accordion.on('hide.bs.collapse', '.item-detail', function () {
            var $detail = $(this);
            var $row = $detail.closest('tr').prev('.item-row');
            var $icon = $row.find('.toggle-icon');
            $icon.removeClass('fa-angle-down').addClass('fa-angle-right');
            $detail.find('.item-detail-wrapper').removeClass('is-open');
        });

        // Switch vendor quotation versions inside loaded item detail.
        $(document).on('change', '.vendor-version-select', function () {
            var selected = $(this).val();
            var $target = $($(this).data('target'));
            if (!$target.length) {
                return;
            }
            $target.find('.vendor-version-card').addClass('d-none');
            $target.find('.vendor-version-card[data-version="' + selected + '"]').removeClass('d-none');
        });
    });
</script>

@endsection

