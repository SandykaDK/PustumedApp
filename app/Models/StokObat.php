<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokObat extends Model
{
    protected $table = 'stok_obat';

    protected $fillable = [
        'nama_obat_id',
        'tanggal_kadaluwarsa',
        'stok',
        'no_batch',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_kadaluwarsa' => 'date',
    ];

    /**
     * Accessor untuk hitung status berdasarkan stok
     * Jika diperlukan, method ini bisa override status dari database
     */
    protected function getStatusAttribute($value)
    {
        // Jika stok > 0 maka 'tersedia', sebaliknya 'kosong'
        return $this->stok > 0 ? 'tersedia' : 'kosong';
    }

    public function namaObat()
    {
        return $this->belongsTo(NamaObat::class);
    }

    public function detailPemusnahanObat()
    {
        return $this->hasMany(DetailPemusnahanObat::class, 'stok_obat_id');
    }

    /**
     * Mutator untuk otomatis update status saat stok disimpan
     * (Opsional, karena accessor sudah handle komputasi)
     */
    protected static function booted()
    {
        static::saving(function ($model) {
            // Update status otomatis berdasarkan stok
            $model->status = $model->stok > 0 ? 'tersedia' : 'kosong';
        });
    }
}
