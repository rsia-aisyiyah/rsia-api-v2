<x-berkas-klaim._header-with-identity :regPeriksa="$regPeriksa" title="TRIASE GAWAT DARURAT"/>
<main>
    <table class="table w-full">
        <thead>
            <tr>
                <td class="border px-2 py-1 text-left leading-none" style="border-color: #333;">
                    <span class="font-bold">Tanggal</span>
                </td>
                <td class="border px-2 py-1 text-left leading-none" colspan="5" style="border-color: #333;">
                    <span class="font-bold">: {{ $triase->asmedUgd->tanggal }}</span>
                </td>
            </tr>
            <tr>
                <td class="border py-1 text-center leading-none" style="background-color: #FFFFFF; border-color: #333;">
                    Prioritas Waktu Tunggu
                </td>
                <td class="{!! !$triase->skala1->isEmpty() ? 'font-bold' : '' !!} border py-1 text-center leading-none" style="background-color: #F87171; border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! $triase->skala1->isEmpty() ? '&#9744;' : '&#9745;' !!}</span> ATS I <br>
                    Segera
                </td>
                <td class="{!! !$triase->skala2->isEmpty() ? 'font-bold' : '' !!} border py-1 text-center leading-none" style="background-color: #FBBF24; border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! $triase->skala2->isEmpty() ? '&#9744;' : '&#9745;' !!}</span> ATS II <br>
                    10 Menit
                </td>
                <td class="{!! !$triase->skala3->isEmpty() ? 'font-bold' : '' !!} border py-1 text-center leading-none" style="background-color: #34D399; border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! $triase->skala3->isEmpty() ? '&#9744;' : '&#9745;' !!}</span> ATS III <br>
                    30 Menit
                </td>
                <td class="{!! !$triase->skala4->isEmpty() ? 'font-bold' : '' !!} border py-1 text-center leading-none" style="background-color: #60A5FA; border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! $triase->skala4->isEmpty() ? '&#9744;' : '&#9745;' !!}</span> ATS IV <br>
                    60 Menit
                </td>
                <td class="{!! !$triase->skala5->isEmpty() ? 'font-bold' : '' !!} border py-1 text-center leading-none" style="background-color: #FFFFFF; border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! $triase->skala5->isEmpty() ? '&#9744;' : '&#9745;' !!}</span> ATS V <br>
                    120 Menit
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-1 py-1 text-center leading-none" style="border-color: #333;">Jalan Nafas</td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala1, 'jalan nafas', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala1, 'jalan nafas') !!}</span> Obstruksi
                </td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala2, 'jalan nafas', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala2, 'jalan nafas') !!}</span> Paten
                </td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala3, 'jalan nafas', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala3, 'jalan nafas') !!}</span> Paten
                </td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala4, 'jalan nafas', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala4, 'jalan nafas') !!}</span> Paten
                </td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala5, 'jalan nafas', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala5, 'jalan nafas') !!}</span> Paten
                </td>
            </tr>

            <tr>
                <td class="border px-1 py-1 text-center leading-none" style="border-color: #333;">Pernapasan</td>
                <td class="border px-1 py-1 leading-none" style="border-color: #333;">
                    <div class="{!! \App\Helpers\TriaseHelper::multiCheckPemeriksaaan($triase->skala1, 'pernapasan', 'Distres Napas Berat', true) ? 'font-bold' : '' !!} leading-none">
                        <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::multiCheckPemeriksaaan($triase->skala1, 'pernapasan', 'Distres Napas Berat') !!}</span>
                        Distres Napas Berat
                    </div>
                    <div class="{!! \App\Helpers\TriaseHelper::multiCheckPemeriksaaan($triase->skala1, 'pernapasan', 'Henti Napas', true) ? 'font-bold' : '' !!} leading-none">
                        <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::multiCheckPemeriksaaan($triase->skala1, 'pernapasan', 'Henti Napas') !!}</span>
                        Henti Napas
                    </div>
                    <div class="{!! \App\Helpers\TriaseHelper::multiCheckPemeriksaaan($triase->skala1, 'pernapasan', 'Hipoventilasi', true) ? 'font-bold' : '' !!} leading-none">
                        <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::multiCheckPemeriksaaan($triase->skala1, 'pernapasan', 'Hipoventilasi') !!}</span>
                        Hipoventilasi
                    </div>
                </td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala2, 'pernapasan', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala2, 'pernapasan') !!}</span> Distres Napas Sedang
                </td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala3, 'pernapasan', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala3, 'pernapasan') !!}</span> Distres Napas Ringan
                </td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala4, 'pernapasan', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala4, 'pernapasan') !!}</span> Tidak Ada Distres Napas
                </td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala5, 'pernapasan', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala5, 'pernapasan') !!}</span> Tidak Ada Distres Napas
                </td>
            </tr>

            <tr>
                <td class="border px-1 py-1 text-center leading-none" style="border-color: #333;">Sirkulasi</td>
                <td class="border px-1 py-1 leading-none" style="border-color: #333;">
                    <div class="{!! \App\Helpers\TriaseHelper::multiCheckPemeriksaaan($triase->skala1, 'sirkulasi', 'Gangguan Hemodinamik Berat', true) ? 'font-bold' : '' !!} leading-none">
                        <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::multiCheckPemeriksaaan($triase->skala1, 'sirkulasi', 'Gangguan Hemodinamik Berat') !!}</span> Gangguan Hemodinamik Berat
                    </div>
                    <div class="{!! \App\Helpers\TriaseHelper::multiCheckPemeriksaaan($triase->skala1, 'sirkulasi', 'Henti Jantung', true) ? 'font-bold' : '' !!} leading-none">
                        <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::multiCheckPemeriksaaan($triase->skala1, 'sirkulasi', 'Henti Jantung') !!}</span> Henti Jantung
                    </div>
                    <div class="{!! \App\Helpers\TriaseHelper::multiCheckPemeriksaaan($triase->skala1, 'sirkulasi', 'Pendarahan Tidak Terkontrol', true) ? 'font-bold' : '' !!} leading-none">
                        <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::multiCheckPemeriksaaan($triase->skala1, 'sirkulasi', 'Pendarahan Tidak Terkontrol') !!}</span> Pendarahan Tidak Terkontrol
                    </div>
                </td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala2, 'sirkulasi', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala2, 'sirkulasi') !!}</span> Gangguan Hemodinamik Sedang
                </td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala3, 'sirkulasi', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala3, 'sirkulasi') !!}</span> Gangguan Hemodinamik Ringan
                </td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala4, 'sirkulasi', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala4, 'sirkulasi') !!}</span> Tidak Ada Gangguan Sirkulasi
                </td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala5, 'sirkulasi', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala5, 'sirkulasi') !!}</span> Tidak Ada Gangguan Sirkulasi
                </td>
            </tr>

            <tr>
                <td class="border px-1 py-1 text-center leading-none" style="border-color: #333;">GCS</td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala1, 'gcs', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala1, 'gcs') !!}</span> GCS < 9 
                </td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala2, 'gcs', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala2, 'gcs') !!}</span> GCS 9 - 12
                </td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala3, 'gcs', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala3, 'gcs') !!}</span> GCS > 12
                </td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala4, 'gcs', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala4, 'gcs') !!}</span> Normal GCS
                </td>
                <td class="{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala5, 'gcs', true) ? 'font-bold' : '' !!} border px-1 py-1 leading-none" style="border-color: #333;">
                    <span class="font-dejavu text-lg leading-none">{!! \App\Helpers\TriaseHelper::cekPemeriksaan($triase->skala5, 'gcs') !!}</span> Normal GCS
                </td>
            </tr>

            <tr class="align-middle">
                <td class="border px-1 py-5 text-center leading-none" colspan="3" style="border-color: #333;">
                    {{ $triase->tgl_kunjungan }}
                </td>
                <td class="border px-1 py-5 text-center leading-none" colspan="3" style="border-color: #333;">
                    <img src="{{ $barcodeDPJP }}" alt="barcode DPJP" style="width: 150px; height: 150px;"/>
                    <div class="mt-1">
                        {{ \App\Helpers\SafeAccess::object($regPeriksa, 'dokter->nm_dokter', '-') }}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</main>
