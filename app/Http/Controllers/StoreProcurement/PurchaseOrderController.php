<?php
namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreProcurement\VendorCategory;

use App\Models\StoreProcurement\Sp_vendor;
use App\Models\StoreProcurement\PurchaseOrder;
use App\Models\StoreProcurement\SpPoApproval;
use DB;
use Carbon\Carbon;
use App\Models\StoreProcurement\QuotationRequest;
use App\Models\StoreProcurement\VendorEmailLog;
use App\Services\StoreProcurement\StoreItemsSchemaService;
class PurchaseOrderController extends Controller
{
            public function create()
        {
            $vendors = Sp_vendor::all();
            return view('store_procurement.purchase_order.create', compact('vendors'));
        }

      public function store(Request $request)
    {
        // dd('sadf');
        $request->validate([
            'vendor_quotation_id' => 'required|numeric',
            'quantity'           => 'required|numeric|min:1',
            'warranty'           => 'nullable|string|max:255',
            'delivery_time'      => 'nullable|string|max:255',
            'vendor_id'          => 'required|numeric',
        ]);
    //    dd($request->all());




// if  ($request->budget_type == 'Budgeted' )
// {
//     if  ($request->total_price > 35000 )
//     {
//  //SpPoApproval.   model
//     }
//      if  (35000 <$request->total_price > 200000 )
//      {

//      }
//       if  (200000  <$request->total_price > 500000 )
//       {

//       }
//       if  (500000  <$request->total_price  )
//       {

//       }


// }

        $purchaseOrder = PurchaseOrder::create([
            'vendor_quotation_id' => $request->vendor_quotation_id,
            'quantity'            => $request->quantity,
            'warranty'            => $request->warranty,
            'delivery_time'       => $request->delivery_time,
            'vendor_id'           => $request->vendor_id,
            'total_price'         => $request->total_price,
            'budget_type'         => $request->budget_type,
            'unit_price'          => $request->unit_price,
            'currency'            => $request->currency,
            'remarks'             => $request->remarks,
            'po_date'             => Carbon::now(), // date from request
            'created_at'          => Carbon::now(),
            'updated_at'          => Carbon::now(),
        ]);
        $poId = $purchaseOrder->po_id;
        // dd($poId);
           $check = QuotationRequest::where('qr_id', $request->qr_id)
            ->update([
                'po_id'      => $poId,
                'updated_at' => Carbon::now(),
            ]);
            // dd($check );

        return response()->json([
            'message' => 'Purchase Order created successfully.',
            'po_id'   => $poId
        ]);
    }

        public function proceedWithOrder(Request $request)
            {
                $request->validate([
                    'quotation_request_id' => 'required|exists:sp_quotation_request,qr_id',
                    'vendor_quotation_id' => 'required|exists:sp_vendor_quotations,vq_id',
                ]);

                // Store PO data - or update quotation status
                $poId = DB::table('sp_purchase_orders')->insertGetId([
                    'quotation_request_id' => $request->quotation_request_id,
                    'vendor_quotation_id' => $request->vendor_quotation_id,
                    'created_at' => now(),
                ]);

                return response()->json([
                    'message' => 'Purchase Order created successfully.',
                    'po_id' => $poId
                ]);
            }
    // public function show($id)
    // {
    //     $po = PurchaseOrder::with(['vendor', 'vendorQuotation'])->findOrFail($id);

