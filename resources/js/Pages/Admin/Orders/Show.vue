<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    order: Object,
});

const formatRupiah = (val) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(val || 0);
};

const formatDateTime = (dateStr) => {
    if (!dateStr) return "-";
    return new Date(dateStr).toLocaleString("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};
</script>

<template>
    <Head :title="'Detail Pesanan #' + order.id_transaksi" />

    <AuthenticatedLayout>
        <div class="py-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <Link
                        :href="route('admin.orders.index')"
                        class="text-sm text-brand-orange-600 hover:underline font-semibold flex items-center gap-1 mb-2"
                    >
                        &larr; Kembali ke Daftar Pesanan
                    </Link>
                    <h1 class="font-['Sora'] font-bold text-2xl text-[#1C1E1B]">
                        Detail Pesanan #{{ order.id_transaksi }}
                    </h1>
                </div>
                <div>
                    <span
                        class="px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider"
                        :class="{
                            'bg-yellow-100 text-yellow-800':
                                order.status === 'pending',
                            'bg-green-100 text-green-800':
                                order.status === 'paid',
                            'bg-blue-100 text-blue-800':
                                order.status === 'processing' ||
                                order.status === 'shipped',
                            'bg-emerald-100 text-emerald-800 border-2 border-emerald-500':
                                order.status === 'done',
                            'bg-red-100 text-red-800 border':
                                order.status === 'cancelled',
                        }"
                    >
                        {{ order.status_label }}
                    </span>
                </div>
            </div>

            <div
                class="bg-white p-6 sm:p-8 rounded-xl border border-[#E1E2D9] shadow-sm mb-6"
            >
                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 border-b border-gray-200 pb-8"
                >
                    <div>
                        <p
                            class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1"
                        >
                            Tanggal Pembelian
                        </p>
                        <p class="font-semibold text-gray-900">
                            {{
                                formatDateTime(
                                    order.created_at || order.tanggal,
                                )
                            }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1"
                        >
                            Pelanggan
                        </p>
                        <p class="font-semibold text-gray-900">
                            {{ order.pelanggan.nama_pelanggan }}
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ order.pelanggan.alamat }},
                            {{ order.pelanggan.kota }}
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ order.pelanggan.telepon }}
                        </p>
                    </div>
                </div>

                <h3 class="font-['Sora'] font-bold text-lg mb-4">
                    Daftar Produk
                </h3>
                <div class="space-y-4">
                    <div
                        v-for="item in order.items"
                        :key="item.id_detail"
                        class="flex justify-between border-b border-gray-100 pb-4"
                    >
                        <div>
                            <p class="font-semibold text-sm">
                                {{ item.nama_produk }}
                            </p>
                            <p class="text-xs text-gray-500 mb-1">
                                {{ item.kategori }} &bull; {{ item.qty }} x
                                {{ formatRupiah(item.harga) }}
                            </p>
                        </div>
                        <div
                            class="font-['IBM_Plex_Mono'] font-bold text-sm self-center"
                        >
                            {{ formatRupiah(item.subtotal) }}
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <div class="w-64">
                        <div
                            class="flex justify-between items-center py-2 text-sm"
                        >
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-['IBM_Plex_Mono']">{{
                                formatRupiah(order.total_belanja)
                            }}</span>
                        </div>
                        <div
                            class="flex justify-between items-center py-2 text-sm border-b border-gray-300"
                        >
                            <span class="text-gray-500">Ongkos Kirim</span>
                            <span class="font-['IBM_Plex_Mono'] text-green-600"
                                >Rp 0</span
                            >
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="font-bold text-gray-900"
                                >Total Tagihan</span
                            >
                            <span
                                class="font-['IBM_Plex_Mono'] font-bold text-[#FF5A1F] text-lg"
                                >{{ formatRupiah(order.total_belanja) }}</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
