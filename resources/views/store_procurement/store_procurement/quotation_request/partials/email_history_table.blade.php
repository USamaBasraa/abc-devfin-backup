@php
    $emailLogs = $emailLogs ?? collect();
    $emailPo = $emailPo ?? null;
    $rfqNumberDisplay = $rfqNumber ?? 'N/A';
@endphp

@if($emailLogs->count() > 0)
<div class="table-responsive">
    <h6><strong><i class="fa fa-history text-info"></i> Email History</strong></h6>
    <table class="table table-sm table-bordered">
        <thead class="bg-light">
            <tr>
                <th>Vendor</th>
                <th>RFQ No.</th>
                <th>Date & Time</th>
                <th>Status</th>
                <th class="text-center">RFQ PDF</th>
            </tr>
        </thead>
        <tbody>
            @foreach($emailLogs as $log)
                @php
                    $rfqDisplay = $log->ve_rfq_no ?? 'N/A';
                    $pdfRouteParams = ['id' => $log->ve_qr_id];
                    if (!empty($log->ve_rfq_no)) {
                        $pdfRouteParams['rfq_no'] = $log->rfq_no_encrypted;
                    }
                @endphp
                <tr>
                    <td>{{ $log->vendor->vendor_name ?? '-' }}</td>
                    <td>{{ $rfqDisplay }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->ve_sent_at)->format('d-M-Y h:i A') }}</td>
                    <td>
                        @if($log->ve_status == 'sent')
                            <span class="badge badge-success">
                                <i class="fa fa-check"></i> Sent
                            </span>
                        @elseif($log->ve_status == 'failed')
                            <span class="badge badge-danger">
                                <i class="fa fa-times"></i> Failed
                            </span>
                        @else
                            <span class="badge badge-warning">
                                <i class="fa fa-clock-o"></i> Pending
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(!empty($log->ve_rfq_no))
                            <a href="{{ route('quotation-request.rfq-pdf', $pdfRouteParams) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-file-pdf-o"></i> PDF
                            </a>
                        @else
                            <span class="text-muted small">N/A</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="alert alert-info">
    <i class="fa fa-info-circle"></i> No emails sent yet for this quotation request.
</div>
@endif

