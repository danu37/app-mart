<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_produk', 'like', "%{$s}%")
                    ->orWhere('kategori', 'like', "%{$s}%");
            });
        }

        if ($request->filled('kategori') && $request->kategori !== 'Semua') {
            $query->where('kategori', $request->kategori);
        }

        $products   = $query->orderByDesc('id_produk')->get();
        $categories = Produk::select('kategori')->distinct()->pluck('kategori')->filter()->values();

        return Inertia::render('Admin/Products/Index', [
            'products'   => $products,
            'categories' => $categories,
            'filters'    => $request->only(['search', 'kategori']),
        ]);
    }

    public function create()
    {
        $categories = Produk::select('kategori')->distinct()->pluck('kategori')->filter()->values();

        return Inertia::render('Admin/Products/Form', [
            'product'    => null,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:150',
            'kategori'    => 'required|string|max:100',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
            'gambar'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only(['nama_produk', 'kategori', 'harga', 'stok']);

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('produk', 'public');
            $data['gambar'] = '/storage/' . $path;
        }

        Produk::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $product    = Produk::findOrFail($id);
        $categories = Produk::select('kategori')->distinct()->pluck('kategori')->filter()->values();

        return Inertia::render('Admin/Products/Form', [
            'product'    => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, $id)
    {
        $product = Produk::findOrFail($id);

        $request->validate([
            'nama_produk' => 'required|string|max:150',
            'kategori'    => 'required|string|max:100',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
            'gambar'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only(['nama_produk', 'kategori', 'harga', 'stok']);

        if ($request->hasFile('gambar')) {
            if ($product->gambar && str_starts_with($product->gambar, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $product->gambar));
            }
            $path = $request->file('gambar')->store('produk', 'public');
            $data['gambar'] = '/storage/' . $path;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $product = Produk::findOrFail($id);
        if ($product->gambar && str_starts_with($product->gambar, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $product->gambar));
        }
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
