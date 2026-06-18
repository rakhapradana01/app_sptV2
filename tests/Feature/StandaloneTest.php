<?php

use App\Models\Pegawai;
use App\Models\Role;
use App\Models\Spt;
use App\Models\Sppd;
use App\Models\SubKegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ==========================================
// Helpers
// ==========================================

function standaloneAdminUser(): User
{
    $role = Role::create(['name' => 'super_admin']);
    return User::create([
        'name'     => 'Admin Standalone',
        'username' => 'admin_standalone',
        'password' => bcrypt('password123'),
        'role_id'  => $role->id,
    ]);
}

function makePegawaiForStandalone(): Pegawai
{
    static $counter = 0;
    $counter++;
    return Pegawai::create([
        'nama'    => "Pegawai Standalone {$counter}",
        'nip'     => "NIP_STANDALONE_{$counter}",
        'jabatan' => "Staff Standalone",
        'pangkat' => 'III/a',
    ]);
}

// ==========================================
// SPT STANDALONE TESTS
// ==========================================

test('halaman daftar SPT mandiri dapat diakses oleh super_admin', function () {
    $user = standaloneAdminUser();
    $response = $this->actingAs($user)->get(route('spt.index'));
    $response->assertStatus(200);
});

test('halaman buat SPT mandiri dapat diakses oleh super_admin', function () {
    $user = standaloneAdminUser();
    $response = $this->actingAs($user)->get(route('spt.create'));
    $response->assertStatus(200);
});

test('dapat menyimpan SPT mandiri baru', function () {
    $user = standaloneAdminUser();
    $pegawai = makePegawaiForStandalone();
    $sub = SubKegiatan::create([
        'pegawai_kasubid_id' => $pegawai->id,
        'nama_kegiatan'      => 'Sub Kegiatan Standalone',
        'nomor_rekening'     => '5.2.02.01',
        'harga_satuan'       => 0,
        'koefisien'          => 0,
        'pagu'               => 1000000,
    ]);

    $response = $this->actingAs($user)->post(route('spt.storeMandiri'), [
        'nomor_spt'       => '800.1.11.1/001/BPKAD/2026',
        'jenis_anggaran'  => 'DPA',
        'tahun_anggaran'  => 2026,
        'sub_kegiatan_id' => $sub->id,
        'kegiatan'        => 'Rapat Koordinasi Standalone',
        'lokasi'          => 'Jakarta',
        'tanggal_mulai'   => '2026-06-20',
        'tanggal_selesai' => '2026-06-22',
        'pegawai_ids'     => [$pegawai->id],
    ]);

    $response->assertRedirect(route('spt.index'));
    $this->assertDatabaseHas('spts', [
        'nomor_spt' => '800.1.11.1/001/BPKAD/2026',
        'kegiatan'  => 'Rapat Koordinasi Standalone',
    ]);

    $spt = Spt::where('nomor_spt', '800.1.11.1/001/BPKAD/2026')->first();
    expect($spt->pegawais)->toHaveCount(1);
    expect($spt->pegawais->first()->id)->toBe($pegawai->id);
});

test('dapat mencetak PDF SPT mandiri', function () {
    $user = standaloneAdminUser();
    $pegawai = makePegawaiForStandalone();
    $spt = Spt::create([
        'nomor_spt'      => '800.1.11.1/002/BPKAD/2026',
        'jenis_anggaran' => 'DPA',
        'tahun_anggaran' => 2026,
        'kegiatan'       => 'Pemeriksaan Kas',
        'lokasi'         => 'Banjarmasin',
        'tanggal_mulai'  => '2026-06-25',
    ]);
    $spt->pegawais()->attach($pegawai->id);

    $response = $this->actingAs($user)->get(route('spt.cetakMandiri', $spt->id));
    $response->assertStatus(200);
});

