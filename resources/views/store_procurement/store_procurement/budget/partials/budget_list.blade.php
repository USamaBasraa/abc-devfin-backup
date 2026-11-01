@forelse($budgets as $groupKey => $budget)

    @php
        $allItems = collect($budget->items ?? []);
    @endphp

    {{-- Budget Head Section --}}
    <div class="budget-section" data-budget-id="{{ $budget->bh_id }}">
        <div class="budget-section-header">
            <div class="row align-items-center">
                <div class="col-md-10">
                    <h4 class="budget-head-title mb-1 font-weight-bold">{{ $budget->head_title }}</h4>
                    <div class="text-muted" style="font-size: 15px;">
                        <i class="fa fa-building"></i> {{ $budget->department->dept_name ?? '-' }}
                        @if($budget->remarks)
                            | <i class="fa fa-comment"></i> {{ $budget->remarks }}
                        @endif
                    </div>
                </div>
                <div class="col-md-2 text-right">
                    <button class="btn btn-sm btn-outline-secondary select-all-btn" type="button" data-budget="{{ $budget->bh_id }}">
                        <input type="checkbox" class="select-all" data-budget="{{ $budget->bh_id }}" style="transform: scale(1.3); margin-right: 5px; pointer-events: none;">
                        <span style="font-size: 13px;">Select All</span>
                    </button>
                </div>
            </div>
        </div>

<div class="table-responsive">
    <table class="modern-table table" style="table-layout: fixed;">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">#</th>
                <th style="width: 24%;">Item Title</th>
                <th style="width: 12%;">Brand</th>
                <th style="width: 12%;">Model</th>
                <th class="text-center" style="width: 7%;">Qty</th>
                <th class="text-right" style="width: 11%;">Rate</th>
                <th class="text-right" style="width: 13%;">Amount</th>
                <th class="text-center" style="width: 8%;">Type</th>
                <th class="text-center" style="width: 8%;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allItems as $item)
                @php
                    $alreadySent = $item->quotationRequests && $item->quotationRequests->count() > 0;
                @endphp
                <tr class="{{ $alreadySent ? 'table-success' : '' }}" style="{{ $alreadySent ? 'background-color: #d4edda !important;' : '' }}">
                    {{-- Checkbox --}}
                    <td class="text-center align-middle">
                        <input type="checkbox"
                            name="items[]"
                            value="{{ $item->item_id }}"
                            class="item-checkbox budget-{{ $budget->bh_id }}"
                            data-budget-id="{{ $budget->bh_id }}"
                            style="transform: scale(1.3);"
                            {{ $alreadySent ? 'checked disabled' : '' }}>
                    </td>

                    {{-- Item Details --}}
                    <td class="align-middle font-weight-bold text-left item-title-cell">{{ $item->item_title }}</td>
                    <td class="align-middle text-center">{{ $item->brand }}</td>
                    <td class="align-middle text-center">{{ $item->model }}</td>
                    <td class="align-middle text-center quantity-cell">{{ $item->quantity }}</td>

                    {{-- Rate & Amount --}}
                    <td class="align-middle text-right rate-cell">{{ number_format($item->rate, 2) }}</td>
                    <td class="align-middle text-right font-weight-bold amount-cell">{{ number_format($item->quantity * $item->rate, 2) }}</td>

                    {{-- Type --}}
                    <td class="align-middle text-center">
                        <span class="badge badge-secondary px-3 py-2" style="font-size: 13px;">
                            {{ ucfirst($item->budget_type) }}
                        </span>
                    </td>

                    {{-- Action Buttons --}}
                    <td class="text-center align-middle action-cell">
                        {{-- <button class="btn btn-sm btn-info viewDetailsBtn"
                            data-title="{{ $item->item_title }}"
                            data-brand="{{ $item->brand }}"
                            data-model="{{ $item->model }}"
                            data-qty="{{ $item->quantity }}"
                            data-rate="{{ number_format($item->rate, 2) }}"
                            data-amount="{{ number_format($item->quantity * $item->rate, 2) }}"
                            data-remarks="{{ $item->remarks ?? 'N/A' }}"
                            data-type="{{ ucfirst($item->budget_type) }}">
                            <i class="fa fa-eye"></i> Details
                        </button> --}}

                        <button class="btn btn-sm btn-warning text-white ml-1" onclick="editBudget({{ $item->item_id }})">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                    <!-- Delete Button -->
                        <button class="btn btn-sm btn-danger text-white ml-1" onclick="deleteBudget({{ $item->item_id }})">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4" style="font-size: 16px;">
                        <i class="fa fa-info-circle"></i> No items available.
                    </td>
                </tr>
            @endforelse
        </tbody>

        {{-- Footer Total --}}
        <tfoot>
            <tr style="font-size: 16px;">
                <th colspan="6" class="text-right font-weight-bold">Total:</th>
                <th class="text-right font-weight-bold text-success" style="font-size: 17px;">
                    {{ number_format($allItems->sum(fn($i) => $i->quantity * $i->rate), 2) }}
                </th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>
</div>

    </div>
@empty
    <div class="text-center text-muted py-5">
        <i class="fa fa-inbox fa-3x mb-3"></i>
        <p>No budget heads available.</p>
    </div>
@endforelse

