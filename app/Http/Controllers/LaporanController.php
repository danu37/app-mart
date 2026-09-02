<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use App\Jobs\ProsesExportLaporan;
use App\Models\RiwayatExport;
use Carbon\Carbon;

class LaporanController extends Controller
{
    // 1. Menampilkan Halaman Laporan & Filter Data
    public function index(Request $request)
    {
        $tipe = $request->input('tipe_laporan', 'harian');
        $tanggal_awal = $request->input('tanggal_awal');
        $tanggal_akhir = $request->input('tanggal_akhir');
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = Transaksi::with('pelanggan');

        // Logika Filter Berdasarkan Tipe Laporan
        if ($tipe == 'harian') {
            $date = $tanggal_awal ?? date('Y-m-d');
            $query->whereDate('tanggal', $date);
        } elseif ($tipe == 'mingguan') {
            $start = $tanggal_awal ?? Carbon::now()->startOfWeek()->toDateString();
            $end = $tanggal_akhir ?? Carbon::now()->endOfWeek()->toDateString();
            $query->whereBetween('tanggal', [$start, $end]);
        } elseif ($tipe == 'bulanan') {
            $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
        } elseif ($tipe == 'tahunan') {
            $query->whereYear('tanggal', $tahun);
        }

        $transaksi = $query->orderBy('tanggal', 'desc')->get();
        $totalPendapatan = $transaksi->sum('total_belanja');

        // Jika request berasal dari tombol export
        if ($request->has('export')) {
            $exportTipe = $request->input('export');

            $dataExport = [
                'transaksi' => $transaksi,
                'total_pendapatan' => $totalPendapatan,
                'tipe' => $tipe,
                'periode' => $this->getPeriodeLabel($request)
            ];

            if ($exportTipe == 'pdf') {
                return $this->exportPDF($dataExport);
            } elseif ($exportTipe == 'excel') {
                return $this->exportExcel($transaksi);
            }
        }

        // Return ke tampilan frontend Inertia
        return Inertia::render('Laporan/Index', [
            'transaksi' => $transaksi,
            'totalPendapatan' => $totalPendapatan,
            'filters' => $request->only(['tipe_laporan', 'tanggal_awal', 'tanggal_akhir', 'bulan', 'tahun'])
        ]);
    }

    // Helper untuk membuat teks periode
    private function getPeriodeLabel($request)
    {
        $tipe = $request->input('tipe_laporan', 'harian');
        if ($tipe == 'harian') return "Tanggal: " . ($request->input('tanggal_awal') ?? date('Y-m-d'));
        if ($tipe == 'bulanan') return "Bulan: " . $request->input('bulan') . " Tahun: " . $request->input('tahun');
        return "Tipe: " . ucfirst($tipe);
    }

    // 2. Export ke PDF menggunakan DomPDF
    private function exportPDF($data)
    {
        $pdf = Pdf::loadView('exports.laporan_pdf', $data);
        return $pdf->download('Laporan_Transaksi_' . date('Y-m-d') . '.pdf');
    }

    // 3. Export ke Excel menggunakan Laravel Excel
    private function exportExcel($transaksi)
    {
        return Excel::download(new LaporanExport($transaksi), 'Laporan_Transaksi_' . date('Ymd_His') . '.xlsx');
    }


    // [NEW] API untuk trigger export background
    public function exportApi(Request $request)
    {
        $format = $request->input('format', 'excel');

        // Buat record riwayat berstatus pending
        $riwayat = RiwayatExport::create([
            'nama_file' => 'Laporan Transaksi ' . date('Y-m-d H:i'),
            'format' => $format,
            'status' => 'pending'
        ]);

        // Dispatch Job ke background
        ProsesExportLaporan::dispatch($request->all(), $format, $riwayat->id);

        return response()->json([
            'message' => 'Proses export ' . strtoupper($format) . ' sedang berjalan di latar belakang.',
            'riwayat_id' => $riwayat->id
        ]);
    }

    // [NEW] Endpoint untuk mengambil daftar riwayat export
    public function riwayatExport()
    {
        $riwayat = RiwayatExport::orderBy('created_at', 'desc')->get();
        return response()->json($riwayat);
    }
}
