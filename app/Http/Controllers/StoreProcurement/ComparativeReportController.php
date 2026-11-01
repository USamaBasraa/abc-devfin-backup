<?php

namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use App\Models\StoreProcurement\QuotationRequest;

class ComparativeReportController extends Controller
{
    public function show($id)
    {
        $quotationRequest = QuotationRequest::with([
            'budgetItem',
            'budgetHead',
            'vendorQuotations.vendor',
        ])->findOrFail($id);

        $requestSummary = [
            'request_number' => $quotationRequest->qr_id,
            'item_title' => optional($quotationRequest->budgetItem)->item_title,
            'brand' => optional($quotationRequest->budgetItem)->brand,
            'model' => optional($quotationRequest->budgetItem)->model,
            'category' => optional($quotationRequest->budgetHead)->head_title,
            'quantity' => optional($quotationRequest->budgetItem)->quantity,
            'budget_type' => optional($quotationRequest->budgetItem)->budget_type,
            'purchase_mode' => optional($quotationRequest->budgetItem)->purchase_mode,
            'remarks' => optional($quotationRequest->budgetItem)->remarks,
            'rate' => optional($quotationRequest->budgetItem)->rate,
            'amount' => optional($quotationRequest->budgetItem)->quantity && optional($quotationRequest->budgetItem)->rate
                ? optional($quotationRequest->budgetItem)->quantity * optional($quotationRequest->budgetItem)->rate
                : null,
        ];

        $latestByVendor = [];

        foreach ($quotationRequest->vendorQuotations as $quotation) {
            $vendorId = $quotation->vendor_id;

            if (!isset($latestByVendor[$vendorId])) {
                $latestByVendor[$vendorId] = $quotation;
                continue;
            }

            $savedQuotation = $latestByVendor[$vendorId];

            if ($quotation->created_at && $savedQuotation->created_at) {
                if ($quotation->created_at->greaterThan($savedQuotation->created_at)) {
                    $latestByVendor[$vendorId] = $quotation;
                }
            } elseif ($quotation->created_at && !$savedQuotation->created_at) {
                $latestByVendor[$vendorId] = $quotation;
            }
        }

        $vendorCards = [];
        $lowestPrice = null;
        $lowestVendorId = null;

        foreach ($latestByVendor as $vendorId => $quotation) {
            $vendorName = optional($quotation->vendor)->vendor_name;
            $price = $quotation->price;

            if ($price !== null) {
                if ($lowestPrice === null || $price < $lowestPrice) {
                    $lowestPrice = $price;
                    $lowestVendorId = $vendorId;
                }
            }

            $advanceRequired = $quotation->advance_required ?? null;
            $advancePercent = $quotation->advance_percent;
            $advanceSummary = '-';
            if ($advanceRequired !== null && $advanceRequired !== '') {
                $advLabel = ucfirst(strtolower(trim((string) $advanceRequired)));
                if ($advancePercent !== null && $advancePercent !== '') {
                    $advanceSummary = $advLabel . ' · ' . number_format((float) $advancePercent, 2) . '%';
                } else {
                    $advanceSummary = $advLabel;
                }
            }

            $vendorCards[] = [
                'vendor_id' => $vendorId,
                'vendor_name' => $vendorName ?: 'Unknown Vendor',
                'item_title' => $quotation->item_title ?: ($requestSummary['item_title'] ?? null),
                'brand' => $quotation->brand ?: ($requestSummary['brand'] ?? null),
                'model' => $quotation->model ?: ($requestSummary['model'] ?? null),
                'rate' => $quotation->rate,
                'amount' => $quotation->price,
                'quantity' => $quotation->quantity,
                'delivery_time' => $quotation->delivery_time,
                'warranty' => $quotation->warranty,
                'currency' => $quotation->currency,
                'advance_summary' => $advanceSummary,
                'specifications' => $quotation->specifications,
            ];
        }

        usort($vendorCards, function ($first, $second) {
            return strcasecmp($first['vendor_name'], $second['vendor_name']);
        });

        $vendorCount = count($vendorCards);

        return view('store_procurement.purchase_order.comparative_report', [
            'requestSummary' => $requestSummary,
            'vendorCards' => $vendorCards,
            'vendorCount' => $vendorCount,
            'lowestVendorId' => $lowestVendorId,
        ]);
    }
}

