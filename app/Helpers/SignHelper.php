<?php

namespace App\Helpers;

class SignHelper
{
    public static function rsia($name, $id_or_nik)
    {
        $hash = \App\Models\SidikJari::where('id', $id_or_nik)->select('id', \Illuminate\Support\Facades\DB::raw('SHA1(sidikjari) as sidikjari'))->first();

        if ($hash) {
            $hash = $hash->sidikjari;
        } else {
            $hash = \Illuminate\Support\Facades\Hash::make($id_or_nik);
        }

        $text     = 'Dikeluarkan di RSIA Aisyiyah Pekajangan, Ditandatangani secara elektronik oleh ' . $name . '. ID : ' . $hash;
        $logoPath = public_path('assets/images/logo.png');

        $qrCode = \Endroid\QrCode\Builder\Builder::create()
            ->writer(new \Endroid\QrCode\Writer\PngWriter())
            ->writerOptions([])
            ->data($text)
            ->logoPath($logoPath)
            ->logoResizeToWidth(100)
            ->encoding(new \Endroid\QrCode\Encoding\Encoding('ISO-8859-1'))
            ->errorCorrectionLevel(new \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh())
            ->build();

        return $qrCode;
    }

    public static function blankRsia()
    {
        $text     = 'Dikeluarkan di RSIA Aisyiyah Pekajangan, Ditandatangani secara elektronik pada ' . date('Y-m-d H:i:s') . ' ID : ' . \Illuminate\Support\Facades\Hash::make(date('Y-m-d H:i:s'));
        $logoPath = public_path('assets/images/logo.png');

        $qrCode = \Endroid\QrCode\Builder\Builder::create()
            ->writer(new \Endroid\QrCode\Writer\PngWriter())
            ->writerOptions([])
            ->data($text)
            ->logoPath($logoPath)
            ->logoResizeToWidth(100)
            ->encoding(new \Endroid\QrCode\Encoding\Encoding('ISO-8859-1'))
            ->errorCorrectionLevel(new \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh())
            ->build();

        return $qrCode;
    } 

    public static function toQr($data)
    {
        $qrCode = \Endroid\QrCode\Builder\Builder::create()
            ->writer(new \Endroid\QrCode\Writer\PngWriter())
            ->writerOptions([])
            ->data($data)
            ->encoding(new \Endroid\QrCode\Encoding\Encoding('ISO-8859-1'))
            ->errorCorrectionLevel(new \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh())
            ->build();

        return $qrCode;
    }
}
