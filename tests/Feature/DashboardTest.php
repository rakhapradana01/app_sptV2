<?php

use App\Models\Pegawai;
use App\Models\NotaDinas;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('rekap-pegawai route returns the correct json and handles month range', function () {
    // Setup roles
    $role = Role::create(['name' => 'super_admin']);
    $user = User::create([
        'name' => 'Admin Test',
        'username' => 'admin_test',
        'password' => bcrypt('password'),
        'role_id' => $role->id,
    ]);

    // Setup models
    $pegawai = Pegawai::create([
        'nama' => 'John Doe Test',
        'nip' => '1234567890',
        'jabatan' => 'Staff',
    ]);

    // Create a NotaDinas for $pegawai in April 2026
    $nota1 = NotaDinas::create([
        'nomor_urut' => 1,
        'tanggal' => '2026-04-15',
        'tanggal_mulai' => '2026-04-15',
        'tanggal_selesai' => '2026-04-18',
        'perihal' => 'Perjalanan Dinas A',
        'kegiatan' => 'Kegiatan A',
        'lokasi' => 'Banjarmasin',
        'status' => 'draft',
    ]);
    $nota1->pegawais()->attach($pegawai->id);

    // Create a NotaDinas for $pegawai in June 2026
    $nota2 = NotaDinas::create([
        'nomor_urut' => 2,
        'tanggal' => '2026-06-10',
        'tanggal_mulai' => '2026-06-10',
        'tanggal_selesai' => '2026-06-12',
        'perihal' => 'Perjalanan Dinas B',
        'kegiatan' => 'Kegiatan B',
        'lokasi' => 'Jakarta',
        'status' => 'draft',
    ]);
    $nota2->pegawais()->attach($pegawai->id);

    $this->actingAs($user);

    // Request with range March to July (3 to 7)
    $response = $this->getJson(route('dashboard.rekap', [
        'bulan_awal' => 3,
        'bulan_akhir' => 7,
        'tahun' => 2026,
    ]));

    $response->assertStatus(200);
    $response->assertJsonPath('namaBulan', 'Maret - Juni 2026'); // Carbon parses actual maximum date range month dynamically

    // Find the record for John Doe Test
    $pegawais = $response->json('pegawais');
    $john = collect($pegawais)->firstWhere('nama', 'John Doe Test');
    expect($john)->not->toBeNull();
    expect($john['nota_dinas_count'])->toBe(2);

    // Test export Excel route
    $responseExcel = $this->get(route('dashboard.export', [
        'bulan_awal' => 3,
        'bulan_akhir' => 7,
        'tahun' => 2026,
    ]));

    $responseExcel->assertStatus(200);
    $responseExcel->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});
