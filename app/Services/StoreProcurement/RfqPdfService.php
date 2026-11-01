<?php

namespace App\Services\StoreProcurement;

use App\Models\StoreProcurement\QuotationRequest;
use App\Models\StoreProcurement\Sp_vendor;
use Carbon\Carbon;

class RfqPdfService
{
    public function buildForVendor(int $qrId, Sp_vendor $vendor, string $rfqNo, ?Carbon $sentAt = null): array
    {
        $quotationRequest = QuotationRequest::with([
            'budgetItem',
            'budgetHead.department',
            'requestDepartment'
        ])->findOrFail($qrId);

        $bidCloseDate = ($sentAt ?: Carbon::now('Asia/Karachi'))->format('d-M-Y h:i A');

        $itemRows = [];
        $budgetItem = $quotationRequest->budgetItem;
        if ($budgetItem) {
            $itemDescription = trim(implode("\n", array_filter([
                $budgetItem->remarks ?? null,
                $budgetItem->specifications ?? null,
                $quotationRequest->specifications_prnt_dept ?? null,
                $quotationRequest->parent_department_remarks ?? null,
                $quotationRequest->remarks ?? null,
            ], function ($v) {
                return $v !== null && trim((string) $v) !== '';
            })));

            $itemRows[] = [
                'title' => $budgetItem->item_title ?? '-',
                'description' => $itemDescription !== '' ? $itemDescription : '-',
                'brand' => $budgetItem->brand ?? '-',
                'model' => $budgetItem->model ?? '-',
                'quantity' => $budgetItem->quantity ?? '-',
                'uom' => $budgetItem->uom ?? '-',
            ];
        }

        if (empty($itemRows)) {
            $itemRows[] = [
                'title' => '-',
                'description' => '-',
                'brand' => '-',
                'model' => '-',
                'quantity' => '-',
                'uom' => '-',
            ];
        }

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
            $pdf->Cell(60, 6, $value, 0, 0, 'L');
        };

        $printDualRow = function ($leftLabel, $leftValue, $rightLabel, $rightValue) use ($pdf, $printMetaRow, $leftColumnX, $rightColumnX) {
            $pdf->SetX($leftColumnX);
            $printMetaRow($leftLabel, $leftValue);
            $pdf->SetX($rightColumnX);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(30, 6, $rightLabel, 0, 0, 'L');
            $pdf->Cell(5, 6, ':', 0, 0, 'C');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(40, 6, $rightValue, 0, 0, 'L');
            $pdf->Ln(6);
        };

        $printDualRow('RFQ No', $rfqNo, 'Bid Close Date', $bidCloseDate);
        $printDualRow('Vendor Name', $vendor->vendor_name ?? '-', 'Vendor Email', $vendor->vendor_email ?? '-');
        $printDualRow('Contact Person', $vendor->vendor_contact_person ?? '-', 'Vendor Phone', $vendor->vendor_phone ?? '-');
        $printDualRow('ATTN', $vendor->attn ?? '-', 'NTN/CNIC', $vendor->ntn_cnic ?? '-');

        $pdf->SetX($leftColumnX);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(30, 6, 'Vendor Address', 0, 0, 'L');
        $pdf->Cell(5, 6, ':', 0, 0, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(0, 6, $vendor->vendor_address ?: '-', 0, 'L');
        $pdf->Ln(2);

        $pdf->Ln(6);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetX($leftColumnX);
        $pdf->Cell(0, 6, '-| ITEM DETAILS |- ', 0, 1, 'L');
        $pdf->Ln(4);

        $tableX = $leftColumnX;
        $columnWidths = [10, 38, 48, 24, 24, 14, 22];
        $headers = ['#', 'Item Title', 'Description', 'Brand', 'Model', 'Qty', 'UOM'];

        $pdfText = function ($text) {
            $text = (string) ($text ?? '-');
            $text = str_replace(["\r\n", "\r"], "\n", $text);
            if (function_exists('iconv')) {
                $converted = @iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $text);
                if ($converted !== false) {
                    $text = $converted;
                }
            }
            return $text !== '' ? $text : '-';
        };

