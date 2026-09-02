<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";
import axios from "axios";

const riwayatList = ref([]);
const isProcessing = ref(false);

const fetchRiwayat = async () => {
    const res = await axios.get(route("laporan.riwayatExport"));
    riwayatList.value = res.data;
};

onMounted(() => {
    fetchRiwayat();
    // Opsional: Polling data riwayat setiap 10 detik agar tabel update otomatis
    setInterval(fetchRiwayat, 10000);
});

const handleExportAsync = async (format) => {
    isProcessing.value = true;
    try {
        const payload = { ...form.value, format: format };
        const response = await axios.post(route("laporan.exportApi"), payload);

        alert(response.data.message); // Bisa diganti dengan Toast Notification
        fetchRiwayat(); // Refresh tabel riwayat
    } catch (error) {
        alert("Gagal memulai export");
    } finally {
        isProcessing.value = false;
    }
};

const props = defineProps({
    transaksi: Array,
    totalPendapatan: Number,
    filters: Object,
});

// State untuk form filter
const form = ref({
    tipe_laporan: props.filters.tipe_laporan || "harian",
    tanggal_awal: props.filters.tanggal_awal || "",
    tanggal_akhir: props.filters.tanggal_akhir || "",
    bulan: props.filters.bulan || new Date().getMonth() + 1,
    tahun: props.filters.tahun || new Date().getFullYear(),
});

// Fungsi untuk submit pencarian
const filterData = () => {
    router.get(route("laporan.index"), form.value, {
        preserveState: true,
        replace: true,
    });
};

// Fungsi export (membuka tab baru karena ini request download file browser biasa)
const exportData = (format) => {
    const params = new URLSearchParams(form.value).toString();
    window.open(`/laporan?${params}&export=${format}`, "_blank");
};
</script>

