<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request For Quotation</title>
</head>
<body style="margin:0; padding:0; background-color:#f5f5f5; font-family: 'Segoe UI', Tahoma, Arial, sans-serif; color:#111111;">
<div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent;">
    RFQ {{ $rfqNumber }} is open. Please share your best quote to stay in consideration.
</div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f5f5; padding:28px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="720" cellpadding="0" cellspacing="0" style="width:720px; max-width:720px; background:#ffffff; border:1px solid #d7dce2; border-radius:12px; overflow:hidden;">
                <tr>
                    <td style="background:#ffffff; padding:20px 28px; border-bottom:1px solid #d7dce2;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="vertical-align:middle; width:76px;">
                                    <img src="https://cms.bnu.edu.pk/assets/images/bg/bnu_logo.png" alt="BNU Logo" style="height:52px; width:auto; display:block;">
                                </td>
                                <td style="vertical-align:middle; color:#111111;">
                                    <div style="font-size:24px; line-height:1.2; font-weight:700; letter-spacing:0.2px;">Beaconhouse National University</div>
                                    <div style="margin-top:4px; font-size:13px; color:#4b5563;">Store Procurement Division</div>
                                </td>
                                <td align="right" style="vertical-align:middle;">
                                    <span style="display:inline-block; background:#111111; color:#ffffff; font-size:12px; font-weight:700; padding:6px 10px; border-radius:999px;">
                                        RFQ NOTICE
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:28px 32px 14px 32px;">
                        <div style="font-size:12px; font-weight:700; color:#111111; letter-spacing:0.8px; text-transform:uppercase;">Vendor Quotation Request</div>
                        <div style="font-size:22px; font-weight:800; color:#111111; margin-top:6px;">Request for Quotation (RFQ) Invitation</div>
                        <div style="font-size:15px; line-height:1.7; color:#111111; margin-top:12px;">Dear {{ $vendorName }},</div>
                        <div style="font-size:15px; line-height:1.7; color:#111111; margin-top:6px;">
                            We are pleased to invite you for RFQ <strong>{{ $rfqNumber }}</strong>.
                            If this scope matches your capability, please share your most competitive and complete offer.
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 32px 8px 32px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:separate; border-spacing:0; border:1px solid #d0d0d0; border-radius:8px; overflow:hidden;">
                            <tr>
                                <td style="width:34%; padding:12px 14px; border-right:1px solid #d0d0d0; border-bottom:1px solid #d0d0d0; background:#fafafa; font-size:13px; font-weight:700; color:#111111;">RFQ Number</td>
                                <td style="padding:12px 14px; border-bottom:1px solid #d0d0d0; font-size:13px; color:#111111;">{{ $rfqNumber }}</td>
                            </tr>
                            <tr>
                                <td style="padding:12px 14px; border-right:1px solid #d0d0d0; border-bottom:1px solid #d0d0d0; background:#fafafa; font-size:13px; font-weight:700; color:#111111;">Quotation Request ID</td>
                                <td style="padding:12px 14px; border-bottom:1px solid #d0d0d0; font-size:13px; color:#111111;">{{ $quotationRequestId }}</td>
                            </tr>
                            <tr>
                                <td style="padding:12px 14px; border-right:1px solid #d0d0d0; border-bottom:1px solid #d0d0d0; background:#fafafa; font-size:13px; font-weight:700; color:#111111;">Request Version</td>
                                <td style="padding:12px 14px; border-bottom:1px solid #d0d0d0; font-size:13px; color:#111111;">{{ $quotationVersion }}</td>
                            </tr>
                            <tr>
                                <td style="padding:12px 14px; border-right:1px solid #d0d0d0; background:#fafafa; font-size:13px; font-weight:700; color:#111111;">Item Description</td>
                                <td style="padding:12px 14px; font-size:13px; color:#111111;">{{ $itemTitle }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:8px 32px 24px 32px;">
                        <div style="border:1px solid #d0d0d0; background:#fafafa; border-radius:6px; padding:12px 14px; font-size:13px; color:#111111; line-height:1.6;">
                            <strong>What helps your quote stand out:</strong> clear pricing, realistic delivery timeline, warranty/support terms, and complete compliance details.
                            The attached RFQ PDF is the official reference for scope and requirements.
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 32px 26px 32px;">
                        <div style="font-size:14px; color:#111111; line-height:1.7;">
                            Regards,<br>
                            <strong>Store Procurement Team</strong><br>
                            Beaconhouse National University
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="background:#f3f4f6; border-top:1px solid #d0d0d0; padding:14px 32px;">
                        <div style="font-size:11px; line-height:1.6; color:#4b5563;">
                            This is an official procurement communication from Beaconhouse National University.
                            For any clarification, please reply through the approved procurement channel.
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