    //     return view('store_procurement.purchase_order.show', compact('po'));
    // }
public function show2($id)
{
    $po = PurchaseOrder::with([
        'vendor',
        'vendorQuotation.vendor',
        'vendorQuotation.quotationRequest.budgetItem',
        'vendorQuotation.quotationRequest.budgetHead'
    ])->findOrFail($id);


    return view('store_procurement.purchase_order.show', compact('po'));
}
public function poList()
{

// dd('dsfsdf');
   $po = PurchaseOrder::with([
    'vendor',
    'vendorQuotation.vendor',
    'vendorQuotation.quotationRequest.budgetItem',
    'vendorQuotation.quotationRequest.budgetHead'
])->latest()->get();

return response()->json($po);
    
    //->findOrFail($id);







    }


public function show($id)
{
    // dd("working");
    $po = PurchaseOrder::with([
        'vendor',
        'vendorQuotation.vendor',
        'vendorQuotation.quotationRequest.budgetItem',
        'vendorQuotation.quotationRequest.budgetHead',
        'vendorQuotation.quotationRequest.storeBudgetItem',
        'vendorQuotation.quotationRequest.storeBudgetHead'
    ])->findOrFail($id);
    $this->normalizePoQuotationRequest($po);

    //$total_price = $po->total_price;
    $pdfContent = $this->generatePurchaseOrderPDF($po);

    $username = auth()->user()->username ?? 'user';
    $username = preg_replace('/[^a-zA-Z0-9_-]/', '_', $username);

    // Set proper PDF title
    $pdfTitle = 'Purchase Order - ' . ($po->po_number ?? 'PO-' . $po->po_id. $username);

    return response($pdfContent)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="' . $pdfTitle . '.pdf"')
        ->header('X-PDF-Title', $pdfTitle);
}
public function newPdf($id)
{
    $po = PurchaseOrder::with([
        'vendor',
        'vendorQuotation.vendor',
        'vendorQuotation.quotationRequest.budgetItem',
        'vendorQuotation.quotationRequest.budgetHead',
        'vendorQuotation.quotationRequest.storeBudgetItem',
        'vendorQuotation.quotationRequest.storeBudgetHead'
    ])->findOrFail($id);
    $this->normalizePoQuotationRequest($po);

    $pdfContent = $this->generateStaticRFQPDF($po);

    $username = auth()->user()->username ?? 'user';
    $username = preg_replace('/[^a-zA-Z0-9_-]/', '_', $username);

    $pdfTitle = 'Request For Quotation - ' . ($po->po_number ?? 'PO-' . $po->po_id . $username);

    return response($pdfContent)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="' . $pdfTitle . '.pdf"')
        ->header('X-PDF-Title', $pdfTitle);
}

private function generatePurchaseOrderPDF($po)
{
    require_once(public_path('fpdf/fpdf.php'));

    $pdf = new class('P', 'mm', 'A4') extends \FPDF {
        public function Footer()
        {
            // Fixed footer on every page
            $this->SetY(-36);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 6, 'I hereby acknowledge that I have reviewed and verified all the above information.', 0, 1, 'C');

            $this->Ln(1);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(90, 6, 'Prepared by:', 0, 0, 'L');
            $this->Cell(0, 6, 'Signed by:', 0, 1, 'R');

            $lineY = $this->GetY() + 1;
            $this->Line(10, $lineY, 80, $lineY);
            $this->Line(130, $lineY, 200, $lineY);

            $this->SetY(-8);
            $this->SetFont('Arial', '', 8);
            $this->Cell(0, 5, 'Generated by BNUCMS at ' . now()->timezone(config('app.timezone', 'Asia/Karachi'))->format('d-m-Y H:i:s'), 0, 0, 'C');
        }
    }; // P = Portrait mode (standard A4 for easy printing)
    $pdf->SetAutoPageBreak(true, 45);
    $pdf->SetMargins(5, 10, 5); // Left, Top, Right margins (reduced left/right margins)
    $pdf->AliasNbPages();

    // Set PDF metadata
    $pdfTitle = 'Purchase Order - ' . ($po->po_number ?? 'PO-' . $po->po_id);
    $pdf->SetTitle($pdfTitle);
    $pdf->SetAuthor('BNUCMS');
    $pdf->SetSubject('Purchase Order Document');
    $pdf->SetKeywords('Purchase Order, PO, BNU, Procurement');

    $pdf->AddPage();

        // Header with Logo (Top Left) - Dark stylized logo with two B shapes
        if (file_exists(public_path('fpdf/logoimages/bnulogo1.jpg'))) {
            $pdf->Image(public_path('fpdf/logoimages/bnulogo1.jpg'), 20, 12, 15, 20); // X=15mm, Y=12mm, Width=20mm, Height=20mm
        }

    // BNU Text below logo - Large bold then smaller
    $pdf->SetXY(15, 32);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(20, 6, '', 0, 1, 'C');
    $pdf->SetX(15);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(20, 4, '', 0, 1, 'C');

    // University Info - Right side of logo
    $pdf->SetXY(40, 12);
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 7, 'Beaconhouse National University', 0, 1, 'L');

    $pdf->SetX(40);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(0, 4, '13-Km Raiwind Road, Lahore 54400, Pakistan', 0, 1, 'L');

    $pdf->SetX(40);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 4, 'Ph: +92-42-38100156 Fax: +92-42-5760254 Email: info@bnu.edu.pk', 0, 1, 'L');

    $pdf->SetX(40);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 4, 'URL: www.bnu.edu.pk NTN: 4098876-7', 0, 1, 'L');

    $pdf->Ln(5);

    // Main Title - Centered
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'PURCHASE ORDER VENDOR', 0, 1, 'C');


    // Table with 10 columns and 3 rows
    $pdf->SetFont('Arial', '', 8);

    // Define custom column widths (A4 Portrait = 210mm - 10mm margins = 200mm available)
    $col1 = 12;   // Sr No
    $col2_4 = 70; // Item Description (cols 2,3,4 merged) - reduced
    $col5 = 28;   // Purchase Req No (reduced)
    $col6 = 18;   // Delivery Date (reduced)
    $col7 = 10;   // Qty
    $col8 = 18;   // UoM
    $col9 = 18;   // Rate (narrower)
    $col10 = 26;  // Total Amount (wider)

    // Row 1 - Header Row (with merged cells) - All same height
    $pdf->SetFont('Arial', '', 8);

    // Get starting position for row
    $startX = $pdf->GetX();
    $startY = $pdf->GetY();


