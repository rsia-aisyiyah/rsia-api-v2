<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    use HasFactory;

    protected $table = 'rsia_surat_internal';

    protected $primaryKey = 'no_surat';

    protected $keyType = 'string';

    protected $casts = [
        'no_surat' => 'string',
    ];

    protected $guarded = [];

    public $incrementing = false;

    public $timestamps = false;


    
    public function undangan()
    {
        return $this->hasOne(RsiaUndangan::class, 'no_surat', 'no_surat');
    }
    
    public function penerima_undangan()
    {
        return $this->hasManyThrough(RsiaPenerimaUndangan::class, RsiaUndangan::class, 'no_surat', 'undangan_id', 'no_surat', 'id');
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
