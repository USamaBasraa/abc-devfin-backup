@extends('layouts.app')

@section('content')
<style>
    .a4-document {
        max-width: 210mm;
        margin: 0 auto;
        background: white;
        padding: 20mm;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        position: relative;
    }
    .document-header {
        border-bottom: 3px solid #3e5871;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }
    .info-table {
        width: 100%;
        margin-bottom: 20px;
    }
    .info-table td {
        padding: 8px 10px;
        border: 1px solid #dee2e6;
    }
    .info-table td:first-child {
        background-color: #f8f9fa;
        font-weight: 600;
        width: 35%;
    }
    .section-title {
        background-color: #3e5871;
        color: white;
        padding: 8px 15px;
        margin-top: 20px;
        margin-bottom: 15px;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .price-summary {
        border: 2px solid #3e5871;
        padding: 15px;
        margin-top: 20px;
    }
    .total-price {
        font-size: 24px;
        font-weight: bold;
        color: #3e5871;
    }
    .status-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: bold;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .print-button {
        position: fixed;
        top: 100px;
        right: 30px;
        z-index: 1000;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    }
    @media print {
        .a4-document {
            box-shadow: none;
            padding: 0;
        }
        .print-button {
            display: none !important;
        }
        .status-badge {
            box-shadow: none;
        }
    }
</style>

<div class="container-fluid py-4">
    {{-- Print Button --}}
    <button onclick="window.print()" class="btn btn-primary print-button">
        <i class="fa fa-print mr-2"></i>Print Document
    </button>

    <div class="a4-document">
        {{-- Status Badge --}}
        @php
            $status = strtolower($po->approvel_status ?? 'pending');
            $badgeConfig = [
                'approved' => ['bg' => '#28a745', 'text' => 'APPROVED'],
                'rejected' => ['bg' => '#dc3545', 'text' => 'REJECTED'],
                'pending' => ['bg' => '#ffc107', 'text' => 'PENDING'],
            ];
            $badge = $badgeConfig[$status] ?? $badgeConfig['pending'];
        @endphp
        <div class="status-badge" style="background-color: {{ $badge['bg'] }}; color: white;">
            {{ $badge['text'] }}
        </div>
        {{-- Document Header --}}
        <div class="document-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 font-weight-bold" style="color: #3e5871;">PURCHASE ORDER</h3>
                    <p class="mb-0 text-muted">Purchase Order Details</p>
                </div>
                <div class="text-right">
                    @if($po->po_no)
                    <h5 class="mb-1 font-weight-bold" style="color: #3e5871;">{{ $po->po_no }}</h5>
                    @endif
                    {{-- <small class="text-muted">PO ID: {{ $po->po_id }}</small> --}}
                </div>
            </div>
        </div>

        {{-- Vendor Information --}}
        <div class="section-title">Vendor Information</div>
        <table class="info-table">
            <tr>
                <td>Vendor Name</td>
                <td>{{ $po->vendor->vendor_name ?? '-' }}</td>
            </tr>
            <tr>
                <td>PO Date</td>
                <td>{{ $po->po_date ? \Carbon\Carbon::parse($po->po_date)->format('d M, Y') : '-' }}</td>
            </tr>
            <tr>
                <td>Warranty</td>
                <td>{{ $po->warranty ?? '-' }}</td>
            </tr>
            <tr>
                <td>Delivery Time</td>
                <td>{{ $po->delivery_time ?? '-' }}</td>
            </tr>
        </table>

        {{-- Item Details --}}
        <div class="section-title">Item Details</div>
        <table class="info-table">
            <tr>
                <td>Item Name</td>
                <td>{{ $po->vendorQuotation->quotationRequest->budgetItem->item_title ?? '-' }}</td>
            </tr>
            <tr>
                <td>Category (Head)</td>
                <td>{{ $po->vendorQuotation->quotationRequest->budgetHead->head_title ?? '-' }}</td>
            </tr>
            <tr>
                <td>Quantity</td>
                <td><strong>{{ $po->vendorQuotation->quotationRequest->budgetItem->quantity ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>Item Remarks</td>
                <td>{{ $po->vendorQuotation->quotationRequest->budgetItem->remarks ?? '-' }}</td>
            </tr>
        </table>

        {{-- Pricing Details --}}
        <div class="section-title">Pricing Details</div>
        <table class="info-table">
            <tr>
                <td>Unit Price</td>
                <td>{{ number_format($po->vendorQuotation->price ?? 0, 2) }} {{ $po->currency }}</td>
            </tr>
            <tr>
                <td>Quantity</td>
                <td>{{ $po->quantity }}</td>
            </tr>
            <tr>
                <td><strong>Total Amount</strong></td>
                <td><strong class="total-price">{{ number_format(($po->total_price ?? 0)) }} {{ $po->currency }}</strong></td>
            </tr>
        </table>

        {{-- Remarks --}}
        @if($po->remarks)
        <div class="section-title">Remarks</div>
        <div style="padding: 15px; border: 1px solid #dee2e6; background-color: #f8f9fa;">
            {{ $po->remarks }}
        </div>
        @endif
    </div>
</div>

@endsection
