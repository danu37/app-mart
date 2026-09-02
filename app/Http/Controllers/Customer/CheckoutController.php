<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $user      = Auth::user();
        $pelanggan = $user->pelanggan;

        // Cart items from session
        $cartItems = session('cart', []);

        // Enrich cart items with current product data
        $enrichedCart = [];
        foreach ($cartItems as $item) {
            $product = Produk::find($item['id_produk']);
            if ($product) {
                $enrichedCart[] = [
                    'id_produk'   => $product->id_produk,
                    'nama_produk' => $product->nama_produk,
                    'kategori'    => $product->kategori,
                    'harga'       => $product->harga,
                    'stok'        => $product->stok,
                    'gambar'      => $product->gambar,
                    'qty'         => min($item['qty'], $product->stok),
                ];
            }
        }

        return Inertia::render('Customer/Checkout', [
            'cartItems' => $enrichedCart,
            'pelanggan' => $pelanggan,
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|integer|exists:tbl_produk,id_produk',
            'qty'       => 'required|integer|min:1',
        ]);

        $product = Produk::findOrFail($request->id_produk);

        if ($product->stok < $request->qty) {
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        $cart = session('cart', []);

        $found = false;
        foreach ($cart as &$item) {
            if ($item['id_produk'] == $request->id_produk) {
                $item['qty'] += $request->qty;
                if ($item['qty'] > $product->stok) {
                    $item['qty'] = $product->stok;
                }
                $found = true;
                break;
            }
        }
        unset($item);

        if (!$found) {
            $cart[] = [
                'id_produk' => $request->id_produk,
                'qty'       => $request->qty,
            ];
        }

        session(['cart' => $cart]);

        return back()->with('success', "\"{$product->nama_produk}\" ditambahkan ke keranjang.");
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
            'cart.*.id_produk' => 'required|integer',
            'cart.*.qty'       => 'required|integer|min:0',
        ]);

        $cart = [];
        foreach ($request->cart as $item) {
            if ($item['qty'] > 0) {
                $cart[] = [
                    'id_produk' => $item['id_produk'],
                    'qty'       => $item['qty'],
                ];
            }
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|integer',
        ]);

        $cart = session('cart', []);
        $cart = array_values(array_filter($cart, fn($item) => $item['id_produk'] != $request->id_produk));
        session(['cart' => $cart]);

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function store(Request $request)
    {
        $user      = Auth::user();
        $pelanggan = $user->pelanggan;

        if (!$pelanggan) {
            return back()->with('error', 'Data pelanggan tidak ditemukan. Silakan lengkapi profil Anda.');
        }

        $cartItems = session('cart', []);
        if (empty($cartItems)) {
            return back()->with('error', 'Keranjang belanja kosong.');
        }

        DB::beginTransaction();
        try {
            $totalBelanja = 0;
            $details = [];

            foreach ($cartItems as $cartItem) {
                $product = Produk::lockForUpdate()->find($cartItem['id_produk']);

                if (!$product) {
                    throw new \Exception("Produk tidak ditemukan.");
                }

                if ($product->stok < $cartItem['qty']) {
                    throw new \Exception("Stok \"{$product->nama_produk}\" tidak mencukupi. Tersisa: {$product->stok}");
                }

                $subtotal = $product->harga * $cartItem['qty'];
                $totalBelanja += $subtotal;

                $details[] = [
                    'id_produk' => $product->id_produk,
                    'qty'       => $cartItem['qty'],
                    'subtotal'  => $subtotal,
                ];

                $product->decrement('stok', $cartItem['qty']);
            }

            $transaksi = Transaksi::create([
                'tanggal'       => now()->toDateString(),
                'id_pelanggan'  => $pelanggan->id_pelanggan,
                'total_belanja' => $totalBelanja,
                'status'        => 'paid',
            ]);

            foreach ($details as $detail) {
                DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_produk'    => $detail['id_produk'],
                    'qty'          => $detail['qty'],
                    'subtotal'     => $detail['subtotal'],
                ]);
            }

            DB::commit();

            session()->forget('cart');

            return redirect()->route('customer.orders.show', $transaksi->id_transaksi)
                ->with('success', 'Pesanan berhasil dibuat! Terima kasih telah berbelanja.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
