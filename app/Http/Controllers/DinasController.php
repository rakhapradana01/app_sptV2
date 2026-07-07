<?php

namespace App\Http\Controllers;

use App\Models\Dinas;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DinasController extends Controller
{
    public function index()
    {
        $dinas = Dinas::latest()->paginate(10);
        return view('pages.master.dinas.index', compact('dinas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_dinas' => 'required|string|max:255|unique:dinas,nama_dinas'
        ]);

        Dinas::create($request->all());

        return redirect()->route('dinas.index')->with('success', 'Dinas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $dinas = Dinas::findOrFail($id);
        $request->validate([
            'nama_dinas' => 'required|string|max:255|unique:dinas,nama_dinas,' . $id
        ]);

        $dinas->update($request->all());

        return redirect()->route('dinas.index')->with('success', 'Dinas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $dinas = Dinas::findOrFail($id);
        $dinas->delete();

        return redirect()->route('dinas.index')->with('success', 'Dinas berhasil dihapus.');
    }

    /**
     * Import Dinas dari file Excel (.xlsx / .xls)
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls|max:2048',
        ], [
            'file_excel.required' => 'File Excel wajib dipilih.',
            'file_excel.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file_excel.max'      => 'Ukuran file maksimal 2 MB.',
        ]);

        $filePath    = $request->file('file_excel')->getRealPath();
        $spreadsheet = IOFactory::load($filePath);
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, true);

        $berhasil          = 0;
        $gagal             = [];
        $dinasSudahDiproses = [];

        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex === 1) continue; // Lewati header

            $namaDinas = trim($row['A'] ?? '');

            if (empty($namaDinas)) continue;

            if (in_array(strtolower($namaDinas), $dinasSudahDiproses)) {
                $gagal[] = "Baris {$rowIndex}: Nama Dinas '{$namaDinas}' muncul duplikat dalam file.";
                continue;
            }

            if (Dinas::where('nama_dinas', $namaDinas)->exists()) {
                $gagal[] = "Baris {$rowIndex}: Nama Dinas '{$namaDinas}' sudah terdaftar di sistem.";
                continue;
            }

            $dinasSudahDiproses[] = strtolower($namaDinas);

            Dinas::create([
                'nama_dinas' => $namaDinas,
            ]);

            $berhasil++;
        }

        $pesan = "Import selesai: {$berhasil} Dinas berhasil ditambahkan.";
        if (!empty($gagal)) {
            $pesan .= ' ' . count($gagal) . ' baris dilewati.';
            return redirect()->route('dinas.index')
                ->with('import_success', $pesan)
                ->with('import_errors', $gagal);
        }

        return redirect()->route('dinas.index')->with('success', $pesan);
    }

    /**
     * Unduh template Excel untuk import Dinas
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Dinas');

        $headers = ['A' => 'Nama Dinas'];
        foreach ($headers as $col => $label) {
            $sheet->setCellValue("{$col}1", $label);
        }

        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        $contoh = [
            ['Dinas Pendapatan Daerah'],
            ['Dinas Pekerjaan Umum'],
        ];
        foreach ($contoh as $i => $baris) {
            $row = $i + 2;
            $sheet->setCellValue("A{$row}", $baris[0]);
            $bg = $i % 2 === 0 ? 'EFF6FF' : 'FFFFFF';
            $sheet->getStyle("A{$row}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DBEAFE']]],
            ]);
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->freezePane('A2');

        $writer   = new Xlsx($spreadsheet);
        $filename = 'template_import_dinas.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
