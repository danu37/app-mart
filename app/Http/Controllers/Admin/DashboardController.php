<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts  = Produk::count();
        $totalCustomers = Pelanggan::count();
        $totalOrders    = Transaksi::count();
        $totalRevenue   = Transaksi::where('status', '!=', 'cancelled')->sum('total_belanja');

        $recentOrders = Transaksi::with('pelanggan')
            ->orderByDesc('id_transaksi')
            ->limit(10)
            ->get()
            ->map(fn($t) => [
                'id_transaksi'   => $t->id_transaksi,
                'tanggal'        => $t->tanggal,
                'total_belanja'  => $t->total_belanja,
                'status'         => $t->status,
                'status_label'   => $t->status_label,
                'nama_pelanggan' => $t->pelanggan?->nama_pelanggan ?? '-',
            ]);

        $topProducts = Produk::orderByDesc('stok')->limit(5)->get();

        $ordersByStatus = [
            'pending'    => Transaksi::where('status', 'pending')->count(),
            'paid'       => Transaksi::where('status', 'paid')->count(),
            'processing' => Transaksi::where('status', 'processing')->count(),
            'shipped'    => Transaksi::where('status', 'shipped')->count(),
            'done'       => Transaksi::where('status', 'done')->count(),
            'cancelled'  => Transaksi::where('status', 'cancelled')->count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalProducts'  => $totalProducts,
                'totalCustomers' => $totalCustomers,
                'totalOrders'    => $totalOrders,
                'totalRevenue'   => $totalRevenue,
            ],
            'recentOrders'   => $recentOrders,
            'topProducts'    => $topProducts,
            'ordersByStatus' => $ordersByStatus,
        ]);
    }
}
