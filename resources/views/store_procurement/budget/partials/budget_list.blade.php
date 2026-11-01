@php
    $budgetsToShow = collect($budgets)->filter(function ($budget) {
        $total = collect($budget->items ?? [])->sum(function ($i) {
            return ((float) ($i->quantity ?? 0)) * ((float) ($i->rate ?? 0));
        });
        return $total > 0;
    });
@endphp
'App\Jobs\Email\SendVendorRfqEmailJob' not found
@forelse($budgetsToShow as $groupKey => $budget)

    @php
        $allItems = collect($budget->items ?? []);
    @endphp

    {{-- Budget Head Section --}}
    <div class="budget-section" data-budget-id="{{ $budget->bh_id }}" style="background: none !important;">
        <div class="budget-section-header" style="
        background: none !important;
        border: none;
    ">
            <div class="row align-items-center">
                <div class="col-md-10">
                    <div class="d-flex align-items-center">
                        <h4 class="budget-head-title mb-0" data-toggle="tooltip" data-parent="{{ $budget->parent_department->dept_name ?? $budget->department->dept_name ?? '-' }}" style="cursor: help;">
                            {{ $budget->head_title }}
                        </h4>
                        @if(!empty($budget->budget_year))
                            <span class="badge badge-primary ml-3" style="font-size: 13px; font-weight: 600; padding: 4px 8px;">
                                {{ $budget->budget_year }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-2 text-right" style="display: none;">
                    <button class="select-all-modern select-all-btn" type="button" data-budget="{{ $budget->bh_id }}">
                        <input type="checkbox" class="select-all" data-budget="{{ $budget->bh_id }}" style="pointer-events: none;">
                        <span>Select All</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="modern-table table budget-items-table" style="table-layout: fixed;">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 4%;"></th>
                        <th style="width: 24%;">Item Title</th>
                        <th style="width: 14%;">Remarks</th>
                        <th class="text-center" style="width: 8%;">Quantity</th>
                        <th class="text-right" style="width: 10%;">Rate</th>
                        <th class="text-right" style="width: 11%;">Amount</th>
                        <th class="text-center" style="width: 14%;">Type</th>
                        <th class="text-center" style="width: 15%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allItems as $item)
                        @php
                            $hasOwnQuotation = $item->quotationRequests && $item->quotationRequests->count() > 0;
                            $alreadySent = !empty($item->already_sent) || $hasOwnQuotation;
                            $quotationRequestId = $hasOwnQuotation ? $item->quotationRequests->first()->qr_id : null;
                            $currentRequestedQty = $hasOwnQuotation ? $item->quotationRequests->first()->requested_quantity : $item->quantity;
                            $vcBudgetFlag = strtolower(trim($item->vc_budget_update_flag ?? ''));

                            $requestedBudgetType = strtolower(trim($item->requested_budget_type ?? ''));
                            $canShowUpdateBudget = $hasOwnQuotation
                                && !($item->has_po ?? false)
                                && $requestedBudgetType === 'partial re-appropriate'
                                && in_array($vcBudgetFlag, ['', '0', 'no', 'pending', 'pending approval', 'false'], true);
                        @endphp
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="items[]"
                                    value="{{ $item->item_id }}"
                                    class="item-checkbox budget-{{ $budget->bh_id }}"
                                    data-budget-id="{{ $budget->bh_id }}"
                                    style="transform: scale(1.3);"
                                    @if(strtolower($item->budget_type) === 'budgeted')
                                    @elseif(
                                        strtolower($item->budget_type) === 'reappropriated' ||
                                        strtolower($item->budget_type) === 'unbudgeted'
                                    )
                                        @if(strtolower($item->reappropriation_approval) === 'approved')
                                        @else
                                            disabled
                                        @endif
                                    @else
                                        disabled
                                    @endif

                                    @if($alreadySent)
                                        disabled
                                    @endif
                                >
                            </td>

                            <td class="item-title-cell" style="font-weight: 500; word-wrap: break-word;">
                                <span class="clickable-item-title-table"
                                    style="cursor: pointer; color: #17675F; text-decoration: underline;"
                                    data-title="{{ $item->item_title }}"
                                    data-brand="{{ $item->brand }}"
                                    data-model="{{ $item->model }}"
                                    data-qty="{{ $item->quantity }}"
                                    data-rate="{{ number_format($item->rate, 2) }}"
                                    data-amount="{{ number_format($item->quantity * $item->rate, 2) }}"
                                    data-remarks="{{ $item->remarks ?? 'N/A' }}"
                                    data-type="{{ ucfirst($item->budget_type) }}"
                                    title="Click to view details">
                                    <i class="fa fa-info-circle"></i> {{ $item->item_title }}
                                </span>
                            </td>
                            <td class="remarks-cell" style="font-weight: 600;">{{ $item->remarks }}</td>
                            <td class="quantity-cell text-center" style="font-weight: 600;">{{ $item->quantity }}</td>
                            <td class="rate-cell text-right">{{ number_format($item->rate, 2) }}</td>
                            <td class="amount-cell text-right" style="font-weight: 600;">{{ number_format($item->quantity * $item->rate, 2) }}</td>
                            <td class="type-cell text-center">
                                <div class="type-cell-inner">
                                @if (strtolower($item->budget_type) === 'reappropriated' || strtolower($item->budget_type) === 'unbudgeted')

                                    @if (strtolower($item->budget_type) === 'reappropriated' )
                                        <button
                                            class="btn btn-sm btn-info view-history-btn"
                                            data-item-id="{{ $item->item_id }}"
                                        >
                                            Reappropriated
                                        </button>
                                    @else
                                        <span class="badge badge-secondary">
                                            {{ ucwords(strtolower($item->budget_type)) }}
                                        </span>
                                    @endif

                                    @if (strtolower($item->budget_type) === 'reappropriated' || strtolower($item->budget_type) === 'unbudgeted')
                                        @if (strtolower($item->send_reappropriation_approval_reapprop) == 'no')
                                            <button
                                                class="btn btn-sm btn-warning send-approval-btn"
                                                data-item-id="{{ $item->item_id }}"
                                            >
                                                Send for Approval
                                            </button>
                                        @else
                                            @php
                                                $approval = strtolower($item->reappropriation_approval ?? 'n/a');
                                            @endphp

                                            @if ($approval === 'approved' || $approval === 'declined')
                                                <button
                                                    class="btn btn-sm {{ $approval === 'approved' ? 'btn-success' : 'btn-danger' }} view-remarks-btn"
                                                    data-remarks="{{ $item->reappropriation_approval_remarks }}"
                                                >
                                                    {{ ucfirst($approval) }}
                                                </button>
                                            @else
                                                <span class="badge badge-secondary">
                                                    {{ $item->reappropriation_approval ?? 'N/A' }}
                                                </span>
                                            @endif
                                        @endif
                                    @else
                                        <span class="badge badge-secondary">
                                            {{ ucwords(strtolower($item->budget_type)) }}
                                        </span>
                                    @endif

                                @else
                                    <span class="badge badge-secondary">
                                        {{ ucwords(strtolower($item->budget_type)) }}
                                    </span>
                                @endif
                                </div>
                            </td>

                            <td class="action-cell text-center">
                                <div class="action-cell-inner">
                                <button class="btn btn-sm btn-info viewDetailsBtn @if($item->has_po) btn-with-po-badge @endif"
                                        data-title="{{ $item->item_title }}"
                                        data-brand="{{ $item->brand }}"
                                        data-model="{{ $item->model }}"
                                        data-qty="{{ $item->quantity }}"
                                        data-rate="{{ number_format($item->rate, 2) }}"
                                        data-amount="{{ number_format($item->quantity * $item->rate, 2) }}"
                                        data-remarks="{{ $item->remarks ?? 'N/A' }}"
                                        data-type="{{ ucfirst($item->budget_type) }}"
                                        data-item-id="{{ $item->item_id }}"
                                        data-qr-id="{{ $item->primary_qr_id }}">
                                    @if($item->has_po)
                                        <span class="po-badge">PO</span>
                                    @endif
                                    <i class="fa fa-info-circle"></i> View
                                    @if(!empty($item->primary_qr_id) && $item->unread_chat_count > 0)
                                        <span class="badge badge-danger chat-unread chat-unread-qr{{ $item->primary_qr_id }}">
                                            <i class="fa fa-bell"></i> {{ $item->unread_chat_count }}
                                        </span>
                                    @endif
                                </button>

                                @if($hasOwnQuotation && !$item->has_po)
                                    <button class="btn btn-sm btn-warning editQuotationBtn"
                                            data-item-id="{{ $item->item_id }}"
                                            data-budget-id="{{ $budget->bh_id }}"
                                            data-qr-id="{{ $quotationRequestId }}"
                                            data-item-title="{{ $item->item_title }}"
                                            data-current-qty="{{ $currentRequestedQty }}"
                                            data-budget-qty="{{ $item->quantity }}"
                                            title="Revise Quotation Request">
                                        <i class="fa fa-refresh"></i> Revise
                                    </button>
                                    @if($canShowUpdateBudget)
                                        <button class="btn btn-sm btn-primary updateBudgetBtn"
                                                data-item-id="{{ $item->item_id }}"
                                                data-head="{{ e($budget->head_title) }}"
                                                data-title="{{ e($item->item_title) }}"
                                                data-amount="{{ number_format($item->quantity * $item->rate, 2, '.', '') }}">
                                            <i class="fa fa-exchange"></i> Update Budget
                                        </button>
                                    @endif
                                @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- leave tbody empty; DataTables shows emptyTable (colspan row breaks column count) --}}
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-right">TOTAL:</th>
                        <th class="text-right" style="font-size: 16px; color: #5a67d8;">{{ number_format($allItems->sum(fn($i) => $i->quantity * $i->rate), 2) }}</th>
                        <th></th>
                        <th></th>
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
