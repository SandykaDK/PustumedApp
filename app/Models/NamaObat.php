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
        'status'
    ];

    protected $attributes = [
        'status' => 'aktif',
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

    public function availableStokObat()
    {
        return $this->hasMany(StokObat::class, 'nama_obat_id', 'id')
            ->where('stok', '>', 0)
            ->where('tanggal_kadaluwarsa', '>', now()->addDays(30));
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

    /**
     * Generate next kode_obat for a given jenis id.
     * If $excludeId is provided it will be ignored when scanning existing codes (useful on update).
     */
    public static function generateKodeForJenis(int $jenisId, ?int $excludeId = null): string
    {
        $jenis = JenisObat::find($jenisId);
        $prefix = $jenis ? $jenis->kode_jenis : 'XX';

        $query = static::where('kode_obat', 'like', $prefix . '-%');
        if ($excludeId) {
            $query->where('id', '<>', $excludeId);
        }

        $last = $query->orderBy('kode_obat', 'desc')->first();

        $next = 1;
        if ($last && preg_match('/-(\d+)$/', $last->kode_obat, $m)) {
            $next = intval($m[1]) + 1;
        }

        return sprintf('%s-%03d', $prefix, $next);
    }

    // public function getTotalStokAttribute()
    // {
    //     return $this->stokObat()->sum('stok');
    // }
}
