<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import StoreNavbar from "@/Components/StoreNavbar.vue";
import StoreFooter from "@/Components/StoreFooter.vue";

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    categories: { type: Array, default: () => [] },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

const searchQuery = ref("");
const isCartOpen = ref(false);

const handleSearch = (q) => {
    searchQuery.value = q;
};
</script>

<template>
    <Head title="Keunggulan - TechStore" />

    <div
        class="min-h-screen bg-gray-50 text-gray-800 font-sans antialiased flex flex-col"
    >
        <!-- ─── Navbar ─────────────────────────────────────── -->
        <StoreNavbar
            :user="user"
            :canLogin="canLogin"
            :canRegister="canRegister"
            :cartCount="0"
            :searchQuery="searchQuery"
            @update:searchQuery="handleSearch"
            @openCart="isCartOpen = true"
        />

        <main class="flex-1">
            <!-- ─── Hero ─────────────────────────────────── -->
            <section
                class="relative bg-white overflow-hidden py-16 lg:py-24 border-b border-gray-100"
            >
                <div
                    class="absolute inset-0 opacity-[0.03]"
                    style="
                        background-image: radial-gradient(
                            circle,
                            #162240 1px,
                            transparent 1px
                        );
                        background-size: 24px 24px;
                    "
                ></div>
                <div
                    class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center"
                >
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 mb-5 rounded-full bg-brand-orange-50 text-brand-orange-600 text-xs font-semibold tracking-wide border border-brand-orange-100"
                    >
                        <span
                            class="w-1.5 h-1.5 rounded-full bg-brand-orange-500"
                        ></span>
                        Mengapa Kami?
                    </span>
                    <h1
                        class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-navy-950 leading-[1.15] tracking-tight mb-6"
                    >
                        Keunggulan Belanja di
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-brand-orange-500 to-brand-orange-600"
                            >TechStore</span
                        >
                    </h1>
                    <p
                        class="text-gray-500 text-base lg:text-lg leading-relaxed max-w-2xl mx-auto"
                    >
                        Kami tidak sekadar menjual barang. Kami menyajikan
                        komitmen, kualitas, dan rasa aman pada setiap produk
                        yang Anda bawa pulang.
                    </p>
                </div>
            </section>

            <!-- ─── Advantages Cards ───────────────────────── -->
            <section
                class="py-16 lg:py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
            >
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
                >
                    <!-- Card 1 -->
                    <div
                        class="bg-white border border-gray-100 border-l-4 border-l-navy-900 rounded-lg p-8 hover:shadow-xl transition-all duration-300"
                    >
                        <div
                            class="text-3xl font-extrabold text-gray-200 mb-4 tracking-tighter"
                        >
                            01.
                        </div>
                        <h3 class="text-xl font-bold text-navy-950 mb-3">
                            Garansi Resmi 100%
                        </h3>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Setiap unit yang Anda beli memiliki perlindungan
                            garansi resmi. Jika rusak, Anda dapat membawanya ke
                            service center dari merek perangkat Anda.
                        </p>
                    </div>

                    <!-- Card 2 -->
                    <div
                        class="bg-white border border-gray-100 border-l-4 border-l-navy-900 rounded-lg p-8 hover:shadow-xl transition-all duration-300"
                    >
                        <div
                            class="text-3xl font-extrabold text-gray-200 mb-4 tracking-tighter"
                        >
                            02.
                        </div>
                        <h3 class="text-xl font-bold text-navy-950 mb-3">
                            Pengiriman 24 Jam
                        </h3>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Proses pesanan secepat kilat. Dukungan operasional
                            logistik 24 jam untuk memaksimalkan kepuasan dari
                            momen pembelian Anda.
                        </p>
                    </div>

                    <!-- Card 3 -->
                    <div
                        class="bg-white border border-gray-100 border-l-4 border-l-navy-900 rounded-lg p-8 hover:shadow-xl transition-all duration-300"
                    >
                        <div
                            class="text-3xl font-extrabold text-gray-200 mb-4 tracking-tighter"
                        >
                            03.
                        </div>
                        <h3 class="text-xl font-bold text-navy-950 mb-3">
                            Pembayaran Aman
                        </h3>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Bekerja sama dengan sistem gerbang pembayaran
                            nasional terenskripsi agar privasi perbankan Anda
                            senantiasa terlindungi.
                        </p>
                    </div>

                    <!-- Card 4 -->
                    <div
                        class="bg-white border border-gray-100 border-l-4 border-l-navy-900 rounded-lg p-8 hover:shadow-xl transition-all duration-300"
                    >
                        <div
                            class="text-3xl font-extrabold text-gray-200 mb-4 tracking-tighter"
                        >
                            04.
                        </div>
                        <h3 class="text-xl font-bold text-navy-950 mb-3">
                            Barang 100% Ori
                        </h3>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Kami menolak barang bajakan atau refurbish rakitan
                            palsu. Semuanya didatangkan langsung dari manufaktur
                            internasional.
                        </p>
                    </div>

                    <!-- Card 5 -->
                    <div
                        class="bg-navy-900 border border-navy-800 border-l-4 border-l-brand-orange-500 rounded-lg p-8 shadow-lg md:col-span-2 lg:col-span-2 flex flex-col justify-center"
                    >
                        <div
                            class="text-brand-orange-500 text-xs font-bold uppercase tracking-widest mb-2"
                        >
                            Benefit Ekstra
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3">
                            Konsultasi Gratis Sebelum Membeli
                        </h3>
                        <p
                            class="text-navy-200 text-sm leading-relaxed mb-6 max-w-2xl"
                        >
                            Kurang paham spesifikasi RAM atau VGA yang cocok?
                            Tim kami melayani konsultasi secara profesional
                            sebelum Anda membeli. Tidak perlu khawatir salah
                            beli.
                        </p>
                        <div>
                            <Link
                                :href="route('contact')"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand-orange-500 hover:bg-brand-orange-600 text-white font-semibold text-sm rounded-lg transition-colors shadow-sm"
                            >
                                Hubungi Tim Kami
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ─── CTA Section ────────────────────────────── -->
            <section
                class="bg-navy-900 border-t border-navy-800 relative overflow-hidden"
            >
                <div
                    class="absolute inset-0 opacity-[0.05]"
                    style="
                        background-image: radial-gradient(
                            circle,
                            #fff 1px,
                            transparent 1px
                        );
                        background-size: 24px 24px;
                    "
                ></div>
                <div
                    class="max-w-4xl mx-auto px-4 py-16 lg:py-24 relative text-center"
                >
                    <h2
                        class="text-3xl md:text-4xl font-extrabold text-white mb-6"
                    >
                        Siap merasakan perbedaan berbelanja bersama kami?
                    </h2>
                    <p
                        class="text-navy-300 text-base md:text-lg mb-8 max-w-2xl mx-auto"
                    >
                        Jelajahi berbagai perangkat terbaru dan populer di
                        katalog lengkap TechStore.
                    </p>
                    <Link
                        :href="route('home') + '#katalog'"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-brand-orange-500 hover:bg-brand-orange-600 text-white font-semibold rounded-xl shadow-lg transition-colors cursor-pointer text-lg"
                    >
                        Mulai Belanja
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2.5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"
                            />
                        </svg>
                    </Link>
                </div>
            </section>
        </main>

        <!-- ─── Footer ────────────────────────────────────── -->
        <StoreFooter :categories="categories" />

        <!-- Cart Drawer Dummy -->
        <Transition name="slide-right">
            <div
                v-if="isCartOpen"
                class="fixed inset-0 z-50 flex justify-end"
                @click.self="isCartOpen = false"
            >
                <div
                    class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                    @click="isCartOpen = false"
                ></div>
                <div
                    class="relative w-full max-w-sm bg-white h-full flex flex-col shadow-2xl"
                >
                    <div
                        class="flex items-center justify-between px-5 py-4 border-b border-gray-100"
                    >
                        <div>
                            <h3 class="font-bold text-navy-950">Keranjang</h3>
                            <p class="text-xs text-gray-400 mt-0.5">0 item</p>
                        </div>
                        <button
                            @click="isCartOpen = false"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors cursor-pointer"
                        >
                            <svg
                                class="w-4 h-4 text-gray-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3">
                        <div
                            class="h-full flex flex-col items-center justify-center gap-3 text-center py-16"
                        >
                            <span class="text-4xl">🛒</span>
                            <p class="font-semibold text-navy-800">
                                Keranjang kosong
                            </p>
                            <p class="text-sm text-gray-400">
                                Tambahkan produk dari
                                <Link
                                    :href="route('home') + '#katalog'"
                                    class="text-brand-orange-500 hover:underline"
                                    >katalog kami</Link
                                >.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
/* Cart slide transition */
.slide-right-enter-active,
.slide-right-leave-active {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-right-enter-from,
.slide-right-leave-to {
    transform: translateX(100%);
}
</style>
