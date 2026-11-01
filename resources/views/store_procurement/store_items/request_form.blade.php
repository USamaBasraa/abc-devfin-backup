@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />

<style>
    .request-card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,.08);
        background: #fff;
        border: none;
        overflow: hidden;
        max-width: 900px;
        margin: 0 auto;
    }
    .request-header {
        background: #17675F !important;
        background-image: none !important;
        color: #fff;
        padding: 1.1rem 1.5rem;
        font-size: 18px;
        font-weight: 600;
    }
    .btn-primary { background: #17675F !important; border-color: #17675F !important; border-radius: 999px; }
    a.text-decoration-none { color: #17675F; }
    .stock-pill {
        display: inline-block;
        padding: .25rem .75rem;
        border-radius: 50px;
        background: #e8f5e9;
        color: #2e7d32;
        font-weight: 600;
        font-size: 13px;
    }
</style>

<div class="container-fluid py-4">
    <div class="mb-3">
                    <a href="{{ route('store-items.requests') }}" class="text-decoration-none">
                        <i class="fa fa-arrow-left mr-1"></i> Back to Requests
                    </a>
    </div>

    <div class="request-card">
        <div class="request-header">
            <i class="fa fa-handshake-o mr-2"></i>
            Store Item Handover Request
        </div>

        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <p class="text-muted mb-4">
                Is form se store item kisi department ko handover / issue ke liye request ki jaati hai.
                Quantity record inventory mein track hoti rahegi.
            </p>

            <form method="POST" action="{{ route('store-items.request.store') }}" id="storeHandoverForm">
                @csrf

                <div class="form-group">
                    <label class="font-weight-bold">Store Item <span class="text-danger">*</span></label>
                    <select name="store_item_id" id="store_item_id" class="form-control" required>
                        <option value="">-- Select Store Item --</option>
                        @foreach ($inventory as $item)
                            @php
                                $available = (float) ($item->available_qty ?? 0);
                                $selected = old('store_item_id', optional($selectedItem)->store_item_id) == $item->store_item_id;
                            @endphp
                            <option
                                value="{{ $item->store_item_id }}"
                                data-available="{{ $available }}"
                                data-unit="{{ $item->unit ?? '' }}"
                                data-title="{{ $item->item_title ?? '' }}"
                                {{ $selected ? 'selected' : '' }}
                                {{ $available <= 0 ? 'disabled' : '' }}
                            >
                                {{ $item->item_title ?? '-' }}
                                (Available: {{ number_format($available, 2) }}{{ !empty($item->unit) ? ' ' . $item->unit : '' }})
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">
                        Available stock:
                        <span class="stock-pill" id="availableStockLabel">
                            @if($selectedItem)
                                {{ number_format((float) ($selectedItem->available_qty ?? 0), 2) }}
                                {{ $selectedItem->unit ?? '' }}
                            @else
                                —
                            @endif
                        </span>
                    </small>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Requested Quantity <span class="text-danger">*</span></label>
                        <input type="number"
                               step="0.01"
                               min="0.01"
                               name="requested_qty"
                               id="requested_qty"
                               class="form-control"
                               value="{{ old('requested_qty') }}"
                               required>
                    </div>
                    <div class="form-group col-md-8">
                        <label class="font-weight-bold">Requesting Department <span class="text-danger">*</span></label>
                        <select name="request_dept_id" class="form-control" required>
                            <option value="">-- Select Department --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->dept_id }}"
                                    {{ (string) old('request_dept_id', $defaultDeptId) === (string) $dept->dept_id ? 'selected' : '' }}>
                                    {{ $dept->dept_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Requested By <span class="text-danger">*</span></label>
                    <input type="text"
                           name="requested_by"
                           class="form-control"
                           value="{{ old('requested_by', $defaultRequestedBy) }}"
                           maxlength="150"
                           required>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Remarks</label>
                    <textarea name="request_remarks"
                              class="form-control"
                              rows="3"
                              maxlength="2000"
                              placeholder="Purpose / notes for handover">{{ old('request_remarks') }}</textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="{{ route('store-items.requests') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa fa-paper-plane mr-1"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(function () {
    function syncAvailable() {
        var opt = $('#store_item_id option:selected');
        var available = parseFloat(opt.data('available'));
        var unit = opt.data('unit') || '';
        if (!opt.val() || isNaN(available)) {
            $('#availableStockLabel').text('—');
            $('#requested_qty').attr('max', '');
            return;
        }
        $('#availableStockLabel').text(available.toFixed(2) + (unit ? (' ' + unit) : ''));
        $('#requested_qty').attr('max', available);
    }

    $('#store_item_id').on('change', syncAvailable);
    syncAvailable();

    $('#storeHandoverForm').on('submit', function (e) {
        var opt = $('#store_item_id option:selected');
        var available = parseFloat(opt.data('available')) || 0;
        var qty = parseFloat($('#requested_qty').val()) || 0;
        if (qty > available) {
            e.preventDefault();
            alert('Requested quantity cannot exceed available stock (' + available + ').');
        }
    });
});
</script>
@endsection
