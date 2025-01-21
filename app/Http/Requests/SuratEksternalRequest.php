<?php

namespace App\Http\Requests;

class SuratEksternalRequest extends \Orion\Http\Requests\Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function storeRules(): array
    {
        $rules = [
            // "no_surat"    => "required|string|unique:rsia_surat_eksternal,no_surat",
            "perihal"     => "required|string",
            "alamat"      => "required|string",
            "tgl_terbit"  => "required|date_format:Y-m-d",
            "pj"          => "required|string|exists:pegawai,nik",
            // "tanggal"     => "required|date_format:Y-m-d H:i:s",
        ];

        return $rules;
    }
}