$supplierInfo = " Supplier:    ".$po->vendor->vendor_name;
$supplierAddress = " Address:  ".$po->vendor->vendor_address;
$phoneNumber = " Phone: ".$po->vendor->vendor_phone;
$supplierNTN = " NTN/CNIC: 3516838-2".$po->vendor->ntn_cnic;
$supplierAttn = " Attn: " .$po->vendor->attn;
$beneficiaryName = " Beneficiary Name: " .$po->vendor->beneficiary_name;
$bankname = " Bank Name: " .$po->vendor->bank_name;
$bankiban = " Bank IBAN: ".$po->vendor->bank_iban;

$supplierInfoText = $supplierInfo . "\n" . $supplierAddress . "\n" . $phoneNumber . "\n" . $supplierNTN . "\n" . $supplierAttn . "\n" . $beneficiaryName . "\n" . $bankname . "\n" . $bankiban;

    // Content for the first merged cell
    $multiCellWidth = $col1 + $col2_4 + $col5;
    $lineHeight = 5; // Height of each line within MultiCell (half spacing)

    // Calculate the height required for the MultiCell content (without drawing)
    $pdf->SetFont('Arial', '', 8); // Ensure simple font for calculation
    $pdf->SetXY($startX, $startY);
    $pdf->MultiCell($multiCellWidth, $lineHeight, $supplierInfoText, 0, 'L', false);
    $multiCellContentHeight = $pdf->GetY() - $startY;

    // Determine the maximum height for the entire row (based on content)
    $rowHeight = $multiCellContentHeight+6;

    // Draw Borders for the entire row first
    $pdf->SetXY($startX, $startY); // Reset to start of row
    $pdf->Cell($multiCellWidth, $rowHeight, '', 1, 0, 'C'); // Border for first cell
    $pdf->Cell($col6 + $col7, $rowHeight, '', 1, 0, 'C'); // Border for second cell
    $pdf->Cell($col8 + $col9 + $col10, $rowHeight, '', 1, 1, 'C'); // Border for third cell

    // Now draw Content for each cell (without borders) - Ensure simple font
    $pdf->SetFont('Arial', '', 8); // Explicitly set simple font
    $pdf->SetXY($startX, $startY); // Reset to start of row for content
    $pdf->MultiCell($multiCellWidth, $lineHeight, $supplierInfoText, 0, 'L', false); // Content for first cell

    // Move to next column position for remaining cells
    $pdf->SetXY($startX + $multiCellWidth, $startY);


    // Content for Merged Col 7-8 (same approach as first cell)
    $poDateRaw = $po->po_date ?? $po->created_at ?? now();
    try {
        $poDateFormatted = \Carbon\Carbon::parse($poDateRaw)->format('d-m-Y');
    } catch (\Exception $e) {
        $poDateFormatted = now()->timezone(config('app.timezone', 'Asia/Karachi'))->format('d-m-Y');
    }
    $poDate = "Dated:" . $poDateFormatted;
    $poNumber = "Purchase Order:";
    $quotationRefNo = "Quotation Ref:";
    $quotationDate = "Quotation Date:";


    $col7_8Text = $poNumber . "\n" . $poDate . "\n" . $quotationRefNo . "\n" . $quotationDate ;
    $pdf->MultiCell($col6 + $col7, $lineHeight, $col7_8Text, 0, 'L', false);

    // Move to next column position for third cell
    $pdf->SetXY($startX + $multiCellWidth + $col6 + $col7, $startY);

    // Content for Merged Col 9-10 (same approach as other cells)
    $poDateValue = $poDateFormatted;
    $poNumberValue = $po->po_no;
    $quotationRefNo = "FOB";
    $quotationDateValue = $po->vendorQuotation->created_at->format('d-m-Y');

    $col9_10Text = $poNumberValue . "\n" . $poDateValue . "\n" . $quotationRefNo . "\n" . $quotationDateValue;
    $pdf->MultiCell($col8 + $col9 + $col10, $lineHeight, $col9_10Text, 0, 'L', false); // Content for third cell

    // Ensure the Y cursor is at the end of the tallest cell for the next row
    $pdf->SetY($startY + $rowHeight);

    // Row 2 - Column Headers (custom widths)

    $pdf->SetFont('Arial', 'B', 7); // Bold font for Row 2 headers (reduced from 8 to 7)
    $pdf->Cell($col1, 8, 'Sr No', 1, 0, 'C'); // Col 1 (smaller width)
    $pdf->Cell($col2_4, 8, 'Item Description', 1, 0, 'C'); // Col 2-4 merged
    $pdf->Cell($col5, 8, 'Purchase Req No', 1, 0, 'C'); // Col 5 (larger width)
    $pdf->Cell($col6, 8, 'Delivery Date', 1, 0, 'C'); // Col 6
    $pdf->Cell($col7, 8, 'Qty', 1, 0, 'C'); // Col 7
    $pdf->Cell($col8, 8, 'UoM', 1, 0, 'C'); // Col 8
    $pdf->Cell($col9, 8, 'Rate (PKR)', 1, 0, 'C'); // Col 9
    $pdf->Cell($col10, 8, 'Total Amount (PKR)', 1, 1, 'C'); // Col 10

    // Row 3 - Data row with dynamic + page-fill height
    $pdf->SetFont('Arial', '', 8); // Simple font for Row 3
    $quotationRequest = optional($po->vendorQuotation)->quotationRequest;
    $budgetItem = $quotationRequest ? $quotationRequest->budgetItem : null;
    $displayQty = (float) ($po->quantity ?? optional($po->vendorQuotation)->quantity ?? $budgetItem->quantity ?? 0);
    $displayUnitPrice = (float) ($po->unit_price ?? optional($po->vendorQuotation)->rate ?? 0);
    $displayTotalPrice = (float) ($po->total_price ?? optional($po->vendorQuotation)->price ?? ($displayQty * $displayUnitPrice));
    $itemDescriptionText = trim(
        ($budgetItem->item_title ?? $po->item_title ?? '') . ' ' .
        ($budgetItem->brand ?? $po->brand ?? '') . ' ' .
        ($budgetItem->model ?? $po->model ?? '')
    );
    if ($itemDescriptionText === '') {
        $itemDescriptionText = '-';
    }
    $descLineCount = max(1, (int) ceil(strlen($itemDescriptionText) / 36));
    $contentBasedHeight = max(20, ($descLineCount * 5) + 6);

    // Get starting position for Row 3
    $row3StartX = $pdf->GetX();
    $row3StartY = $pdf->GetY();

    // Fill remaining printable space so table visually spans the page like old layout.
    // Footer is fixed on each page, so we reserve space up to footerTopY.
    $footerTopY = 252;
    $fixedRowsAfterDataHeight = 24; // Row 4 + Row 5 + Row 6
    $fillHeight = $footerTopY - $row3StartY - $fixedRowsAfterDataHeight;
    $row3Height = max($contentBasedHeight, $fillHeight, 20);

    // Draw borders first
    $pdf->Cell($col1, $row3Height, '', 1, 0, 'C'); // Col 1 border
    $pdf->Cell($col2_4, $row3Height, '', 1, 0, 'C'); // Col 2-4 border
    $pdf->Cell($col5, $row3Height, '', 1, 0, 'C'); // Col 5 border
    $pdf->Cell($col6, $row3Height, '', 1, 0, 'C'); // Col 6 border
    $pdf->Cell($col7, $row3Height, '', 1, 0, 'C'); // Col 7 border
    $pdf->Cell($col8, $row3Height, '', 1, 0, 'C'); // Col 8 border
    $pdf->Cell($col9, $row3Height, '', 1, 0, 'C'); // Col 9 border
    $pdf->Cell($col10, $row3Height, '', 1, 1, 'C'); // Col 10 border

    // Add content with top alignment
    $pdf->SetXY($row3StartX, $row3StartY + 2); // Move slightly down from top
    $pdf->Cell($col1, 5, '1', 0, 0, 'C'); // Col 1 (Sr No - horizontally center, top aligned)

    // Multi-line item description for 1st item
    $pdf->SetXY($row3StartX + $col1, $row3StartY + 2);
    $pdf->MultiCell($col2_4, 5, $itemDescriptionText, 0, 'L');

    $pdf->SetXY($row3StartX + $col1 + $col2_4, $row3StartY + 2);
    $pdf->Cell($col5, 5, $po->po_no , 0, 0, 'C'); // Col 5 (Purchase Req No)
    $pdf->Cell($col6, 5, '17-10-2025', 0, 0, 'C'); // Col 6 (Delivery Date)
    $pdf->Cell($col7, 5, $displayQty > 0 ? rtrim(rtrim(number_format($displayQty, 2), '0'), '.') : '-', 0, 0, 'C'); // Col 7 (Qty - integer)
    $pdf->Cell($col8, 5, 'JOB', 0, 0, 'C'); // Col 8 (UoM)
    $pdf->Cell($col9, 5, $displayUnitPrice > 0 ? number_format($displayUnitPrice, 2) : '-', 0, 0, 'C'); // Col 9 (Rate)
    $pdf->Cell($col10, 5, $displayTotalPrice > 0 ? number_format($displayTotalPrice, 2) : '-', 0, 0, 'C'); // Col 10 (Total Amount)

    // Add second item row (Row 3.5)
    // $pdf->SetXY($row3StartX, $row3StartY + 20); // Move down for second item
    // $pdf->Cell($col1, 5, '2', 0, 0, 'C'); // Col 1 (Sr No 2)

    // Multi-line item description for 2nd item
    // $pdf->SetXY($row3StartX + $col1, $row3StartY + 20);
    // $pdf->MultiCell($col2_4, 5, "Computer Hardware and Software Maintenance Services for IT Department Annual Support Contract", 0, 'L');

    // $pdf->SetXY($row3StartX + $col1 + $col2_4, $row3StartY + 20);
    // $pdf->Cell($col5, 5, 'IT/11/25/UNI/8/2', 0, 0, 'C'); // Col 5 (Purchase Req No)
    // $pdf->Cell($col6, 5, '25-10-2025', 0, 0, 'C'); // Col 6 (Delivery Date)
    // $pdf->Cell($col7, 5, '1', 0, 0, 'C'); // Col 7 (Qty - integer)
    // $pdf->Cell($col8, 5, 'CONTRACT', 0, 0, 'C'); // Col 8 (UoM)
    // $pdf->Cell($col9, 5, '75,000.00', 0, 0, 'C'); // Col 9 (Rate)
    // $pdf->Cell($col10, 5, '75,000', 0, 0, 'C'); // Col 10 (Total Amount)

    // Move to next row position (Row 4)
    $pdf->SetXY($row3StartX, $row3StartY + $row3Height);

    // Row 4 - Total row (merged columns with Total and Price)
    $pdf->Cell($col1 + $col2_4 + $col5 + $col6 + $col7, 8, '', 1, 0, 'C'); // Merge cols 1-7 (empty)
    $pdf->Cell($col8, 8, 'Total', 1, 0, 'C'); // Col 8 (UoM location - "Total")
    $pdf->Cell($col9, 8, '', 1, 0, 'C'); // Col 9 (empty)
    $pdf->Cell($col10, 8, number_format(max($displayTotalPrice, 0), 2), 1, 1, 'C'); // Col 10 (Grand total)

    // Row 5 - Mode of Payment (all columns merged)
    $pdf->Cell($col1 + $col2_4 + $col5 + $col6 + $col7 + $col8 + $col9 + $col10, 8, '    Mode of Payment: Online Transfer', 1, 1, 'L'); // All columns merged, text with 4 spaces at start

    // Row 6 - Terms and Conditions (same as Row 5)
    $pdf->Cell($col1 + $col2_4 + $col5 + $col6 + $col7 + $col8 + $col9 + $col10, 8, '    Terms and Conditions:', 1, 1, 'L'); // All columns merged, text with 4 spaces at start

    return $pdf->Output('', 'S');
}

