@php
    $logoPath = public_path('images/logo-pustumed-v2.png');
    $logoBase64 = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;
    $printNow = \Carbon\Carbon::now('Asia/Jakarta')->locale('id');
    $tanggalPengeluaran = \Carbon\Carbon::parse($pengeluaran->tanggal_pengeluaran)->locale('id');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Bukti Pengeluaran Obat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A5 portrait;
            margin: 12mm 10mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            font-size: 10px;
            line-height: 1.35;
            background: #fff;
        }

        .page {
            position: relative;
            padding: 18px 14px 10px;
        }

        .watermark {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            opacity: 0.08;
            z-index: 0;
        }

        .watermark img {
            width: 230px;
            height: auto;
        }

        .content {
            position: relative;
            z-index: 1;
        }

        .letterhead {
            text-align: center;
            margin-bottom: 10px;
        }

        .institution-name {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .institution-address {
            margin-top: 2px;
            font-size: 9px;
            color: #444;
        }

        .letterhead-rule {
            border-top: 1.6px solid #111;
            margin: 10px 0 10px;
        }

        .report-title {
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .meta-table td {
            padding: 1px 0;
            vertical-align: top;
        }

        .meta-label {
            width: 92px;
            white-space: nowrap;
        }

        .meta-separator {
            width: 8px;
            text-align: center;
        }

        .meta-value {
            word-break: break-word;
        }

        .summary-box {
            border: 1px solid #111;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .summary-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-grid td {
            padding: 6px 8px;
            border-bottom: 1px solid #111;
        }

        .summary-grid tr:last-child td {
            border-bottom: none;
        }

        .summary-label {
            width: 118px;
            font-weight: 700;
        }

        .summary-value {
            width: auto;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 9px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #111;
            padding: 4px 5px;
            vertical-align: top;
        }

        .items-table thead th {
            background: #efefef;
            font-weight: 700;
            text-align: center;
        }

        .col-no {
            width: 20px;
            text-align: center;
        }

        .col-name {
            width: auto;
        }

        .col-qty {
            width: 44px;
            text-align: center;
        }

        .col-unit {
            width: 46px;
            text-align: center;
        }

        .notes {
            margin-top: 6px;
            font-size: 9px;
            line-height: 1.4;
        }

        .notes-title {
            font-weight: 700;
        }

        .sign-wrap {
            width: 100%;
            margin-top: 18px;
        }

        .sign-space {
            float: left;
            width: 58%;
            min-height: 120px;
        }

        .sign-box {
            float: right;
            width: 38%;
            text-align: center;
            font-size: 9px;
            padding-top: 8px;
        }

        .sign-date {
            margin-bottom: 10px;
        }

        .sign-greeting {
            margin-top: 0;
            margin-bottom: 30px;
        }

        .sign-line {
            margin-top: 0;
            border-top: none;
            height: 28px;
        }

        .sign-name {
            margin-top: 4px;
            font-weight: 700;
        }

        .print-footer {
            margin-top: 10px;
            text-align: left;
            font-size: 9px;
            color: #444;
        }

        .clearfix::after {
            content: "";
            display: block;
            clear: both;
        }

        @media print {
            body {
                background: #fff;
            }

            .page {
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="content">
            <div class="letterhead">
                <div class="institution-name">PUSKESMAS PEMBANTU MOJOSULUR</div>
                <div class="institution-address">Jl. KH. Wahid Hasyim, Tegal Dadi, Mojosulur, Kec. Mojosari, Kabupaten Mojokerto, Jawa Timur 61382</div>
            </div>

            <div class="letterhead-rule"></div>

            <div class="report-title">Bukti Pengeluaran Obat</div>

            <table class="meta-table">
                <tr>
                    <td class="meta-label">No. Transaksi</td>
                    <td class="meta-separator">:</td>
                    <td class="meta-value">#{{ str_pad($pengeluaran->id, 4, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Tanggal</td>
                    <td class="meta-separator">:</td>
                    <td class="meta-value">{{ $tanggalPengeluaran->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Nama Petugas</td>
                    <td class="meta-separator">:</td>
                    <td class="meta-value">{{ $pengeluaran->user->name ?? '-' }}</td>
                </tr>
            </table>

            <div class="summary-box">
                <table class="summary-grid">
                    <tr>
                        <td class="summary-label">Pasien</td>
                        <td class="summary-value">{{ $pengeluaran->pasien->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">No. BPJS</td>
                        <td class="summary-value">{{ $pengeluaran->pasien->no_bpjs ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Dokter</td>
                        <td class="summary-value">{{ $pengeluaran->dokter->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th class="col-name">Nama Obat</th>
                        <th class="col-qty">Jumlah</th>
                        <th class="col-unit">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $items = $detailItems ?? $pengeluaran->detailPengeluaranObat;
                    @endphp
                    @forelse($items as $index => $detail)
                        <tr>
                            <td class="col-no">{{ $index + 1 }}</td>
                            <td class="col-name">{{ $detail->namaObat->nama_obat ?? '-' }}</td>
                            <td class="col-qty">{{ $detail->jumlah_keluar }}</td>
                            <td class="col-unit">{{ $detail->satuan->satuan_obat ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 8px 5px;">Tidak ada item</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($pengeluaran->keterangan)
                <div class="notes">
                    <span class="notes-title">Keterangan:</span>
                    <span>{{ $pengeluaran->keterangan }}</span>
                </div>
            @endif

            <div class="sign-wrap clearfix">
                <div class="sign-space"></div>
                <div class="sign-box">
                    <div class="sign-date">Mojosulur, {{ $printNow->translatedFormat('d F Y') }}</div>
                    <div class="sign-greeting">Mengetahui,</div>
                    <div class="sign-line"></div>
                    <div class="sign-name">Kepala Pustu</div>
                </div>
            </div>

            <div class="print-footer">
                Dicetak: {{ $printNow->translatedFormat('d F Y H:i:s') }}
            </div>
        </div>
    </div>
</body>
</html>
