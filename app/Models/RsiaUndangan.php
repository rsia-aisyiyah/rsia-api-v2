<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaUndangan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rsia_undangan';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    public $incrementing = true;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $casts = [
        'no_surat'   => 'string',
        'pj'         => 'string',
        'modals'     => 'string',
        'tanggal'    => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;


    
    /**
     * Get the pegawai that owns the RsiaUndangan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pj', 'nik');
    }

    public function penerima()
    {
        return $this->hasMany(RsiaPenerimaUndangan::class, 'id', 'id')->select('penerima', 'updated_at')->with('pegawai');
    }
}