private function normalizePoQuotationRequest(PurchaseOrder $po): void
{
    $quotationRequest = optional($po->vendorQuotation)->quotationRequest;
    if (!$quotationRequest) {
        return;
    }

    if (app(StoreItemsSchemaService::class)->ensure() && $quotationRequest->request_type === 'store_items') {
        if ($quotationRequest->storeBudgetItem) {
            $quotationRequest->setRelation('budgetItem', $quotationRequest->storeBudgetItem);
        }
        if ($quotationRequest->storeBudgetHead) {
            $quotationRequest->setRelation('budgetHead', $quotationRequest->storeBudgetHead);
        }
    }
}

    private function buildRfqNumber($reference)
    {
        $id = $reference ?? random_int(1, 99999);
        return 'QF-RFQ-' . str_pad($id, 5, '0', STR_PAD_LEFT);
    }

    private function resolveBidCloseDate($qrId)
    {
        $latestEmail = VendorEmailLog::where('ve_qr_id', $qrId)
            ->orderBy('ve_sent_at', 'desc')
            ->first();

        if ($latestEmail) {
            return Carbon::parse($latestEmail->ve_sent_at)->format('d-M-Y h:i A');
        }

        return 'N/A';
    }

    private function generateStaticRFQPDF($po)
    {
        require_once(public_path('fpdf/fpdf.php'));

        $rfqNumber = $this->buildRfqNumber($po->qr_id ?? $po->po_id);
        $bidCloseDate = $this->resolveBidCloseDate($po->qr_id);

        $quotation = $po->vendorQuotation;
        $request = $quotation ? $quotation->quotationRequest : null;
        $budgetItem = $request ? $request->budgetItem : null;
        $vendor = $po->vendor ?: ($quotation ? $quotation->vendor : null);

        $itemTitle = $budgetItem->item_title ?? $quotation->item_title ?? 'Security seals (1000 nos. in 1 packet)';
        $brand = $budgetItem->brand ?? $quotation->brand ?? 'Generic';
        $model = $budgetItem->model ?? $quotation->model ?? 'N/A';
        $quantity = $po->quantity ?? $quotation->quantity ?? $budgetItem->quantity ?? 1;
        $unitPrice = $po->unit_price ?? $quotation->rate ?? 0;
        $totalPrice = $po->total_price ?? $quotation->price ?? ($unitPrice * $quantity);
        $currency = $po->currency ?? $quotation->currency ?? 'PKR';

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

        $printMetaRow = function($label, $value) use ($pdf) {
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(30, 6, $label, 0, 0, 'L');
            $pdf->Cell(5, 6, ':', 0, 0, 'C');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(60, 6, $value, 0, 0, 'L');
        };

        $printDualRow = function($leftLabel, $leftValue, $rightLabel, $rightValue) use ($pdf, $printMetaRow, $leftColumnX, $rightColumnX) {
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

        $pdf->SetX($leftColumnX);
        $printMetaRow('Subject', $itemTitle);
        $pdf->Ln(6);

        $printDualRow('RFQ No', $rfqNumber, 'Bid Close Date', $bidCloseDate);
        $printDualRow('Payment Terms', 'N/A', 'Shipping Method', 'N/A');
        $printDualRow('Incoterms', 'N/A', 'Currency', strtoupper($currency));

        $pdf->Ln(6);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetX($leftColumnX);
        $pdf->Cell(0, 6, '-| INVITATION TO TENDER |- ', 0, 1, 'L');

        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetX($leftColumnX);
        $pdf->Cell(25, 5, 'Date', 0, 0, 'L');
        $pdf->Cell(5, 5, ':', 0, 0, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, Carbon::now()->format('d F Y'), 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetX($leftColumnX);
        $pdf->Cell(25, 5, 'Tender Reference', 0, 0, 'L');
        $pdf->Cell(5, 5, ':', 0, 0, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, $rfqNumber, 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetX($leftColumnX);
        $pdf->Cell(25, 5, 'Tender Title', 0, 0, 'L');
        $pdf->Cell(5, 5, ':', 0, 0, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, $itemTitle, 0, 1, 'L');

        $pdf->Ln(6);
        $invitationText = "Dear Sir/Madam,\n\n"
            . "Beaconhouse National University is pleased to invite you to tender for the services as per the following "
            . "conditions contained on:\n\n"
            . "- Scope of Work\n\n"
            . "By submitting a proposal, the Tenderer accepts and agrees to comply with these requirements. "
            . "Your positive response will be highly appreciated.";
        $pdf->SetX($leftColumnX);
        $pdf->MultiCell(0, 5, $invitationText, 0, 'L');

        $pdf->Ln(6);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetX($leftColumnX);
        $pdf->Cell(0, 6, 'Scope of Supply & BOQ :', 0, 1, 'L');

        $pdf->Ln(2);
        $tableX = $leftColumnX;
        $columnWidths = [18, 52, 75, 15, 20];
        $headers = ['No.', 'ITEM CODE', 'ITEM DESCRIPTION', 'Qty', 'UOM'];

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

        $lineHeight = 6;
        $rows = [
            ['1', $brand, $itemTitle . ' - ' . $model, (string)$quantity, 'Unit'],
        ];

        foreach ($rows as $rowIndex => $row) {
            $wrappedRow = [
                $row[0],
                wordwrap($row[1], 26, "\n", true),
                wordwrap($row[2], 36, "\n", true),
                $row[3],
                $row[4],
            ];

            $maxLines = 0;
            foreach ($wrappedRow as $cellText) {
                $maxLines = max($maxLines, substr_count($cellText, "\n") + 1);
            }
            $rowHeight = $maxLines * $lineHeight;

            $x = $tableX;
            $y = $pdf->GetY();

            foreach ($columnWidths as $width) {
                $pdf->Rect($x, $y, $width, $rowHeight);
                $x += $width;
            }

            $x = $tableX;
            foreach ($wrappedRow as $idx => $cellText) {
                $pdf->SetXY($x, $y);
                $pdf->MultiCell($columnWidths[$idx], $lineHeight, $cellText, 0, 'L');
                $x += $columnWidths[$idx];
            }
            $pdf->SetY($pdf->GetY() + $rowHeight - $lineHeight);
        }

        $pdf->Ln(8);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 6, '-| PAYMENT SCHEDULE |- ', 0, 1, 'L');
        $pdf->Ln(2);
        $paymentSchedule = "Milestone                Milestone Value\n"
            . "Upon delivery of items                            100%";
        $pdf->MultiCell(0, 5, $paymentSchedule, 0, 'L');

        $pdf->Ln(6);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 6, '-| RFQ TERMS & CONDITIONS |- ', 0, 1, 'L');

        $rfqTerms = [
            'Offers must be submitted quoting the RFQ reference in the subject line.',
            'The offer should be valid for 30 days from the bid closing date.',
            'Delivery timeline: within the mutually agreed schedule after award.',
            'BNU reserves the right to split the award or reject any offer without assigning any reason.',
        ];
        $pdf->SetFont('Arial', '', 9);
        foreach ($rfqTerms as $term) {
            $pdf->Cell(5, 5, chr(149), 0, 0);
            $pdf->MultiCell(0, 5, $term, 0, 'L');
        }

        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, 'For Beaconhouse National University', 0, 1, 'L');
        $pdf->Ln(15);
        $pdf->Cell(0, 5, 'Authorized Signatory: ____________________________', 0, 1, 'L');

        return $pdf->Output('S');
    }




    public function recommendedQuotations()
    {
        $hasStoreItemsSchema = app(StoreItemsSchemaService::class)->ensure();

        // Get logged-in user's ID
        $approvalUserIds = collect([
            auth()->user()->employee_id ?? null,
            auth()->id(),
        ])->filter()->unique()->values();

        if ($approvalUserIds->isEmpty()) {
            $empty = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, ['path' => request()->url()]);
            return view('store_procurement.quotation_request.recommended_quotations', [
                'pendingApprovals' => $empty,
                'approvedApprovals' => $empty,
                'rejectedApprovals' => $empty,
            ]);
        }
    // dd(11111);
        // Get all pending approvals for this user (po_id can be null)
        $allPendingApprovals = SpPoApproval::whereIn('approver_id', $approvalUserIds)
            ->whereRaw('LOWER(status) = ?', ['pending'])
            ->orderBy('slab_order', 'asc')
            ->get();

        // Filter: Only show if it's 1st slab OR previous slab is approved
        $filteredApprovals = $allPendingApprovals->filter(function($approval) {
            // If first slab, always show
            if ($approval->slab_order == 1) {
                return true;
            }

            // Check if previous slab is approved (using qr_id instead of po_id)
            $previousSlabApproved = SpPoApproval::where('qr_id', $approval->qr_id)
                ->where('slab_order', $approval->slab_order - 1)
                ->whereRaw('LOWER(status) = ?', ['approved'])
                ->exists();

            return $previousSlabApproved;
        });

        // Build quotations collection from qr_id
        $pendingQuotations = collect();
        foreach ($filteredApprovals as $approval) {
            $quotationRequest = $this->resolveQuotationRequestForApproval($approval->qr_id, $hasStoreItemsSchema);

            if ($quotationRequest) {
                $pendingQuotations->push($this->buildMockPoForApproval($approval, $quotationRequest));
            }
        }

        // Paginate the filtered results manually
        $page = request()->get('pending_page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $pendingApprovals = new \Illuminate\Pagination\LengthAwarePaginator(
            $pendingQuotations->slice($offset, $perPage)->values(),
            $pendingQuotations->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'pageName' => 'pending_page']
        );

        // Get approved approvals assigned to the logged-in user
        $approvedApprovalsData = SpPoApproval::whereIn('approver_id', $approvalUserIds)
            ->whereRaw('LOWER(status) = ?', ['approved'])
            ->orderBy('slab_order', 'asc')
            ->get();

        // Build approved quotations collection
        $approvedQuotations = collect();
        foreach ($approvedApprovalsData as $approval) {
            $quotationRequest = $this->resolveQuotationRequestForApproval($approval->qr_id, $hasStoreItemsSchema);

            if ($quotationRequest) {
                $approvedQuotations->push($this->buildMockPoForApproval($approval, $quotationRequest));
            }
        }

        // Paginate approved
        $approvedPage = request()->get('approved_page', 1);
        $approvedPerPage = 10;
        $approvedOffset = ($approvedPage - 1) * $approvedPerPage;

        $approvedApprovals = new \Illuminate\Pagination\LengthAwarePaginator(
            $approvedQuotations->slice($approvedOffset, $approvedPerPage)->values(),
            $approvedQuotations->count(),
            $approvedPerPage,
            $approvedPage,
            ['path' => request()->url(), 'pageName' => 'approved_page']
        );

        // Get rejected approvals
        $rejectedApprovalsData = SpPoApproval::whereIn('approver_id', $approvalUserIds)
            ->whereRaw('LOWER(status) = ?', ['rejected'])
            ->orderBy('slab_order', 'asc')
            ->get();

        // Build rejected quotations collection
        $rejectedQuotations = collect();
        foreach ($rejectedApprovalsData as $approval) {
            $quotationRequest = $this->resolveQuotationRequestForApproval($approval->qr_id, $hasStoreItemsSchema);

            if ($quotationRequest) {
                $rejectedQuotations->push($this->buildMockPoForApproval($approval, $quotationRequest));
            }
        }

        // Paginate rejected
        $rejectedPage = request()->get('rejected_page', 1);
        $rejectedPerPage = 10;
        $rejectedOffset = ($rejectedPage - 1) * $rejectedPerPage;

        $rejectedApprovals = new \Illuminate\Pagination\LengthAwarePaginator(
            $rejectedQuotations->slice($rejectedOffset, $rejectedPerPage)->values(),
            $rejectedQuotations->count(),
            $rejectedPerPage,
            $rejectedPage,
            ['path' => request()->url(), 'pageName' => 'rejected_page']
        );

        return view('store_procurement.quotation_request.recommended_quotations', compact('pendingApprovals', 'approvedApprovals','rejectedApprovals'));
    }

    private function resolveQuotationRequestForApproval($qrId, bool $hasStoreItemsSchema): ?QuotationRequest
    {
        $relations = [
            'vendorQuotations.vendor',
            'budgetItem.vendorCategory.parentDepartment',
            'budgetHead',
            'requestDepartment',
            'recommendations',
        ];

        if ($hasStoreItemsSchema) {
            $relations[] = 'storeBudgetItem';
            $relations[] = 'storeBudgetHead';
        }

        $quotationRequest = QuotationRequest::with($relations)->find($qrId);
        if (!$quotationRequest) {
            return null;
        }

        if ($hasStoreItemsSchema && $quotationRequest->request_type === 'store_items') {
            if ($quotationRequest->storeBudgetItem) {
                $quotationRequest->setRelation('budgetItem', $quotationRequest->storeBudgetItem);
            }
            if ($quotationRequest->storeBudgetHead) {
                $quotationRequest->setRelation('budgetHead', $quotationRequest->storeBudgetHead);
            }
        }

        return $quotationRequest;
    }

    private function buildMockPoForApproval($approval, QuotationRequest $quotationRequest): \stdClass
    {
        $mockPo = new \stdClass();
        $vendorQuotation = $quotationRequest->vendorQuotations->first();

        if (!$vendorQuotation) {
            $vendorQuotation = (object) [
                'vendor' => null,
                'quotationRequest' => $quotationRequest,
                'quantity' => null,
                'rate' => null,
                'price' => null,
                'currency' => null,
            ];
        }

        $mockPo->po_id = $approval->po_id;
        $mockPo->qr_id = $approval->qr_id;
        $mockPo->quotationRequest = $quotationRequest;
        $mockPo->approval_record = $approval;
        $mockPo->vendorQuotation = $vendorQuotation;
        $mockPo->quantity = $vendorQuotation->quantity ?? null;
        $mockPo->unit_price = $vendorQuotation->rate ?? null;
        $mockPo->total_price = $vendorQuotation->price ?? null;
        $mockPo->currency = $vendorQuotation->currency ?? null;
        $budgetItem = $quotationRequest->budgetItem;
        $mockPo->budget_type = optional($budgetItem)->budget_type ?? optional($budgetItem)->requested_budget_type;
        $itemQty = optional($budgetItem)->quantity;
        $itemRate = optional($budgetItem)->rate;
        $mockPo->budget_amount = ($itemQty !== null && $itemRate !== null)
            ? ((float) $itemQty * (float) $itemRate)
            : null;

        return $mockPo;
    }

        public function approvePo(Request $request)
        {
            try {
                DB::beginTransaction();

                $po = PurchaseOrder::findOrFail($request->po_id);
                $userId = auth()->id();
                $employeeId = auth()->user()->employee_id;

                // Update approval record for this approver
                $approval = SpPoApproval::where('po_id', $request->po_id)
                    ->where('approver_id', $employeeId)
                    ->where('status', 'pending')
                    ->first();

                if ($approval) {
                    $approval->update([
                        'status' => 'approved',
                        'remarks' => $request->remarks,
                        'updated_user_id' => $userId,
                        'updated_datetime' => now(),
                    ]);
                }

                // Check if all approvals are done
                $pendingApprovals = SpPoApproval::where('po_id', $request->po_id)
                    ->where('status', 'pending')
                    ->count();

                // If no pending approvals, approve the PO
                if ($pendingApprovals == 0) {
                    $po->approve();
                }

                DB::commit();
                return response()->json(['message' => 'Purchase Order Approved Successfully']);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
            }
        }

        public function rejectPo(Request $request)
        {
            try {
                DB::beginTransaction();

                $po = PurchaseOrder::findOrFail($request->po_id);
                $userId = auth()->id();
                $employeeId = auth()->user()->employee_id;

                // Update approval record for this approver
                $approval = SpPoApproval::where('po_id', $request->po_id)
                    ->where('approver_id', $employeeId)
                    ->where('status', 'pending')
                    ->first();

                if ($approval) {
                    $approval->update([
                        'status' => 'rejected',
                        'remarks' => $request->remarks,
                        'updated_user_id' => $userId,
                        'updated_datetime' => now(),
                    ]);
                }

                // Reject the entire PO
                $po->reject($request->remarks);

                DB::commit();

                return response()->json(['message' => 'Purchase Order Rejected']);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
            }
        }


}
