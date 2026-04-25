<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SatuanObat extends Model
{
    protected $table = 'satuan_obat';
    protected $fillable = [
        'kode_satuan',
        'satuan_obat',
    ];


    public $timestamps = true;

    // public function penerimaanObat()
    // {
    //     return $this->hasMany(PenerimaanObat::class, 'satuan_id');
    // }

    public function namaObat()
    {
        return $this->hasMany(NamaObat::class, 'satuan_obat_id');
    }
}
