<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengeluaranObat extends Model
{
    protected $table = 'pengeluaran_obat';

    protected $fillable = [
        'tanggal_pengeluaran',
        'user_id',
        'pasien_id',
        'dokter_id',
        'keterangan'
    ];
    public $timestamps = true;

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function Pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function Dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }

    public function detailPengeluaranObat()
    {
        return $this->hasMany(DetailPengeluaranObat::class, foreignKey: 'pengeluaran_obat_id');
    }
}
