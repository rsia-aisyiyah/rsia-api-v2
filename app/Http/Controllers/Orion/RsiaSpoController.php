<?php

namespace App\Http\Controllers\Orion;

use Orion\Http\Requests\Request;

// A = Medis, B = Penunjang, C = Umum
class RsiaSpoController extends \Orion\Http\Controllers\Controller
{
    protected $jenisMapping = [
        'medis'     => 'A',
        'penunjang' => 'B',
        'umum'      => 'C',
    ];

    /**
     * Disable authorization for all actions
     * 
     * @var bool
     * */
    use \Orion\Concerns\DisableAuthorization;

    /**
     * Model class for Dokter
     * 
     * @var string
     * */
    protected $model = \App\Models\RsiaSpo::class;

    /**
     * The relations that are allowed to be included together with a resource.
     * 
     * @param Request $request
     * @param array $requestedRelations
     * @return \Illuminate\Database\Eloquent\Builder 
     */
    protected function buildIndexFetchQuery(Request $request, array $requestedRelations): \Illuminate\Database\Eloquent\Builder
    {
        return parent::buildIndexFetchQuery($request, $requestedRelations)
            ->orderBy('tgl_terbit', 'desc');
    }

    /**
     * Fills attributes on the given entity and stores it in database.
     *
     * @param Request $request
     * @param \Illuminate\Database\Eloquent\Model $entity
     * @param array $attributes
     */
    protected function performStore(Request $request, \Illuminate\Database\Eloquent\Model $e, array $attributes): void
    {
        $spoData = [
            'status'       => $request->status ?? "pengajuan",
            'judul'        => $request->judul,
            'tgl_terbit'   => $request->tgl_terbit,
            'unit_id'      => $request->unit_id,
            'jenis'        => $request->jenis,
            'pengertian'   => $request->pengertian,
            'tujuan'       => $request->tujuan,
            'kebijakan'    => $request->kebijakan,
            'prosedur'     => $request->prosedur,
        ];

        if ($spoData['status'] == 'disetujui') {
            $lastNomor = $this->model::whereYear('tgl_terbit', $request->tgl_terbit)
                ->where('jenis', $request->jenis)
                ->where('no_surat', "<>", null)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastNomor) {
                $lastNomor    = explode('/', $lastNomor->nomor);
                $lastNomor[0] = str_pad(($lastNomor[0] + 1), 3, '0', STR_PAD_LEFT);
                $lastNomor[3] = \Carbon\Carbon::parse($request->tgl_terbit)->format('dmy');
                $lastNomor = implode('/', $lastNomor);
            } else {
                $lastNomor = implode('/', [
                    '001',
                    $this->jenisMapping[\Str::lower($request->jenis)] ?? 'X',
                    'SPO-RSIA',
                    \Carbon\Carbon::parse($request->tgl_terbit)->format('dmy'),
                ]);
            }

            $spoData['nomor'] = $lastNomor;
        }

