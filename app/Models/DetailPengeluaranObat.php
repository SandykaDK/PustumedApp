<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPengeluaranObat extends Model
{
    protected $table = 'detail_pengeluaran_obat';

    protected $fillable = [
        'pengeluaran_obat_id',
        'nama_obat_id',
        'jumlah_keluar',
        'satuan_id',
        'stok_obat_id',
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

    public function pengeluaranObat()
    {
        return $this->belongsTo(PengeluaranObat::class, foreignKey: 'pengeluaran_obat_id');
    }

    public function stokObat()
    {
        return $this->belongsTo(StokObat::class, 'stok_obat_id');
    }
}
