<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'no_telepon',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function penerimaanObat()
    {
        return $this->hasMany(PenerimaanObat::class, 'user_id');
    }

    public function pengeluaranObat()
    {
        return $this->hasMany(PengeluaranObat::class, 'user_id');
    }

    public function pemusnahanObat()
    {
        return $this->hasMany(PemusnahanObat::class, 'user_id');
    }

    public function hasTransactions(): bool
    {
        if (isset($this->penerimaan_obat_count, $this->pengeluaran_obat_count, $this->pemusnahan_obat_count)) {
            return ($this->penerimaan_obat_count + $this->pengeluaran_obat_count + $this->pemusnahan_obat_count) > 0;
        }

        return $this->penerimaanObat()->exists()
            || $this->pengeluaranObat()->exists()
            || $this->pemusnahanObat()->exists();
    }
}
