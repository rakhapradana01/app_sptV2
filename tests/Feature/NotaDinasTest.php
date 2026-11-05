<?php

use App\Models\Bidang;
use App\Models\Dinas;
use App\Models\NotaDinas;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\SubBidang;
use App\Models\SubKegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

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

function kasubidUser(?int $subBidangId = null): User
{
    $role = Role::firstOrCreate(['name' => 'kepala_sub_bidang']);
    return User::create([
        'name'          => 'Kasubid Test',
        'username'      => 'kasubid_nota_' . uniqid(),
        'password'      => bcrypt('password123'),
        'role_id'       => $role->id,
        'sub_bidang_id' => $subBidangId,
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
        'status'           => NotaDinas::STATUS_TERSIMPAN,
    ], $attrs));
}

/** Buat dua sub bidang berbeda untuk menguji pembatasan akses. */
function makeDuaSubBidang(): array
{
    $dinas = Dinas::create(['nama_dinas' => 'Dinas Test']);

    $bidang1 = Bidang::create(['dinas_id' => $dinas->id, 'nama_bidang' => 'Bidang Satu']);
    $bidang2 = Bidang::create(['dinas_id' => $dinas->id, 'nama_bidang' => 'Bidang Dua']);

    $sub1 = SubBidang::create(['bidang_id' => $bidang1->id, 'nama_sub_bidang' => 'Sub Satu']);
    $sub2 = SubBidang::create(['bidang_id' => $bidang2->id, 'nama_sub_bidang' => 'Sub Dua']);

    return [$sub1, $sub2];
}

