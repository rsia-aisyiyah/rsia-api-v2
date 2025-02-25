<header>
    <table class="table w-full border-b" style="border-bottom: 1px solid #333;">
        <tr>
            <td style="width: 60px" class="p-2 py-4 text-center align-middle">
                <img src="{{ public_path('assets/images/logo.png') }}" width="60" />
            </td>
            <td class="p-2 py-4 text-center">
                <h2 class="text-center text-lg font-bold leading-none text-gray-800">Rumah Sakit Ibu Dan Anak Aisyiyah Pekajangan</h2>
                <p class="mt-1 text-sm leading-none">Jalan Raya Pekajangan No. 610, Pekalongan, 51172<br>Telp. (0285) 785909 Email : rba610@gmail.com<br>Website : www.rsiaaisyiyah.com</p>
            </td>
        </tr>
    </table>
</header>

<main>
    <div class="my-3 text-center">
        <h4 class="text-lg font-bold">BILLING</h4>
    </div>

    <div class="my-3">
        <table class="table w-full">
            @foreach ([
                'No. Nota'    => \App\Helpers\SafeAccess::object($nota, 'no_nota', '-'),
                'No. RM'      => \App\Helpers\SafeAccess::object($regPeriksa, 'no_rkm_medis', '-'),
                'Nama Pasien' => \App\Helpers\SafeAccess::object($pasien, 'nm_pasien', '-'),
                'JK / Umur'   => (\Str::lower(\App\Helpers\SafeAccess::object($pasien, 'jk', '')) == 'p' ? 'Perempuan' : 'Laki-laki') . ' / ' . \App\Helpers\SafeAccess::object($regPeriksa, 'umurdaftar', '-') . ' ' . \App\Helpers\SafeAccess::object($regPeriksa, 'sttsumur', '-'),
                'Cara Bayar'  => \App\Helpers\SafeAccess::object($regPeriksa, 'caraBayar->png_jawab', '-'),
            ] as $key => $val)
                <tr class="align-top">
                    <th class="text-left text-nowrap whitespace-nowrap text-sm leading-4" style="width: 128px;">{{ $key }}</th>
                    <td class="px-2 text-sm leading-4">:</td>
                    <td class="w-full text-sm leading-4">{{ $val }}</td>
                </tr>
            @endforeach


            @if (\Str::lower($regPeriksa->status_lanjut) == 'ranap')
                @php
                    $firstRuang = $ruang->first();
                    $lastRuang = $ruang->where('stts_pulang', '!=', 'Pindah Kamar')->last();
                @endphp
                
                <tr class="align-top">
                    <th class="text-left text-nowrap whitespace-nowrap text-sm leading-4">Bangsal / Kamar</th>
                    <td class="px-2 text-sm leading-4">:</td>
                    <td class="w-full text-sm leading-none">
                        {{ \App\Helpers\SafeAccess::object($firstRuang, 'kamar->bangsal->nm_bangsal', '-') }} /
                        {{ \App\Helpers\SafeAccess::object($firstRuang, 'kd_kamar', '-') }}
                    </td>
                </tr>
            @else
                <tr class="align-top">
                    <th class="text-left text-nowrap whitespace-nowrap text-sm leading-4">Poliklinik</th>
                    <td class="px-2 text-sm leading-4">:</td>
                    <td class="w-full text-sm leading-none">{{ $regPeriksa->poliklinik->nm_poli }}</td>
                </tr>
                <tr class="align-top">
                    <th class="text-left text-nowrap whitespace-nowrap text-sm leading-4">Dokter</th>
                    <td class="px-2 text-sm leading-4">:</td>
                    <td class="w-full text-sm leading-none">{{ $dpjp->nm_dokter }}</td>
                </tr>
            @endif

            <tr class="align-top">
                <th class="text-left whitespace-nowrap text-sm">
                    @if (\Str::lower($regPeriksa->status_lanjut) == 'ranap')
                        Tanggal Perawatan
                    @else
                        Tanggal Periksa
                    @endif
                </th>
                <td class="px-2 text-sm leading-4">:</td>
                <td class="w-full text-sm leading-none">
                    {{ $regPeriksa->tgl_registrasi . ' ' . $regPeriksa->jam_reg }}
                    @if (\Str::lower($regPeriksa->status_lanjut) == 'ranap')
                        <span class="font-bold"> s/d </span> {{ \App\Helpers\SafeAccess::object($lastRuang, 'tgl_keluar', '-') . ' ' . \App\Helpers\SafeAccess::object($lastRuang, 'jam_keluar', '-') }}
                    @endif
                </td>
            </tr>
            <tr class="align-top">
                <th class="text-left whitespace-nowrap text-sm">Alamat</th>
                <td class="px-2 text-sm leading-4">:</td>
                <td class="w-full text-sm leading-none">{{ $regPeriksa->almt_pj }}</td>
            </tr>
        </table>
    </div>

    @if ($dokters)
        <h4 class="text-base font-bold" style="margin-bottom: 2px">
            &#x2022; Dokter Rawat
        </h4>
        <table class="table mb-5 w-full">
            <thead>
                <tr style="background-color: lightgoldenrodyellow">
                    <th class="border-b border-t px-1 text-left text-sm leading-none" style="border-color: #333, #333">Keterangan</th>
                    <th class="border-b border-t px-1 text-left text-sm leading-none" style="border-color: #333, #333" colspan="2">Nama Dokter</th>
                    <th class="border-b border-t px-1 text-left text-sm leading-none" style="border-color: #333, #333" colspan="3">Spesialis</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dokters as $kd => $vd)
                    <?php $frvd = $vd->first(); ?>
                    <tr>
                        <td class="px-1 text-sm font-bold leading-none" style="padding: 3px 3px; border-color: #333; width: 150px;">
                            @if ($loop->first)
                                <span class="px-1 font-bold">Dokter Rawat</span>
                            @endif
                        </td>
                        <td class="border-b px-1 text-sm leading-none" style="padding: 3px 3px; border-color: lightgray; max-width: 250px;" colspan="2">{{ \App\Helpers\SafeAccess::object($frvd, 'dokter->nm_dokter', '-') }}</td>
                        <td class="border-b px-1 text-sm leading-none" style="padding: 3px 3px; border-color: lightgray; max-width: 250px;" colspan="3">{{ \App\Helpers\SafeAccess::object($frvd, 'dokter->spesialis->nm_sps', '-') }}</td>
                    </tr>
                @endforeach

                {{-- gap --}}
                <tr>
                    <td class="border-b px-1 py-1 text-sm font-bold leading-none" style="border-color: #333"></td>
                    <td class="border-b px-1 py-1 text-sm font-bold leading-none" style="border-color: #333" colspan="5"></td>
                </tr>
            </tbody>
        </table>
    @endif

    <?php $totalRuang = 0; ?>
    @if ($ruang)
        <h4 class="text-base font-bold" style="margin-bottom: 2px">&#x2022; Ruang Rawat</h4>
        <table class="table mb-5 w-full">
            <thead>
                <tr style="background-color: lightgoldenrodyellow">
                    <th class="border-b border-t px-1 text-left text-sm leading-none" style="border-color: #333, #333">Keterangan</th>
                    <th class="border-b border-t px-1 text-left text-sm leading-none" style="border-color: #333, #333">Kamar Rawat</th>
                    <th class="border-b border-t px-1 text-right text-sm leading-none" style="border-color: #333, #333">Biaya</th>
                    <th class="border-b border-t px-1 text-right text-sm leading-none" style="border-color: #333, #333">Lama</th>
                    <th class="border-b border-t px-1 text-right text-sm leading-none" style="border-color: #333, #333">Tambahan</th>
                    <th class="whitespace-nowrap border-b border-t px-1 text-right text-sm leading-none" style="border-color: #333, #333">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ruang->groupBy('kd_kamar')->sortKeys() as $k => $v)
                    <?php $frv = $v->first(); ?>
                    <tr>
                        <td class="px-1 text-sm font-bold leading-none" style="padding: 3px, 3px; border-color: #333; width: 150px;">
                            @if ($loop->first)
                                <span class="font-bold">Kamar Rawat</span>
                            @endif
                        </td>
                        <td class="border-b px-1 text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray; max-width: 250px;">{{ $k }} {{ \App\Helpers\SafeAccess::object($frv, 'kamar->bangsal->nm_bangsal', '') }}</td>
                        <td class="border-b px-1 text-right text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray">{{ number_format(\App\Helpers\SafeAccess::object($frv, 'trf_kamar', 0), 0, ',', '.') }}</td>
                        <td class="border-b px-1 text-right text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray">{{ $v->sum('lama') }}</td>
                        <td class="border-b px-1 text-right text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray"></td>
                        <td class="border-b px-1 text-right text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray">{{ number_format(\App\Helpers\SafeAccess::object($frv, 'ttl_biaya', 0), 0, ',', '.') }}</td>
                    </tr>

                    <?php $totalRuang += \App\Helpers\SafeAccess::object($frv, 'ttl_biaya', 0); ?>
                @endforeach

                <tr>
                    <td></td>
                    <td class="px-1 py-1 text-left text-sm font-bold italic leading-none" style="padding: 3px, 3px; background-color: lightgrey" colspan="4">Total Biaya Ruang</td>
                    <td class="px-1 py-1 text-right text-sm font-bold italic leading-none" style="padding: 3px, 3px; background-color: lightgrey">
                        Rp {{ number_format($totalRuang, 0, ',', '.') }}
                    </td>
                </tr>

                <tr>
                    <td class="border-b px-1 py-1 text-sm font-bold leading-none" style="padding: 3px, 3px; border-color: #333"></td>
                    <td class="border-b px-1 py-1 text-sm font-bold leading-none" style="padding: 3px, 3px; border-color: #333" colspan="5"></td>
                </tr>
            </tbody>
        </table>
    @endif

    <h4 class="text-base font-bold" style="margin-bottom: 2px">&#x2022; Rincian Biaya</h4>
    <?php $sumOfBiling = 0; $sumResepPulang = 0; ?>
    @foreach ($billing as $noRawat => $items)
        <table class="table mb-5 w-full">
            <thead>
                <tr style="background-color: lightgoldenrodyellow">
                    <th class="border-b border-t px-1 text-left text-sm leading-none" style="border-color: #333, #333">Keterangan</th>
                    <th class="border-b border-t px-1 text-left text-sm leading-none" style="border-color: #333, #333">Tindakan/Terapi</th>
                    <th class="border-b border-t px-1 text-right text-sm leading-none" style="border-color: #333, #333">Biaya</th>
                    <th class="border-b border-t px-1 text-right text-sm leading-none" style="border-color: #333, #333">Jumlah</th>
                    <th class="border-b border-t px-1 text-right text-sm leading-none" style="border-color: #333, #333">Tambahan</th>
                    <th class="whitespace-nowrap border-b border-t px-1 text-right text-sm leading-none" style="border-color: #333, #333">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $sumAllCategories = 0;  
                    $indexItem = 0;
                    $noKartegori = 1;
                    $indexForResepPulang = 0;
                ?>
                @foreach ($items as $kategori => $item)
                    <?php $sumCategory = 0; ?>

                    {{-- Looping item --}}
                    @foreach ($item as $k => $v)
                        <?php $frv = $v->first(); ?>
                        @if (\Str::contains(\Str::lower($kategori), ['operasi']))
                            <tr>
                                <td class="relative px-1 text-sm font-bold leading-none" style="padding:3px 3px;border-color: #333; width: 150px;">
                                    @if ($indexItem == 0)
                                        <p class="font-bold" style="position: absolute; top: 3px; left: 4px; transform-origin: top left;">
                                            <span class="mr-1">{{ $noKartegori }}.</span>{{ $kategori }}
                                        </p>
                                    @endif
                                </td>

                                <td colspan="5" class="border-b px-1 text-sm font-bold leading-none" style="padding:3px 3px;border-color: lightgray; max-width: 250px;">
                                    {{ \App\Helpers\SafeAccess::object($v, 'detailPaket->nm_perawatan') }}
                                </td>
                            </tr>

                            @foreach (json_decode($v, true) as $key => $value)
                                @if (\Str::contains(\Str::lower($key), ['detail', 'paket']) || $value == 0)
                                    @continue
                                @endif

                                <tr>
                                    <td class="px-1 text-sm leading-none" style="padding:3px 3px;"></td>
                                    <td class="border-b px-1 pl-3 text-sm leading-none" style="padding:3px 3px;border-color: lightgray; max-width: 250px;">
                                        {{ \App\Helpers\BillingHelper::determineTarifOperasiTitle($key) }}
                                        <span class="float-right">:</span>
                                    </td>
                                    <td class="border-b px-1 text-right text-sm leading-none" style="padding:3px 3px;border-color: lightgray">{{ number_format($value, 0, ',', '.') }}</td>
                                    <td class="border-b px-1 text-right text-sm leading-none" style="padding:3px 3px;border-color: lightgray">1</td>
                                    <td class="border-b px-1 text-right text-sm leading-none" style="padding:3px 3px;border-color: lightgray">0</td>
                                    <td class="border-b px-1 text-right text-sm leading-none" style="padding:3px 3px;border-color: lightgray">{{ number_format($value, 0, ',', '.') }}</td>
                                </tr>

                                <?php $sumCategory += $value; ?>
                            @endforeach

                            @continue
                        @endif

                        <tr>
                            <td class="relative px-1 text-sm font-bold leading-none" style="padding:3px 3px; border-color: #333; width: 150px;">
                                @if ($indexItem == 0)
                                    <p class="font-bold" style="position: absolute; top: 3px; left: 4px; transform-origin: top left;">
                                        <span class="mr-1">{{ $noKartegori }}.</span>{{ $kategori }}
                                    </p>
                                @endif
                            </td>

                            <td class="border-b px-1 text-sm leading-none" style="padding:3px 3px; border-color: lightgray; max-width: 250px;">
                                @if (\Str::contains(\Str::lower($kategori), ['obat', 'bhp']))
                                    {{ $k }}
                                @else
                                    {{ \App\Helpers\SafeAccess::object($frv, 'jenisPerawatan->nm_perawatan', 'Invalid Params') }}
                                @endif

                                {{-- float right --}}
                                <span class="float-right">:</span>
                            </td>

                            <td class="border-b px-1 text-right text-sm leading-none" style="padding:3px 3px; border-color: lightgray">
                                @if (\Str::contains(\Str::lower($kategori), ['obat', 'bhp']))
                                    {{ number_format(\App\Helpers\SafeAccess::object($frv, 'biaya_obat', 0), 0, ',', '.') }}
                                @elseif (\Str::contains(\Str::lower($kategori), ['lab', 'radiologi']))
                                    {{ number_format(\App\Helpers\SafeAccess::object($frv, 'biaya', 0), 0, ',', '.') }}
                                @else
                                    <span class="whitespace-nowrap">{{ number_format(\App\Helpers\SafeAccess::object($frv, 'biaya_rawat', 0), 0, ',', '.') }}</span>
                                @endif
                            </td>

                            <td class="border-b px-1 text-right text-sm leading-none" style="padding:3px 3px; border-color: lightgray">
                                @if (\Str::contains(\Str::lower($kategori), ['obat', 'bhp']))
                                    {{ number_format($v->sum('jml'), 0, ',', '.') }}
                                @else
                                    {{ number_format($v->count(), 0, ',', '.') }}
                                @endif
                            </td>

                            <td class="border-b px-1 text-right text-sm leading-none" style="padding:3px 3px; border-color: lightgray">
                                @if (\Str::contains(\Str::lower($kategori), ['obat', 'bhp']))
                                    {{ number_format($v->sum('embalase') + $v->sum('tuslah'), 0, ',', '.') }}
                                @endif
                            </td>

                            <td class="border-b px-1 text-right text-sm leading-none" style="padding:3px 3px; border-color: lightgray">
                                @if (\Str::contains(\Str::lower($kategori), ['obat', 'bhp']))
                                    {{ number_format($v->sum('total'), 0, ',', '.') }}
                                @elseif (\Str::contains(\Str::lower($kategori), ['lab', 'radiologi']))
                                    {{ number_format($v->sum('biaya'), 0, ',', '.') }}
                                @elseif (!\Str::contains(\Str::lower($kategori), ['operasi']))
                                    {{ number_format($v->sum('biaya_rawat'), 0, ',', '.') }}
                                @endif
                            </td>
                        </tr>

                        {{-- Hitung total kategori --}}
                        @if (\Str::contains(\Str::lower($kategori), ['obat', 'bhp']))
                            <?php $sumCategory += $v->sum('total'); ?>
                        @elseif (\Str::contains(\Str::lower($kategori), ['lab', 'radiologi']))
                            <?php $sumCategory += $v->sum('biaya'); ?>
                        @elseif (!\Str::contains(\Str::lower($kategori), ['operasi']))
                            <?php $sumCategory += $v->sum('biaya_rawat'); ?>
                        @endif

                        {{-- Increment index item --}}
                        <?php $indexItem++; ?>
                    @endforeach

                    {{-- Total Kategori Row --}}
                    <tr>
                        <td></td>
                        <td class="px-1 py-1 text-left text-sm font-bold italic leading-none" style="background-color: lightgrey" colspan="4">Total Biaya {{ $kategori }}</td>
                        <td class="px-1 py-1 text-right text-sm font-bold italic leading-none" style="background-color: lightgrey">
                            Rp {{ number_format($sumCategory, 0, ',', '.') }}
                        </td>
                    </tr>

                    {{-- Retur Obat --}}
                    @if (\Str::contains(\Str::lower($kategori), ['obat', 'bhp']) && $returObat->count() > 0)
                        @if (\Str::contains($returObat->first()->map(function ($item) { return $item->no_retur_jual; })->first(), $noRawat))
                            <tr>
                                <td></td>
                                <td colspan="5" class="border-b border-t px-1 text-left text-sm leading-none" style="padding:3px 3px; padding-top: 10px; border-bottom-color: lightgray; border-top-color: #333">
                                    <span class="font-bold">Retur Obat</span>
                                </td>
                            </tr>

                            <?php $totalRetur = 0; ?>
                            @foreach ($returObat as $rok => $rov)
                                <?php $frrov = $rov->first(); ?>
                                <tr>
                                    <td></td>
                                    <td class="border-b px-1 text-left text-sm leading-none" style="padding:3px 3px; border-color: lightgray">
                                        {{ \App\Helpers\SafeAccess::object($frrov, 'obat->nama_brng') }}

                                        {{-- float right --}}
                                        <span class="float-right">:</span>
                                    </td>
                                    <td class="border-b px-1 text-right text-sm leading-none" style="padding:3px 3px; border-color: lightgray">
                                        {{ number_format(\App\Helpers\SafeAccess::object($frrov, 'h_retur', 0), 0, ',', '.') }}
                                    </td>
                                    <td class="border-b px-1 text-right text-sm leading-none" style="padding:3px 3px; border-color: lightgray">
                                        {{-- - {{ \App\Helpers\SafeAccess::object($frrov, 'jml_retur', 0) }} --}}
                                        - {{ $rov->sum('jml_retur') }}
                                    </td>
                                    <td class="border-b px-1 text-right text-sm leading-none" style="padding:3px 3px; border-color: lightgray">
                                        0
                                    </td>
                                    <td class="border-b px-1 text-right text-sm leading-none" style="padding:3px 3px; border-color: lightgray">
                                        {{-- - {{ number_format(\App\Helpers\SafeAccess::object($frrov, 'subtotal', 0), 0, ',', '.') }} --}}
                                        - {{ number_format($rov->sum('subtotal'), 0, ',', '.') }}
                                    </td>
                                </tr>

                                <?php 
                                    // $totalRetur += \App\Helpers\SafeAccess::object($frrov, 'subtotal', 0); 
                                    $totalRetur += $rov->sum('subtotal');
                                ?>
                            @endforeach

                            <tr>
                                <td></td>
                                <td class="px-1 py-1 text-left text-sm font-bold italic leading-none" style="padding: 3px, 3px; background-color: lightgrey" colspan="4">Total Retur Obat</td>
                                <td class="px-1 py-1 text-right text-sm font-bold italic leading-none" style="padding: 3px, 3px; background-color: lightgrey">
                                    - {{ number_format($totalRetur, 0, ',', '.') }}
                                </td>
                            </tr>

                            <?php $sumCategory -= $totalRetur; ?>

                            <tr>
                                <td></td>
                                <td class="px-1 py-1 text-left text-sm font-bold italic leading-none" style="padding: 3px, 3px; background-color: lightgrey" colspan="4">Total Obat Bersih</td>
                                <td class="px-1 py-1 text-right text-sm font-bold italic leading-none" style="padding: 3px, 3px; background-color: lightgrey">
                                    Rp {{ number_format($sumCategory, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                    @endif

                    {{-- gap --}}
                    <tr>
                        <td class="border-b px-1 py-1 text-sm font-bold leading-none" style="border-color: #333"></td>
                        <td class="border-b px-1 py-1 text-sm font-bold leading-none" style="border-color: #333" colspan="5"></td>
                    </tr>

                    {{-- total semua kategori  --}}
                    <?php $sumAllCategories += $sumCategory; ?>

                    {{-- Reset index item --}}
                    <?php
                        $noKartegori++;
                        $indexForResepPulang++;
                        $indexItem = 0;
                    ?>
                @endforeach
            </tbody>
        </table>

        @if ($indexForResepPulang == $items->count())
            @if ($resepPulang->get($noRawat))
                <?php $sumResepPulangPerNoRawat = 0; ?>

                <h4 class="text-base font-bold" style="margin-bottom: 2px">&#x2022; Resep Pulang</h4>
                <table class="table mb-5 w-full">
                    <thead>
                        <tr style="background-color: lightgoldenrodyellow">
                            <th class="border-b border-t px-1 text-left text-sm leading-none" style="border-color: #333, #333">Keterangan</th>
                            <th class="border-b border-t px-1 text-left text-sm leading-none" style="border-color: #333, #333">Tindakan / Terapi</th>
                            <th class="border-b border-t px-1 text-right text-sm leading-none" style="border-color: #333, #333">Biaya</th>
                            <th class="border-b border-t px-1 text-right text-sm leading-none" style="border-color: #333, #333">Jumlah</th>
                            <th class="border-b border-t px-1 text-right text-sm leading-none" style="border-color: #333, #333">Tambahan</th>
                            <th class="whitespace-nowrap border-b border-t px-1 text-right text-sm leading-none" style="border-color: #333, #333">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($resepPulang->get($noRawat) as $rpk => $rpv)
                            <?php $frpv = $rpv->first(); ?>
                            <tr>
                                <td class="px-1 text-sm font-bold leading-none" style="padding: 3px, 3px; border-color: #333; width: 150px;">
                                    @if ($loop->first)
                                        <span class="font-bold">Resep Pulang</span>
                                    @endif
                                </td>
                                <td class="border-b px-1 text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray; max-width: 250px;">{{ \App\Helpers\SafeAccess::object($frpv, 'obat->nama_brng', '') }}</td>
                                <td class="border-b px-1 text-right text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray">{{ number_format(\App\Helpers\SafeAccess::object($frpv, 'harga', 0), 0, ',', '.') }}</td>
                                <td class="border-b px-1 text-right text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray">{{ number_format($rpv->sum('jml_barang'), 0, ',', '.') }}</td>
                                <td class="border-b px-1 text-right text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray">0</td>
                                <td class="border-b px-1 text-right text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray">{{ number_format($rpv->sum('total'), 0, ',', '.') }}</td>
                            </tr>

                            <?php $sumResepPulangPerNoRawat += $rpv->sum('total'); ?>
                        @endforeach

                        <tr>
                            <td></td>
                            <td class="px-1 py-1 text-left text-sm font-bold italic leading-none" style="padding: 3px, 3px; background-color: lightgrey" colspan="4">Total Biaya Resep Pulang</td>
                            <td class="px-1 py-1 text-right text-sm font-bold italic leading-none" style="padding: 3px, 3px; background-color: lightgrey">
                                Rp {{ number_format($sumResepPulangPerNoRawat, 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- gap --}}
                        <tr>
                            <td class="border-b px-1 py-1 text-sm font-bold leading-none" style="padding: 3px, 3px; border-color: #333"></td>
                            <td class="border-b px-1 py-1 text-sm font-bold leading-none" style="padding: 3px, 3px; border-color: #333" colspan="5"></td>
                        </tr>
                    </tbody>
                </table>

                <?php $sumResepPulang += $sumResepPulangPerNoRawat; ?>
            @endif
        @endif

        <?php $sumOfBiling += $sumAllCategories; ?>
    @endforeach

    <h4 class="text-base font-bold" style="margin-bottom: 2px">&#x2022; Tambahan & Potongan Biaya</h4>
    <table class="table mb-5 w-full">
        <thead>
            <tr style="background-color: lightgoldenrodyellow">
                <th class="border-b border-t px-1 text-left text-sm leading-none" style="border-color: #333, #333">Keterangan</th>
                <th class="border-b border-t px-1 text-left text-sm leading-none" style="border-color: #333, #333">Tindakan / Terapi</th>
                <th class="border-b border-t px-1 text-right text-sm leading-none" style="border-color: #333, #333">Biaya</th>
                <th class="border-b border-t px-1 text-right text-sm leading-none" style="border-color: #333, #333">Jumlah</th>
                <th class="border-b border-t px-1 text-right text-sm leading-none" style="border-color: #333, #333">Tambahan</th>
                <th class="whitespace-nowrap border-b border-t px-1 text-right text-sm leading-none" style="border-color: #333, #333">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $sumAdditionalCost = 0; ?>
            @foreach ($tambahanBiaya as $tbk => $tbv)
                <tr>
                    <td class="px-1 text-sm font-bold leading-none" style="padding: 3px, 3px; border-color: #333; width: 150px;">
                        @if ($tbk == 0)
                            <span class="font-bold">Tambahan Biaya</span>
                        @endif
                    </td>
                    <td class="border-b px-1 text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray; max-width: 250px;">{{ $tbv->nama_biaya }}</td>
                    <td class="border-b px-1 text-right text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray">{{ $tbv->besar_biaya }}</td>
                    <td class="border-b px-1 text-right text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray">1</td>
                    <td class="border-b px-1 text-right text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray">0</td>
                    <td class="border-b px-1 text-right text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray">{{ $tbv->besar_biaya }}</td>
                </tr>

                <?php $sumAdditionalCost += $tbv->besar_biaya ?? 0; ?>
            @endforeach

            <tr>
                <td class="px-1 text-sm font-bold leading-none" style="padding: 3px, 3px; border-color: #333; width: 150px;">
                    @if ($tambahanBiaya->count() == 0)
                        <span class="font-bold">Tambahan Biaya</span>
                    @endif
                </td>
                <td class="px-1 py-1 text-left text-sm font-bold italic leading-none" style="padding: 3px, 3px; background-color: lightgrey" colspan="4">Total Tambahan Biaya</td>
                <td class="px-1 py-1 text-right text-sm font-bold italic leading-none" style="padding: 3px, 3px; background-color: lightgrey">
                    Rp {{ number_format($sumAdditionalCost, 0, ',', '.') }}
                </td>
            </tr>

            {{-- gap --}}
            <tr>
                <td class="border-b px-1 py-1 text-sm font-bold leading-none" style="padding: 3px, 3px; border-color: #333"></td>
                <td class="border-b px-1 py-1 text-sm font-bold leading-none" style="padding: 3px, 3px; border-color: #333" colspan="5"></td>
            </tr>

            {{-- ---------- --}}

            <?php $totalCostDiscount = 0; ?>
            @foreach ($potonganBiaya as $pbk => $pbv)
                <tr>
                    <td class="px-1 text-sm font-bold leading-none" style="padding: 3px, 3px; border-color: #333; width: 150px;">
                        @if ($pbk == 0)
                            <span class="font-bold">Potongan Biaya</span>
                        @endif
                    </td>
                    <td class="border-b px-1 text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray; max-width: 250px;">{{ $pbv->nama_pengurangan }}</td>
                    <td class="border-b px-1 text-right text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray">{{ $pbv->besar_pengurangan }}</td>
                    <td class="border-b px-1 text-right text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray">1</td>
                    <td class="border-b px-1 text-right text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray">0</td>
                    <td class="border-b px-1 text-right text-sm leading-none" style="padding: 3px, 3px; border-color: lightgray">{{ $pbv->besar_pengurangan }}</td>
                </tr>

                <?php $totalCostDiscount += $pbv->besar_pengurangan ?? 0; ?>
            @endforeach

            <tr>
                <td class="px-1 text-sm font-bold leading-none" style="padding: 3px, 3px; border-color: #333; width: 150px;">
                    @if ($potonganBiaya->count() == 0)
                        <span class="font-bold">Potongan Biaya</span>
                    @endif
                </td>
                <td class="px-1 py-1 text-left text-sm font-bold italic leading-none" style="padding: 3px, 3px; background-color: lightgrey" colspan="4">
                    Total Potongan Biaya
                </td>
                <td class="px-1 py-1 text-right text-sm font-bold italic leading-none" style="padding: 3px, 3px; background-color: lightgrey">
                    Rp {{ number_format($totalCostDiscount, 0, ',', '.') }}
                </td>
            </tr>

            {{-- gap --}}
            <tr>
                <td class="border-b px-1 py-1 text-sm font-bold leading-none" style="padding: 3px, 3px; border-color: #333"></td>
                <td class="border-b px-1 py-1 text-sm font-bold leading-none" style="padding: 3px, 3px; border-color: #333" colspan="5"></td>
            </tr>

            {{-- ---------- --}}
            {{-- Total Keseluruhan --}}
            <tr>
                <td class="border-b px-1 text-left font-bold italic leading-none" style="padding: 4px, 4px; background-color: lightblue; border-color: #333" colspan="5">Total Keseluruhan</td>
                <td class="border-b px-1 text-right font-bold italic leading-none" style="padding: 4px, 4px; background-color: lightblue; border-color: #333">
                    Rp {{ number_format($totalRuang + $sumOfBiling + $sumResepPulang + $sumAdditionalCost - $totalCostDiscount, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    @php
        $QRAsmenKeuangan = \App\Helpers\SignHelper::rsia($asmenKeuangan->nama, $asmenKeuangan->nik);
        if ($kasir) {
            $QRKasir = \App\Helpers\SignHelper::rsia($kasir->nama, $kasir->nip);
        } else {
            $QRKasir = \App\Helpers\SignHelper::blankRsia();
        }
    @endphp

    <div class="my-4">
        <table class="w-full">
            <tr class="align-bottom">
                <td class="w-full text-center">
                    <p class="mb-3 leading-none">
                        Mengetahui, <br>
                        a/n Direktur <br>
                        Kabid Umum dan Keuangan
                    </p>

                    <img src="{{ $QRAsmenKeuangan->getDataUri() }}" alt="QR Asment Keuangan" style="width: 150px; height: 150px;"/>

                    <p class="mt-3">{{ \App\Helpers\SafeAccess::object($asmenKeuangan, 'nama', '- null -') }}</p>
                </td>
                <td class="w-full text-center">
                    <p class="mb-3">
                        &nbsp; <br>
                        Kasir
                    </p>

                    <img src="{{ $QRKasir->getDataUri() }}" alt="QR Petugas kasir" style="width: 150px; height: 150px;"/>

                    <p class="mt-3">{{ \App\Helpers\SafeAccess::object($kasir, 'nama', '- null -') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="mt-4">
        <p class="text-sm italic leading-none">NB : Mohon maaf apabila ada tagihan yang belum tertagihkan dalam perincian ini akan ditagihkan kemudian, dan apabila berlebih akan dikembalikan</p>
    </div>
</main>
