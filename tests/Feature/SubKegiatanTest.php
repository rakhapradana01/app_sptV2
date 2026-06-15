<?php

use App\Models\Pegawai;
use App\Models\Role;
use App\Models\SubKegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Helper untuk buat user super_admin + login
function adminUser(): User
{
    $role = Role::create(['name' => 'super_admin']);
    return User::create([
        'name'     => 'Admin Test',
        'username' => 'admin_test',
        'password' => bcrypt('password123'),
        'role_id'  => $role->id,
    ]);
}

function makePegawai(array $attrs = []): Pegawai
{
    return Pegawai::create(array_merge([
        'nama'    => 'Pegawai Test',
        'nip'     => '111222333',
        'jabatan' => 'Staff',
        'pangkat' => 'III/a',
    ], $attrs));
}

// ==========================================
// INDEX - Daftar Sub Kegiatan
// ==========================================

test('halaman sub-kegiatan bisa diakses oleh super_admin', function () {
    $user = adminUser();

    $response = $this->actingAs($user)->get(route('sub-kegiatan.index'));
    $response->assertStatus(200);
});

test('non-super_admin tidak bisa akses halaman sub-kegiatan', function () {
    $role = Role::create(['name' => 'kepala_sub_bidang']);
    $user = User::create([
        'name'     => 'Kasubid',
        'username' => 'kasubid',
        'password' => bcrypt('password123'),
        'role_id'  => $role->id,
    ]);

    $response = $this->actingAs($user)->get(route('sub-kegiatan.index'));
    $response->assertStatus(403);
});

// ==========================================
// STORE - Tambah Sub Kegiatan
// ==========================================

test('super_admin dapat membuat sub kegiatan baru', function () {
    $user    = adminUser();
    $pegawai = makePegawai();

    $response = $this->actingAs($user)->post(route('sub-kegiatan.store'), [
        'pegawai_kasubid_id' => $pegawai->id,
        'nama_kegiatan'      => 'Program Lingkungan Hidup',
        'nomor_rekening'     => '5.2.01.01.01.0001',
    ]);

    $response->assertRedirect(route('sub-kegiatan.index'));
    $this->assertDatabaseHas('sub_kegiatans', [
        'nama_kegiatan'  => 'Program Lingkungan Hidup',
        'nomor_rekening' => '5.2.01.01.01.0001',
    ]);
});

test('store gagal jika nomor_rekening kosong', function () {
    $user    = adminUser();
    $pegawai = makePegawai();

    $response = $this->actingAs($user)->post(route('sub-kegiatan.store'), [
        'pegawai_kasubid_id' => $pegawai->id,
        'nama_kegiatan'      => 'Program Lingkungan Hidup',
        // 'nomor_rekening' sengaja dikosongkan
    ]);

    $response->assertSessionHasErrors(['nomor_rekening']);
    $this->assertDatabaseMissing('sub_kegiatans', ['nama_kegiatan' => 'Program Lingkungan Hidup']);
});

test('store gagal jika nama_kegiatan kosong', function () {
    $user    = adminUser();
    $pegawai = makePegawai();

    $response = $this->actingAs($user)->post(route('sub-kegiatan.store'), [
        'pegawai_kasubid_id' => $pegawai->id,
        'nomor_rekening'     => '5.2.01.01.01.0001',
        // 'nama_kegiatan' sengaja dikosongkan
    ]);

    $response->assertSessionHasErrors(['nama_kegiatan']);
});

test('store gagal jika pegawai_kasubid_id tidak valid', function () {
    $user = adminUser();

    $response = $this->actingAs($user)->post(route('sub-kegiatan.store'), [
        'pegawai_kasubid_id' => 9999, // ID tidak ada
        'nama_kegiatan'      => 'Program Test',
        'nomor_rekening'     => '5.2.01',
    ]);

    $response->assertSessionHasErrors(['pegawai_kasubid_id']);
});

test('nomor_rekening tersimpan dengan benar ke database', function () {
    $user    = adminUser();
    $pegawai = makePegawai();

    $this->actingAs($user)->post(route('sub-kegiatan.store'), [
        'pegawai_kasubid_id' => $pegawai->id,
        'nama_kegiatan'      => 'Kegiatan Dana',
        'nomor_rekening'     => '5.2.01.01.01.9999',
    ]);

    $sub = SubKegiatan::where('nama_kegiatan', 'Kegiatan Dana')->first();
    expect($sub)->not->toBeNull();
    expect($sub->nomor_rekening)->toBe('5.2.01.01.01.9999');
    expect($sub->harga_satuan)->toBe(0);
    expect($sub->koefisien)->toBe(0);
    expect($sub->pagu)->toBe(0);
});

