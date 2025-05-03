<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RsiaPenerimaUndangan
 *
 * @property string|null $no_surat
 * @property string|null $penerima
 * @property string|null $tipe
 * @property string|null $model
 * @property string $created_at
 * @property-read \App\Models\Pegawai|null $detail
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaPenerimaUndangan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaPenerimaUndangan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaPenerimaUndangan query()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaPenerimaUndangan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaPenerimaUndangan whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaPenerimaUndangan whereNoSurat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaPenerimaUndangan wherePenerima($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaPenerimaUndangan whereTipe($value)
 * @property string $updated_at
 * @property-read Model|\Eloquent $relatedModel
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaPenerimaUndangan searchByRelatedModel($searchTerm)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaPenerimaUndangan whereBetweenDate($start, $end)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaPenerimaUndangan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaPenerimaUndangan withDetail()
 * @mixin \Eloquent
 */
class RsiaPenerimaUndangan extends Model
{
    use Compoships;

    protected $table = 'rsia_penerima_undangan';

    protected $primaryKey = 'undangan_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'penerima' => 'string',
        'creared_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the detail that owns the RsiaPenerimaUndangan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function detail()
    {
        return $this->belongsTo(Pegawai::class, 'penerima', 'nik')
            ->select('nik', 'nama', 'jbtn', 'departemen', 'bidang', 'jk');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'penerima', 'nik')->select('nik', 'nama', 'jbtn', 'departemen', 'bidang');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class,  'penerima', 'nip');
    }

    public function kehadiran()
    {
        return $this->belongsTo(RsiaKehadiranRapat::class, ['undangan_id', 'penerima'], ['undangan_id', 'nik'])->without('pegawai');
    }
}
