<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Pasien extends Model
{
    protected $table = 'pasien';
    protected $fillable = [
        'nama',
        'nik',
        'alamat',
        'jenis_kelamin',
        'golongan_darah',
        'no_telepon',
        'no_bpjs',
        'status'
    ];


    public $timestamps = true;

    public function pengeluaranObat()
    {
        return $this->hasMany(PengeluaranObat::class, 'pasien_id');
    }
}
