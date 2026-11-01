<?php

namespace App\Services\StoreProcurement;

use App\Models\StoreProcurement\QuotationRequest;
use App\Models\StoreProcurement\Sp_vendor;

class StoreItemsRfqPdfService
{
    /**
     * Store Items RFQ only. Same fields as the department RFQ:
     * vendor block and item title, description, brand, model, qty, UOM.
     * No rate or amount.
     */
    public function build(QuotationRequest $quotationRequest, ?Sp_vendor $vendor, string $rfqNo, string $bidCloseDate): array
    {
        $item = $quotationRequest->storeBudgetItem;
        $description = trim(implode("\n", array_filter([
            optional($item)->remarks,
            $quotationRequest->specifications_prnt_dept ?? null,
            $quotationRequest->parent_department_remarks ?? null,
            $quotationRequest->remarks ?? null,
        ], function ($value) {
            return $value !== null && trim((string) $value) !== '';
        })));

        $itemRows = [[
            'title' => optional($item)->item_title ?? '-',
            'description' => $description !== '' ? $description : '-',
            'brand' => optional($item)->brand ?: '-',
            'model' => optional($item)->model ?: '-',
            'quantity' => $quotationRequest->requested_quantity ?? optional($item)->quantity ?? '-',
            'uom' => optional($item)->unit ?: (optional($item)->app_unit ?: '-'),
        ]];

        require_once public_path('fpdf/fpdf.php');

        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddPage();

        $logoPath = public_path('fpdf/logoimages/bnulogo1.jpg');
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 15, 20, 18);
        }

        $pdf->SetXY(163, 24);
        $pdf->SetTextColor(110, 110, 110);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 6, 'Request For', 0, 2, 'L');
        $pdf->Cell(0, 6, 'Quotation', 0, 2, 'L');
        $pdf->SetTextColor(0, 0, 0);

        $pdf->SetY(60);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 6, '-| Request For Quotation |- ', 0, 1, 'C');
        $pdf->Ln(4);

        $leftColumnX = 15;
        $rightColumnX = 110;

        $printMetaRow = function ($label, $value) use ($pdf) {
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(30, 6, $label, 0, 0, 'L');
            $pdf->Cell(5, 6, ':', 0, 0, 'C');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(60, 6, $this->text($value), 0, 0, 'L');
        };

        $printDualRow = function ($leftLabel, $leftValue, $rightLabel, $rightValue) use ($pdf, $printMetaRow, $leftColumnX, $rightColumnX) {
            $pdf->SetX($leftColumnX);
            $printMetaRow($leftLabel, $leftValue);
            $pdf->SetX($rightColumnX);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(30, 6, $rightLabel, 0, 0, 'L');
            $pdf->Cell(5, 6, ':', 0, 0, 'C');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(40, 6, $this->text($rightValue), 0, 0, 'L');
            $pdf->Ln(6);
        };

        $printDualRow('RFQ No', $rfqNo, 'Bid Close Date', $bidCloseDate);
        $printDualRow('Vendor Name', optional($vendor)->vendor_name ?? '-', 'Vendor Email', optional($vendor)->vendor_email ?? '-');
        $printDualRow('Contact Person', optional($vendor)->vendor_contact_person ?? '-', 'Vendor Phone', optional($vendor)->vendor_phone ?? '-');
        $printDualRow('ATTN', optional($vendor)->attn ?? '-', 'NTN/CNIC', optional($vendor)->ntn_cnic ?? '-');

        $pdf->SetX($leftColumnX);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(30, 6, 'Vendor Address', 0, 0, 'L');
        $pdf->Cell(5, 6, ':', 0, 0, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(0, 6, $this->text(optional($vendor)->vendor_address ?: '-'), 0, 'L');
        $pdf->Ln(8);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetX($leftColumnX);
        $pdf->Cell(0, 6, '-| ITEM DETAILS |- ', 0, 1, 'L');
        $pdf->Ln(4);

        $columnWidths = [10, 38, 48, 24, 24, 14, 22];
        $headers = ['#', 'Item Title', 'Description', 'Brand', 'Model', 'Qty', 'UOM'];

        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetX($leftColumnX);
        foreach ($headers as $index => $header) {
            $pdf->Cell($columnWidths[$index], 8, $header, 1, $index === count($headers) - 1 ? 1 : 0, 'C', true);
        }

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 9);
        foreach ($itemRows as $idx => $row) {
            $cells = [
                (string) ($idx + 1),
                $this->text($row['title']),
                $this->text($row['description']),
                $this->text($row['brand']),
                $this->text($row['model']),
                $this->text($row['quantity']),
                $this->text($row['uom']),
            ];
            $pdf->SetX($leftColumnX);
            foreach ($cells as $index => $cell) {
                $pdf->Cell($columnWidths[$index], 8, $cell, 1, $index === count($cells) - 1 ? 1 : 0, in_array($index, [0, 5, 6], true) ? 'C' : 'L');
            }
        }

        $pdf->Ln(8);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetX($leftColumnX);
        $pdf->Cell(0, 6, '-| NOTES |- ', 0, 1, 'L');
        $pdf->Ln(2);
        $pdf->SetFont('Arial', '', 9);
        foreach ([
            'All quotations should clearly reference the RFQ number.',
            'Please confirm delivery schedule and payment terms in your response.',
            'For any questions, contact the procurement department.',
        ] as $note) {
            $pdf->Cell(5, 5, chr(149), 0, 0);
            $pdf->MultiCell(0, 5, $note, 0, 'L');
        }

        $pdf->Ln(12);
        $pdf->Cell(0, 5, 'For Beaconhouse National University', 0, 1, 'L');
        $pdf->Ln(15);
        $pdf->Cell(0, 5, 'Authorized Signatory: ____________________________', 0, 1, 'L');

        return [
            'content' => $pdf->Output('S'),
            'filename' => 'RFQ-' . preg_replace('/[^A-Za-z0-9\-]/', '', $rfqNo) . '.pdf',
        ];
    }

    private function text($value): string
    {
        $text = trim(str_replace(["\r\n", "\r"], ' ', (string) ($value ?? '')));
        if ($text === '') {
            return '-';
        }
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $text);
            if ($converted !== false && $converted !== '') {
                return $converted;
            }
        }
        return $text;
    }
}
