<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    cartItems: Array,
    pelanggan: Object,
});

const form = useForm({});

const submitCheckout = () => {
    form.post(route("customer.checkout.store"), {
        preserveScroll: true,
    });
};

const formatRupiah = (val) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(val || 0);
};

// Calculate initial total
const subtotal = () =>
    props.cartItems.reduce((acc, item) => acc + item.harga * item.qty, 0);
</script>

<template>
    <Head title="Checkout" />

    <AuthenticatedLayout>
        <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="font-['Sora'] font-bold text-3xl text-[#1C1E1B] mb-8">
                Selesaikan Pesanan Anda
            </h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Customer details -->
                    <div
                        class="bg-white p-6 rounded-xl border border-[#E1E2D9] shadow-2xs"
                    >
                        <h2
                            class="font-['Sora'] font-bold text-lg mb-4 border-b pb-2"
                        >
                            Informasi Pengiriman
                        </h2>

                        <div
                            v-if="
                                !pelanggan?.alamat ||
                                !pelanggan?.kota ||
                                !pelanggan?.telepon
                            "
                            class="mb-4 p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg text-sm"
                        >
                            <span class="font-bold">Perhatian:</span> Mohon
                            lengkapi profil Anda (Kota, Alamat, Telepon) sebelum
                            melakukan checkout.
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex">
                                <span class="w-32 font-semibold text-[#5B5F58]"
                                    >Nama</span
                                >
                                <span class="font-medium text-[#1C1E1B]">{{
                                    pelanggan?.nama_pelanggan ||
                                    $page.props.auth.user.name
                                }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-semibold text-[#5B5F58]"
                                    >No. Telepon</span
                                >
                                <span class="font-medium text-[#1C1E1B]">{{
                                    pelanggan?.telepon || "-"
                                }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-semibold text-[#5B5F58]"
                                    >Kota</span
                                >
                                <span class="font-medium text-[#1C1E1B]">{{
                                    pelanggan?.kota || "-"
                                }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-semibold text-[#5B5F58]"
                                    >Alamat Lengkap</span
                                >
                                <span class="font-medium text-[#1C1E1B]">{{
                                    pelanggan?.alamat || "-"
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Order items -->
                    <div
                        class="bg-white p-6 rounded-xl border border-[#E1E2D9] shadow-2xs"
                    >
                        <h2
                            class="font-['Sora'] font-bold text-lg mb-4 border-b pb-2"
                        >
                            Ringkasan Produk
                        </h2>

                        <div class="space-y-4">
                            <div
                                v-for="item in cartItems"
                                :key="item.id_produk"
                                class="flex gap-4"
                            >
                                <div
                                    class="w-20 h-20 shrink-0 bg-gray-100 rounded-lg overflow-hidden border border-[#E1E2D9]"
                                >
                                    <img
                                        v-if="item.gambar"
                                        :src="item.gambar"
                                        :alt="item.nama_produk"
                                        class="w-full h-full object-cover"
                                    />
                                    <div
                                        v-else
                                        class="w-full h-full flex items-center justify-center text-gray-400"
                                    >
                                        <svg
                                            class="w-8 h-8"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            ></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-sm">
                                        {{ item.nama_produk }}
                                    </h3>
                                    <p class="text-xs text-[#5B5F58] mb-2">
                                        {{ item.kategori }}
                                    </p>
                                    <div
                                        class="flex justify-between items-center w-full"
                                    >
                                        <span
                                            class="text-xs bg-[#F3F3EE] px-2 py-0.5 rounded font-mono border border-gray-200"
                                            >{{ item.qty }} x
                                            {{ formatRupiah(item.harga) }}</span
                                        >
                                        <span
                                            class="font-['IBM_Plex_Mono'] font-bold text-[#FF5A1F]"
                                            >{{
                                                formatRupiah(
                                                    item.harga * item.qty,
                                                )
                                            }}</span
                                        >
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="!cartItems.length"
                                class="text-center py-6 text-[#5B5F58]"
                            >
                                Keranjang Anda kosong.
                                <Link
                                    :href="route('home')"
                                    class="text-[#FF5A1F] hover:underline"
                                    >Belanja sekarang</Link
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment summary -->
                <div>
                    <div
                        class="bg-white p-6 rounded-xl border border-[#E1E2D9] shadow-2xs sticky top-24"
                    >
                        <h2
                            class="font-['Sora'] font-bold text-lg mb-4 text-[#1C1E1B]"
                        >
                            Ringkasan Belanja
                        </h2>

                        <div
                            class="space-y-3 text-sm border-b border-dashed border-[#E1E2D9] pb-4 mb-4"
                        >
                            <div class="flex justify-between">
                                <span class="text-[#5B5F58]"
                                    >Total Harga ({{
                                        cartItems.length
                                    }}
                                    Barang)</span
                                >
                                <span
                                    class="font-medium font-['IBM_Plex_Mono']"
                                    >{{ formatRupiah(subtotal()) }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#5B5F58]">Ongkos Kirim</span>
                                <span class="font-medium text-green-600"
                                    >Gratis (Promo)</span
                                >
                            </div>
                        </div>

                        <div class="flex justify-between items-center mb-6">
                            <span class="font-bold text-sm text-[#1C1E1B]"
                                >Total Tagihan</span
                            >
                            <span
                                class="font-['IBM_Plex_Mono'] font-bold text-xl text-[#FF5A1F]"
                                >{{ formatRupiah(subtotal()) }}</span
                            >
                        </div>

                        <form @submit.prevent="submitCheckout">
                            <button
                                type="submit"
                                :disabled="form.processing || !cartItems.length"
                                class="w-full bg-[#1C1E1B] hover:bg-[#FF5A1F] text-white font-['Sora'] font-bold text-sm py-3 px-4 rounded-xl transition-colors shadow-2xs disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                <span
                                    v-if="form.processing"
                                    class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"
                                ></span>
                                BUAT PESANAN
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
