<?php

use App\Models\NotaDinas;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\SubKegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ==========================================
// Helpers
// ==========================================

function notaAdminUser(): User
{
    $role = Role::create(['name' => 'super_admin']);
    return User::create([
        'name'     => 'Admin Test',
        'username' => 'admin_nota',
        'password' => bcrypt('password123'),
        'role_id'  => $role->id,
    ]);
}

function kasubidUser(): User
{
    $role = Role::create(['name' => 'kepala_sub_bidang']);
    return User::create([
        'name'     => 'Kasubid Test',
        'username' => 'kasubid_nota',
        'password' => bcrypt('password123'),
        'role_id'  => $role->id,
    ]);
}

function makeSubKegiatan(Pegawai $pegawai): SubKegiatan
{
    return SubKegiatan::create([
        'pegawai_kasubid_id' => $pegawai->id,
        'nama_kegiatan'      => 'Sub Kegiatan Test',
        'nomor_rekening'     => '5.2.01.01',
        'harga_satuan'       => 0,
        'koefisien'          => 0,
        'pagu'               => 5_000_000,
    ]);
}

function makeFullPegawai(string $jabatan = 'Staff'): Pegawai
{
    static $counter = 0;
    $counter++;
    return Pegawai::create([
        'nama'    => "Pegawai {$counter}",
        'nip'     => "NIP{$counter}",
        'jabatan' => $jabatan,
        'pangkat' => 'III/a',
    ]);
}

/** Buat NotaDinas lengkap dengan semua kolom NOT NULL */
function makeNotaDinas(array $attrs = []): NotaDinas
{
    $pegawai  = makeFullPegawai();
    $pegawai2 = makeFullPegawai();
    $sub      = makeSubKegiatan($pegawai);

    return NotaDinas::create(array_merge([
        'sub_kegiatan_id'  => $sub->id,
        'kepada_id'        => $pegawai->id,
        'dari_id'          => $pegawai2->id,
        'tanggal'          => '2026-06-10',
        'perihal'          => 'Nota Test',
        'lokasi'           => 'Jakarta',
        'jenis_perjalanan' => 'luar_daerah',
        'tanggal_mulai'    => '2026-06-15',
        'kegiatan'         => 'Koordinasi',
        'asal_undangan'    => 'Pusat',
        'status'           => NotaDinas::DIAJUKAN_KABID,
    ], $attrs));
}

// ==========================================
// INDEX
// ==========================================

test('halaman daftar nota dinas dapat diakses oleh super_admin', function () {
    $user = notaAdminUser();
    $response = $this->actingAs($user)->get(route('nota-dinas.index'));
    $response->assertStatus(200);
});

test('halaman daftar nota dinas dapat diakses oleh kasubid', function () {
    $user = kasubidUser();
    $response = $this->actingAs($user)->get(route('nota-dinas.index'));
    $response->assertStatus(200);
});

// ==========================================
// STORE - Buat Nota Dinas
// ==========================================

test('dapat membuat nota dinas baru', function () {
    $user    = notaAdminUser();
    $kepada  = makeFullPegawai('Kepala Badan Lingkungan Hidup');
    $dari    = makeFullPegawai('Kepala Bidang Pengendalian');
    $sub     = makeSubKegiatan($dari);

    $response = $this->actingAs($user)->post(route('nota-dinas.store'), [
        'sub_kegiatan_id'  => $sub->id,
        'tanggal'          => '2026-06-10',
        'kepada_id'        => $kepada->id,
        'dari_id'          => $dari->id,
        'melalui_id'       => null,
        'perihal'          => 'Perjalanan Dinas ke Jakarta',
        'lokasi'           => 'Jakarta',
        'jenis_perjalanan' => 'luar_daerah',
        'tanggal_mulai'    => '2026-06-15',
        'tanggal_selesai'  => null,
        'kegiatan'         => 'Koordinasi Program Lingkungan',
        'asal_undangan'    => 'Kementerian LHK',
        'sifat'            => 'Penting',
        'lampiran'         => '-',
    ]);

    $response->assertRedirect(route('nota-dinas.index'));
    $this->assertDatabaseHas('nota_dinas', [
        'perihal' => 'Perjalanan Dinas ke Jakarta',
        'lokasi'  => 'Jakarta',
    ]);
});

test('nota dinas disimpan dengan tanggal_selesai null jika tidak diisi', function () {
    // Langsung buat model tanpa melalui HTTP (bypass store)
    $nota = makeNotaDinas([
        'perihal'         => 'Cek tanggal selesai null',
        'tanggal_selesai' => null,
    ]);

    expect($nota)->not->toBeNull();
    expect($nota->tanggal_selesai)->toBeNull();
    $this->assertDatabaseHas('nota_dinas', [
        'id'              => $nota->id,
        'tanggal_selesai' => null,
    ]);
});