// ==========================================
// SHOW - JSON Detail Sub Kegiatan
// ==========================================

test('show mengembalikan JSON sub kegiatan yang benar', function () {
    $user    = adminUser();
    $pegawai = makePegawai();

    $sub = SubKegiatan::create([
        'pegawai_kasubid_id' => $pegawai->id,
        'nama_kegiatan'      => 'Program Air Bersih',
        'nomor_rekening'     => '5.2.02.01',
        'harga_satuan'       => 0,
        'koefisien'          => 0,
        'pagu'               => 0,
    ]);

    $response = $this->actingAs($user)->getJson("/sub-kegiatan/{$sub->id}");
    $response->assertStatus(200);
    $response->assertJsonFragment([
        'nama_kegiatan'  => 'Program Air Bersih',
        'nomor_rekening' => '5.2.02.01',
    ]);
});

test('show mengembalikan 404 jika id tidak ditemukan', function () {
    $user = adminUser();

    $response = $this->actingAs($user)->getJson('/sub-kegiatan/99999');
    $response->assertStatus(404);
});

// ==========================================
// UPDATE - Edit Sub Kegiatan
// ==========================================

test('super_admin dapat mengupdate sub kegiatan via PUT JSON', function () {
    $user    = adminUser();
    $pegawai = makePegawai();

    $sub = SubKegiatan::create([
        'pegawai_kasubid_id' => $pegawai->id,
        'nama_kegiatan'      => 'Lama Kegiatan',
        'nomor_rekening'     => '1.1.1',
        'harga_satuan'       => 0,
        'koefisien'          => 0,
        'pagu'               => 0,
    ]);

    $pegawai2 = makePegawai(['nama' => 'Pegawai Baru', 'nip' => '999888']);

    $response = $this->actingAs($user)->putJson("/sub-kegiatan/{$sub->id}", [
        'pegawai_kasubid_id' => $pegawai2->id,
        'nama_kegiatan'      => 'Kegiatan Diperbarui',
        'nomor_rekening'     => '5.2.99.99',
    ]);

    $response->assertStatus(200);
    $response->assertJsonFragment(['success' => 'Sub Kegiatan Berhasil Dirubah!']);

    $this->assertDatabaseHas('sub_kegiatans', [
        'id'             => $sub->id,
        'nama_kegiatan'  => 'Kegiatan Diperbarui',
        'nomor_rekening' => '5.2.99.99',
    ]);
});

test('update gagal jika nomor_rekening tidak dikirim', function () {
    $user    = adminUser();
    $pegawai = makePegawai();

    $sub = SubKegiatan::create([
        'pegawai_kasubid_id' => $pegawai->id,
        'nama_kegiatan'      => 'Lama',
        'nomor_rekening'     => '1.1',
        'harga_satuan'       => 0,
        'koefisien'          => 0,
        'pagu'               => 0,
    ]);

    $response = $this->actingAs($user)->putJson("/sub-kegiatan/{$sub->id}", [
        'pegawai_kasubid_id' => $pegawai->id,
        'nama_kegiatan'      => 'Kegiatan Baru',
        // 'nomor_rekening' dikosongkan
    ]);

    $response->assertStatus(422); // Validation error
    $response->assertJsonValidationErrors(['nomor_rekening']);
});

test('update mengembalikan 404 jika id tidak ada', function () {
    $user    = adminUser();
    $pegawai = makePegawai();

    $response = $this->actingAs($user)->putJson('/sub-kegiatan/99999', [
        'pegawai_kasubid_id' => $pegawai->id,
        'nama_kegiatan'      => 'Test',
        'nomor_rekening'     => '1.1',
    ]);

    $response->assertStatus(404);
});

// ==========================================
// UNIT - Model SubKegiatan
// ==========================================

test('model SubKegiatan menghitung realisasi dari uraians', function () {
    $pegawai = makePegawai();
    adminUser(); // Butuh DB setup

    $sub = SubKegiatan::create([
        'pegawai_kasubid_id' => $pegawai->id,
        'nama_kegiatan'      => 'Test Realisasi',
        'nomor_rekening'     => '5.1.1',
        'harga_satuan'       => 0,
        'koefisien'          => 0,
        'pagu'               => 10_000_000,
    ]);

    // Realisasi default 0 karena belum ada uraians
    expect($sub->realisasi)->toBe(0);
    expect($sub->sisa)->toBe(10_000_000);
});
