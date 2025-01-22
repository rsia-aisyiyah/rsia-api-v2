<?php

namespace App\Http\Controllers\Orion;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Orion\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Orion\Concerns\DisableAuthorization;
use Illuminate\Database\Eloquent\Builder;

class RsiaSuratMasukController extends Controller
{
    use DisableAuthorization;

    /**
     * Fully-qualified model class name
     */
    protected $model = \App\Models\RsiaSuratMasuk::class;

    /**
     * Request class for the current resource
     * 
     * @var string $request
     */
    protected $request = \App\Http\Requests\SuratMasukRequest::class;

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
            ->orderBy('no', 'desc');
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
        $file      = $request->file('file');
        $file_name = null;

        if ($file && $file->isValid()) {
            $request->validate([
                'file' => 'required|file|mimes:pdf|max:64000',
            ]);

            $original_name = $file->getClientOriginalName();
            [$name, $extension] = explode('.', $original_name);

            $file_name = strtotime(now()) . '-' . Str::slug($name) . '.' . $extension;
            $storageDisk = \Storage::disk('sftp');
            $saveLocation = env('DOCUMENT_SURAT_MASUK_SAVE_LOCATION');

            if (!$storageDisk->exists($saveLocation)) {
                $storageDisk->makeDirectory($saveLocation);
            }
        }

        if ($request->has('pelaksanaan') && $request->input('pelaksanaan') != 'null') {
            $pelaksanaanDate    = $request->pelaksanaan ? date('Y-m-d', strtotime($request->pelaksanaan)) : null;
        }

        if ($request->has('pelaksanaan_end') && $request->input('pelaksanaan_end') != 'null') {
            $pelaksanaanEndDate = $request->pelaksanaan_end ? date('Y-m-d', strtotime($request->pelaksanaan_end)) : null;
        }

        $data = [
            'no_simrs'        => $request->input('no_simrs'),
            'no_surat'        => $request->input('no_surat'),
            'pengirim'        => $request->input('pengirim'),
            'tgl_surat'       => $request->input('tgl_surat'),
            'perihal'         => $request->input('perihal'),
            'pelaksanaan'     => $pelaksanaanDate ?? null,
            'pelaksanaan_end' => $pelaksanaanEndDate ?? null,
            'tempat'          => $request->input('tempat') ?? null,
            'ket'             => $request->input('ket'),
            'status'          => "1",
            'berkas'          => $file_name
        ];

        $this->performFill($request, $entity, $data);
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
        $file      = $request->file('file');
        $file_name = null;

        \Log::warning("LOGGING " . __FUNCTION__, [
            'file' => $file,
            'file_name' => $file_name,
            'request' => $request->all(),
            'attributes' => $attributes,
        ]);

        if ($file && $file->isValid()) {
            $request->validate([
                'file' => 'required|file|mimes:pdf|max:64000',
            ]);

            $original_name = $file->getClientOriginalName();
            [$name, $extension] = explode('.', $original_name);

            $file_name = strtotime(now()) . '-' . Str::slug($name) . '.' . $extension;
            $storageDisk = \Storage::disk('sftp');
            $saveLocation = env('DOCUMENT_SURAT_MASUK_SAVE_LOCATION');

            if (!$storageDisk->exists($saveLocation)) {
                $storageDisk->makeDirectory($saveLocation);
            }
        }

        if ($request->has('pelaksanaan') && $request->input('pelaksanaan') != 'null') {
            $pelaksanaanDate    = $request->pelaksanaan ? date('Y-m-d', strtotime($request->pelaksanaan)) : null;
        }

        if ($request->has('pelaksanaan_end') && $request->input('pelaksanaan_end') != 'null') {
            $pelaksanaanEndDate = $request->pelaksanaan_end ? date('Y-m-d', strtotime($request->pelaksanaan_end)) : null;
        }

        $data = [
            'no_simrs'        => $request->input('no_simrs'),
            'no_surat'        => $request->input('no_surat'),
            'pengirim'        => $request->input('pengirim'),
            'tgl_surat'       => $request->input('tgl_surat'),
            'perihal'         => $request->input('perihal'),
            'pelaksanaan'     => $pelaksanaanDate ?? null,
            'pelaksanaan_end' => $pelaksanaanEndDate ?? null,
            'tempat'          => $request->input('tempat') ?? null,
            'ket'             => $request->input('ket'),
            'status'          => "1",
        ];

        if ($file_name) {
            $data['berkas'] = $file_name;
        }

        $this->performFill($request, $e, $data);
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
        \Log::info("LOGGING " . __FUNCTION__, [
            'request' => $request->all(),
            'key' => $key,
        ]);

        return $this->runFetchQuery($request, $q, $key);
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
        $file_name = $entity->berkas;
        $file      = $request->file('file');

        if ($file && $file->isValid()) {
            $storage = new \Illuminate\Support\Facades\Storage();
            $storage::disk('sftp')->put(env('DOCUMENT_SURAT_MASUK_SAVE_LOCATION') . '/' . $file_name, file_get_contents($file));
        }
    }

    /**
     * The hook is executed after updating the resource.
     *
     * @param Request $request
     * @param Model $entity
     * @return mixed
     */
    protected function afterUpdate(Request $request, Model $entity)
    {
        $file_name = $entity->berkas;
        $file      = $request->file('file');

        if ($file && $file->isValid()) {
            $storage = new \Illuminate\Support\Facades\Storage();
            $storage::disk('sftp')->put(env('DOCUMENT_SURAT_MASUK_SAVE_LOCATION') . '/' . $file_name, file_get_contents($file));
        }
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
     * The attributes that are used for filtering.
     *
     * @return array
     */
    public function filterableBy(): array
    {
        return ['no_simrs', 'tgl_surat', 'ket', 'status'];
    }

    /**
     * The attributes that are used for sorting.
     *
     * @return array
     */
    public function sortableBy(): array
    {
        return ['no', 'no_simrs', 'no_surat', 'pengirim', 'tgl_surat', 'perihal', 'pelaksanaan'];
    }

    /**
     * The relations that are always included together with a resource.
     *
     * @return array
     */
    public function alwaysIncludes(): array
    {
        return [];
    }

    /**
     * The relations that are allowed to be included together with a resource.
     *
     * @return array
     */
    public function includes(): array
    {
        return [];
    }

    /**
     * The attributes that are used for searching.
     *
     * @return array
     */
    public function searchableBy(): array
    {
        return ['perihal', 'pengirim', 'no_surat', 'tempat'];
    }
}
