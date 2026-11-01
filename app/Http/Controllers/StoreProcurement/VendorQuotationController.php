<?php
/**
 *
 * User: Arsalan Ahmed
 * Date: 29/07/2025
 * Time: 9:52 AM
 */
namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Console\Helper\Table;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Models\Grader\Schools;
use App\Models\Grader\Departments;
use App\Models\Grader\FacultyCourses;
use App\Models\Grader\EmpEmployees;

use App\Models\StoreProcurement\Sp_vendor;
use App\Models\StoreProcurement\VendorQuotation;
use App\Models\StoreProcurement\QuotationRequest;
use App\Models\StoreProcurement\QuotationChat;
use App\Models\StoreProcurement\BudgetHead;
use App\Models\StoreProcurement\StoreBudgetHead;
use App\Models\StoreProcurement\StoreBudgetItem;
use App\Models\StoreProcurement\VendorCategory;
use App\Models\StoreProcurement\VendorEmailLog;
use App\Models\StoreProcurement\PurchaseOrder;
use App\Models\StoreProcurement\SpPoApproval;

use Response;
use Validator;
use DataTables;
use Carbon\Carbon;
use App\Services\StoreProcurement\VendorQuotationEmailService;
use App\Services\StoreProcurement\StoreItemsSchemaService;

class VendorQuotationController extends Controller
{

        public function vendorQuotations()
    {

        // $vendor = Sp_vendor::where('vendor_id',3);//auth()->user()->vendor; // Adjust how vendor is identified
        $vendor = $vendor = Sp_vendor::find(1);

        $requests = QuotationRequest::where('vendor_category_id', $vendor->vendor_category_id)
                    ->where('department_head_approval' , 'approved')
                    ->with('vendorCategory')
                    ->get();

        return view('store_procurement.purchase_order.vendor_quotation_submit', compact('requests', 'vendor'));
    }

public function searchBudgetItems(Request $request){
    $search = $request->get('q', '');

    if (strlen($search) < 2) {
        return response()->json([]);
    }

    $items = DB::table('sp_budget_items')
        ->select('item_id', 'item_title')
        ->whereRaw('LOWER(item_title) LIKE ?', ['%' . strtolower($search) . '%'])
        ->orderBy('item_title')
        ->limit(20)
        ->get();

    return response()->json($items);
}


public function getLazyLoad(Request $request)
{
    $hasStoreItemsSchema = app(StoreItemsSchemaService::class)->ensure();
    $perPage = $request->get('per_page', 20);
    $page = $request->get('page', 1);

    $query = QuotationRequest::with([

       'budgetItem',
        'budgetHead.department',
        'storeBudgetItem',
        'storeBudgetHead.department',
        'vendorQuotations.vendor'
    ])->where('department_head_approval', 'approved');

    $requestType = $request->get('request_type');
    if ($hasStoreItemsSchema) {
        if ($requestType === 'store_items') {
            $query->where('request_type', 'store_items');
        } else {
            $query->whereNull('request_type');
        }
    }
    // dd($query->get());



    // Apply Filters
    if ($request->filled('department') && $request->department != '') {
        if ($requestType === 'store_items') {
            $query->whereHas('storeBudgetHead', function($q) use ($request) {
                $q->where('dept_id', $request->department);
            });
        } else {
            $query->whereHas('budgetHead', function($q) use ($request) {
                $q->where('dept_id', $request->department);
            });
        }
    }

    // ðŸ”¹ Filter by budget head (inside budgetItem)
    if ($request->filled('budget_head')) {
        if ($requestType === 'store_items') {
            $query->where('cat_id', $request->budget_head);
        } else {
            $query->whereHas('budgetItem', function ($q) use ($request) {
                $q->where('bh_cat_id', $request->budget_head);
            });
        }
    }

    if ($request->filled('item_title') && $request->item_title != '') {
        if ($requestType === 'store_items') {
            $query->whereHas('storeBudgetItem', function($q) use ($request) {
                $q->whereRaw('LOWER(item_title) LIKE ?', ['%' . strtolower($request->item_title) . '%']);
            });
        } else {
            $query->whereHas('budgetItem', function($q) use ($request) {
                $q->whereRaw('LOWER(item_title) LIKE ?', ['%' . strtolower($request->item_title) . '%']);
            });
        }
    }

    if ($request->filled('financial_year') && $request->financial_year != '') {
        if ($requestType === 'store_items') {
            $query->whereHas('storeBudgetHead', function($q) use ($request) {
                $q->where('ay_id', $request->financial_year);
            });
        } else {
            $query->whereHas('budgetHead', function($q) use ($request) {
                $q->where('ay_id', $request->financial_year);
            });
        }
    }

    $user = auth()->user();
    $username = $user ? ($user->username ?? $user->EMP_AD_USERNAME ?? $user->name ?? null) : null;

    // Get total count
    $total = $query->count();

    // Get paginated results
    $results = $query->orderBy('created_at', 'desc')
                     ->orderBy('qr_id', 'desc')
                     ->skip(($page - 1) * $perPage)
                     ->take($perPage)
                     ->get();

    // Format data
    $data = $results->map(function($row) use ($username, $requestType) {
        if ($requestType === 'store_items') {
            if ($row->storeBudgetItem) { $row->setRelation('budgetItem', $row->storeBudgetItem); }
            if ($row->storeBudgetHead) { $row->setRelation('budgetHead', $row->storeBudgetHead); }
        }
        // Check if email sent to any vendor
        $emailSent = VendorEmailLog::where('ve_qr_id', $row->qr_id)->exists();

        // Check if PO exists and all approvals are approved
        $poApprovalCompleted = false;
        $poGenerated = false;
        $poExists = false;
        $poNumber = null;
        $purchaseOrder = PurchaseOrder::where('qr_id', $row->qr_id)->first();

        if ($purchaseOrder) {
            $poExists = true;  // PO record exists

            // Check if PO is already generated
            if ($purchaseOrder->po_status === 'generated' && $purchaseOrder->po_no) {
                $poGenerated = true;
                $poNumber = $purchaseOrder->po_no;
            } else {
                // Check if all approvals are completed
                $totalApprovals = SpPoApproval::where('po_id', $purchaseOrder->po_id)->count();
                $approvedCount = SpPoApproval::where('po_id', $purchaseOrder->po_id)
                                            ->where('status', 'approved')
                                            ->count();

                if ($totalApprovals > 0 && $totalApprovals === $approvedCount) {
                    $poApprovalCompleted = true;
                }
            }
        }

        // Calculate pricing information
        $rate = $row->budgetItem->rate ?? 0;
        $quantity = $row->budgetItem->quantity ?? 0;
        $estimatedTotal = $rate * $quantity;

        // Get lowest vendor quote
        $lowestQuote = null;
        $currency = 'PKR';
        if ($row->vendorQuotations && $row->vendorQuotations->count() > 0) {
            $lowestQuote = $row->vendorQuotations->min('price');
            $currency = $row->vendorQuotations->first()->currency ?? 'PKR';
        }

        // Get PO price if exists
        $poPrice = $purchaseOrder ? $purchaseOrder->total_price : null;

        $chatQuery = QuotationChat::where('qr_id', $row->qr_id)->where('is_read', 0);
        if ($username) {
            $chatQuery->where('entry_username', '!=', $username);
        }
        $unreadChatCount = $chatQuery->count();

        return [
            'qr_id' => $row->qr_id,
            'item_title' => $row->budgetItem->item_title ?? '-',
            'budget_head' => $row->budgetHead->head_title ?? '-',
            'department' => $row->budgetHead->department->dept_name ?? '-',
            'purchase_mode' => ucfirst($row->budgetItem->purchase_mode ?? '-'),
            'quantity' => $quantity,
            'rate' => $rate,
            'estimated_total' => $estimatedTotal,
            'lowest_quote' => $lowestQuote,
            'po_price' => $poPrice,
            'currency' => $currency,
            'email_sent' => $emailSent,
            'po_exists' => $poExists,
            'po_approval_completed' => $poApprovalCompleted,
            'po_generated' => $poGenerated,
            'po_number' => $poNumber,
            'po_id' => $purchaseOrder ? $purchaseOrder->po_id : null,
            'unread_chat_count' => $unreadChatCount,
        ];
    });

    return response()->json([
        'data' => $data,
        'current_page' => $page,
        'per_page' => $perPage,
        'total' => $total,
        'has_more' => ($page * $perPage) < $total
    ]);
}

private function generateRfqNumber(int $requestId)
{
    $baseCounter = 101;

    $latestRfq = VendorEmailLog::where('ve_qr_id', $requestId)
        ->whereNotNull('ve_rfq_no')
        ->orderBy('ve_id', 'desc')
        ->value('ve_rfq_no');

    $nextCounter = $baseCounter;

    if (!empty($latestRfq)) {
        $parts = explode('-', $latestRfq);
        $lastPart = array_pop($parts);

        if (is_numeric($lastPart)) {
            $nextCounter = (int) $lastPart + 1;
        }
    }

    if ($nextCounter < $baseCounter) {
        $nextCounter = $baseCounter;
    }

    return sprintf('RFQ-%s-%03d', $requestId, $nextCounter);
}

