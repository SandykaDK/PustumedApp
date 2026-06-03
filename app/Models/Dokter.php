<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PengeluaranObat;


class Dokter extends Model
{
    protected $table = 'dokter';
    protected $fillable = [
        'nama',
        'alamat',
        'jenis_kelamin',
        'no_telepon',
        'email',
        'status',
    ];


    public $timestamps = true;

    public function pengeluaranObat()
    {
        return $this->hasMany(PengeluaranObat::class, 'dokter_id');
    }
}