        $nbLines = function ($width, $text) use ($pdf) {
            $text = str_replace("\r", '', (string) $text);
            if ($text === '') {
                return 1;
            }
            $paragraphs = explode("\n", $text);
            $lineCount = 0;
            foreach ($paragraphs as $paragraph) {
                if ($paragraph === '') {
                    $lineCount++;
                    continue;
                }
                $words = preg_split('/\s+/', $paragraph) ?: [];
                $current = '';
                foreach ($words as $word) {
                    $test = $current === '' ? $word : ($current . ' ' . $word);
                    if ($pdf->GetStringWidth($test) <= ($width - 1.5)) {
                        $current = $test;
                    } else {
                        if ($current !== '') {
                            $lineCount++;
                        }
                        if ($pdf->GetStringWidth($word) > ($width - 1.5)) {
                            $chunk = '';
                            $chars = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY) ?: str_split($word);
                            foreach ($chars as $ch) {
                                $try = $chunk . $ch;
                                if ($pdf->GetStringWidth($try) <= ($width - 1.5)) {
                                    $chunk = $try;
                                } else {
                                    $lineCount++;
                                    $chunk = $ch;
                                }
                            }
                            $current = $chunk;
                        } else {
                            $current = $word;
                        }
                    }
                }
                if ($current !== '') {
                    $lineCount++;
                }
            }
            return max(1, $lineCount);
        };

        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetX($tableX);
        foreach ($headers as $index => $header) {
            $isLast = $index === count($headers) - 1;
            $pdf->Cell($columnWidths[$index], 8, $header, 1, $isLast ? 1 : 0, 'C', true);
        }

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 9);
        $lineHeight = 5;
        $pageBottom = 277;

        foreach ($itemRows as $idx => $row) {
            $rowData = [
                (string) ($idx + 1),
                $pdfText($row['title'] ?? '-'),
                $pdfText($row['description'] ?? '-'),
                $pdfText($row['brand'] ?? '-'),
                $pdfText($row['model'] ?? '-'),
                $pdfText($row['quantity'] ?? '-'),
                $pdfText($row['uom'] ?? '-'),
            ];

            $maxLines = 1;
            foreach ($rowData as $colIndex => $cellText) {
                $maxLines = max($maxLines, $nbLines($columnWidths[$colIndex], $cellText));
            }
            $rowHeight = max($lineHeight * 2, $maxLines * $lineHeight);

            if ($pdf->GetY() + $rowHeight > $pageBottom) {
                $pdf->AddPage();
                $pdf->SetFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->SetX($tableX);
                foreach ($headers as $index => $header) {
                    $isLast = $index === count($headers) - 1;
                    $pdf->Cell($columnWidths[$index], 8, $header, 1, $isLast ? 1 : 0, 'C', true);
                }
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('Arial', '', 9);
            }

            $x = $tableX;
            $y = $pdf->GetY();

            foreach ($columnWidths as $width) {
                $pdf->Rect($x, $y, $width, $rowHeight);
                $x += $width;
            }

            $x = $tableX;
            foreach ($rowData as $colIndex => $cellText) {
                $pdf->SetXY($x + 0.8, $y + 1);
                $alignment = in_array($colIndex, [0, 5, 6], true) ? 'C' : 'L';
                $pdf->MultiCell($columnWidths[$colIndex] - 1.6, $lineHeight, $cellText, 0, $alignment);
                $x += $columnWidths[$colIndex];
            }

            $pdf->SetY($y + $rowHeight);
        }

        $pdf->Ln(8);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetX($leftColumnX);
        $pdf->Cell(0, 6, '-| NOTES |- ', 0, 1, 'L');
        $pdf->Ln(2);

        $pdf->SetFont('Arial', '', 9);
        $notes = [
            'All quotations should clearly reference the RFQ number.',
            'Please confirm delivery schedule and payment terms in your response.',
            'For any questions, contact the procurement department.',
        ];

        foreach ($notes as $note) {
            $pdf->Cell(5, 5, chr(149), 0, 0);
            $pdf->MultiCell(0, 5, $note, 0, 'L');
        }

        $pdf->Ln(12);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, 'For Beaconhouse National University', 0, 1, 'L');
        $pdf->Ln(15);
        $pdf->Cell(0, 5, 'Authorized Signatory: ____________________________', 0, 1, 'L');

        $fileName = 'RFQ-' . str_replace('RFQ-', '', $rfqNo) . '.pdf';

        return [
            'content' => $pdf->Output('S'),
            'filename' => $fileName,
        ];
    }
}
