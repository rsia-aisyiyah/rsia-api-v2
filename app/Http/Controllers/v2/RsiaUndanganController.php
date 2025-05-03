<?php

namespace App\Http\Controllers\v2;

use App\Models\RsiaNotulen;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RsiaPenerimaUndangan;

class RsiaUndanganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string $base64_no_surat
     * @return \Illuminate\Http\Response
     */
    public function show($base64_no_surat)
    {
        try {
            $no_surat = base64_decode($base64_no_surat);
            $undangan = RsiaPenerimaUndangan::where('no_surat', $no_surat)->first();

            if (!$undangan) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            $model = new $undangan->model;
            $undangan = $model->with('penerima.kehadiran')->find($no_surat);
            
            return new \App\Http\Resources\RealDataResource($undangan);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Download undangan internal
     *
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        $undangan = \App\Models\RsiaUndangan::with('surat', 'penanggungJawab.jenjang_jabatan')->find($id);
        
        if (!$undangan) {
            return ApiResponse::error('Resource not found', 'resource_not_found', null, 404);
        }

        $penerima = \App\Models\RsiaPenerimaUndangan::where('undangan_id', $id)->with(['pegawai.dep'])->get();
        $penerima = $penerima->sortBy('pegawai.nama', SORT_NATURAL | SORT_FLAG_CASE)->values();

        $pdf = \Mccarlosen\LaravelMpdf\Facades\LaravelMpdf::loadView('pdf.undangan.undangan', [
            'penerima' => $penerima,
            'undangan' => $undangan,
        ], [], [
            'default_font' => 'new_times',
        ]);

        return $pdf->stream('undangan_' . \Str::slug($undangan->perihal) . '.pdf');

        // $noSurat = null;
        // try {
        //     $noSurat = base64_decode($base64_no_surat);
        // } catch (\Throwable $th) {
        //     return ApiResponse::error('Data tidak ditemukan', 404);
        // }

        // $penerima = \App\Models\RsiaPenerimaUndangan::where('no_surat', $noSurat)->with(['pegawai.dep'])->get();

        // if ($penerima->count() == 0) {
        //     return ApiResponse::error('Undangan belum memiliki penerima, setidaknya ada 1 penerima undangan', 404);
        // }
        
        // // penerima order by pegawai nama ascending
        // $penerima = $penerima->sortBy('pegawai.nama', SORT_NATURAL | SORT_FLAG_CASE);

        // // reset key $penerima
        // $penerima = $penerima->values();
        
        // $model = $penerima->first()->model;
        // $model = new $model;
        
        // $detailUndangan = $model->with(['penanggungJawab' => function($qq) {
        //     return $qq->with('jenjang_jabatan')->select('nik', 'nama', 'bidang', 'jbtn', 'jnj_jabatan');
        // }])->where('no_surat', $noSurat)->first();

        // $pdf = \Mccarlosen\LaravelMpdf\Facades\LaravelMpdf::loadView('pdf.undangan.undangan', [
        //     'nomor'    => $noSurat,
        //     'penerima' => $penerima,
        //     'undangan' => $detailUndangan,
        // ], [], [
        //     'default_font' => 'new_times',
        // ]);

        // return $pdf->stream('undangan_internal.pdf');

        // =============================================

        // $html = view('pdf.undangan.undangan', [
        //     'nomor'    => $noSurat,
        //     'penerima' => $penerima,
        //     'undangan' => $detailUndangan,
        // ]);

        // // PDF
        // $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setWarnings(false)->setOptions([
        //     'isPhpEnabled'            => true,
        //     'isRemoteEnabled'         => true,
        //     'isHtml5ParserEnabled'    => true,
        //     'dpi'                     => 300,
        //     'defaultFont'             => 'sans-serif',
        //     'isFontSubsettingEnabled' => true,
        //     'isJavascriptEnabled'     => true,
        // ]);

        // $pdf->setOption('margin-top', 0);
        // $pdf->setOption('margin-right', 0);
        // $pdf->setOption('margin-bottom', 0);
        // $pdf->setOption('margin-left', 0);

        // return $pdf->stream('undangan_internal.pdf');
    }

    /**
     * Get proof of undangan
     *
     * @param  string $base64_no_surat
     * @return \Illuminate\Http\Response
     */
    public function qrContent($id)
    {
        $undangan = \App\Models\RsiaUndangan::find($id);

        if (!$undangan) {
            return ApiResponse::error('Resouce not found', 'resource_not_found',  null, 404);
        }

        $encryptedId = \Illuminate\Support\Facades\Crypt::encrypt($id);

        // undangan to collection
        $undangan = collect($undangan->toArray());
        $undangan->put('qr', [
            'content' => $encryptedId,
        ]);

        // $qr = \Endroid\QrCode\Builder\Builder::create()
        //     ->writer(new \Endroid\QrCode\Writer\PngWriter())
        //     ->writerOptions([])
        //     ->data(\Illuminate\Support\Facades\Hash::make($id))
        //     ->encoding(new \Endroid\QrCode\Encoding\Encoding('ISO-8859-1'))
        //     ->errorCorrectionLevel(new \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh())
        //     ->build();

        // return response($qr->getString(), 200)
        //     ->header('Content-Type', 'image/png')
        //     ->header('Content-Disposition', 'inline; filename="qrcode.png"');

        return ApiResponse::success('success', $undangan);
    }

    /**
     * Get proof of undangan
     *
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function proof($id)
    {
        $undangan = \App\Models\RsiaUndangan::with('surat', 'penanggungJawab')->find($id);
        $penerima = \App\Models\RsiaPenerimaUndangan::with('kehadiran', 'pegawai')->where('undangan_id', $id)->with(['detail'])->get();

        if (!$penerima || !$undangan) {
            return ApiResponse::error('Resource not found','found', null,404);
        }

        $pdf = \Mccarlosen\LaravelMpdf\Facades\LaravelMpdf::loadView('pdf.undangan.kehadiran', [
            'undangan' => $undangan,
            'penerima' => $penerima,
        ]);

        return $pdf->stream('proof-kehadiran-' . \Hash::make($id) .'.pdf');
    }
    
    /**
     * Get notulen from surat
     *
     * @param  string $base64_no_surat
     * @return \Illuminate\Http\Response
     */
    public function notulen($base64_no_surat)
    {
        try {
            $no_surat = base64_decode($base64_no_surat);
            $undangan = RsiaPenerimaUndangan::where('no_surat', $no_surat)->first();

            if (!$undangan) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            $model = new $undangan->model;
            $undangan = $model->with(['penanggungJawab' => function($qq) {
                return $qq->select('nik', 'nama');
            }])->find($no_surat);

            if (!$undangan) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            $notulen = RsiaNotulen::where('no_surat', $no_surat)->with('notulis')->first();

            $undangan->notulen = $notulen;
            
            return new \App\Http\Resources\RealDataResource($undangan);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
