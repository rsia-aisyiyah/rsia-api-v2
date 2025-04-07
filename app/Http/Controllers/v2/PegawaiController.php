<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{
    /**
     * Menampilkan daftar pegawai.
     * 
     * @queryParam page int Halaman yang ditampilkan. Defaults to 1. Example: 1
     * @queryParam select string Kolom yang ingin ditampilkan. Defaults to '*'. Example: nik,nama
     *
     * @return \App\Http\Resources\Pegawai\CompleteCollection
     */
    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $select = $request->query('select', '*');

        $pegawai = \App\Models\Pegawai::select(explode(',', $select))
            ->orderBy('nama', 'asc')
            ->where('stts_aktif', 'AKTIF')
            ->paginate(10, explode(',', $select), 'page', $page);

        return new \App\Http\Resources\Pegawai\CompleteCollection($pegawai);
    }

    /**
     * Menampilkan form untuk membuat pegawai baru.
     *
     * > fungsi ini tidak digunakan dalam API.
     * 
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Menyimpan pegawai baru.
     * 
     * > catatan: data key pada body request harus sesuai dengan field pada tabel pegawai.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(self::validationRule());
        $file = $request->file('file');

        if ($file) {
            $file_name = time() . $file->getClientOriginalName() . '.' . $file->extension();
            $st = new Storage();

            if (!$st::disk('sftp')->exists(env('FOTO_PEGAWAI_SAVE_LOCATION'))) {
                $st::disk('sftp')->makeDirectory(env('FOTO_PEGAWAI_SAVE_LOCATION'));
            }

            $request->merge(['photo' => $file_name]);
        }

        try {
            \DB::transaction(function () use ($request) {
                \App\Models\Pegawai::create($request->all());
            });
        } catch (\Exception $e) {
            return \App\Helpers\ApiResponse::error('Failed to save data', 'store_failed', $e->getMessage(), 500);
        }

        if ($file) {
            $st::disk('sftp')->put(env('FOTO_PEGAWAI_SAVE_LOCATION') . $file_name, file_get_contents($file));
        }

        return \App\Helpers\ApiResponse::success('Data saved successfully');
    }

    /**
     * Menampilkan data pegawai berdasarkan NIK.
     * 
     * @queryParam select string Kolom yang ingin ditampilkan. Defaults to '*'. Example: nik,nama
     *
     * @param  string  $id NIK pegawai. Example: 3.928.0623
     * @return \App\Http\Resources\Pegawai\CompleteResource
     */
    public function show($id, Request $request) {}

    /**
     * Menampilkan form untuk mengedit pegawai.
     *
     * > fungsi ini tidak digunakan dalam API.
     * 
     * @param  string  $id Nik pegawai
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update pegawai berdasarkan NIK.
     * 
     * > catatan: data key pada body request harus sesuai dengan field pada tabel pegawai.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id Nik pegawai. Example: 3.928.0623
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $file = $request->file('file');
        $request->validate(self::validationRule(false));

        $pegawai = \App\Models\Pegawai::find($id);
        $oldPhoto = $pegawai->photo;
        if (!$pegawai) {
            return \App\Helpers\ApiResponse::notFound('Resource not found');
        }

        if ($file) {
            $file_name = time() . $file->getClientOriginalName() . '.' . $file->extension();
            $st = new Storage();

            if (!$st::disk('sftp')->exists(env('FOTO_PEGAWAI_SAVE_LOCATION'))) {
                $st::disk('sftp')->makeDirectory(env('FOTO_PEGAWAI_SAVE_LOCATION'));
            }

            $request->merge(['photo' => $file_name]);
        }

        try {
            \DB::transaction(function () use ($request, $pegawai) {
                $pegawai->update($request->all());
            });
        } catch (\Exception $e) {
            return \App\Helpers\ApiResponse::error('Failed to update data', 'update_failed', $e->getMessage(), 500);
        }

        if ($request->delete_old_photo) {
            if ($pegawai && $file && $oldPhoto != '' && $st::disk('sftp')->exists(env('FOTO_PEGAWAI_SAVE_LOCATION') . $oldPhoto)) {
                $st::disk('sftp')->delete(env('FOTO_PEGAWAI_SAVE_LOCATION') . $oldPhoto);
            }
        }

        if ($file) {
            $st::disk('sftp')->put(env('FOTO_PEGAWAI_SAVE_LOCATION') . $file_name, file_get_contents($file));
        }

        return \App\Helpers\ApiResponse::success('Data updated successfully');
    }

    /**
     * Menghapus pegawai berdasarkan NIK.
     * 
     * > catatan : data yang di hapus tidak dapat dikembalikan.
     * 
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pegawai = \App\Models\Pegawai::find($id);
        if (!$pegawai) {
            return \App\Helpers\ApiResponse::notFound('Resource not found');
        }

        try {
            $pegawai->delete();
        } catch (\Exception $e) {
            return \App\Helpers\ApiResponse::error('Failed to delete data', 'delete_failed', $e->getMessage(), 500);
        }

        return \App\Helpers\ApiResponse::success('Data deleted successfully');
    }

    public function get(Request $request)
    {
        if (!$request->user()) {
            return \App\Helpers\ApiResponse::unauthorized('Unauthorized');
        }

        $pegawai = \App\Models\Pegawai::with('dep')
            ->select('nik', 'nama', 'jk', 'jbtn', 'departemen', 'photo')
            ->where('nik', $request->user()->id_user)->first();

        if (!$pegawai) {
            return \App\Helpers\ApiResponse::notFound('Resource not found');
        }

        return \App\Helpers\ApiResponse::success('Data retrieved successfully', $pegawai);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'email'   => 'required|email',
            'alamat'  => 'required|string',
            'no_telp' => 'required|numeric|digits_between:11,13'
        ]);

        $user = \Illuminate\Support\Facades\Auth::guard('user-aes')->user();

        $pegawai = \App\Models\Pegawai::where('nik', $user->id_user)->first();
        $petugas = \App\Models\Petugas::where('nip', $user->id_user)->first();

        if (!$pegawai || !$petugas) {
            return \App\Helpers\ApiResponse::notFound('Resource not found');
        }

        try {
            \DB::transaction(function () use ($request, $pegawai, $petugas) {
                \App\Models\Petugas::where('nip', $petugas->nip)->update([
                    'alamat'  => $request->alamat,
                    'no_telp' => $request->no_telp,
                ]);

                \App\Models\RsiaEmailPegawai::updateOrCreate(['nik' => $pegawai->nik], [
                    'email' => $request->email,
                ]);

                \App\Models\Pegawai::whereNik($pegawai->nik)->update([
                    'alamat' => $request->alamat,
                ]);
            });
        } catch (\Exception $e) {
            return \App\Helpers\ApiResponse::error('Failed to update data', 'update_failed', $e->getMessage(), 500);
        }

        return \App\Helpers\ApiResponse::success('Data updated successfully');
    }

    private static function validationRule($withRequired = true)
    {
        return [
            "file"          => "nullable|mimes:jpeg,jpg,png|max:20480",

            "nik"            => ($withRequired ? 'required|' : '') . "string|regex:/^\d{1,3}\.\d{1,3}\.\d{1,4}$/",
            "nama"           => "required|string",
            "jk"             => "required|string|in:Wanita,Pria",
            "jbtn"           => "required|string",
            "jnj_jabatan"    => "required|string|exists:jnj_jabatan,kode",
            "kode_kelompok"  => ($withRequired ? "required|" : "") . "string|exists:kelompok,kode_kelompok",
            "kode_resiko"    => "required|string|exists:resiko_kerja,kode_resiko",
            "kode_emergency" => ($withRequired ? "required|" : "") . "string|exists:emergency_index,kode_emergency",
            "status_koor"    => ($withRequired ? "required|" : "") . "string|in:0,1",
            "departemen"     => "required|string|exists:departemen,dep_id",
            "bidang"         => "required|string|exists:bidang,nama",
            "stts_wp"        => ($withRequired ? "required|" : "") . "string|exists:stts_wp,stts",
            "stts_kerja"     => ($withRequired ? "required|" : "") . "string|exists:stts_kerja,stts",
            "npwp"           => ($withRequired ? "required|" : "") . "string",
            "pendidikan"     => ($withRequired ? "required|" : "") . "string|exists:pendidikan,tingkat",
            "gapok"          => ($withRequired ? "required|" : "") . "numeric",
            "tmp_lahir"      => ($withRequired ? "required|" : "") . "string",
            "tgl_lahir"      => ($withRequired ? "required|" : "") . "date",
            "alamat"         => ($withRequired ? "required|" : "") . "string",
            "kota"           => ($withRequired ? "required|" : "") . "string",
            "mulai_kerja"    => "required|date",
            "ms_kerja"       => ($withRequired ? "required|" : "") . "string|in:<1,PT,FT>1",
            "indexins"       => ($withRequired ? "required|" : "") . "string|exists:departemen,dep_id",
            "bpd"            => ($withRequired ? "required|" : "") . "string|exists:bank,namabank",
            "rekening"       => ($withRequired ? "required|" : "") . "string",
            "stts_aktif"     => "required|string|in:AKTIF,CUTI,KELUAR,TENAGA LUAR",
            "wajibmasuk"     => ($withRequired ? "required|" : "") . "integer",
            "pengurang"      => ($withRequired ? "required|" : "") . "numeric",
            "indek"          => ($withRequired ? "required|" : "") . "integer",
            "mulai_kontrak"  => ($withRequired ? "required|" : "") . "date",
            "cuti_diambil"   => ($withRequired ? "required|" : "") . "integer",
            "dankes"         => ($withRequired ? "required|" : "") . "numeric",
            "photo"          => ($withRequired ? "required|" : "") . "string",
            "no_ktp"         => ($withRequired ? "required|" : "") . "string",
        ];
    }
}