    public function getDetails($id)
    {
        app(StoreItemsSchemaService::class)->ensure();
        $req = QuotationRequest::with([
            'budgetItem',
            'budgetHead.department',
            'storeBudgetItem',
            'storeBudgetHead.department',
            'vendorQuotations.vendor',
            'chatMessages'
        ])->findOrFail($id);

        if ($req->request_type === 'store_items') {
            if ($req->storeBudgetItem) { $req->setRelation('budgetItem', $req->storeBudgetItem); }
            if ($req->storeBudgetHead) { $req->setRelation('budgetHead', $req->storeBudgetHead); }
        }

        try {
            $vendors = Sp_vendor::orderBy('vendor_name')->get();
        } catch (\Throwable $e) {
            \Log::warning('Failed to load vendors for quotation details.', [
                'qr_id' => $id,
                'error' => $e->getMessage(),
            ]);
            $vendors = collect();
        }

        $emailedVendorIds = VendorEmailLog::where('ve_qr_id', $id)
        ->whereNotNull('ve_vendor_id')
        ->pluck('ve_vendor_id')
        ->unique()
        ->filter();

    $emailedVendors = $emailedVendorIds->isNotEmpty()
        ? Sp_vendor::whereIn('vendor_id', $emailedVendorIds)->orderBy('vendor_name')->get()
        : collect();


        $user = auth()->user();
        $username = $user ? ($user->username ?? $user->EMP_AD_USERNAME ?? $user->name ?? null) : null;

        $chatQuery = QuotationChat::where('qr_id', $id)->where('is_read', 0);
        if ($username) {
            $chatQuery->where('entry_username', '!=', $username);
        }
        $unreadCount = $chatQuery->count();

        $latestEmail = VendorEmailLog::where('ve_qr_id', $id)
            ->orderBy('ve_sent_at', 'desc')
            ->first();
        $rfqNumber = $latestEmail->ve_rfq_no ?? $this->generateRfqNumber($id);
        $bidCloseDate = $latestEmail ? Carbon::parse($latestEmail->ve_sent_at)->format('d-M-Y h:i A') : null;
        $emailPo = PurchaseOrder::where('qr_id', $req->qr_id)->first();

        $html = view('store_procurement.quotation_request.partials.quotation_detail_modal', [
            'req' => $req,
            'vendors' => $vendors,
            'unreadCount' => $unreadCount,
            'emailedVendors' => $emailedVendors,
            'rfqNumber' => $rfqNumber,
            'bidCloseDate' => $bidCloseDate,
            'emailPo' => $emailPo,
        ])->render();

        return response()->json(['html' => $html]);
    }

