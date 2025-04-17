<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RsiaUndangan extends Model
{
    use HasFactory, SoftDeletes;

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
        'pj'         => 'string',
        'modals'     => 'string',
        'tanggal'    => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // hidden
    // protected $hidden = [
    //     'id', 'surat_id'
    // ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;


    
    /**
     * Get the penanggungJawab that owns the RsiaUndangan
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function penanggungJawab()
    {
        return $this->belongsTo(Pegawai::class, 'pj', 'nik')
            ->select('nik', 'nama', 'jbtn', 'jnj_jabatan');
    }

    /**
     * Get the peserta the RsiaUndangan
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function peserta()
    {
        return $this->hasMany(RsiaPenerimaUndangan::class, 'undangan_id', 'id')
            ->select('undangan_id', 'penerima', 'updated_at');
    }
    
    /**
     * Get the surat that owns the RsiaUndangan
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function surat()
    {
        return $this->morphTo(__FUNCTION__, 'model', 'surat_id');
    }
}