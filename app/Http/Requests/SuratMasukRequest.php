<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuratMasukRequest extends \Orion\Http\Requests\Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function storeRules(): array
    {
        $rules = [
            'no_simrs'        => "required|string",
            'no_surat'        => "nullable|string|max:50",
            'pengirim'        => "required|string|max:255",
            'tgl_surat'       => "required|date_format:Y-m-d",
            'perihal'         => 'required|string|max:255',
            'tempat'          => 'nullable|string|max:255',
            'ket'             => 'required|string|in:-,wa,email,fisik',
            'status'          => 'nullable|boolean',
        ];

        return $rules;
    }

    public function updateRules(): array
    {
        return $this->storeRules();
    }
}
