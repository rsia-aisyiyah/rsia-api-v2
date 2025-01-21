<?php

namespace App\Http\Controllers\v2;

use App\Models\RsiaUndangan;
use Illuminate\Http\Request;
use App\Models\RsiaSuratInternal;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Orion\Http\Requests\Request as OrionRequest;

class RsiaSuratInternalController extends Controller
{
    /**
     * Meampilkan data surat internal
     * 
     * Semua data surat internal yang ada di database akan ditampilkan, data diurutkan berdasarkan tanggal terbit surat. Bersama dengan data surat internal, data penanggung jawab surat internal juga akan ditampilkan.
     * 
     * @return \App\Http\Resources\Berkas\CompleteCollection
     */
    public function index(Request $request) {}

    /**
     * Menampilkan form untuk membuat surat internal baru
     * 
     * > **Catatan:** fungsi ini tidak digunakan dalam API.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Menyimpan surat internal baru
     * 
     * Menyimpan data surat internal baru yang dibuat oleh pengguna. Data yang disimpan berupa nomor surat, perihal, tempat, pj, tanggal terbit, catata, status, dan file surat.
     * > data key pada body request harus sesuai dengan field pada tabel pegawai.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     "perihal"            => "required|string",
        //     "pj"                 => "required|string|exists:pegawai,nik",
        //     "tgl_terbit"         => "required|date",
        //     "undangan"           => "nullable|array",
        // ]);

        // if ($request->undangan) {
        //     $request->validate([
        //         "undangan.tanggal"   => "required|date_format:Y-m-d H:i:s",
        //         "undangan.lokasi"    => "required|string",
        //         "undangan.deskripsi" => "string",
        //         "undangan.catatan"   => "string",
        //         "undangan.status"    => "required|string|in:pengajuan,disetujui,ditolak,batal",
        //     ]);
        // }

        // $last_nomor = RsiaSuratInternal::select('no_surat')
        //     ->orderBy('created_at', 'desc')
        //     ->whereYear('tgl_terbit', \Carbon\Carbon::parse($request->tgl_terbit)->year)
        //     ->first();

        // if ($last_nomor) {
        //     $last_nomor = explode('/', $last_nomor->no_surat);
        //     $last_nomor[0] = str_pad($last_nomor[0] + 1, 3, '0', STR_PAD_LEFT);
        //     $last_nomor[3] = \Carbon\Carbon::parse($request->tgl_terbit)->format('dmy');
        //     $last_nomor = implode('/', $last_nomor);
        // } else {
        //     $last_nomor = '001/A/S-RSIA/' . \Carbon\Carbon::parse($request->tgl_terbit)->format('dmy');
        // }

        // $request->merge([
        //     'no_surat' => $last_nomor,
        // ]);

        // $suratData = [
        //     'no_surat'   => $request->no_surat,
        //     'perihal'    => $request->perihal,
        //     'pj'         => $request->pj,
        //     'tgl_terbit' => $request->tgl_terbit,
        // ];

        // if ($request->undangan) {
        //     $undanganData = [
        //         'no_surat'   => $request->no_surat,
        //         'model'      => RsiaSuratInternal::class,
        //         'tanggal'    => $request->undangan['tanggal'],
        //         'perihal'    => $request->perihal,
        //         'lokasi'     => $request->undangan['lokasi'],
        //         'deskripsi'  => $request->undangan['deskripsi'],
        //         'catatan'    => $request->undangan['catatan'],
        //         'pj'         => $request->pj,
        //         'status'     => $request->undangan['status'],
        //     ];
        // }

        // try {
        //     DB::beginTransaction();

        //     RsiaSuratInternal::updateOrCreate(['no_surat' => $request->no_surat], $suratData);

        //     if ($request->undangan) {
        //         RsiaUndangan::updateOrCreate(['no_surat' => $request->no_surat], $undanganData);
        //     }

        //     DB::commit();
        // } catch (\Exception $e) {
        //     DB::rollBack();

        //     \App\Helpers\Logger\RSIALogger::berkas("STORE DATA FAILED", 'error', ['data' => $request->all(), 'error' => $e->getMessage()]);
        //     return \App\Helpers\ApiResponse::error('Failed to save data', 'store_failed', $e->getMessage(), 500);
        // } finally {
        //     \App\Helpers\Logger\RSIALogger::berkas("STORED", 'info', ['data' => $request->all()]);
        //     return \App\Helpers\ApiResponse::successWithData(['no_surat' => $request->no_surat], 'Data saved successfully');
        // }
    }

    /**
     * Menampilkan data surat internal berdasarkan nomor surat
     * 
     * Menampilkan spesifik data surat internal berdasarkan nomor surat yang diberikan oleh pengguna. Data yang ditampilkan berupa nomor surat, perihal, tempat, pj, tanggal terbit, tanggal, catatan, status, dan penanggung jawab surat internal.
     * 
     * > **Catatan:** nomor surat yang digunakan harus di encode base64 terlebih dahulu.
     * > **Contoh:** MDU4L0EvUy1SU0lBLzAzMDQyNA==
     * > dari nilai asli 058/A/S-RSIA/030424
     *
     * @param  string $no_surat Nomor surat internal (yang di encode base64) yang ingin ditampilkan. Example: MDU4L0EvUy1SU0lBLzAzMDQyNA==
     * @return \Illuminate\Http\Response
     */
    public function show($no_surat, Request $request)
    {
        $decoded_no_surat = base64_decode($no_surat);
        $select = $request->input('select', '*');

        $data = RsiaSuratInternal::select(array_map('trim', explode(',', $select)))
            ->with(['penanggungJawab' => function ($query) {
                $query->select('nik', 'nama');
            }])
            ->where('no_surat', $decoded_no_surat)
            ->first();

        if (!$data) {
            return \App\Helpers\ApiResponse::notFound('Resource not found');
        }

        $data->model = RsiaSuratInternal::class;

        return new \App\Http\Resources\Berkas\CompleteResource($data);
    }

