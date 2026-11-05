<?php

use App\Models\NotaDinas;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\Spt;
use App\Models\SubKegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

// ==========================================
// Helpers (prefix khusus agar tidak bentrok antar file test)
// ==========================================

function sptSuperAdminUser(): User
{
    $role = Role::firstOrCreate(['name' => 'super_admin']);

    return User::create([
        'name'     => 'Admin SPT',
        'username' => 'admin_spt_' . uniqid(),
        'password' => bcrypt('password123'),
        'role_id'  => $role->id,
    ]);
}

function sptPegawai(string $jabatan = 'Staff'): Pegawai
{
    static $counter = 0;
    $counter++;

    return Pegawai::create([
        'nama'    => "Pegawai SPT {$counter}",
        'nip'     => "NIP_SPT_{$counter}_" . uniqid(),
        'jabatan' => $jabatan,
        'pangkat' => 'III/a',
    ]);
}

function sptSubKegiatan(Pegawai $pegawai): SubKegiatan
{
    return SubKegiatan::create([
        'pegawai_kasubid_id' => $pegawai->id,
        'nama_kegiatan'      => 'Sub Kegiatan SPT',
        'nomor_rekening'     => '5.2.03.' . uniqid(),
        'harga_satuan'       => 0,
        'koefisien'          => 0,
        'pagu'               => 5_000_000,
    ]);
}

function sptNotaDinas(array $attrs = []): NotaDinas
{
    $kepada = sptPegawai('Kepala Badan');
    $dari   = sptPegawai('Kepala Bidang');
    $sub    = sptSubKegiatan($dari);

    return NotaDinas::create(array_merge([
        'sub_kegiatan_id'  => $sub->id,
        'kepada_id'        => $kepada->id,
        'dari_id'          => $dari->id,
        'tanggal'          => '2026-06-10',
        'perihal'          => 'Nota untuk SPT',
        'lokasi'           => 'Jakarta',
        'jenis_perjalanan' => 'luar_daerah',
        'tanggal_mulai'    => '2026-06-15',
        'kegiatan'         => 'Koordinasi',
        'asal_undangan'    => 'Pusat',
        'status'           => NotaDinas::STATUS_TERSIMPAN,
    ], $attrs));
}

function sptRecord(array $attrs = []): Spt
{
    $pegawai = sptPegawai();

    $spt = Spt::create(array_merge([
        'nomor_spt'       => '800.1.11.1/' . uniqid() . '/BPKAD/2026',
        'jenis_anggaran'  => 'DPA',
        'tahun_anggaran'  => 2026,
        'kegiatan'        => 'Perjalanan Dinas Uji',
        'lokasi'          => 'Banjarbaru',
        'tanggal_mulai'   => '2026-06-20',
        'tanggal_selesai' => '2026-06-22',
        'status'          => Spt::STATUS_TERSIMPAN,
    ], $attrs));

    $spt->pegawais()->attach($pegawai->id);

    return $spt;
}

// ==========================================
// STATUS & PEMBUATAN SPT
// ==========================================

test('SPT mandiri baru tersimpan dengan status tersimpan', function () {
    $user    = sptSuperAdminUser();
    $pegawai = sptPegawai();

    $response = $this->actingAs($user)->post(route('spt.storeMandiri'), [
        'nomor_spt'       => '800.1.11.1/100/BPKAD/2026',
        'jenis_anggaran'  => 'DPA',
        'tahun_anggaran'  => 2026,
        'kegiatan'        => 'Rapat Koordinasi',
        'lokasi'          => 'Jakarta',
        'tanggal_mulai'   => '2026-06-20',
        'tanggal_selesai' => '2026-06-22',
        'pegawai_ids'     => [$pegawai->id],
    ]);

    $response->assertRedirect(route('spt.index'));

    $this->assertDatabaseHas('spts', [
        'nomor_spt' => '800.1.11.1/100/BPKAD/2026',
        'status'    => Spt::STATUS_TERSIMPAN,
    ]);
});

