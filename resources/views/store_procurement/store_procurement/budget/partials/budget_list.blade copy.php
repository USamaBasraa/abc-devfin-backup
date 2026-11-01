{{-- @forelse($budgets as $groupKey => $budget)

    @php
        $allItems = collect($budget->items ?? []);
    @endphp

    {{-- Budget Head Section --}}
    {{-- <div class="budget-section border-bottom" data-budget-id="{{ $budget->bh_id }}">
        <div class="p-3 bg-light border-bottom">
            <div class="row align-items-center">
                <div class="col-md-10">
                    <h4 class="mb-1 font-weight-bold">{{ $budget->head_title }}</h4>
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
            <table class="table table-hover table-bordered mb-0" style="font-size: 15px; table-layout: fixed;">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center" style="font-size: 14px; width: 5%;"></th>
                        <th style="font-size: 15px; width: 40%;">Item Title</th>
                        <th class="text-right" style="font-size: 15px; width: 15%;">Rate</th>
                        <th class="text-right" style="font-size: 15px; width: 15%;">Amount</th>
                        <th class="text-center" style="font-size: 15px; width: 12%;">Type</th>
                        <th class="text-center" style="font-size: 15px; width: 13%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allItems as $item)
                        @php
                            $alreadySent = $item->quotationRequests && $item->quotationRequests->count() > 0;
                        @endphp
                        <tr class="{{ $alreadySent ? 'table-success' : '' }}" style="{{ $alreadySent ? 'background-color: #d4edda !important;' : '' }}">
                            <td class="text-center" style="vertical-align: middle;">
                                <input type="checkbox" name="items[]"
                                       value="{{ $item->item_id }}"
                                       class="item-checkbox budget-{{ $budget->bh_id }}"
                                       data-budget-id="{{ $budget->bh_id }}"
                                       style="transform: scale(1.4);"
                                       {{ $alreadySent ? 'checked disabled' : '' }}>
                            </td>
                            <td style="vertical-align: middle; font-weight: 500; word-wrap: break-word;">{{ $item->item_title }}</td>
                            <td class="text-right" style="vertical-align: middle;">{{ number_format($item->rate, 2) }}</td>
                            <td class="text-right" style="vertical-align: middle; font-weight: 600;">{{ number_format($item->quantity * $item->rate, 2) }}</td>
                            <td class="text-center" style="vertical-align: middle;"><span class="badge badge-secondary" style="font-size: 13px; padding: 5px 10px;">{{ ucfirst($item->budget_type) }}</span></td>
                            <td class="text-center" style="vertical-align: middle;">
                                <button class="btn btn-sm btn-info viewDetailsBtn"
                                        data-title="{{ $item->item_title }}"
                                        data-brand="{{ $item->brand }}"
                                        data-model="{{ $item->model }}"
                                        data-qty="{{ $item->quantity }}"
                                        data-rate="{{ number_format($item->rate, 2) }}"
                                        data-amount="{{ number_format($item->quantity * $item->rate, 2) }}"
                                        data-remarks="{{ $item->remarks ?? 'N/A' }}"
                                        data-type="{{ ucfirst($item->budget_type) }}">
                                    <i class="fa fa-eye"></i> Details
                                </button>

                                <button class="btn btn-sm btn-warning text-white ml-1" onclick="editBudget({{ $item->item_id }})">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4" style="font-size: 16px;">No items</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-light">
                    <tr style="font-size: 16px;">
                        <th colspan="3" class="text-right font-weight-bold">Total:</th>
                        <th class="text-right font-weight-bold" style="font-size: 17px;">{{ number_format($allItems->sum(fn($i) => $i->quantity * $i->rate), 2) }}</th>
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
@endforelse --}}
 --}}
