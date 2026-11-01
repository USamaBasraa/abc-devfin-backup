<?php

namespace App\Services\StoreProcurement;

use App\Jobs\Email\SendVendorRfqEmailJob;
use App\Models\StoreProcurement\QuotationRequest;
use App\Models\StoreProcurement\Sp_vendor;
use App\Models\StoreProcurement\VendorEmailLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VendorQuotationEmailService
{
    /**
     * Queue vendor emails and insert logs first.
     */
    public function queueRfqEmails(int $requestId, array $vendorIds, string $sentBy): array
    {
        $vendorIds = collect($vendorIds)
            ->filter()
            ->map(function ($id) {
                return (int) $id;
            })
            ->unique()
            ->values()
            ->all();

        $quotationRequest = QuotationRequest::with('budgetItem')->findOrFail($requestId);
        $currentVersion = (int) ($quotationRequest->current_version ?? 1);
        $itemTitle = (string) ($quotationRequest->budgetItem->item_title ?? 'Item');
        $subjectLine = 'Quotation Request - ' . $itemTitle;
        $rfqNumber = $this->nextRfqNumber($requestId);

        $vendors = Sp_vendor::whereIn('vendor_id', $vendorIds)
            ->orderBy('vendor_name')
            ->get();

        $queuedCount = 0;
        $skippedCount = 0;
        $skippedVendors = [];

        foreach ($vendors as $vendor) {
            if (empty($vendor->vendor_email)) {
                $skippedCount++;
                $skippedVendors[] = (string) ($vendor->vendor_name ?? ('Vendor #' . $vendor->vendor_id));
                continue;
            }

            $logId = DB::select("SELECT sp_vendor_email_log_seq.NEXTVAL as next_id FROM DUAL")[0]->next_id;

            VendorEmailLog::create([
                've_id' => $logId,
                've_qr_id' => $requestId,
                've_qr_version' => $currentVersion,
                've_vendor_id' => $vendor->vendor_id,
                've_sent_by' => $sentBy,
                've_sent_at' => Carbon::now('Asia/Karachi'),
                've_email_subject' => $subjectLine,
                've_status' => 'queued',
                've_remarks' => 'Email queued for vendor quotation submission (Version ' . $currentVersion . ').',
                've_rfq_no' => $rfqNumber,
            ]);

            SendVendorRfqEmailJob::dispatchSync(
                $requestId,
                (int) $vendor->vendor_id,
                (string) $vendor->vendor_email,
                $subjectLine,
                $rfqNumber,
                $itemTitle,
                (string) ($vendor->vendor_name ?? 'Vendor'),
                $currentVersion,
                (int) $logId
            );

            $queuedCount++;
        }

        return [
            'queued' => $queuedCount,
            'skipped' => $skippedCount,
            'skipped_vendors' => $skippedVendors,
            'rfq_number' => $rfqNumber,
        ];
    }

    private function nextRfqNumber(int $requestId): string
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
}

