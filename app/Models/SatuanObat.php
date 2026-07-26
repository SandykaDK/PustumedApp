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

    public static function generateKode(?int $excludeId = null): string
    {
        $query = static::where('kode_satuan', 'like', 'SAT-%');

        if ($excludeId) {
            $query->where('id', '<>', $excludeId);
        }

        $last = $query->orderBy('kode_satuan', 'desc')->first();

        $next = 1;
        if ($last && preg_match('/-(\d+)$/', $last->kode_satuan, $matches)) {
            $next = (int) $matches[1] + 1;
        }

        return sprintf('SAT-%02d', $next);
    }
}
