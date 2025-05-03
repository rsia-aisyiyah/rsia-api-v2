<?php

namespace App\Http\Controllers\Orion;

use App\Models\Departemen;
use Illuminate\Http\Request;

class DepartemenController extends \Orion\Http\Controllers\Controller
{
    use \Orion\Concerns\DisableAuthorization;

    protected $model = Departemen::class;

    /**
     * Retrieves currently authenticated user based on the guard.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function resolveUser()
    {
        return \Illuminate\Support\Facades\Auth::user();
    }

    /**
     * The attributes that are used for filtering.
     *
     * @return array
     */
    public function filterableBy(): array
    {
        return ['nama', 'dep_id', 'aktif'];
    }

    /**
     * The attributes that are used for sorting.
     *
     * @return array
     */
    public function sortableBy(): array
    {
        return ['dep_id', 'nama', 'kelompok'];
    }

    /**
     * The attributes that are used for searching.
     *
     * @return array
     */
    public function searchableBy(): array
    {
        return ['dep_id', 'nama', 'kelompok'];
    }
}
