<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\RsiaSpo
 *
 * @property string $id
 * @property string $nomor
 * @property string $status
 * @property string $judul
 * @property string $tgl_terbit
 * @property string $unit_id
 * @property string $jenis
 * @property string $pengertian
 * @property string $tujuan
 * @property string $kebijakan
 * @property string $prosedur
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSpo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSpo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSpo query()
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSpo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSpo whereNomor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSpo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSpo whereTglTerbit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSpo whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RsiaSpo whereJenis($value)
 * @mixin \Eloquent
 */
class RsiaSpo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rsia_spo';

    protected $guarded = [
        'id'
    ];

    protected $primaryKey = 'id';

    public $timestamps = true;

    public $incrementing = true;

    public $casts = [
        'nomor' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function units()
    {
        return $this->hasMany(RsiaSpoUnits::class, 'spo_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Departemen::class, 'unit_id', 'dep_id');
    }
}
