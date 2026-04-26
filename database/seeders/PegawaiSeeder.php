<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $pegawais = [
            [
                'nama' => 'ADYA FERINA, S.E., M.Ak',
                'nip' => '19860206 201101 2 005',
                'jabatan' => 'Kepala Bidang Perencanaan Anggaran Daerah',
                'pangkat' => 'Penata Tk.I (III/d)',
            ],
            [
                'nama' => 'YENNI NURRAHMI, S.E., M.M.',
                'nip' => '19810503 200501 2 017',
                'jabatan' => 'Kepala Sub Bidang Perencanaan Anggaran Daerah III',
                'pangkat' => 'Pembina (IV/a)',
            ],
            [
                'nama' => 'MUHAMMAD KHARIS ELYANI, S.E., M.M.',
                'nip' => '19870304 201101 1 001',
                'jabatan' => 'Kepala Sub Bidang Perencanaan Anggaran Daerah I',
                'pangkat' => 'Penata Tk.I (III/d)',
            ],
            [
                'nama' => 'ARIEF HIDAYAT, S.E, M.E',
                'nip' => '19910330 201903 1 014',
                'jabatan' => 'Kepala Sub Bidang Perencanaan Anggaran Daerah II',
                'pangkat' => 'Penata Muda Tk.I (III/b)',
            ],
            [
                'nama' => 'DODI SETIYAWAN, SE, M.M.',
                'nip' => '19771113 201001 1 003',
                'jabatan' => 'Penelaah Teknis Kebijakan',
                'pangkat' => 'Penata Tk.I (III/d)',
            ],
            [
                'nama' => 'NORMILA SARI, S.E',
                'nip' => '19801221 201001 2 003',
                'jabatan' => 'Penelaah Teknis Kebijakan',
                'pangkat' => 'Penata Tk.I (III/d)',
            ],
            [
                'nama' => 'REGA SALVIAN BHASKARA, SE',
                'nip' => '19860819 201001 1 017',
                'jabatan' => 'Perencana Ahli Muda',
                'pangkat' => 'Penata Tk.I (III/d)',
            ],
            [
                'nama' => 'M. RIFQI FIRDAUS, S.Kom, M.M.',
                'nip' => '19860523 201101 1 002',
                'jabatan' => 'Perencana Ahli Pertama',
                'pangkat' => 'Penata Tk.I (III/d)',
            ],
            [
                'nama' => 'LEO KUSANDHI ADINUGROHO, S.E, M.M',
                'nip' => '19830728 201101 1 001',
                'jabatan' => 'Penelaah Teknis Kebijakan',
                'pangkat' => 'Penata Tk.I (III/d)',
            ],
            [
                'nama' => 'RAUDATUL ZANNAH, S.Kom',
                'nip' => '19850324 201101 2 003',
                'jabatan' => 'Penelaah Teknis Kebijakan',
                'pangkat' => 'Penata Tk.I (III/d)',
            ],
            [
                'nama' => 'MUHAMMAD AULIA AKBAR, S.IP, M.A.P',
                'nip' => '19960525 201808 1 001',
                'jabatan' => 'Penelaah Teknis Kebijakan',
                'pangkat' => 'Penata Muda Tk.I (III/b)',
            ],
            [
                'nama' => 'ADITYA ABIMANYU, S.Tr.IP',
                'nip' => '19991028 202108 1 001',
                'jabatan' => 'Penelaah Teknis Kebijakan',
                'pangkat' => 'Penata Muda Tk.I (III/b)',
            ],
            [
                'nama' => 'IRFANSYAH, SE',
                'nip' => '19941013 202012 1 016',
                'jabatan' => 'Perencana Ahli Pertama',
                'pangkat' => 'Penata Muda (III/a)',
            ],
            [
                'nama' => 'SITI AULIA, SE',
                'nip' => '19880829 202203 2 001',
                'jabatan' => 'Penelaah Teknis Kebijakan',
                'pangkat' => 'Penata Muda (III/B)',
            ],
            [
                'nama' => 'GALUH ANANDA SARI, S.M',
                'nip' => '19920122 201503 2 001',
                'jabatan' => 'Penelaah Teknis Kebijakan',
                'pangkat' => 'Penata Muda (III/a)',
            ],
            [
                'nama' => 'FAJAR QORIAWAN, A.Md',
                'nip' => '19841010 201101 1 003',
                'jabatan' => 'Pranata Komputer Mahir',
                'pangkat' => 'Penata Muda (III/a)',
            ],
            [
                'nama' => 'DILA JUSTITIANA, S.Ak',
                'nip' => '19980215 202101 2 001',
                'jabatan' => 'Penelaah Teknis Kebijakan',
                'pangkat' => 'Penata Muda (III/a)',
            ],
            [
                'nama' => 'TAUFIQURRAHMAN, A.Md',
                'nip' => '19891026 201503 1 002',
                'jabatan' => 'Pranata Komputer Terampil',
                'pangkat' => 'Pengatur (II/D)',
            ],
            [
                'nama' => 'M. RIZKI PRATAMA, S.Kom',
                'nip' => '19920626 202521 1 184',
                'jabatan' => 'Penata Layanan Operasional',
                'pangkat' => null,
            ],
            [
                'nama' => 'GUSTI DEVI OKTAVIA, S.M',
                'nip' => '20011021 202521 2 034',
                'jabatan' => 'Operator Layanan Operasional',
                'pangkat' => null,
            ],
            [
                'nama' => 'EDI MAULANA',
                'nip' => '19881222 202521 1 097',
                'jabatan' => 'Operator Layanan Operasional',
                'pangkat' => null,
            ],
            [
                'nama' => 'TRIA LEJAR TRIASIH',
                'nip' => '19931211 202521 2 128',
                'jabatan' => 'Operator Layanan Operasional',
                'pangkat' => null,
            ],
            [
                'nama' => 'FIRDA RIZQY ANANDA YULIASARI, S.Pd.',
                'nip' => null,
                'jabatan' => 'Petugas Layanan Operasional',
                'pangkat' => null,
            ],
            [
                'nama' => 'HASANUDDIN',
                'nip' => null,
                'jabatan' => 'Asisten Layanan Teknis Operasional',
                'pangkat' => null,
            ],
            [
                'nama' => 'RAKHA PRADANA SUSILO PUTRA, S.Kom',
                'nip' => null,
                'jabatan' => 'Asisten Layanan Teknis Operasional',
                'pangkat' => null,
            ],
            [
                'nama' => 'MUHAMMAD ADI FATRA',
                'nip' => null,
                'jabatan' => 'Pengemudi',
                'pangkat' => null,
            ],
        ];

        Pegawai::insert($pegawais);
    }
}
