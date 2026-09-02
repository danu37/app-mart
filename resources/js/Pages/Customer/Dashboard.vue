<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    orders: Array,
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
    <Head title="Customer Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2
                        class="font-['Sora'] font-bold text-2xl text-[#1C1E1B] leading-tight"
                    >
                        Riwayat Pesanan Anda
                    </h2>
                    <p class="text-sm text-[#5B5F58] mt-1">
                        Daftar transaksi dan status pembelian produk Anda di
                        TechStore.
                    </p>
                </div>
                <Link
                    :href="route('home')"
                    class="px-4 py-2 bg-[#1C1E1B] hover:bg-[#33352F] text-white font-['Sora'] font-semibold text-xs rounded-md transition-colors shadow-2xs"
                >
                    Belanja Lagi
                </Link>
            </div>
        </template>

        <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="bg-white rounded-lg border border-[#E1E2D9] shadow-2xs overflow-hidden"
            >
                <div class="overflow-x-auto">
                    <table
                        class="w-full text-left border-collapse text-xs text-[#1C1E1B]"
                    >
                        <thead
                            class="bg-[#F3F3EE] border-b border-[#E1E2D9] text-[11px] font-semibold text-[#5B5F58] uppercase"
                        >
                            <tr>
                                <th class="py-3.5 px-6">Order ID</th>
                                <th class="py-3.5 px-6">Tanggal</th>
                                <th class="py-3.5 px-6">Total Nilai</th>
                                <th class="py-3.5 px-6">Item</th>
                                <th class="py-3.5 px-6">Status</th>
                                <th class="py-3.5 px-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E1E2D9]">
                            <tr
                                v-for="order in orders"
                                :key="order.id_transaksi"
                                class="hover:bg-[#F3F3EE]/60 transition-colors"
                            >
                                <td
                                    class="py-4 px-6 font-bold font-['IBM_Plex_Mono']"
                                >
                                    #{{ order.id_transaksi }}
                                </td>
                                <td class="py-4 px-6 text-[#5B5F58]">
                                    {{
                                        formatDate(
                                            order.created_at || order.tanggal,
                                        )
                                    }}
                                </td>
                                <td
                                    class="py-4 px-6 font-bold font-['IBM_Plex_Mono']"
                                >
                                    {{ formatRupiah(order.total_belanja) }}
                                </td>
                                <td class="py-4 px-6 font-medium">
                                    {{ order.items_count }} Item
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
                                                order.status === 'processing' ||
                                                order.status === 'shipped',
                                            'bg-emerald-100 text-emerald-800 border border-emerald-200':
                                                order.status === 'done',
                                            'bg-red-100 text-red-800 border border-red-200':
                                                order.status === 'cancelled',
                                        }"
                                    >
                                        {{ order.status_label }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <Link
                                        :href="
                                            route(
                                                'customer.orders.show',
                                                order.id_transaksi,
                                            )
                                        "
                                        class="inline-block px-3 py-1.5 bg-[#F3F3EE] border border-[#E1E2D9] rounded-md font-semibold hover:border-[#1C1E1B] transition-colors"
                                    >
                                        Detail
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="!orders.length">
                                <td
                                    colspan="6"
                                    class="py-12 text-center text-[#5B5F58]"
                                >
                                    <div class="mb-4">
                                        <svg
                                            class="w-12 h-12 mx-auto text-gray-300"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                            ></path>
                                        </svg>
                                    </div>
                                    <p class="font-bold">Belum ada pesanan</p>
                                    <p class="text-xs max-w-sm mx-auto mt-1">
                                        Anda belum melakukan transaksi belanja
                                        di TechStore. Silakan kunjungi katalog
                                        untuk mulai berbelanja.
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
