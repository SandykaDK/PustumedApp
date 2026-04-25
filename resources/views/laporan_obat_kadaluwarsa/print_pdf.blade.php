<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Obat Kadaluwarsa</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; color: #333; font-size: 11px; line-height: 1.4; }
        .page { width: 100%; margin: 0 auto; padding: 16px; background: white; border: 1px solid #ddd; }
        .header { margin-bottom: 18px; }
        .header-left h1 { font-size: 18px; margin-bottom: 6px; text-transform: uppercase; }
        .header-left p { font-size: 12px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 11px; }
        table thead { background: #f9fafb; }
        table th, table td { border: 1px solid #e5e7eb; padding: 8px 8px; text-align: left; }
        table th { font-weight: 700; color: #374151; }
        table tbody tr:nth-child(even) { background: #fbfbfb; }
        .footer { display: flex; justify-content: space-between; align-items: center; margin-top: 18px; flex-wrap: wrap; gap: 12px; }
        .footer-text { font-size: 10px; color: #6b7280; }
        .signature { text-align: center; margin-top: 26px; }
        .signature-line { width: 140px; border-top: 1px solid #333; margin: 28px auto 6px; }
        .print-date { font-size: 10px; color: #6b7280; margin-top: 6px; }
        @media print { body { background: white; } .page { border: none; box-shadow: none; } }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div class="header-left">
                <h1>Laporan Obat Kadaluwarsa</h1>
                <p>{{ config('app.name', 'PustumedApp') }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th>Nama Obat</th>
                    <th style="width: 120px;">Exp Date</th>
                    <th style="width: 55px;">Stok</th>
                    <th style="width: 90px;">Status Exp</th>
                    <th style="width: 120px;">Status Pemusnahan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['nama_obat'] }}</td>
                        <td>{{ optional($item['tanggal_kadaluwarsa'])->translatedFormat('d F Y') }}</td>
                        <td>{{ $item['stok'] }}</td>
                        <td>{{ $item['status_exp'] }}</td>
                        <td>{{ $item['status_pemusnahan'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Tidak ada obat kadaluwarsa</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <div class="footer-text">Dicetak dari {{ config('app.name', 'PustumedApp') }}</div>
            <div class="footer-text">Tanggal cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i:s') }}</div>
        </div>

        <div class="signature">
            <div class="signature-line"></div>
            <div>Penanggung Jawab</div>
        </div>
    </div>
</body>
</html>
