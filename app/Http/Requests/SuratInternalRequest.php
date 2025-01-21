<?php

namespace App\Http\Requests;

class SuratInternalRequest extends \Orion\Http\Requests\Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function storeRules(): array
    {
        $rules = [
            "perihal"    => "required|string",
            "pj"         => "required|string|exists:pegawai,nik",
            "tgl_terbit" => "required|date",
            "undangan"   => "nullable|array",
            "status"     => "required|string|in:pengajuan,disetujui,ditolak,batal",
        ];

        if ($this->has('undangan') && !is_null($this->input('undangan'))) {
            $rules = array_merge($rules, [
                "undangan.tanggal"   => "required|date_format:Y-m-d H:i:s",
                "undangan.lokasi"    => "required|string",
                "undangan.deskripsi" => "nullable|string",
                "undangan.catatan"   => "nullable|string",
                // "undangan.status"    => "required|string|in:pengajuan,disetujui,ditolak,batal",
            ]);
        }

        return $rules;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function updateRules(): array
    {
        $rules = [
            "perihal"    => "required|string",
            "pj"         => "required|string|exists:pegawai,nik",
            "tgl_terbit" => "required|date",
            "undangan"   => "nullable|array",
            "status"     => "required|string|in:pengajuan,disetujui,ditolak,batal",
        ];

        if ($this->has('undangan') && !is_null($this->input('undangan'))) {
            $rules = array_merge($rules, [
                "undangan.tanggal"   => "required|date_format:Y-m-d H:i:s",
                "undangan.lokasi"    => "required|string",
                "undangan.deskripsi" => "nullable|string",
                "undangan.catatan"   => "nullable|string",
                // "undangan.status"    => "required|string|in:pengajuan,disetujui,ditolak,batal",
            ]);
        }

        return $rules;
    }
}
