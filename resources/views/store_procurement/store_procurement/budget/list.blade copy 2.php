    @extends('layouts.app')

    @section('content')
    @include('components.toast-notifications')
    <link href="/css/bnu-layout.css" rel="stylesheet" />

    <style>
        /* Modern Department Budgets Filter Section Styling */
        .dept-budgets-header {
            background-color: #f5f5f5;
            padding: 20px 0;
        }

        .dept-budgets-title {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-left: 15px;
        }

        .filter-card-modern {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .filter-label-modern {
            font-size: 13px;
            font-weight: 400;
            color: #5a6c7d;
            margin-bottom: 8px;
            display: block;
        }

        .filter-select-modern {
            background-color: #f0f1f3;
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 14px;
            color: #2c3e50;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%235a6c7d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
            padding-right: 45px;
            width: 100%;
            transition: all 0.2s ease;
        }

        .filter-select-modern:focus {
            outline: none;
            background-color: #e8eaed;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
        }

        .reset-btn-modern {
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 25px;
            padding: 10px 24px;
            font-size: 14px;
            color: #5a6c7d;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 28px;
        }

        .reset-btn-modern:hover {
            background-color: #f9fafb;
            border-color: #9ca3af;
        }

        .reset-btn-modern i {
            font-size: 14px;
        }

        /* Modern Table Styling - Matching Second Screenshot */
        .modern-table-wrapper {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .modern-table {
            font-size: 14px;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .modern-table thead {
            background: #4562C3;
        }

        .modern-table thead th {
            color: white !important;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none !important;
            padding: 18px 14px;
            vertical-align: middle;
        }

        .modern-table thead th:first-child {
            border-top-left-radius: 12px;
        }

        .modern-table thead th:last-child {
            border-top-right-radius: 12px;
        }

        .modern-table tbody tr {
            background-color: white;
            border-bottom: 1px solid #e2e8f0;
        }

        .modern-table tbody tr:nth-child(even) {
            background-color: #f7fafc;
        }

        .modern-table tbody tr:hover {
            background-color: #edf2f7 !important;
        }

        .modern-table tbody td {
            padding: 14px 12px;
            vertical-align: middle;
            border: none !important;
            color: #2d3748;
        }

        .modern-table tfoot {
            background-color: #f7fafc;
            border-top: 2px solid #cbd5e0;
        }

        .modern-table tfoot th {
            padding: 14px 12px;
            border: none !important;
            color: #2d3748;
            font-weight: 600;
        }

        .budget-section-header {
            background: white;
            padding: 20px 24px;
            border-bottom: 2px solid #e2e8f0;
        }

        .budget-section-header h4 {
            color: #2d3748;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .budget-section-header .text-muted {
            color: #718096 !important;
            font-size: 14px;
        }

        .select-all-modern {
            background: white;
            border: 1px solid #cbd5e0;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 13px;
            color: #4a5568;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .select-all-modern:hover {
            background-color: #f7fafc;
            border-color: #a0aec0;
        }

        .select-all-modern input[type="checkbox"] {
            transform: scale(1.2);
            margin: 0;
        }

        .budget-section {
            margin-bottom: 24px;
            border-radius: 12px;
            overflow: hidden;
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        /* Invalid field styling */
        .is-invalid {
            border-color: #C34545 !important;
        }

        .is-invalid:focus {
            border-color: #C34545 !important;
            box-shadow: 0 0 0 0.2rem rgba(195, 69, 69, 0.25) !important;
        }

        /* Green PO badge on button corner */
        .btn-with-po-badge {
            position: relative;
        }
        .po-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #28a745;
            color: white;
            font-size: 9px;
            font-weight: bold;
            padding: 2px 5px;
            border-radius: 3px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
    </style>

    <div class="container-fluid mt-4">
        {{-- Header with Actions --}}
        <div class="dept-budgets-header">
                        <div class="card-header" style="background: #fff !important; color: #252525 !important;">
                    <h4 class="mb-0">
                        Department Budgets
                    </h4>
                </div>
            {{-- <h3 class="dept-budgets-title">Department Budgets</h3> --}}
            <div class="filter-card-modern">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="filter-label-modern">Academic Year</label>
                        <select id="academicYearFilter" class="filter-select-modern">
                            <option value="">-- All Academic Years --</option>
                            @foreach($academic_years as $ay)
                                <option value="{{ $ay->ay_id }}" {{ $loop->first ? 'selected' : '' }}>{{ $ay->ay_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="filter-label-modern">Budget Head Title</label>
                        <select id="departmentFilter" class="filter-select-modern">
                            <option value="">-- All Departments --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="filter-label-modern">Budget Head Title</label>
                        <select id="categoryFilter" class="filter-select-modern">
                            <option value="">-- All Budget Heads --</option>
                            @foreach($vendor_categories as $cat)
                                <option value="{{ $cat->category_id }}">{{ $cat->category_name }} ({{ $cat->budget_type }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="reset-btn-modern" id="clearFiltersBtn">
                            <i class="fa fa-refresh"></i> Reset Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

    {{-- Fixed Action Bar - Bottom center, always visible when items selected --}}
    <div id="floatingActionBar" class="floating-action-bar-bottom bg-white shadow-lg d-none">
        <div class="py-3 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fa fa-check-square text-primary"></i>
                        <span id="selectedItemsCountDisplay" class="font-weight-bold text-primary">0</span> Selected
                    </h5>
                </div>
                <div>
                    <button class="btn btn-outline-secondary mr-2 multiReappropriateBtnGlobal">
                        <i class="fa fa-exchange"></i> Re-appropriate
                    </button>
                    <button class="btn btn-primary mr-2" id="requestQuotationBtnGlobal">
                        <i class="fa fa-paper-plane"></i> Request Quotation
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="closeActionBar">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

        {{-- Main Budget Card --}}
        <div class="card shadow-sm">
            <div class="card-body p-0">
                @if(!empty($unreadChatItems))
                <div class="mb-3" style="padding-left: 20px; padding-right: 20px;">
                    <button class="btn btn-sm btn-primary" id="unreadChatsToggle" type="button" data-toggle="collapse" data-target="#unreadChatsPanel">
                        <i class="fa fa-comments"></i> Quotation Chats ({{ $totalUnreadChats }})
                    </button>
                    <div class="collapse show mt-2" id="unreadChatsPanel">
                        <ul class="list-group" id="topUnreadChatList">
                            @foreach($unreadChatItems as $chatItem)
                                <li class="list-group-item d-flex justify-content-between align-items-center top-chat-entry" data-qr-id="{{ $chatItem->qr_id }}">
                                    <div>
                                        <strong>{{ $chatItem->budget_head }}</strong>
                                        <span class="text-muted"> - {{ $chatItem->item_title }}</span>
                                    </div>
                                    <div>
                                        <span class="badge badge-danger badge-pill">{{ $chatItem->count }}</span>
                                        <button type="button"
                                                class="btn btn-sm btn-info ml-2 viewDetailsBtn"
                                                data-item-id="{{ $chatItem->item_id }}"
                                                data-qr-id="{{ $chatItem->qr_id }}">
                                            <i class="fa fa-comment"></i> Open
                                        </button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                <div id="budgetsContainer">


                    @forelse($budgets as $groupKey => $budget)

                        @php
                            $allItems = collect($budget->items ?? []);
                        @endphp

                        {{-- Budget Head Section --}}
                        <div class="budget-section" data-budget-id="{{ $budget->bh_id }}">
                            <div class="budget-section-header">
                                <div class="row align-items-center">
                                    <div class="col-md-10">
                                        <h4 class="budget-head-title" data-toggle="tooltip" data-parent="{{ $budget->parent_department->dept_name ?? $budget->department->dept_name ?? '-' }}" style="cursor: help;">
                                            {{ $budget->head_title }}
                                        </h4>
                                        <div class="text-muted">
                                            <i class="fa fa-building"></i> {{ $budget->department->dept_name ?? '-' }}
                                            @if($budget->remarks)
                                                | <i class="fa fa-comment"></i> {{ $budget->remarks }}
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
                                <table class="modern-table table" style="table-layout: fixed;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 5%;"></th>
                                            <th style="width: 35%;">Item Title</th>
                                            <th class="text-center" style="width: 10%;">Quantity</th>
                                            <th class="text-right" style="width: 12%;">Rate</th>
                                            <th class="text-right" style="width: 13%;">Amount</th>
                                            <th class="text-center" style="width: 12%;">Type</th>
                                            <th class="text-center" style="width: 13%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allItems as $item)
                                {{-- @dd($allItems) --}}
                                    @php
                                        $alreadySent = $item->quotationRequests && $item->quotationRequests->count() > 0;
                                        $quotationRequestId = $alreadySent ? $item->quotationRequests->first()->qr_id : null;
                                        $currentRequestedQty = $alreadySent ? $item->quotationRequests->first()->requested_quantity : $item->quantity;
                                    @endphp
                                            {{-- <tr class="{{ $alreadySent ? 'table-success' : '' }}" style="{{ $alreadySent ? 'background-color: #d4edda !important;' : '' }}">
                                                <td class="text-center">
                                            <input type="checkbox" name="items[]"
                                                value="{{ $item->item_id }}"
                                                        class="item-checkbox budget-{{ $budget->bh_id }}"
                                                        data-budget-id="{{ $budget->bh_id }}"
                                                        style="transform: scale(1.3);"
                                                        {{ $alreadySent ? 'checked disabled' : '' }}>
                                                </td> --}}
                                                <tr class="{{ $alreadySent ? 'table-success' : '' }}" style="{{ $alreadySent ? 'background-color: #d4edda !important;' : '' }}">
                                                        {{-- <td class="text-center">
                                                            <input type="checkbox" name="items[]"
                                                                value="{{ $item->item_id }}"
                                                                class="item-checkbox budget-{{ $budget->bh_id }}"
                                                                data-budget-id="{{ $budget->bh_id }}"
                                                                style="transform: scale(1.3);"
                                                                @if($alreadySent)
                                                                    checked
                                                                    @if(strtolower($item->budget_type) !== 'reappropriated')
                                                                        disabled
                                                                    @endif
                                                                @endif
                                                            >
                                                        </td> --}}
                                                     {{-- <td class="text-center">
                                                        <input type="checkbox" name="items[]"
                                                            value="{{ $item->item_id }}"
                                                            class="item-checkbox budget-{{ $budget->bh_id }}"
                                                            data-budget-id="{{ $budget->bh_id }}"
                                                            style="transform: scale(1.3);"
                                                            @if($alreadySent) --}}
                                                                {{-- checked --}}
                                                                {{-- @if(strtolower($item->budget_type) !== 'reappropriated')
                                                                    disabled
                                                                @endif
                                                            @endif
                                                        >
                                                    </td> --}}

                                                    {{-- <td class="text-center">
                                                        <input type="checkbox" name="items[]"
                                                            value="{{ $item->item_id }}"
                                                            class="item-checkbox budget-{{ $budget->bh_id }}"
                                                            data-budget-id="{{ $budget->bh_id }}"
                                                            style="transform: scale(1.3);"
                                                             @if($strtolower($item->budget_type) === 'budgeted')
                                                               {{-- Enabled --}}
                                                            {{-- @endif
                                                            @if($alreadySent)
                                                               disabled
                                                            @endif

                                                             @if(
                                                                    strtolower($item->budget_type) === 'reappropriated' ||
                                                                    strtolower($item->budget_type) === 'unbudgeted'
                                                                )
                                                                    @if(strtolower($item->reappropriation_approval) === 'approved') --}}
                                                                        {{-- Enabled --}}
                                                                    {{-- @else
                                                                        disabled
                                                                    @endif
                                                                @else
                                                                    disabled
                                                                @endif --}}
                                                        {{-- >
                                                    </td> --}}


                                                    <td class="text-center">
                                                        <input type="checkbox" name="items[]"
                                                            value="{{ $item->item_id }}"
                                                            class="item-checkbox budget-{{ $budget->bh_id }}"
                                                            data-budget-id="{{ $budget->bh_id }}"
                                                            style="transform: scale(1.3);"
                                                            @if(strtolower($item->budget_type) === 'budgeted')
                                                                {{-- Enabled --}}
                                                            @elseif(
                                                                strtolower($item->budget_type) === 'reappropriated' ||
                                                                strtolower($item->budget_type) === 'unbudgeted'
                                                            )
                                                                @if(strtolower($item->reappropriation_approval) === 'approved')
                                                                    {{-- Enabled --}}
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


                                                <td style="font-weight: 500; word-wrap: break-word;">
                                                    <span class="clickable-item-title-table"
                                                        style="cursor: pointer; color: #4562C3; text-decoration: underline;"
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
                                                <td class="text-center" style="font-weight: 600;">{{ $item->quantity }}</td>
                                                <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                                                <td class="text-right" style="font-weight: 600;">{{ number_format($item->quantity * $item->rate, 2) }}</td>
                                                {{-- <td class="text-center"><span class="badge badge-secondary">{{ ucfirst($item->budget_type) }}</span></td> --}}



    {{-- new --}}

                                                {{-- <td class="text-center">
                                                    @if (strtolower($item->budget_type) === 'reappropriated')
                                                        <button
                                                            class="btn btn-sm btn-info view-history-btn"
                                                            data-item-id="{{ $item->item_id }}"
                                                        >
                                                            Reappropriated
                                                        </button>
                                                    @else
                                                        <span class="badge badge-secondary">
                                                            {{ ucfirst($item->budget_type) }}
                                                        </span>
                                                    @endif
                                                </td> --}}
                                         <td class="text-center">
                                                @if (strtolower($item->budget_type) === 'reappropriated' || strtolower($item->budget_type) === 'unbudgeted')

                                                @if (strtolower($item->budget_type) === 'reappropriated' )
                                                 <button
                                                        class="btn btn-sm btn-info view-history-btn"
                                                        data-item-id="{{ $item->item_id }}"
                                                    >
                                                        Reappropriated
                                                    </button>
                                                       {{-- <br><br> --}}
                                                        @else
                                                    <span class="badge badge-secondary">
                                                        {{ $item->budget_type }}
                                                    </span>

                                                    @endif

                                                    @if (strtolower($item->budget_type) === 'reappropriated' || strtolower($item->budget_type) === 'unbudgeted')
                                                                        @if (strtolower($item->send_reappropriation_approval_reapprop) == 'no')
                                                                         <br><br>
                                                                        <button
                                                                                class="btn btn-sm btn-warning send-approval-btn"
                                                                                data-item-id="{{ $item->item_id }}"
                                                                            >
                                                                                Send for Approval
                                                                            </button>
                                                                        @else
                                                                            <br><br>



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
                                                                            {{-- <span class="badge badge-secondary">
                                                                                {{ $item->reappropriation_approval ?? 'N/A' }}
                                                                            </span> --}}
                                                                        @endif
                                                                    @else
                                                                     <br><br>
                                                                        <span class="badge badge-secondary">
                                                                            {{ $item->budget_type }}
                                                                        </span>
                                                                    @endif
                                                    <!-- <span class="badge badge-secondary">{{ $item->reappropriation_approval ?? 'N/A'  }}</span> -->

                                                @else

                                                 <br><br>
                                                    <span class="badge badge-secondary">
                                                        {{ $item->budget_type }}
                                                    </span>
                                                @endif
                                            </td>






                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-info mb-2 viewDetailsBtn @if($item->has_po) btn-with-po-badge @endif"
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

                                                    {{-- New PDF Button --}}

                                                    {{-- remvoe it after creating new PDF --}}


                                                    @if($alreadySent && !$item->has_po)
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
                                                    @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                                <td colspan="6" class="text-center text-muted py-4" style="font-size: 14px;">No items</td>
                                    </tr>
                                @endforelse
                            </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-right">TOTAL:</th>
                                            <th class="text-right" style="font-size: 16px; color: #5a67d8;">{{ number_format($allItems->sum(fn($i) => $i->quantity * $i->rate), 2) }}</th>
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
                </div>
            </div>
        </div>
    </div>

    {{-- Floating Action Bar - Top sticky, hidden by default --}}
    <div class="floating-action-bar-top bg-white shadow-lg py-3 d-none border-bottom border-primary">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        <i class="fa fa-check-square text-primary"></i>
                        <span class="font-weight-bold text-primary">0</span> Item(s) Selected
                    </h5>
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-outline-secondary mr-2 multiReappropriateBtnGlobal">
                        <i class="fa fa-exchange"></i> Re-appropriate Selected
                    </button>
                    <button class="btn btn-primary" id="requestQuotationBtnGlobal">
                        <i class="fa fa-paper-plane"></i> Request Quotation
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- new --}}
    <!-- Remarks Modal -->
<div class="modal fade" id="remarksModal" tabindex="-1" role="dialog" aria-labelledby="remarksModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="remarksModalLabel">Approval Remarks</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="remarksContent">
        <!-- remarks will load here dynamically -->
      </div>
    </div>
  </div>
</div>

    {{-- new --}}
<!-- Reappropriation History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="historyModalLabel">Reappropriation History</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-sm" id="historyTable">
          <thead>
            <tr>
              <th>From Item ID</th>
              <th>From Item Title</th>
              <th>Amount</th>
              {{-- <th>Budgetary Head</th> --}}
              <th>Entry User</th>
              <th>Created At</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="5" class="text-center text-muted">No data found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- Approval Confirmation Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="approvalModalLabel">Send for Approval</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to send this item for approval?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmApprovalBtn" class="btn btn-primary">Yes, Send</button>
      </div>
    </div>
  </div>
</div>


    {{-- new --}}
    <!-- Reappropriation History Modal -->
    <div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="historyModalLabel">Reappropriation History</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered table-sm" id="historyTable">
            <thead>
                <tr>
                <th>From Item ID</th>
                <th>From Item Title</th>
                <th>Amount</th>
                {{-- <th>Budgetary Head</th> --}}
                <th>Entry User</th>
                <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <td colspan="5" class="text-center text-muted">No data found</td>
                </tr>
            </tbody>
            </table>
        </div>
        </div>
    </div>
    </div>









    {{-- Re-appropriate Modal --}}
    <div class="modal fade" id="multiReappropriateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <form id="multiReappropriateForm">
            @csrf

            <div class="modal-header reappropriate-modal-header">
            <h5 class="modal-title">
                <i class="fa fa-exchange"></i> Re-appropriate Budget Across Heads
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

            <p class="text-muted mb-3">
                <i class="fa fa-info-circle"></i> Transfer budget amounts from selected items to create a new item in a target budget head.
            </p>

            <h5 class="font-weight-bold mb-3"><i class="fa fa-arrow-up text-primary"></i> Source Items (From)</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover reappropriate-table" id="selectedItemsTable">
                <thead class="thead-light">
                    <tr>
                    <th>Budget Head</th>
                    <th>Item</th>
                    <th class="text-right">Available Amount</th>
                    <th>Amount to Transfer</th>
                    <th class="text-right">Remaining Amount</th>
                    </tr>
                </thead>
                <tbody></tbody>
                </table>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                <label class="font-weight-bold">Re-appropriation Type <span class="text-danger">*</span></label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="reappropriation_type" id="existingItem" value="existing" checked>
                    <label class="form-check-label" for="existingItem">
                    <i class="fa fa-plus-circle text-info"></i> Re-appropriate to Existing Item
                    </label>
                </div>
                {{-- <div class="form-check mt-2">
                    <input class="form-check-input" type="radio" name="reappropriation_type" id="newItem" value="new">
                    <label class="form-check-label" for="newItem">
                    <i class="fa fa-file-text-o text-success"></i> Create New Item
                    </label>
                </div> --}}
                </div>
            </div>

            {{-- Existing Item Section --}}
    <div class="row mt-4" id="existingItemSection">
        <div class="col-md-12">

            {{-- Target Budget Head Dropdown --}}
            <div class="form-group mb-3">
                <label class="font-weight-bold">
                    Target Budget Head <span class="text-danger">*</span>
                </label>
                <select name="target_bh_id" id="targetBudgetHeadExisting" class="form-control" required>
                    <option value="">-- Select Target Budget Head --</option>
                    @foreach($budgets as $budget)
                        <option value="{{ $budget->bh_id }}">
                            {{ $budget->head_title }} ({{ $budget->department->dept_name ?? '-' }})
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Choose the budget head where you want to add the amount</small>
            </div>

            {{-- Target Item Dropdown --}}
            <div class="form-group mb-3">
                <label class="font-weight-bold">
                    Select Target Item <span class="text-danger">*</span>
                </label>
                <select name="existing_item_id" id="existingItemDropdown" class="form-control" required>
                    <option value="">-- Select Item --</option>
                </select>
                <small class="form-text text-muted">Select the item to which amount will be added</small>
            </div>

            {{-- Target Item Details Table --}}
            <div id="targetItemDetailsSection" class="d-none">
                <hr class="my-3">
                <h6 class="font-weight-bold text-primary"><i class="fa fa-info-circle"></i> Target Item Details</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Item Title</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-right">Current Rate</th>
                                <th class="text-right">Current Amount</th>
                                <th class="text-right bg-success text-white">New Amount After Transfer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td id="targetItemTitle">-</td>
                                <td id="targetItemQty" class="text-center">-</td>
                                <td id="targetItemRate" class="text-right">-</td>
                                <td id="targetItemCurrentAmount" class="font-weight-bold text-right">-</td>
                                <td id="targetItemNewAmount" class="font-weight-bold text-success text-right">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>









            {{-- <div class="row mt-3" id="existingItemSection">
                <div class="col-md-12">
                <div class="form-group">
                    <label style="font-size: 15px; font-weight: 600;">Select Target Item <span class="text-danger">*</span></label>

                            @php
                                $allItems = collect($budget->items ?? []);
                            @endphp

                            <select name="existing_item_id" id="existingItemDropdown" class="form-control" style="font-size: 15px;" required>
                                <option value="">-- Select Item --</option>
                                @foreach($allItems as $item)
                                    <option value="{{ $item->item_id }}">
                                        {{ $item->item_title }} ({{ number_format($item->quantity * $item->rate, 2) }})
                                    </option>
                                @endforeach
                                </select>

                    {{-- <select name="existing_item_id" id="existingItemDropdown" class="form-control" style="font-size: 15px;">
                    <option value="">-- Select Item --</option>
                    <!-- Populated dynamically -->
                    </select> --}}
                {{-- </div>
                </div>
            </div> --}}
























            {{-- New Item Section --}}
            <div id="newItemSection" class="d-none">
                <hr class="my-4">
                <h5 class="font-weight-bold text-success mb-3"><i class="fa fa-arrow-down"></i> New Item Details (To)</h5>

                {{-- Total Transfer Amount Display --}}
                <div class="alert alert-info">
                <i class="fa fa-info-circle"></i>
                <strong>Total Amount to Transfer:</strong>
                <span id="totalTransferDisplay" class="font-weight-bold text-primary">0.00</span>
                </div>

                <div class="row">
                <div class="col-md-12">
                    <div class="form-group mb-3">
                    <label class="font-weight-bold">Target Budget Head <span class="text-danger">*</span></label>
                    <select name="target_bh_id" class="form-control">
                        <option value="">-- Select Target Budget Head --</option>
                        @foreach($budgets as $groupKey => $budget)
                        <option value="{{ $budget->bh_id }}">
                            {{ $budget->head_title }} ({{ $budget->department->dept_name ?? '-' }})
                        </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Choose the budget head for the new item</small>
                    </div>
                </div>
                </div>

                {{-- Row 1: Item Title, Brand, Model --}}
                <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                    <label class="font-weight-bold">Item Title <span class="text-danger">*</span></label>
                    <input type="text" name="new_item_title" class="form-control" placeholder="Enter item title">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                    <label class="font-weight-bold">Brand</label>
                    <input type="text" name="brand" class="form-control" placeholder="Enter brand">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                    <label class="font-weight-bold">Model</label>
                    <input type="text" name="model" class="form-control" placeholder="Enter model">
                    </div>
                </div>
                </div>

                {{-- Row 2: Quantity, Rate, Amount --}}
                <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                    <label class="font-weight-bold">Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="quantity" class="form-control" placeholder="0" min="1">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                    <label class="font-weight-bold">Rate <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="rate" class="form-control bg-light" placeholder="Auto-calculated" min="0" readonly>
                    <small class="form-text text-muted">Auto-calculated based on quantity</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                    <label class="font-weight-bold">Amount</label>
                    <input type="text" id="newItemAmount" class="form-control font-weight-bold bg-success text-white" placeholder="0.00" readonly>
                    <small class="form-text text-muted">Quantity × Rate</small>
                    </div>
                </div>
                </div>

                <div class="form-group mb-3">
                <label class="font-weight-bold">Remarks</label>
                <textarea name="remarks" class="form-control" rows="2" placeholder="Enter any additional remarks..."></textarea>
                </div>
            </div>

            </div><!-- End Modal Body -->

            <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                <i class="fa fa-times"></i> Cancel
            </button>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa fa-check-circle"></i> Submit Re-appropriation
            </button>
            </div>

        </form>
        </div>
    </div>
    </div>







    {{-- Item Status Modal --}}
    <div class="modal fade" id="itemStatusModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-info">
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" style="font-size: 15px;">

            <!-- Loading State -->
            <div id="statusLoadingDiv" class="text-center py-5">
            <i class="fa fa-spinner fa-spin fa-3x text-info"></i>
            <p class="mt-3">Loading status...</p>
            </div>

            <!-- No Request State -->
            <div id="statusNoRequestDiv" style="display: none;" class="text-center py-5">
            <i class="fa fa-inbox fa-3x text-muted"></i>
            <h5 class="mt-3">No Quotation Request Created</h5>
            <p class="text-muted">This item has not been sent for quotation yet.</p>
            </div>

            <!-- Has Request State -->
            <div id="statusContentDiv" style="display: none;">

            <!-- Section 1: Vendors Who Received Email -->
            <div class="mb-4">
                <div class="bg-primary text-white p-2 mb-2" style="cursor: pointer;"
                    data-toggle="collapse" data-target="#emailedVendorsCollapse">
                <strong><i class="fa fa-envelope"></i> Vendors Emailed</strong>
                <i class="fa fa-chevron-down float-right mt-1"></i>
                </div>
                <div id="emailedVendorsCollapse" class="collapse show">
                <div id="emailedVendorsList" style="max-height: 200px; overflow-y: auto;">
                    <p class="text-muted">No vendors have been emailed yet.</p>
                </div>
                </div>
            </div>

            <!-- Section 2: Vendors Who Submitted Quotation -->
            <div>
                <div class="bg-success text-white p-2 mb-2" style="cursor: pointer;"
                    data-toggle="collapse" data-target="#respondedVendorsCollapse">
                <strong><i class="fa fa-check-circle"></i> Vendors Responded</strong>
                <i class="fa fa-chevron-down float-right mt-1"></i>
                </div>
                <div id="respondedVendorsCollapse" class="collapse">
                <div id="respondedVendorsList" style="max-height: 200px; overflow-y: auto;">
                    <p class="text-muted">No vendors have submitted quotations yet.</p>
                </div>
                </div>
            </div>

            <!-- Section 3: Chat Messages -->
            <div id="chatMessagesSection">
                <div class="bg-danger text-white p-2 mb-2" style="cursor: pointer;"
                    data-toggle="collapse" data-target="#chatMessagesCollapse">
                <strong><i class="fa fa-comments"></i> Request Chat</strong>
                <i class="fa fa-chevron-down float-right mt-1"></i>
                </div>
                <div id="chatMessagesCollapse" class="collapse">
                <div id="chatMessagesList" class="p-3 border" style="max-height: 220px; overflow-y: auto;">
                    <p class="text-muted mb-0">No chat messages yet.</p>
                </div>
                <div class="p-3 border border-top-0">
                    <form id="chatSendForm">
                        @csrf
                        <input type="hidden" name="qr_id" value="">
                        <input type="hidden" name="sender_type" value="procurement">
                        <div class="form-group mb-2">
                            <label class="font-weight-bold">Write Message</label>
                            <textarea name="message_text" class="form-control" rows="2" maxlength="1000" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fa fa-paper-plane"></i> Send
                        </button>
                        <span id="chatSendResponse" class="small text-muted ml-2"></span>
                    </form>
                </div>
                </div>
            </div>

            <!-- Section 3: Recommendation -->
            <div id="recommendationSection" style="display: none;">
                <div class="bg-warning text-white p-2 mb-2" style="cursor: pointer;"
                    data-toggle="collapse" data-target="#recommendationCollapse">
                <strong><i class="fa fa-star"></i> Recommendation</strong>
                <i class="fa fa-chevron-down float-right mt-1"></i>
                </div>
                <div id="recommendationCollapse" class="collapse">
                <div id="recommendationContent" class="p-3 border">
                    <p class="text-muted">No recommendation yet.</p>
                </div>
                </div>
            </div>

            <!-- Section 4: PO Generation -->
            <div id="poGenerationSection" style="display: none;">
                <div class="bg-info text-white p-2 mb-2" style="cursor: pointer;"
                    data-toggle="collapse" data-target="#poGenerationCollapse">
                <strong><i class="fa fa-file-text"></i> PO Generation</strong>
                <i class="fa fa-chevron-down float-right mt-1"></i>
                </div>
                <div id="poGenerationCollapse" class="collapse">
                <div id="poGenerationContent" class="p-3 border">
                    <p class="text-muted">PO not generated yet.</p>
                </div>
                </div>
            </div>

            </div>

        </div>
        <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Close
            </button>
        </div>
        </div>
    </div>
    </div>

    {{-- Item Details Modal --}}
    <div class="modal fade" id="itemDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-light">
            <h5 class="modal-title">
            <i class="fa fa-list"></i> Item Details
            </h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" style="font-size: 15px;">
            <table class="table table-bordered">
            <tbody>
                <tr>
                <th width="40%">Item Title</th>
                <td>
                    <span class="clickable-title-status"
                        style="cursor: pointer; color: #4562C3; text-decoration: underline;"
                        data-item-id="">
                    <i class="fa fa-info-circle"></i> <span id="detail-title-text"></span>
                    </span>
                </td>
                </tr>
                <tr>
                <th>Brand</th>
                <td id="detail-brand"></td>
                </tr>
                <tr>
                <th>Model</th>
                <td id="detail-model"></td>
                </tr>
                <tr>
                <th>Quantity</th>
                <td id="detail-qty"></td>
                </tr>
                <tr>
                <th>Rate</th>
                <td id="detail-rate"></td>
                </tr>
                <tr>
                <th>Amount</th>
                <td id="detail-amount" class="font-weight-bold"></td>
                </tr>
                <tr>
                <th>Budget Type</th>
                <td id="detail-type"></td>
                </tr>
                <tr>
                <th>Remarks</th>
                <td id="detail-remarks"></td>
                </tr>
            </tbody>
            </table>
        </div>
        <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Close
            </button>
        </div>
        </div>
    </div>
    </div>

    {{-- Revise Quotation Request Modal --}}
    <div class="modal fade" id="editQuotationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header bg-warning">
            <h5 class="modal-title text-white">
            <i class="fa fa-refresh"></i> Revise Quotation Request
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>

        {{-- Tabs Navigation --}}
        <ul class="nav nav-tabs bg-light" role="tablist" style="margin: 0 !important;">
            <li class="nav-item">
            <a class="nav-link active" id="new-tab" data-toggle="tab" href="#newRevision" role="tab">
                <i class="fa fa-plus-circle"></i> New Revision
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="history-tab" data-toggle="tab" href="#historyTab" role="tab">
                <i class="fa fa-history"></i> History
            </a>
            </li>
        </ul>

        <form id="editQuotationForm">
            @csrf
            <div class="modal-body">
            <div class="tab-content">
                {{-- New Revision Tab --}}
                <div class="tab-pane fade show active" id="newRevision" role="tabpanel">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    <strong>Note:</strong> Revising this quotation request will create a new revision. Previous version will be saved in history.
                </div>

                <input type="hidden" id="editQrId" name="qr_id">
                <input type="hidden" id="editItemId" name="item_id">
                <input type="hidden" id="editBudgetId" name="bh_id">

                <div class="form-group">
                    <label class="font-weight-bold">Item Title</label>
                    <input type="text" id="editItemTitle" class="form-control" readonly>
                </div>

                <div class="row">
                    <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Budget Quantity</label>
                        <input type="number" id="editBudgetQty" class="form-control" readonly>
                    </div>
                    </div>
                    <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Requested Quantity <span class="text-danger">*</span></label>
                        <input type="number" id="editRequestedQty" name="requested_quantity"
                            class="form-control" min="1" required>
                        <small class="form-text text-muted">Cannot exceed Budget Quantity</small>
                    </div>
                    </div>
                </div>
                </div>

                {{-- History Tab --}}
                <div class="tab-pane fade" id="historyTab" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                        <th>Version</th>
                        <th class="text-center">Budget Qty</th>
                        <th class="text-center">Requested Qty</th>
                        <th class="text-center">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        <tr>
                        <td colspan="4" class="text-center text-muted py-3">
                            <i class="fa fa-spinner fa-spin"></i> Loading history...
                        </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
                </div>
            </div>
            </div>
            <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                <i class="fa fa-times"></i> Cancel
            </button>
            <button type="submit" class="btn btn-warning">
                <i class="fa fa-refresh"></i> Revise
            </button>
            </div>
        </form>
        </div>
    </div>
    </div>

    {{-- Confirmation Modal for Quotation Request --}}
    <div class="modal fade" id="confirmQuotationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-light">
            <h5 class="modal-title">
            <i class="fa fa-question-circle"></i> Confirm Quotation Request
            </h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> <strong>Note:</strong> Enter quantity you want to request quotation for. This can be different from budget quantity.
            </div>

            <div class="table-responsive">
            <table class="table table-bordered" id="quotationItemsTable">
                <thead class="thead-light">
                <tr>
                    <th style="width: 50%;">Item Title</th>
                    <th class="text-center" style="width: 20%;">Budget Qty</th>
                    <th class="text-center" style="width: 30%;">Request Qty <span class="text-danger">*</span></th>
                </tr>
                </thead>
                <tbody id="selectedItemsList">
                {{-- Will be filled via JavaScript --}}
                </tbody>
            </table>
            </div>

            <input type="hidden" id="quotationBudgetGroups" value="">
        </div>
        <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
            </button>
            <button type="button" class="btn btn-primary" id="confirmQuotationBtn">
            <i class="fa fa-check"></i> Send Request
            </button>
        </div>
        </div>
    </div>
    </div>


    @endsection
    @section('javascript')
    <script>
        $(document).on('click', '.view-remarks-btn', function() {
            const remarks = $(this).data('remarks') || 'No remarks available.';
            $('#remarksContent').text(remarks);
            $('#remarksModal').modal('show');
        });
$(document).ready(function () {
    let selectedItemId = null; // store the item ID temporarily

    // When "Send for Approval" button is clicked
    $(document).on('click', '.send-approval-btn', function () {
        selectedItemId = $(this).data('item-id');
        $('#approvalModal').modal('show'); // Show modal
    });

    // When user confirms in the modal
    $('#confirmApprovalBtn').on('click', function () {
        if (!selectedItemId) return;

        $.ajax({
            url: "{{ route('reappropriation.sendForApproval') }}",
            type: "POST",
            data: {
                item_id: selectedItemId,
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                $('#approvalModal').modal('hide');
                if (response.success) {
                      showToast('success', response.message, 'Success');
                    // alert('Item sent for approval successfully!');
                    location.reload();
                } else {
                    alert(response.message || 'Something went wrong.');
                }
            },
            error: function (xhr) {
                $('#approvalModal').modal('hide');
                console.error(xhr.responseText);
                alert('Error sending approval request.');
            }
        });
    });
});
</script>

    <script>
        // Enable Bootstrap tooltips on this page
        $(function () {
            $('.budget-head-title').each(function(){
                var parentName = $(this).data('parent') || '-';
                $(this).attr('title', 'Parent Department: ' + parentName);
            });
            $('[data-toggle="tooltip"]').tooltip({container: 'body'});
        });


    var chatCsrf = '{{ csrf_token() }}';
var currentUsername = '{{ addslashes(auth()->user()->username ?? (auth()->user()->EMP_AD_USERNAME ?? (auth()->user()->name ?? ''))) }}';

    function updateTopChatCounter() {
        if (!$('#unreadChatsToggle').length) {
            return;
        }
        var total = 0;
        $('#topUnreadChatList .top-chat-entry').each(function () {
            var count = parseInt($(this).find('.badge-pill').text(), 10);
            if (!isNaN(count)) {
                total += count;
            }
        });
        if (total <= 0) {
            $('#unreadChatsToggle').closest('.mb-3').hide();
        } else {
            $('#unreadChatsToggle').closest('.mb-3').show();
            $('#unreadChatsToggle').html('<i class="fa fa-comments"></i> Quotation Chats (' + total + ')');
        }
    }

    function markChatMessagesReadForQr(qrId) {
        if (!qrId) {
            return;
        }
        $.ajax({
            url: '{{ route("sp.quotations.chat.read") }}',
            type: 'POST',
            data: {
                _token: chatCsrf,
                qr_id: qrId
            },
            success: function () {
                $('.chat-unread-qr' + qrId).remove();
                $('#topUnreadChatList .top-chat-entry[data-qr-id="' + qrId + '"]').remove();
                $('#chatUnreadBadge' + qrId).remove();
                updateTopChatCounter();
            }
        });
    }





    // new

// $(document).on('click', '.send-approval-btn', function() {
//    let selectedItemId = null; // store the item ID temporarily


//         selectedItemId = $(this).data('item-id');
//         $('#approvalModal').modal('show'); // Show modal







//     let itemId = $(this).data('item-id');



//     $.ajax({
//         url: "{{ route('reappropriation.sendForApproval') }}",
//         type: "POST",
//         data: {
//             item_id: itemId,
//             _token: "{{ csrf_token() }}"
//         },
//         success: function(response) {
//             if (response.success) {
//                 alert('Item sent for approval successfully!');
//                 location.reload(); // Refresh table or update dynamically
//             } else {
//                 alert(response.message || 'Something went wrong.');
//             }
//         },
//         error: function(xhr) {
//             console.error(xhr.responseText);
//             alert('Error sending approval request.');
//         }
//     });
// });

    updateTopChatCounter();

    // ============================================================================
    // ===                  REAPPROPRIATION WORKING - START                     ===
    // ============================================================================
    // Purpose: Handle budget re-appropriation across different budget heads
    // Features:
    //   1. Switch between existing item or new item
    //   2. Load items from selected budget head
    //   3. Show item details and calculate new amounts
    //   4. Update amounts dynamically when user enters transfer values
    // ============================================================================

    // --- 1. SWITCH BETWEEN EXISTING ITEM AND NEW ITEM ---
    // When user clicks radio button (existing item or create new item)
    $('input[name="reappropriation_type"]').change(function () {

        var selectedType = $(this).val(); // Get selected value: 'existing' or 'new'

        if (selectedType === 'existing') {
            // User wants to add to existing item
            $('#existingItemSection').removeClass('d-none');
            $('#newItemSection').addClass('d-none');

            // Disable new item fields so they don't submit
            $('#newItemSection [name="target_bh_id"]').prop('disabled', true);
            $('#newItemSection [name="new_item_title"]').prop('disabled', true);
            $('#newItemSection [name="brand"]').prop('disabled', true);
            $('#newItemSection [name="model"]').prop('disabled', true);
            $('#newItemSection [name="quantity"]').prop('disabled', true);
            $('#newItemSection [name="rate"]').prop('disabled', true);
            $('#newItemSection [name="remarks"]').prop('disabled', true);

            // Enable existing item fields
            $('#existingItemSection [name="target_bh_id"]').prop('disabled', false).attr('required', true);
            $('#existingItemDropdown').prop('disabled', false).attr('required', true);

        } else {
            // User wants to create new item
            $('#existingItemSection').addClass('d-none');
            $('#newItemSection').removeClass('d-none');

            // Disable existing item fields so they don't submit
            $('#existingItemSection [name="target_bh_id"]').prop('disabled', true);
            $('#existingItemDropdown').prop('disabled', true);

            // Enable new item fields
            $('#newItemSection [name="target_bh_id"]').prop('disabled', false).attr('required', true);
            $('#newItemSection [name="new_item_title"]').prop('disabled', false).attr('required', true);
            $('#newItemSection [name="brand"]').prop('disabled', false);
            $('#newItemSection [name="model"]').prop('disabled', false);
            $('#newItemSection [name="quantity"]').prop('disabled', false).attr('required', true);
            $('#newItemSection [name="rate"]').prop('disabled', false).attr('required', true);
            $('#newItemSection [name="remarks"]').prop('disabled', false);
        }
    });

    // new

    $(document).ready(function() {
        $('.view-history-btn').click(function() {
            let itemId = $(this).data('item-id');
            let modal = $('#historyModal');
            let tableBody = modal.find('#historyTable tbody');

            tableBody.html('<tr><td colspan="5" class="text-center text-muted">Loading...</td></tr>');
            modal.modal('show');

            $.ajax({
                url: '/reappropriation-history/' + itemId,
                type: 'GET',
                success: function(response) {
                    if (response.data && response.data.length > 0) {
                        let rows = '';
                        $.each(response.data, function(index, history) {
                            rows += `
                                <tr>
                                    <td>${history.from_item_id}</td>
                                    <td>${history.from_item_title}</td>
                                    <td>${history.amount}</td>
                                    <!--td>${history.category_name}</td-->
                                    <td>${history.entry_user_id}</td>
                                    <td>${history.created_at}</td>
                                </tr>`;
                        });
                        tableBody.html(rows);
                    } else {
                        tableBody.html('<tr><td colspan="5" class="text-center text-muted">No history found</td></tr>');
                    }
                },
                error: function() {
                    tableBody.html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
                }
            });
        });
    });



    // Set initial state when page loads
    $('input[name="reappropriation_type"]:checked').trigger('change');


    // --- 2. LOAD ITEMS WHEN BUDGET HEAD IS SELECTED ---
    // When user selects target budget head, load its items via AJAX
    $('#existingItemSection select[name="target_bh_id"]').change(function () {

        var budgetHeadId = $(this).val(); // Get selected budget head ID

        // If no budget head selected, clear dropdown
        if (!budgetHeadId) {
            $('#existingItemDropdown').empty().append('<option value="">-- Select Item --</option>');
            $('#targetItemDetailsSection').addClass('d-none');
            return;
        }

        // Call backend API to get items
        $.ajax({
            url: '/get-items-by-budget-head/' + budgetHeadId,
            type: 'GET',
            success: function (items) {
                // Clear existing options
                $('#existingItemDropdown').empty().append('<option value="">-- Select Item --</option>');

                // Add each item to dropdown
                $.each(items, function (index, item) {
                    var optionHtml = '<option value="' + item.id + '" ' +
                        'data-title="' + item.item_title + '" ' +
                        'data-qty="' + item.quantity + '" ' +
                        'data-rate="' + item.rate + '" ' +
                        'data-amount="' + item.amount + '">' +
                        item.item_title + ' (' + item.amount + ')' +
                        '</option>';
                    $('#existingItemDropdown').append(optionHtml);
                });
            },
            error: function() {
                showToast('error', 'Failed to load items. Please try again.', 'Error');
            }
        });
    });


    // --- 3. SHOW ITEM DETAILS WHEN SELECTED ---
    // When user selects an item from dropdown, show its details
    $('#existingItemDropdown').on('change', function() {

        var selectedOption = $(this).find('option:selected');

        // If no item selected, hide details
        if (selectedOption.val() === '') {
            $('#targetItemDetailsSection').addClass('d-none');
            return;
        }

        // Get item information from data attributes
        var itemTitle = selectedOption.data('title');
        var itemQty = selectedOption.data('qty');
        var itemRate = parseFloat(selectedOption.data('rate'));
        var itemAmount = parseFloat(selectedOption.data('amount'));

        // Fill item details in table
        $('#targetItemTitle').text(itemTitle);
        $('#targetItemQty').text(itemQty);
        $('#targetItemRate').text(itemRate.toFixed(2));
        $('#targetItemCurrentAmount').text(itemAmount.toFixed(2));
        $('#targetItemNewAmount').text(itemAmount.toFixed(2)); // Initially same as current

        // Show the details section
        $('#targetItemDetailsSection').removeClass('d-none');

        // Recalculate amounts
        updateTargetNewAmount();
    });


    // --- 4. UPDATE TARGET ITEM AMOUNT DYNAMICALLY ---
    // Function: Calculate new amount = current amount + total transfer
    function updateTargetNewAmount() {

        var totalTransfer = 0; // Sum of all transfer amounts

        // Loop through all transfer input fields and add them up
        $('.transfer-amount').each(function() {
            var amount = parseFloat($(this).val()) || 0;
            totalTransfer = totalTransfer + amount;
        });

        // Get current target item amount
        var currentAmount = parseFloat($('#targetItemCurrentAmount').text()) || 0;

        // Calculate: new amount = current + transfer
        var newAmount = currentAmount + totalTransfer;

        // Update the display
        $('#targetItemNewAmount').text(newAmount.toFixed(2));
    }


    // --- 5. UPDATE REMAINING AMOUNT WHEN USER TYPES ---
    // When user enters transfer amount, update remaining amount
    $('#selectedItemsTable').on('input', '.transfer-amount', function() {

        var inputField = $(this);
        var transferAmount = parseFloat(inputField.val()) || 0;
        var availableAmount = parseFloat(inputField.attr('data-available'));

        // Validation: Check if transfer amount exceeds available amount
        if (transferAmount > availableAmount) {
            // Show error styling
            inputField.addClass('border-danger');

            // Auto-correct to max available
            inputField.val(availableAmount.toFixed(2));
            transferAmount = availableAmount;

            // Show warning toast
            showToast('warning', 'Transfer amount cannot exceed available amount. Auto-corrected to maximum.', 'Invalid Amount');
        } else {
            // Remove error styling
            inputField.removeClass('border-danger');
        }

        // Calculate remaining = available - transfer
        var remaining = availableAmount - transferAmount;

        // Update remaining amount display for this row
        inputField.closest('tr').find('.remaining-amount strong').text(remaining.toFixed(2));

        // Update remaining color based on value
        var remainingCell = inputField.closest('tr').find('.remaining-amount strong');
        if (remaining < 0) {
            remainingCell.addClass('text-danger');
        } else if (remaining === 0) {
            remainingCell.removeClass('text-danger').addClass('text-success');
        } else {
            remainingCell.removeClass('text-danger text-success');
        }

        // If target item is selected, recalculate its new amount
        if ($('#existingItemDropdown').val()) {
            updateTargetNewAmount();
        }
    });

    // --- 6. AUTO-CALCULATE RATE AND AMOUNT FOR NEW ITEM ---
    // When user enters quantity, automatically calculate rate and amount
    $('#newItemSection [name="quantity"]').on('input', function() {

        var quantity = parseFloat($(this).val()) || 0;

        // Calculate total transferred amount from all source items
        var totalTransfer = 0;
        $('.transfer-amount').each(function() {
            var amount = parseFloat($(this).val()) || 0;
            totalTransfer = totalTransfer + amount;
        });

        // Update total transfer display
        $('#totalTransferDisplay').text(totalTransfer.toFixed(2));

        // If no quantity, clear rate and amount
        if (quantity <= 0) {
            $('#newItemSection [name="rate"]').val('');
            $('#newItemAmount').val('');
            return;
        }

        // If no transfer amount, clear rate and amount
        if (totalTransfer <= 0) {
            $('#newItemSection [name="rate"]').val('');
            $('#newItemAmount').val('');
            return;
        }

        // Calculate rate: Total Transfer Amount / Quantity
        var rate = totalTransfer / quantity;

        // Set the rate (rounded to 2 decimal places)
        $('#newItemSection [name="rate"]').val(rate.toFixed(2));

        // Calculate and display amount: Quantity × Rate
        var amount = quantity * rate;
        $('#newItemAmount').val(amount.toFixed(2));
    });

    // Also recalculate when transfer amounts change
    $('#selectedItemsTable').on('input', '.transfer-amount', function() {
        // Update total transfer display
        var totalTransfer = 0;
        $('.transfer-amount').each(function() {
            var amount = parseFloat($(this).val()) || 0;
            totalTransfer = totalTransfer + amount;
        });
        $('#totalTransferDisplay').text(totalTransfer.toFixed(2));

        // Trigger quantity input to recalculate rate if in new item mode
        var selectedType = $('input[name="reappropriation_type"]:checked').val();
        if (selectedType === 'new') {
            $('#newItemSection [name="quantity"]').trigger('input');
        }
    });

    // ============================================================================
    // ===                   REAPPROPRIATION WORKING - END                      ===
    // ======================================================= =====================


    // ============================================================================
    // OPEN ITEM STATUS MODAL
    // Shows which vendors received email and which vendors submitted quotations
    // ============================================================================
    function openItemStatusModal(itemId, buttonElement) {

        // Step 1: Reset modal to loading state
        $('#statusLoadingDiv').show();
        $('#statusNoRequestDiv').hide();
        $('#statusContentDiv').hide();

        // Step 2: Open modal immediately (show loading)
        $('#itemStatusModal').modal('show');

        // Step 3: Fetch item status data from backend
        $.ajax({
            url: '{{ route("budget.item.status") }}',
            method: 'GET',
            data: { item_id: itemId },
            success: function(response) {

                // Hide loading indicator
                $('#statusLoadingDiv').hide();

                // Check if item has quotation request
                if (!response.has_request) {
                    // No quotation request created yet
                    $('#statusNoRequestDiv').show();
                    return;
                }

                // Show content div
                $('#statusContentDiv').show();

        var qrToMark = null;
        if (buttonElement) {
            qrToMark = $(buttonElement).data('qr-id') || null;
        }
        if (!qrToMark && response.quotation_request) {
            qrToMark = response.quotation_request.qr_id;
        }
        markChatMessagesReadForQr(qrToMark);

                // ====================================================================
                // BUILD EMAILED VENDORS LIST
                // Show which vendors received the quotation request email
                // ====================================================================
                var emailedHtml = '';
                if (response.emailed_vendors && response.emailed_vendors.length > 0) {
                    emailedHtml = '<div class="list-group">';
                    response.emailed_vendors.forEach(function(vendor) {
                        var sentDate = new Date(vendor.sent_at).toLocaleString();
                        emailedHtml += `
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <i class="fa fa-user text-primary"></i>
                                        <strong>${vendor.vendor_name}</strong>
                                    </div>
                                    <small class="text-muted">${sentDate}</small>
                                </div>
                                <div class="mt-1">
                                    <small class="text-muted">
                                        <i class="fa fa-envelope"></i> ${vendor.email}
                                    </small>
                                </div>
                            </div>
                        `;
                    });
                    emailedHtml += '</div>';
                } else {
                    emailedHtml = '<p class="text-muted">No vendors have been emailed yet.</p>';
                }
                $('#emailedVendorsList').html(emailedHtml);

                // ====================================================================
                // BUILD RESPONDED VENDORS LIST WITH EXPANDABLE VERSIONS
                // ====================================================================
                var respondedHtml = '';
                if (response.responded_vendors && response.responded_vendors.length > 0) {
                    respondedHtml = '<div id="respondedAccordion">';
                    response.responded_vendors.forEach(function(vendor, index) {
                        var collapseId = 'collapseVendor' + index;
                        var versionCount = vendor.versions ? vendor.versions.length : 0;

                        // Vendor header - simple blue
                        respondedHtml += `
                            <div class="mb-2">
                                <div class="bg-primary text-white p-2" style="cursor: pointer;"
                                    data-toggle="collapse" data-target="#${collapseId}">
                                    <i class="fa fa-user"></i> <strong>${vendor.vendor_name}</strong>
                                    <span class="badge badge-light ml-2">${versionCount} Version(s)</span>
                                    <i class="fa fa-chevron-down float-right mt-1"></i>
                                </div>
                                <div id="${collapseId}" class="collapse" data-parent="#respondedAccordion">
                                    <div class="border p-3">
                        `;

                        // Show ALL versions for this vendor
                        if (vendor.versions && vendor.versions.length > 0) {
                            vendor.versions.forEach(function(version, vIndex) {
                                var currencyCode = (version.currency || 'PKR').toUpperCase();
                                var currencySymbol = currencyCode === 'USD' ? '$' : '₨';
                                var priceValue = parseFloat(version.price);
                                var priceText = !isNaN(priceValue) ? `${currencySymbol} ${Number(priceValue).toLocaleString()}` : '-';
                                var quantityValue = parseFloat(version.quantity);
                                var quantityText = !isNaN(quantityValue) ? Number(quantityValue).toLocaleString() : '-';
                                var rateValue = parseFloat(version.rate);
                                var rateText = !isNaN(rateValue) ? `${currencySymbol} ${Number(rateValue).toLocaleString()}` : '-';
                                var advanceRequiredLabel = (version.advance_required || '').toLowerCase() === 'yes' ? 'Yes' : 'No';
                                var advancePercentValue = parseFloat(version.advance_percent);
                                var advancePercentText = !isNaN(advancePercentValue)
                                    ? `${Number(advancePercentValue).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 })}%`
                                    : '-';
                                var submittedDate = new Date(version.submitted_at).toLocaleString();
                                var isCurrent = version.is_current == 1;
                                var versionLabel = isCurrent ?
                                    `<span class="badge badge-success">Version ${version.version_number} (Current)</span>` :
                                    `<span class="badge badge-secondary">Version ${version.version_number}</span>`;
                                var itemTitle = version.item_title || '-';
                                var brand = version.brand || '-';
                                var model = version.model || '-';
                                var currencyLabel = version.currency || '-';

                                respondedHtml += `
                                    <div class="mb-3 pb-3 ${vIndex < vendor.versions.length - 1 ? 'border-bottom' : ''}">
                                        <div class="d-flex justify-content-between mb-2">
                                            ${versionLabel}
                                            <small class="text-muted">${submittedDate}</small>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong><i class="fa fa-tag text-primary"></i> Item Title:</strong> ${itemTitle}</p>
                                                <p class="mb-1"><strong><i class="fa fa-industry text-secondary"></i> Brand:</strong> ${brand}</p>
                                                <p class="mb-1"><strong><i class="fa fa-cube text-muted"></i> Model:</strong> ${model}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong><i class="fa fa-sort-numeric-up text-dark"></i> Quantity:</strong> ${quantityText}</p>
                                                <p class="mb-1"><strong><i class="fa fa-balance-scale text-info"></i> Unit Rate:</strong> ${rateText}</p>
                                                <p class="mb-1"><strong><i class="fa fa-money text-success"></i> Price:</strong> ${priceText}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong><i class="fa fa-globe text-info"></i> Currency:</strong> ${currencyLabel}</p>
                                                <p class="mb-1"><strong><i class="fa fa-credit-card text-success"></i> Advance Payment:</strong> ${advanceRequiredLabel}</p>
                                                <p class="mb-1"><strong><i class="fa fa-percent text-success"></i> Advance %:</strong> ${advancePercentText}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong><i class="fa fa-calendar text-warning"></i> Delivery:</strong> ${version.delivery_time || '-'}</p>
                                                <p class="mb-1"><strong><i class="fa fa-shield text-primary"></i> Warranty:</strong> ${version.warranty || '-'}</p>
                                            </div>
                                        </div>
                                        ${version.specifications ? `
                                            <div class="mt-2">
                                                <strong><i class="fa fa-cogs text-secondary"></i> Specifications:</strong>
                                                <p class="text-muted mb-0">${version.specifications}</p>
                                            </div>
                                        ` : ''}
                                        ${version.remarks ? `
                                            <div class="mt-2">
                                                <strong><i class="fa fa-comment text-info"></i> Remarks:</strong>
                                                <p class="text-muted mb-0">${version.remarks}</p>
                                            </div>
                                        ` : ''}
                                    </div>
                                `;
                            });
                        } else {
                            respondedHtml += '<p class="text-muted">No version data available.</p>';
                        }

                        respondedHtml += `
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    respondedHtml += '</div>';
                } else {
                    respondedHtml = '<p class="text-muted">No vendors have submitted quotations yet.</p>';
                }
                $('#respondedVendorsList').html(respondedHtml);

            if (response.chat_messages && response.chat_messages.length > 0) {
                $('#chatMessagesSection').show();
                var chatHtml = '<div class="list-group">';
                response.chat_messages.forEach(function(chat) {
                    var when = chat.created_at ? new Date(chat.created_at).toLocaleString() : '';
                    var escapedMessage = $('<div>').text(chat.message_text || '').html();
                    var isSelf = false;
                    if (currentUsername && chat.entry_username) {
                        isSelf = currentUsername.toLowerCase() === chat.entry_username.toLowerCase();
                    } else if (chat.sender_type) {
                        isSelf = chat.sender_type.toLowerCase() === 'procurement';
                    }
                    var selfClass = isSelf ? 'text-right' : 'text-left';
                    chatHtml += '<div class="list-group-item ' + selfClass + '">';
                    chatHtml += '<div>' + escapedMessage + '</div>';
                    if (when) {
                        chatHtml += '<div class="small text-muted mt-1"><i class="fa fa-clock-o"></i> ' + when + '</div>';
                    }
                    chatHtml += '</div>';
                });
                chatHtml += '</div>';
                $('#chatMessagesList').html(chatHtml);
            } else {
                $('#chatMessagesList').html('<p class="text-muted mb-0">No chat messages yet.</p>');
            }
            var chatQr = qrToMark || (response.quotation_request ? response.quotation_request.qr_id : null);
            $('#chatSendForm input[name="qr_id"]').val(chatQr);
            $('#chatSendForm textarea[name="message_text"]').val('');
            $('#chatSendResponse').text('');
            var chatQr = qrToMark || (response.quotation_request ? response.quotation_request.qr_id : null);
            $('#chatSendForm input[name="qr_id"]').val(chatQr);
            $('#chatSendForm textarea[name="message_text"]').val('');
            $('#chatSendResponse').text('');

                // ====================================================================
                // BUILD RECOMMENDATION SECTION
                // ====================================================================
                if (response.po_tracking && response.po_tracking.selected_vendor_id) {
                    $('#recommendationSection').show();

                    // Find recommended vendor name
                    var recommendedVendor = response.responded_vendors.find(v => v.vendor_id == response.po_tracking.selected_vendor_id);
                    var vendorName = recommendedVendor ? recommendedVendor.vendor_name : 'Unknown Vendor';
                    var versionNum = response.po_tracking.selected_version || '-';

                    var recHtml = `
                        <div class="alert alert-warning mb-0">
                            <i class="fa fa-star"></i> <strong>Recommended Vendor:</strong> ${vendorName}<br>
                            <i class="fa fa-info-circle"></i> <strong>Selected Version:</strong> ${versionNum}
                        </div>
                    `;
                    $('#recommendationContent').html(recHtml);
                } else {
                    $('#recommendationSection').hide();
                }

                // ====================================================================
                // BUILD PO GENERATION SECTION
                // ====================================================================
                if (response.po_tracking && response.po_tracking.selected_vendor_id) {
                    $('#poGenerationSection').show();

                    var poHtml = '';
                    if (response.po_tracking.po_status === 'generated' && response.po_tracking.po_number) {
                        // PO Generated - Show PO number with button
                        poHtml = `
                            <div class="alert alert-success mb-0">
                                <i class="fa fa-check-circle"></i> <strong>PO Generated</strong><br>
                                <i class="fa fa-file-text"></i> <strong>PO Number:</strong> ${response.po_tracking.po_number}
                                <br><br>
                                <a href="{{ url('purchase-orders') }}/${response.po_tracking.po_id}"
                                target="_blank"
                                class="btn btn-success btn-sm">
                                    <i class="fa fa-external-link"></i> View Purchase Order
                                </a>
                                <a href="{{ url('purchase-orders') }}/${response.po_tracking.po_id}/new-pdf"
                                target="_blank"
                                class="btn btn-success btn-sm">
                                    <i class="fa fa-external-link"></i> New PDF
                                </a>
                            </div>
                        `;
                    } else {
                        // PO Pending - Show approval list
                        poHtml = `<div class="alert alert-info mb-2">
                            <i class="fa fa-clock-o"></i> <strong>PO Generation Pending</strong>
                        </div>`;

                        // Show approvals list
                        if (response.po_tracking.approvals && response.po_tracking.approvals.length > 0) {
                            poHtml += '<div class="table-responsive"><table class="table table-sm table-bordered mb-0">';
                            poHtml += '<thead class="bg-light"><tr>';
                            poHtml += '<th width="10%">Step</th>';
                            poHtml += '<th width="50%">Approver</th>';
                            poHtml += '<th width="40%" class="text-center">Status</th>';
                            poHtml += '</tr></thead><tbody>';

                            response.po_tracking.approvals.forEach(function(approval) {
                                var statusBadge = '';
                                if (approval.status === 'approved') {
                                    statusBadge = '<span class="badge badge-success"><i class="fa fa-check"></i> Approved</span>';
                                } else if (approval.status === 'rejected') {
                                    statusBadge = '<span class="badge badge-danger"><i class="fa fa-times"></i> Rejected</span>';
                                } else {
                                    statusBadge = '<span class="badge badge-warning"><i class="fa fa-clock-o"></i> Pending</span>';
                                }

                                poHtml += `<tr>
                                    <td class="text-center">${approval.slab_order}</td>
                                    <td>${approval.approver_name}</td>
                                    <td class="text-center">${statusBadge}</td>
                                </tr>`;
                            });

                            poHtml += '</tbody></table></div>';
                        } else {
                            poHtml += '<p class="text-muted mb-0">No approval details available.</p>';
                        }
                    }
                    $('#poGenerationContent').html(poHtml);
                } else {
                    $('#poGenerationSection').hide();
                }

            },
            error: function(xhr) {
                // Hide loading, show error
                $('#statusLoadingDiv').hide();
                $('#statusContentDiv').show();
                $('#emailedVendorsList').html('<div class="alert alert-danger">Failed to load data. Please try again.</div>');
                $('#respondedVendorsList').html('<div class="alert alert-danger">Failed to load data. Please try again.</div>');
            }
        });
    }

        // Function to open Item Details Modal
        function openItemDetailsModal(element) {
            // Get data from element (button or span)
            var itemId = $(element).data('item-id');
            var title = $(element).data('title');
            var brand = $(element).data('brand') || 'N/A';
            var model = $(element).data('model') || 'N/A';
            var qty = $(element).data('qty');
            var rate = $(element).data('rate');
            var amount = $(element).data('amount');
            var type = $(element).data('type');
            var remarks = $(element).data('remarks');

            // Fill item details
            $('#detail-title-text').text(title);
            $('.clickable-title-status').data('item-id', itemId);
            $('#detail-brand').text(brand);
            $('#detail-model').text(model);
            $('#detail-qty').text(qty);
            $('#detail-rate').text(rate);
            $('#detail-amount').text(amount);
            $('#detail-type').html('<span class="badge badge-secondary">' + type + '</span>');
            $('#detail-remarks').text(remarks);

            // Show Details modal
            $('#itemDetailsModal').modal('show');
        }

        // View Status - Open Status Modal from Button
        $(document).on('click', '.viewDetailsBtn', function() {
            var itemId = $(this).data('item-id');
            openItemStatusModal(itemId, this);
        });

        // View Details - Open Details Modal from Item Title in Table
        $(document).on('click', '.clickable-item-title-table', function() {
            openItemDetailsModal(this);
        });

        // Click on Item Title in Details Modal to Open Status Modal
        $(document).on('click', '.clickable-title-status', function() {
            var itemId = $(this).data('item-id');
            // Close Details modal
            $('#itemDetailsModal').modal('hide');
            // Open Status modal
            openItemStatusModal(itemId);
        });

    // ============================================================================
    // ===              REAPPROPRIATION MODAL & FORM - START                    ===
    // ============================================================================

    // ========================================================================
    // OPEN RE-APPROPRIATION MODAL
    // When user clicks "Re-appropriate" button after selecting items
    // ========================================================================
    $(document).on('click', '.multiReappropriateBtnGlobal', function() {

        // Collect all selected items from the budget list
        var selectedItems = [];

        // Loop through each checked checkbox
        $('input.item-checkbox:checked:not(:disabled)').each(function() {

            // Get references to checkbox, row, and section
            var checkbox = $(this);
            var tableRow = checkbox.closest('tr');
            var budgetSection = checkbox.closest('.budget-section');

            // Extract item data from the table row
            var itemId = checkbox.val();
            var budgetHeadName = budgetSection.find('h4').text().trim();
            var itemTitle = tableRow.find('td:nth-child(2)').text().trim();
            var itemAmount = tableRow.find('td:nth-child(5)').text().replace(/,/g, ''); // Remove commas

            // Create item object with clean data
            var itemData = {
                id: itemId,
                head: budgetHeadName,
                title: itemTitle,
                amount: parseFloat(itemAmount)
            };

            // Add to selected items array
            selectedItems.push(itemData);
        });

        // Validate: Check if at least one item is selected
        if (selectedItems.length === 0) {
            showToast('warning', 'Please select at least one item.', 'No Items Selected');
            return; // Stop here
        }

        // ========================================================================
        // BUILD TABLE IN MODAL
        // Show each selected item in the source items table
        // ========================================================================

        var tableBody = $('#selectedItemsTable tbody');
        tableBody.empty(); // Clear any previous data

        // Loop through each selected item and create a table row
        for (var i = 0; i < selectedItems.length; i++) {
            var item = selectedItems[i];

            // Create new table row
            var newRow = $('<tr>');

            // === Column 1: Budget Head Name ===
            var budgetHeadColumn = '<td class="align-middle"><strong>' + item.head + '</strong></td>';
            newRow.append(budgetHeadColumn);

            // === Column 2: Item Title (with hidden input for ID) ===
            var itemTitleColumn = $('<td class="align-middle">');
            var hiddenInput = '<input type="hidden" name="sources[' + item.id + '][id]" value="' + item.id + '">';
            itemTitleColumn.append(hiddenInput);
            itemTitleColumn.append(document.createTextNode(item.title)); // Safe text insertion
            newRow.append(itemTitleColumn);

            // === Column 3: Available Amount ===
            var availableAmountColumn = '<td class="text-right align-middle"><strong>' + item.amount.toFixed(2) + '</strong></td>';
            newRow.append(availableAmountColumn);

            // === Column 4: Transfer Amount Input Field ===
            var transferInputColumn = $('<td class="align-middle">');
            var transferInput = $('<input>')
                .attr('type', 'number')
                .attr('step', '0.01')
                .attr('name', 'sources[' + item.id + '][amount]')
                .attr('placeholder', '0.00')
                .attr('min', '0.01')
                .attr('max', item.amount)
                .attr('required', true)
                .attr('data-available', item.amount) // Store available amount
                .attr('data-item-title', item.title) // Store item title for validation
                .addClass('form-control transfer-amount');
            transferInputColumn.append(transferInput);
            newRow.append(transferInputColumn);

            // === Column 5: Remaining Amount (will update when user types) ===
            var remainingAmountColumn = '<td class="text-right align-middle font-weight-bold remaining-amount">';
            remainingAmountColumn += '<strong>' + item.amount.toFixed(2) + '</strong></td>';
            newRow.append(remainingAmountColumn);

            // Add the complete row to table body
            tableBody.append(newRow);
        }

        // Open the modal
        $('#multiReappropriateModal').modal('show');
    });


    // ========================================================================
    // SUBMIT RE-APPROPRIATION FORM
    // Validate and submit the form when user clicks submit button
    // ========================================================================
    $('#multiReappropriateForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        // ====================================================================
        // STEP 1: VALIDATE ALL TRANSFER AMOUNTS
        // ====================================================================

        var isFormValid = true;
        var validationErrors = [];

        // Get all transfer amount input fields from THIS form only
        var transferInputFields = $(this).find('.transfer-amount');

        // Check each transfer input field
        transferInputFields.each(function() {

            // Get current input field and its values
            var currentInput = $(this);
            var enteredAmount = parseFloat(currentInput.val()) || 0;
            var maximumAllowed = parseFloat(currentInput.attr('data-available'));
            var itemName = currentInput.attr('data-item-title');

            // Validation Rule 1: Check if user entered an amount
            if (enteredAmount <= 0 || !currentInput.val()) {
                isFormValid = false;
                validationErrors.push('Please enter transfer amount for: ' + itemName);
                currentInput.addClass('border-danger'); // Red border to show error
            }
            // Validation Rule 2: Check if amount is not more than available
            else if (enteredAmount > maximumAllowed) {
                isFormValid = false;
                validationErrors.push('Transfer amount exceeds available for: ' + itemName);
                currentInput.addClass('border-danger'); // Red border to show error
            }
            // No errors - remove red border if it exists
            else {
                currentInput.removeClass('border-danger');
            }
        });

        // If validation failed, show error messages and stop
        if (!isFormValid) {
            showToast('error', validationErrors.join('<br>'), 'Validation Error');
            return false; // Stop form submission
        }

        // ====================================================================
        // STEP 2: SHOW LOADING STATE
        // ====================================================================

        var submitButton = $(this).find('button[type="submit"]');
        var originalButtonText = submitButton.html();

        // Change button to loading state
        submitButton.html('<i class="fa fa-spinner fa-spin"></i> Processing...');
        submitButton.prop('disabled', true); // Disable to prevent double submission

        // ====================================================================
        // STEP 3: PREPARE FORM DATA
        // ====================================================================

        var formData = $(this).serialize(); // Convert form to query string

        // ====================================================================
        // STEP 4: SEND DATA TO SERVER
        // ====================================================================

        $.post('{{ url("/budgets/reappropriate/cross") }}', formData)
            .done(function(response) {
                // SUCCESS: Server processed the request successfully

                // Close the modal
                $('#multiReappropriateModal').modal('hide');

                // Show success message to user
                showToast('success', response.message, 'Success');

                // Reload page after 1.5 seconds to show updated data
                setTimeout(function() {
                    location.reload();
                }, 1500);
            })
            .fail(function(xhr) {
                // ERROR: Something went wrong

                // Reset button to original state
                submitButton.html(originalButtonText);
                submitButton.prop('disabled', false);

                // Get error message from server or use default
                var errorMessage = 'Something went wrong. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                // Show error message to user
                showToast('error', errorMessage, 'Error');
            });
    });

    // ============================================================================
    // ===               REAPPROPRIATION MODAL & FORM - END                     ===
    // ============================================================================






    // ============================================================================
    // ===                    FILTER WORKING - START                            ===
    // ============================================================================
    // Purpose: Filter budget list by academic year, department, and category
    // ============================================================================

    // --- APPLY FILTERS FUNCTION ---
    // Send filter values to backend and update budget list
    function applyFilters() {

        // Step 1: Get all filter values from dropdowns
        var yearId = $('#academicYearFilter').val();
        var deptId = $('#departmentFilter').val();
        var categoryId = $('#categoryFilter').val();

        // Step 2: Show loading spinner while fetching data
        $('#budgetsContainer').html(
            '<div class="text-center py-5">' +
                '<i class="fa fa-spinner fa-spin fa-3x text-muted"></i>' +
                '<p class="mt-3">Loading budgets...</p>' +
            '</div>'
        );

        // Step 3: Send AJAX request to backend with filters
        $.ajax({
            url: @json(url('/budgets/filter')),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                year_id: yearId,
                dept_id: deptId,
                category_id: categoryId
            },
            success: function(html) {
                // Success: Replace container with filtered budgets HTML
                $('#budgetsContainer').html(html);
            },
            error: function() {
                // Error: Show error message
                $('#budgetsContainer').html(
                    '<div class="text-center text-danger py-5">' +
                        '<i class="fa fa-exclamation-triangle fa-3x mb-3"></i>' +
                        '<p>Failed to load budgets. Please try again.</p>' +
                    '</div>'
                );
                showToast('error', 'Failed to fetch budgets. Please try again.', 'Error');
            }
        });
    }

    // --- ACADEMIC YEAR FILTER CHANGE ---
    $('#academicYearFilter').on('change', function() {
        applyFilters(); // Call filter function
    });

    // --- DEPARTMENT FILTER CHANGE ---
    $('#departmentFilter').on('change', function() {
        applyFilters(); // Call filter function
    });

    // --- BUDGET HEAD CATEGORY FILTER CHANGE ---
    $('#categoryFilter').on('change', function() {
        applyFilters(); // Call filter function
    });

    // --- RESET FILTERS BUTTON ---
    $('#clearFiltersBtn').on('click', function() {
        // Reset all dropdowns to default
        $('#academicYearFilter').prop('selectedIndex', 1); // First academic year (latest)
        $('#departmentFilter').prop('selectedIndex', 0);   // All Departments
        $('#categoryFilter').prop('selectedIndex', 0);     // All Budget Heads

        // Reload page to show all buttons like on initial page load
        location.reload();
    });

    // ============================================================================
    // ===                     FILTER WORKING - END                             ===
    // ============================================================================

        // Note: Do not auto-apply filters on initial load to avoid flicker.

        // Check if any items are selected and show/hide floating action bar
        function checkSelectedItems() {
            let selectedCount = $('input.item-checkbox:checked:not(:disabled)').length;

            // Update count display
            $('#selectedItemsCountDisplay').text(selectedCount);

            if (selectedCount > 0) {
                $('#floatingActionBar').removeClass('d-none').show();
            } else {
                $('#floatingActionBar').hide().addClass('d-none');
            }
        }

        // Select All Button Click - anywhere on button
        $(document).on('click', '.select-all-btn', function(){
            let budgetId = $(this).data('budget');
            let checkbox = $(this).find('.select-all');
            let isChecked = !checkbox.is(':checked');

            checkbox.prop('checked', isChecked);
            $('.budget-' + budgetId + ':not(:disabled)').prop('checked', isChecked);
            checkSelectedItems();
        });

        // Individual checkbox change
        $(document).on('change', '.item-checkbox', function(){
            checkSelectedItems();
        });

        // Close Action Bar - Uncheck all and hide
        $('#closeActionBar').on('click', function() {
            // Uncheck all checkboxes
            $('input.item-checkbox:not(:disabled)').prop('checked', false);
            $('.select-all').prop('checked', false);

            // Hide the action bar
            $('#floatingActionBar').hide().addClass('d-none');
        });

        // Global Request Quotation Button
        $('#requestQuotationBtnGlobal').on('click', function() {
            let selectedItems = [];
            let budgetGroups = {};

            $('input.item-checkbox:checked:not(:disabled)').each(function(){
                let itemId = $(this).val();
                let budgetId = $(this).data('budget-id');

                // Get item details from row
                let row = $(this).closest('tr');
                let itemTitle = row.find('td:eq(1)').text().trim();
                let budgetQty = row.find('td:eq(2)').text().trim();
                let rate = row.find('td:eq(3)').text().trim();
                let amount = row.find('td:eq(4)').text().trim();
                let budgetType = row.find('td:eq(5)').text().trim();

                selectedItems.push({
                    id: itemId,
                    budgetId: budgetId,
                    title: itemTitle,
                    budgetQty: budgetQty,
                    rate: rate,
                    amount: amount,
                    budgetType: budgetType
                });

                // Group by budget
                if (!budgetGroups[budgetId]) {
                    budgetGroups[budgetId] = [];
                }
                budgetGroups[budgetId].push(itemId);
            });

            if (selectedItems.length === 0) {
                showToast('warning', 'Please select at least one item before requesting quotation.', 'No Items Selected');
                return;
            }

            // Set data for confirmation
            $('#quotationBudgetGroups').val(JSON.stringify(budgetGroups));

            // Populate modal table with selected items
            let tableHtml = '';
            selectedItems.forEach(function(item) {
                tableHtml += `
                    <tr>
                        <td>
                            <span class="clickable-item-title" style="cursor: pointer; color: #4562C3; text-decoration: underline;">
                                <i class="fa fa-chevron-down"></i> ${item.title}
                            </span>
                        </td>
                        <td class="text-center"><strong>${item.budgetQty}</strong></td>
                        <td class="text-center">
                            <input type="number"
                                class="form-control requested-qty"
                                data-item-id="${item.id}"
                                data-budget-id="${item.budgetId}"
                                value="${item.budgetQty}"
                                min="1"
                                required
                                style="width: 100px; margin: 0 auto;">
                        </td>
                    </tr>
                    <tr class="item-detail-row" style="display: none;">
                        <td colspan="3" class="bg-light p-3">
                            <table class="table table-sm table-bordered mb-0">
                                <tr>
                                    <th width="20%">Item ID:</th>
                                    <td>${item.id}</td>
                                    <th width="20%">Budget ID:</th>
                                    <td>${item.budgetId}</td>
                                </tr>
                                <tr>
                                    <th>Budget Qty:</th>
                                    <td>${item.budgetQty}</td>
                                    <th>Rate:</th>
                                    <td>${item.rate}</td>
                                </tr>
                                <tr>
                                    <th>Amount:</th>
                                    <td><strong>${item.amount}</strong></td>
                                    <th>Budget Type:</th>
                                    <td>${item.budgetType}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                `;
            });
            $('#selectedItemsList').html(tableHtml);

            // Show confirmation modal
            $('#confirmQuotationModal').modal('show');
        });

        // Confirm Quotation Request
        $('#confirmQuotationBtn').on('click', function() {
            // Validate all quantity inputs
            let isValid = true;
            $('.requested-qty').each(function() {
                if (!$(this).val() || $(this).val() < 1) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                showToast('error', 'Please enter valid quantity for all items', 'Validation Error');
                return;
            }

            let budgetGroups = JSON.parse($('#quotationBudgetGroups').val());

            // Collect quantities for each item
            let itemQuantities = {};
            $('.requested-qty').each(function() {
                let itemId = $(this).data('item-id');
                let quantity = $(this).val();
                itemQuantities[itemId] = quantity;
            });

            let confirmBtn = $(this);
            let originalText = confirmBtn.html();
            confirmBtn.html('<i class="fa fa-spinner fa-spin"></i> Sending...').prop('disabled', true);

            // Send requests for each budget group
            let promises = [];
            Object.keys(budgetGroups).forEach(function(budgetId) {
                promises.push(
                    $.post('{{ url("/budgets") }}/' + budgetId + '/request-quotation', {
                        _token: '{{ csrf_token() }}',
                        items: budgetGroups[budgetId],
                        quantities: itemQuantities
                    })
                );
            });

            Promise.all(promises).then(function() {
                $('#confirmQuotationModal').modal('hide');
                showToast('success', 'Quotation requests sent successfully!', 'Success');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            }).catch(function(xhr) {
                confirmBtn.html(originalText).prop('disabled', false);
                let errorMsg = xhr.responseJSON?.message ?? 'Error sending quotation request. Please try again.';
                showToast('error', errorMsg, 'Error');
            });
        });

        // Hide floating buttons after actions
        $(document).on('hidden.bs.modal', '#confirmQuotationModal, #multiReappropriateModal', function() {
            checkSelectedItems();
        });

        // Click on Item Title in Confirm Quotation Modal to Expand/Collapse Details
        $(document).on('click', '.clickable-item-title', function() {
            // Find the next row (detail row)
            var detailRow = $(this).closest('tr').next('.item-detail-row');

            // Toggle detail row visibility
            detailRow.slideToggle();

            // Change icon (down arrow = collapsed, up arrow = expanded)
            var icon = $(this).find('i');
            if (icon.hasClass('fa-chevron-down')) {
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            } else {
                icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            }
        });

    // Edit Quotation Request Modal
    $(document).on('click', '.editQuotationBtn', function() {
        var itemId = $(this).data('item-id');
        var budgetId = $(this).data('budget-id');
        var qrId = $(this).data('qr-id');
        var itemTitle = $(this).data('item-title');
        var currentQty = $(this).data('current-qty');
        var budgetQty = $(this).data('budget-qty');

        $('#editQrId').val(qrId);
        $('#editItemId').val(itemId);
        $('#editBudgetId').val(budgetId);
        $('#editItemTitle').val(itemTitle);
        $('#editBudgetQty').val(budgetQty);
        $('#editRequestedQty').val(currentQty);

        // Reset to New Revision tab
        $('#new-tab').tab('show');

        // Show modal
        $('#editQuotationModal').modal('show');
    });

    // Real-time validation for Requested Quantity
    $('#editRequestedQty').on('input', function() {
        var requestedQty = parseFloat($(this).val());
        var budgetQty = parseFloat($('#editBudgetQty').val());

        if (requestedQty > budgetQty) {
            $(this).addClass('is-invalid');
            showToast('error', 'Requested Quantity cannot exceed Budget Quantity (' + budgetQty + ')', 'Validation Error');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    // Load History when History Tab is clicked
    $('#history-tab').on('shown.bs.tab', function() {
        var qrId = $('#editQrId').val();

        if (!qrId) {
            $('#historyTableBody').html('<tr><td colspan="4" class="text-center text-danger py-3">No quotation request ID found</td></tr>');
            return;
        }

        // Show loading
        $('#historyTableBody').html('<tr><td colspan="4" class="text-center text-muted py-3"><i class="fa fa-spinner fa-spin"></i> Loading history...</td></tr>');

        // Load history via AJAX
        $.ajax({
            url: '/budgets/quotation-history/' + qrId,
            type: 'GET',
            success: function(response) {
                if (response.history && response.history.length > 0) {
                    var tableHtml = '';
                    response.history.forEach(function(item) {
                        // Format date and time
                        var dateTime = '-';
                        if (item.created_at) {
                            try {
                                var date = new Date(item.created_at);
                                dateTime = date.toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: '2-digit'
                                }) + ' ' + date.toLocaleTimeString('en-US', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true
                                });
                            } catch(e) {
                                dateTime = item.created_at;
                            }
                        }

                        tableHtml += '<tr>' +
                            '<td><strong>Version ' + (item.version_number || 0) + '</strong></td>' +
                            '<td class="text-center">' + (item.budget_quantity || 0) + '</td>' +
                            '<td class="text-center">' + (item.requested_quantity || 0) + '</td>' +
                            '<td class="text-center">' + dateTime + '</td>' +
                            '</tr>';
                    });
                    $('#historyTableBody').html(tableHtml);
                } else {
                    $('#historyTableBody').html('<tr><td colspan="4" class="text-center text-muted py-3">No history found</td></tr>');
                }
            },
            error: function(xhr) {
                var errorMsg = 'Error loading history';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#historyTableBody').html('<tr><td colspan="4" class="text-center text-danger py-3">' + errorMsg + '</td></tr>');
            }
        });
    });

    $('#editQuotationForm').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        var qrId = $('#editQrId').val();
        var budgetId = $('#editBudgetId').val();
        var requestedQty = parseFloat($('#editRequestedQty').val());
        var budgetQty = parseFloat($('#editBudgetQty').val());

        if (!requestedQty || requestedQty < 1) {
            showToast('error', 'Please enter a valid quantity (minimum 1).', 'Validation Error');
            return;
        }

        if (requestedQty > budgetQty) {
            showToast('error', 'Requested Quantity cannot exceed Budget Quantity (' + budgetQty + ')', 'Validation Error');
            return;
        }

        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Processing...').prop('disabled', true);

        $.ajax({
            url: '/budgets/' + budgetId + '/edit-quotation/' + qrId,
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#editQuotationModal').modal('hide');
                showToast('success', response.message || 'Quotation request updated successfully!', 'Success');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                submitBtn.html(originalText).prop('disabled', false);

                var errorMessage = 'Something went wrong. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                showToast('error', errorMessage, 'Error');
            }
        });
    });

$(document).on('submit', '#chatSendForm', function(e) {
    e.preventDefault();

    var form = $(this);
    var messageField = form.find('textarea[name="message_text"]');
    var message = $.trim(messageField.val());
    var responseBox = $('#chatSendResponse');
    var qrId = form.find('input[name="qr_id"]').val();

    if (!qrId) {
        responseBox.text('No request selected.').removeClass('text-success').addClass('text-danger');
        return;
    }

    if (message.length === 0) {
        responseBox.text('Please write a message.').removeClass('text-success').addClass('text-danger');
        return;
    }

    responseBox.text('Sending...').removeClass('text-danger').addClass('text-muted');

    $.ajax({
        url: '{{ route("sp.quotations.chat.store") }}',
        type: 'POST',
        data: {
            _token: chatCsrf,
            qr_id: qrId,
            sender_type: form.find('input[name="sender_type"]').val(),
            message_text: message
        },
        success: function(res) {
            var chatBox = $('#chatMessagesList');
            var safeMessage = $('<div>').text(res.chat.message_text || '').html();
            var isSelf = false;
            if (currentUsername && res.chat.entry_username) {
                isSelf = currentUsername.toLowerCase() === res.chat.entry_username.toLowerCase();
            } else if (res.chat.sender_type) {
                isSelf = res.chat.sender_type.toLowerCase() === 'procurement';
            }
            var selfClass = isSelf ? 'text-right' : 'text-left';
            var newHtml = '<div class="list-group-item ' + selfClass + '">';
            newHtml += '<div>' + safeMessage + '</div>';
            if (res.chat.created_at) {
                newHtml += '<div class="small text-muted mt-1">' + res.chat.created_at + '</div>';
            }
            newHtml += '</div>';
            chatBox.append(newHtml);
            messageField.val('');
            responseBox.text('Message sent.').removeClass('text-danger').addClass('text-success');
            chatBox.scrollTop(chatBox[0].scrollHeight);
        },
        error: function() {
            responseBox.text('Unable to send message.').removeClass('text-success').addClass('text-danger');
        }
    });
});

    </script>
    @stop


