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

    <main class="font-normal">
        {{-- text on center --}}
        <div class="text-center">
            <h1 class="text-xl font-bold">BUKTI KEHADIRAN</h1>
        </div>

        {{-- Prolog --}}
        <table class="mt-4 w-full">
            <tr>
                <td>Perihal</td>
                <td style="max-width: 10px;">:</td>
                <td>{{ $undangan->perihal }}</td>
            </tr>
            <tr>
                <td>Tempat</td>
                <td style="max-width: 10px;">:</td>
                <td>{{ $undangan->lokasi }}</td>
            </tr>
            <tr>
                <td>Agenda</td>
                <td style="max-width: 10px;">:</td>
                <td>{{ \Carbon\Carbon::parse($undangan->tanggal)->translatedFormat('l, d F Y H:i') }}</td>
            </tr>
            <tr>
                <td>Penanggung Jawab</td>
                <td style="max-width: 10px;">:</td>
                <td>{{ $undangan->penanggungJawab->nama }}</td>
            </tr>
        </table>

        <div class="mt-4">
            <p class="text-xs italic leading-none" style="color: gray">Dokumen ini dibuat secara otomatis oleh sistem</p>
            <p class="text-xs italic leading-none" style="color: gray">Semua bukti kehadiran yang tertera diatas adalah benar dan tidak bisa diubah</p>
        </div>

        {{-- daftar karyawan hadir loop $penerima --}}
        <table class="mt-2 w-full">
            <tr class="border-b-2 font-bold p-2" style="background-color: #d1d1d1;">
                <td class="py-1 px-2" style="border: 1px solid #d1d1d1">No</td>
                <td class="py-1 px-2" style="border: 1px solid #d1d1d1">Nama</td>
                <td class="py-1 px-2" style="border: 1px solid #d1d1d1">Jabatan</td>
                <td class="py-1 px-2" style="border: 1px solid #d1d1d1" width="70">Timestamp</td>
            </tr>
            @foreach ($penerima as $item)
            <tr>
                <td class="px-2 py-1" style="border-bottom: 1px solid #d1d1d1;">{{ $loop->iteration }}.</td>
                <td class="px-2 py-1" style="border-bottom: 1px solid #d1d1d1;">{{ $item->pegawai->nama ?? '-' }}</td>
                <td class="px-2 py-1" style="border-bottom: 1px solid #d1d1d1;">{{ $item->pegawai->jbtn }}</td>
                <td class="px-2 py-1 whitespace-nowrap" style="border-bottom: 1px solid #d1d1d1;">{{ $item->kehadiran ? $item->kehadiran->created_at->translatedFormat('d M Y H:i') : '-' }}</td>
                </td>
            </tr>
            @endforeach
        </table>
    </main>
</x-print-layout>