<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class JenisObat extends Model
{
    protected $table = 'jenis_obat';

    protected $fillable = [
        'kode_jenis',
        'jenis_obat',
    ];



    public $timestamps = true;

    public function namaObat()
    {
        return $this->hasMany(NamaObat::class, 'jenis_obat_id');
    }
}
