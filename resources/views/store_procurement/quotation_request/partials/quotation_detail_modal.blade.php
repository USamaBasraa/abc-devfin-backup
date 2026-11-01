<!-- Modern Tabbed Modal Content -->
<style>
    .nav-tabs.modern-tabs {
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 0;
    }
    
    .nav-tabs.modern-tabs .nav-link {
        border: none !important;
        border-bottom: 3px solid transparent !important;
        color: #6c757d !important;
        font-weight: 500;
        padding: 12px 20px;
        margin-bottom: -2px;
        transition: all 0.3s ease;
        background-color: transparent !important;
    }
    
    .nav-tabs.modern-tabs .nav-link:hover {
        border-bottom-color: #3e5871;
        color: #3e5871 !important;
        background-color: rgba(62, 88, 113, 0.05) !important;
    }
    
    .nav-tabs.modern-tabs .nav-link.active {
        border-bottom-color: #3e5871;
        color: #3e5871 !important;
        background-color: rgba(62, 88, 113, 0.1) !important;
        font-weight: 600;
    }
    
    .nav-tabs.modern-tabs .nav-link i {
        margin-right: 6px;
        font-size: 14px;
    }
    
    .spinner-border.d-none {
        display: none !important;
    }
    
    .qr-chat-box {
        max-height: 320px;
        overflow-y: auto;
        background-color: #f7f9fc;
        border-radius: 12px;
        padding: 15px;
    }
    
    .qr-chat-entry {
        margin-bottom: 12px;
    }
    
    .qr-chat-bubble {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 12px;
        max-width: 80%;
        text-align: left;
        word-break: break-word;
    }
    
    .qr-chat-bubble-self {
        background-color: #4562C3;
        color: #fff;
    }
    
    .qr-chat-bubble-other {
        background-color: #e2e8f0;
        color: #252525;
    }
    
    /* Keep main modal stable: each tab gets its own vertical scroll */
    #quotationTabContent > .tab-pane {
        max-height: 600px;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 6px;
    }

    #quotationTabContent > .tab-pane::-webkit-scrollbar {
        width: 8px;
    }

    #quotationTabContent > .tab-pane::-webkit-scrollbar-thumb {
        background: rgba(62, 88, 113, 0.35);
        border-radius: 8px;
    }

    #quotationTabContent > .tab-pane::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 8px;
    }
</style>
    
@php
    $poGenerated = !is_null($req->po_id);
    $budgetItem = $req->budgetItem;

    $itemTitle = '-';
    if ($budgetItem && isset($budgetItem->item_title) && trim($budgetItem->item_title) !== '') {
        $itemTitle = $budgetItem->item_title;
    } elseif (isset($req->item_name) && trim($req->item_name) !== '') {
        $itemTitle = $req->item_name;
    }

    $brand = '-';
    if ($budgetItem && isset($budgetItem->brand) && trim($budgetItem->brand) !== '') {
        $brand = $budgetItem->brand;
    } elseif (isset($req->brand) && trim($req->brand) !== '') {
        $brand = $req->brand;
    }

    $model = '-';
    if ($budgetItem && isset($budgetItem->model) && trim($budgetItem->model) !== '') {
        $model = $budgetItem->model;
    } elseif (isset($req->model) && trim($req->model) !== '') {
        $model = $req->model;
    } elseif (isset($req->model_no) && trim($req->model_no) !== '') {
        $model = $req->model_no;
    }
