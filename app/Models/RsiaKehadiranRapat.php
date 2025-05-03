<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;
use Thiagoprz\CompositeKey\HasCompositeKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\RsiaKehadiranRapat
 *
 * @property int $id
 * @property string $no_surat
 * @property string $nik
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Pegawai $pegawai
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaKehadiranRapat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaKehadiranRapat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaKehadiranRapat query()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaKehadiranRapat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaKehadiranRapat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaKehadiranRapat whereNik($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaKehadiranRapat whereNoSurat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaKehadiranRapat whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RsiaKehadiranRapat extends Model
{
    use Compoships;

    protected $table = 'rsia_kehadiran_rapat';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    // with but only select nik ane nama
    protected $with = ['pegawai'];


    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nik', 'nik')->select('nik', 'nama');
    }

    public function penerima_undangan()
    {
        return $this->belongsTo(RsiaPenerimaUndangan::class, ['nik', 'undangan_id'], ['penerima', 'undangan_id']);
    }
}
