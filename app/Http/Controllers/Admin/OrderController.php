<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['pelanggan', 'detailTransaksi.produk']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('id_transaksi', 'like', "%{$s}%")
                    ->orWhereHas('pelanggan', fn($pq) => $pq->where('nama_pelanggan', 'like', "%{$s}%"));
            });
        }

        $orders = $query->orderByDesc('id_transaksi')->paginate(10)->withQueryString()
            ->through(fn($t) => [
                'id_transaksi'   => $t->id_transaksi,
                'tanggal'        => $t->tanggal,
                'total_belanja'  => $t->total_belanja,
                'status'         => $t->status,
                'status_label'   => $t->status_label,
                'nama_pelanggan' => $t->pelanggan?->nama_pelanggan ?? '-',
                'kota'           => $t->pelanggan?->kota ?? '-',
                'items_count'    => $t->detailTransaksi->count(),
                'created_at'     => $t->created_at,
            ]);

        return Inertia::render('Admin/Orders/Index', [
            'orders'  => $orders,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function exportPdf(Request $request)
    {
        $query = Transaksi::with(['pelanggan', 'detailTransaksi.produk']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('id_transaksi', 'like', "%{$s}%")
                    ->orWhereHas('pelanggan', fn($pq) => $pq->where('nama_pelanggan', 'like', "%{$s}%"));
            });
        }

        $transaksi = $query->orderByDesc('id_transaksi')->get();
        $totalPendapatan = $transaksi->sum('total_belanja');

        $pdf = Pdf::loadView('exports.laporan_pdf', [
            'transaksi' => $transaksi,
            'total_pendapatan' => $totalPendapatan,
            'tipe' => 'Seluruh Pesanan',
            'periode' => 'Status: ' . ($request->status ?? 'Semua')
        ]);

        return $pdf->download('Laporan_Pesanan.pdf');
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksi.produk'])->findOrFail($id);

        return Inertia::render('Admin/Orders/Show', [
            'order' => [
                'id_transaksi'   => $transaksi->id_transaksi,
                'tanggal'        => $transaksi->tanggal,
                'total_belanja'  => $transaksi->total_belanja,
                'status'         => $transaksi->status,
                'status_label'   => $transaksi->status_label,
                'created_at'     => $transaksi->created_at,
                'pelanggan'      => $transaksi->pelanggan,
                'items'          => $transaksi->detailTransaksi->map(fn($d) => [
                    'id_detail'   => $d->id_detail,
                    'nama_produk' => $d->produk?->nama_produk ?? '-',
                    'kategori'    => $d->produk?->kategori ?? '-',
                    'harga'       => $d->produk?->harga ?? 0,
                    'qty'         => $d->qty,
                    'subtotal'    => $d->subtotal,
                ]),
            ],
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,done,cancelled',
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