function dataNotaValid(Pegawai $kepada, Pegawai $dari, SubKegiatan $sub): array
{
    return [
        'sub_kegiatan_id'  => $sub->id,
        'tanggal'          => '2026-06-10',
        'kepada_id'        => $kepada->id,
        'dari_id'          => $dari->id,
        'melalui_id'       => null,
        'tembusan'         => 'Kepala Bidang Perencanaan Anggaran Daerah',
        'perihal'          => 'Perjalanan Dinas ke Jakarta',
        'lokasi'           => 'Jakarta',
        'jenis_perjalanan' => 'luar_daerah',
        'tanggal_mulai'    => '2026-06-15',
        'tanggal_selesai'  => null,
        'kegiatan'         => 'Koordinasi Program Lingkungan',
        'asal_undangan'    => 'Kementerian LHK',
        'sifat'            => 'Penting',
        'lampiran'         => '-',
    ];
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

test('dapat membuat nota dinas baru dan langsung tersimpan tanpa approval', function () {
    $user   = notaAdminUser();
    $kepada = makeFullPegawai('Kepala Badan Lingkungan Hidup');
    $dari   = makeFullPegawai('Kepala Bidang Pengendalian');
    $sub    = makeSubKegiatan($dari);

    $response = $this->actingAs($user)->post(
        route('nota-dinas.store'),
        dataNotaValid($kepada, $dari, $sub)
    );

    $response->assertRedirect(route('nota-dinas.index'));

    $this->assertDatabaseHas('nota_dinas', [
        'perihal' => 'Perjalanan Dinas ke Jakarta',
        'lokasi'  => 'Jakarta',
        'status'  => NotaDinas::STATUS_TERSIMPAN,
        'tembusan' => 'Kepala Bidang Perencanaan Anggaran Daerah',
    ]);
});

test('form tambah nota dinas memakai format Yth, Dari, Tembusan, dan Hal', function () {
    $user = notaAdminUser();

    $response = $this->actingAs($user)->get(route('nota-dinas.create'));

    $response->assertStatus(200);
    $response->assertSee('Yth (Kepala Badan)');
    $response->assertSee('Dari');
    $response->assertSee('Tembusan');
    $response->assertSee('Hal');
    $response->assertDontSee('Melalui (kepala bidang)');
});

test('form edit nota dinas memakai format Yth, Dari, Tembusan, dan Hal', function () {
    $user = notaAdminUser();
    $nota = makeNotaDinas(['tembusan' => 'Kepala Bidang Perencanaan Anggaran Daerah']);

    $response = $this->actingAs($user)->get(route('nota-dinas.edit', $nota->id));

    $response->assertStatus(200);
    $response->assertSee('Yth (Kepala Badan)');
    $response->assertSee('Dari');
    $response->assertSee('Tembusan');
    $response->assertSee('Kepala Bidang Perencanaan Anggaran Daerah');
    $response->assertSee('Hal');
    $response->assertDontSee('Melalui (kepala bidang)');
});

test('nota dinas disimpan dengan tanggal_selesai null jika tidak diisi', function () {
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

test('nota dinas baru memiliki status tersimpan, bukan status approval', function () {
    $nota = makeNotaDinas(['perihal' => 'Cek Status Default']);

    expect($nota->status)->toBe(NotaDinas::STATUS_TERSIMPAN);
    $this->assertDatabaseHas('nota_dinas', [
        'id'     => $nota->id,
        'status' => NotaDinas::STATUS_TERSIMPAN,
    ]);
});

// ==========================================
// EDIT / UPDATE / DESTROY
// ==========================================

test('kasubid hanya dapat mengedit nota pada sub bidangnya sendiri', function () {
    [$sub1, $sub2] = makeDuaSubBidang();

    $pemilik = kasubidUser($sub1->id);
    $lain    = kasubidUser($sub2->id);

    $nota = makeNotaDinas([
        'perihal'       => 'Nota Milik Sub Satu',
        'sub_bidang_id' => $sub1->id,
    ]);

    $this->actingAs($pemilik)
        ->get(route('nota-dinas.edit', $nota->id))
        ->assertStatus(200);

    $this->actingAs($lain)
        ->get(route('nota-dinas.edit', $nota->id))
        ->assertStatus(403);
});

test('nota dinas dapat diperbarui tanpa terhalang status approval', function () {
    $user   = notaAdminUser();
    $kepada = makeFullPegawai();
    $dari   = makeFullPegawai();
    $sub    = makeSubKegiatan($dari);
    $nota   = makeNotaDinas(['perihal' => 'Perihal Lama']);

    $payload = dataNotaValid($kepada, $dari, $sub);
    $payload['perihal'] = 'Perihal Baru Setelah Edit';

    $response = $this->actingAs($user)->put(route('nota-dinas.update', $nota->id), $payload);

    $response->assertRedirect(route('nota-dinas.index'));
    $this->assertDatabaseHas('nota_dinas', [
        'id'      => $nota->id,
        'perihal' => 'Perihal Baru Setelah Edit',
        'status'  => NotaDinas::STATUS_TERSIMPAN,
    ]);
});

test('nota dinas dapat dihapus tanpa terhalang status approval', function () {
    $user = notaAdminUser();
    $nota = makeNotaDinas(['perihal' => 'Akan Dihapus']);

    $response = $this->actingAs($user)->delete(route('nota-dinas.destroy', $nota->id));

    $response->assertRedirect(route('nota-dinas.index'));
    $this->assertDatabaseMissing('nota_dinas', ['id' => $nota->id]);
});

// ==========================================
// CETAK PDF TANPA APPROVAL
// ==========================================

test('nota dinas dapat dicetak tanpa persetujuan Kaban', function () {
    $user = notaAdminUser();
    $nota = makeNotaDinas([
        'perihal' => 'Nota Siap Cetak',
        'status'  => NotaDinas::STATUS_TERSIMPAN,
    ]);

    $response = $this->actingAs($user)->get(route('nota.cetakNotaDinas', $nota->id));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
});

test('PDF nota dinas memakai format Yth Dari Tembusan dan tabel nama jabatan', function () {
    $nota = makeNotaDinas([
        'perihal'        => 'Perihal Cetak Format Baru',
        'tembusan'       => 'Kepala Bidang Perencanaan Anggaran Daerah',
        'asal_undangan'  => 'Sehubungan dengan undangan dari Kementerian LHK',
        'kegiatan'       => 'Koordinasi Program Lingkungan',
        'lokasi'         => 'Jakarta',
        'tanggal_mulai'  => '2026-06-15',
        'tanggal_selesai' => '2026-06-17',
    ]);

    $peserta = makeFullPegawai('Analis Kebijakan');
    $nota->pegawais()->attach($peserta->id);
    $nota->load(['pegawais', 'dari', 'kepada', 'melalui', 'subKegiatan']);

    $html = view('pages.nota_dinas.pdf', ['nota' => $nota])->render();

    expect($html)->toContain('Yth');
    expect($html)->toContain('Dari');
    expect($html)->toContain('Tembusan');
    expect($html)->toContain('Kepala Bidang Perencanaan Anggaran Daerah');
    expect($html)->toContain('Sehubungan dengan undangan dari Kementerian LHK, bersama ini disampaikan hal-hal sebagai berikut.');
    expect($html)->toContain('Nama');
    expect($html)->toContain('Jabatan');
    expect($html)->toContain('Analis Kebijakan');
    expect($html)->toContain('5.2.01.01');
    expect($html)->toContain('Sub Kegiatan Test');

    // Pagu tidak boleh ikut tercetak pada Nota Dinas.
    expect($html)->not->toContain('5.000.000');
    expect($html)->not->toContain('5000000');

    expect($html)->toContain('Demikian Nota Dinas ini disampaikan untuk menjadi perhatian dan sebagaimana mestinya.');
});

test('PDF nota dinas menampilkan tiga kotak disposisi kosong', function () {
    $nota = makeNotaDinas([
        'perihal'         => 'Nota Dengan Disposisi Lama',
        'status'          => NotaDinas::STATUS_TERSIMPAN,
        'disposisi_kabid' => 'Catatan rahasia kabid',
        'disposisi_sekban' => 'Catatan rahasia sekban',
        'disposisi_kaban' => 'Catatan rahasia kaban',
    ]);
    $nota->load(['pegawais', 'dari', 'subKegiatan']);

    $html = view('pages.nota_dinas.pdf', ['nota' => $nota])->render();

    expect($html)->toContain('KEPALA BIDANG PAD.');
    expect($html)->toContain('SEKRETARIS BPKAD.');
    expect($html)->toContain('KEPALA BPKAD.');

    // Teks disposisi dari database tidak boleh ikut tercetak.
    expect($html)->not->toContain('Catatan rahasia kabid');
    expect($html)->not->toContain('Catatan rahasia sekban');
    expect($html)->not->toContain('Catatan rahasia kaban');
});

test('halaman preview tidak menampilkan tombol atau panel approval', function () {
    $user = notaAdminUser();
    $nota = makeNotaDinas(['perihal' => 'Preview Tanpa Approval']);

    $response = $this->actingAs($user)->get(route('nota-dinas.preview', $nota->id));

    $response->assertStatus(200);
    $response->assertDontSee('Verifikasi Kabid');
    $response->assertDontSee('Verifikasi Sekban');
    $response->assertDontSee('ACC Kaban');
    $response->assertDontSee('Kirim Revisi');
});

// ==========================================
// ARSIP
// ==========================================

test('nota dinas langsung muncul di arsip tanpa menunggu ACC Kaban', function () {
    $user = notaAdminUser();
    $nota = makeNotaDinas([
        'perihal' => 'Nota Arsip Tanpa ACC',
        'status'  => NotaDinas::STATUS_TERSIMPAN,
    ]);

    $response = $this->actingAs($user)->get(route('arsip'));

    $response->assertStatus(200);
    $response->assertSee('Nota Arsip Tanpa ACC');
});

// ==========================================
// ROUTE APPROVAL SUDAH DIHAPUS
// ==========================================

test('route approval nota dinas sudah tidak tersedia', function () {
    expect(Route::has('nota-dinas.approve-kasubid'))->toBeFalse();
    expect(Route::has('nota-dinas.approve-kabid'))->toBeFalse();
    expect(Route::has('nota-dinas.approve-sekban'))->toBeFalse();
    expect(Route::has('nota-dinas.approve-kaban'))->toBeFalse();
    expect(Route::has('nota-dinas.revisi-kabid'))->toBeFalse();
    expect(Route::has('nota-dinas.revisi-sekban'))->toBeFalse();
    expect(Route::has('nota-dinas.revisi-kaban'))->toBeFalse();
    expect(Route::has('nota-dinas.reject-kabid'))->toBeFalse();
    expect(Route::has('nota-dinas.reject-sekban'))->toBeFalse();
    expect(Route::has('nota-dinas.reject-kaban'))->toBeFalse();
});