// dd($budgetItem);
    $specifications = '-';
    if ($budgetItem && isset($budgetItem->specifications) && trim($budgetItem->specifications) !== '') {
        $specifications = $budgetItem->specifications;
    } elseif (isset($req->specifications_prnt_dept) && trim($req->specifications_prnt_dept) !== '') {
        $specifications = $req->specifications_prnt_dept;
    }

    $parentRemarks = '-';
    if (isset($req->parent_department_remarks) && trim($req->parent_department_remarks) !== '') {
        $parentRemarks = $req->parent_department_remarks;
    } elseif ($budgetItem && isset($budgetItem->parent_remarks) && trim($budgetItem->parent_remarks) !== '') {
        $parentRemarks = $budgetItem->parent_remarks;
    }

    $departmentName = '-';
    if ($req->budgetHead && $req->budgetHead->department && isset($req->budgetHead->department->dept_name) && trim($req->budgetHead->department->dept_name) !== '') {
        $departmentName = $req->budgetHead->department->dept_name;
    } elseif ($req->requestDepartment && isset($req->requestDepartment->dept_name) && trim($req->requestDepartment->dept_name) !== '') {
        $departmentName = $req->requestDepartment->dept_name;
    }

    $purchaseModeValue = '-';
    if ($budgetItem && isset($budgetItem->purchase_mode) && trim($budgetItem->purchase_mode) !== '') {
        $purchaseModeValue = $budgetItem->purchase_mode;
    } elseif (isset($req->purchase_mode) && trim($req->purchase_mode) !== '') {
        $purchaseModeValue = $req->purchase_mode;
    }

    $quantityValue = null;
    if ($budgetItem && isset($budgetItem->quantity)) {
        $quantityValue = $budgetItem->quantity;
    } elseif (isset($req->requested_quantity)) {
        $quantityValue = $req->requested_quantity;
    } elseif (isset($req->budget_quantity)) {
        $quantityValue = $req->budget_quantity;
    }

    $unitRateValue = null;
    if ($budgetItem && isset($budgetItem->rate)) {
        $unitRateValue = $budgetItem->rate;
    }

    $quantityDisplay = $quantityValue !== null ? $quantityValue : '-';
    $unitRateDisplay = $unitRateValue !== null ? number_format($unitRateValue) . ' PKR' : '-';

    $estimatedTotalValue = null;
    if (is_numeric($quantityValue) && is_numeric($unitRateValue)) {
        $estimatedTotalValue = number_format($unitRateValue * $quantityValue);
    }