    /**
     * Menampilkan form untuk mengedit surat internal
     *
     * > **Catatan:** fungsi ini tidak digunakan dalam API.
     * 
     * @param  \App\Models\RsiaSuratInternal  $rsiaSuratInternal
     * @return \Illuminate\Http\Response
     */
    public function edit(RsiaSuratInternal $rsiaSuratInternal)
    {
        //
    }

    /**
     * Mengupdate data surat internal
     * 
     * Mengupdate data surat internal berdasarkan nomor surat yang diberikan oleh pengguna. Data yang dapat diupdate berupa perihal, tempat, pj, tanggal terbit, catatan, dan status.
     * 
     * > **Catatan:** nomor surat yang digunakan harus di encode base64 terlebih dahulu.
     * > **Contoh:** MDU4L0EvUy1SU0lBLzAzMDQyNA==
     * > dari nilai asli 058/A/S-RSIA/030424
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $no_surat Nomor surat internal (yang di encode base64) yang ingin diupdate. Example: MDU4L0EvUy1SU0lBLzAzMDQyNA==
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $no_surat)
    {
        $decoded_no_surat = base64_decode($no_surat);

        $request->merge([
            'no_surat' => $decoded_no_surat,
        ]);

        $request->validate(self::validationRule(false));

        $data = RsiaSuratInternal::where('no_surat', $decoded_no_surat)->first();

        if (!$data) {
            return \App\Helpers\ApiResponse::notFound('Resource not found');
        }

        $oldData = $data->toArray();

        try {
            $data->update($request->except('user'));
        } catch (\Exception $e) {
            \App\Helpers\Logger\RSIALogger::berkas("UPDATE FAILED", 'error', ['data' => $request->all(), 'error' => $e->getMessage(), 'old_data' => $oldData]);
            return \App\Helpers\ApiResponse::error('Failed to update data', 'update_failed', $e->getMessage(), 500);
        }

        \App\Helpers\Logger\RSIALogger::berkas("UPDATED", 'info', ['old_data' => $oldData, 'data' => $request->all()]);
        return \App\Helpers\ApiResponse::success('Data updated successfully');
    }

    /**
     * Menghapus data surat internal
     *
     * @param  \App\Models\RsiaSuratInternal  $rsiaSuratInternal
     * @return \Illuminate\Http\Response
     */
    public function destroy($no_surat)
    {
        $decoded_no_surat = base64_decode($no_surat);

        $data = RsiaSuratInternal::where('no_surat', $decoded_no_surat)->first();
        if (!$data) {
            return \App\Helpers\ApiResponse::notFound('Resource not found');
        }

        try {
            $data->delete();
        } catch (\Exception $e) {
            \App\Helpers\Logger\RSIALogger::berkas("DELETE FAILED", 'error', ['data' => $data]);
            return \App\Helpers\ApiResponse::error('Failed to delete data', 'delete_failed', $e->getMessage(), 500);
        }

        \App\Helpers\Logger\RSIALogger::berkas("DELETED", 'info', ['data' => $data]);
        return \App\Helpers\ApiResponse::success('Data deleted successfully');
    }

    /**
     * Aturan validasi untuk data surat internal
     * 
     * Dengan menerapkan aturan validasi ini, data yang dikirimkan oleh pengguna akan dicek kebenarannya. Jika data tidak sesuai dengan aturan validasi, maka data tidak akan disimpan ke dalam database.
     * 
     * @param  boolean  $withRequired
     * @return array 
     */
    private static function validationRule($withRequired = true)
    {
        return [
            "no_surat"   => ($withRequired ? 'required|' : '') . "string|max:20|regex:/^\d{3}\/A\/S-RSIA\/\d{6}$/",
            "perihal"    => "required|string",
            "tempat"     => "required|string|max:100",
            "pj"         => "required|string|exists:pegawai,nik",
            "tgl_terbit" => "required|date",
            "tanggal"    => "required|date_format:Y-m-d H:i:s",
            "catatan"    => "string",
            "status"     => ($withRequired ? 'required|' : '') . "string|in:pengajuan,disetujui,ditolak,batal",
        ];
    }
}
