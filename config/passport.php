<?php
return [
    'scopes' => [
        // Berkas dan Surat
        'surat:access'              => 'Mengakses Surat Internal, Eksternal, dan Masuk',
        'surat:self-manage'         => 'Mengelola Surat Internal, Eksternal, dan Masuk Milik Sendiri',
        'surat:manage'              => 'Mengelola Surat Internal, Eksternal, dan Masuk',

        'berkas:access'             => 'Mengakses Berkas PKS, SK, IHT, Radiologi, dan SPO',
        'berkas:self-manage'        => 'Mengelola Berkas PKS, SK, IHT, Radiologi, dan SPO Milik Sendiri',
        'berkas:manage'             => 'Mengelola Berkas PKS, SK, IHT, Radiologi, dan SPO',

        'berkas-komite:access'      => 'Mengakses Berkas Komite',
        'berkas-komite:self-manage' => 'Mengelola Berkas Komite Milik Sendiri',
        'berkas-komite:manage'      => 'Mengelola Berkas Komite',

        // Undangan
        'undangan:access'           => 'Mengakses Undangan Rapat',
        'undangan:self-manage'      => 'Mengelola Undangan Rapat Milik Sendiri',
        'undangan:manage'           => 'Mengelola Undangan Rapat',

        // Bridginig SEP
        'sep:manage'                => 'Mengelola Data SEP',

        // Klaim
        'klaim:download'            => 'Download Berkas Klaim',
        'klaim:manage'              => 'Mengelola Data Klaim',
        'klaim:ws'                  => 'Melakukan Aktifitas Klaim Sesuai Web Service',

        // Bupel
        'bupel:manage'              => 'Mengelola Data Bulan Pelayanan',

        // Pegawai
        'pegawai:manage'            => 'Mengelola Data Pegawai',
        'pegawai:berkas'            => 'Mengelola Berkas Pegawai',
        'pegawai:jasa'              => 'Melihat Jasa Medis dan Pelayanan Pegawai',
        'pegawai:presensi'          => 'Melihat Presensi Pegawai',

        // Undangan
        'undangan:manage'           => 'Mengelola Data Undangan',
        'undangan:kehadiran'        => 'Mengelola Kehadiran Rapat',

        // Departemen
        'departemen:manage'         => 'Mengelola Data Departemen',

        // Dokter
        'dokter:manage'             => 'Mengelola Data Dokter',

        // Jadwal
        'jadwal:manage'             => 'Mengelola Jadwal Dokter',

        // Kamar Inap
        'kamar-inap:manage'         => 'Mengelola Data Kamar Inap',

        // Pasien
        'pasien:manage'             => 'Mengelola Data Pasien',
        'pasien:history'            => 'Melihat Riwayat Pasien Rawat Inap',
        'pasien:pemeriksaan'        => 'Melihat Riwayat Pemeriksaan Pasien',
        'pasien:cost-billing'       => 'Melihat Biaya dan billing Pasien',
        'pasien-ranap:manage'       => 'Mengelola Data Pasien Rawat Inap',
        'pasien-ralan:manage'       => 'Mengelola Data Pasien Rawat Jalan',

        // Poliklinik
        'poliklinik:manage'         => 'Mengelola Data Poliklinik',
    ],
];
