<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Laporan Obat Kadaluwarsa</title>
    <style>
        @page { size: A4 portrait; margin: 14mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --ink: #111827;
            --muted: #6b7280;
            --line: #e5e7eb;
            --soft: #f8fafc;
            --brand: #1d4ed8;
            --brand-soft: #dbeafe;
            --warn: #f59e0b;
            --warn-soft: #fef3c7;
            --danger: #dc2626;
            --danger-soft: #fee2e2;
            --success: #16a34a;
            --success-soft: #dcfce7;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f4f6;
            color: var(--ink);
            font-size: 11px;
            line-height: 1.45;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .page {
            width: 100%;
            margin: 0 auto;
            padding: 0;
            background: white;
        }
        .report {
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
        }
        .report-topbar {
            height: 8px;
            background: linear-gradient(90deg, var(--brand), #0f766e, var(--warn));
        }
        .report-body {
            padding: 18px 18px 16px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            padding-bottom: 14px;
            margin-bottom: 14px;
            border-bottom: 1px solid var(--line);
        }
        .header-left {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .header-left h1 {
            font-size: 18px;
            line-height: 1.1;
            margin-bottom: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .report-meta {
            text-align: right;
            min-width: 220px;
        }
        .report-meta .meta-label {
            font-size: 10px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 4px;
        }
        .report-meta .meta-value {
            font-size: 11px;
            color: var(--ink);
            font-weight: 600;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 14px;
        }
        .summary-card {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px 12px;
            background: linear-gradient(180deg, #ffffff 0%, var(--soft) 100%);
        }
        .summary-label {
            font-size: 10px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .summary-value {
            font-size: 16px;
            font-weight: 700;
            color: var(--ink);
        }
        .summary-note {
            font-size: 10px;
            color: var(--muted);
            margin-top: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin-bottom: 14px;
        }
        table thead {
            background: var(--soft);
        }
        table th,
        table td {
            border: 1px solid var(--line);
            padding: 8px 7px;
            text-align: left;
            vertical-align: top;
        }
        table th {
            font-weight: 700;
            color: var(--ink);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        table tbody tr:nth-child(even) {
            background: #fcfcfd;
        }
        .num,
        .stok-cell,
        .center {
            text-align: center;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            line-height: 1;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .status-pill.expired,
        .status-pill.kadaluwarsa {
            background: var(--danger-soft);
            color: var(--danger);
            border-color: #fca5a5;
        }
        .status-pill.near,
        .status-pill.warning,
        .status-pill.mendekati {
            background: var(--warn-soft);
            color: #b45309;
            border-color: #fcd34d;
        }
        .status-pill.safe,
        .status-pill.aman {
            background: var(--success-soft);
            color: var(--success);
            border-color: #86efac;
        }
        .status-pill.pending {
            background: #fef3c7;
            color: #92400e;
            border-color: #fcd34d;
        }
        .status-pill.approved {
            background: var(--success-soft);
            color: var(--success);
            border-color: #86efac;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 16px;
            margin-top: 18px;
            padding-top: 12px;
            border-top: 1px solid var(--line);
            flex-wrap: wrap;
        }
        .footer-text {
            font-size: 10px;
            color: var(--muted);
        }
        .signature {
            width: 220px;
            text-align: center;
            margin-left: auto;
        }
        .signature-label {
            font-size: 10px;
            color: var(--muted);
            margin-bottom: 32px;
        }
        .signature-line {
            width: 160px;
            border-top: 1px solid #111827;
            margin: 0 auto 6px;
        }
        .signature-name {
            font-size: 11px;
            font-weight: 600;
            color: var(--ink);
        }
        .empty-state {
            text-align: center;
            color: var(--muted);
            padding: 18px 0;
        }
        @media print {
            body { background: white; }
            .report { border: none; border-radius: 0; }
            .report-body { padding: 0; }
            .summary-card { break-inside: avoid; }
            table tr { break-inside: avoid; }
        }
    </style>
</head>
<body>
    @php
        $itemCount = is_countable($items) ? count($items) : collect($items)->count();
        $expiredCount = collect($items)->filter(fn ($item) => str_contains(strtolower((string) ($item['status_exp'] ?? '')), 'kadaluwarsa') || str_contains(strtolower((string) ($item['status_exp'] ?? '')), 'expired'))->count();
        $warningCount = collect($items)->filter(fn ($item) => str_contains(strtolower((string) ($item['status_exp'] ?? '')), 'mendekati') || str_contains(strtolower((string) ($item['status_exp'] ?? '')), 'warning') || str_contains(strtolower((string) ($item['status_exp'] ?? '')), 'near'))->count();
        $printedAt = \Carbon\Carbon::now()->translatedFormat('d F Y H:i:s');
    @endphp

    <div class="page">
        <div class="report">
            <div class="report-topbar"></div>
            <div class="report-body">
                <div class="header">
                    <div class="header-left">
                        <div>
                            <h1>Laporan Obat Kadaluwarsa</h1>
                        </div>
                    </div>
                    <div class="report-meta">
                        <div class="meta-label">Tanggal Cetak</div>
                        <div class="meta-value">{{ $printedAt }}</div>
                    </div>
                </div>

                <div class="summary-grid">
                    <div class="summary-card">
                        <div class="summary-label">Total Data</div>
                        <div class="summary-value">{{ $itemCount }}</div>
                        <div class="summary-note">Semua item pada hasil cetak ini</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Status Kadaluwarsa</div>
                        <div class="summary-value">{{ $expiredCount }}</div>
                        <div class="summary-note">Obat dengan status kadaluwarsa</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Status Peringatan</div>
                        <div class="summary-value">{{ $warningCount }}</div>
                        <div class="summary-note">Obat yang mendekati kadaluwarsa</div>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th style="width: 42px;" class="center">No</th>
                            <th>Nama Obat</th>
                            <th style="width: 125px;">Exp Date</th>
                            <th style="width: 65px;" class="center">Stok</th>
                            <th style="width: 105px;" class="center">Status Exp</th>
                            <th style="width: 125px;" class="center">Status Pemusnahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $index => $item)
                            @php
                                $statusExp = strtolower((string) ($item['status_exp'] ?? ''));
                                $statusPemusnahan = strtolower((string) ($item['status_pemusnahan'] ?? ''));

                                $expClass = str_contains($statusExp, 'kadaluwarsa') || str_contains($statusExp, 'expired')
                                    ? 'expired'
                                    : (str_contains($statusExp, 'mendekati') || str_contains($statusExp, 'warning') || str_contains($statusExp, 'near')
                                        ? 'near'
                                        : 'safe');

                                $pemusnahanClass = str_contains($statusPemusnahan, 'selesai') || str_contains($statusPemusnahan, 'approved')
                                    ? 'approved'
                                    : (str_contains($statusPemusnahan, 'proses') || str_contains($statusPemusnahan, 'pending')
                                        ? 'pending'
                                        : 'safe');
                            @endphp
                            <tr>
                                <td class="num">{{ $index + 1 }}</td>
                                <td>{{ $item['nama_obat'] }}</td>
                                <td>{{ optional($item['tanggal_kadaluwarsa'])->translatedFormat('d F Y') }}</td>
                                <td class="stok-cell">{{ $item['stok'] }}</td>
                                <td class="center"><span class="status-pill {{ $expClass }}">{{ $item['status_exp'] }}</span></td>
                                <td class="center"><span class="status-pill {{ $pemusnahanClass }}">{{ $item['status_pemusnahan'] }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state">Tidak ada obat kadaluwarsa</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="footer">
                    <div>
                        <div class="footer-text">Dicetak dari {{ config('app.name', 'PustumedApp') }}</div>
                        <div class="footer-text">Tanggal cetak: {{ $printedAt }}</div>
                    </div>

                    <div class="signature">
                        <div class="signature-label">Penanggung Jawab</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">(_____________________)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
