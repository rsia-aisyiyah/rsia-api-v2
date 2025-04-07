<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsiaAppMenu extends Model
{
    use HasFactory;

    protected $table = 'rsia_app_menu';

    public $timestamps = false;

    protected $guarded = ['id'];
}
