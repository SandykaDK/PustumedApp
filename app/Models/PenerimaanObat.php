<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PenerimaanObat extends Model
{
    protected $table = 'penerimaan_obat';
    protected $fillable = [
        'no_batch',
        'tanggal_penerimaan',
        'user_id',
        'keterangan',
    ];
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->no_batch)) {
                $batchPeriod = Carbon::parse($model->tanggal_penerimaan ?? now())->format('Ym');
                $batchPrefix = 'BATCH-' . $batchPeriod;

                $latestBatchNo = static::where('no_batch', 'like', $batchPrefix . '-%')
                    ->orderByDesc('no_batch')
                    ->value('no_batch');

                $nextSequence = 1;

                if ($latestBatchNo && preg_match('/^(?:BATCH-\d{6})-(\d{3})$/', $latestBatchNo, $matches)) {
                    $nextSequence = ((int) $matches[1]) + 1;
                }

                $model->no_batch = sprintf('%s-%03d', $batchPrefix, $nextSequence);
            }
        });
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detailPenerimaanObat()
    {
        return $this->hasMany(DetailPenerimaanObat::class, 'penerimaan_obat_id');
    }

    /**
     * Check if this penerimaan has been used in pengeluaran or pemusnahan
     * Returns array with 'used' boolean and 'message' describing where it's used
     */
    public function checkUsage()
    {
        $stokObatIds = StokObat::where('no_batch', $this->no_batch)->pluck('id')->toArray();

        if (empty($stokObatIds)) {
            return ['used' => false, 'message' => null];
        }

        $pengeluaranCount = DetailPengeluaranObat::whereIn('stok_obat_id', $stokObatIds)->count();
        $pemusnahanCount = DetailPemusnahanObat::whereIn('stok_obat_id', $stokObatIds)->count();

        if ($pengeluaranCount > 0 || $pemusnahanCount > 0) {
            $messages = [];
            if ($pengeluaranCount > 0) {
                $messages[] = "Pengeluaran Obat ($pengeluaranCount transaksi)";
            }
            if ($pemusnahanCount > 0) {
                $messages[] = "Pemusnahan Obat ($pemusnahanCount transaksi)";
            }
            return [
                'used' => true,
                'message' => 'Data ini sudah digunakan di: ' . implode(' dan ', $messages) . '. Tidak bisa dihapus!'
            ];
        }

        return ['used' => false, 'message' => null];
    }
}
