<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class NaikKelasHelper
{
    public static function translate($kelasNaik) 
    {

        if (!is_numeric($kelasNaik) || $kelasNaik < 1 || $kelasNaik > 8) {
            return "Kelas naik harus berupa angka antara 1-8";
        }

        $data = [
            '1' => 'VVIP',
            '2' => 'VIP',
            '3' => 'Kelas I',
            '4' => 'Kelas II',
            '5' => 'Kelas III',
            '6' => 'ICCU',
            '7' => 'ICU',
            '8' => 'Diatas Kelas 1',
        ];

        return $data[$kelasNaik];
    }

    public static function getPembiayaan($pembiayaan)
    {
        $data = [
            '1' => 'Pribadi Pasien / Keluarga Pasien',
            '2' => 'Pemberi Kerja',
            '3' => 'Asuransi Lain',
        ];

        return $data[$pembiayaan];
    }

    public static function getJumlahNaik($klsRawat, $klsNaik)
    {
        $klsRawat = (int) $klsRawat;  // sesuai dengan kelas rawat pasien
        $klsNaik  = (int) $klsNaik;   // Reference to $this->translate() function

        $translated = self::translate($klsNaik);

        if ($klsRawat == 1 && Str::contains($translated, ['VIP', 'Diatas'])) {
            return 1;
        }

        if ($klsRawat == 2 && Str::contains($translated, ['VIP', 'Diatas'])) {
            return 2;
        }

        if ($klsRawat == 2 && Str::contains($translated, ['Kelas I'])) {
            return 1;
        }

        return 0;
    }
}
