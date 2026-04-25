<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenerimaanObat extends Model
{
    protected $table = 'detail_penerimaan_obat';

    protected $fillable = [
        'penerimaan_obat_id',
        'nama_obat_id',
        'jenis_obat_id',
        'no_batch',
        'tanggal_kadaluwarsa',
        'jumlah_masuk',
        'satuan_id',
        'lokasi_penyimpanan',
    ];

    public $timestamps = true;

    public function namaObat()
    {
        return $this->belongsTo(NamaObat::class, 'nama_obat_id');
    }

    public function jenisObat()
    {
        return $this->belongsTo(JenisObat::class, 'jenis_obat_id');
    }

    public function satuan()
    {
        return $this->belongsTo(SatuanObat::class, 'satuan_id');
    }

    public function penerimaanObat()
    {
        return $this->belongsTo(PenerimaanObat::class, 'penerimaan_obat_id');
    }

    // public function pengeluaranObat()
    // {
    //     return $this->belongsTo(PengeluaranObat::class, 'pengeluaran_obat_id');
    // }
}
