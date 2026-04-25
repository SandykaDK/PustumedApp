<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MinMax extends Model
{
    protected $table = 'min_max';

    protected $fillable = [
        'nama_obat_id',
        'average_daily_usage',
        'maximum_daily_usage',
        'minimum_stock',
        'maximum_stock',
        'safety_stock',
        'reorder_point',
        'lead_time',
    ];

    public function namaObat()
    {
        return $this->belongsTo(NamaObat::class, 'nama_obat_id');
    }
}