    public function downloadRfqPdf(Request $request, $qrId)
    {
        $quotationRequest = QuotationRequest::with([
            'budgetItem',
            'budgetHead.department',
            'requestDepartment'
        ])->findOrFail($qrId);

        $requestedRfqNo = $request->query('rfq_no');
        if ($requestedRfqNo) {
            $decoded = base64_decode($requestedRfqNo, true);
            $requestedRfqNo = $decoded !== false ? $decoded : null;
        }

        $latestEmailLog = VendorEmailLog::where('ve_qr_id', $qrId)
            ->with('vendor')
            ->orderBy('ve_sent_at', 'desc')
            ->first();

        $vendor = optional($latestEmailLog)->vendor;
        $vendorName = $vendor->vendor_name ?? '-';
        $vendorContact = $vendor->vendor_contact_person ?? '-';
        $vendorEmail = $vendor->vendor_email ?? '-';
        $vendorPhone = $vendor->vendor_phone ?? '-';
        $vendorAddress = $vendor->vendor_address ?? '-';
        $vendorAttn = $vendor->attn ?? '-';
        $vendorNtnCnic = $vendor->ntn_cnic ?? '-';

        $rfqNo = $requestedRfqNo ?? ($latestEmailLog->ve_rfq_no ?? 'N/A');
        $bidCloseDate = $latestEmailLog ? Carbon::parse($latestEmailLog->ve_sent_at)->format('d-M-Y h:i A') : '07-Oct-2025 02:00 PM';

        $budgetItem = $quotationRequest->budgetItem;
        $itemTitle = optional($budgetItem)->item_title ?? '-';
        $itemDescription = trim(implode("\n", array_filter([
            optional($budgetItem)->remarks ?? null,
            optional($budgetItem)->specifications ?? null,
            $quotationRequest->specifications_prnt_dept ?? null,
            $quotationRequest->parent_department_remarks ?? null,
            $quotationRequest->remarks ?? null,
        ], function ($v) {
            return $v !== null && trim((string) $v) !== '';
        })));
        if ($itemDescription === '') {
            $itemDescription = '-';
        }

        $itemRows = [];
        if ($budgetItem) {
            $itemRows[] = [
                'title' => $itemTitle,
                'description' => $itemDescription,
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

        $printDualRow('RFQ No', $rfqNo, 'Bid Close Date', $bidCloseDate);
        $printDualRow('Vendor Name', $vendorName, 'Vendor Email', $vendorEmail);
        $printDualRow('Contact Person', $vendorContact, 'Vendor Phone', $vendorPhone);
        $printDualRow('ATTN', $vendorAttn, 'NTN/CNIC', $vendorNtnCnic);

        $pdf->SetX($leftColumnX);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(30, 6, 'Vendor Address', 0, 0, 'L');
        $pdf->Cell(5, 6, ':', 0, 0, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(0, 6, $vendorAddress ?: '-', 0, 'L');
        $pdf->Ln(2);

        $pdf->Ln(6);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetX($leftColumnX);
        $pdf->Cell(0, 6, '-| ITEM DETAILS |- ', 0, 1, 'L');
        $pdf->Ln(4);

        $tableX = $leftColumnX;
        // #, Title, Description, Brand, Model, Qty, UOM
        $columnWidths = [10, 38, 48, 24, 24, 14, 22]; // total 180mm
        $headers = ['#', 'Item Title', 'Description', 'Brand', 'Model', 'Qty', 'UOM'];

        $pdfText = function ($text) {
            $text = (string) ($text ?? '-');
            $text = str_replace(["\r\n", "\r"], "\n", $text);
            // FPDF core fonts need Latin-1 / windows-1252 safe text
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
        $pageBottom = 277; // A4 with bottom margin ~20

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

            // New page if row does not fit
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

            // Draw cell borders for full dynamic row height
            foreach ($columnWidths as $width) {
                $pdf->Rect($x, $y, $width, $rowHeight);
                $x += $width;
            }

            // Fill each cell independently (row-wise MultiCell)
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

        $pdfContent = $pdf->Output('S');

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="RFQ-' . str_replace('QF-RFQ-', '', $rfqNo) . '.pdf"'
        ]);
    }

// ========================================================================
// SEND EMAIL TO VENDORS
// Track which request version the email is being sent for
// ========================================================================
public function sendMailToVendors(Request $request, VendorQuotationEmailService $vendorQuotationEmailService)
{
    $request->validate([
        'request_id' => 'required|integer',
        'vendors' => 'required|array|min:1',
    ]);

    $sentBy = Auth::user()->username ?? Auth::user()->name ?? 'system';

    $result = $vendorQuotationEmailService->queueRfqEmails(
        (int) $request->request_id,
        $request->vendors,
        $sentBy
    );

    if ((int) ($result['queued'] ?? 0) === 0) {
        return response()->json([
            'message' => 'No emails were sent. Selected vendors have empty email addresses. Please update vendor email first.',
            'queued' => 0,
            'skipped' => (int) ($result['skipped'] ?? 0),
            'skipped_vendors' => $result['skipped_vendors'] ?? [],
            'rfq_number' => $result['rfq_number'] ?? null,
        ], 422);
    }

    $message = 'Email jobs queued for ' . $result['queued'] . ' vendor(s).';
    if (!empty($result['skipped'])) {
        $message .= ' Skipped ' . $result['skipped'] . ' vendor(s) due to missing email.';
    }

    return response()->json([
        'message' => $message,
        'queued' => $result['queued'],
        'skipped' => $result['skipped'],
        'skipped_vendors' => $result['skipped_vendors'] ?? [],
        'rfq_number' => $result['rfq_number'],
    ]);
}

public function getEmailHistory(Request $request)
{
    $qrId = $request->get('qr_id');

    $emailLogs = VendorEmailLog::where('ve_qr_id', $qrId)
        ->whereHas('vendor')  // Only include emails sent to valid vendors
        ->with('vendor')
        ->orderBy('ve_sent_at', 'desc')
        ->get();

    $html = view('store_procurement.quotation_request.partials.email_history_table', compact('emailLogs'))->render();

    return response()->json(['html' => $html]);
}

// ========================================================================
// GET SUBMITTED QUOTATIONS
// Get all quotations for a request with version history
// ========================================================================




public function getSubmittedQuotations(Request $request)
{
    $qrId = $request->get('qr_id');

    $quotations = VendorQuotation::where('quotation_request_id', $qrId)
        ->with('vendor')
        ->where('is_current', 1)
        ->orderBy('created_at', 'desc')
        ->get()
        ->filter()
        ->unique('vendor_id')
        ->values();

    if ($quotations->isEmpty()) {
        $quotations = VendorQuotation::where('quotation_request_id', $qrId)
            ->with('vendor')
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter()
            ->unique('vendor_id')
            ->values();
    }

    $quotationsWithHistory = $quotations->map(function ($quotation) use ($qrId) {
        // Get all version records for this vendor & request
        $historyRecords = DB::table('SP_VENDOR_QUOTATIONS_HISTORY')
            ->where('VENDOR_ID', $quotation->vendor_id)
            ->where('QUOTATION_REQUEST_ID', $qrId)
            ->orderBy('VERSION_NUMBER', 'desc')
            ->get();

        // Count how many versions this quotation has
        $versionCount = $historyRecords->count();

        // Convert each version record to a simple array (handles uppercase/lowercase DB fields)
        $versionList = $historyRecords->map(function ($record) {
            return [
                'VQH_ID'            => $record->VQH_ID ?? $record->vqh_id ?? null,
                'VERSION_NUMBER'    => $record->VERSION_NUMBER ?? $record->version_number ?? null,
                'QR_VERSION'        => $record->QR_VERSION ?? $record->qr_version ?? null,
                'IS_CURRENT'        => $record->IS_CURRENT ?? $record->is_current ?? 0,
                'VQ_ID'             => $record->VQ_ID ?? $record->vq_id ?? null,
                'VENDOR_ID'         => $record->VENDOR_ID ?? $record->vendor_id ?? null,
                'QUOTATION_REQUEST_ID' => $record->QUOTATION_REQUEST_ID ?? $record->quotation_request_id ?? null,
                'ITEM_TITLE'        => $record->ITEM_TITLE ?? $record->item_title ?? null,
                'BRAND'             => $record->BRAND ?? $record->brand ?? null,
                'MODEL'             => $record->MODEL ?? $record->model ?? null,
                'QUANTITY'          => $record->QUANTITY ?? $record->quantity ?? null,
                'RATE'              => $record->RATE ?? $record->rate ?? null,
                'PRICE'             => $record->PRICE ?? $record->price ?? null,
                'CURRENCY'          => $record->CURRENCY ?? $record->currency ?? null,
                'DELIVERY_TIME'     => $record->DELIVERY_TIME ?? $record->delivery_time ?? null,
                'ADVANCE_REQUIRED'  => $record->ADVANCE_REQUIRED ?? $record->advance_required ?? null,
                'ADVANCE_PERCENT'   => $record->ADVANCE_PERCENT ?? $record->advance_percent ?? null,
                'WARRANTY'          => $record->WARRANTY ?? $record->warranty ?? null,
                'SPECIFICATIONS'    => $record->SPECIFICATIONS ?? $record->specifications ?? null,
                'ATTACHMENT'        => $record->ATTACHMENT ?? $record->attachment ?? null,
                'REMARKS'           => $record->REMARKS ?? $record->remarks ?? null,
                'QUANTITY_QUOTED'   => $record->QUANTITY_QUOTED ?? $record->quantity_quoted ?? null,
                'CREATED_AT'        => $record->CREATED_AT ?? $record->created_at ?? null,
                'UPDATED_AT'        => $record->UPDATED_AT ?? $record->updated_at ?? null,
            ];
        });

        // Add version details inside the quotation object
        $quotation->version_history = $versionList;
        $quotation->version_count = $versionCount;

        // Return the final quotation with all its info
        return $quotation;
    });

    // âœ… STEP 5: Return response as JSON
    return response()->json([
        'quotations' => $quotationsWithHistory
    ]);
}


// ========================================================================
// CHECK IF QUOTATION EXISTS
// Check if vendor has submitted quotation (can create new version)
// ========================================================================
public function check(Request $request)
{
    // Get current quotation (is_current = 1) for this vendor and request
    $currentQuotation = VendorQuotation::where('quotation_request_id', $request->quotation_request_id)
        ->where('vendor_id', $request->vendor_id)
        ->where('is_current', 1)
        ->first();

    // Get total count of all quotations (including old versions) for this vendor
    $totalVersions = VendorQuotation::where('quotation_request_id', $request->quotation_request_id)
        ->where('vendor_id', $request->vendor_id)
        ->count();

    // Get version count from history table
    $historyVersions = 0;
    if ($currentQuotation) {
        $historyVersions = DB::table('SP_VENDOR_QUOTATIONS_HISTORY')
            ->where('VQ_ID', $currentQuotation->vq_id)
            ->count();
    }

    return response()->json([
        'exists' => $currentQuotation ? true : false,  // Current quotation exists
        'quotation' => $currentQuotation,
        'total_versions' => $totalVersions,  // Total quotations submitted
        'history_versions' => $historyVersions,  // Total versions in history
        'can_update' => true  // Always allow to create new version
    ]);
}


    // ========================================================================
    // STORE VENDOR QUOTATION
    // Save vendor response with version tracking and history
    // ========================================================================
    public function store(Request $request)
    {
        // Step 1: Validate all incoming data
        $request->validate([
            'vendor_id' => 'required|numeric',
            'quotation_request_id' => 'required|numeric',
            'item_title' => 'required|string|max:255',
            'brand' => 'nullable|string|max:150',
            'model' => 'nullable|string|max:150',
            'quantity' => 'required|numeric|min:0',
            'rate' => 'required|numeric|min:0',
            'price' => 'nullable|numeric',
            'currency' => 'required|string|max:100',
            'delivery_time' => 'required|string|max:100',
            'advance_required' => 'required|in:yes,no',
            'advance_percent' => 'nullable|numeric|min:0|max:100|required_if:advance_required,yes',
            'warranty' => 'nullable|string|max:500',
            'specifications' => 'nullable|string|max:500',
            'remarks' => 'nullable|string|max:500',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048'
        ], [
            'advance_required.required' => 'Please select Yes or No for advance payment.',
            'advance_required.in' => 'Advance payment choice is invalid.',
            'advance_percent.required_if' => 'Advance percentage is required when advance payment is Yes.',
            'advance_percent.max' => 'Advance percentage cannot exceed 100%.'
        ]);

        // Step 2: Start database transaction (rollback if error occurs)
        DB::beginTransaction();

        try {

            // Step 3: Get the quotation request to find current version
            $quotationRequest = QuotationRequest::findOrFail($request->quotation_request_id);
            $currentRequestVersion = $quotationRequest->current_version ?? 1;
            $requestedQuantity = $quotationRequest->requested_quantity ?? 0;

            // Step 4: Check if this vendor already submitted quotation for this request
            $existingQuotation = VendorQuotation::where('quotation_request_id', $request->quotation_request_id)
                ->where('vendor_id', $request->vendor_id)
                ->first();

            // Step 5: Prepare data to save
            $calculatedQuantity = (float) $request->input('quantity', 0);
            $calculatedRate = (float) $request->input('rate', 0);
            $calculatedPrice = round($calculatedQuantity * $calculatedRate, 2);

            $quotationData = $request->only([
                'vendor_id',
                'quotation_request_id',
                'item_title',
                'brand',
                'model',
                'currency',
                'warranty',
                'specifications',
                'delivery_time',
                'remarks'
            ]);

            $quotationData['quantity'] = $calculatedQuantity;
            $quotationData['rate'] = $calculatedRate;
            $advanceRequired = $request->advance_required === 'yes' ? 'yes' : 'no';
            $advancePercent = $advanceRequired === 'yes' ? (float) $request->advance_percent : null;
            $quotationData['advance_required'] = $advanceRequired;
            $quotationData['advance_percent'] = $advancePercent;
            $quotationData['price'] = $calculatedPrice;

            // Add version tracking fields
            $quotationData['qr_version'] = $currentRequestVersion;  // Which request version
            $quotationData['quantity_quoted'] = $requestedQuantity;  // Quantity from request
            $quotationData['is_current'] = 1;  // This is the current/latest quotation
            $hasStoreItemsSchema = app(StoreItemsSchemaService::class)->ensure();
            if ($hasStoreItemsSchema) {
                $quotationData['request_type'] = $quotationRequest->request_type;
            }

            // Step 6: Handle file attachment if uploaded
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();

                // Move file to public folder
                $file->move(public_path('assets/vendor_quotations'), $filename);

                // Save file path in data
                $quotationData['attachment'] = 'assets/vendor_quotations/' . $filename;
            }

            // Step 7: If quotation already exists, mark old one as not current
            if ($existingQuotation) {

                // Mark old quotation as not current
                $existingQuotation->is_current = 0;
                $existingQuotation->save();

                // Mark all old history entries as not current for this vendor and request
                DB::table('SP_VENDOR_QUOTATIONS_HISTORY')
                    ->where('VENDOR_ID', $request->vendor_id)
                    ->where('QUOTATION_REQUEST_ID', $request->quotation_request_id)
                    ->where('IS_CURRENT', 1)
                    ->update(['IS_CURRENT' => 0]);

                // Create new version (new quotation record)
                $newQuotation = VendorQuotation::create($quotationData);
                $vqId = $newQuotation->vq_id;

                // Get next version number for history (based on vendor and request, not VQ_ID)
                $lastHistoryVersion = DB::table('SP_VENDOR_QUOTATIONS_HISTORY')
                    ->where('VENDOR_ID', $request->vendor_id)
                    ->where('QUOTATION_REQUEST_ID', $request->quotation_request_id)
                    ->max('VERSION_NUMBER');
                $newVersionNumber = ($lastHistoryVersion ?? 0) + 1;

                // Save to history table (new version after negotiation)
                DB::table('SP_VENDOR_QUOTATIONS_HISTORY')->insert([
                    'QR_VERSION' => $currentRequestVersion,
                    'IS_CURRENT' => 1,  // This is current version
                    'QUANTITY_QUOTED' => $requestedQuantity,
                    'VQ_ID' => $vqId,
                    'VENDOR_ID' => $request->vendor_id,
                    'QUOTATION_REQUEST_ID' => $request->quotation_request_id,
                    'ITEM_TITLE' => $request->item_title,
                    'BRAND' => $request->brand,
                    'MODEL' => $request->model,
                    'QUANTITY' => $calculatedQuantity,
                    'RATE' => $calculatedRate,
                    'PRICE' => $calculatedPrice,
                    'CURRENCY' => $request->currency,
                    'DELIVERY_TIME' => $request->delivery_time,
                    'ADVANCE_REQUIRED' => $advanceRequired,
                    'ADVANCE_PERCENT' => $advancePercent,
                    'WARRANTY' => $request->warranty,
                    'SPECIFICATIONS' => $request->specifications,
                    'ATTACHMENT' => $quotationData['attachment'] ?? null,
                    'REMARKS' => $request->remarks,
                    'VERSION_NUMBER' => $newVersionNumber,
                    'CREATED_AT' => now(),
                    'UPDATED_AT' => now()
                ]);

                $message = 'Quotation updated successfully (Version ' . $newVersionNumber . ')';

            } else {

                // This is first time submission
                $newQuotation = VendorQuotation::create($quotationData);
                $vqId = $newQuotation->vq_id;

                // Save to history table (Version 1)
                DB::table('SP_VENDOR_QUOTATIONS_HISTORY')->insert([
                    'QR_VERSION' => $currentRequestVersion,
                    'IS_CURRENT' => 1,  // This is current version
                    'QUANTITY_QUOTED' => $requestedQuantity,
                    'VQ_ID' => $vqId,
                    'VENDOR_ID' => $request->vendor_id,
                    'QUOTATION_REQUEST_ID' => $request->quotation_request_id,
                    'ITEM_TITLE' => $request->item_title,
                    'BRAND' => $request->brand,
                    'MODEL' => $request->model,
                    'QUANTITY' => $calculatedQuantity,
                    'RATE' => $calculatedRate,
                    'PRICE' => $calculatedPrice,
                    'CURRENCY' => $request->currency,
                    'DELIVERY_TIME' => $request->delivery_time,
                    'ADVANCE_REQUIRED' => $advanceRequired,
                    'ADVANCE_PERCENT' => $advancePercent,
                    'WARRANTY' => $request->warranty,
                    'SPECIFICATIONS' => $request->specifications,
                    'ATTACHMENT' => $quotationData['attachment'] ?? null,
                    'REMARKS' => $request->remarks,
                    'VERSION_NUMBER' => 1,  // First version
                    'CREATED_AT' => now(),
                    'UPDATED_AT' => now()
                ]);

                $message = 'Quotation submitted successfully (Version 1)';
            }

            // Step 8: All done - commit to database
            DB::commit();

            // Step 9: Return success message
            return response()->json(['message' => $message]);

        } catch (\Exception $e) {

            // If any error occurs, rollback all changes
            DB::rollBack();

            // Return error message
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 422);
        }
    }



// ========================================================================
// GET ITEM STATUS FOR BUDGET LIST
// Shows email history and submitted quotations for a budget item
// ========================================================================
public function getItemStatus(Request $request)
{
    // Step 1: Get the budget item ID from request
    $itemId = $request->get('item_id');

    // Step 2: Find if this item has a quotation request
    $quotationRequest = \App\Models\StoreProcurement\QuotationRequest::where('item_id', $itemId)
        ->first();

    // Step 3: If no quotation request exists, return empty data
    if (!$quotationRequest) {
        return response()->json([
            'has_request' => false,
            'message' => 'No quotation request created yet'
        ]);
    }

    $qrId = $quotationRequest->qr_id;

    $user = auth()->user();
    $username = $user ? ($user->username ?? $user->EMP_AD_USERNAME ?? $user->name ?? null) : null;

    // Step 4: Get email history - which vendors received email
    $emailHistory = \App\Models\StoreProcurement\VendorEmailLog::where('ve_qr_id', $qrId)
        ->whereHas('vendor')  // Only include emails sent to valid vendors
        ->with('vendor')
        ->orderBy('ve_sent_at', 'desc')
        ->get();

    // Step 5: Get ALL submitted quotations (all versions) for this request
    $allQuotations = VendorQuotation::where('quotation_request_id', $qrId)
        ->whereHas('vendor')  // Only include quotations with valid vendor
        ->with('vendor')
        ->orderBy('vendor_id')
        ->orderBy('created_at', 'desc')
        ->get();

    // Step 6: Build simple data structure for frontend
    $emailedVendors = [];
    foreach ($emailHistory as $email) {
        $emailedVendors[] = [
            'vendor_id' => $email->ve_vendor_id,
            'vendor_name' => $email->vendor ? $email->vendor->vendor_name : 'Unknown',
            'sent_at' => $email->ve_sent_at,
            'email' => $email->vendor ? $email->vendor->vendor_email : '-'
        ];
    }

    // Group quotations by vendor and include ALL versions
    $respondedVendors = [];
    $quotationsByVendor = $allQuotations->groupBy('vendor_id');

    foreach ($quotationsByVendor as $vendorId => $vendorQuotations) {
        $vendor = $vendorQuotations->first()->vendor;

        // Get all versions for this vendor from history table
        $historyVersions = DB::table('SP_VENDOR_QUOTATIONS_HISTORY')
            ->where('VENDOR_ID', $vendorId)
            ->where('QUOTATION_REQUEST_ID', $qrId)
            ->orderBy('VERSION_NUMBER', 'desc')
            ->get();

        $versions = [];
        foreach ($historyVersions as $version) {
            $versions[] = [
                'version_number' => $version->version_number ?? $version->VERSION_NUMBER ?? 1,
                'is_current' => $version->is_current ?? $version->IS_CURRENT ?? 0,
                'price' => $version->price ?? $version->PRICE ?? 0,
                'quantity' => $version->quantity ?? $version->QUANTITY ?? null,
                'rate' => $version->rate ?? $version->RATE ?? null,
                'currency' => $version->currency ?? $version->CURRENCY ?? 'PKR',
                'delivery_time' => $version->delivery_time ?? $version->DELIVERY_TIME ?? '-',
                'warranty' => $version->warranty ?? $version->WARRANTY ?? '-',
                'specifications' => $version->specifications ?? $version->SPECIFICATIONS ?? '',
                'remarks' => $version->remarks ?? $version->REMARKS ?? '',
                'item_title' => $version->item_title ?? $version->ITEM_TITLE ?? '',
                'brand' => $version->brand ?? $version->BRAND ?? '',
                'model' => $version->model ?? $version->MODEL ?? '',
                'advance_required' => strtolower($version->advance_required ?? $version->ADVANCE_REQUIRED ?? 'no'),
                'advance_percent' => $version->advance_percent ?? $version->ADVANCE_PERCENT ?? null,
                'submitted_at' => $version->created_at ?? $version->CREATED_AT ?? now()
            ];
        }

        $respondedVendors[] = [
            'vendor_id' => $vendorId,
            'vendor_name' => $vendor ? $vendor->vendor_name : 'Unknown',
            'versions' => $versions
        ];
    }

    $chatMessages = QuotationChat::where('qr_id', $qrId)
        ->orderBy('chat_id')
        ->get(['entry_username', 'message_text', 'created_at']);

    // Step 7: Check if Purchase Order exists and get tracking info
    $purchaseOrder = PurchaseOrder::where('qr_id', $qrId)->first();
    $selectedVendorId = null;
    $selectedVendorQuotationId = null;
    $poStatus = null;
    $poNumber = null;
    $poId = null;
    $selectedVersionNumber = null;
    $approvalsList = [];

    if ($purchaseOrder) {
        $selectedVendorId = $purchaseOrder->vendor_id;
        $selectedVendorQuotationId = $purchaseOrder->vendor_quotation_id;
        $poStatus = $purchaseOrder->po_status; // 'pending', 'generated', etc.
        $poNumber = $purchaseOrder->po_no;
        $poId = $purchaseOrder->po_id;

        // Get the version number from vendor quotation
        if ($selectedVendorQuotationId) {
            $selectedQuotation = VendorQuotation::find($selectedVendorQuotationId);
            if ($selectedQuotation) {
                // Get version from history table
                $versionInfo = DB::table('SP_VENDOR_QUOTATIONS_HISTORY')
                    ->where('VENDOR_ID', $selectedQuotation->vendor_id)
                    ->where('QUOTATION_REQUEST_ID', $qrId)
                    ->where('IS_CURRENT', 1)
                    ->first();

                if ($versionInfo) {
                    $selectedVersionNumber = $versionInfo->version_number ?? $versionInfo->VERSION_NUMBER ?? 1;
                }
            }
        }

        // Get PO approvals list
        $approvals = SpPoApproval::where('po_id', $purchaseOrder->po_id)
            ->orderBy('slab_order', 'asc')
            ->get();

        foreach ($approvals as $approval) {
            // Get approver name from emp_employees
            $employee = DB::table('emp_employees')
                ->select('prefix', 'emp_f_name', 'emp_l_name')
                ->where('employee_id', $approval->approver_id)
                ->first();

            $approverName = 'Unknown';
            if ($employee) {
                $approverName = trim(($employee->prefix ?? '') . ' ' . ($employee->emp_f_name ?? '') . ' ' . ($employee->emp_l_name ?? ''));
            }

            $approvalsList[] = [
                'approver_name' => $approverName,
                'status' => $approval->status,
                'slab_order' => $approval->slab_order
            ];
        }
    }

    $chatQuery = QuotationChat::where('qr_id', $qrId)->where('is_read', 0);
    if ($username) {
        $chatQuery->where('entry_username', '!=', $username);
    }
    $unreadCount = $chatQuery->count();

    // Step 8: Return all data including PO tracking information
    return response()->json([
        'has_request' => true,
        'quotation_request' => [
            'qr_id' => $quotationRequest->qr_id,
            'item_name' => $quotationRequest->item_name,
            'requested_quantity' => $quotationRequest->requested_quantity,
            'current_version' => $quotationRequest->current_version ?? 1,
            'unread_chat_count' => $unreadCount,
        ],
        'emailed_vendors' => $emailedVendors,
        'responded_vendors' => $respondedVendors,
        'total_emailed' => count($emailedVendors),
        'total_responded' => count($respondedVendors),
        'chat_messages' => $chatMessages,
        'po_tracking' => [
            'selected_vendor_id' => $selectedVendorId,
            'selected_version' => $selectedVersionNumber,
            'po_status' => $poStatus,
            'po_number' => $poNumber,
            'po_id' => $poId,
            'approvals' => $approvalsList
        ]
    ]);
}

