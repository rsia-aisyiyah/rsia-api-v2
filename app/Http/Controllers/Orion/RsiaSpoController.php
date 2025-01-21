<?php

namespace App\Http\Controllers\Orion;

use Illuminate\Http\Request;

// A = Medis, B = Penunjang, C = Umum
class RsiaSpoController extends \Orion\Http\Controllers\Controller
{
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
     * Runs the given query for fetching entity in show method.
     *
     * @param Request $request
     * @param Builder $query
     * @param int|string $key
     * @return Model
     */
    protected function runShowFetchQuery(Request $request, \Illuminate\Database\Eloquent\Builder $q, $key): \Illuminate\Database\Eloquent\Model
    {
        // try decoding the key using base64
        try {
            $key = base64_decode($key);
        } catch (\Exception $e) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
        }

        return $this->runFetchQuery($request, $q, $key);
    }

    /**
     * Fills attributes on the given entity and stores it in database.
     *
     * @param Request $request
     * @param Model $entity
     * @param array $attributes
     */
    protected function performStore(\Orion\Http\Requests\Request $request, \Illuminate\Database\Eloquent\Model $e, array $attributes): void
    {
        // Retrieve the maximum 'nomor' for the given year and jenis
        $maxNomor = $this->model::whereYear('tgl_terbit', $request->tgl_terbit)
            ->where('jenis', $request->jenis)
            ->max('nomor');

        // Generate the next nomor
        $lastNomor = $maxNomor ? str_pad((int)explode('/', $maxNomor)[0] + 1, 3, '0', STR_PAD_LEFT) : '001';

        // Map jenis to its corresponding code
        $jenisMapping = [
            'medis'     => 'A',
            'penunjang' => 'B',
            'umum'      => 'C',
        ];

        $codeJenis = $jenisMapping[strtolower($request->jenis)] ?? 'X';

        // Format the date to DDMMYY
        $formatDate = \Carbon\Carbon::parse($request->tgl_terbit)->format('dmy');

        // Build the final nomor
        $buildedNomor = "{$lastNomor}/{$codeJenis}/SPO-RSIA/{$formatDate}";


        $newAttributes = [
            'nomor'        => $buildedNomor,
            'judul'        => $request->judul,
            'unit'         => $request->unit,
            'unit_terkait' => $request->unit_terkait,
            'tgl_terbit'   => $request->tgl_terbit,
            'jenis'        => $request->jenis,
        ];

        // Fill the entity with the attributes
        $this->performFill($request, $e, $newAttributes);
        $e->save();
    }

    /**
     * Runs the given query for fetching entity in update method.
     *
     * @param Request $request
     * @param Builder $query
     * @param int|string $key
     * @return Model
     */
    protected function runUpdateFetchQuery(Request $request, \Illuminate\Database\Eloquent\Builder $q, $key): \Illuminate\Database\Eloquent\Model
    {
        // try decoding the key using base64
        try {
            $key = base64_decode($key);
        } catch (\Exception $e) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
        }

        return $this->runFetchQuery($request, $q, $key);
    }

    /**
     * Fills attributes on the given entity and persists changes in database.
     *
     * @param Request $request
     * @param Model $entity
     * @param array $attributes
     */
    protected function performUpdate(Request $request, \Illuminate\Database\Eloquent\Model $e, array $attributes): void
    {
        $newAttributes = [
            'nomor'        => $e->nomor,
            'judul'        => $request->judul,
            'unit'         => $request->unit,
            'unit_terkait' => $request->unit_terkait,
            'tgl_terbit'   => $request->tgl_terbit,
            'jenis'        => $request->jenis,
        ];

        // Fill the entity with the attributes
        $this->performFill($request, $e, $newAttributes);
        $e->save();
    }

    /**
     * Fetches the model that has just been updated using the given key.
     *
     * @param Request $request
     * @param array $requestedRelations
     * @param int|string $key
     * @return Model
     */
    protected function refreshUpdatedEntity(Request $request, array $requestedRelations, $key): \Illuminate\Database\Eloquent\Model
    {
        $query = $this->buildFetchQueryBase($request, $requestedRelations);

        try {
            $key = base64_decode($key);
        } catch (\Exception $e) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
        }

        return $this->runFetchQueryBase($request, $query, $key);
    }

    /**
     * The hook is executed after creating new resource.
     *
     * @param Request $request
     * @param Model $entity
     * @return mixed
     */
    protected function afterStore(Request $request, \Illuminate\Database\Eloquent\Model $entity)
    {
        $detailData = [
            'nomor'      => $entity->nomor,
            'pengertian' => \Stevebauman\Purify\Facades\Purify::clean($request->pengertian),
            'tujuan'     => \Stevebauman\Purify\Facades\Purify::clean($request->tujuan),
            'kebijakan'  => \Stevebauman\Purify\Facades\Purify::clean($request->kebijakan),
            'prosedur'   => \Stevebauman\Purify\Facades\Purify::clean($request->prosedur),
        ];

        \Illuminate\Support\Facades\DB::transaction(function () use ($entity, $detailData) {
            $entity->detail()->create($detailData);
        }, 5);
    }

    /**
     * The hook is executed after updating a resource.
     *
     * @param Request $request
     * @param Model $entity
     * @return mixed
     */
    protected function afterUpdate(Request $request, \Illuminate\Database\Eloquent\Model $entity)
    {
        $detailData = [
            'nomor' => $entity->nomor,
            'pengertian' => $request->pengertian,
            'tujuan' => $request->tujuan,
            'kebijakan' => $request->kebijakan,
            'prosedur' => $request->prosedur,
        ];

        \Illuminate\Support\Facades\DB::transaction(function () use ($entity, $detailData) {
            $entity->detail()->updateOrCreate(['nomor' => $entity->nomor], $detailData);
        }, 5);
    }

    /**
     * Runs the given query for fetching entity in restore method.
     *
     * @param Request $request
     * @param Builder $query
     * @param int|string $key
     * @return Model
     */
    protected function runRestoreFetchQuery(Request $request, \Illuminate\Database\Eloquent\Builder $q, $key): \Illuminate\Database\Eloquent\Model
    {
        try {
            $key = base64_decode($key);
        } catch (\Exception $e) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
        }
        
        return $this->runFetchQuery($request, $q, $key);
    }

    /**
     * Runs the given query for fetching entity in destroy method.
     *
     * @param Request $request
     * @param Builder $query
     * @param int|string $key
     * @return Model
     */
    protected function runDestroyFetchQuery(Request $request, \Illuminate\Database\Eloquent\Builder $q, $key): \Illuminate\Database\Eloquent\Model
    {
        try {
            $key = base64_decode($key);
        } catch (\Exception $e) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
        }

        return $this->runFetchQuery($request, $q, $key);
    }

    /**
     * Retrieves currently authenticated user based on the guard.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function resolveUser()
    {
        return \Illuminate\Support\Facades\Auth::guard('user-aes')->user();
    }

    /**
     * The attributes that are used for sorting.
     *
     * @return array
     */
    public function sortableBy(): array
    {
        return ['nomor', 'judul', 'unit', 'tgl_terbit', 'jenis'];
    }

    /**
     * The attributes that are used for filtering.
     *
     * @return array
     */
    public function filterableBy(): array
    {
        return ['nomor', 'unit', 'tgl_terbit', 'jenis', 'status'];
    }

    /**
     * The attributes that are used for searching.
     *
     * @return array
     */
    public function searchableBy(): array
    {
        return ['nomor', 'judul', 'unit', 'unit_terkait'];
    }

    /**
     * The relations that are allowed to be included together with a resource.
     *
     * @return array
     */
    public function includes(): array
    {
        return ['detail'];
    }

    /**
     * The relations that are allowed to be always included together with a resource.
     * 
     * @return array
     * */
    public function alwaysIncludes(): array
    {
        return ['detail'];
    }
}
