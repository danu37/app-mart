<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const stats = [
    {
        title: 'Total Pendapatan',
        value: 'Rp 45.850.000',
        change: '+12.5%',
        isPositive: true,
        tag: 'KEUANGAN',
        icon: 'cash',
    },
    {
        title: 'Total Pesanan',
        value: '1.240 Transaksi',
        change: '+8.2%',
        isPositive: true,
        tag: 'PENJUALAN',
        icon: 'cart',
    },
    {
        title: 'Total Pelanggan',
        value: '850 Pengguna',
        change: '+5.4%',
        isPositive: true,
        tag: 'PELANGGAN',
        icon: 'users',
    },
    {
        title: 'Produk Terjual',
        value: '3.420 Item',
        change: '-2.1%',
        isPositive: false,
        tag: 'ELEKTRONIK',
        icon: 'box',
    }
];

const topProducts = [
    { name: 'Laptop ASUS ROG', category: 'ELEKTRONIK', sold: 120, total: 'Rp 25.000.000', stock: 50 },
    { name: 'Keyboard Mekanik Keychron', category: 'AKSESORIS', sold: 85, total: 'Rp 1.200.000', stock: 100 },
    { name: 'Mouse Nirkabel Logitech', category: 'AKSESORIS', sold: 240, total: 'Rp 150.000', stock: 200 },
    { name: 'Monitor LG 24 Inci', category: 'ELEKTRONIK', sold: 64, total: 'Rp 2.000.000', stock: 75 },
];
</script>

