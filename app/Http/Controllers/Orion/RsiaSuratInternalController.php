<?php

namespace App\Http\Controllers\Orion;

use Illuminate\Http\Request;
use Orion\Concerns\DisableAuthorization;

class RsiaSuratInternalController extends \Orion\Http\Controllers\Controller
{
    use DisableAuthorization;

    /**
     * Fully-qualified model class name
     * 
     * @var string $model
     */
    protected $model = \App\Models\RsiaSuratInternal::class;

    /**
     * Request class for the current resource
     * 
     * @var string $request
     */
    protected $request = \App\Http\Requests\SuratInternalRequest::class;

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
            ->orderBy('created_at', 'desc');
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
     * @param \Orion\Http\Requests\Request $request
     * @param \Illuminate\Database\Eloquent\Model $entity
     * @param array $attributes
     */
    protected function performStore(\Orion\Http\Requests\Request $request, \Illuminate\Database\Eloquent\Model $entity, array $attributes): void
    {
        $last_nomor = \App\Models\RsiaSuratInternal::select('no_surat')
            ->orderBy('created_at', 'desc')
            ->whereYear('tgl_terbit', \Carbon\Carbon::parse($request->tgl_terbit)->year)
            ->first();

        if ($last_nomor) {
            $last_nomor = explode('/', $last_nomor->no_surat);
            $last_nomor[0] = str_pad($last_nomor[0] + 1, 3, '0', STR_PAD_LEFT);
            $last_nomor[3] = \Carbon\Carbon::parse($request->tgl_terbit)->format('dmy');
            $last_nomor = implode('/', $last_nomor);
        } else {
            $last_nomor = '001/A/S-RSIA/' . \Carbon\Carbon::parse($request->tgl_terbit)->format('dmy');
        }

        $nomor = $last_nomor;

        $suratData = [
            'no_surat'   => $nomor,
            'perihal'    => $request->perihal,
            'pj'         => $request->pj,
            'tgl_terbit' => $request->tgl_terbit,
            'status'     => $request->status,
        ];

        $this->performFill($request, $entity, $suratData);
        $entity->save();
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
        $suratData = [
            'perihal'    => $request->perihal,
            'pj'         => $request->pj,
            'tgl_terbit' => $request->tgl_terbit,
            'status'     => $request->status,
        ];

        $this->performFill($request, $e, $suratData);
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
        if ($request->undangan) {
            $undanganData = [
                'no_surat'   => $entity->no_surat,
                'model'      => \App\Models\RsiaSuratInternal::class,
                'tanggal'    => $request->undangan['tanggal'],
                'perihal'    => $request->perihal,
                'lokasi'     => $request->undangan['lokasi'],
                'deskripsi'  => $request->undangan['deskripsi'],
                'catatan'    => $request->undangan['catatan'],
                'pj'         => $request->pj,
                // 'status'     => $request->undangan['status'],
            ];

            $undangan = new \App\Models\RsiaUndangan();
            $undangan->fill($undanganData);
            $undangan->save();
        }
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
        if ($request->undangan) {
            $undanganData = [
                'no_surat'   => $entity->no_surat,
                'model'      => \App\Models\RsiaSuratInternal::class,
                'tanggal'    => $request->undangan['tanggal'],
                'perihal'    => $request->perihal,
                'lokasi'     => $request->undangan['lokasi'],
                'deskripsi'  => $request->undangan['deskripsi'],
                'catatan'    => $request->undangan['catatan'],
                'pj'         => $request->pj,
                // 'status'     => $request->undangan['status'],
            ];

            $undangan = \App\Models\RsiaUndangan::where('no_surat', $entity->no_surat)->first();
            $undangan->fill($undanganData);
            $undangan->save();
        }
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
     * The relations and fields that are allowed to be aggregated on a resource.
     *
     * @return array
     */
    public function aggregates(): array
    {
        return ['penerima_undangan'];
    }

    /**
     * The list of available query scopes.
     *
     * @return array
     */
    public function exposedScopes(): array
    {
        return ['hasPenerima'];
    }

    /**
     * The attributes that are used for filtering.
     *
     * @return array
     */
    public function filterableBy(): array
    {
        return ['tgl_terbit', 'tanggal', 'status', 'no_surat', 'pj'];
    }

    /**
     * The attributes that are used for sorting.
     *
     * @return array
     */
    public function sortableBy(): array
    {
        return ['penanggungJawabSimple.nama', 'pj', 'tempat', 'no_surat', 'created_at'];
    }

    /**
     * The relations that are always included together with a resource.
     *
     * @return array
     */
    public function alwaysIncludes(): array
    {
        return ['penanggungJawabSimple'];
    }

    /**
     * The relations that are allowed to be included together with a resource.
     *
     * @return array
     */
    public function includes(): array
    {
        return ['penerimaUndangan', 'undangan'];
    }

    /**
     * The attributes that are used for searching.
     *
     * @return array
     */
    public function searchableBy(): array
    {
        return ['perihal', 'penanggungJawabSimple.nama', 'no_surat'];
    }
}
