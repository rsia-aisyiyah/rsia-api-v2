<?php

namespace App\Http\Controllers\Orion;

use Illuminate\Http\Request;
use Orion\Http\Controllers\Controller;
use Orion\Concerns\DisableAuthorization;

class RsiaSuratEksternalController extends Controller
{
    use DisableAuthorization;

    /**
     * Fully-qualified model class name
     */
    protected $model = \App\Models\RsiaSuratEksternal::class;

    /**
     * Request class for the current resource
     * 
     * @var string $request
     */
    protected $request = \App\Http\Requests\SuratEksternalRequest::class;

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
     * Fills attributes on the given entity and stores it in database.
     *
     * @param \Orion\Http\Requests\Request $request
     * @param \Illuminate\Database\Eloquent\Model $entity
     * @param array $attributes
     */
    protected function performStore(\Orion\Http\Requests\Request $request, \Illuminate\Database\Eloquent\Model $entity, array $attributes): void
    {
        $suratData = [
            'perihal'      => $request->perihal,
            'alamat'       => $request->alamat,
            'tgl_terbit'   => $request->tgl_terbit,
            'pj'           => $request->pj,
            'status'       => $request->status ?? "pengajuan",
            'requested_by' => $this->resolveUser()->id_user,
        ];

        if ($request->status == 'disetujui') {
            $lastNomor = \App\Models\RsiaSuratEksternal::select('no_surat')
                ->orderBy('created_at', 'desc')
                ->where('no_surat', '<>', null)
                ->whereYear('tgl_terbit', \Carbon\Carbon::parse($request->tgl_terbit)->year)
                ->first();

            if ($lastNomor) {
                $n = explode('/', $lastNomor->no_surat);
                $n[0] = str_pad($n[0] + 1, 3, '0', STR_PAD_LEFT);
                $n[3] = \Carbon\Carbon::parse($request->tgl_terbit)->format('dmy');
                $n = implode('/', $n);
            } else {
                $n = '001/B/S-RSIA' . \Carbon\Carbon::parse($request->tgl_terbit)->format('dmy');
            }

            $suratData['no_surat'] = $n;
            $suratData['verified_at'] = now();
        }

        $this->performFill($request, $entity, $suratData);
        $entity->save();
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
            'perihal'      => $request->perihal,
            'alamat'       => $request->alamat,
            'tgl_terbit'   => $request->tgl_terbit,
            'pj'           => $request->pj,
            'status'       => $request->status ?? "pengajuan",
            'requested_by' => $this->resolveUser()->id_user,
            'verified_at' => $request->status == 'disetujui' ? now() : null,
        ];

        if ($request->status == 'disetujui' && !$e->no_surat) {
            $lastNomor = \App\Models\RsiaSuratEksternal::select('no_surat')
                ->orderBy('created_at', 'desc')
                ->where('no_surat', '<>', null)
                ->whereYear('tgl_terbit', \Carbon\Carbon::parse($request->tgl_terbit)->year)
                ->first();

            if ($lastNomor) {
                $n = explode('/', $lastNomor->no_surat);
                $n[0] = str_pad($n[0] + 1, 3, '0', STR_PAD_LEFT);
                $n[3] = \Carbon\Carbon::parse($request->tgl_terbit)->format('dmy');
                $n = implode('/', $n);
            } else {
                $n = '001/B/S-RSIA' . \Carbon\Carbon::parse($request->tgl_terbit)->format('dmy');
            }

            $suratData['no_surat'] = $n;
            $suratData['verified_at'] = now();
        }

        if (!$e->requested_by) {
            $suratData['requested_by'] = $this->resolveUser()->id_user;
        }

        $this->performFill($request, $e, $suratData);
        $e->save();
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
     * The attributes that are used for filtering.
     *
     * @return array
     */
    public function filterableBy(): array
    {
        return ['tanggal', 'status', 'no_surat', 'pj'];
    }

    /**
     * The attributes that are used for sorting.
     *
     * @return array
     */
    public function sortableBy(): array
    {
        return ['penanggungJawab.nama', 'pj', 'alamat', 'no_surat', 'created_at'];
    }

    /**
     * The relations that are always included together with a resource.
     *
     * @return array
     */
    public function alwaysIncludes(): array
    {
        return ['penanggungJawab'];
    }

    /**
     * The relations that are allowed to be included together with a resource.
     *
     * @return array
     */
    public function includes(): array
    {
        return ['penanggungJawab'];
    }

    /**
     * The attributes that are used for searching.
     *
     * @return array
     */
    public function searchableBy(): array
    {
        return ['perihal', 'alamat', 'penanggungJawabSimple.nama'];
    }
}
