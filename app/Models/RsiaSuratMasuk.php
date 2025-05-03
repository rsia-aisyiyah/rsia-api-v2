<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RsiaSuratMasuk
 *
 * @property int $no
 * @property string $no_simrs
 * @property string|null $no_surat
 * @property string $pengirim
 * @property string|null $tgl_surat
 * @property string $perihal
 * @property string|null $pelaksanaan
 * @property string|null $pelaksanaan_end
 * @property string|null $tempat
 * @property string $ket
 * @property string|null $berkas
 * @property string $status
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratMasuk newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratMasuk newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratMasuk query()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratMasuk whereBerkas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratMasuk whereKet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratMasuk whereNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratMasuk whereNoSimrs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratMasuk whereNoSurat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratMasuk wherePelaksanaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratMasuk wherePelaksanaanEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratMasuk wherePengirim($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratMasuk wherePerihal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratMasuk whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratMasuk whereTempat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratMasuk whereTglSurat($value)
 * @mixin \Eloquent
 */
class RsiaSuratMasuk extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rsia_surat_masuk';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'no';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['no'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'no_simrs'  => 'date',
        'tgl_surat' => 'date',
        'no_surat'  => 'string',
    ];
}