@endphp
    <ul class="nav nav-tabs modern-tabs" id="quotationTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab">
                <i class="fa fa-info-circle"></i> Details
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab">
                <i class="fa fa-envelope"></i> Email Vendors
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="submit-tab" data-toggle="tab" href="#submit" role="tab">
                <i class="fa {{ $poGenerated ? 'fa-lock' : 'fa-paper-plane' }}"></i> Submit New Quotation
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="chat-tab" data-toggle="tab" href="#chat" role="tab">
                <i class="fa fa-comments"></i> Request Chat
                @if(isset($unreadCount) && $unreadCount > 0)
                    <span class="badge badge-danger ml-1" id="chatUnreadBadge{{ $req->qr_id }}">
                        <i class="fa fa-bell"></i> {{ $unreadCount }}
                    </span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="submitted-tab" data-toggle="tab" href="#submitted" role="tab">
                <i class="fa fa-list-alt"></i> View Submitted Quotations
            </a>
        </li>
    </ul>
    
    <div class="tab-content" id="quotationTabContent">
        <!-- Details Tab -->
        <div class="tab-pane fade show active" id="details" role="tabpanel">
            <div class="mt-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="text-primary mr-3" style="font-size: 20px;">
                                        <i class="fa fa-shopping-cart"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Item Title</small>
                                        <strong class="d-block">{{ $itemTitle }}</strong>
                                    </div>
                                </div>
                            </div>
    
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="text-info mr-3" style="font-size: 20px;">
                                        <i class="fa fa-list-alt"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Budget Head</small>
                                        <strong class="d-block">{{ $req->budgetHead->head_title ?? '-' }}</strong>
                                    </div>
                                </div>
                            </div>
    
    
                           <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="text-primary mr-3" style="font-size: 20px;">
                                        <i class="fa fa-shopping-cart"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Brand</small>
                                        <strong class="d-block">{{ $brand }}</strong>
                                    </div>
                                </div>
                            </div>
    
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="text-info mr-3" style="font-size: 20px;">
                                        <i class="fa fa-list-alt"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Model</small>
                                        <strong class="d-block">{{ $model }}</strong>
                                    </div>
                                </div>
                            </div>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="text-primary mr-3" style="font-size: 20px;">
                                        <i class="fa fa-shopping-cart"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Item Specification</small>
                                        <strong class="d-block">{{ $specifications }}</strong>
                                    </div>
                                </div>
                            </div>
    
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="text-info mr-3" style="font-size: 20px;">
                                        <i class="fa fa-list-alt"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Supervising Department Remarks</small>
                                        <strong class="d-block">{{ $parentRemarks ?? '-' }}</strong>
                                    </div>
                                </div>
                            </div>
    

    
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="text-success mr-3" style="font-size: 20px;">
                                        <i class="fa fa-building"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Department</small>
                                        <strong class="d-block">{{ $departmentName }}</strong>
                                    </div>
                                </div>
                            </div>
    
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="text-warning mr-3" style="font-size: 20px;">
                                        <i class="fa fa-credit-card"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Purchase Mode</small>
                                        <strong class="d-block">{{ ucfirst($purchaseModeValue) }}</strong>
                                    </div>
                                </div>
                            </div>
    
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="text-danger mr-3" style="font-size: 20px;">
                                        <i class="fa fa-cubes"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Quantity</small>
                                        <strong class="d-block">{{ $quantityDisplay }}</strong>
                                    </div>
                                </div>
                            </div>
    
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="text-primary mr-3" style="font-size: 20px;">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Unit Rate</small>
                                        <strong class="d-block">{{ $unitRateDisplay }}</strong>
                                    </div>
                                </div>
                            </div>
    
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="text-success mr-3" style="font-size: 20px;">
                                        <i class="fa fa-calculator"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Estimated Total</small>
                                        <strong class="d-block text-success">
                                            {{ $estimatedTotalValue ? $estimatedTotalValue . ' PKR' : '-' }}
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        @php
                            $lowestQuote = null;
                            $quoteCurrency = 'PKR';
                            if ($req->vendorQuotations && $req->vendorQuotations->count() > 0) {
                                $lowestQuote = $req->vendorQuotations->min('price');
                                $quoteCurrency = $req->vendorQuotations->first()->currency ?? 'PKR';
                            }
    
                            $purchaseOrder = \App\Models\StoreProcurement\PurchaseOrder::where('qr_id', $req->qr_id)->first();
                        @endphp
    
                        @if($lowestQuote || $purchaseOrder)
                        <hr>
                        <div class="row">
                            @if($lowestQuote)
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="text-info mr-3" style="font-size: 20px;">
                                        <i class="fa fa-tag"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Lowest Vendor Quote</small>
                                        <strong class="d-block text-info">{{ number_format($lowestQuote) }} {{ $quoteCurrency }}</strong>
                                    </div>
                                </div>
                            </div>
                            @endif
    
                            @if($purchaseOrder && $purchaseOrder->total_price)
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="text-success mr-3" style="font-size: 20px;">
                                        <i class="fa fa-file-text"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Purchase Order Price</small>
                                        <strong class="d-block text-success">{{ number_format($purchaseOrder->total_price) }} {{ $purchaseOrder->currency ?? 'PKR' }}</strong>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
    
                        @php
                            $remarks = $req->budgetItem->remarks ?? $req->remarks;
                        @endphp

                        @if($remarks)
                        <hr>
                        <div class="d-flex align-items-start">
                            <div class="text-secondary mr-3" style="font-size: 20px;">
                                <i class="fa fa-comment"></i>
                            </div>
                            <div style="flex: 1;">
                                <small class="text-muted d-block">Remarks</small>
                                <p class="mb-0">{{ $remarks }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Email Vendors Tab -->
        <div class="tab-pane fade" id="email" role="tabpanel">
            <div class="mt-3">
                @if(!$poGenerated)
                    {{-- Vendor Selection with Dropdown + Chips --}}
                    <div class="mb-3">
                        <label><strong><i class="fa fa-users text-primary"></i> Select Vendors to Email</strong></label>
                        <select class="form-control vendor-email-select" data-request-id="{{ $req->qr_id }}">
                            <option value="">-- Select a Vendor --</option>
                            @forelse(($vendors ?? collect()) as $vendor)
                                @php
                                    $vendorId = $vendor->vendor_id ?? $vendor->VENDOR_ID ?? null;
                                    $vendorName = trim((string) ($vendor->vendor_name ?? $vendor->VENDOR_NAME ?? ''));
                                    $vendorEmail = trim((string) ($vendor->vendor_email ?? $vendor->VENDOR_EMAIL ?? ''));
                                    $hasEmail = $vendorEmail !== '';
                                @endphp
                                @if($vendorId !== null && $vendorId !== '')
                                    <option value="{{ $vendorId }}"
                                            data-name="{{ $vendorName !== '' ? $vendorName : ('Vendor #' . $vendorId) }}"
                                            data-email="{{ $vendorEmail }}"
                                            data-no-email="{{ $hasEmail ? '0' : '1' }}"
                                            {{ $hasEmail ? '' : 'disabled' }}>
                                        {{ $vendorName !== '' ? $vendorName : ('Vendor #' . $vendorId) }}{{ $hasEmail ? '' : ' (No Email - Update Vendor)' }}
                                    </option>
                                @endif
                            @empty
                            @endforelse
                        </select>
                        @if(($vendors ?? collect())->isEmpty())
                            <div class="alert alert-warning mt-2 mb-0">
                                <i class="fa fa-exclamation-triangle"></i> No vendors found. Please add vendors in Vendor Create first.
                            </div>
                        @endif
                        <small class="text-muted d-block mt-1">
                            Vendors without email are disabled. Update email in Vendor Create before sending RFQ.
                        </small>

                        {{-- Selected Vendors Display as Chips --}}
                        <div class="selected-vendors-area mt-2" id="selectedVendorsArea{{ $req->qr_id }}">
                            {{-- Chips will be dynamically added here --}}
                        </div>
                    </div>
                    <div class="text-center my-3">
                        <button class="btn btn-primary btn-sm sendEmailBtn" data-request-id="{{ $req->qr_id }}">
                            <i class="fa fa-envelope"></i> Send Email to Selected Vendors
                        </button>
                    </div>
                @else
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="fa fa-info-circle mr-2"></i>
                        <div>
                            <strong>Purchase Order Generated:</strong> Vendor emails are disabled once a PO is created.
                        </div>
                    </div>
                @endif
    
                <hr>
    
                {{-- Email History Section (Dynamic) --}}
                <div id="emailHistorySection">
                    @php
                        $emailLogs = \App\Models\StoreProcurement\VendorEmailLog::where('ve_qr_id', $req->qr_id)
                            ->with('vendor')
                            ->orderBy('ve_sent_at', 'desc')
                            ->get();
                        $emailPo = \App\Models\StoreProcurement\PurchaseOrder::where('qr_id', $req->qr_id)->first();
                    @endphp
    
                    @include('store_procurement.quotation_request.partials.email_history_table', [
                        'emailLogs' => $emailLogs,
                        'emailPo' => $emailPo,
                        'rfqNumber' => $rfqNumber ?? null,
                        'rfqPdfRoute' => ($req->request_type ?? '') === 'store_items' ? 'store-items.rfq-pdf' : null,
                    ])
                </div>
            </div>
        </div>
    
        <!-- Submit Quotation Tab -->
        <div class="tab-pane fade" id="submit" role="tabpanel">
            <div class="mt-3">
            @if(!$poGenerated)
                {{-- Vendor Selection --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label><strong><i class="fa fa-user text-primary"></i> Select Vendor</strong> <span class="text-danger">*</span></label>
                        <select class="form-control selectVendor select2-filter"
                                data-request-id="{{ $req->qr_id }}"
                                data-item-unit="{{ $req->budgetItem->unit ?? '' }}"
                                data-item-app-unit="{{ $req->budgetItem->app_unit ?? '' }}"
                                {{ $emailedVendors->isEmpty() ? 'disabled' : '' }}>
                            <option value="">{{ $emailedVendors->isEmpty() ? '-- No emailed vendors available --' : '-- Select Vendor --' }}</option>
                            @foreach($emailedVendors as $vendor)
                                @php
                                    $emailedVendorId = $vendor->vendor_id ?? $vendor->VENDOR_ID ?? null;
                                    $emailedVendorName = trim((string) ($vendor->vendor_name ?? $vendor->VENDOR_NAME ?? ''));
                                @endphp
                                @if($emailedVendorId !== null && $emailedVendorId !== '')
                                    <option value="{{ $emailedVendorId }}">{{ $emailedVendorName !== '' ? $emailedVendorName : ('Vendor #' . $emailedVendorId) }}</option>
                                @endif
                            @endforeach
                        </select>
                        @if($emailedVendors->isEmpty())
                            <div class="alert alert-warning mt-2 mb-0">
                                <i class="fa fa-exclamation-triangle"></i> Please send the RFQ email to vendors before submitting quotations.
                            </div>
                        @endif
                    </div>
                </div>
    
                {{-- Excel Upload --}}
                <div class="row mb-3" id="excelUploadSection{{ $req->qr_id }}" style="display: none;">
                    <div class="col-md-12">
                        <label><strong><i class="fa fa-file-excel-o text-success"></i> Upload Vendor Excel</strong></label>
                        <div class="mb-2">
                            <input type="file" class="vendorExcelFile" id="vendorExcelFile{{ $req->qr_id }}" accept=".xlsx,.xls" data-request-id="{{ $req->qr_id }}" style="display: none;">
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('vendorExcelFile{{ $req->qr_id }}').click()">
                                <i class="fa fa-file-excel-o"></i> Choose Excel File
                            </button>
                            <button type="button" class="btn btn-outline-secondary ml-2" onclick="clearFileInput('{{ $req->qr_id }}')">
                                <i class="fa fa-times"></i> Clear
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="fa fa-download"></i>
                            <a href="javascript:void(0);" class="downloadTemplateBtn text-primary">
                                Download Template
                            </a>
                        </small>
                    </div>
                </div>
    
                <!-- Area to show existing quotation OR the form -->
                <div class="quotation-area" id="quotationArea{{ $req->qr_id }}"></div>
            @else
                <div class="alert alert-info d-flex align-items-center">
                    <i class="fa fa-info-circle mr-2"></i>
                    <div>
                        <strong>Purchase Order Generated:</strong> New quotations cannot be submitted after a PO has been created.
                    </div>
                </div>
            @endif
            </div>
        </div>
    
        <!-- Chat Tab -->
        <div class="tab-pane fade" id="chat" role="tabpanel">
            <div class="mt-3">
                @php
                    $currentUsername = strtolower(auth()->user()->username ?? auth()->user()->EMP_AD_USERNAME ?? auth()->user()->name ?? '');
                @endphp
                <div class="qr-chat-box mb-3" id="chatBox{{ $req->qr_id }}">
                    @forelse($req->chatMessages as $chat)
                        @php
                            $entryUser = strtolower($chat->entry_username ?? '');
                            $senderType = strtolower($chat->sender_type ?? '');
                            $isSelf = false;
                            if (!empty($currentUsername) && !empty($entryUser)) {
                                $isSelf = $entryUser === $currentUsername;
                            } else {
                                $isSelf = $senderType === 'procurement';
                            }
                            $timestamp = '';
                            if (!empty($chat->created_at)) {
                                $timestamp = date('d-M-Y h:i A', strtotime($chat->created_at));
                            }
                        @endphp
                        <div class="qr-chat-entry {{ $isSelf ? 'text-right' : '' }}">
                            <div class="qr-chat-bubble {{ $isSelf ? 'qr-chat-bubble-self' : 'qr-chat-bubble-other' }}">
                                {{ $chat->message_text }}
                            </div>
                            @if($timestamp)
                            <div class="small text-muted mt-1">
                                {{ $timestamp }}
                            </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">No conversation yet. Start messaging below.</p>
                    @endforelse
                </div>
    
                <form class="chatForm" data-request-id="{{ $req->qr_id }}">
                    @csrf
                    <input type="hidden" name="qr_id" value="{{ $req->qr_id }}">
                    <input type="hidden" name="sender_type" value="procurement">
                    <div class="form-group">
                        <label><strong><i class="fa fa-commenting text-primary"></i> Write Message</strong></label>
                        <textarea name="message_text" class="form-control" rows="2" maxlength="1000" required></textarea>
                    </div>
                    <button class="btn btn-primary chatSubmitBtn" type="submit">
                        <i class="fa fa-paper-plane"></i> Send Message
                    </button>
                    <div class="chatResponse mt-2 text-muted small"></div>
                </form>
            </div>
        </div>
    
        <!-- Submitted Quotations Tab -->
        <div class="tab-pane fade" id="submitted" role="tabpanel">
            <div class="mt-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="fa fa-list-alt text-primary"></i>
                        Submitted Quotations
                    </h5>
                    <span class="badge badge-primary" id="submittedCount{{ $req->qr_id }}">0</span>
                </div>
    
                <div id="submittedQuotationsList{{ $req->qr_id }}">
                    <!-- Submitted quotations will be loaded here -->
                    <div class="text-center p-4">
                        <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                        <p class="text-muted mt-2">Loading submitted quotations...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    