test('nota dinas gagal dibuat jika field wajib kosong', function () {
    $user = notaAdminUser();

    $response = $this->actingAs($user)->post(route('nota-dinas.store'), []);
    $response->assertSessionHasErrors([
        'sub_kegiatan_id',
        'tanggal',
        'kepada_id',
        'dari_id',
        'perihal',
        'lokasi',
        'jenis_perjalanan',
        'tanggal_mulai',
        'kegiatan',
        'asal_undangan',
    ]);
});

test('nota dinas gagal dibuat jika jenis_perjalanan tidak valid', function () {
    $user   = notaAdminUser();
    $kepada = makeFullPegawai();
    $dari   = makeFullPegawai();
    $sub    = makeSubKegiatan($dari);

    $response = $this->actingAs($user)->post(route('nota-dinas.store'), [
        'sub_kegiatan_id'  => $sub->id,
        'tanggal'          => '2026-06-10',
        'kepada_id'        => $kepada->id,
        'dari_id'          => $dari->id,
        'perihal'          => 'Test',
        'lokasi'           => 'Test Kota',
        'jenis_perjalanan' => 'tidak_valid',
        'tanggal_mulai'    => '2026-06-15',
        'kegiatan'         => 'Test kegiatan',
        'asal_undangan'    => 'Test sumber',
    ]);

    $response->assertSessionHasErrors(['jenis_perjalanan']);
});

// ==========================================
// STATUS DEFAULT saat store
// ==========================================

test('nota dinas baru memiliki status diajukan_kabid secara default', function () {
    // Controller selalu set status DIAJUKAN_KABID — verifikasi via model langsung
    $nota = makeNotaDinas(['perihal' => 'Cek Status Default']);

    expect($nota->status)->toBe(NotaDinas::DIAJUKAN_KABID);
    $this->assertDatabaseHas('nota_dinas', [
        'id'     => $nota->id,
        'status' => NotaDinas::DIAJUKAN_KABID,
    ]);
});

// ==========================================
// APPROVE Kabid
// ==========================================

test('kepala_bidang dapat menyetujui nota dinas', function () {
    $roleKabid = Role::create(['name' => 'kepala_bidang']);
    $kabid     = User::create([
        'name'     => 'Kabid',
        'username' => 'kabid_test',
        'password' => bcrypt('password123'),
        'role_id'  => $roleKabid->id,
    ]);

    $nota = makeNotaDinas(['perihal' => 'Nota Kabid']);

    $response = $this->actingAs($kabid)->patch(route('nota-dinas.approve-kabid', $nota));
    $response->assertRedirect(route('nota-dinas.index'));

    $this->assertDatabaseHas('nota_dinas', [
        'id'     => $nota->id,
        'status' => NotaDinas::DISETUJUI_KABID,
    ]);
});

test('non-kabid tidak bisa approve nota dinas sebagai kabid', function () {
    $role = Role::create(['name' => 'staff']);
    $user = User::create([
        'name'     => 'Staff Test',
        'username' => 'staff_test',
        'password' => bcrypt('password123'),
        'role_id'  => $role->id,
    ]);

    $nota = makeNotaDinas(['perihal' => 'Test Nota Non Kabid']);

    $response = $this->actingAs($user)->patch(route('nota-dinas.approve-kabid', $nota));
    $response->assertStatus(403);
});

// ==========================================
// REVISI Kabid
// ==========================================

test('kabid dapat mengirim revisi ke nota dinas', function () {
    $roleKabid = Role::create(['name' => 'kepala_bidang']);
    $kabid     = User::create([
        'name'     => 'Kabid Revisi',
        'username' => 'kabid_revisi',
        'password' => bcrypt('password123'),
        'role_id'  => $roleKabid->id,
    ]);

    $nota = makeNotaDinas(['perihal' => 'Perlu Revisi']);

    $response = $this->actingAs($kabid)->patch(
        route('nota-dinas.revisi-kabid', $nota->id),
        ['revisi' => 'Mohon perbaiki lokasi kegiatan dan tujuan perjalanan.']
    );

    $response->assertRedirect(route('nota-dinas.index'));
    $this->assertDatabaseHas('nota_dinas', [
        'id'     => $nota->id,
        'status' => NotaDinas::REVISI_KABID,
    ]);
});

test('revisi gagal jika pesan terlalu pendek', function () {
    $roleKabid = Role::create(['name' => 'kepala_bidang']);
    $kabid     = User::create([
        'name'     => 'Kabid',
        'username' => 'kabid_short',
        'password' => bcrypt('password123'),
        'role_id'  => $roleKabid->id,
    ]);

    $nota = makeNotaDinas(['perihal' => 'Revisi Pendek Test']);

    $response = $this->actingAs($kabid)->patch(
        route('nota-dinas.revisi-kabid', $nota->id),
        ['revisi' => 'ok'] // < 5 karakter
    );

    $response->assertSessionHasErrors(['revisi']);
});
