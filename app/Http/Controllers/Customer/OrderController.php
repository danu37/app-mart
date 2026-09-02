<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $user      = Auth::user();
        $pelanggan = $user->pelanggan;

        $orders = collect([]);
        if ($pelanggan) {
            $orders = Transaksi::with('detailTransaksi.produk')
                ->where('id_pelanggan', $pelanggan->id_pelanggan)
                ->orderByDesc('id_transaksi')
                ->get()
                ->map(fn($t) => [
                    'id_transaksi'  => $t->id_transaksi,
                    'tanggal'       => $t->tanggal,
                    'total_belanja' => $t->total_belanja,
                    'status'        => $t->status,
                    'status_label'  => $t->status_label,
                    'items_count'   => $t->detailTransaksi->count(),
                    'items'         => $t->detailTransaksi->map(fn($d) => [
                        'nama_produk' => $d->produk?->nama_produk,
                        'qty'         => $d->qty,
                        'subtotal'    => $d->subtotal,
                    ]),
                    'created_at'    => $t->created_at,
                ]);
        }

        return Inertia::render('Customer/Orders', [
            'orders' => $orders,
        ]);
    }

    public function show($id)
    {
        $user      = Auth::user();
        $pelanggan = $user->pelanggan;

        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksi.produk'])->findOrFail($id);

        // Ensure customer can only view their own orders
        if (!$pelanggan || $transaksi->id_pelanggan !== $pelanggan->id_pelanggan) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        return Inertia::render('Customer/OrderDetail', [
            'order' => [
                'id_transaksi'  => $transaksi->id_transaksi,
                'tanggal'       => $transaksi->tanggal,
                'total_belanja' => $transaksi->total_belanja,
                'status'        => $transaksi->status,
                'status_label'  => $transaksi->status_label,
                'created_at'    => $transaksi->created_at,
                'pelanggan'     => $transaksi->pelanggan,
                'items'         => $transaksi->detailTransaksi->map(fn($d) => [
                    'id_detail'   => $d->id_detail,
                    'nama_produk' => $d->produk?->nama_produk ?? '-',
                    'kategori'    => $d->produk?->kategori ?? '-',
                    'harga'       => $d->produk?->harga ?? 0,
                    'gambar'      => $d->produk?->gambar,
                    'qty'         => $d->qty,
                    'subtotal'    => $d->subtotal,
                ]),
            ],
        ]);
    }
}
