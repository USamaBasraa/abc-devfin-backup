@php
    use Illuminate\Support\Str;
    $vendorCards = $vendorCards ?? [];
@endphp

@once
<style>
    @keyframes blinkPrimaryLightBackground {
        0% {
            background-color: #4562C3;
            border-color: #4562C3;
        }
        100% {
            background-color: #FFFFFF;
            border-color: #4562C3;
        }
    }

    @keyframes blinkPrimaryLightIcon {
        0% {
            color: #FFFFFF;
        }
        100% {
            color: #4562C3;
        }
    }

    .blinking-circle {
        animation: blinkPrimaryLightBackground 6.5s ease-in-out infinite alternate;
    }

    .blinking-circle i {
        animation: blinkPrimaryLightIcon 6.5s ease-in-out infinite alternate;
    }

    .comparative-report-layout {
        gap: 12px;
    }

    .comparative-report-info-panel {
        min-width: 300px;
        max-width: 300px;
    }

    .comparative-report-table {
        width: 100%;
        table-layout: fixed;
    }

    .comparative-report-table thead th {
        white-space: normal;
        word-break: keep-all;
        line-height: 1.2;
        font-size: 0.72rem;
        letter-spacing: 0.01em;
        padding-top: 0.35rem;
        padding-bottom: 0.35rem;
    }

    .comparative-report-table th,
    .comparative-report-table td {
        padding: 0.5rem 0.35rem;
        font-size: 0.82rem;
        white-space: normal;
        word-break: break-word;
        vertical-align: middle;
    }

    .comparative-report-table .progress {
        margin-top: 0.3rem;
    }

    @media (max-width: 1400px) {
        .comparative-report-info-panel {
            min-width: 260px;
            max-width: 260px;
        }

        .comparative-report-table th,
        .comparative-report-table td {
            font-size: 0.76rem;
            padding: 0.45rem 0.3rem;
        }

        .comparative-report-table thead th {
            font-size: 0.68rem;
        }
    }
</style>
@endonce

