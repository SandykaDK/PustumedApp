<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Permintaan Obat</title>
    <style>
        @page { size: A4 portrait; margin: 14mm 16mm 12mm 16mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #111; font-size: 9px; }
        .page { width: 100%; padding: 4mm 0 0 0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .header-title { text-transform: uppercase; font-weight: 700; font-size: 14px; line-height: 1.25; margin-bottom: 8px; }
        .header-meta { width: 94%; margin: 0 auto 8px auto; border-collapse: collapse; }
        .header-meta td { padding: 1px 0; font-size: 10px; vertical-align: top; }

        .report-table { width: 94%; margin: 0 auto; border-collapse: collapse; table-layout: fixed; }
        .report-table th,
        .report-table td {
            border: 1px solid #222;
            padding: 3px 2px;
            font-size: 8.5px;
            vertical-align: middle;
            text-align: center;
        }

        .report-table tbody td:nth-child(2) {
            text-align: left;
        }

        .report-table thead th {
            text-transform: uppercase;
            font-weight: 700;
            background: #f5f5f5;
            line-height: 1.05;
        }

        .header-stack {
            display: inline-block;
            line-height: 1.05;
        }

        .w-no { width: 4.5%; }
        .w-name { width: 17%; }
        .w-sat { width: 4.5%; }
        .w-stok { width: 5%; }
        .w-prev { width: 6%; }
        .w-compact { width: 6.5%; }
        .w-note { width: 8.5%; }

        .signature-section {
            width: 94%;
            margin: 10px auto 0 auto;
            font-size: 9px;
            page-break-inside: avoid;
            break-inside: avoid-page;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .signature-table td {
            border: none;
            padding: 0;
            vertical-align: top;
            text-align: center;
        }

        .signature-top td,
        .signature-bottom td {
            width: 33.333%;
        }

        .signature-bottom td {
            padding-top: 14px;
        }

        .signature-block {
            width: 100%;
        }

        .signature-title {
            font-weight: 700;
            margin-bottom: 22px;
            line-height: 1.25;
        }

        .signature-block--left .signature-title {
            margin-bottom: 34px;
        }

        .signature-block--left .signature-name {
            margin-top: 10px;
        }

        .signature-name {
            display: inline-block;
            min-width: 180px;
            padding-top: 6px;
            font-weight: 700;
            margin-top: 5px;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .signature-nip {
            margin-top: 2px;
            display: block;
            width: 100%;
            text-align: center;
            transform: none;
            font-size: 8px;
        }

        .signature-block--right .signature-nip {
            text-align: left;
            padding-left: 60px;
        }

        .signature-position {
            display: inline-block;
            min-width: 180px;
            font-weight: 700;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="text-center header-title">
            LAPORAN PEMAKAIAN DAN LEMBAR PERMINTAAN OBAT<br>
            SUB UNIT PELAYANAN
        </div>

        <table class="header-meta">
            <tr>
                <td style="width: 28%;">PONKESDES / PUSTU</td>
                <td style="width: 37%;">: MOJOSULUR</td>
                <td style="width: 18%;">LAPORAN BULAN</td>
                <td style="width: 17%;">: {{ $monthLabel }}</td>
            </tr>
            <tr>
                <td>PUSKESMAS</td>
                <td>: - </td>
                <td>TAHUN</td>
                <td>: {{ $selectedYear }}</td>
            </tr>
        </table>

        <table class="report-table">
            <thead>
                <tr>
                    <th class="w-no">No</th>
                    <th class="w-name">Nama Obat</th>
                    <th class="w-sat">Sat</th>
                    <th class="w-stok"><span class="header-stack">Stok<br>Awal</span></th>
                    <th class="w-prev">Pemberian<br>{{ $previousMonthLabel }}</th>
                    <th class="w-compact">Persediaan</th>
                    <th class="w-compact">Pemakaian</th>
                    <th class="w-stok"><span class="header-stack">Sisa<br>Stok</span></th>
                    <th class="w-compact">Permintaan</th>
                    <th class="w-compact">Pemberian<br>{{ $monthLabel }} {{ $selectedYear }}</th>
                    <th class="w-note">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item['nama_obat'] }}</td>
                        <td class="text-center">{{ $item['satuan'] }}</td>
                        <td class="text-right">{{ number_format($item['stok_awal']) }}</td>
                        <td class="text-right">{{ number_format($item['pemberian_bulan_lalu'] ?? 0) }}</td>
                        <td class="text-right">{{ number_format($item['persediaan']) }}</td>
                        <td class="text-right">{{ number_format($item['pemakaian']) }}</td>
                        <td class="text-right">{{ number_format($item['sisa_stok']) }}</td>
                        <td class="text-right">{{ number_format($item['permintaan']) }}</td>
                        <td class="text-right">{{ $isPermintaanPrint ? '' : number_format($item['pemberian']) }}</td>
                        <td>&nbsp;</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">Tidak ada data permintaan obat</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="signature-section">
            <table class="signature-table">
                <tr class="signature-top">
                    <td>
                        <div class="signature-block signature-block--left">
                            <div class="signature-title">PETUGAS GUDANG OBAT</div>
                            <div class="signature-name">MUHAMMAD UBAIDILLAH, A.Md.Farm</div>
                            <div class="signature-nip">NIP. 19890917 202012 1 009</div>
                        </div>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <div class="signature-block signature-block--right">
                            <div class="signature-title">YANG MENYERAHKAN</div>
                            <div class="signature-position">&nbsp;</div>
                            <div class="signature-name">.........................................................</div>
                            <div class="signature-nip">NIP. &nbsp;</div>
                        </div>
                    </td>
                </tr>
                <tr class="signature-bottom">
                    <td>&nbsp;</td>
                    <td>
                        <div class="signature-block signature-block--middle">
                            <div class="signature-title">MENGETAHUI<br>KEPALA UPTD PUSKESMAS MOJOSULUR</div>
                            <div class="signature-position">&nbsp;</div>
                            <div class="signature-name">dr. POPHARIA BERLIANDA</div>
                            <div class="signature-nip">NIP. 19720121 200012 2 009</div>
                        </div>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
