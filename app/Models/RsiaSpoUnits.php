<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaSpoUnits extends Model
{
    use HasFactory;

    protected $table = 'rsia_spo_units';

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'id'
    ];

    protected $primaryKey = 'id';

    public $timestamps = true;

    public $incrementing = true;

    public $casts = [
        'spo_id' => 'string',
        'unit_id' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];


    public function unit()
    {
        return $this->belongsTo(Departemen::class, 'unit_id', 'dep_id');
    }
}
