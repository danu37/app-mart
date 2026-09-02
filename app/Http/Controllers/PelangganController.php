<?php

namespace App\Http\Controllers;

use Exception;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use App\Models\Pelanggan;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggan = Schema::hasTable('tbl_pelanggan') ? Pelanggan::with('user')->withCount('transaksi')->paginate(10) : collect([]);

        return Inertia::render('Pelanggan/Index', [
            'pelangganList' => $pelanggan
        ]);
    }

    public function exportPdf()
    {
        $pelanggan = Schema::hasTable('tbl_pelanggan') ? Pelanggan::with('user')->withCount('transaksi')->get() : collect([]);

        $pdf = Pdf::loadView('exports.pelanggan_pdf', [
            'pelanggan' => $pelanggan
        ]);

        return $pdf->download('Data_Pelanggan.pdf');
    }

    public function riwayat($id)
    {
        try {
            if (!Schema::hasTable('tbl_transaksi') && !Schema::hasTable('tbl_pelanggan')) {
                return response()->json([]);
            }
            // Memanggil Stored Procedure yang telah dibuat pada Praktikum 7
            $riwayat = DB::select('CALL LihatRiwayatBelanja(?)', [$id]);
            return response()->json($riwayat);
        } catch (Exception $e) {
            // Fallback jika stored procedure belum tersedia di DB testing
            try {
                if (Schema::hasTable('tbl_transaksi')) {
                    $riwayat = DB::table('tbl_transaksi')
                        ->leftJoin('tbl_pelanggan', 'tbl_transaksi.id_pelanggan', '=', 'tbl_pelanggan.id_pelanggan')
                        ->where('tbl_transaksi.id_pelanggan', $id)
                        ->orderByDesc('tbl_transaksi.id_transaksi')
                        ->limit(10)
                        ->select('tbl_transaksi.id_transaksi', 'tbl_transaksi.tanggal', 'tbl_pelanggan.nama_pelanggan', 'tbl_pelanggan.kota', 'tbl_transaksi.total_belanja')
                        ->get();
                    return response()->json($riwayat);
                }
            } catch (Exception $fallbackEx) {
                // ignore
            }

            return response()->json([
                'error' => 'Gagal memanggil stored procedure: ' . $e->getMessage()
            ], 500);
        }
    }

    public function struk($id)
    {
        try {
            if (!Schema::hasTable('tbl_transaksi')) {
                return response()->json([
                    'transaksi' => [
                        'id_transaksi' => $id,
                        'tanggal' => date('Y-m-d'),
                        'total_belanja' => 0,
                        'id_pelanggan' => 1,
                        'nama_pelanggan' => 'Pelanggan Umum',
                        'kota' => 'Indonesia'
                    ],
                    'items' => []
                ]);
            }

            $transaksi = DB::table('tbl_transaksi')
                ->leftJoin('tbl_pelanggan', 'tbl_transaksi.id_pelanggan', '=', 'tbl_pelanggan.id_pelanggan')
                ->where('tbl_transaksi.id_transaksi', $id)
                ->select(
                    'tbl_transaksi.id_transaksi',
                    'tbl_transaksi.tanggal',
                    'tbl_transaksi.total_belanja',
                    'tbl_pelanggan.id_pelanggan',
                    'tbl_pelanggan.nama_pelanggan',
                    'tbl_pelanggan.kota'
                )
                ->first();

            if (!$transaksi) {
                return response()->json(['error' => 'Transaksi tidak ditemukan'], 404);
            }

            $items = collect([]);
            if (Schema::hasTable('tbl_detail_transaksi') && Schema::hasTable('tbl_produk')) {
                $items = DB::table('tbl_detail_transaksi')
                    ->join('tbl_produk', 'tbl_detail_transaksi.id_produk', '=', 'tbl_produk.id_produk')
                    ->where('tbl_detail_transaksi.id_transaksi', $id)
                    ->select(
                        'tbl_detail_transaksi.id_detail',
                        'tbl_detail_transaksi.id_produk',
                        'tbl_detail_transaksi.qty',
                        'tbl_detail_transaksi.subtotal',
                        'tbl_produk.nama_produk',
                        'tbl_produk.kategori',
                        'tbl_produk.harga'
                    )
                    ->get();
            }

            return response()->json([
                'transaksi' => $transaksi,
                'items' => $items
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Gagal memuat data struk belanja: ' . $e->getMessage()
            ], 500);
        }
    }
}
