<?php

namespace App\Jobs;

use App\Models\RiwayatExport;
use App\Models\Transaksi;
use App\Exports\LaporanExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProsesExportLaporan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filters;
    protected $format;
    protected $riwayatId;

    public function __construct($filters, $format, $riwayatId)
    {
        $this->filters = $filters;
        $this->format  = $format;
        $this->riwayatId = $riwayatId;
    }

    public function handle(): void
    {
        $riwayat = RiwayatExport::find($this->riwayatId);
        if (!$riwayat) return;

        $riwayat->update(['status' => 'processing']);

        try {
            $transaksi = $this->buildQuery()->get();

            $fileName = 'laporan_' . date('Y-m-d_His') . '_' . uniqid();
            $path     = '';

            if ($this->format === 'excel') {
                $fileName .= '.xlsx';
                $path      = 'exports/' . $fileName;
                // Excel::store default ke public disk
                Excel::store(new LaporanExport($transaksi), $path, 'public');
            } elseif ($this->format === 'pdf') {
                $fileName .= '.pdf';
                $path      = 'exports/' . $fileName;
                $pdf = Pdf::loadView('exports.laporan_pdf', [
                    'transaksi'       => $transaksi,
                    'total_pendapatan' => $transaksi->sum('total_belanja'),
                    'tipe'            => $this->filters['tipe_laporan'] ?? 'semua',
                    'periode'         => 'Export Asynchronous – ' . now()->format('d M Y H:i')
                ]);
                Storage::disk('public')->put($path, $pdf->output());
            }

            $riwayat->update([
                'status'   => 'completed',
                'file_url' => Storage::url($path),
            ]);
        } catch (\Exception $e) {
            $riwayat->update(['status' => 'failed']);
            throw $e;
        }
    }

    private function buildQuery()
    {
        $tipe         = $this->filters['tipe_laporan'] ?? 'harian';
        $tanggalAwal  = $this->filters['tanggal_awal'] ?? null;
        $tanggalAkhir = $this->filters['tanggal_akhir'] ?? null;
        $bulan        = $this->filters['bulan'] ?? date('m');
        $tahun        = $this->filters['tahun'] ?? date('Y');

        $query = Transaksi::with('pelanggan');

        if ($tipe === 'harian') {
            $date = $tanggalAwal ?? date('Y-m-d');
            $query->whereDate('tanggal', $date);
        } elseif ($tipe === 'mingguan') {
            $start = $tanggalAwal  ?? Carbon::now()->startOfWeek()->toDateString();
            $end   = $tanggalAkhir ?? Carbon::now()->endOfWeek()->toDateString();
            $query->whereBetween('tanggal', [$start, $end]);
        } elseif ($tipe === 'bulanan') {
            $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
        } elseif ($tipe === 'tahunan') {
            $query->whereYear('tanggal', $tahun);
        }

        return $query->orderBy('tanggal', 'desc');
    }
}
