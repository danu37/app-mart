<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import axios from "axios";

const props = defineProps({
    pelangganList: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
});

const riwayat = ref([]);
const selectedPelangganId = ref(null);
const selectedPelangganName = ref("");
const selectedPelangganCity = ref("");
const isLoading = ref(false);
const errorMessage = ref("");

// Struk State
const isStrukModalOpen = ref(false);
const isFetchingStruk = ref(false);
const currentStruk = ref(null);

const formatRupiah = (val) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(val || 0);
};

const formatDate = (dateStr) => {
    if (!dateStr) return "-";
    try {
        const d = new Date(dateStr);
        return d.toLocaleDateString("id-ID", {
            day: "numeric",
            month: "long",
            year: "numeric",
        });
    } catch {
        return dateStr;
    }
};

const formatDateTime = (dateStr) => {
    const d = dateStr ? new Date(dateStr) : new Date();
    const months = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember",
    ];
    const pad = (n) => String(n).padStart(2, "0");
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()} pukul ${pad(d.getHours())}.${pad(d.getMinutes())}`;
};

const fetchRiwayat = async (pelanggan) => {
    selectedPelangganId.value = pelanggan.id_pelanggan;
    selectedPelangganName.value = pelanggan.nama_pelanggan;
    selectedPelangganCity.value = pelanggan.kota;
    isLoading.value = true;
    errorMessage.value = "";

    try {
        const response = await axios.get(
            `/admin/pelanggan/${pelanggan.id_pelanggan}/riwayat`,
        );
        riwayat.value = response.data || [];
    } catch (err) {
        console.error(err);
        errorMessage.value = "Gagal memuat riwayat transaksi pelanggan.";
        riwayat.value = [];
    } finally {
        isLoading.value = false;
    }
};

const openStruk = async (item) => {
    isFetchingStruk.value = true;
    currentStruk.value = {
        id_transaksi: item.id_transaksi,
        tanggal: item.tanggal,
        nama_pelanggan: item.nama_pelanggan || selectedPelangganName.value,
        kota: item.kota || selectedPelangganCity.value,
        total_belanja: item.total_belanja,
        items: [],
    };
    isStrukModalOpen.value = true;

    try {
        const response = await axios.get(
            `/admin/pelanggan/transaksi/${item.id_transaksi}/struk`,
        );
        if (response.data && response.data.transaksi) {
            currentStruk.value = {
                ...response.data.transaksi,
                items: response.data.items || [],
            };
        }
    } catch (err) {
        console.warn(
            "Detail struk dari server gagal/kosong, menggunakan ringkasan transaksi.",
            err,
        );
    } finally {
        isFetchingStruk.value = false;
    }
};

const closeStruk = () => {
    isStrukModalOpen.value = false;
    currentStruk.value = null;
};

const printStruk = () => {
    window.print();
};
</script>

<template>
    <Head title="Data Pelanggan & Cetak Struk Belanja" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-col sm:flex-row sm:items-center justify-between gap-4"
            >
                <div>
                    <h2
                        class="font-['Poppins'] font-bold text-2xl text-[#1C1E1B] leading-tight"
                    >
                        Data Pelanggan & Riwayat Transaksi
                    </h2>
                    <p class="text-sm text-[#5B5F58] mt-1 font-['Poppins']">
                        Kelola data pelanggan, eksekusi Stored Procedure
                        <code>LihatRiwayatBelanja</code>, dan cetak struk
                        belanja resmi.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span
                        class="text-xs font-semibold font-['IBM_Plex_Mono'] px-3 py-1.5 bg-[#F3F3EE] text-[#1C1E1B] border border-[#E1E2D9] rounded-lg"
                    >
                        TOTAL: {{ pelangganList.total }} PELANGGAN
                    </span>
                    <a
                        :href="route('admin.pelanggan.exportPdf')"
                        target="_blank"
                        class="px-4 py-2 bg-[#1C1E1B] hover:bg-[#33352F] text-white text-sm font-semibold rounded-lg flex items-center gap-2 transition-colors ml-2"
                    >
                        Export PDF
                    </a>
                </div>
            </div>
        </template>

        <div class="py-8 font-['Poppins']">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Section: Pilih Pelanggan -->
                <div
                    class="p-6 bg-white rounded-xl border border-[#E1E2D9] shadow-sm space-y-4"
                >
                    <div
                        class="flex items-center justify-between pb-3 border-b border-[#E1E2D9]"
                    >
                        <div>
                            <h3 class="text-sm font-bold text-[#1C1E1B]">
                                Daftar Pelanggan Terdaftar
                            </h3>
                            <p class="text-xs text-[#5B5F58] mt-0.5">
                                Pilih salah satu pelanggan pada tabel untuk
                                mengeksekusi Stored Procedure dan melihat
                                riwayat transaksinya.
                            </p>
                        </div>
                    </div>

                    <!-- Pelanggan Table -->
                    <div
                        class="overflow-x-auto rounded-xl border border-[#E1E2D9]"
                    >
                        <table class="w-full text-left text-xs text-[#1C1E1B]">
                            <thead
                                class="bg-[#F3F3EE] border-b border-[#E1E2D9] text-[#5B5F58] font-bold uppercase tracking-wider text-[11px]"
                            >
                                <tr>
                                    <th class="p-3.5">ID</th>
                                    <th class="p-3.5">Nama</th>
                                    <th class="p-3.5">Kontak & Kota</th>
                                    <th class="p-3.5">Alamat Lengkap</th>
                                    <th class="p-3.5 text-center">
                                        Jml Transaksi
                                    </th>
                                    <th class="p-3.5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E1E2D9]">
                                <tr
                                    v-for="p in pelangganList.data"
                                    :key="p.id_pelanggan"
                                    :class="
                                        selectedPelangganId === p.id_pelanggan
                                            ? 'bg-[#FF5A1F]/5'
                                            : 'hover:bg-[#F3F3EE]/50 transition-colors'
                                    "
                                >
                                    <td
                                        class="p-3.5 font-bold font-['IBM_Plex_Mono'] text-[#1C1E1B]"
                                    >
                                        #{{ p.id_pelanggan }}
                                    </td>
                                    <td
                                        class="p-3.5 font-semibold text-[#1C1E1B]"
                                    >
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="w-6 h-6 rounded bg-[#1C1E1B] text-white flex items-center justify-center font-bold text-[10px]"
                                            >
                                                {{
                                                    p.nama_pelanggan
                                                        .charAt(0)
                                                        .toUpperCase()
                                                }}
                                            </span>
                                            <div class="flex flex-col">
                                                <span>{{
                                                    p.nama_pelanggan
                                                }}</span>
                                                <span
                                                    class="font-normal text-[10px] text-[#FF5A1F] mt-0.5"
                                                    v-if="p.user"
                                                    >{{ p.user.email }}</span
                                                >
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3.5">
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="font-medium text-[#1C1E1B] whitespace-nowrap"
                                                >{{ p.telepon || "-" }}</span
                                            >
                                            <span
                                                class="inline-flex px-2 py-0.5 w-fit bg-[#E1E2D9]/50 border border-[#E1E2D9] rounded-md text-[10px] text-[#5B5F58]"
                                            >
                                                {{ p.kota || "-" }}
                                            </span>
                                        </div>
                                    </td>
                                    <td
                                        class="p-3.5 text-[#5B5F58] max-w-[200px] truncate"
                                        :title="p.alamat"
                                    >
                                        {{ p.alamat || "-" }}
                                    </td>
                                    <td class="p-3.5 text-center">
                                        <span
                                            class="font-bold font-['IBM_Plex_Mono'] px-2 py-1 bg-white border border-[#E1E2D9] rounded-lg"
                                        >
                                            {{ p.transaksi_count ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 text-center">
                                        <button
                                            @click="fetchRiwayat(p)"
                                            :class="[
                                                selectedPelangganId ===
                                                p.id_pelanggan
                                                    ? 'bg-[#1C1E1B] text-white border-[#1C1E1B]'
                                                    : 'bg-white text-[#1C1E1B] border-[#E1E2D9] hover:border-[#1C1E1B]',
                                                'px-3 py-1.5 rounded-lg font-semibold text-xs border transition-all cursor-pointer',
                                            ]"
                                        >
                                            {{
                                                selectedPelangganId ===
                                                p.id_pelanggan
                                                    ? "Sedang Dilihat"
                                                    : "Lihat Riwayat"
                                            }}
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="pelangganList.data.length === 0">
                                    <td
                                        colspan="6"
                                        class="p-6 text-center text-[#5B5F58]"
                                    >
                                        Tidak ada data pelanggan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="
                            pelangganList.links &&
                            pelangganList.links.length > 3
                        "
                        class="mt-4 flex justify-center gap-1"
                    >
                        <template
                            v-for="(link, i) in pelangganList.links"
                            :key="i"
                        >
                            <a
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                class="px-3 py-1 text-sm border rounded-md"
                                :class="
                                    link.active
                                        ? 'bg-navy-800 text-white border-navy-800'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                "
                            ></a>
                            <span
                                v-else
                                v-html="link.label"
                                class="px-3 py-1 text-sm border border-gray-300 rounded-md text-gray-400 bg-gray-50"
                            ></span>
                        </template>
                    </div>
                </div>

                <!-- Section: Tabel Riwayat Transaksi -->
                <div
                    class="p-6 bg-white rounded-xl border border-[#E1E2D9] shadow-sm space-y-5"
                >
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-[#E1E2D9]"
                    >
                        <div>
                            <h3 class="text-base font-bold text-[#1C1E1B]">
                                Riwayat Belanja Pelanggan
                                <span
                                    v-if="selectedPelangganName"
                                    class="text-[#FF5A1F]"
                                >
                                    : {{ selectedPelangganName }} ({{
                                        selectedPelangganCity
                                    }})</span
                                >
                            </h3>
                            <p class="text-xs text-[#5B5F58] mt-0.5">
                                Hasil kueri pemanggilan Stored Procedure
                                <code>CALL LihatRiwayatBelanja(?)</code>
                            </p>
                        </div>

                        <span
                            v-if="riwayat.length > 0"
                            class="text-xs font-semibold font-['IBM_Plex_Mono'] px-3 py-1 bg-[#0C8B7C]/10 text-[#0C8B7C] border border-[#0C8B7C]/20 rounded-lg self-start sm:self-auto"
                        >
                            {{ riwayat.length }} TRANSAKSI TERAKHIR
                        </span>
                    </div>

                    <!-- Loading State -->
                    <div v-if="isLoading" class="py-16 text-center space-y-3">
                        <div
                            class="inline-flex p-3 bg-[#F3F3EE] text-[#1C1E1B] rounded-full animate-spin border border-[#E1E2D9]"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-[#5B5F58]">
                            Memuat riwayat transaksi dari Stored Procedure...
                        </p>
                    </div>

                    <!-- Error Alert -->
                    <div
                        v-else-if="errorMessage"
                        class="p-4 bg-[#FF5A1F]/10 border border-[#FF5A1F]/20 text-[#FF5A1F] rounded-xl text-xs flex items-center gap-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 shrink-0"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <span class="font-medium">{{ errorMessage }}</span>
                    </div>

                    <!-- Table Data -->
                    <div
                        v-else-if="riwayat.length > 0"
                        class="overflow-x-auto rounded-xl border border-[#E1E2D9]"
                    >
                        <table class="w-full text-left text-xs text-[#1C1E1B]">
                            <thead
                                class="bg-[#F3F3EE] border-b border-[#E1E2D9] text-[#5B5F58] font-bold uppercase tracking-wider text-[11px]"
                            >
                                <tr>
                                    <th class="p-3.5">ID Transaksi</th>
                                    <th class="p-3.5">Tanggal</th>
                                    <th class="p-3.5">Nama Pelanggan</th>
                                    <th class="p-3.5">Kota</th>
                                    <th class="p-3.5 text-right">
                                        Total Belanja
                                    </th>
                                    <th class="p-3.5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E1E2D9]">
                                <tr
                                    v-for="item in riwayat"
                                    :key="item.id_transaksi"
                                    class="hover:bg-[#F3F3EE]/50 transition-colors"
                                >
                                    <td
                                        class="p-3.5 font-bold font-['IBM_Plex_Mono'] text-[#1C1E1B]"
                                    >
                                        #{{ item.id_transaksi }}
                                    </td>
                                    <td class="p-3.5 text-[#5B5F58]">
                                        {{ formatDate(item.tanggal) }}
                                    </td>
                                    <td
                                        class="p-3.5 font-semibold text-[#1C1E1B]"
                                    >
                                        {{ item.nama_pelanggan }}
                                    </td>
                                    <td class="p-3.5">
                                        <span
                                            class="px-2 py-0.5 bg-[#F3F3EE] border border-[#E1E2D9] rounded-md text-[11px] font-medium text-[#5B5F58]"
                                        >
                                            {{ item.kota || "-" }}
                                        </span>
                                    </td>
                                    <td
                                        class="p-3.5 font-bold font-['IBM_Plex_Mono'] text-[#1C1E1B] text-right"
                                    >
                                        {{ formatRupiah(item.total_belanja) }}
                                    </td>
                                    <td class="p-3.5 text-center">
                                        <button
                                            @click="openStruk(item)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#1C1E1B] hover:bg-[#FF5A1F] text-white rounded-lg text-xs font-semibold transition-all shadow-xs cursor-pointer"
                                            title="Cetak Struk Belanja"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-3.5 w-3.5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                                                />
                                            </svg>
                                            <span>Cetak Struk</span>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State When No Customer Selected -->
                    <div
                        v-else-if="!selectedPelangganId"
                        class="py-16 text-center bg-[#F3F3EE]/60 rounded-xl border border-dashed border-[#D8D9CF] space-y-2"
                    >
                        <div
                            class="w-10 h-10 rounded-xl bg-white border border-[#E1E2D9] text-[#5B5F58] flex items-center justify-center mx-auto shadow-xs"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.75"
                                    d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"
                                />
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-[#1C1E1B]">
                            Belum Ada Pelanggan yang Dipilih
                        </h4>
                        <p class="text-xs text-[#5B5F58] max-w-sm mx-auto">
                            Klik salah satu tombol nama pelanggan di atas untuk
                            menampilkan 10 riwayat transaksi belanja terakhir.
                        </p>
                    </div>

                    <!-- Empty State When Customer Has No Transactions -->
                    <div
                        v-else
                        class="py-16 text-center bg-[#F3F3EE]/60 rounded-xl border border-dashed border-[#D8D9CF] space-y-2"
                    >
                        <div
                            class="w-10 h-10 rounded-xl bg-white border border-[#E1E2D9] text-[#5B5F58] flex items-center justify-center mx-auto shadow-xs"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.75"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                                />
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-[#1C1E1B]">
                            Belum Ada Riwayat Transaksi
                        </h4>
                        <p class="text-xs text-[#5B5F58] max-w-sm mx-auto">
                            Pelanggan ini belum memiliki catatan riwayat
                            transaksi belanja pada basis data.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════ -->
        <!-- MODAL CETAK STRUK BELANJA PELANGGAN            -->
        <!-- ═══════════════════════════════════════════════ -->
        <Transition
            enter-active-class="transition duration-250 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isStrukModalOpen && currentStruk"
                class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 overflow-y-auto print:absolute print:inset-auto print:w-full print:bg-transparent print:backdrop-blur-none print:overflow-visible"
                @click.self="closeStruk"
            >
                <div
                    class="relative w-full max-w-[340px] bg-white rounded-2xl shadow-2xl border border-gray-300 overflow-hidden font-['Poppins'] print:shadow-none print:border-none print:max-w-none print:w-full"
                >
                    <!-- Area Struk yang Dicetak -->
                    <div
                        id="struk-print-area"
                        class="bg-white p-5 text-black space-y-3 leading-tight font-['Poppins'] print:p-0"
                    >
                        <!-- Header Toko -->
                        <div class="text-center space-y-1">
                            <h2
                                class="font-extrabold text-xl text-black tracking-tight uppercase"
                            >
                                TechStore
                            </h2>
                            <p class="text-xs font-semibold text-gray-800">
                                Jl. Boulevard Teknologi No.88
                            </p>
                            <p class="text-xs font-medium text-gray-700">
                                Telp: 0812-3456-7890
                            </p>
                        </div>

                        <!-- Garis Pemisah Thermal Receipt Tebal -->
                        <div
                            class="border-t-2 border-dashed border-gray-400 my-1.5"
                        ></div>

                        <!-- Informasi Transaksi & Pelanggan -->
                        <div
                            class="space-y-1 text-xs sm:text-[13px] font-medium text-gray-900"
                        >
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">No. Faktur:</span>
                                <span class="font-mono font-bold text-black"
                                    >INV/{{ currentStruk.id_transaksi }}</span
                                >
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Waktu:</span>
                                <span
                                    class="font-bold text-black whitespace-nowrap"
                                    >{{
                                        formatDateTime(currentStruk.tanggal)
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Pelanggan:</span>
                                <span class="font-bold text-black"
                                    >{{ currentStruk.nama_pelanggan }} ({{
                                        currentStruk.kota || "-"
                                    }})</span
                                >
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Kasir:</span>
                                <span class="font-bold text-black"
                                    >Pembayaran Online</span
                                >
                            </div>
                        </div>

                        <!-- Garis Pemisah -->
                        <div
                            class="border-t-2 border-dashed border-gray-400 my-1.5"
                        ></div>

                        <!-- Detail Item Belanja -->
                        <div class="space-y-2 text-xs sm:text-[13px]">
                            <!-- Jika ada list item detail -->
                            <div
                                v-if="
                                    currentStruk.items &&
                                    currentStruk.items.length > 0
                                "
                                class="space-y-2"
                            >
                                <div
                                    v-for="(item, idx) in currentStruk.items"
                                    :key="item.id_detail || idx"
                                    class="space-y-0.5"
                                >
                                    <p
                                        class="font-bold text-black text-[13.5px] leading-snug"
                                    >
                                        {{ item.nama_produk }}
                                    </p>
                                    <div
                                        class="flex justify-between items-center text-xs font-bold text-black"
                                    >
                                        <span class="font-['IBM_Plex_Mono']">
                                            {{ item.qty }} x
                                            {{
                                                formatRupiah(
                                                    item.harga ||
                                                        item.subtotal /
                                                            (item.qty || 1),
                                                )
                                            }}
                                        </span>
                                        <span
                                            class="font-['IBM_Plex_Mono'] font-extrabold"
                                        >
                                            {{ formatRupiah(item.subtotal) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Fallback jika transaksi dari stored procedure belum ada detail pecahannya -->
                            <div v-else class="space-y-0.5">
                                <p class="font-bold text-black text-[13.5px]">
                                    Paket Belanja Komputer & Aksesoris
                                </p>
                                <div
                                    class="flex justify-between items-center text-xs font-bold text-black"
                                >
                                    <span class="font-['IBM_Plex_Mono']"
                                        >1 x
                                        {{
                                            formatRupiah(
                                                currentStruk.total_belanja,
                                            )
                                        }}</span
                                    >
                                    <span
                                        class="font-['IBM_Plex_Mono'] font-extrabold"
                                    >
                                        {{
                                            formatRupiah(
                                                currentStruk.total_belanja,
                                            )
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Garis Pemisah -->
                        <div
                            class="border-t-2 border-dashed border-gray-400 my-1.5"
                        ></div>

                        <!-- Rincian Pembayaran -->
                        <div
                            class="space-y-1 text-xs sm:text-[13px] font-medium text-gray-900"
                        >
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Ongkir:</span>
                                <span
                                    class="font-bold text-black font-['IBM_Plex_Mono']"
                                    >Rp 0 (GRATIS)</span
                                >
                            </div>

                            <div
                                class="flex justify-between items-center pt-2 border-t-2 border-dashed border-gray-400 my-1"
                            >
                                <span
                                    class="font-black text-sm text-black uppercase"
                                    >TOTAL:</span
                                >
                                <span
                                    class="text-xl font-black text-black font-['IBM_Plex_Mono'] whitespace-nowrap"
                                    >{{
                                        formatRupiah(currentStruk.total_belanja)
                                    }}</span
                                >
                            </div>

                            <div
                                class="flex justify-between items-center text-xs pt-0.5"
                            >
                                <span class="text-gray-600">Status:</span>
                                <span
                                    class="font-black text-black uppercase tracking-wider"
                                    >LUNAS</span
                                >
                            </div>
                        </div>

                        <!-- Garis Pemisah -->
                        <div
                            class="border-t-2 border-dashed border-gray-400 my-1.5"
                        ></div>

                        <!-- Footer Struk -->
                        <div
                            class="text-center space-y-1 text-xs text-gray-800 pt-1"
                        >
                            <p class="font-bold text-black text-xs">
                                Terima Kasih Telah Berbelanja!
                            </p>
                            <p class="text-[11px] text-gray-700 font-medium">
                                Garansi resmi 1 tahun toko.
                            </p>
                            <div
                                class="pt-1 font-mono text-[10px] text-gray-600 font-bold tracking-widest uppercase"
                            >
                                === STRUK RESMI KASIR ===
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi Modal (Tidak ikut dicetak saat print) -->
                    <div
                        class="no-print p-3.5 bg-gray-100 border-t border-gray-300 flex gap-2"
                    >
                        <button
                            @click="printStruk"
                            class="flex-1 py-2.5 px-3 bg-black hover:bg-orange-600 text-white rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-1.5 shadow-sm cursor-pointer"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                                />
                            </svg>
                            <span>Cetak Struk</span>
                        </button>
                        <button
                            @click="closeStruk"
                            class="py-2.5 px-4 bg-white hover:bg-gray-200 text-gray-800 border border-gray-300 rounded-xl font-bold text-xs transition-colors cursor-pointer"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<style scoped>
@media print {
    @page {
        size: auto;
        margin: 5mm;
    }

    html,
    body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Sembunyikan semua elemen di luar area struk */
    body * {
        visibility: hidden;
    }

    /* Spesifik paksa tampilan print area agar menimpa wrapper vue inert*/
    #struk-print-area,
    #struk-print-area * {
        visibility: visible !important;
        color: #000 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    #struk-print-area {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        max-width: 340px !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .no-print {
        display: none !important;
    }
}
</style>
