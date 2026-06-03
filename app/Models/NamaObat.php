<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class NamaObat extends Model
{
    protected $table = 'nama_obat';

    protected $fillable = [
        'kode_obat',
        'nama_obat',
        'jenis_obat_id',
        'satuan_obat_id',
        'lokasi_penyimpanan',
    ];

    public $timestamps = true;

    public function penerimaanObat()
    {
        return $this->hasMany(PenerimaanObat::class, 'nama_obat_id');
    }

     public function jenisObat()
     {
         return $this->belongsTo(JenisObat::class, 'jenis_obat_id');
     }

    public function satuanObat()
    {
        return $this->belongsTo(SatuanObat::class, 'satuan_obat_id');
    }

    public function minMax()
    {
        return $this->hasOne(MinMax::class, 'nama_obat_id')->latestOfMany();
    }

    public function minMaxRecords()
    {
        return $this->hasMany(MinMax::class, 'nama_obat_id');
    }

    public function stokObat()
    {
        return $this->hasMany(StokObat::class, 'nama_obat_id', 'id');
    }

    public function detailPenerimaanObat()
    {
        return $this->hasMany(DetailPenerimaanObat::class, 'nama_obat_id');
    }

    public function detailPengeluaranObat()
    {
        return $this->hasMany(DetailPengeluaranObat::class, 'nama_obat_id');
    }

    public function detailPemusnahanObat()
    {
        return $this->hasMany(DetailPemusnahanObat::class, 'nama_obat_id');
    }

    public function isInUse(): bool
    {
        $usageRelations = [
            'detailPenerimaanObat',
            'detailPengeluaranObat',
            'detailPemusnahanObat',
            'stokObat',
            'minMax',
        ];

        foreach ($usageRelations as $relation) {
            if ($this->{$relation}()->exists()) {
                return true;
            }
        }

        return false;
    }

    // public function getTotalStokAttribute()
    // {
    //     return $this->stokObat()->sum('stok');
    // }
}
