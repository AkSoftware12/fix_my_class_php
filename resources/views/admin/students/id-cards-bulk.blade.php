<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Cards – Bulk Print</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #f1f5f9;
            padding: 1.5rem;
        }

        /* ── Print bar ── */
        .print-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #1e3a8a;
            color: #fff;
            padding: .75rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .print-bar span { font-size: .9rem; }
        .print-bar button {
            background: #fff;
            color: #1e3a8a;
            border: none;
            padding: .4rem 1.1rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: .88rem;
        }

        /* ── Grid ── */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 380px);
            gap: 1.5rem;
            justify-content: center;
        }

        /* ── Card shell ── */
        .id-card {
            width: 380px;
            border-radius: 20px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 8px 32px rgba(37,99,235,.18), 0 2px 8px rgba(0,0,0,.08);
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        /* ── Top header ── */
        .id-top {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
            color: #fff;
            padding: .9rem 1.1rem .8rem;
            display: flex;
            align-items: center;
            gap: .7rem;
        }
        .id-logo-wrap {
            width: 42px; height: 42px;
            background: rgba(255,255,255,.18);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        .id-institute { flex: 1; min-width: 0; }
        .id-institute-name { font-size: .88rem; font-weight: 700; line-height: 1.2; }
        .id-institute-branch { font-size: .72rem; opacity: .82; margin-top: .1rem; }
        .id-type-badge {
            background: rgba(255,255,255,.22);
            border: 1px solid rgba(255,255,255,.35);
            color: #fff;
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .1em;
            padding: .18rem .55rem;
            border-radius: 20px;
            flex-shrink: 0;
        }

        /* ── Golden accent strip ── */
        .id-divider {
            height: 6px;
            background: linear-gradient(90deg, #f59e0b, #fbbf24, #fcd34d);
        }

        /* ── Body ── */
        .id-body {
            display: flex;
            gap: 1rem;
            padding: 1rem 1.1rem 1rem;
        }
        .id-left {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .6rem;
            flex-shrink: 0;
        }
        .id-photo-wrap {
            width: 90px; height: 90px;
            border-radius: 12px;
            overflow: hidden;
            border: 3px solid #dbeafe;
            box-shadow: 0 2px 8px rgba(37,99,235,.15);
        }
        .id-photo { width: 100%; height: 100%; object-fit: cover; display: block; }
        .id-qr-wrap { text-align: center; }
        .id-qr-wrap canvas, .id-qr-wrap img { border-radius: 6px; }
        .id-qr-label { font-size: .6rem; color: #94a3b8; margin-top: .2rem; letter-spacing: .03em; }

        .id-right { flex: 1; min-width: 0; }
        .id-name { font-size: 1rem; font-weight: 700; color: #1e293b; line-height: 1.2; margin-bottom: .15rem; }
        .id-adm {
            font-size: .72rem; font-weight: 600; color: #2563eb;
            background: #eff6ff; display: inline-block;
            padding: .1rem .5rem; border-radius: 20px;
            margin-bottom: .6rem; letter-spacing: .04em;
        }
        .id-fields { display: flex; flex-direction: column; gap: .3rem; }
        .id-field {
            display: flex; flex-direction: column;
            background: #f8fafc;
            border-left: 3px solid #2563eb;
            border-radius: 0 6px 6px 0;
            padding: .2rem .5rem;
        }
        .id-field-label { font-size: .62rem; color: #94a3b8; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; }
        .id-field-label i { font-size: .65rem; margin-right: .15rem; }
        .id-field-val { font-size: .78rem; color: #1e293b; font-weight: 500; line-height: 1.3; }

        /* ── Footer ── */
        .id-footer {
            background: #f1f5f9;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: .65rem;
            color: #94a3b8;
            padding: .4rem 1rem;
            letter-spacing: .02em;
        }
        .id-footer-dot { margin: 0 .4rem; }

        /* ── Print ── */
        @media print {
            @page { margin: 10mm; size: A4; }

            body { background: #fff !important; padding: 0; }
            .print-bar { display: none !important; }

            .cards-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 8mm;
            }

            .id-card {
                break-inside: avoid;
                page-break-inside: avoid;
                box-shadow: none !important;
                border: 1px solid #cbd5e1;
                border-radius: 12px;
                width: 100%;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .id-top, .id-divider, .id-field, .id-footer {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

<div class="print-bar">
    <span>
        <i class="bi bi-person-badge"></i> &nbsp;
        {{ $students->count() }} Student ID Card{{ $students->count() > 1 ? 's' : '' }}
        @isset($batch) — <strong>{{ $batch->name }}</strong> @endisset
        ready to print
    </span>
    <button onclick="window.print()"><i class="bi bi-printer"></i> &nbsp;Print</button>
</div>

<div class="cards-grid">
    @foreach ($students as $student)
    <div class="id-card">

        <div class="id-top">
            <div class="id-logo-wrap"><i class="bi bi-mortarboard-fill"></i></div>
            <div class="id-institute">
                <div class="id-institute-name">{{ $student->branch?->coaching?->name ?? setting('app_name', 'Fix My Class') }}</div>
                @if($student->branch?->name)
                    <div class="id-institute-branch">{{ $student->branch->name }}</div>
                @endif
            </div>
            <div class="id-type-badge">STUDENT</div>
        </div>

        <div class="id-divider"></div>

        <div class="id-body">
            <div class="id-left">
                <div class="id-photo-wrap">
                    <img src="{{ $student->photo_url }}" class="id-photo" alt="">
                </div>
                <div class="id-qr-wrap">
                    <div id="qr-{{ $student->id }}"></div>
                    <div class="id-qr-label">Scan to verify</div>
                </div>
            </div>

            <div class="id-right">
                <div class="id-name">{{ $student->user?->name }}</div>
                <div class="id-adm">{{ $student->admission_number }}</div>
                <div class="id-fields">
                    @if($student->schoolClass?->name)
                    <div class="id-field">
                        <span class="id-field-label"><i class="bi bi-book"></i> Class</span>
                        <span class="id-field-val">{{ $student->schoolClass->name }}</span>
                    </div>
                    @endif
                    @if($student->batch?->name)
                    <div class="id-field">
                        <span class="id-field-label"><i class="bi bi-clock"></i> Batch</span>
                        <span class="id-field-val">{{ $student->batch->name }}</span>
                    </div>
                    @endif
                    @if($student->user?->mobile)
                    <div class="id-field">
                        <span class="id-field-label"><i class="bi bi-telephone"></i> Mobile</span>
                        <span class="id-field-val">{{ $student->user->mobile }}</span>
                    </div>
                    @endif
                    @if($student->guardian_name)
                    <div class="id-field">
                        <span class="id-field-label"><i class="bi bi-person-hearts"></i> Guardian</span>
                        <span class="id-field-val">{{ $student->guardian_name }}</span>
                    </div>
                    @endif
                    @if($student->date_of_birth)
                    <div class="id-field">
                        <span class="id-field-label"><i class="bi bi-calendar3"></i> DOB</span>
                        <span class="id-field-val">{{ $student->date_of_birth->format('d M Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="id-footer">
            <span>Issued: {{ now()->format('d M Y') }}</span>
            <span class="id-footer-dot">•</span>
            <span>Valid for current session</span>
        </div>

    </div>
    @endforeach
</div>

<script>
    @foreach ($students as $student)
    @php
        $qrData = json_encode([
            'admission_number' => $student->admission_number,
            'name'     => $student->user?->name,
            'coaching' => $student->branch?->coaching?->name,
            'branch'   => $student->branch?->name,
        ]);
    @endphp
    new QRCode(document.getElementById('qr-{{ $student->id }}'), {
        text: @json($qrData),
        width: 80,
        height: 80,
        colorDark: '#1e3a8a',
        colorLight: '#ffffff',
    });
    @endforeach
</script>
</body>
</html>
