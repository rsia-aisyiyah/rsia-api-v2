<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\RsiaSuratInternal
 *
 * @property string $no_surat
 * @property string|null $perihal
 * @property string|null $tempat
 * @property string|null $pj
 * @property string $tgl_terbit
 * @property string|null $tanggal
 * @property string|null $catatan
 * @property string|null $status
 * @property string $created_at
 * @property-read \App\Models\Pegawai|null $penanggung_jawab
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratInternal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratInternal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratInternal query()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratInternal whereCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratInternal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratInternal whereNoSurat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratInternal wherePerihal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratInternal wherePj($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratInternal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratInternal whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratInternal whereTempat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratInternal whereTglTerbit($value)
 * @property-read \App\Models\Pegawai|null $penanggungJawab
 * @property-read \App\Models\Pegawai|null $penanggungJawabSimple
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RsiaPenerimaUndangan> $penerimaUndangan
 * @property-read int|null $penerima_undangan_count
 * @mixin \Eloquent
 */
class RsiaSuratInternal extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rsia_surat_internal';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $casts = [
        'no_surat' => 'string',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;


    
    public function undangan()
    {
        return $this->hasOne(RsiaUndangan::class, 'surat_id', 'id');
    }
    
    public function penerima_undangan()
    {
        return $this->hasManyThrough(RsiaPenerimaUndangan::class, RsiaUndangan::class, 'surat_id', 'undangan_id', 'id', 'id');
    }

    public function penerima()
    {
        return $this->hasMany(RsiaPenerimaUndangan::class, 'no_surat', 'no_surat')->select('no_surat', 'penerima', 'updated_at')->with('pegawai');
    }
    
    public function penerimaUndangan()
    {
        return $this->hasMany(RsiaPenerimaUndangan::class, 'no_surat', 'no_surat');
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(Pegawai::class, 'pj', 'nik');
    }

    public function penanggungJawabSimple()
    {
        return $this->belongsTo(Pegawai::class, 'pj', 'nik')->select('nik', 'nama');
    }


    /**
     * Scope a query to only include models that have related data in RsiaPenerimaUndangan.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopehasPenerima($query)
    {
        return $query->whereHas('penerimaUndangan');
    }
}
