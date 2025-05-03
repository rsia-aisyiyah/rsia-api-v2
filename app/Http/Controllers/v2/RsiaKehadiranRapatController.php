<?php

namespace App\Http\Controllers\v2;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\RsiaKehadiranRapat;
use Illuminate\Http\Request;

class RsiaKehadiranRapatController extends Controller
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
        $request->validate([
            'undangan_id' => 'required|string|exists:rsia_undangan,id',
            'nik'         => 'array',
            'nik.*'       => 'string|exists:pegawai,nik',
        ]);

        // Ambil NIK yang valid untuk undangan ini
        $validParticipants = \App\Models\RsiaPenerimaUndangan::where('undangan_id', $request->undangan_id)
            ->pluck('penerima')
            ->toArray();

        // Cek jika ada NIK yang tidak termasuk dalam penerima undangan
        $invalidNik = array_diff($request->nik, $validParticipants);
        if (!empty($invalidNik)) {
            return ApiResponse::error(
                'Beberapa karyawan tidak terdaftar dalam undangan ini',
                'not_permitted',
                ['invalid_nik' => $invalidNik],
                403
            );
        }

        // Cek yang sudah hadir agar tidak double insert
        $alreadyHadir = RsiaKehadiranRapat::where('undangan_id', $request->undangan_id)
            ->whereIn('nik', $request->nik)
            ->pluck('nik')
            ->toArray();

        // Sisakan hanya yang belum hadir
        $newAttendance = array_diff($request->nik, $alreadyHadir);

        // Insert baru
        $data = [];
        foreach ($newAttendance as $nik) {
            $data[] = [
                'undangan_id' => $request->undangan_id,
                'nik'         => $nik,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }

        if (!empty($data)) {
            RsiaKehadiranRapat::insert($data);
        }

        return ApiResponse::success([
            'inserted' => array_values($newAttendance),
            'skipped'  => $alreadyHadir,
        ], 'Kehadiran berhasil dicatat');
    }

    /**
     * Display the specified resource.
     *
     * @param  $undangan_id
     * @return \Illuminate\Http\Response
     */
    public function show($undangan_id)
    {
        $rsiaKehadiranRapat = RsiaKehadiranRapat::where('undangan_id', $undangan_id)->get();

        return new \App\Http\Resources\RealDataCollection($rsiaKehadiranRapat);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RsiaKehadiranRapat  $rsiaKehadiranRapat
     * @return \Illuminate\Http\Response
     */
    public function edit(RsiaKehadiranRapat $rsiaKehadiranRapat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RsiaKehadiranRapat  $rsiaKehadiranRapat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RsiaKehadiranRapat $rsiaKehadiranRapat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RsiaKehadiranRapat  $rsiaKehadiranRapat
     * @return \Illuminate\Http\Response
     */
    public function destroy(RsiaKehadiranRapat $rsiaKehadiranRapat)
    {
        //
    }
}