        $this->performFill($request, $e, $spoData);
        $e->save();
    }

    /**
     * Fills attributes on the given entity and persists changes in database.
     *
     * @param Request $request
     * @param \Illuminate\Database\Eloquent\Model $entity
     * @param array $attributes
     */
    protected function performUpdate(Request $request, \Illuminate\Database\Eloquent\Model $e, array $attributes): void
    {
        $spoData = [
            'status'       => $request->status ?? "pengajuan",
            'judul'        => $request->judul,
            'tgl_terbit'   => $request->tgl_terbit,
            'unit_id'      => $request->unit,
            'jenis'        => $request->jenis,
            'pengertian'   => \Stevebauman\Purify\Facades\Purify::clean($request->pengertian),
            'tujuan'       => \Stevebauman\Purify\Facades\Purify::clean($request->tujuan),
            'kebijakan'    => \Stevebauman\Purify\Facades\Purify::clean($request->kebijakan),
            'prosedur'     => \Stevebauman\Purify\Facades\Purify::clean($request->prosedur),
        ];

        if ($request->status == 'disetujui' && !$e->nomor) {
            $lastNomor = $this->model::whereYear('tgl_terbit', $request->tgl_terbit)
                ->where('jenis', $request->jenis)
                ->where('no_surat', "<>", null)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastNomor) {
                $lastNomor    = explode('/', $lastNomor->nomor);
                $lastNomor[0] = str_pad(($lastNomor[0] + 1), 3, '0', STR_PAD_LEFT);
                $lastNomor[3] = \Carbon\Carbon::parse($request->tgl_terbit)->format('dmy');
                $lastNomor = implode('/', $lastNomor);
            } else {
                $lastNomor = implode('/', [
                    '001',
                    $this->jenisMapping[\Str::lower($request->jenis)] ?? 'X',
                    'SPO-RSIA',
                    \Carbon\Carbon::parse($request->tgl_terbit)->format('dmy'),
                ]);
            }

            $spoData['nomor'] = $lastNomor;
        }

        $this->performFill($request, $e, $spoData);
        $e->save();
    }

    /**
     * The hook is executed after creating new resource.
     *
     * @param Request $request
     * @param \Illuminate\Database\Eloquent\Model $entity
     * @return mixed
     */
    protected function afterStore(Request $request, \Illuminate\Database\Eloquent\Model $entity)
    {
        if ($request->semua_unit_terkait) {
            $depIds = \App\Models\Departemen::where('aktif', '1')->pluck('dep_id')->toArray(); // Ambil semua id departemen
            $spoId = $entity->id;

            \Illuminate\Support\Facades\DB::transaction(function () use ($spoId, $depIds) {
                foreach ($depIds as $depId) {
                    $unitData = [
                        'spo_id'   => $spoId,
                        'unit_id'  => $depId,  // Asumsikan bahwa Departemen ID adalah unit yang dimaksud
                    ];

                    // Simpan setiap unit terkait di model RsiaSpoUnits
                    \App\Models\RsiaSpoUnits::create($unitData);
                }
            }, 5);
        } else {
            $spoId = $entity->id;
            $units = $request->units;
            
            \Illuminate\Support\Facades\DB::transaction(function () use ($spoId, $units) {
                foreach ($units as $unit) {
                    $unitData = [
                        'spo_id'  => $spoId,
                        'unit_id' => $unit,
                    ];
    
                    \App\Models\RsiaSpoUnits::create($unitData);
                }
            }, 5);
        }
    }

    /**
     * The hook is executed after updating a resource.
     *
     * @param Request $request
     * @param \Illuminate\Database\Eloquent\Model $entity
     * @return mixed
     */
    protected function afterUpdate(Request $request, \Illuminate\Database\Eloquent\Model $entity)
    {
        if ($request->semua_unit_terkait) {
            $spoId = $entity->id;
            $depIds = \App\Models\Departemen::where('aktif', '1')->pluck('dep_id')->toArray(); // Ambil semua id departemen

            // Menghapus unit lama
            \App\Models\RsiaSpoUnits::where('spo_id', $spoId)->delete();

            \Illuminate\Support\Facades\DB::transaction(function () use ($spoId, $depIds) {
                // Insert unit terkait untuk semua departemen aktif
                foreach ($depIds as $depId) {
                    $unitData = [
                        'spo_id'   => $spoId,
                        'unit_id'  => $depId,  // Asumsikan departemen adalah unit yang dimaksud
                    ];
    
                    \App\Models\RsiaSpoUnits::create($unitData);
                }
            }, 5);
        } else {
            $spoId = $entity->id;
            $units = $request->units;
            
            // delete old units
            \App\Models\RsiaSpoUnits::where('spo_id', $spoId)->delete();
    
            \Illuminate\Support\Facades\DB::transaction(function () use ($spoId, $units) {
                // insert new units
                foreach ($units as $unit) {
                    $unitData = [
                        'spo_id'  => $spoId,
                        'unit_id' => $unit,
                    ];
    
                    \App\Models\RsiaSpoUnits::create($unitData);
                }
    
            }, 5);
        }
    }

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
     * The attributes that are used for sorting.
     *
     * @return array
     */
    public function sortableBy(): array
    {
        return ['nomor', 'judul', 'unit_id', 'tgl_terbit', 'jenis', 'status', 'created_at', 'deleted_at'];
    }

    /**
     * The attributes that are used for filtering.
     *
     * @return array
     */
    public function filterableBy(): array
    {
        return ['nomor', 'unit_id', 'tgl_terbit', 'jenis', 'status'];
    }

    /**
     * The attributes that are used for searching.
     *
     * @return array
     */
    public function searchableBy(): array
    {
        return ['nomor', 'judul', 'unit_id', 'jenis'];
    }

    /**
     * The relations that are allowed to be included together with a resource.
     *
     * @return array
     */
    public function includes(): array
    {
        return ['unit', 'units', 'units.unit'];
    }

    /**
     * The relations that are allowed to be always included together with a resource.
     * 
     * @return array
     * */
    public function alwaysIncludes(): array
    {
        return ['unit'];
    }
}
