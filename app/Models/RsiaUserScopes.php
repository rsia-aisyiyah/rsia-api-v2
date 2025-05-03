<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaUserScopes extends Model
{
    use HasFactory;

    protected $table = 'rsia_user_scopes';

    protected $fillable = [
        'nik',
        'scope'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nik', 'nik');
    }
}