test('SPT yang dibuat dari nota dinas langsung tersimpan tanpa approval', function () {
    $user = sptSuperAdminUser();
    $nota = sptNotaDinas();

    $response = $this->actingAs($user)->post(route('spt.store', $nota->id), [
        'nomor_spt'      => '800.1.11.1/101/BPKAD/2026',
        'jenis_anggaran' => 'DPPA',
        'tahun_anggaran' => 2026,
    ]);

    $response->assertRedirect(route('arsip'));

    $this->assertDatabaseHas('spts', [
        'nota_dinas_id' => $nota->id,
        'status'        => Spt::STATUS_TERSIMPAN,
    ]);
});

// ==========================================
// ROUTE APPROVAL SUDAH DIHAPUS
// ==========================================

test('route approval SPT sudah tidak tersedia', function () {
    expect(Route::has('spt.approveKasubid'))->toBeFalse();
    expect(Route::has('spt.approveKabid'))->toBeFalse();
    expect(Route::has('spt.approveSekban'))->toBeFalse();
    expect(Route::has('spt.approveKaban'))->toBeFalse();
});

// ==========================================
// TAMPILAN TANPA UI APPROVAL
// ==========================================

test('halaman daftar SPT tidak menampilkan status paraf atau aksi approval', function () {
    $user = sptSuperAdminUser();
    $spt  = sptRecord(['nomor_spt' => '800.1.11.1/102/BPKAD/2026']);

    $response = $this->actingAs($user)->get(route('spt.index'));

    $response->assertStatus(200);
    $response->assertSee($spt->nomor_spt);
    $response->assertSee('Cetak SPT');
    $response->assertDontSee('Status Paraf');
    $response->assertDontSee('Paraf Kasubid');
    $response->assertDontSee('Paraf Kabid');
    $response->assertDontSee('Paraf Sekban');
    $response->assertDontSee('ACC Kaban');
});

test('arsip menampilkan SPT tanpa status paraf dan tetap bisa dicetak', function () {
    $user = sptSuperAdminUser();
    $spt  = sptRecord(['nomor_spt' => '800.1.11.1/103/BPKAD/2026']);

    $response = $this->actingAs($user)->get(route('arsip'));

    $response->assertStatus(200);
    $response->assertSee('Arsip SPT Mandiri');
    $response->assertSee($spt->nomor_spt);
    $response->assertDontSee('Status Paraf');
    $response->assertDontSee('Persetujuan Paraf');
    $response->assertDontSee('Paraf Kasubid');
    $response->assertDontSee('ACC Kaban');
});

// ==========================================
// CETAK PDF & PARAF MANUAL
// ==========================================

test('PDF SPT mandiri memiliki kolom paraf kosong tanpa tanda tangan digital', function () {
    $spt = sptRecord();

    $html = view('pages.spt.pdf_standalone', ['spt' => $spt->load(['pegawais', 'subKegiatan', 'notaDinas'])])->render();

    expect($html)->toContain('Paraf Hirarki');
    expect($html)->toContain('Sekretaris');
    expect($html)->toContain('Kabid');
    expect($html)->toContain('Kasubid');
    expect($html)->not->toContain('images/signatures');
    expect($html)->not->toContain('isApprovedBy');
});

test('PDF SPT via nota dinas memiliki kolom paraf kosong tanpa tanda tangan digital', function () {
    $nota = sptNotaDinas();
    sptRecord(['nota_dinas_id' => $nota->id]);
    $nota->load(['spt', 'pegawais']);

    $html = view('pages.spt.pdf', ['nota' => $nota])->render();

    expect($html)->toContain('Paraf Hirarki');
    expect($html)->toContain('Sekretaris');
    expect($html)->toContain('Kabid');
    expect($html)->toContain('Kasubid');
    expect($html)->not->toContain('images/signatures');
    expect($html)->not->toContain('isApprovedBy');
});

test('SPT mandiri dapat dicetak tanpa persetujuan siapa pun', function () {
    $user = sptSuperAdminUser();
    $spt  = sptRecord();

    $response = $this->actingAs($user)->get(route('spt.cetakMandiri', $spt->id));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
});

test('SPT via nota dinas dapat dicetak tanpa persetujuan siapa pun', function () {
    $user = sptSuperAdminUser();
    $nota = sptNotaDinas();
    sptRecord(['nota_dinas_id' => $nota->id]);

    $response = $this->actingAs($user)->get(route('nota.cetakSpt', $nota->id));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
});
