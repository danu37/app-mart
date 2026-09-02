<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    stats: Object,
    recentOrders: Array,
    topProducts: Array,
    ordersByStatus: Object,
});

const formatRupiah = (val) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(val || 0);
};

const formatDate = (dateStr) => {
    if (!dateStr) return "-";
    return new Date(dateStr).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
    });
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-col sm:flex-row sm:items-center justify-between gap-4"
            >
                <div>
                    <h2
                        class="font-['Sora'] font-bold text-2xl text-[#1C1E1B] leading-tight"
                    >
                        Admin Dashboard
                    </h2>
                    <p class="text-sm text-[#5B5F58] mt-1">
                        Ikhtisar performa penjualan, pesanan, dan produk.
                    </p>
                </div>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                <!-- Stat Cards -->
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5"
                >
                    <div
                        class="bg-white rounded-lg border border-[#E1E2D9] p-5 shadow-2xs hover:border-[#1C1E1B] transition-colors"
                    >
                        <p
                            class="text-xs font-medium text-[#5B5F58] uppercase tracking-wide"
                        >
                            Total Pendapatan
                        </p>
                        <h1
                            class="text-sm md:text-base lg:text-lg font-bold font-['IBM_Plex_Mono'] text-[#1C1E1B] mt-1 truncate"
                            :title="formatRupiah(stats.totalRevenue)"
                        >
                            {{ formatRupiah(stats.totalRevenue) }}
                        </h1>
                    </div>
                    <div
                        class="bg-white rounded-lg border border-[#E1E2D9] p-5 shadow-2xs hover:border-[#1C1E1B] transition-colors"
                    >
                        <p
                            class="text-xs font-medium text-[#5B5F58] uppercase tracking-wide"
                        >
                            Total Pesanan
                        </p>
                        <h3
                            class="text-2xl font-bold font-['IBM_Plex_Mono'] text-[#1C1E1B] mt-1"
                        >
                            {{ stats.totalOrders }}
                        </h3>
                    </div>
                    <div
                        class="bg-white rounded-lg border border-[#E1E2D9] p-5 shadow-2xs hover:border-[#1C1E1B] transition-colors"
                    >
                        <p
                            class="text-xs font-medium text-[#5B5F58] uppercase tracking-wide"
                        >
                            Total Pelanggan
                        </p>
                        <h3
                            class="text-2xl font-bold font-['IBM_Plex_Mono'] text-[#1C1E1B] mt-1"
                        >
                            {{ stats.totalCustomers }}
                        </h3>
                    </div>
                    <div
                        class="bg-white rounded-lg border border-[#E1E2D9] p-5 shadow-2xs hover:border-[#1C1E1B] transition-colors"
                    >
                        <p
                            class="text-xs font-medium text-[#5B5F58] uppercase tracking-wide"
                        >
                            Produk Tersedia
                        </p>
                        <h3
                            class="text-2xl font-bold font-['IBM_Plex_Mono'] text-[#1C1E1B] mt-1"
                        >
                            {{ stats.totalProducts }}
                        </h3>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div
                    class="bg-white rounded-lg border border-[#E1E2D9] shadow-2xs overflow-hidden"
                >
                    <div
                        class="p-6 border-b border-[#E1E2D9] flex items-center justify-between"
                    >
                        <div>
                            <h3
                                class="font-['Sora'] font-bold text-[#1C1E1B] text-lg"
                            >
                                Pesanan Terbaru
                            </h3>
                            <p class="text-xs text-[#5B5F58] mt-0.5">
                                Daftar 10 transaksi terakhir yang masuk.
                            </p>
                        </div>
                        <Link
                            :href="route('admin.orders.index')"
                            class="text-xs font-semibold text-brand-orange-600 hover:underline"
                        >
                            Lihat Semua &rarr;
                        </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table
                            class="w-full text-left border-collapse text-xs text-[#1C1E1B]"
                        >
                            <thead
                                class="bg-[#F3F3EE] border-b border-[#E1E2D9] text-[11px] font-semibold text-[#5B5F58] uppercase font-['Sora']"
                            >
                                <tr>
                                    <th class="py-3.5 px-6">ID Transaksi</th>
                                    <th class="py-3.5 px-6">Pelanggan</th>
                                    <th class="py-3.5 px-6">Tanggal</th>
                                    <th class="py-3.5 px-6">Total Belanja</th>
                                    <th class="py-3.5 px-6">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E1E2D9]">
                                <tr
                                    v-for="order in recentOrders"
                                    :key="order.id_transaksi"
                                    class="hover:bg-[#F3F3EE]/60 transition-colors"
                                >
                                    <td
                                        class="py-4 px-6 font-bold font-['IBM_Plex_Mono']"
                                    >
                                        #{{ order.id_transaksi }}
                                    </td>
                                    <td class="py-4 px-6 font-semibold">
                                        {{ order.nama_pelanggan }}
                                    </td>
                                    <td class="py-4 px-6 text-[#5B5F58]">
                                        {{ formatDate(order.tanggal) }}
                                    </td>
                                    <td
                                        class="py-4 px-6 font-bold font-['IBM_Plex_Mono'] text-right"
                                    >
                                        {{ formatRupiah(order.total_belanja) }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span
                                            class="px-2 py-0.5 rounded text-[11px] font-medium"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800 border border-yellow-200':
                                                    order.status === 'pending',
                                                'bg-green-100 text-green-800 border border-green-200':
                                                    order.status === 'paid',
                                                'bg-blue-100 text-blue-800 border border-blue-200':
                                                    order.status ===
                                                        'processing' ||
                                                    order.status === 'shipped',
                                                'bg-emerald-100 text-emerald-800 border border-emerald-200':
                                                    order.status === 'done',
                                                'bg-red-100 text-red-800 border border-red-200':
                                                    order.status ===
                                                    'cancelled',
                                            }"
                                        >
                                            {{ order.status_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!recentOrders.length">
                                    <td
                                        colspan="5"
                                        class="py-6 text-center text-[#5B5F58]"
                                    >
                                        Belum ada pesanan terbaru.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
