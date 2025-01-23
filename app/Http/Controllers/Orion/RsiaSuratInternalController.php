<?php

namespace App\Http\Controllers\Orion;

// use Illuminate\Http\Request;
use Orion\Http\Requests\Request;
use Illuminate\Database\Eloquent\Model;
use Orion\Concerns\DisableAuthorization;
use Illuminate\Database\Eloquent\Builder;

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
     * @return Builder 
     */
    protected function buildIndexFetchQuery(Request $request, array $requestedRelations): Builder
    {
        return parent::buildIndexFetchQuery($request, $requestedRelations)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Fills attributes on the given entity and stores it in database.
     *
     * @param Request $request
     * @param Model $entity
     * @param array $attributes
     */
    protected function performStore(Request $request, Model $entity, array $attributes): void
    {
        $suratData = [
            'perihal'      => $request->perihal,
            'tgl_terbit'   => $request->tgl_terbit,
            'status'       => $request->status,
            'pj'           => $request->pj,
            'requested_by' => $this->resolveUser()->id_user,
            'catatan'      => $request->catatan,
            'status'       => $request->status ?? "pengajuan",
        ];

        if ($request->status == 'disetujui') {
            $last_nomor = \App\Models\RsiaSuratInternal::select('no_surat')
                ->orderBy('created_at', 'desc')
                ->where('no_surat', "<>", null)
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

            $suratData['no_surat'] = $nomor;
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
    protected function performUpdate(Request $request, Model $e, array $attributes): void
    {
        $suratData = [
            'perihal'      => $request->perihal,
            'tgl_terbit'   => $request->tgl_terbit,
            'status'       => $request->status,
            'pj'           => $request->pj,
            'catatan'      => $request->catatan,
            'status'       => $request->status ?? "pengajuan",
        ];

        if ($request->status == 'disetujui' && !$e->no_surat) {
            $last_nomor = \App\Models\RsiaSuratInternal::select('no_surat')
                ->orderBy('created_at', 'desc')
                ->where('no_surat', "<>", null)
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

            $suratData['verified_at'] = now();
            $suratData['no_surat'] = $nomor;
        }

        if ($request->status == 'disetujui') {
            $suratData['verified_at'] = now();
        } else {
            $suratData['verified_at'] = null;
        }

        if (!$e->requested_by) {
            $suratData['requested_by'] = $this->resolveUser()->id_user;
        }

        $this->performFill($request, $e, $suratData);
        $e->save();
    }

    /**
     * The hook is executed after creating new resource.
     *
     * @param Request $request
     * @param Model $entity
     * @return mixed
     */
    protected function afterStore(Request $request, Model $entity)
    {
        if ($request->undangan) {
            $undanganData = [
                'surat_id'   => $entity->id,
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
    protected function afterUpdate(Request $request, Model $entity)
    {
        if ($request->undangan) {
            $undanganData = [
                'model'      => \App\Models\RsiaSuratInternal::class,
                'tanggal'    => $request->undangan['tanggal'],
                'perihal'    => $request->perihal,
                'lokasi'     => $request->undangan['lokasi'],
                'deskripsi'  => $request->undangan['deskripsi'],
                'catatan'    => $request->undangan['catatan'],
                'pj'         => $request->pj,
            ];

            \App\Models\RsiaUndangan::updateOrCreate(
                ['surat_id' => $entity->id],
                $undanganData
            );
        } else {
            \App\Models\RsiaUndangan::where('surat_id', $entity->id)->delete();
        }
    }

    /**
     * The hook is executed after deleting a resource.
     *
     * @param Request $request
     * @param Model $entity
     * @return mixed
     */
    protected function afterDestroy(Request $request, Model $entity)
    {
        \App\Models\RsiaUndangan::where('surat_id', $entity->id)->delete();
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
