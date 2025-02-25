<?php

namespace App\Http\Controllers\v2;

use App\Helpers\ApiResponse;
use App\Helpers\PDFHelper;
use App\Helpers\SignHelper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class BerkasKlaimController2 extends Controller
{
    /**
     * Ukuran kertas F4
     *
     * @var array
     */
    protected $f4 = [0, 0, 609.448, 935.432];

    /**
     * Orientasi kertas
     *
     * @var string
     */
    protected $orientation = 'portrait';

    /**
     * Koordinat berdasarkan departemen
     *
     * @var array
     */
    protected $koorByDepartemen = [
        'Anak'      => 'Anak',
        'Kandungan' => 'Nifas',
        'BY'        => 'PERINATOLOGI',
        'VK'        => 'VK',
    ];


    // ====================================================================================================


    public function export($sep)
    {
        \App\Jobs\ExportPdfJob::dispatch($sep)->delay(now()->addSeconds(5));

        sleep(2);

        return response()->json([
            'message' => 'berkas klaim akan segera di export.',
        ]);
    }


    // ====================================================================================================


    public function delete($sep)
    {
        $bSep = \App\Models\BridgingSep::where('no_sep', $sep)->first();
        if (!$bSep) {
            return ApiResponse::error('Resource not found', 'Data tidak ditemukan', null, 404);
        }

        $berkas = \App\Models\BerkasDigitalPerawatan::where('no_rawat', $bSep->no_rawat)->where('kode', '009')->first();
        if (!$berkas) {
            return ApiResponse::error('Resource not found', 'Data tidak ditemukan', null, 404);
        }

        try {
            $st = \Illuminate\Support\Facades\Storage::disk('sftp');
            $filePath = '/simrsiav2/file/berkas_klaim_pengajuan/' . $berkas->lokasi_file;

            if ($berkas->lokasi_file && $st->exists($filePath) && !$st->directories($filePath)) {
                $st->delete($filePath);
            }

            // Hapus record di database setelah memastikan file dihapus dengan aman
            $berkas->delete();

            return ApiResponse::success('Data berhasil dihapus');
        } catch (\Throwable $th) {
            return ApiResponse::error('Internal Server Error', 'Gagal menghapus berkas klaim', $th->getMessage(), 500);
        }
    }

    // ====================================================================================================


    public function print($sep, Request $request)
    {
        $bSep        = \App\Models\BridgingSep::where('no_sep', $sep)->first();
        $regPeriksa  = \App\Models\RegPeriksa::with(['pasien', 'caraBayar', 'poliklinik'])->where('no_rawat', $bSep->no_rawat)->first();
        $dpjp        = \App\Models\Dokter::with('pegawai')->where('kd_dokter', $regPeriksa->kd_dokter)->first();

        $pendukung   = \App\Models\RsiaUpload::where('no_rawat', $bSep->no_rawat)->get()->map(function ($item) {
            $item->kategori = strtolower($item->kategori);
            return $item;
        });

        $ttdPasien   = \App\Models\RsiaVerifSep::where('no_sep', $bSep->no_sep)->first();
        $barcodeDPJP = SignHelper::rsia($dpjp->pegawai->nama, $dpjp->pegawai->id);

        $pasien      = $regPeriksa->pasien;

        $title = $bSep->no_sep . "_" . trim(Str::upper($pasien->nm_pasien));
        $pdfTitle = Str::endsWith($title, '.') ? $title . 'pdf' : $title . '.pdf';
        $pdfNewTitle = $sep . '.pdf';

        // ==== Generate pages

        $pages = collect([
            $this->genSepPage($bSep, $regPeriksa, $pasien, $barcodeDPJP),
            $this->genTriaseUgd($bSep->jnspelayanan, $regPeriksa, $barcodeDPJP),
            $this->genAsmedUgdPage($bSep->jnspelayanan, $regPeriksa, $barcodeDPJP),
            $this->genResumeMedisPage($bSep, $pasien, $regPeriksa, $barcodeDPJP, $ttdPasien),
            $this->genCpptPage($bSep->jnspelayanan, $regPeriksa, $pasien),
            $this->genOperasiPage($bSep->no_rawat, $regPeriksa, $barcodeDPJP),
            $this->genSpriPage($bSep, $pasien, $barcodeDPJP),
            $this->genRencanaKontrolPage($bSep, $regPeriksa, $pasien, $barcodeDPJP),
            $this->pendukung($pendukung, ['skl']),
            $this->genHasilPemeriksaanUsg($bSep, $regPeriksa, $pasien, $dpjp, $barcodeDPJP),
            $this->pendukung($pendukung, ['surat rujukan']),
            $this->pendukung($pendukung, ['usg']),
            $this->genHasilLabPage($bSep, $regPeriksa, $pasien),
            $this->genHasilRadiologiPage($regPeriksa, $pasien, $barcodeDPJP),
            $this->pendukung($pendukung, ['laborat']),
            $this->pendukung($pendukung, ['skl', 'surat rujukan', 'usg', 'laborat'], true),
            $this->genResepObatPage($regPeriksa, $dpjp, $pasien),
            $this->genBillingPage($regPeriksa, $dpjp, $pasien),
            $this->genKwitansiNaikKelasPage($bSep, $regPeriksa, $pasien, $ttdPasien),
        ]);


        // ==== Filter pages

        $pages = $pages->filter(function ($page) {
            return !empty($page);
        });

        // ==== Generate PDF

        $pdf = PDFHelper::generate('berkas-klaim.layout', [
            'title' => $title,
            'pages' => $pages
        ], false);

        // ==== Merge PDF

        $inacbgReport = $this->genInacbgReportPage($sep);
        if ($inacbgReport) {
            $pdf = PDFHelper::merge([$pdf, $inacbgReport]);
            $pdf->setFileName($pdfNewTitle);
        }

        // ==== Return PDF

        return response($pdf->stream($pdfNewTitle), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('Content-Disposition', 'inline; filename="' . $pdfNewTitle . '"');
    }


    // ====================================================================================================


    /**
     * Generate SEP (Surat Eligibilitas Peserta) document.
     *
     * @param \App\Models\BridgingSep $sep The SEP (Surat Eligibilitas Peserta) model instance.
     * @param \App\Models\RegPeriksa $regPeriksa The registration check model instance.
     * @param \App\Models\Pasien $pasien The patient model instance.
     * @param \Endroid\QrCode\Writer\Result\PngResult $barcodeDPJP The DPJP barcode.
     * 
     * @return string Rendered SEP document view.
     */
    public function genSepPage($sep, $regPeriksa, $pasien, $barcodeDPJP)
    {
        $diagnosa = \App\Models\DiagnosaPasien::with('penyakit')->orderBy('prioritas', 'asc')->where('no_rawat', $regPeriksa->no_rawat)->get();
        $prosedur = \App\Models\ProsedurPasien::with('penyakit')->orderBy('prioritas', 'asc')->where('no_rawat', $regPeriksa->no_rawat)->get();

        $barcodePasien = SignHelper::toQr($sep->no_kartu);

        $berkasSep = view('berkas-klaim.sep', [
            'sep'           => $sep,
            'pasien'        => $pasien,
            'regPeriksa'    => $regPeriksa,
            'diagnosa'      => $diagnosa,
            'prosedur'      => $prosedur,
            'barcodeDPJP'   => $barcodeDPJP->getDataUri(),
            'barcodePasien' => $barcodePasien->getDataUri(),
        ]);

        return $berkasSep->render();
    }

    /**
     * Generate triage UGD view for claim documents.
     *
     * @param int $jenisPelayanan The type of service.
     * @param \App\Models\RegPeriksa $regPeriksa The registration check model.
     * @param \App\Models\BarcodeDPJP $barcodeDPJP The barcode DPJP model.
     * @return string|null The rendered triage UGD view or null if conditions are not met.
     */
    public function genTriaseUgd($jenisPelayanan, $regPeriksa, $barcodeDPJP)
    {
        if ($jenisPelayanan == 1) {
            return null;
        }

        $triase = \App\Models\RsiaTriaseUgd::where('no_rawat', $regPeriksa->no_rawat)->first();

        if ($triase) {
            $triase = view('berkas-klaim.triase', [
                'regPeriksa'  => $regPeriksa,
                'triase'      => $triase,
                'barcodeDPJP' => $barcodeDPJP->getDataUri(),
            ])->render();

            return $triase;
        }
    }

    /**
     * Generates the Asmed UGD page for the given service type, registration check, and DPJP barcode.
     *
     * @param int $jenisPelayanan The type of service.
     * @param \App\Models\RegPeriksa $regPeriksa The registration check model.
     * @param \App\Models\BarcodeDPJP $barcodeDPJP The DPJP barcode model.
     * @return string|null The rendered Asmed UGD page or null if the service type is 2.
     */
    public function genAsmedUgdPage($jenisPelayanan, $regPeriksa, $barcodeDPJP)
    {
        if ($jenisPelayanan != 2) {
            return null;
        }

        $asmed = \App\Models\PenilaianMedisIgd::with('dokter.sidikjari')->where('no_rawat', $regPeriksa->no_rawat)->first();

        if ($asmed) {
            $asmed = view('berkas-klaim.asmed-ugd', [
                'regPeriksa'  => $regPeriksa,
                'asmed'       => $asmed,
                'barcodeDPJP' => $barcodeDPJP->getDataUri(),
            ])->render();

            return $asmed;
        }
    }

    /**
     * Generate resume medis page for claim documents.
     *
     * @param \App\Models\BridgingSep $sep The SEP (Surat Eligibilitas Peserta) model instance.
     * @param \App\Models\Pasien $pasien The patient model instance.
     * @param \App\Models\RegPeriksa $regPeriksa The registration check model instance.
     * @param \App\Models\BarcodeDPJP $barcodeDPJP The DPJP barcode model instance.
     * @return string|null The rendered resume medis page view or null if no resume data is found.
     */
    public function genResumeMedisPage($sep, $pasien, $regPeriksa, $barcodeDPJP, $ttdPasien)
    {
        $resume    = \App\Models\ResumePasienRanap::where('no_rawat', $sep->no_rawat)->first();
        if ($resume) {
            $kamarInap = \App\Models\KamarInap::with('kamar.bangsal')->where('no_rawat', $sep->no_rawat)->where('stts_pulang', '<>', 'Pindah Kamar')->orderBy('tgl_masuk', 'desc')->orderBy('jam_masuk', 'desc')->get();
            $koor      = \App\Models\Pegawai::select('id', 'nik', 'nama', 'departemen')->whereHas('dep', function ($q) use ($kamarInap) {
                return $q->where('nama', \Illuminate\Support\Str::upper($this->getDepartemen($kamarInap)));
            })->where('status_koor', '1')->first();

            if ($koor) {
                $barcodeResume = SignHelper::rsia($koor->nama, $koor->id);
            } else {
                $barcodeResume = SignHelper::blankRsia();
            }

            $resumeMedis = view('berkas-klaim.resume', [
                'sep'         => $sep,
                'pasien'      => $pasien,
                'regPeriksa'  => $regPeriksa,
                'kamarInap'   => $kamarInap,
                'resume'      => $resume,
                'koor'        => $koor,
                'barcodeKoor' => $barcodeResume->getDataUri(),
                'barcodeDPJP' => $barcodeDPJP->getDataUri(),
                'ttdPasien'   => $ttdPasien,
            ])->render();

            return $resumeMedis;
        }
    }

    /**
     * Generate CPPT (Catatan Perkembangan Pasien Terintegrasi) document.
     *
     * @param string $jenisPelayanan The type of service.
     * @param \App\Models\RegPeriksa $regPeriksa The registration check model instance.
     * @param \App\Models\Pasien $pasien The patient model instance.
     * 
     * @return string|null The rendered CPPT document view, or null if no corresponding CPPT is found.
     */
    public function genCpptPage(string $jenisPelayanan, $regPeriksa, $pasien)
    {
        $no_rawat = \App\Models\RegPeriksa::select('no_rawat')
            ->where('no_rkm_medis', $regPeriksa->no_rkm_medis)
            ->whereBetween('tgl_registrasi', [\Carbon\Carbon::parse($regPeriksa->tgl_registrasi)->subDays(30), \Carbon\Carbon::parse($regPeriksa->tgl_registrasi)->addDays(30)])
            ->orderBy('tgl_registrasi', 'desc')
            ->get();

        $no_rawat = $no_rawat->pluck('no_rawat')->toArray();

        if ($jenisPelayanan == "2") {
            $cppt = \App\Models\PemeriksaanRalanKlaim::with('petugas')->whereIn('no_rawat', $no_rawat)->orderBy('tgl_perawatan', 'DESC')->get();
        } else if ($jenisPelayanan == "1") {
            $cppt = \App\Models\PemeriksaanRanapKlaim::with('petugas')->whereIn('no_rawat', $no_rawat)->orderBy('tgl_perawatan', 'DESC')->get();
        } else {
            return null;
        }

        if (!$cppt || $cppt->isEmpty()) {
            return null;
        }

        $cppt = view('berkas-klaim.cppt', [
            'regPeriksa' => $regPeriksa->withoutRelations(),
            'pasien'     => $pasien,
            'cppt'       => $cppt,
        ])->render();

        return $cppt;
    }

    /**
     * Generate Operasi document.
     *
     * @param string $no_rawat The registration number.
     * @param \App\Models\RegPeriksa $regPeriksa The registration check model instance.
     * @param \Endroid\QrCode\Writer\Result\PngResult $barcodeDPJP The DPJP barcode.
     * 
     * @return string|null The rendered operation document view, or null if no corresponding operation data is found.
     */
    public function genOperasiPage(string $no_rawat, $regPeriksa, $barcodeDPJP)
    {
        $operasiData = \App\Models\RsiaOperasiSafe::withAllRelations()->where('no_rawat', $no_rawat)->get();
        if ($operasiData) {
            $operasi = view('berkas-klaim.operasi', [
                'data'        => $operasiData,
                'regPeriksa'  => $regPeriksa,
                'barcodeDPJP' => $barcodeDPJP->getDataUri()
            ])->render();

            return $operasi;
        }
    }

    /**
     * Generate Surat Perintah Rawat Inap document.
     *
     * @param \App\Models\BridgingSep $sep The SEP (Surat Eligibilitas Peserta) model instance.
     * @param \App\Models\Pasien $pasien The patient model instance.
     * @param \Endroid\QrCode\Writer\Result\PngResult $barcodeDPJP The DPJP barcode.
     * 
     * @return string|null The rendered control plan letter view, or null if no corresponding Surat Kontrol is found
     */
    public function genSpriPage($sep, $pasien, $barcodeDPJP)
    {
        $spri = \App\Models\BridgingSuratPriBpjs::where('no_surat', $sep->noskdp)->first();
        if ($spri && $spri->no_surat) {
            $spri = view('berkas-klaim.spri', [
                'sep'           => (object) $sep->only(['tglsep', 'no_sep', 'no_kartu']),
                'pasien'        => $pasien,
                'spri'          => $spri,
                'barcodeDPJP'   => $barcodeDPJP->getDataUri(),
            ]);

            return $spri->render();
        }
    }

    /**
     * Generates a control plan letter (Surat Rencana Kontrol) for a patient.
     *
     * @param \App\Models\BridgingSep $sep The SEP (Health Insurance Participant Eligibility Letter) information.
     * @param \App\Models\RegPeriksa $regPeriksa The registration check information.
     * @param \App\Models\Pasien $pasien The patient information.
     * @param \Endroid\QrCode\Writer\Result\PngResult $barcodeDPJP The DPJP barcode.
     * 
     * @return string|null The rendered control plan letter view, or null if no corresponding Surat Kontrol is found.
     */
    public function genRencanaKontrolPage($sep, $regPeriksa, $pasien, $barcodeDPJP)
    {
        $srk = \App\Models\BridgingSuratKontrolBpjs::where('no_surat', $sep->noskdp)->first();
        if ($srk) {
            $kontrol = view('berkas-klaim.kontrol', [
                'sep'         => $sep,
                'pasien'      => $pasien,
                'regPeriksa'  => $regPeriksa,
                'srk'         => $srk,
                'barcodeDPJP' => $barcodeDPJP->getDataUri()
            ])->render();

            return $kontrol;
        }
    }

    /**
     * Generate the USG examination result for the claim document.
     *
     * @param \App\Models\BridgingSep $bSep The SEP (Surat Eligibilitas Peserta) model instance.
     * @param \App\Models\RegPeriksa $regPeriksa The registration check model instance.
     * @param \App\Models\Pasien $pasien The patient model instance.
     * @param \App\Models\Dokter $dpjp The DPJP model instance.
     * @param \Endroid\QrCode\Writer\Result\PngResult $barcodeDPJP The DPJP barcode model instance.
     * 
     * @return string|null The rendered USG examination result view, or null if no USG examination result is found.
     */
    public function genHasilPemeriksaanUsg($bSep, $regPeriksa, $pasien, $dpjp, $barcodeDPJP)
    {
        $catatanPerawatan = \App\Models\CatatanPerawatan::where('no_rawat', $regPeriksa->no_rawat)->first();
        if ($catatanPerawatan) {
            $catatan = view('berkas-klaim.hasil-usg', [
                'sep'        => $bSep,
                'pasien'     => $pasien,
                'regPeriksa' => $regPeriksa,
                'usg'        => $catatanPerawatan,
                'dpjp'       => $dpjp,
                'barcodeDPJP' => $barcodeDPJP->getDataUri(),
            ]);

            return $catatan;
        }
    }

    /**
     * Generate the radiology examination result for the claim document.
     *
     * @param \App\Models\BridgingSep $bSep The SEP (Surat Eligibilitas Peserta) model instance.
     * @param \App\Models\RegPeriksa $regPeriksa The registration check model instance.
     * @param \App\Models\Pasien $pasien The patient model instance.
     * @param \App\Models\Dokter $dpjp The DPJP model instance.
     * @param \Endroid\QrCode\Writer\Result\PngResult $barcodeDPJP The DPJP barcode model instance.
     * 
     * @return string|null The rendered radiology examination result view, or null if no radiology examination result is found.
     */
    public function genHasilRadiologiPage($regPeriksa, $pasien)
    {
        $radiologi = \App\Models\PeriksaRadiologi::where('no_rawat', $regPeriksa->no_rawat)
            ->with(['dokter', 'petugas', 'dokterPerujuk', 'hasilRadiologi', 'jenisPerawatan'])
            ->get();

        if ($radiologi) {
            $hasilRadiologi = view('berkas-klaim.radiologi', [
                'regPeriksa'  => $regPeriksa,
                'pasien'      => $pasien,
                'radiologi'   => $radiologi,
            ])->render();

            return $hasilRadiologi;
        }
    }

    /**
     * Retrieve and render supporting files based on the given category.
     *
     * @param \Illuminate\Database\Eloquent\Collection|null $data The data collection to search within.
     * @param string $ambil The category to filter the data by.
     * @return string|null The rendered view of supporting files or null if no data or files are found.
     */
    public function pendukung($data, array $ambil, bool $notIn = false)
    {
        if (!$data) {
            return null;
        }

        if ($notIn) {
            $pendukung = $data->whereNotIn('kategori', $ambil)->first();
        } else {
            $pendukung = $data->whereIn('kategori', $ambil)->first();
        }

        if (empty($pendukung)) {
            return null;
        }

        $files = explode(',', $pendukung->file);
        $pendukung = view('berkas-klaim.image', [
            'files' => $files,
        ])->render();

        return $pendukung;
    }

    /**
     * Generate the laboratory examination result for the claim document.
     *
     * @param \App\Models\BridgingSep $sep The SEP (Surat Eligibilitas Peserta) model instance.
     * @param \App\Models\RegPeriksa $regPeriksa The registration check model instance.
     * @param \App\Models\Pasien $pasien The patient model instance.
     * 
     * @return string|null The rendered laboratory examination result view, or null if no laboratory examination result is found.
     */
    public function genHasilLabPage($sep, $regPeriksa, $pasien)
    {
        $labWih  = ['perujuk', 'jenisPerawatan', 'detailPeriksaLab.template', 'pegawai', 'dokter'];
        $labData = \App\Models\PeriksaLab::with($labWih)->whereIn('no_rawat', $this->getRegisterLabDouble($regPeriksa->kd_poli, $sep->no_rawat, $sep->nomr))->orderBy('tgl_periksa', 'DESC')->orderBy('jam', 'DESC')->get();
        $labs    = $this->groupPeriksaLabData($labData);

        $html = view('berkas-klaim.hasil-lab', [
            'sep'        => $sep,
            'regPeriksa' => $regPeriksa,
            'pasien'     => $pasien,
            'labs'       => $labs,
        ])->render();

        return $html;
    }

    /**
     * Get the register lab double
     *
     * @param string $kd_poli
     * @param string $no_rawat
     * @param string $no_rkm_medis
     * @return array
     */
    private function getRegisterLabDouble($kd_poli, $no_rawat, $no_rkm_medis)
    {
        // Filter kd_poli
        $filterPoli = ['U0016', 'OPE'];

        // Check if the kd_poli is in the filter
        if (in_array($kd_poli, $filterPoli)) {
            $registrasiData = \App\Models\RegPeriksa::where('no_rkm_medis', $no_rkm_medis)
                ->where('no_rawat', '<=', $no_rawat)
                ->orderBy('no_rawat', 'desc')->limit(2)
                ->pluck('no_rawat');

            return $registrasiData->toArray();
        }

        // Return the no_rawat if not in the filter
        return [$no_rawat];
    }

    /**
     * Group periksa lab data
     *
     * Group the periksa lab data by tgl_periksa and jam
     *
     * @param \Illuminate\Support\Collection $data
     * @return \Illuminate\Support\Collection
     */
    private function groupPeriksaLabData(Collection $data): Collection
    {
        return $data->groupBy(function ($item) {
            return $item->tgl_periksa . ' ' . $item->jam;
        });
    }

    /**
     * Generate INACBG report
     *
     * @param string $sep
     * @return string
     */
    public function genInacbgReportPage($sep)
    {
        \Halim\EKlaim\Builders\BodyBuilder::setMetadata('claim_print');
        \Halim\EKlaim\Builders\BodyBuilder::setData([
            "nomor_sep" => $sep,
        ]);

        $response = \Halim\EKlaim\Services\EklaimService::send(\Halim\EKlaim\Builders\BodyBuilder::prepared());

        if ($response->getStatusCode() == 200) {
            $resp = $response->getData();

            if (!$resp->data) {
                \Illuminate\Support\Facades\Log::channel(config('eklaim.log_channel'))->error('Berkas Klaim Print - Failed to generate INACBG report', [
                    'sep'      => $sep,
                    'response' => $resp,
                ]);

                return null;
            }

            return base64_decode($resp->data);
        }

        \Illuminate\Support\Facades\Log::channel(config('eklaim.log_channel'))->error('Berkas Klaim Print - Failed to generate INACBG report', [
            'sep'      => $sep,
            'response' => $response->getData(),
        ]);

        return null;
    }

    /**
     * Generate kwitansi naik kelas page
     *
     * @param \App\Models\BridgingSep $bSep
     * @param \App\Models\RegPeriksa $regPeriksa
     * @param \App\Models\Pasien $pasien
     * @param \App\Models\RsiaVerifSep $ttdPasien
     * @return string|null
     */
    public function genKwitansiNaikKelasPage($bSep, $regPeriksa, $pasien, $ttdPasien)
    {
        $naikKelas = \App\Models\RsiaNaikKelas::where('no_sep', $bSep->no_sep)->first();
        if ($naikKelas) {
            $kamarInap = \App\Models\KamarInap::with('kamar.bangsal')->where('no_rawat', $bSep->no_rawat)->where('stts_pulang', '<>', 'Pindah Kamar')->orderBy('tgl_masuk', 'desc')->orderBy('jam_masuk', 'desc')->get();
            $kasir = \App\Helpers\JurnalHelper::determinePetugas($bSep->no_rawat);

            $kwitansi = view('berkas-klaim.naik-kelas', [
                'sep'        => $bSep,
                'regPeriksa' => $regPeriksa,
                'pasien'     => $pasien,
                'naikKelas'  => $naikKelas,
                'kamarInap'  => $kamarInap,
                'ttdPasien'  => $ttdPasien,
                'kasir'      => $kasir,
            ]);

            return $kwitansi->render();
        }
    }

    public function genResepObatPage($regPeriksa, $dpjp, $pasien)
    {
        $detailObatHtml = [];
        $html = "";

        $obat = $this->groupDetailPemberianObat(\App\Models\DetailPemberianObat::select('tgl_perawatan', 'jam', 'no_rawat', 'kode_brng', 'jml')->with('obat')->whereIn('no_rawat', $this->cekGabung($regPeriksa->no_rawat))->get());
        $regPeriksa = $regPeriksa->whereIn('no_rawat', array_keys($obat->toArray()))->get()->keyBy('no_rawat');

        foreach ($obat as $key => $value) {
            foreach ($value as $sk => $sv) {
                $detailObatHtml[$key] = view('berkas-klaim.obat', [
                    'obat' => $value,
                ])->render();
            }
        }

        $html = "";

        foreach ($detailObatHtml as $key => $value) {
            $registrasi = $regPeriksa->get($key);

            $html .= view('berkas-klaim.partials.header.obat-header', [
                'regPeriksa' => $registrasi,
            ])->render();

            $html .= $value;

            if ($key != $regPeriksa->last()->no_rawat) {
                $html .= "<div style='page-break-after: always;'></div>";
            }

            if ($key == $regPeriksa->last()->no_rawat) {
                $html .= "</div>";
            }
        }

        return $html;
    }

    public function genBillingPage($regPeriksa, $dpjp, $pasien)
    {
        $noRawatGabung = $this->cekGabung($regPeriksa->no_rawat);

        $bData         = $this->billingData($regPeriksa->no_rawat, $regPeriksa->status_lanjut);
        $kasir         = \App\Helpers\JurnalHelper::determinePetugas($regPeriksa->no_rawat);
        $asmenKeuangan = \App\Models\Pegawai::select('id', 'nik', 'nama', 'jnj_jabatan')->where('jnj_jabatan', 'RS7')->first();

        if (Str::lower($regPeriksa->status_lanjut) == 'ranap') {
            // $resepPulang = \App\Models\ResepPulang::with('obat')->where('no_rawat', $regPeriksa->no_rawat)->get()->groupBy('kode_brng');
            $resepPulang = \App\Models\ResepPulang::with('obat')->whereIn('no_rawat', $noRawatGabung)->get()->groupBy('no_rawat');
            $resepPulang = $resepPulang->map(function ($grouped) {
                return $grouped->groupBy('kode_brng');
            });

            $ruang       = \App\Models\KamarInap::with(['kamar.bangsal'])->where('no_rawat', $regPeriksa->no_rawat)->orderBy('tgl_masuk', 'desc')->orderBy('jam_masuk', 'desc')->get();
        }

        if (Str::lower($regPeriksa->status_lanjut) == 'ralan') {
            $nota = \App\Models\NotaJalan::where('no_rawat', $regPeriksa->no_rawat)->first();
        } else if (Str::lower($regPeriksa->status_lanjut) == 'ranap') {
            $dokters = \App\Models\RawatInapDr::with('dokter.spesialis')->where('no_rawat', $regPeriksa->no_rawat)->get()->groupBy('kd_dokter');
            $nota    = \App\Models\NotaInap::where('no_rawat', $regPeriksa->no_rawat)->first();
        }

        if (isset($dokters)) {
            $dokters = $dokters->sortBy('dokter.nm_dokter');
        }

        $returObat     = \App\Models\DetReturJual::with('obat')->where('no_retur_jual', 'like', "%$regPeriksa->no_rawat%")->get()->groupBy("kode_brng");
        $tambahanBiaya = \App\Models\TambahanBiaya::where('no_rawat', $regPeriksa->no_rawat)->orderBy('nama_biaya', 'desc')->get();
        $potonganBiaya = \App\Models\PenguranganBiaya::where('no_rawat', $regPeriksa->no_rawat)->orderBy('nama_pengurangan', 'desc')->get();

        $billing = view('berkas-klaim.billing', [
            'dpjp'          => $dpjp,
            'kasir'         => $kasir,
            'billing'       => $bData,
            'pasien'        => $pasien,
            'regPeriksa'    => $regPeriksa,
            'nota'          => $nota ?? null,
            'ruang'         => $ruang ?? null,
            'dokters'       => $dokters ?? null,
            'resepPulang'   => $resepPulang ?? null,

            'returObat'     => $returObat,
            'tambahanBiaya' => $tambahanBiaya,
            'potonganBiaya' => $potonganBiaya,
            'asmenKeuangan' => $asmenKeuangan,
        ])->render();

        return $billing;
    }


    // ====================================================================================================

    /**
     * Cek gabung
     *
     * Cek pasien rawat gabung atau tidak
     *
     * @param string $no_rawat
     * @return array
     */
    private function cekGabung($no_rawat)
    {
        $ranapGabung = \App\Models\RanapGabung::where('no_rawat', $no_rawat)->first();

        if ($ranapGabung) {
            $ranapGabung = array_values($ranapGabung->toArray());
            return $ranapGabung;
        }

        return [$no_rawat];
    }

    /**
     * Group detail pemberian obat data
     *
     * Group the periksa lab data by tgl_perawatan and jam
     *
     * @param \Illuminate\Support\Collection $data
     * @return \Illuminate\Support\Collection
     */
    private function groupDetailPemberianObat(Collection $data): Collection
    {
        return $data->groupBy('no_rawat')->map(function ($grouped) {
            return $grouped->groupBy(function ($item) {
                return $item->tgl_perawatan . ' ' . $item->jam;
            });
        });
    }

    /**
     * Get the department based on the given room information.
     *
     * This method checks if the provided room information is empty. If not, it filters
     * the keys of the $this->koorByDepartemen array to find a match with the room code.
     * It then returns the corresponding department value.
     *
     * @param \App\Models\KamarInap $kamarInap An array of room information objects.
     * @return mixed|null The department value if found, or null if not found or if the input is empty.
     */
    private function getDepartemen($kamarInap)
    {
        if ($kamarInap->isEmpty()) {
            return null;
        }

        $filteredKeys = array_filter(array_keys($this->koorByDepartemen), function ($key) use ($kamarInap) {
            return strpos($kamarInap[0]->kd_kamar, $key) != false;
        });

        $values = array_values(array_intersect_key($this->koorByDepartemen, array_flip($filteredKeys)));

        return $values[0] ?? null; // Return the first value or null if empty
    }

    public function billingData(string $no_rawat, string $statusLanjut)
    {
        $tarif = [];
        $cekGabung = \App\Models\RanapGabung::where('no_rawat', $no_rawat)->first();

        $tarif[$no_rawat] = $this->getTarif($no_rawat, $statusLanjut);

        if ($cekGabung) {
            $sttsLanjut = \App\Models\RegPeriksa::select('status_lanjut')->where('no_rawat', $cekGabung->no_rawat2)->first();
            $tarif[$cekGabung->no_rawat2] = $this->getTarif($cekGabung->no_rawat2, $sttsLanjut->status_lanjut);
        }

        return $tarif;
    }

    public function getTarif(string $no_rawat, string $statusLanjut)
    {
        // Define common query parameters
        $baseQuery = [
            'select' => ['no_rawat', 'kd_jenis_prw', 'biaya_rawat'],
            'with' => [
                'jenisPerawatan' => function ($q) {
                    $q->select(
                        DB::raw('TRIM(kd_jenis_prw) as kd_jenis_prw'),
                        DB::raw('TRIM(nm_perawatan) as nm_perawatan'),
                        DB::raw('TRIM(kd_kategori) as kd_kategori')
                    )->with(['kategori' => function ($qq) {
                        $qq->select(
                            DB::raw('TRIM(kd_kategori) as kd_kategori'),
                            DB::raw('TRIM(nm_kategori) as nm_kategori')
                        );
                    }]);
                }
            ],
            'where' => ['no_rawat' => $no_rawat]
        ];

        // Define models to query
        $models = [
            \App\Models\RawatInapPr::class,
            \App\Models\RawatInapDr::class,
            \App\Models\RawatInapDrPr::class,
            \App\Models\RawatJalanPr::class,
            \App\Models\RawatJalanDr::class,
            \App\Models\RawatJalanDrPr::class
        ];

        // Process all models in a single loop
        $rawatData = collect($models)->map(function ($model) use ($baseQuery) {
            $mappedSelect = array_map(function ($item) {
                return DB::raw("TRIM($item) as $item");
            }, $baseQuery['select']);

            return $model::select($mappedSelect)
                ->with($baseQuery['with'])
                ->where($baseQuery['where'])
                ->get()
                ->groupBy(function ($item) {
                    return $item->jenisPerawatan->kategori->nm_kategori;
                })
                ->map(function ($group) {
                    return $group->groupBy(function ($item) {
                        return $item->jenisPerawatan->nm_perawatan;
                    });
                });
        })->filter();

        // Initialize base data
        $mergedData = collect([
            "Pemeriksaan Lab" => $this->getTarifPeriksaLab($no_rawat),
            "Pemeriksaan Radiologi" => $this->getTarifPeriksaRadiologi($no_rawat),
            "Obat dan BHP" => $this->getTarifObatDanBhp($no_rawat),
            "Operasi" => Str::lower($statusLanjut) == 'ranap'
                ? (new \App\Http\Resources\Pasien\Tarif\TarifOperasi($no_rawat))->toArray(new Request(), false)
                : null
        ]);

        // Merge rawat data
        foreach ($rawatData as $data) {
            foreach ($data as $kategori => $items) {
                if ($mergedData->has($kategori)) {
                    $mergedData[$kategori] = $mergedData[$kategori]->mergeRecursive($items);
                } else {
                    $mergedData[$kategori] = $items;
                }
            }
        }

        return $mergedData->filter(function ($item) {
            return $item && !$item->isEmpty();
        })->sortKeys();
    }

    private function getTarifPeriksaLab(string $no_rawat)
    {
        return \App\Models\PeriksaLab::with('jenisPerawatan')
            ->where('no_rawat', $no_rawat)
            ->get()->groupBy('kd_jenis_prw');
    }

    private function getTarifPeriksaRadiologi(string $no_rawat)
    {
        return \App\Models\PeriksaRadiologi::with('jenisPerawatan')
            ->where('no_rawat', $no_rawat)
            ->get()->groupBy('kd_jenis_prw');
    }

    private function getTarifObatDanBhp(string $no_rawat)
    {
        return \App\Models\DetailPemberianObat::with('obat')
            ->where('jml', '<>', 0)
            ->where('no_rawat', $no_rawat)
            ->get()->groupBy(function ($q) {
                return $q->obat->nama_brng;
            })->sortKeys();
    }
}
