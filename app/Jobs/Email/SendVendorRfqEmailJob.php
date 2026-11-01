<?php

namespace App\Jobs\Email;

use App\Models\StoreProcurement\Sp_vendor;
use App\Models\StoreProcurement\VendorEmailLog;
use App\Services\StoreProcurement\RfqPdfService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

class SendVendorRfqEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    private $quotationRequestId;
    private $vendorId;
    private $toEmail;
    private $subjectLine;
    private $rfqNumber;
    private $itemTitle;
    private $vendorName;
    private $quotationVersion;
    private $logId;

    public function __construct(
        int $quotationRequestId,
        int $vendorId,
        string $toEmail,
        string $subjectLine,
        string $rfqNumber,
        string $itemTitle,
        string $vendorName,
        int $quotationVersion,
        int $logId
    ) {
        $this->quotationRequestId = $quotationRequestId;
        $this->vendorId = $vendorId;
        $this->toEmail = $toEmail;
        $this->subjectLine = $subjectLine;
        $this->rfqNumber = $rfqNumber;
        $this->itemTitle = $itemTitle;
        $this->vendorName = $vendorName;
        $this->quotationVersion = $quotationVersion;
        $this->logId = $logId;
    }

    public function handle(): void
    {
        try {
            $vendor = Sp_vendor::find($this->vendorId);

            if (!$vendor) {
                throw new RuntimeException('Vendor not found for RFQ email job.');
            }

            $pdf = app(RfqPdfService::class)->buildForVendor(
                $this->quotationRequestId,
                $vendor,
                $this->rfqNumber
            );

            $viewData = [
                'vendorName' => $this->vendorName,
                'rfqNumber' => $this->rfqNumber,
                'itemTitle' => $this->itemTitle,
                'quotationRequestId' => $this->quotationRequestId,
                'quotationVersion' => $this->quotationVersion,
                'portalUrl' => url('/sp/quotations'),
            ];

            // If MAIL_FORCE_TO is set, all emails go to one testing inbox.
            $forceTo = config('mail.force_to');
            $recipientEmail = !empty($forceTo) ? $forceTo : $this->toEmail;

            Mail::send('emails.store_procurement.vendor_rfq', $viewData, function ($message) use ($pdf, $recipientEmail) {
                $message->to($recipientEmail)
                    ->subject($this->subjectLine)
                    ->attachData($pdf['content'], $pdf['filename'], ['mime' => 'application/pdf']);
            });

            $remark = sprintf(
                'Email sent with RFQ PDF (Version %d). Recipient: %s',
                $this->quotationVersion,
                $recipientEmail
            );

            $this->updateLog('sent', $remark);
        } catch (Throwable $exception) {
            $this->updateLog('failed', 'Email failed: ' . mb_substr($exception->getMessage(), 0, 400));
            throw $exception;
        }
    }

    public function failed(Throwable $exception): void
    {
        $this->updateLog('failed', 'Email failed: ' . mb_substr($exception->getMessage(), 0, 400));
    }

    private function updateLog(string $status, string $remarks): void
    {
        VendorEmailLog::where('ve_id', $this->logId)->update([
            've_status' => $status,
            've_remarks' => $remarks,
            've_sent_at' => Carbon::now('Asia/Karachi'),
        ]);
    }
}
