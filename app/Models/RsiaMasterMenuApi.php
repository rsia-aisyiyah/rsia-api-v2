<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaMasterMenuApi extends Model
{
    use HasFactory;

    protected $table = 'rsia_master_menu_api';

    protected $guarded = ['id'];

    // cast
    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
    ];

    protected $hidden = [
        'id', 'client_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
