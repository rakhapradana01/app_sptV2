# Dokumentasi Business Flow Aplikasi Pengelolaan SPT, SPPD, dan SPJ

Dokumen ini menyajikan analisis menyeluruh terhadap alur bisnis (*business flow*), aturan validasi (*business logic*), hierarki otorisasi (*role & data scoping*), dan integrasi antar-modul yang telah diimplementasikan di dalam source code aplikasi.

---

## Daftar Isi
1. [Gambaran Umum Sistem & Arsitektur](#1-gambaran-umum-sistem--arsitektur)
2. [Hierarki Organisasi & Matriks Peran (Role-Based Access Control)](#2-hierarki-organisasi--matriks-peran-role-based-access-control)
3. [Manajemen Master Data](#3-manajemen-master-data)
4. [Alur Bisnis Nota Dinas (Tanpa Persetujuan Digital)](#4-alur-bisnis-nota-dinas-tanpa-persetujuan-digital)
5. [Alur Pengarsipan Dokumen (Arsip)](#5-alur-pengarsipan-dokumen-arsip)
6. [Alur Bisnis Surat Perintah Tugas (SPT) Tanpa Paraf Digital](#6-alur-bisnis-surat-perintah-tugas-spt-tanpa-paraf-digital)
7. [Alur Bisnis Surat Perintah Perjalanan Dinas (SPPD)](#7-alur-bisnis-surat-perintah-perjalanan-dinas-sppd)
8. [Alur Bisnis SPJ & Rekapitulasi Biaya Riil](#8-alur-bisnis-spj--rekapitulasi-biaya-riil)
9. [Alur Monitoring dan Evaluasi (Monev) Anggaran](#9-alur-monitoring-dan-evaluasi-monev-anggaran)
10. [Alur Dashboard, Statistik, & Rekapitulasi Tahunan](#10-alur-dashboard-statistik--rekapitulasi-tahunan)
11. [Diagram Alur End-to-End (Mermaid)](#11-diagram-alur-end-to-end-mermaid)

---

## 1. Gambaran Umum Sistem & Arsitektur

Aplikasi dirancang untuk mengotomatisasi siklus hidup administrasi perjalanan dinas pada instansi pemerintah daerah (khususnya Bidang Anggaran / BPKAD), mulai dari:
1. Pembuatan Nota Dinas Perjalanan Dinas beserta personil, tanpa alur persetujuan/disposisi digital.
2. Penerbitan Surat Perintah Tugas (SPT) dan Surat Perintah Perjalanan Dinas (SPPD) yang dicetak untuk ditandatangani secara manual.
3. Pengelolaan Surat Pertanggungjawaban (SPJ) pengeluaran riil (tiket, transport, uang harian, dan penginapan).
4. Monitoring dan evaluasi realisasi pagu anggaran per sub-kegiatan dan target Orang-Kegiatan (OK).
5. Pencetakan dokumen resmi berbasis PDF berstandar dan ekspor berkas kuitansi SPJ format Excel multi-sheet.

### Komponen Teknologi Utama
- **Backend Framework**: Laravel 11.x (PHP >= 8.2)
- **Database**: MySQL / MariaDB dengan relasi Eloquent ORM
- **Export PDF**: `barryvdh/laravel-dompdf` (DomPDF)
- **Export & Import Excel**: `phpoffice/phpspreadsheet`
- **Frontend Layer**: Blade Templating, TailwindCSS (TailAdmin Theme), Alpine.js

---

## 2. Hierarki Organisasi & Matriks Peran (Role-Based Access Control)

Sistem menerapkan prinsip *Multi-Tenancy Organisasi Berjenjang* dan *Row-Level Data Scoping* berdasarkan struktur:
$$\text{Dinas (Instansi)} \longrightarrow \text{Bidang} \longrightarrow \text{Sub Bidang}$$

### 2.1. Daftar Peran (Roles)
Terdapat 7 peran pengguna yang terdaftar di dalam sistem (`roles` table):
1. **`super_admin`**: Administrator sistem level tertinggi dengan akses tak terbatas lintas dinas, bidang, dan konfigurasi master data.
2. **`admin`**: Administrator level dinas/bidang yang mengelola master pegawai, akun pengguna di dinasnya, dan sub kegiatan.
3. **`kepala_badan`** (Kaban): Pimpinan dinas/instansi tertinggi yang menandatangani dokumen dinas secara manual.
4. **`sekretaris_badan`** (Sekban): Pejabat vertikal yang membubuhkan paraf basah pada dokumen cetak.
5. **`kepala_bidang`** (Kabid): Pimpinan tingkat bidang yang membubuhkan paraf basah dan mengawasi sub-kegiatan di bawah bidangnya.
6. **`kepala_sub_bidang`** (Kasubid): Pemimpin operasional sub-bidang / PPTK yang membuat nota dinas, mengelola uraian pagu, dan membubuhkan paraf basah.
7. **`user`**: Staf umum pelaksana perjalanan dinas.

### 2.2. Row-Level Data Scoping Rules
Setiap query data operasional disaring secara dinamis berdasarkan atribut pengguna yang sedang login:
- **Pengguna `super_admin`**: Tanpa batasan scope (`whereRaw` tidak diterapkan).
- **Pengguna `kepala_badan` & `sekretaris_badan`**: Dibatasi oleh `dinas_id` pengguna (`where('dinas_id', $user->dinas_id)`).
- **Pengguna `kepala_bidang` & `admin`**: Dibatasi oleh `bidang_id` pengguna (`where('bidang_id', $user->bidang_id)`).
- **Pengguna `kepala_sub_bidang`**: Dibatasi oleh `sub_bidang_id` pengguna (`where('sub_bidang_id', $user->sub_bidang_id)`). Jika akun belum dipetakan ke `sub_bidang_id`, query mengembalikan hasil kosong (`whereRaw('1 = 0')`).

### 2.3. Matriks Hak Akses Modul

| Modul / Fitur | Super Admin | Admin | Kasubid | Kabid | Sekban | Kaban | User |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **Dashboard & Rekap** | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | - |
| **Master Dinas & Bidang** | ✓ | - | - | - | - | - | - |
| **Master Pegawai & Akun** | ✓ | ✓ | - | - | - | - | - |
| **Master Sub-Kegiatan** | ✓ | ✓ | ✓ | - | - | - | ✓ |
| **Nota Dinas (Buat)** | ✓ | - | ✓ | - | - | - | - |
| **Nota Dinas (Edit/Hapus)** | ✓ | ✓ | ✓ | ✓ | - | - | - |
| **Cetak PDF Nota Dinas (TTD basah)** | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | - |
| **Arsip Dokumen & Cetak PDF** | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | - |
| **SPT: Lihat & Cetak PDF** | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| **SPT: Buat (via Nota / Mandiri)** | ✓ | - | ✓ | - | - | - | - |
| **Paraf & Tanda Tangan SPT** | manual | manual | manual | manual | manual | manual | manual |
| **SPPD (Buat & Cetak PDF)** | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| **SPJ (Input Biaya & Export XLS)** | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| **Monev & Uraian Anggaran** | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | - |

---

## 3. Manajemen Master Data

### 3.1. Struktur Institusi (Dinas, Bidang, Sub-Bidang)
- **Controller**: [DinasController](file:///d:/Rakha/app-spt/app/Http/Controllers/DinasController.php), [BidangController](file:///d:/Rakha/app-spt/app/Http/Controllers/BidangController.php), [SubBidangController](file:///d:/Rakha/app-spt/app/Http/Controllers/SubBidangController.php)
- **Fitur Utama**:
  - CRUD master dinas, bidang, dan sub-bidang.
  - Unduh template Excel (`.xlsx`) dan impor masal Dinas dengan validasi duplikasi per baris.
  - Endpoint AJAX dinamis (`/api/bidang/{dinas_id}`) untuk cascading dropdown form pegawai/akun.

### 3.2. Master Pegawai
- **Controller**: [PegawaiController](file:///d:/Rakha/app-spt/app/Http/Controllers/PegawaiController.php)
- **Model**: [Pegawai](file:///d:/Rakha/app-spt/app/Models/Pegawai.php)
- **Atribut**: `nama`, `nip` (unik), `pangkat`, `jabatan`, `dinas_id`, `bidang_id`.
- **Fitur**:
  - Impor berkas Excel (`/pegawai/import`) dan unduh template resmi.
  - Auto-scoping: Admin hanya dapat menambahkan pegawai ke dinas dan bidang yang sesuai dengan profil akunnya.

### 3.3. Master Pengguna & Tanda Tangan Digital (Signature)
- **Controller**: [UserController](file:///d:/Rakha/app-spt/app/Http/Controllers/UserController.php), [AuthController](file:///d:/Rakha/app-spt/app/Http/Controllers/AuthController.php)
- **Model**: [User](file:///d:/Rakha/app-spt/app/Models/User.php)
- **Autentikasi Bertingkat**:
  - Pengguna umum wajib memilih `dinas` saat login agar akun terisolasi sesuai instansi kerjanya.
  - Khusus pengguna khusus `superadmin00`, seleksi dinas dilewati untuk kemudahan administrasi global.
- **Tanda Tangan & Paraf**:
  - Admin/User masih dapat mengunggah file tanda tangan/paraf (`signature_path`) sebagai data profil pada menu akun/pengguna.
  - Berkas tanda tangan **tidak lagi dipakai** untuk persetujuan atau paraf digital pada Nota Dinas dan SPT. Seluruh tanda tangan dan paraf dilakukan secara manual (basah) pada dokumen PDF yang telah dicetak.

### 3.4. Master Sub-Kegiatan & PPTK
- **Controller**: [SubKegiatanController](file:///d:/Rakha/app-spt/app/Http/Controllers/SubKegiatanController.php)
- **Model**: [SubKegiatan](file:///d:/Rakha/app-spt/app/Models/SubKegiatan.php)
- **Logika Otomatisasi PPTK**:
  - Sub-kegiatan dihubungkan dengan Pejabat Pelaksana Teknis Kegiatan (PPTK).
  - Sistem memiliki logika penentuan otomatis (`getPptkNamaAttribute()`): jika terdapat `sub_bidang_id`, sistem secara cerdas mencari akun pengguna dengan role `kepala_sub_bidang` pada sub-bidang tersebut untuk dijadikan PPTK secara otomatis.

---

## 4. Alur Bisnis Nota Dinas (Tanpa Persetujuan Digital)

Nota Dinas merupakan pintu masuk utama pengajuan perjalanan dinas dalam alur reguler. Sejak perubahan alur bisnis, **tidak ada lagi proses persetujuan, ACC, penolakan, revisi, atau disposisi digital** pada modul ini. Nota Dinas hanya mendukung pembuatan, penyimpanan/pengelolaan, pencetakan PDF, dan pengarsipan.

```mermaid
stateDiagram-v2
    [*] --> TERSIMPAN : Buat & Simpan Nota Dinas
    TERSIMPAN --> TERSIMPAN : Edit / Perbarui Data
    TERSIMPAN --> TERSIMPAN : Cetak PDF (tanda tangan basah)
    TERSIMPAN --> TERSIMPAN : Terbitkan SPT / SPPD Terhubung
    TERSIMPAN --> ARSIP : Dokumen Masuk Daftar Arsip
    TERSIMPAN --> [*] : Hapus oleh Role Berwenang
```

### 4.1. Tahap Pembuatan (Create & Store)
1. **Pembuat Nota** (Kasubid/Super Admin melalui menu) memilih:
   - Sub-Kegiatan (hanya yang dalam lingkup sub-bidang/bidang user).
   - Pejabat Penerima (`kepada_id`), Pengirim (`dari_id`), dan Perantara (`melalui_id`).
   - Perihal, Lokasi Tujuan, Jenis Perjalanan (`dalam_daerah` atau `luar_daerah`).
   - Rentang Tanggal (`tanggal_mulai` s.d. `tanggal_selesai`).
   - Personil yang ditugaskan (`pegawai_ids[]` disimpan ke tabel pivot `nota_dinas_pegawai`).
   - Lampiran, Sifat Surat, Asal Undangan, dan Uraian Kegiatan.
2. Nota langsung tersimpan dengan status netral `tersimpan` (`NotaDinas::STATUS_TERSIMPAN`). Tidak ada status antrean persetujuan.
3. Personil dapat ditambah/dikelola melalui halaman pratinjau selama nota masih dalam pengelolaan pembuatnya.

### 4.2. Tahap Pengelolaan (Edit & Hapus)
- **Edit (`update`)**: Nota dapat diperbarui oleh pembuat dan role berwenang (`super_admin`, `admin`, `kepala_bidang`, serta `kepala_sub_bidang` pada sub-bidangnya). Setiap penyimpanan mengembalikan status ke `tersimpan` dan mengosongkan kolom `revisi` lama.
- **Hapus (`destroy`)**: Hanya tersedia untuk role yang sama dengan hak edit di atas. Relasi personil dilepas sebelum data nota dihapus.
- Tidak ada pembatasan “hanya boleh diedit saat status tertentu”; nota yang sudah tercetak maupun sudah memiliki SPT/SPPD tetap dapat dikelola sesuai hak akses.

### 4.3. Tahap Cetak PDF & Tanda Tangan Manual
- **Preview Halaman** (`/nota-dinas/{nota}/preview`): Menampilkan ringkasan nota, grouping daftar personil berdasarkan jabatan, serta tombol **Cetak PDF**. Tidak ada panel persetujuan atau riwayat disposisi.
- **Cetak PDF Resmi** (`/nota-dinas/{nota}/cetak`): Menghasilkan lembar PDF format A4 Portrait berisi format surat dinas resmi dan daftar personil.
- Di bagian bawah PDF terdapat **tiga kotak disposisi kosong** yang disediakan untuk tanda tangan/paraf basah:
  1. `KEPALA BIDANG PAD.`
  2. `SEKRETARIS BPKAD.`
  3. `KEPALA BPKAD.`
- Kotak tersebut **tidak diisi otomatis** dari database. Teks disposisi, status persetujuan, maupun tanda tangan digital tidak dicetak.

### 4.4. Hubungan dengan SPT, SPPD, dan Arsip
- Nota Dinas dapat langsung dijadikan dasar pembuatan **SPT terhubung** tanpa menunggu persetujuan siapa pun.
- Nota Dinas juga dapat dijadikan dasar pembuatan **SPPD terhubung**.
- Semua Nota Dinas yang tersimpan otomatis tampil pada modul **Arsip** sesuai cakupan data pengguna.

### 4.5. Kompatibilitas Data Historis
- Konstanta status lama (mis. `diajukan_kabid`, `disetujui_kaban`) dan kolom disposisi lama **tetap dipertahankan** di database agar data historis tidak rusak.
- Nilai lama tersebut hanya dibaca sebagai arsip; aplikasi tidak lagi menampilkan tombol maupun alur approval untuknya.

---

## 5. Alur Pengarsipan Dokumen (Arsip)

Modul Arsip (`/arsip`) bertindak sebagai pusat repositori dan *action hub* untuk dokumen Nota Dinas dan SPT.

### 5.1. Cakupan Dokumen pada Arsip
Arsip **tidak lagi memfilter berdasarkan status persetujuan**. Seluruh Nota Dinas dan SPT yang tersimpan ditampilkan, dengan pembatasan cakupan data sesuai peran pengguna:
- `kepala_sub_bidang`: hanya data pada `sub_bidang_id` miliknya (jika belum dipetakan, hasil kosong).
- `kepala_bidang`, `admin`, `user`: hanya data pada `bidang_id` miliknya.
- `kepala_badan`, `sekretaris_badan`, `super_admin`: dapat melihat data lintas unit sesuai aturan masing-masing.

### 5.2. Aksi Cepat (Quick Actions) dari Arsip
Dari satu baris dokumen terarsip, pengguna berwenang dapat langsung:
1. **Mencetak Nota Dinas PDF** untuk ditandatangani manual.
2. **Membuat SPT Terhubung**: Membuka modal input Nomor SPT dan Jenis Anggaran (DPA/DPPA) yang langsung terikat dengan nota dinas tersebut.
3. **Mencetak SPT PDF**: Mengunduh PDF SPT yang telah terisi data personil dan jadwal, dengan kolom paraf kosong untuk diisi manual.
4. **Membuat SPPD Terhubung**: Membuka form SPPD yang otomatis mewarisi parameter nota dinas.
5. **Mencetak SPPD PDF**: Mengunduh berkas fisik SPPD resmi.
6. **Mencetak SPT Mandiri**: Mengunduh PDF SPT standalone beserta kolom paraf manual.
7. **Menghapus SPT Mandiri** sesuai hak akses.

---

## 6. Alur Bisnis Surat Perintah Tugas (SPT) Tanpa Paraf Digital

Aplikasi menyediakan **dua jalur independen** dalam pengelolaan SPT. Keduanya berakhir pada dokumen berstatus `tersimpan` yang siap dicetak dan ditandatangani secara manual.

```mermaid
flowchart TD
    subgraph Jalur_1["Jalur 1: Integrasi via Nota Dinas"]
        A[Nota Dinas Dibuat & Tersimpan] --> B[Input Nomor SPT & Jenis Anggaran di Arsip]
        B --> C[SPT Tersimpan Terhubung dengan Nota Dinas]
        C --> D[Cetak PDF SPT]
    end

    subgraph Jalur_2["Jalur 2: Standalone / SPT Mandiri"]
        E[Form Buat SPT Mandiri] --> F[Pilih Pegawai & Sub Kegiatan]
        F --> G[SPT Tersimpan]
        G --> H[Cetak PDF Standalone]
    end

    D --> I[Paraf & Tanda Tangan Manual]
    H --> I
    I --> J[Lanjut ke Pembuatan SPPD / SPJ]
```

### 6.1. Jalur 1: SPT via Nota Dinas (Alur Standar)
- **Controller**: [SPTController@store](file:///d:/Rakha/app-spt/app/Http/Controllers/SPTController.php#L32)
- **Mekanisme**:
  - Menggunakan metode `Spt::updateOrCreate(['nota_dinas_id' => $nota_id])`.
  - Nota Dinas yang dapat dipilih adalah **semua nota yang tersimpan** sesuai cakupan data pengguna — tidak ada filter status persetujuan.
  - Data penugasan (lokasi, tanggal mulai/selesai, uraian tugas, personil) mengacu langsung secara dinamis ke relasi `notaDinas`.
  - SPT disimpan dengan `status = tersimpan` (`Spt::STATUS_TERSIMPAN`).
  - Dokumen dicetak melalui rute `/spt/cetak/{id}`.

### 6.2. Jalur 2: SPT Mandiri (Standalone)
Fitur ini disediakan untuk penerbitan SPT cepat tanpa membuat nota dinas lengkap terlebih dahulu.

- **Pembuatan SPT Mandiri (`storeMandiri`)**:
  - Input: Nomor SPT, Jenis Anggaran (DPA/DPPA), Tahun Anggaran, Tanggal Mulai & Selesai, Lokasi, Kegiatan, Sub-Kegiatan, dan daftar `pegawai_ids[]`.
  - Disimpan ke tabel `spts` dengan `nota_dinas_id = null` (kecuali memilih nota referensi), `status = tersimpan`, dan pegawai disimpan ke tabel pivot `spt_pegawai`.
- **Tidak ada workflow paraf digital**:
  - Tidak ada lagi aksi Paraf Kasubid, Paraf Kabid, Paraf Sekban, maupun ACC Kaban.
  - Tidak ada perubahan status berjenjang (`diajukan_kabid` → `diajukan_sekban` → `diajukan_kaban` → `disetujui_kaban`) pada alur baru.
  - Kolom `_approved_at`, `_paraf`, dan relasi pejabat lama tetap ada di database untuk kompatibilitas data historis, tetapi tidak diisi maupun ditampilkan pada alur baru.

### 6.3. Cetak PDF SPT & Paraf Manual
- **SPT via Nota (`cetakSpt`)** dan **SPT Mandiri (`cetakSptMandiri`)** menghasilkan PDF resmi A4.
- Pada bagian **Paraf Hirarki** tetap ditampilkan baris **Sekretaris**, **Kabid**, dan **Kasubid**, namun seluruh kolom paraf **dibiarkan kosong** dengan tinggi yang cukup untuk paraf basah.
- Tanda tangan utama Kepala Badan pada bagian bawah dokumen juga dilakukan secara manual setelah PDF dicetak.
- Tidak ada gambar tanda tangan digital yang disisipkan ke dalam PDF.

### 6.4. Pengelolaan SPT
- **Daftar SPT** (`/spt`) menampilkan seluruh SPT sesuai cakupan data pengguna tanpa kolom “Status Paraf”.
- Aksi yang tersedia: **Cetak SPT** dan **Hapus** (sesuai hak akses).
- **Nomor SPT** masih dapat diperbarui melalui `updateNomor`, dan nomor riil tetap menjadi syarat akses modul SPJ.

---

## 7. Alur Bisnis Surat Perintah Perjalanan Dinas (SPPD)

Sama seperti SPT, SPPD mendukung dua jalur:

### 7.1. SPPD via Nota Dinas
- Dibuat via `SPPDController@store` dari halaman Arsip atau Nota Dinas.
- Atribut yang diinput: `nomor_sppd`, `alat_angkutan` (darat/udara/laut/kereta), `tempat_berangkat`, `tempat_tujuan`, `tempat_tujuan_2` (opsional), dan `tanggal_sppd`.
- Durasi hari dihitung otomatis: `Lama Hari = (Tanggal Selesai - Tanggal Mulai) + 1 hari`.
- Cetak PDF menghasilkan lembar SPPD lembar 1 (identitas personil & tujuan) dan lembar 2 (visum perjalanan/catatan keberangkatan & kepulangan).

### 7.2. SPPD Mandiri (Standalone)
- Dibuat via `SPPDController@storeMandiri` dengan memilih referensi SPT (`spt_id`).
- Mengambil parameter bawaan dari SPT terkait (`nomor_spt_ref`, lokasi, kegiatan, tanggal mulai & selesai, dan daftar personil bawaan SPT).
- Fleksibilitas Override: Pengguna diperbolehkan mengubah (override) tanggal atau membatasi personil yang tercantum di SPPD tanpa merusak data asli pada SPT induk.
- Cetak PDF mandiri melalui rute `/sppd/{id}/cetak-mandiri`.

---

## 8. Alur Bisnis SPJ & Rekapitulasi Biaya Riil

Modul SPJ ([SPJController](file:///d:/Rakha/app-spt/app/Http/Controllers/SPJController.php)) bertanggung jawab atas akuntabilitas keuangan dan pencairan anggaran perjalanan dinas.

```mermaid
flowchart LR
    A[SPT dengan Nomor Riil] --> B[Pilih Uraian Anggaran Sub-Kegiatan]
    B --> C[Input Rincian Biaya per Pegawai]
    C --> D[Hitung Total Biaya Riil]
    D --> E[Simpan SpjRincian]
    E -->|Trigger Eloquent Event| F[Update Otomatis Uraian: ok_terpakai & anggaran_terpakai]
    F -->|Trigger Eloquent Event| G[Update Otomatis Sub-Kegiatan: pagu & koefisien]
    E --> H[Export Excel Berkas Lengkap: Kuitansi, Rincian, DPR, TT]
```

### 8.1. Validasi Akses SPJ
- Sebelum SPJ dapat diakses atau diisi, sistem memverifikasi `has_real_nomor` pada model `Spt`. Jika nomor SPT masih berupa placeholder atau kosong, pengisian SPJ diblokir untuk mencegah dokumen bodong.

### 8.2. Komponen Pengeluaran Biaya Riil per Personil
Untuk setiap pegawai yang ditugaskan pada SPT, staf keuangan menginput rincian pengeluaran pada tabel `spj_rincians`:
1. **Uang Harian**: `jumlah_hari × uang_harian`
2. **Tiket Pesawat**: `tiket_pesawat_pergi + tiket_pesawat_pulang`
3. **Transport Lokal / Taksi**: `transport`
4. **Biaya Penginapan**: `penginapan`
5. **Kode Rekening & Uraian Anggaran**: Menghubungkan rincian belanja ke pos rekening sub-kegiatan tertentu.

$$\text{Total Rincian Pegawai} = (\text{Hari} \times \text{Uang Harian}) + \text{Tiket Pergi} + \text{Tiket Pulang} + \text{Transport} + \text{Penginapan}$$

### 8.3. Sinkronisasi Anggaran Otomatis (Event-Driven Eloquent)
Sistem memiliki mekanisme trigger otomatis yang sangat ketat:
1. Saat data `SpjRincian` disimpan (`saved`) atau dihapus (`deleted`), model memanggil method `syncUraian()` -> `recalculateUraian($uraianId)`.
2. Sistem menghitung ulang seluruh pengeluaran pada uraian tersebut:
   - `ok_terpakai = count(rincians)`
   - `anggaran_terpakai = sum(rincians.total)`
3. Perubahan pada model `Uraian` secara otomatis memicu method `updateSubKegiatanTotals()`, yang memperbarui total pagu dan koefisien pada model `SubKegiatan`.

### 8.4. Ekspor Berkas SPJ Excel Multi-Sheet Komprehensif
Aplikasi mengolah template resmi `resources/tamplates/tamplate_spj.xlsx` menggunakan PhpSpreadsheet dengan output berupa file Excel komprehensif:
1. **Sheet `Kuitansi`**:
   - Berisi tanda terima pembayaran tunggal untuk total kumulatif semua personil.
   - Mengonversi nominal angka menjadi kalimat terbilang otomatis bahasa Indonesia melalui fungsi private `terbilang()`.
   - Menautkan nama Penerima (Pegawai pertama dkk), KPA (Kuasa Pengguna Anggaran), PPTK, dan Bendahara Pengeluaran.
2. **Sheet `Rincian_[NamaPegawai]`**:
   - Dibuat secara dinamis dengan mengkloning template untuk setiap pegawai.
   - Menghitung rincian tiket, uang harian, penginapan, dan bagian pengesahan rampung perjalanan dinas.
3. **Sheet `DPR_[NamaPegawai]` (Daftar Pengeluaran Riil)**:
   - Diterbitkan khusus untuk perjalanan **Luar Daerah / Jakarta**.
   - Berisi surat pernyataan pengeluaran riil transport taksi tempat kedudukan PP yang ditandatangani oleh pegawai bersangkutan dan KPA.
4. **Sheet `TT_bpkad` (Tanda Terima BPKAD)**:
   - Menghubungkan rumus antar-sheet secara dinamis (`='Rincian_Nama'!E16`, dst).
   - Menghapus baris slot yang tidak terpakai secara otomatis jika jumlah pegawai kurang dari kapasitas maksimal (6 personil), serta menyesuaikan posisi baris Total dan tanda tangan PPTK.

---

## 9. Alur Monitoring dan Evaluasi (Monev) Anggaran

Modul Monev ([MonevController](file:///d:/Rakha/app-spt/app/Http/Controllers/MonevController.php)) menyediakan visibilitas kesehatan keuangan dan serapan anggaran perjalanan dinas.

### 9.1. Hirarki Monitoring Uraian
- Setiap Sub-Kegiatan dipecah menjadi beberapa pos **Uraian Belanja**.
- Setiap Uraian mencakup:
  - `ok_total`: Target kuota Orang-Kegiatan.
  - `ok_terpakai`: Realisasi kuota Orang-Kegiatan dari SPJ.
  - `harga_satuan`: Standar biaya per paket/orang.
  - `total_anggaran`: Pagu maksimal uraian (`ok_total × harga_satuan`).
  - `anggaran_terpakai`: Realisasi serapan dana dari SPJ.

### 9.2. Pengamanan Otorisasi Bidang (`checkBidangAuthorization`)
Setiap penambahan, pengubahan, atau penghapusan uraian diproteksi melalui fungsi otorisasi ketat:
- Kasubid hanya dapat memanipulasi uraian pada sub-kegiatan yang berada di sub-bidangnya.
- Kabid/Admin hanya dapat memanipulasi uraian di bidangnya. Pelanggaran hak akses langsung memicu `abort(403)`.

---

## 10. Alur Dashboard, Statistik, & Rekapitulasi Tahunan

Modul Dashboard ([DashboardController](file:///d:/Rakha/app-spt/app/Http/Controllers/DashboardController.php)) mengagregasikan seluruh data transaksi menjadi indikator kinerja utama.

### 10.1. Indikator Kinerja Utama (KPI Metrics)
1. **Total Pagu Anggaran**: Akumulasi `total_anggaran` seluruh uraian kegiatan dalam scope user.
2. **Total Realisasi Anggaran**: Akumulasi `anggaran_terpakai` dari SPJ.
3. **Sisa Anggaran**: $\text{Sisa} = \text{Total Pagu} - \text{Total Realisasi}$.
4. **Persentase Serapan Dana**: $\frac{\text{Total Realisasi}}{\text{Total Pagu}} \times 100\%$.
5. **Serapan OK (Orang-Kegiatan)**: Perbandingan `ok_terpakai` terhadap `ok_total`.
6. **Total Sub-Kegiatan, Total PPTK, dan Total SPT**.
7. **Top 5 Sub-Kegiatan**: Sub-kegiatan dengan alokasi anggaran terbesar beserta persentase serapannya.
8. **Aktivitas Terbaru**: Daftar 5 transaksi nota dinas paling mutakhir.

### 10.2. Rekapitulasi Perjalanan Pegawai Bulanan & Tahunan
- Menyajikan matriks frekuensi penugasan setiap pegawai dari bulan Januari sampai Desember pada tahun anggaran terpilih.
- Mencegah disparitas atau ketimpangan penugasan antar pegawai.
- Data rekapitulasi dapat diekspor ke file Excel untuk kebutuhan pelaporan ke pimpinan instansi.

---

## 11. Diagram Alur End-to-End (Mermaid)

Diagram berikut merangkum perjalanan data secara menyeluruh dari login hingga pengarsipan dan pertanggungjawaban dana:

```mermaid
sequenceDiagram
    autonumber
    actor Staf as Pembuat / Kasubid
    actor Pejabat as Pejabat (Kabid/Sekban/Kaban)
    actor Keuangan as Staf Keuangan / SPJ

    Note over Staf, Pejabat: 1. Pembuatan Nota Dinas (tanpa persetujuan digital)
    Staf->>Staf: Buat Nota Dinas (Pilih Personil, Lokasi, Kegiatan)
    Staf->>Staf: Simpan Nota (status: tersimpan)
    Staf->>Staf: Cetak PDF Nota Dinas
    Staf->>Pejabat: Tanda tangan / paraf basah pada dokumen cetak

    Note over Staf, Keuangan: 2. Penerbitan Dokumen (SPT & SPPD)
    Staf->>Staf: Terbitkan SPT (Input No. SPT, DPA/DPPA)
    Staf->>Staf: Terbitkan SPPD (Alat Angkut, Rute)
    Staf->>Staf: Cetak PDF SPT & SPPD
    Staf->>Pejabat: Paraf Hirarki (Sekretaris, Kabid, Kasubid) & TTD Kaban secara manual

    Note over Keuangan: 3. Pertanggungjawaban (SPJ) & Evaluasi (Monev)
    Keuangan->>Keuangan: Akses SPJ berdasarkan Nomor SPT
    Keuangan->>Keuangan: Input Rincian Biaya per Pegawai (Uang Harian, Tiket, Penginapan, Transport)
    Keuangan->>Keuangan: Sistem Otomatis Update Serapan Uraian & Pagu Sub-Kegiatan
    Keuangan->>Keuangan: Ekspor SPJ Excel Multi-Sheet (Kuitansi, Rincian, DPR, TT)
```

---

## Ringkasan File & Controller Terkait

| Modul | Controller Utama | Model Terkait | Lokasi Views |
| :--- | :--- | :--- | :--- |
| **Autentikasi & Akun** | `AuthController.php`<br>`UserController.php` | `User`, `Role` | `resources/views/pages/auth/`<br>`resources/views/pages/master/users/` |
| **Master Organisasi** | `DinasController.php`<br>`BidangController.php`<br>`SubBidangController.php` | `Dinas`, `Bidang`, `SubBidang` | `resources/views/pages/master/dinas/` |
| **Master Pegawai** | `PegawaiController.php` | `Pegawai` | `resources/views/pages/master/pegawai/` |
| **Sub Kegiatan** | `SubKegiatanController.php` | `SubKegiatan`, `Uraian` | `resources/views/pages/master/sub_kegiatan/` |
| **Nota Dinas & Arsip** | `NotaDinasController.php` | `NotaDinas`, `Pegawai` | `resources/views/pages/nota_dinas/`<br>`resources/views/pages/arsip/` |
| **SPT** | `SPTController.php` | `Spt`, `NotaDinas`, `Pegawai` | `resources/views/pages/spt/` |
| **SPPD** | `SPPDController.php` | `Sppd`, `Spt`, `NotaDinas` | `resources/views/pages/sppd/` |
| **SPJ** | `SPJController.php` | `SpjRincian`, `Spt`, `Uraian` | `resources/views/pages/spj/` |
| **Monev** | `MonevController.php` | `Uraian`, `SubKegiatan` | `resources/views/pages/monev/` |
| **Dashboard** | `DashboardController.php` | Seluruh Model di atas | `resources/views/pages/dashboard/` |
