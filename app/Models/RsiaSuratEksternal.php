<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RsiaSuratEksternal
 *
 * @property string $no_surat
 * @property string|null $perihal
 * @property string|null $alamat
 * @property string $tgl_terbit
 * @property string|null $pj
 * @property string|null $tanggal
 * @property string|null $created_at
 * @property-read \App\Models\Pegawai|null $penanggung_jawab
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratEksternal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratEksternal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratEksternal query()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratEksternal whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratEksternal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratEksternal whereNoSurat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratEksternal wherePerihal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratEksternal wherePj($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratEksternal whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSuratEksternal whereTglTerbit($value)
 * @property-read \App\Models\Pegawai|null $penanggungJawab
 * @property-read \App\Models\Pegawai|null $penanggungJawabSimple
 * @mixin \Eloquent
 */
class RsiaSuratEksternal extends Model
{
    use HasFactory;

    protected $table = 'rsia_surat_eksternal';

    protected $primaryKey = 'id';

    protected $casts = [
        'no_surat'   => 'string',
        'tgl_terbit' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $guarded = [];

    public $incrementing = true;

    public $timestamps = true;


    public function penanggungJawab()
    {
        return $this->belongsTo(Pegawai::class, 'pj', 'nik')
            ->select('nik', 'nama', 'jbtn', 'departemen', 'bidang', 'jnj_jabatan');
    }

    public function penanggungJawabSimple()
    {
        return $this->belongsTo(Pegawai::class, 'pj', 'nik')->select('nik', 'nama');
    }
}