<template>
    <Head title="Dashboard & Statistik" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="font-['Sora'] font-bold text-2xl text-[#1C1E1B] leading-tight">
                        Statistik & Ringkasan Toko
                    </h2>
                    <p class="text-sm text-[#5B5F58] mt-1">
                        Ikhtisar performa penjualan, pelanggan, dan aktivitas toko Anda.
                    </p>
                </div>
                <div class="flex items-center gap-2.5">
                    <Link
                        :href="route('pelanggan.index')"
                        class="px-4 py-2 bg-white hover:bg-[#F3F3EE] text-[#1C1E1B] border border-[#E1E2D9] font-['Sora'] font-semibold text-xs rounded-md transition-colors shadow-2xs"
                    >
                        Data Pelanggan &rarr;
                    </Link>
                    <Link
                        :href="route('backup.index')"
                        class="px-4 py-2 bg-[#1C1E1B] hover:bg-[#33352F] text-white font-['Sora'] font-semibold text-xs rounded-md transition-colors shadow-2xs"
                    >
                        Backup Database
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div 
                        v-for="(item, index) in stats" 
                        :key="index"
                        class="bg-white rounded-lg border border-[#E1E2D9] p-5 shadow-2xs hover:border-[#1C1E1B] transition-colors flex flex-col justify-between"
                    >
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[10px] font-bold font-['IBM_Plex_Mono'] tracking-wider px-2 py-0.5 rounded bg-[#F3F3EE] text-[#5B5F58] border border-[#E1E2D9] uppercase">
                                {{ item.tag }}
                            </span>
                            <span :class="['text-xs font-bold font-[\'IBM_Plex_Mono\'] px-2 py-0.5 rounded', item.isPositive ? 'bg-[#0C8B7C]/10 text-[#0C8B7C]' : 'bg-[#FF5A1F]/10 text-[#FF5A1F]']">
                                {{ item.change }}
                            </span>
                        </div>

                        <div class="my-2">
                            <p class="text-xs font-medium text-[#5B5F58] uppercase tracking-wide">{{ item.title }}</p>
                            <h3 class="text-2xl font-bold font-['IBM_Plex_Mono'] text-[#1C1E1B] mt-1">{{ item.value }}</h3>
                        </div>

                        <div class="mt-4 pt-3 border-t border-[#E1E2D9] flex items-center justify-between text-xs text-[#5B5F58]">
                            <span>Status Terkini</span>
                            <span class="font-['IBM_Plex_Mono'] font-medium text-[#1C1E1B] flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#0C8B7C]"></span>
                                Sinkron
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Middle Grid: Chart & Quick Actions -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Chart Section -->
                    <div class="lg:col-span-2 bg-white rounded-lg border border-[#E1E2D9] p-6 shadow-2xs">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-[#E1E2D9]">
                            <div>
                                <h3 class="font-['Sora'] font-bold text-[#1C1E1B] text-lg">Tren Transaksi Mingguan</h3>
                                <p class="text-xs text-[#5B5F58] mt-0.5">Aktivitas pesanan belanja selama 7 hari terakhir</p>
                            </div>
                            <span class="text-[11px] font-semibold font-['IBM_Plex_Mono'] text-[#0C8B7C] bg-[#0C8B7C]/10 px-2.5 py-1 rounded border border-[#0C8B7C]/20">
                                ● REALTIME
                            </span>
                        </div>

                        <div class="h-60 bg-[#F3F3EE] rounded-lg p-5 flex items-end justify-between gap-3 border border-dashed border-[#D8D9CF]">
                            <div v-for="(day, idx) in ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']" :key="idx" class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                                <div 
                                    class="w-full max-w-[42px] bg-[#1C1E1B] group-hover:bg-[#FF5A1F] rounded-t transition-colors relative"
                                    :style="{ height: `${[45, 60, 80, 50, 95, 75, 85][idx]}%` }"
                                >
                                    <div class="opacity-0 group-hover:opacity-100 absolute -top-7 left-1/2 -translate-x-1/2 bg-[#1C1E1B] text-white text-[10px] font-['IBM_Plex_Mono'] font-bold py-0.5 px-1.5 rounded pointer-events-none transition-opacity">
                                        {{ [45, 60, 80, 50, 95, 75, 85][idx] }}%
                                    </div>
                                </div>
                                <span class="text-xs text-[#5B5F58] font-['Sora'] font-medium">{{ day }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg border border-[#E1E2D9] p-6 shadow-2xs flex flex-col justify-between">
                        <div>
                            <h3 class="font-['Sora'] font-bold text-[#1C1E1B] text-lg mb-1">Aksi Cepat Admin</h3>
                            <p class="text-xs text-[#5B5F58] mb-5">Pintasan menuju stored procedure & data master toko</p>

                            <div class="space-y-3">
                                <Link 
                                    :href="route('pelanggan.index')" 
                                    class="flex items-center justify-between p-3.5 rounded-lg border border-[#E1E2D9] bg-[#F3F3EE] hover:border-[#1C1E1B] transition-colors group"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-md bg-[#1C1E1B] text-white flex items-center justify-center text-xs font-bold">
                                            👥
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold font-['Sora'] text-[#1C1E1B]">Riwayat Pelanggan</p>
                                            <p class="text-[11px] text-[#5B5F58]">Panggil Stored Procedure SP7</p>
                                        </div>
                                    </div>
                                    <span class="text-[#FF5A1F] font-bold text-sm group-hover:translate-x-1 transition-transform">&rarr;</span>
                                </Link>

                                <Link 
                                    :href="route('backup.index')" 
                                    class="flex items-center justify-between p-3.5 rounded-lg border border-[#E1E2D9] bg-[#F3F3EE] hover:border-[#1C1E1B] transition-colors group"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-md bg-[#FF5A1F] text-white flex items-center justify-center text-xs font-bold">
                                            💾
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold font-['Sora'] text-[#1C1E1B]">Backup & Restore</p>
                                            <p class="text-[11px] text-[#5B5F58]">Unduh & pulihkan file .sql</p>
                                        </div>
                                    </div>
                                    <span class="text-[#FF5A1F] font-bold text-sm group-hover:translate-x-1 transition-transform">&rarr;</span>
                                </Link>
                            </div>
                        </div>

                        <div class="pt-5 border-t border-[#E1E2D9] mt-4">
                            <Link
                                :href="route('home')"
                                class="w-full inline-block py-2.5 bg-[#1C1E1B] hover:bg-[#33352F] text-white rounded-md text-xs font-semibold font-['Sora'] text-center transition-colors shadow-2xs"
                            >
                                Kunjungi Toko Online
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="bg-white rounded-lg border border-[#E1E2D9] shadow-2xs overflow-hidden">
                    <div class="p-6 border-b border-[#E1E2D9] flex items-center justify-between">
                        <div>
                            <h3 class="font-['Sora'] font-bold text-[#1C1E1B] text-lg">Ringkasan Produk Terlaris</h3>
                            <p class="text-xs text-[#5B5F58] mt-0.5">Daftar item dengan performa penjualan tertinggi</p>
                        </div>
                        <span class="text-xs font-semibold font-['IBM_Plex_Mono'] px-2.5 py-1 bg-[#F3F3EE] text-[#1C1E1B] border border-[#E1E2D9] rounded">
                            {{ topProducts.length }} ITEM
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs text-[#1C1E1B]">
                            <thead>
                                <tr class="bg-[#F3F3EE] border-b border-[#E1E2D9] text-[11px] font-semibold text-[#5B5F58] uppercase font-['Sora']">
                                    <th class="py-3.5 px-6">Produk</th>
                                    <th class="py-3.5 px-6">Kategori</th>
                                    <th class="py-3.5 px-6">Terjual</th>
                                    <th class="py-3.5 px-6">Sisa Stok</th>
                                    <th class="py-3.5 px-6 text-right">Total Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E1E2D9]">
                                <tr v-for="(product, idx) in topProducts" :key="idx" class="hover:bg-[#F3F3EE]/60 transition-colors">
                                    <td class="py-4 px-6 font-semibold text-[#1C1E1B] font-['Sora']">{{ product.name }}</td>
                                    <td class="py-4 px-6">
                                        <span class="text-[10px] font-bold font-['IBM_Plex_Mono'] px-2 py-0.5 rounded border uppercase bg-[#F3F3EE] text-[#5B5F58] border-[#E1E2D9]">
                                            {{ product.category }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-medium text-[#5B5F58] font-['IBM_Plex_Mono']">{{ product.sold }} Unit</td>
                                    <td class="py-4 px-6">
                                        <span class="text-[#0C8B7C] bg-[#0C8B7C]/10 border border-[#0C8B7C]/20 px-2 py-0.5 rounded font-['IBM_Plex_Mono'] text-[11px] font-medium">
                                            Stok {{ product.stock }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-bold font-['IBM_Plex_Mono'] text-[#1C1E1B] text-right">{{ product.total }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>