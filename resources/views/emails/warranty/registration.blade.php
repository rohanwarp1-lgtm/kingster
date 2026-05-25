@php
    $logoPath = public_path('uploads/general_settings/kingster-white-logo.png');
    $logoB64  = (file_exists($logoPath) && is_readable($logoPath))
                    ? base64_encode(file_get_contents($logoPath))
                    : null;
    $logoSrc  = $logoB64 ? 'data:image/png;base64,' . $logoB64 : null;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f0f2f8;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f0f2f8;padding:40px 20px;">
    <tr>
        <td align="center">

            {{-- Card --}}
            <table width="620" cellpadding="0" cellspacing="0" border="0" style="max-width:620px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,0.10);">

                {{-- Header --}}
                <tr>
                    <td style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:36px 40px;text-align:center;">
                        @if($logoSrc)
                        <img src="{{ $logoSrc }}" alt="Kingster"
                             style="height:44px;max-width:180px;display:block;margin:0 auto;
                                    filter:drop-shadow(0 2px 6px rgba(0,0,0,0.15));">
                        @else
                        <p style="color:#fff;font-size:20px;font-weight:800;letter-spacing:4px;margin:0;text-transform:uppercase;">KINGSTER</p>
                        @endif
                        <h1 style="color:#ffffff;margin:18px 0 4px;font-size:22px;font-weight:700;letter-spacing:0.3px;line-height:1.3;">
                            Warranty Registration Received
                        </h1>
                        <p style="color:rgba(255,255,255,0.82);margin:0;font-size:14px;">
                            Thank you for choosing Kingster
                        </p>
                    </td>
                </tr>

                {{-- Status Badge --}}
                <tr>
                    <td style="background:#f8f9ff;padding:14px 40px;text-align:center;border-bottom:1px solid #eef0f8;">
                        <span style="display:inline-block;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:6px 22px;border-radius:50px;font-size:12px;font-weight:600;letter-spacing:1px;text-transform:uppercase;">
                            &#9679;&nbsp; Under Review
                        </span>
                    </td>
                </tr>

                {{-- Body Content --}}
                <tr>
                    <td style="padding:36px 40px 24px;">
                        <div style="color:#3d4166;font-size:15px;line-height:1.75;">
                            {!! $body !!}
                        </div>
                    </td>
                </tr>

                {{-- Warranty Details Table --}}
                <tr>
                    <td style="padding:0 40px 36px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="background:#f8f9ff;border-radius:10px;overflow:hidden;border:1px solid #e8eaf6;">
                            <tr>
                                <td colspan="2" style="background:linear-gradient(135deg,#667eea,#764ba2);padding:12px 20px;">
                                    <p style="color:#fff;margin:0;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;">
                                        &#128196;&nbsp; Warranty Details
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:11px 20px;border-bottom:1px solid #e8eaf6;font-size:13px;color:#74788d;font-weight:500;width:45%;">Ticket Number</td>
                                <td style="padding:11px 20px;border-bottom:1px solid #e8eaf6;font-size:13px;color:#3d4166;font-weight:700;">{{ $warranty->ticket_no }}</td>
                            </tr>
                            <tr style="background:#fff;">
                                <td style="padding:11px 20px;border-bottom:1px solid #e8eaf6;font-size:13px;color:#74788d;font-weight:500;">Product Name</td>
                                <td style="padding:11px 20px;border-bottom:1px solid #e8eaf6;font-size:13px;color:#3d4166;">{{ $warranty->product_name }}</td>
                            </tr>
                            <tr>
                                <td style="padding:11px 20px;border-bottom:1px solid #e8eaf6;font-size:13px;color:#74788d;font-weight:500;">Model</td>
                                <td style="padding:11px 20px;border-bottom:1px solid #e8eaf6;font-size:13px;color:#3d4166;">{{ $warranty->model ?? '—' }}</td>
                            </tr>
                            <tr style="background:#fff;">
                                <td style="padding:11px 20px;border-bottom:1px solid #e8eaf6;font-size:13px;color:#74788d;font-weight:500;">Serial Number</td>
                                <td style="padding:11px 20px;border-bottom:1px solid #e8eaf6;font-size:13px;color:#3d4166;">{{ $warranty->serial_number ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td style="padding:11px 20px;border-bottom:1px solid #e8eaf6;font-size:13px;color:#74788d;font-weight:500;">Purchase Platform</td>
                                <td style="padding:11px 20px;border-bottom:1px solid #e8eaf6;font-size:13px;color:#3d4166;">{{ $warranty->purchase_platform ?? '—' }}</td>
                            </tr>
                            <tr style="background:#fff;">
                                <td style="padding:11px 20px;border-bottom:1px solid #e8eaf6;font-size:13px;color:#74788d;font-weight:500;">Purchase Date</td>
                                <td style="padding:11px 20px;border-bottom:1px solid #e8eaf6;font-size:13px;color:#3d4166;">{{ optional($warranty->purchase_date)->format('d M Y') ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td style="padding:11px 20px;font-size:13px;color:#74788d;font-weight:500;">Warranty Status</td>
                                <td style="padding:11px 20px;font-size:13px;">
                                    <span style="background:#fff3cd;color:#856404;padding:3px 12px;border-radius:50px;font-size:12px;font-weight:600;">Pending Review</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- CTA Button --}}
                <tr>
                    <td style="padding:0 40px 40px;text-align:center;">
                        <a href="{{ url('/warranty-status-lookup') }}"
                           style="display:inline-block;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#ffffff;text-decoration:none;padding:14px 36px;border-radius:8px;font-size:14px;font-weight:600;letter-spacing:0.3px;">
                            Track My Warranty Status
                        </a>
                    </td>
                </tr>

                {{-- Divider --}}
                <tr>
                    <td style="padding:0 40px;">
                        <hr style="border:none;border-top:1px solid #eef0f8;margin:0;">
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="padding:28px 40px;text-align:center;">
                        <p style="margin:0 0 6px;font-size:13px;color:#74788d;">
                            Need help? Email us at
                            <a href="mailto:support@kingster.info" style="color:#667eea;text-decoration:none;font-weight:500;">support@kingster.info</a>
                        </p>
                        <p style="margin:0 0 14px;font-size:12px;color:#adb5bd;">
                            This is an automated email. Please do not reply directly to this message.
                        </p>
                        <p style="margin:0;font-size:12px;color:#c0c4d6;">
                            &copy; {{ date('Y') }} Kingster. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>
            {{-- /Card --}}

        </td>
    </tr>
</table>

</body>
</html>
