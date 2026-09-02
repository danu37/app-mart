<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    orders: Object,
    filters: Object,
});

const form = useForm({
    status: "",
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

const updateStatus = (id, newStatus) => {
    form.status = newStatus;
    form.patch(route("admin.orders.updateStatus", id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Kelola Pesanan" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-col sm:flex-row sm:items-end justify-between mb-4 mt-2"
            >
                <div>
                    <h2
                        class="font-poppins font-bold text-2xl text-navy-950 leading-tight"
                    >
                        Transaksi Pesanan
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Daftar semua transaksi dan kelola status pembayaran
                        belanja pelanggan. (Total:
                        <span class="font-semibold text-brand-orange-500">{{
                            orders.total
                        }}</span>
                        pesanan).
                    </p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a
                        :href="route('admin.orders.exportPdf', filters)"
                        target="_blank"
                        class="px-5 py-2.5 bg-white border border-gray-200 shadow-sm hover:border-brand-orange-300 hover:text-brand-orange-600 text-gray-700 text-sm font-semibold rounded-xl flex items-center gap-2 transition-all focus:ring focus:ring-brand-orange-200"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                        Export Laporan PDF
                    </a>
                </div>
            </div>
        </template>

        <div
            class="bg-white rounded-2xl border border-gray-100 shadow-xl shadow-gray-200/40 overflow-hidden"
        >
            <div class="overflow-x-auto">
                <table
                    class="w-full text-left border-collapse text-sm text-gray-700"
                >
                    <thead
                        class="bg-navy-50/50 border-b border-gray-100 text-[11px] font-bold text-navy-600 uppercase tracking-wider"
                    >
                        <tr>
                            <th class="py-4 px-6">ID & Waktu</th>
                            <th class="py-4 px-6">Data Pelanggan</th>
                            <th class="py-4 px-6">Belanja</th>
                            <th class="py-4 px-6">Status</th>
                            <th class="py-4 px-6 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr
                            v-for="order in orders.data"
                            :key="order.id_transaksi"
                            class="hover:bg-brand-orange-50/30 transition-colors"
                        >
                            <td class="py-4 px-6 align-top">
                                <p
                                    class="font-bold text-navy-900 font-mono text-sm"
                                >
                                    #{{ order.id_transaksi }}
                                </p>
                                <p class="text-gray-400 text-xs mt-1">
                                    {{
                                        formatDate(
                                            order.created_at || order.tanggal,
                                        )
                                    }}
                                </p>
                            </td>
                            <td class="py-4 px-6 align-top">
                                <p class="font-semibold text-navy-950">
                                    {{ order.nama_pelanggan }}
                                </p>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <svg
                                        class="w-3 h-3 text-gray-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                    </svg>
                                    <span class="text-[11px] text-gray-500">{{
                                        order.kota
                                    }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 align-top">
                                <p class="font-bold text-green-600">
                                    {{ formatRupiah(order.total_belanja) }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ order.items_count }} item barang
                                </p>
                            </td>
                            <td class="py-4 px-6 align-top">
                                <span
                                    class="inline-flex px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider"
                                    :class="{
                                        'bg-yellow-50 text-yellow-700 border border-yellow-200/60':
                                            order.status === 'pending',
                                        'bg-green-50 text-green-700 border border-green-200/60':
                                            order.status === 'paid',
                                        'bg-blue-50 text-blue-700 border border-blue-200/60':
                                            order.status === 'processing' ||
                                            order.status === 'shipped',
                                        'bg-emerald-50 text-emerald-700 border border-emerald-200/60':
                                            order.status === 'done',
                                        'bg-red-50 text-red-700 border border-red-200/60':
                                            order.status === 'cancelled',
                                    }"
                                >
                                    {{ order.status_label ?? order.status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center align-top">
                                <select
                                    @change="
                                        updateStatus(
                                            order.id_transaksi,
                                            $event.target.value,
                                        )
                                    "
                                    :value="order.status"
                                    :disabled="
                                        order.status === 'done' ||
                                        order.status === 'cancelled'
                                    "
                                    class="text-xs font-medium border-gray-200 rounded-lg py-1.5 pl-3 pr-8 shadow-sm focus:border-brand-orange-400 focus:ring focus:ring-brand-orange-200/50 disabled:opacity-50 disabled:bg-gray-50 transition-all cursor-pointer"
                                >
                                    <option value="pending">⏳ Menunggu</option>
                                    <option value="paid">💳 Dibayar</option>
                                    <option value="processing">
                                        ⚙ Diproses
                                    </option>
                                    <option value="shipped">🚚 Dikirim</option>
                                    <option value="done">✅ Selesai</option>
                                    <option value="cancelled">❌ Batal</option>
                                </select>
                            </td>
                        </tr>
                        <tr v-if="!orders.data.length">
                            <td colspan="5" class="py-16 text-center">
                                <div
                                    class="bg-gray-50 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3"
                                >
                                    <svg
                                        class="h-8 w-8 text-gray-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                        />
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-600">
                                    Belum ada pesanan masuk.
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Transaksi yang dilakukan pelanggan akan
                                    muncul di sini.
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="orders.links && orders.links.length > 3"
                class="px-6 py-5 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-4"
            >
                <span class="text-xs font-medium text-gray-500">
                    Menampilkan Halaman {{ orders.current_page }} dari
                    {{ orders.last_page }}
                </span>

                <div class="flex items-center gap-1.5 shadow-sm rounded-lg">
                    <template v-for="(link, i) in orders.links" :key="i">
                        <a
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            class="px-3.5 py-1.5 text-xs font-semibold border rounded-lg transition-all"
                            :class="
                                link.active
                                    ? 'bg-brand-orange-500 text-white border-brand-orange-500 shadow-md shadow-brand-orange-500/20'
                                    : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50 hover:text-navy-950'
                            "
                        ></a>
                        <span
                            v-else
                            v-html="link.label"
                            class="px-3.5 py-1.5 text-xs font-semibold border border-gray-100 rounded-lg text-gray-400 bg-gray-50/50 cursor-not-allowed"
                        ></span>
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
