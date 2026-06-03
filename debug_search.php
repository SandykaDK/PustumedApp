<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PengeluaranObat;

$search = $argv[1] ?? 'Citra';

// Reuse the same search logic as controller: prefer pasien/no_bpjs matches
use App\Models\Pasien;

$patientMatch = Pasien::where('nama', 'like', "%{$search}%")
    ->orWhere('no_bpjs', 'like', "%{$search}%")
    ->exists();

$qBase = PengeluaranObat::with(['Pasien', 'Dokter', 'detailPengeluaranObat.namaObat']);
if ($patientMatch) {
    $results = $qBase->where(function($q) use ($search) {
        $q->whereHas('Pasien', function($sub) use ($search) {
            $sub->where('nama', 'like', "%{$search}%")
                ->orWhere('no_bpjs', 'like', "%{$search}%");
        })->orWhereHas('Dokter', function($sub) use ($search) {
            $sub->where('nama', 'like', "%{$search}%");
        });
    })->get();
} else {
    $results = $qBase->where(function($q) use ($search) {
        $q->whereHas('Pasien', function($sub) use ($search) {
            $sub->where('nama', 'like', "%{$search}%")
                ->orWhere('no_bpjs', 'like', "%{$search}%");
        })
        ->orWhereHas('Dokter', function($sub) use ($search) {
            $sub->where('nama', 'like', "%{$search}%");
        })
        ->orWhereHas('detailPengeluaranObat.namaObat', function($sub) use ($search) {
            $escaped = preg_quote(strtolower($search), '/');
            $pattern = '(^|[^a-z0-9])' . $escaped . '([^a-z0-9]|$)';
            $sub->whereRaw("LOWER(nama_obat) REGEXP ?", [$pattern]);
        });
    })->get();
}

foreach ($results as $r) {
    $detailNames = $r->detailPengeluaranObat->map(fn($d) => $d->namaObat?->nama_obat ?? '-')->toArray();
    echo "ID: {$r->id} | Pasien: " . ($r->Pasien?->nama ?? '-') . " | BPJS: " . ($r->Pasien?->no_bpjs ?? '-') . " | Dokter: " . ($r->Dokter?->nama ?? '-') . " | Details: " . implode('|', $detailNames) . PHP_EOL;
}

echo "Total: " . $results->count() . PHP_EOL;
