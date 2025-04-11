@foreach ($radiologi as $key => $item)
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
		<div class="text-center">
			<h4 class="text-lg font-bold">HASIL PEMERIKSAAN RADIOLOGI</h4>
		</div>

		<div class="mt-3">
			<table class="table w-full">
				<tr class="align-top">
					<td>
						<table class="table w-full">
							@foreach ([
								'No. RM'      => $regPeriksa->no_rkm_medis,
								'Nama Pasien' => \Str::title($pasien->nm_pasien),
								'JK / Umur'   => $pasien->jk . ' / ' . $regPeriksa->umurdaftar . ' ' . $regPeriksa->sttsumur,
								'Alamat'      => \Str::title($pasien->alamat),
								'No. Periksa' => $item->no_rawat,
							] as $key => $val)
								<tr class="align-top">
									<th class="text-left leading-5">{{ $key }}</th>
									<td class="leading-5">:</td>
									<td class="leading-5">{{ $val }}</td>
								</tr>
							@endforeach
						</table>
					</td>
					<td>
						<table class="table w-full">
							@foreach ([
								'Penanggung Jawab' => $item->dokter->nm_dokter,
								'Dokter Pengirim'  => $item->dokterPerujuk->nm_dokter,
								'Tgl. Pemeriksaan' => $item->tgl_periksa,
								'Jam pemeriksaan'  => $item->jam,
								'Jenis Perawan'    => $item->jenisPerawatan->nm_perawatan,
							] as $key => $val)
								<tr class="align-top">
									<th class="text-left leading-5">{{ $key }}</th>
									<td class="leading-5">:</td>
									<td class="leading-5">{{ $val }}</td>
								</tr>
							@endforeach
						</table>
					</td>
				</tr>
			</table>
		</div>

		<div class="mt-5 w-full">
			<div class="w-full border-b" style="border-bottom: 1px solid #333;">
				<div class="border-t border-b mb-2" style="border-top: 1px solid #333; border-bottom: 1px solid #333;">
					<h4 class="text-base font-bold">Hasil Pemeriksaan :</h4>
				</div>
				<p class="leading-none pb-2">{!! nl2br($item->hasilRadiologi->hasil ?? '') !!}</p>
			</div>
		</div>

		@php
			$QRDokter = App\Helpers\SignHelper::rsia($item->dokter->nm_dokter, $item->dokter->id);
			$QRPetugas = App\Helpers\SignHelper::rsia($item->petugas->nama, $item->petugas->id);
		@endphp

		<div class="mt-5">
			<table class="table w-full">
				<tr class="align-middle">
					<td class="text-center">
						<p class="text-base leading-none">&nbsp;</p>
						<p class="text-base leading-none">Penanggung Jawab</p>
						<img class="m-0 p-0 leading-none" src="{{ $QRDokter->getDataUri() }}" alt="QR Penanggung Jawab" style="width: 150px !important; height: 150px !important;"/>
						<p class="text-base leading-none">{{ $item->dokter->nm_dokter }}</p>
					</td>
					<td class="text-center">
						<div class="text-base leading-none">Tgl. Cetak : {{ date('d/m/Y H:i:s', strtotime($item->tgl_periksa . ' ' . $item->jam)) }}</div>
						<p class="text-base leading-none">Petugas Radiologi</p>
						<img class="m-0 p-0 leading-none" src="{{ $QRPetugas->getDataUri() }}" alt="QR Petugas" style="width: 150px !important; height: 150px !important;"/>
						<p class="text-base leading-none">{{ $item->petugas->nama }}</p>
					</td>
				</tr>
			</table>
		</div>
	</main>

	@if (!$loop->last)
		<div style="page-break-after: always;"></div>
	@endif
@endforeach
