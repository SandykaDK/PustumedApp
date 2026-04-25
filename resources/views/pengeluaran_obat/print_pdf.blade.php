<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pengeluaran Obat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            font-size: 11px;
            line-height: 1.4;
        }

        .receipt-container {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 15px;
            margin: 0 auto;
            border: 1px solid #ddd;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .receipt-header h1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .receipt-header p {
            font-size: 9px;
            color: #666;
        }

        .receipt-info {
            margin-bottom: 12px;
            font-size: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .info-label {
            font-weight: bold;
            min-width: 80px;
        }

        .info-value {
            text-align: right;
            flex: 1;
            padding-left: 8px;
            word-break: break-word;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }

        table thead {
            background-color: #f0f0f0;
            border-top: 1px solid #333;
            border-bottom: 1px solid #333;
        }

        table th {
            padding: 4px 2px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
        }

        table td {
            padding: 4px 2px;
            border-bottom: 1px solid #eee;
        }

        table tbody tr:last-child td {
            border-bottom: 1px solid #333;
        }

        .item-no {
            width: 20px;
            text-align: center;
        }

        .item-name {
            flex: 1;
            word-break: break-word;
        }

        .item-qty {
            width: 40px;
            text-align: center;
        }

        .item-satuan {
            width: 35px;
            text-align: center;
        }

        .receipt-footer {
            margin-top: 15px;
            text-align: center;
            border-top: 2px solid #333;
            padding-top: 8px;
        }

        .footer-text {
            font-size: 9px;
            color: #666;
            margin-bottom: 3px;
        }

        .signature-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            font-size: 9px;
        }

        .signature-box {
            text-align: center;
            width: 150px;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 100%;
            margin: 40px 0 5px;
        }

        .print-date {
            text-align: right;
            font-size: 9px;
            color: #666;
            margin-top: 10px;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        .divider {
            border-top: 1px dashed #999;
            margin: 10px 0;
        }

        .notes {
            font-size: 9px;
            color: #666;
            margin-top: 8px;
            padding: 5px;
            background-color: #f9f9f9;
            border-left: 3px solid #007bff;
        }

        @media print {
            body {
                background: white;
            }
            .receipt-container {
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <h1>Bukti Pengeluaran Obat</h1>
            <p>{{ config('app.name', 'PustumedApp') }}</p>
        </div>

        <!-- Transaction Info -->
        <div class="receipt-info">
            <div class="info-row">
                <span class="info-label">No. Transaksi</span>
                <span class="info-value">#{{ str_pad($pengeluaran->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($pengeluaran->tanggal_pengeluaran)->translatedFormat('d F Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Nama Petugas</span>
                <span class="info-value">{{ $pengeluaran->user->name ?? '-' }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Patient & Doctor Info -->
        <div class="receipt-info">
            <div class="info-row">
                <span class="info-label">Pasien</span>
                <span class="info-value">{{ $pengeluaran->pasien->nama ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">No. BPJS</span>
                <span class="info-value">{{ $pengeluaran->pasien->no_bpjs ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Dokter</span>
                <span class="info-value">{{ $pengeluaran->dokter->nama ?? '-' }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th class="item-no">No</th>
                    <th class="item-name">Nama Obat</th>
                    <th class="item-qty">Jumlah</th>
                    <th class="item-satuan">Satuan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengeluaran->detailPengeluaranObat as $index => $detail)
                    <tr>
                        <td class="item-no">{{ $index + 1 }}</td>
                        <td class="item-name">{{ $detail->namaObat->nama_obat ?? '-' }}</td>
                        <td class="item-qty">{{ $detail->jumlah_keluar }}</td>
                        <td class="item-satuan">{{ $detail->satuan->satuan_obat ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">Tidak ada item</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Notes -->
        @if($pengeluaran->keterangan)
            <div class="notes">
                <strong>Keterangan:</strong><br>
                {{ $pengeluaran->keterangan }}
            </div>
        @endif

        <!-- Footer -->
        <div class="receipt-footer">
            <p class="footer-text">Terima kasih telah mempercayakan kesehatan Anda kepada kami</p>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <p>Kepala Pustu</p>
                <div class="signature-line"></div>
                <p style="margin-top: 3px;"></p>
            </div>
        </div>

        <!-- Print Date -->
        <div class="print-date">
            Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i:s') }}
        </div>
    </div>
</body>
</html>
