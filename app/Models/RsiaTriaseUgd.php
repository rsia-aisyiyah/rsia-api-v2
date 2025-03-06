<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaTriaseUgd extends Model
{
    use HasFactory;

    protected $table = 'rsia_triase_ugd';

    protected $primaryKey = 'no_rawat';

    public $incrementing = false;

    public $timestamps = false;

    protected $guarded = [];

    protected $with = [
        'skala1', 'skala2', 'skala3', 'skala4', 'skala5'
    ];


    /**
     * Get the skala1 that owns the RsiaTriaseUgd
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function skala1()
    {
        return $this->hasMany(RsiaDataTriaseUgddetailSkala1::class, 'no_rawat', 'no_rawat');
    }

    /**
     * Get the skala2 that owns the RsiaTriaseUgd
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function skala2()
    {
        return $this->hasMany(RsiaDataTriaseUgddetailSkala2::class, 'no_rawat', 'no_rawat');
    }
    /**
     * Get the skala3 that owns the RsiaTriaseUgd
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function skala3()
    {
        return $this->hasMany(RsiaDataTriaseUgddetailSkala3::class, 'no_rawat', 'no_rawat');
    }
    /**
     * Get the skala4 that owns the RsiaTriaseUgd
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function skala4()
    {
        return $this->hasMany(RsiaDataTriaseUgddetailSkala4::class, 'no_rawat', 'no_rawat');
    }
    /**
     * Get the skala5 that owns the RsiaTriaseUgd
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function skala5()
    {
        return $this->hasMany(RsiaDataTriaseUgddetailSkala5::class, 'no_rawat', 'no_rawat');
    }

    public function asmedUgd()
    {
        return $this->hasOne(PenilaianMedisIgd::class, 'no_rawat', 'no_rawat');
    }
}
