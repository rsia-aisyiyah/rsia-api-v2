<x-print-layout>
    @push('styles')
    <style>
        @page {
            header: page-header;
            footer: page-footer;
            size: 210mm 330mm portrait;
            margin: 100px 0px 60px 0px;
        }

        main {
            margin: 0, 14mm
        }
    </style>
    @endpush

    <htmlpageheader name="page-header">
        <img src="{{ asset('assets/images/kop-surat/header.png') }}" alt="Kop Header" />
    </htmlpageheader>

    <htmlpagefooter name="page-footer">
        <img src="{{ asset('assets/images/kop-surat/footer.png') }}" alt="Kop Footer" />
    </htmlpagefooter>


    <main>
        <div class="">
            <div class="mb-3 text-center">
                <img src="{{ asset('assets/images/bismillah-2.png') }}" alt="basmallah" style="width: 35%">
            </div>

            <div class="text-right mb-3">
                Pekalongan, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}
            </div>

            <div>
                <table class="mb-3">
                    <tr>
                        <th class="text-left" style="width: 100px;">Nomor</th>
                        <td style="width: 50px;">:</td>
                        <td>
                            <?= $undangan->surat->no_surat ?? "-" ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-left" style="width: 100px;">Hal</th>
                        <td style="width: 50px;">:</td>
                        <td>Undangan</td>
                    </tr>
                    <tr>
                        <th class="text-left" style="width: 100px;">Lampiran</th>
                        <td style="width: 50px;">:</td>
                        <td>
                            <?= count($penerima) <= 7 ? '-' : '1 (satu) berkas' ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="mb-3 mt-4">
                <span class="p-0 m-0">Kepada Yth.</span>
                @if (count($penerima) <= 5) 
                    <ol style="list-style-type: decimal; padding-left: 28px;">
                        @foreach ($penerima as $key => $item)
                            <li>{{ $item->pegawai->nama }}</li>
                        @endforeach
                    </ol>
                @else
                    <p>Nama-nama terlampir</p>
                @endif
            </div>

            <p class="mb-1">
                Di <br />
            <div class="pl-5 leading-none">Tempat</div>
            </p>

            <div class="mt-6">
                <p class="font-weight-bold"><i>Assalamu'alaikum Warahmatullahi Wabarakatuh</i></p>
                <p style="text-indent: 50px; text-align: justify; line-height: 1.8" class="mt-3">
                    Puji syukur kami panjatkan kepada Allah SWT atas rahmat-Nya yang melimpah. Kami bersyukur atas petunjuk-Nya yang tak
                    pernah terputus. Semoga kita selalu berada dalam lindungan-Nya dan mendapatkan keberkahan-Nya. Aamiin. <br />
                    Dihomon kehadirannya pada :
                </p>

                <div style="width: 90%; margin: auto; margin-top: 20px; margin-bottom: 20px;">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th style="width: 100px; padding-bottom: 5px;" class="text-left">Hari</th>
                            <td style="width: 20px;">:</td>
                            <td>{{ \Carbon\Carbon::parse($undangan->tanggal)->isoFormat('dddd, D MMMM Y') }}</td>
                        </tr>
                        <tr>
                            <th style="width: 100px; padding-bottom: 5px;" class="text-left">Jam</th>
                            <td style="width: 20px;">:</td>
                            <td>{{ \Carbon\Carbon::parse($undangan->tanggal)->isoFormat('HH:mm') }} WIB s/d selesai</td>
                        </tr>
                        <tr>
                            <th style="width: 100px; padding-bottom: 5px;" class="text-left">Tempat</th>
                            <td style="width: 20px;">:</td>
                            <td>{{ $undangan->lokasi }}</td>
                        </tr>
                        <tr>
                            <th style="width: 100px; padding-bottom: 5px;" class="text-left">Acara</th>
                            <td style="width: 20px;">:</td>
                            <td>{{ $undangan->perihal }}</td>
                        </tr>
                    </table>
                </div>

                <p class="mb-5" style="line-height: 1.8">
                    Demikian disampaikan,. Terimakasih. <br />
                    <i>Nasrun minallohi wa fatkhun qorieb.</i> <br /><br>
                    <strong><i>Wassalamu'alaikum Warahmatullahi Wabarakatuh</i></strong>
                </p>
            </div>

            <table class="table table-borderless table-sm mt-4">
                <tr>
                    <td class="p-0 m-0" style="width:32%;"></td>
                    <td class="p-0 m-0" style="width:32%;"></td>
                    <td class="p-0 m-0 text-center">
                        <p class="mb-0 pb-0">Pekalongan, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                        <p class="mb-0 pb-0" style="font-weight: bold; text-transform: capitalize;">{{ strtolower($undangan->penanggungJawab->jenjang_jabatan->nama) }}</p>
                        <br /><br /><br /><br /><br /><br />
                        <p class="mb-0 pb-0" style="font-weight: bold">{{ $undangan->penanggungJawab->nama }}</p>
                    </td>
                </tr>
            </table>

            @if ($undangan->catatan != null && $undangan->catatan != '-' && $undangan->catatan != '')
            <div class="mt-4">
                <strong>
                    NB : {{ $undangan->catatan }}
                </strong>
            </div>
            @endif
        </div>

        {{-- page break --}}

        <div>
            @if (count($penerima) > 5)

            <div style="page-break-before: always;"></div>

            {{-- break for penerima --}}
            <div class="break-page"></div>

            <h5 class="text-center text-xl" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                Daftar Nama Terundang
            </h5>

            <div class="mt-4">
                <table class="table table-sm w-full">
                    <thead>
                        <tr>
                            <th class="px-2 py-2 text-left" style="background: #d1d1d1;">No.</th>
                            <th class="px-2 py-2 text-left" style="background: #d1d1d1;">Nama</th>
                            <th class="px-2 py-2 text-left" style="background: #d1d1d1;">Jabatan</th>
                            <th class="px-2 py-2 text-left" style="background: #d1d1d1;">Departemen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penerima as $key => $item)
                        <tr>
                            <td class="px-2 py-2" style="border-bottom: 1px solid #d1d1d1;">{{ $key + 1 }}</td>
                            <td class="px-2 py-2" style="border-bottom: 1px solid #d1d1d1;">{{ $item->pegawai->nama }}</td>
                            <td class="px-2 py-2" style="border-bottom: 1px solid #d1d1d1;">{{ $item->pegawai->jbtn }}</td>
                            <td class="px-2 py-2" style="border-bottom: 1px solid #d1d1d1;">{{ $item->pegawai->dep->nama }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <div>
            <div style="page-break-before: always;"></div>

            <?php $maxRow = 20 ?>

            <h5 class="text-center" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                <u>DAFTAR HADIR</u>
            </h5>

            <table class="mt-5">
                <tr>
                    <th style="width: 4cm;" class="text-left">Hari / Tanggal</th>
                    <td>: {{ \Carbon\Carbon::parse($undangan->tanggal)->isoFormat('dddd, D MMMM Y') }}</td>
                </tr>
                <tr>
                    <th style="width: 4cm;" class="text-left">Jam</th>
                    <td>: {{ \Carbon\Carbon::parse($undangan->tanggal)->isoFormat('HH:mm') }} WIB s/d selesai</td>
                </tr>
                <tr>
                    <th style="width: 4cm;" class="text-left">Agenda</th>
                    <td>: {{ $undangan->perihal }}</td>
                </tr>
            </table>

            <div class="mt-5">
                <div class="text-right">
                    <small class="text-muted" style="color: #6c757d !important; font-size: 0.7rem !important;">
                        Generated automatically by system - {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y HH:mm') }}
                    </small>
                </div>
                <table class="table table-bordered table-sm w-full">
                    <thead>
                        <tr>
                            <th class="px-2 py-1" style="background: #d1d1d1; width: 50px;">No.</th>
                            <th class="px-2 py-1" style="background: #d1d1d1;">Nama</th>
                            <th class="px-2 py-1" style="background: #d1d1d1;">Departemen</th>
                            <th class="px-2 py-1" style="background: #d1d1d1; width: 30%;">TTD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penerima as $key => $item)
                        <tr>
                            <td style="border-bottom: 1px solid #d1d1d1; padding-top: 5px; padding-bottom: 5px; border-left: 1px solid #d1d1d1;" class="text-center">{{ $key + 1 }}.</td>
                            <td style="border-bottom: 1px solid #d1d1d1; padding-top: 5px; padding-bottom: 5px; border-left: 1px solid #d1d1d1;" class="px-2">
                                <p>{{ $item->pegawai->nama }}</p>
                            </td>
                            {{-- if in jbtn contain koordinator change to koor --}}
                            <td style="border-bottom: 1px solid #d1d1d1; padding-top: 5px; padding-bottom: 5px; border-left: 1px solid #d1d1d1;" class="px-2">
                                {{ $item->pegawai->jbtn }}
                            </td>
                            <td style="border-bottom: 1px solid #d1d1d1; padding-top: 5px; padding-bottom: 5px; border-left: 1px solid #d1d1d1; border-right: 1px solid #d1d1d1;" class="px-2 {{ $key % 2 != 0 ? 'text-center' : '' }}">
                                <p style="width: 100%;">{{ $key + 1 }}.</p>
                            </td>
                        </tr>
                        @endforeach

                        {{-- if count penerima < maxRow loop blank --}} 
                        @if (count($penerima) < $maxRow) 
                            @for ($i=0; $i < $maxRow - count($penerima); $i++) 
                                <tr>
                                    <td style="border-bottom: 1px solid #d1d1d1; padding-top: 5px; padding-bottom: 5px; border-left: 1px solid #d1d1d1;" class="text-center">
                                        {{ $i + count($penerima) + 1 }}.
                                    </td>
                                    <td style="border-bottom: 1px solid #d1d1d1; padding-top: 5px; padding-bottom: 5px; border-left: 1px solid #d1d1d1;" class="px-2">
                                        <div>
                                            <br><br><br>
                                        </div>
                                    </td>
                                    <td style="border-bottom: 1px solid #d1d1d1; padding-top: 5px; padding-bottom: 5px; border-left: 1px solid #d1d1d1;" class="px-2">
                                        <div>
                                            <br><br><br>
                                        </div>
                                    </td>
                                    <td style="border-bottom: 1px solid #d1d1d1; padding-top: 5px; padding-bottom: 5px; border-left: 1px solid #d1d1d1; border-right: 1px solid #d1d1d1;" class="px-2 {{ ($i + count($penerima)) % 2 == 0 ? '' : 'text-center' }}">
                                        <div>{{ $i + count($penerima) + 1 }}.</div>
                                    </td>
                                </tr>
                            @endfor
                        @elseif (count($penerima) > $maxRow)
                            @for ($i = 0; $i < $maxRow - (count($penerima) % $maxRow); $i++) 
                                <tr>
                                    <td style="border-bottom: 1px solid #d1d1d1; padding-top: 5px; padding-bottom: 5px; border-left: 1px solid #d1d1d1;" class="text-center">
                                        {{ $i + count($penerima) + 1 }}.
                                    </td>
                                    <td style="border-bottom: 1px solid #d1d1d1; padding-top: 5px; padding-bottom: 5px; border-left: 1px solid #d1d1d1;" class="px-2">
                                        <div>
                                            <br><br><br>
                                        </div>
                                    </td>
                                    <td style="border-bottom: 1px solid #d1d1d1; padding-top: 5px; padding-bottom: 5px; border-left: 1px solid #d1d1d1;" class="px-2">
                                        <div>
                                            <br><br><br>
                                        </div>
                                    </td>
                                    <td style="border-bottom: 1px solid #d1d1d1; padding-top: 5px; padding-bottom: 5px; border-left: 1px solid #d1d1d1; border-right: 1px solid #d1d1d1;" class="px-2 {{ ($i + count($penerima)) % 2 == 0 ? '' : 'text-center' }}">
                                        <p>{{ $i + count($penerima) + 1 }}.</p>
                                    </td>
                                </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</x-print-layout>