<template>
    <Head title="Laporan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Laporan Keuangan
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Filter Section -->
                <div class="p-6 bg-white shadow rounded-lg">
                    <form
                        @submit.prevent="filterData"
                        class="flex flex-wrap gap-4 items-end"
                    >
                        <div>
                            <label class="block text-sm font-medium"
                                >Tipe Laporan</label
                            >
                            <select
                                v-model="form.tipe_laporan"
                                class="mt-1 block w-full rounded border-gray-300"
                            >
                                <option value="harian">Harian</option>
                                <option value="mingguan">Mingguan</option>
                                <option value="bulanan">Bulanan</option>
                                <option value="tahunan">Tahunan</option>
                            </select>
                        </div>

                        <div
                            v-if="
                                form.tipe_laporan === 'harian' ||
                                form.tipe_laporan === 'mingguan'
                            "
                        >
                            <label class="block text-sm font-medium"
                                >Tanggal (Awal)</label
                            >
                            <input
                                type="date"
                                v-model="form.tanggal_awal"
                                class="mt-1 block w-full rounded border-gray-300"
                            />
                        </div>

                        <div v-if="form.tipe_laporan === 'mingguan'">
                            <label class="block text-sm font-medium"
                                >Tanggal Akhir</label
                            >
                            <input
                                type="date"
                                v-model="form.tanggal_akhir"
                                class="mt-1 block w-full rounded border-gray-300"
                            />
                        </div>

                        <div v-if="form.tipe_laporan === 'bulanan'">
                            <label class="block text-sm font-medium"
                                >Bulan</label
                            >
                            <select
                                v-model="form.bulan"
                                class="mt-1 block w-full rounded border-gray-300"
                            >
                                <option v-for="n in 12" :key="n" :value="n">
                                    {{ n }}
                                </option>
                            </select>
                        </div>

                        <div
                            v-if="
                                form.tipe_laporan === 'bulanan' ||
                                form.tipe_laporan === 'tahunan'
                            "
                        >
                            <label class="block text-sm font-medium"
                                >Tahun</label
                            >
                            <input
                                type="number"
                                v-model="form.tahun"
                                class="mt-1 block w-24 rounded border-gray-300"
                            />
                        </div>

                        <div>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                            >
                                Tampilkan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Hasil Section -->
                <div class="p-6 bg-white shadow rounded-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">Hasil Data Transaksi</h3>
                        <div class="flex gap-2">
                            <button
                                @click="handleExportAsync('pdf')"
                                :disabled="isProcessing"
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                            >
                                Export PDF (Background)
                            </button>
                            <button
                                @click="handleExportAsync('excel')"
                                :disabled="isProcessing"
                                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                            >
                                Export Excel (Background)
                            </button>
                        </div>
                    </div>

                    <table
                        class="w-full text-left border-collapse border border-gray-200"
                    >
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 border">ID</th>
                                <th class="p-2 border">Tanggal</th>
                                <th class="p-2 border">Pelanggan</th>
                                <th class="p-2 border text-right">
                                    Total Belanja
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="item in transaksi"
                                :key="item.id_transaksi"
                            >
                                <td class="p-2 border">
                                    {{ item.id_transaksi }}
                                </td>
                                <td class="p-2 border">{{ item.tanggal }}</td>
                                <td class="p-2 border">
                                    {{
                                        item.pelanggan
                                            ? item.pelanggan.nama_pelanggan
                                            : "-"
                                    }}
                                </td>
                                <td class="p-2 border text-right">
                                    Rp
                                    {{
                                        Number(
                                            item.total_belanja,
                                        ).toLocaleString("id-ID")
                                    }}
                                </td>
                            </tr>
                            <tr v-if="transaksi.length === 0">
                                <td
                                    colspan="4"
                                    class="p-4 text-center text-gray-500"
                                >
                                    Tidak ada data transaksi.
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="font-bold bg-gray-50">
                                <td colspan="3" class="p-2 border text-right">
                                    Total Pendapatan:
                                </td>
                                <td
                                    class="p-2 border text-right text-green-600"
                                >
                                    Rp
                                    {{
                                        Number(totalPendapatan).toLocaleString(
                                            "id-ID",
                                        )
                                    }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- Tabel Riwayat Export Baru -->
                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-bold mb-4">
                            Riwayat Export (Download Disini)
                        </h3>
                        <table
                            class="w-full text-left border-collapse border border-gray-200"
                        >
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="p-2 border border-gray-200">
                                        Waktu Request
                                    </th>
                                    <th class="p-2 border border-gray-200">
                                        Nama File
                                    </th>
                                    <th class="p-2 border border-gray-200">
                                        Format
                                    </th>
                                    <th class="p-2 border border-gray-200">
                                        Status
                                    </th>
                                    <th class="p-2 border border-gray-200">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in riwayatList" :key="item.id">
                                    <td class="p-2 border border-gray-200">
                                        {{
                                            new Date(
                                                item.created_at,
                                            ).toLocaleString()
                                        }}
                                    </td>
                                    <td class="p-2 border border-gray-200">
                                        {{ item.nama_file }}
                                    </td>
                                    <td
                                        class="p-2 border border-gray-200 uppercase"
                                    >
                                        {{ item.format }}
                                    </td>
                                    <td class="p-2 border border-gray-200">
                                        <span
                                            v-if="item.status === 'pending'"
                                            class="text-yellow-600"
                                            >Menunggu...</span
                                        >
                                        <span
                                            v-if="item.status === 'processing'"
                                            class="text-blue-600"
                                            >Diproses...</span
                                        >
                                        <span
                                            v-if="item.status === 'completed'"
                                            class="text-green-600"
                                            >Selesai</span
                                        >
                                        <span
                                            v-if="item.status === 'failed'"
                                            class="text-red-600"
                                            >Gagal</span
                                        >
                                    </td>
                                    <td class="p-2 border border-gray-200">
                                        <a
                                            v-if="item.status === 'completed'"
                                            :href="item.file_url"
                                            target="_blank"
                                            class="text-blue-500 underline"
                                        >
                                            Download
                                        </a>
                                    </td>
                                </tr>
                                <tr v-if="riwayatList.length === 0">
                                    <td
                                        colspan="5"
                                        class="p-4 text-center text-gray-500"
                                    >
                                        Tidak ada riwayat export.
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