<div class="container-fluid py-3">
    <div class="d-flex flex-column flex-xl-row align-items-stretch comparative-report-layout">
        <div class="flex-shrink-0 d-flex comparative-report-info-panel">
            <div class="card border-0 shadow-sm w-100 h-100" style="border-radius: 18px;">
                <div class="card-header border-0" style="background-color: #4562C3; color: #FFFFFF; border-top-left-radius: 18px; border-top-right-radius: 18px;">
                    <h5 class="mb-0 font-weight-bold">Item Information</h5>
                    <small class="text-light-50 d-block" style="opacity: 0.8;">Department Budget (Estimated)</small>
                </div>
                <div class="card-body" style="background-color: #F1F1F1; border-bottom-left-radius: 18px; border-bottom-right-radius: 18px;">
                    @php
                        $itemDetails = [
                            ['label' => 'Item Title', 'value' => $requestSummary['item_title'] ?? '-'],
                            ['label' => 'Brand', 'value' => $requestSummary['brand'] ?? '-'],
                            ['label' => 'Model', 'value' => $requestSummary['model'] ?? '-'],
                            ['label' => 'Category', 'value' => $requestSummary['category'] ?? '-'],
                            ['label' => 'Quantity', 'value' => isset($requestSummary['quantity']) && $requestSummary['quantity'] !== null ? number_format((float) $requestSummary['quantity'], 2) : '-'],
                            ['label' => 'Budget Type', 'value' => !empty($requestSummary['budget_type']) ? ucfirst($requestSummary['budget_type']) : '-'],
                            ['label' => 'Purchase Mode', 'value' => !empty($requestSummary['purchase_mode']) ? ucfirst($requestSummary['purchase_mode']) : '-'],
                            ['label' => 'Rate (Budgeted)', 'value' => $requestSummary['rate'] !== null ? number_format($requestSummary['rate'], 2) : '-'],
                            ['label' => 'Amount (Budgeted)', 'value' => $requestSummary['amount'] !== null ? number_format($requestSummary['amount'], 2) : '-'],
                        ];
                    @endphp

                    @foreach($itemDetails as $detail)
                        <div class="mb-3">
                            <small class="text-muted d-block">{{ $detail['label'] }}</small>
                            <span class="font-weight-bold" style="color: #252525;">{{ $detail['value'] }}</span>
                        </div>
                    @endforeach

                    @if(!empty($requestSummary['remarks']))
                        <div class="mb-0">
                            <small class="text-muted d-block">Remarks</small>
                            <span class="font-weight-bold" style="color: #252525;">{{ Str::limit($requestSummary['remarks'], 140) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="d-none d-xl-flex align-items-center justify-content-center" style="min-width: 40px;">
            <div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center blinking-circle" style="width: 40px; height: 40px; border: 2px solid #4562C3;">
                <i class="fa fa-exchange" style="color: #4562C3;"></i>
            </div>
        </div>

        <div class="flex-grow-1 d-flex">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 18px;">
                <div class="card-header border-0" style="background-color: #4562C3; color: #FFFFFF; border-top-left-radius: 18px; border-top-right-radius: 18px;">
                    <h5 class="mb-0 font-weight-bold">Price & Quantity Comparison</h5>
                </div>
                <div class="card-body" style="background-color: #F1F1F1; border-bottom-left-radius: 18px; border-bottom-right-radius: 18px;">
                    @if(empty($vendorCards))
                        <div class="text-center text-muted py-5">
                            <i class="fa fa-inbox fa-2x mb-2"></i>
                            <p class="mb-0">No vendor quotations received yet.</p>
                        </div>
                    @else
                        @php
                            $maxAmountValue = isset($maxAmount) && $maxAmount > 0 ? $maxAmount : max(array_map(fn($card) => $card['amount'] ?? 0, $vendorCards));
                            $maxRateValue = isset($maxRate) && $maxRate > 0 ? $maxRate : max(array_map(fn($card) => $card['rate'] ?? 0, $vendorCards));
                            $maxQuantityValue = isset($maxQuantity) && $maxQuantity > 0 ? $maxQuantity : max(array_map(fn($card) => $card['quantity'] ?? 0, $vendorCards));

                            $maxAmountValue = $maxAmountValue > 0 ? $maxAmountValue : 1;
                            $maxRateValue = $maxRateValue > 0 ? $maxRateValue : 1;
                            $maxQuantityValue = $maxQuantityValue > 0 ? $maxQuantityValue : 1;
                        @endphp
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-muted">Budget Baseline</h6>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small text-muted">
                                    <span>Amount</span>
                                    <span>{{ $requestSummary['amount'] !== null ? number_format($requestSummary['amount'], 2) : '-' }}</span>
                                </div>
                                <div class="progress" style="height: 10px; border-radius: 999px;">
                                    <div class="progress-bar bg-secondary" role="progressbar" style="width: 100%;"></div>
                                </div>
                            </div>
                        </div>

                        <h6 class="font-weight-bold text-muted mb-3">Vendor Comparison</h6>
                        <div class="table-responsive">
                            <table class="table table-borderless table-hover mb-0 comparative-report-table">
                                <thead>
                                    <tr class="small text-muted">
                                        <th style="width: 20%; text-align: left;">Vendor</th>
                                        <th style="width: 16%; text-align: center;">Amount</th>
                                        <th style="width: 11%; text-align: center;">Unit Rate</th>
                                        <th style="width: 8%; text-align: center;">Qty</th>
                                        <th style="width: 10%; text-align: center;">Delivery</th>
                                        <th style="width: 9%; text-align: center;">Warranty</th>
                                        <th style="width: 8%; text-align: center;">Currency</th>
                                        <th style="width: 8%; text-align: center;">Advance</th>
                                        <th style="width: 10%; text-align: left;">Specs</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vendorCards as $card)
                                        @php
                                            $amountPercent = $card['amount'] !== null ? min(100, ($card['amount'] / $maxAmountValue) * 100) : 0;
                                            $ratePercent = $card['rate'] !== null ? min(100, ($card['rate'] / $maxRateValue) * 100) : 0;
                                            $quantityPercent = $card['quantity'] !== null ? min(100, ($card['quantity'] / $maxQuantityValue) * 100) : 0;
                                            $deliveryDisplay = isset($card['delivery_time']) && trim((string) $card['delivery_time']) !== '' ? $card['delivery_time'] : '-';
                                            $warrantyDisplay = isset($card['warranty']) && trim((string) $card['warranty']) !== '' ? $card['warranty'] : '-';
                                            $currencyDisplay = isset($card['currency']) && trim((string) $card['currency']) !== '' ? strtoupper($card['currency']) : '-';
                                            $advanceDisplay = $card['advance_summary'] ?? '-';
                                            $specsDisplay = isset($card['specifications']) && trim((string) $card['specifications']) !== '' ? Str::limit($card['specifications'], 80) : '-';
                                        @endphp
                                        <tr>
                                            <td class="align-middle" style="color: #252525;">
                                                <div class="font-weight-bold">{{ $card['vendor_name'] }}</div>
                                                <div class="small text-muted">
                                                    {{ !empty($card['brand']) ? Str::title($card['brand']) : '-' }}
                                                    @if(!empty($card['model']))
                                                        &bull; {{ Str::upper($card['model']) }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex justify-content-between align-items-start flex-wrap small text-muted">
                                                    <span>{{ $card['amount'] !== null ? number_format($card['amount'], 2) : '-' }}</span>
                                                    @if(isset($lowestVendorId) && $lowestVendorId !== null && $card['vendor_id'] === $lowestVendorId)
                                                        <span
                                                            class="badge badge-pill d-inline-flex align-items-center"
                                                            style="background-color: #4562C3; color: #FFFFFF; border: 1px solid rgba(255,255,255,0.55); box-shadow: 0 3px 8px rgba(69,85,195,0.25); padding: 3px 12px; margin-left: 6px; margin-top: 3px;"
                                                            onmouseover="var icon=this.querySelector('i'); if(icon){icon.style.transition='transform 0.3s ease'; icon.style.transform='rotate(25deg)'; setTimeout(function(){icon.style.transform='rotate(0deg)';}, 300);}"
                                                            onmouseout="var icon=this.querySelector('i'); if(icon){icon.style.transform='rotate(0deg)';}"
                                                        >
                                                            <i class="fa fa-star mr-1" aria-hidden="true"></i>
                                                            Lowest
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="progress" style="height: 10px; border-radius: 999px; background-color: rgba(69,85,195,0.15);">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ $amountPercent }}%; background-color: #4562C3;"></div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="d-flex justify-content-center small text-muted">
                                                    <span>{{ $card['rate'] !== null ? number_format($card['rate'], 2) : '-' }}</span>
                                                </div>
                                                <div class="progress" style="height: 10px; border-radius: 999px; background-color: rgba(98,199,132,0.2);">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ $ratePercent }}%; background-color: #62C784;"></div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="d-block font-weight-bold" style="color: #252525;">{{ $card['quantity'] ?? '-' }}</span>
                                                <div class="progress" style="height: 6px; border-radius: 999px; background-color: rgba(255,152,0,0.2);">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ $quantityPercent }}%; background-color: #FF9800;"></div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center small" style="color: #252525;">
                                                <span class="d-block">{{ $deliveryDisplay }}</span>
                                            </td>
                                            <td class="align-middle text-center small" style="color: #252525;">
                                                <span class="d-block">{{ $warrantyDisplay }}</span>
                                            </td>
                                            <td class="align-middle text-center small font-weight-bold" style="color: #252525;">
                                                {{ $currencyDisplay }}
                                            </td>
                                            <td class="align-middle text-center small" style="color: #252525;">
                                                {{ $advanceDisplay }}
                                            </td>
                                            <td class="align-middle small text-muted" style="max-width: 200px;">
                                                {{ $specsDisplay }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
