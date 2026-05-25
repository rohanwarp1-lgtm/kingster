@php
    $logoPath = public_path('uploads/general_settings/kingster-white-logo.png');
    $logoB64  = (file_exists($logoPath) && is_readable($logoPath))
                    ? base64_encode(file_get_contents($logoPath))
                    : null;
    $logoSrc  = $logoB64 ? 'data:image/png;base64,' . $logoB64 : null;

    $themes = [
        'approved' => [
            'gradient'     => 'linear-gradient(135deg,#2ecc71 0%,#1a9e55 100%)',
            'btn_gradient' => 'linear-gradient(135deg,#2ecc71 0%,#1a9e55 100%)',
            'icon_bg'      => 'rgba(255,255,255,0.18)',
            'icon_char'    => '&#10003;',
            'badge_bg'     => '#d4f7e5',
            'badge_color'  => '#0d6e3b',
            'badge_label'  => 'ACTIVATED',
            'tbl_hdr'      => 'linear-gradient(135deg,#2ecc71 0%,#1a9e55 100%)',
        ],
        'rejected' => [
            'gradient'     => 'linear-gradient(135deg,#e74c3c 0%,#c0392b 100%)',
            'btn_gradient' => 'linear-gradient(135deg,#e74c3c 0%,#c0392b 100%)',
            'icon_bg'      => 'rgba(255,255,255,0.18)',
            'icon_char'    => '&#10005;',
            'badge_bg'     => '#fde8e8',
            'badge_color'  => '#922b21',
            'badge_label'  => 'NOT APPROVED',
            'tbl_hdr'      => 'linear-gradient(135deg,#e74c3c 0%,#c0392b 100%)',
        ],
        'expired'  => [
            'gradient'     => 'linear-gradient(135deg,#636e8a 0%,#3d4566 100%)',
            'btn_gradient' => 'linear-gradient(135deg,#636e8a 0%,#3d4566 100%)',
            'icon_bg'      => 'rgba(255,255,255,0.18)',
            'icon_char'    => '&#8987;',
            'badge_bg'     => '#e8eaf0',
            'badge_color'  => '#3d4566',
            'badge_label'  => 'EXPIRED',
            'tbl_hdr'      => 'linear-gradient(135deg,#636e8a 0%,#3d4566 100%)',
        ],
        'pending'  => [
            'gradient'     => 'linear-gradient(135deg,#f39c12 0%,#d68910 100%)',
            'btn_gradient' => 'linear-gradient(135deg,#f39c12 0%,#d68910 100%)',
            'icon_bg'      => 'rgba(255,255,255,0.18)',
            'icon_char'    => '&#8943;',
            'badge_bg'     => '#fef9e7',
            'badge_color'  => '#856404',
            'badge_label'  => 'PENDING',
            'tbl_hdr'      => 'linear-gradient(135deg,#f39c12 0%,#d68910 100%)',
        ],
    ];
    $t = $themes[$status] ?? $themes['pending'];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="margin:0;padding:0;background:#eef0f7;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#eef0f7;padding:36px 16px;">