test('dapat menghapus SPT mandiri', function () {
    $user = standaloneAdminUser();
    $pegawai = makePegawaiForStandalone();
    $spt = Spt::create([
        'nomor_spt'      => '800.1.11.1/003/BPKAD/2026',
        'jenis_anggaran' => 'DPA',
        'tahun_anggaran' => 2026,
        'kegiatan'       => 'Pemeriksaan Aset',
        'lokasi'         => 'Banjarbaru',
        'tanggal_mulai'  => '2026-06-26',
    ]);
    $spt->pegawais()->attach($pegawai->id);

    $response = $this->actingAs($user)->delete(route('spt.destroyMandiri', $spt->id));
    $response->assertRedirect(route('spt.index'));
    $this->assertDatabaseMissing('spts', ['id' => $spt->id]);
});

// ==========================================
// SPPD STANDALONE TESTS
// ==========================================

test('halaman daftar SPPD mandiri dapat diakses oleh super_admin', function () {
    $user = standaloneAdminUser();
    $response = $this->actingAs($user)->get(route('sppd.index'));
    $response->assertStatus(200);
});

test('halaman buat SPPD mandiri dapat diakses oleh super_admin', function () {
    $user = standaloneAdminUser();
    $response = $this->actingAs($user)->get(route('sppd.create'));
    $response->assertStatus(200);
});

test('dapat menyimpan SPPD mandiri baru', function () {
    $user = standaloneAdminUser();
    $pegawai = makePegawaiForStandalone();

    $response = $this->actingAs($user)->post(route('sppd.storeMandiri'), [
        'nomor_sppd'       => '000.1.2.3/001/BPKAD/2026',
        'nomor_spt_ref'    => '800.1.11.1/001/BPKAD/2026',
        'alat_angkutan'    => 'mobil',
        'tempat_berangkat' => 'Banjarbaru',
        'tempat_tujuan'    => 'Jakarta',
        'tempat_tujuan_2'  => 'Bogor',
        'tanggal_sppd'     => '2026-06-18',
        'tanggal_mulai'    => '2026-06-20',
        'tanggal_selesai'  => '2026-06-22',
        'kegiatan'         => 'Perjalanan Standalone',
        'pegawai_ids'      => [$pegawai->id],
    ]);

    $response->assertRedirect(route('sppd.index'));
    $this->assertDatabaseHas('sppds', [
        'nomor_sppd' => '000.1.2.3/001/BPKAD/2026',
        'kegiatan'   => 'Perjalanan Standalone',
    ]);

    $sppd = Sppd::where('nomor_sppd', '000.1.2.3/001/BPKAD/2026')->first();
    expect($sppd->pegawais)->toHaveCount(1);
    expect($sppd->pegawais->first()->id)->toBe($pegawai->id);
});

test('dapat mencetak PDF SPPD mandiri', function () {
    $user = standaloneAdminUser();
    $pegawai = makePegawaiForStandalone();
    $sppd = Sppd::create([
        'nomor_sppd'       => '000.1.2.3/002/BPKAD/2026',
        'alat_angkutan'    => 'mobil',
        'tempat_berangkat' => 'Banjarbaru',
        'tempat_tujuan'    => 'Jakarta',
        'tanggal_sppd'     => '2026-06-18',
        'tanggal_mulai'    => '2026-06-20',
        'kegiatan'         => 'Perjalanan Dinas Standalone',
    ]);
    $sppd->pegawais()->attach($pegawai->id);

    $response = $this->actingAs($user)->get(route('sppd.cetakMandiri', $sppd->id));
    $response->assertStatus(200);
});

test('dapat menghapus SPPD mandiri', function () {
    $user = standaloneAdminUser();
    $pegawai = makePegawaiForStandalone();
    $sppd = Sppd::create([
        'nomor_sppd'       => '000.1.2.3/003/BPKAD/2026',
        'alat_angkutan'    => 'mobil',
        'tempat_berangkat' => 'Banjarbaru',
        'tempat_tujuan'    => 'Jakarta',
        'tanggal_sppd'     => '2026-06-18',
        'tanggal_mulai'    => '2026-06-20',
        'kegiatan'         => 'Perjalanan Aset Standalone',
    ]);
    $sppd->pegawais()->attach($pegawai->id);

    $response = $this->actingAs($user)->delete(route('sppd.destroyMandiri', $sppd->id));
    $response->assertRedirect(route('sppd.index'));
    $this->assertDatabaseMissing('sppds', ['id' => $sppd->id]);
});
