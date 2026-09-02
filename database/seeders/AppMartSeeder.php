<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AppMartSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks to safely truncate
        Schema::disableForeignKeyConstraints();
        Pelanggan::truncate();
        Produk::truncate();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Create Admin
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@techstore.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // 2. Create Customers
        $customersData = [
            ['name' => 'Andi', 'email' => 'andi@mail.com', 'kota' => 'Jakarta'],
            ['name' => 'Bunga', 'email' => 'bunga@mail.com', 'kota' => 'Bandung'],
            ['name' => 'Candra', 'email' => 'candra@mail.com', 'kota' => 'Surabaya'],
        ];

        foreach ($customersData as $data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make('password'),
                'role'     => 'customer',
            ]);

            Pelanggan::create([
                'user_id'        => $user->id,
                'nama_pelanggan' => $data['name'],
                'kota'           => $data['kota'],
                'alamat'         => 'Jl. Raya ' . $data['kota'] . ' No. 1',
                'telepon'        => '08123456789',
            ]);
        }

        // 3. Products
        $produk = [
            ['nama_produk' => 'Laptop ASUS ROG', 'kategori' => 'Elektronik', 'harga' => 25000000, 'stok' => 12, 'gambar' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&q=80&w=800'],
            ['nama_produk' => 'MacBook Pro M3', 'kategori' => 'Elektronik', 'harga' => 32000000, 'stok' => 8, 'gambar' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&q=80&w=800'],
            ['nama_produk' => 'Dell XPS 15', 'kategori' => 'Elektronik', 'harga' => 28000000, 'stok' => 15, 'gambar' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&q=80&w=800'],
            ['nama_produk' => 'Lenovo ThinkPad X1', 'kategori' => 'Elektronik', 'harga' => 23000000, 'stok' => 10, 'gambar' => 'https://images.unsplash.com/photo-1587391892556-91e84ce00cc3?auto=format&fit=crop&q=80&w=800'],
            ['nama_produk' => 'Acer Predator Helios', 'kategori' => 'Elektronik', 'harga' => 26000000, 'stok' => 5, 'gambar' => 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?auto=format&fit=crop&q=80&w=800'],
            ['nama_produk' => 'Smartphone Xiaomi', 'kategori' => 'Elektronik', 'harga' => 3500000, 'stok' => 25, 'gambar' => 'https://images.unsplash.com/photo-1598327105666-5b89351cb315?auto=format&fit=crop&q=80&w=800'],
            ['nama_produk' => 'Headset Sony', 'kategori' => 'Aksesoris', 'harga' => 850000, 'stok' => 30, 'gambar' => 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?auto=format&fit=crop&q=80&w=800'],
            ['nama_produk' => 'Keyboard Mechanical', 'kategori' => 'Perangkat', 'harga' => 1200000, 'stok' => 20, 'gambar' => 'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&q=80&w=800'],
            ['nama_produk' => 'Monitor LG', 'kategori' => 'Elektronik', 'harga' => 4200000, 'stok' => 10, 'gambar' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&q=80&w=800'],
        ];

        foreach ($produk as $item) {
            Produk::create($item);
        }
    }
}
