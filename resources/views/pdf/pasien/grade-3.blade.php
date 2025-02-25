<x-print-layout>
    @push('styles')
        <style>
            @page {
                margin-top: 0.4cm;
                margin-bottom: 0.4cm;
                margin-left: 0.9cm;
                margin-right: 0.9cm;
            }
        </style>
    @endpush

    @foreach ($pasiens as $pasien)
        {{-- didalam pasien ada 2 relasi penting, yaitu diagnosa dan prosedur ambil yang terbanyak diantara keduanya --}}
        @php
            $max = max(($pasien->diagnosa->count() - 2), ($pasien->prosedur->count() - 2));
        @endphp

        <header>
            <table class="table w-full border-b" style="border-bottom: 1px solid #333;">
                <tr>
                    <td style="width: 60px" class="p-2 py-4 text-center align-middle">
                        <img src="{{ public_path('assets/images/logo.png') }}" width="60" />
                    </td>
                    <td class="p-2 py-4 text-center">
                        <h2 class="text-center text-lg font-bold leading-none text-gray-800">Rumah Sakit Ibu Dan Anak Aisyiyah Pekajangan</h2>
                        <p class="mt-1 text-sm leading-none">Jalan Raya Pekajangan No. 610, Pekalongan, 51172<br>Telp. (0285) 785909 Email : rba610@gmail.com Website : www.rsiaaisyiyah.com</p>
                    </td>
                </tr>
            </table>
        </header>

        <main>
            <div class="text-center">
                <h4 class="text-lg font-bold">SURAT PENGESAHAN KOMITE MEDIK</h4>
            </div>
            
            <div class="mt-3">
                <p class="mb-2">Dibawah ini merupakan nama pasien rawat inap pada {{ \Carbon\Carbon::parse($tgl_awal)->translatedFormat('F Y') }} dengan komplikasi atau severity level 3 yang memerlukan pengesahan Komite Medik : </p>
                <table class="table w-full border" style="border-color: #333;">
                    <thead>
                        <tr class="bg-gray-200">
                            <th rowspan="2" class="border" style="font-size: 10pt; border-color: #333;">NO</th>
                            <th rowspan="2" class="border" style="font-size: 10pt; border-color: #333;">NAMA PASIEN</th>
                            <th rowspan="2" class="border" style="font-size: 10pt; border-color: #333;">NO RM</th>
                            <th rowspan="2" class="border" style="font-size: 10pt; border-color: #333;">NO SEP</th>
                            <th rowspan="2" class="border" style="font-size: 10pt; border-color: #333;">TGL RAWAT</th>
                            <th colspan="2" class="border" style="font-size: 10pt; border-color: #333;">DIAGNOSA UTAMA</th>
                            <th colspan="2" class="border" style="font-size: 10pt; border-color: #333;">DIAGNOSA SEKUNDER</th>
                            <th rowspan="2" class="border" style="font-size: 10pt; border-color: #333;">PROSEDUR</th>
                            <th rowspan="2" class="border" style="font-size: 10pt; border-color: #333;">KODE<br>INA CBG</th>
                            <th rowspan="2" class="border" style="font-size: 10pt; border-color: #333;">TARIF</th>
                        </tr>
                        <tr>
                            <th class="border" style="font-size: 10pt; border-color: #333;">ICD X</th>
                            <th class="border" style="font-size: 10pt; border-color: #333;">DESKRIPSI</th>
                            <th class="border" style="font-size: 10pt; border-color: #333;">ICD X</th>
                            <th class="border" style="font-size: 10pt; border-color: #333;">DESKRIPSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td rowspan="{{ ($max + 1) }}" class="border text-center p-1" style="font-size: 10pt; border-color: #333;">1</td>
                            <td rowspan="{{ ($max + 1) }}" class="border text-center p-1" style="font-size: 10pt; border-color: #333;">{{ $pasien->pasien->nm_pasien }}</td>
                            <td rowspan="{{ ($max + 1) }}" class="border text-center p-1" style="font-size: 10pt; border-color: #333;">{{ $pasien->nomr }}</td>
                            <td rowspan="{{ ($max + 1) }}" class="border text-center p-1" style="font-size: 10pt; border-color: #333;">{{ $pasien->no_sep }}</td>
                            <td rowspan="{{ ($max + 1) }}" class="border text-center p-1" style="font-size: 10pt; border-color: #333;">
                                <div class="leading-none">
                                    {{ \Carbon\Carbon::parse($pasien->reg_periksa->tgl_registrasi)->translatedFormat('d M Y') }} <br>
                                    s/d <br>
                                    {{ \Carbon\Carbon::parse($pasien->tanggal_pulang->tgl_keluar)->translatedFormat('d M Y') }}
                                </div>
                            </td>
                            <td rowspan="1" class="border text-center p-1" style="font-size: 10pt; border-color: #333;">{{ $pasien->diagnosa->first()->kd_penyakit ?? "-" }}</td>
                            <td rowspan="1" class="border p-1 whitespace-nowrap" style="font-size: 10pt; border-color: #333;">{{ $pasien->diagnosa->first()->penyakit->nm_penyakit ?? "-" }}</td>
                            <td rowspan="1" class="border text-center p-1" style="font-size: 10pt; border-color: #333;">{{ $pasien->diagnosa->get(1)->kd_penyakit ?? "-" }}</td>
                            <td rowspan="1" class="border p-1 whitespace-nowrap" style="font-size: 10pt; border-color: #333;">{{ $pasien->diagnosa->get(1)->penyakit->nm_penyakit ?? "-" }}</td>
                            <td rowspan="1" class="border text-center p-1" style="font-size: 10pt; border-color: #333;">{{ $pasien->prosedur->first()->kode ?? "-" }}</td>
                            <td rowspan="{{ ($max + 1) }}" class="border text-center p-1 whitespace-nowrap" style="font-size: 10pt; border-color: #333;">{{ $pasien->groupStage->code_cbg }}</td>
                            <td rowspan="{{ ($max + 1) }}" class="border text-center p-1" style="font-size: 10pt; border-color: #333;">{{number_format($pasien->groupStage->tarif, 0, ',', '.') }}</td>
                        </tr>

                        @if ($max > 0)
                            @for ($i = 0; $i < $max; $i++)
                            <tr>
                                <td class="border text-center p-1" style="font-size: 10pt; border-color: #333;">-</td>
                                <td class="border p-1" style="font-size: 10pt; border-color: #333;">-</td>
                                <td class="border text-center p-1" style="font-size: 10pt; border-color: #333;">{{ $pasien->diagnosa->get($i + 2)->kd_penyakit ?? "-" }}</td>
                                <td class="border p-1" style="font-size: 10pt; border-color: #333;">{{ $pasien->diagnosa->get($i + 2)->penyakit->nm_penyakit ?? "-" }}</td>
                                <td class="border text-center p-1" style="font-size: 10pt; border-color: #333;">{{ $pasien->prosedur->get($i + 1)->kode ?? "-" }}</td>
                            </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>

                <div class="leading-none">
                    <p class="mt-4 mb-1">Data-data diatas benar adanya dan telah diketahui oleh dokter yang memeriksa beserta berkas penunjang dan bersama ini pula dilakukan pengesahan oleh komite medik.</p>
                    <p>Demikian surat ini dibuat agar bisa dipergunakan sebagaimana mestinya.</p>
                </div>
            </div>

            <div class="my-10">
                <table class="table w-full">
                    <tbody>
                        <tr class="text-center">
                            <td>
                                <div class="leading-none">
                                    Yang Mengesahkan, <br>
                                    Ketua Komite Medik <br>
                                    RSIA AISYIYAH PEKAJANGAN
                                </div>

                                <div class="mt-24">
                                    <div class="leading-none">
                                        <p class="font-bold"><u>dr. Rendy Yoga Adrian, Sp.A</u></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="leading-none">
                                    Mengetahui, <br>
                                    Direktur <br>
                                    RSIA AISYIYAH PEKAJANGAN
                                </div>

                                <div class="mt-24">
                                    <div class="leading-none">
                                        <p class="font-bold"><u>dr. Widjdan Kadir</u></p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>

        @if (!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach
</x-print-layout>