<tr><td align="center">

    {{-- Outer card --}}
    <table width="600" cellpadding="0" cellspacing="0" border="0"
           style="max-width:600px;width:100%;background:#ffffff;border-radius:20px;overflow:hidden;
                  box-shadow:0 12px 48px rgba(0,0,0,0.12);">

        {{-- ── HEADER ──────────────────────────────────────────────── --}}
        <tr>
            <td style="background:{{ $t['gradient'] }};padding:40px 40px 36px;text-align:center;">

                {{-- Logo --}}
                @if($logoSrc)
                <img src="{{ $logoSrc }}" alt="Kingster"
                     style="height:44px;max-width:176px;display:block;margin:0 auto 24px;
                            filter:drop-shadow(0 2px 6px rgba(0,0,0,0.15));">
                @else
                <p style="color:#fff;font-size:20px;font-weight:800;letter-spacing:4px;
                           margin:0 0 24px;text-transform:uppercase;
                           text-shadow:0 2px 6px rgba(0,0,0,0.2);">KINGSTER</p>
                @endif

                {{-- Status icon circle (table-based, Gmail-safe) --}}
                <table cellpadding="0" cellspacing="0" border="0" style="margin:0 auto 18px;">
                    <tr>
                        <td width="60" height="60" align="center" valign="middle"
                            style="width:60px;height:60px;
                                   background:{{ $t['icon_bg'] }};
                                   border-radius:50%;
                                   border:2px solid rgba(255,255,255,0.35);
                                   font-size:26px;color:#ffffff;font-weight:700;
                                   line-height:60px;text-align:center;">
                            {!! $t['icon_char'] !!}
                        </td>
                    </tr>
                </table>

                {{-- Title --}}
                <h1 style="color:#ffffff;margin:0 0 8px;font-size:24px;font-weight:800;
                            letter-spacing:-0.3px;line-height:1.2;
                            text-shadow:0 2px 8px rgba(0,0,0,0.15);">
                    {{ $headerTitle }}
                </h1>
                <p style="color:rgba(255,255,255,0.85);margin:0;font-size:14px;font-weight:400;">
                    Ticket:&nbsp;<strong style="letter-spacing:0.5px;">{{ $warranty->ticket_no }}</strong>
                </p>

            </td>
        </tr>

        {{-- ── STATUS BADGE ─────────────────────────────────────────── --}}
        <tr>
            <td style="background:#f7f8fd;padding:14px 40px;text-align:center;
                        border-bottom:1px solid #eaecf5;">
                <span style="display:inline-block;
                              background:{{ $t['badge_bg'] }};
                              color:{{ $t['badge_color'] }};
                              padding:7px 24px;
                              border-radius:50px;
                              font-size:11px;font-weight:800;
                              letter-spacing:1.5px;
                              border:1px solid {{ $t['badge_color'] }}22;">
                    &#9679;&ensp;{{ $t['badge_label'] }}
                </span>
            </td>
        </tr>

        {{-- ── BODY CONTENT ─────────────────────────────────────────── --}}
        <tr>
            <td style="padding:36px 40px 24px;">
                <div style="color:#3c4072;font-size:15px;line-height:1.8;">
                    {!! $body !!}
                </div>
            </td>
        </tr>

        {{-- ── WARRANTY DETAILS TABLE ────────────────────────────────── --}}
        <tr>
            <td style="padding:0 40px 36px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                       style="border-radius:12px;overflow:hidden;
                              border:1px solid #e4e7f3;">

                    {{-- Table header --}}
                    <tr>
                        <td colspan="2" style="background:{{ $t['tbl_hdr'] }};padding:13px 20px;">
                            <span style="color:#fff;font-size:12px;font-weight:700;
                                          text-transform:uppercase;letter-spacing:1px;">
                                &#128196;&ensp;Warranty Details
                            </span>
                        </td>
                    </tr>

                    {{-- Rows --}}
                    <tr style="background:#ffffff;">
                        <td style="padding:12px 20px;border-bottom:1px solid #eaecf5;
                                    font-size:13px;color:#74788d;font-weight:500;width:42%;">
                            Ticket Number
                        </td>
                        <td style="padding:12px 20px;border-bottom:1px solid #eaecf5;
                                    font-size:13px;color:#3c4072;font-weight:700;
                                    letter-spacing:0.3px;">
                            {{ $warranty->ticket_no }}
                        </td>
                    </tr>
                    <tr style="background:#f7f8fd;">
                        <td style="padding:12px 20px;border-bottom:1px solid #eaecf5;
                                    font-size:13px;color:#74788d;font-weight:500;">
                            Customer
                        </td>
                        <td style="padding:12px 20px;border-bottom:1px solid #eaecf5;
                                    font-size:13px;color:#3c4072;">
                            {{ $warranty->customer_name }}
                        </td>
                    </tr>
                    <tr style="background:#ffffff;">
                        <td style="padding:12px 20px;border-bottom:1px solid #eaecf5;
                                    font-size:13px;color:#74788d;font-weight:500;">
                            Product
                        </td>
                        <td style="padding:12px 20px;border-bottom:1px solid #eaecf5;
                                    font-size:13px;color:#3c4072;">
                            {{ $warranty->product_name }}
                        </td>
                    </tr>
                    <tr style="background:#f7f8fd;">
                        <td style="padding:12px 20px;border-bottom:1px solid #eaecf5;
                                    font-size:13px;color:#74788d;font-weight:500;">
                            Model
                        </td>
                        <td style="padding:12px 20px;border-bottom:1px solid #eaecf5;
                                    font-size:13px;color:#3c4072;">
                            {{ $warranty->model ?? '—' }}
                        </td>
                    </tr>
                    @if($warranty->expiry_date)
                    <tr style="background:#ffffff;">
                        <td style="padding:12px 20px;font-size:13px;color:#74788d;font-weight:500;">
                            Expiry Date
                        </td>
                        <td style="padding:12px 20px;font-size:13px;color:#3c4072;font-weight:600;">
                            {{ $warranty->expiry_date->format('d M Y') }}
                        </td>
                    </tr>
                    @endif

                </table>
            </td>
        </tr>

        {{-- ── CTA BUTTON ────────────────────────────────────────────── --}}
        <tr>
            <td style="padding:0 40px 40px;text-align:center;">
                <a href="{{ url('/warranty-status-lookup') }}"
                   style="display:inline-block;
                          background:{{ $t['btn_gradient'] }};
                          color:#ffffff;text-decoration:none;
                          padding:15px 40px;border-radius:10px;
                          font-size:14px;font-weight:700;
                          letter-spacing:0.4px;
                          box-shadow:0 4px 14px rgba(0,0,0,0.15);">
                    View Warranty Status
                </a>
            </td>
        </tr>

        {{-- ── DIVIDER ───────────────────────────────────────────────── --}}
        <tr>
            <td style="padding:0 40px;">
                <hr style="border:none;border-top:1px solid #eaecf5;margin:0;">
            </td>
        </tr>

        {{-- ── FOOTER ────────────────────────────────────────────────── --}}
        <tr>
            <td style="padding:28px 40px 32px;text-align:center;background:#f7f8fd;">
                <p style="margin:0 0 6px;font-size:13px;color:#74788d;">
                    Need help?&nbsp;
                    <a href="mailto:support@kingster.info"
                       style="color:#667eea;text-decoration:none;font-weight:600;">
                        support@kingster.info
                    </a>
                </p>
                <p style="margin:0 0 12px;font-size:11.5px;color:#adb5bd;">
                    This is an automated message. Please do not reply directly to this email.
                </p>
                <p style="margin:0;font-size:11px;color:#c5c8d8;">
                    &copy;&nbsp;{{ date('Y') }}&nbsp;Kingster. All rights reserved.
                </p>
            </td>
        </tr>

    </table>
    {{-- /card --}}

</td></tr>
</table>

</body>
</html>
