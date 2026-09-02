<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
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
                    'created_at'    => $t->created_at,
                ]);
        }

        return Inertia::render('Customer/Dashboard', [
            'orders'    => $orders,
            'pelanggan' => $pelanggan,
        ]);
    }
}