    // Template generation moved to JavaScript - no PHP library needed
////PO LIST FOR REVERSAL
public function spQuotations(){

    $hasStoreItemsSchema = app(StoreItemsSchemaService::class)->ensure();
    $vendors = Sp_vendor::orderBy('vendor_name')->get();
    $departments = Departments::orderBy('dept_name')->get();
    $budgetHeads = VendorCategory::orderBy('category_name')->get();
    $academic_years = \App\Models\AcademicYear::latest('ay_id')->get();

    $requestType = null;
    $pageTitle = 'Submit Vendor Quotation';

    $requests = QuotationRequest::where('department_head_approval', 'approved')
        ->where('visibility_to_vendors','yes')
        ->when($hasStoreItemsSchema, function ($query) {
            $query->whereNull('request_type');
        })
        ->orderBy('created_at', 'desc')
        ->get();

    foreach ($requests as $request) {
        $request->load(['budgetItem', 'budgetHead', 'vendorCategory', 'chatMessages']);
    }

    return view('store_procurement.quotation_request.sp_quotation_submit', compact('vendors', 'departments', 'budgetHeads', 'academic_years', 'requests', 'requestType', 'pageTitle'));
}

public function storeChat(Request $request)
{
    $request->validate([
        'qr_id' => 'required|exists:sp_quotation_request,qr_id',
        'sender_type' => 'required|string|max:20',
        'message_text' => 'required|string|max:1000',
    ]);

    $user = auth()->user();
    $username = $user ? ($user->username ?? $user->EMP_AD_USERNAME ?? $user->name ?? 'system') : 'system';
    $now = Carbon::now('Asia/Karachi');

    $chat = new QuotationChat();
    $chat->qr_id = $request->qr_id;
    $chat->sender_type = $request->sender_type;
    $chat->entry_username = $username;
    $chat->message_text = $request->message_text;
    $chat->is_read = 0;
    $chat->created_at = $now;
    $chat->updated_at = $now;
    $chat->save();

    return response()->json([
        'message' => 'Message sent successfully.',
        'chat' => [
            'entry_username' => $chat->entry_username,
            'message_text' => $chat->message_text,
            'created_at' => $now->format('d-M-Y h:i A'),
            'sender_type' => $chat->sender_type,
        ],
    ]);
}

public function markChatRead(Request $request)
{
    $request->validate([
        'qr_id' => 'required|exists:sp_quotation_request,qr_id',
    ]);

    $now = Carbon::now('Asia/Karachi');
    $user = auth()->user();
    $username = $user ? ($user->username ?? $user->EMP_AD_USERNAME ?? $user->name ?? null) : null;

    $chatQuery = QuotationChat::where('qr_id', $request->qr_id)
        ->where('is_read', 0);

    if ($username) {
        $chatQuery->where('entry_username', '!=', $username);
    }

    $chatQuery->update([
        'is_read' => 1,
        'read_at' => $now,
        'updated_at' => $now,
    ]);

    return response()->json(['message' => 'Messages marked as read.']);
}
public function po_list(){
    $vendors = Sp_vendor::orderBy('vendor_name')->get();
    $departments = Departments::orderBy('dept_name')->get();
    $budgetHeads = VendorCategory::orderBy('category_name')->get();


    return view('store_procurement.quotation_request.po_list', compact('vendors', 'departments', 'budgetHeads'));
}
public function searchPo(Request $request)
{
    $search = $request->get('q', '');

    if (strlen($search) < 2) {
        return response()->json([]);
    }

    $items = DB::table('sp_purchase_order')
        ->select('item_id', 'item_title')
        ->whereRaw('LOWER(item_title) LIKE ?', ['%' . strtolower($search) . '%'])
        ->orderBy('item_title')
        ->limit(20)
        ->get();

    return response()->json($items);
}

}






