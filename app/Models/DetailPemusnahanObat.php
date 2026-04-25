<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPemusnahanObat extends Model
{
    use HasFactory;

    protected $table = 'detail_pemusnahan_obat';

    protected $fillable = [
        'pemusnahan_obat_id',
        'nama_obat_id',
        'stok_obat_id',
        'jumlah',
        'satuan_id',
        'lokasi_penyimpanan'
    ];

    public function pemusnahan()
    {
        return $this->belongsTo(PemusnahanObat::class, 'pemusnahan_obat_id');
    }

    public function namaObat()
    {
        return $this->belongsTo(NamaObat::class, 'nama_obat_id');
    }

    public function stok()
    {
        return $this->belongsTo(StokObat::class, 'stok_obat_id');
    }

    public function satuan()
    {
        return $this->belongsTo(SatuanObat::class, 'satuan_id');
    }
}
