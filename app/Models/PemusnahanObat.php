<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemusnahanObat extends Model
{
    use HasFactory;

    protected $table = 'pemusnahan_obat';

    protected $fillable = [
        'user_id',
        'tanggal_pemusnahan',
        'status',
        'approved_by',
        'approved_at',
        'keterangan',
        'bukti_foto',
    ];

    protected $casts = [
        'tanggal_pemusnahan' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function details()
    {
        return $this->hasMany(DetailPemusnahanObat::class);
    }
}
