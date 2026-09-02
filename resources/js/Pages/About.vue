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
const isCartOpen = ref(false); // Can be linked if cart is persistent, else just dummy for UI here.

const faqs = ref([
    {
        question: "Apakah produk yang dijual bergaransi resmi?",
        answer: "Ya, semua produk yang kami jual dilengkapi dengan garansi resmi dari distributor/merek terkait. Lama garansi bervariasi dari 1 hingga 3 tahun tergantung pada jenis produk yang Anda beli.",
        isOpen: false,
    },
    {
        question: "Metode pembayaran apa saja yang didukung?",
        answer: "Kami menerima berbagai metode pembayaran termasuk Bank Transfer (BCA, Mandiri, BNI, BRI), kartu kredit, dan berbagai dompet digital (GoPay, OVO, Dana) melalui payment gateway yang aman dan terenkripsi.",
        isOpen: false,
    },
    {
        question:
            "Apakah saya bisa mengajukan retur jika barang yang diterima rusak?",
        answer: "Tentu. Anda dapat mengajukan retur dalam waktu maksimal 3x24 jam sejak barang diterima dengan melampirkan video unboxing secara full tanpa terpotong. Tim kami akan segera menindaklanjuti untuk penukaran unit atau pengembalian dana.",
        isOpen: false,
    },
    {
        question: "Berapa lama waktu pengiriman pesanan?",
        answer: "Pesanan yang masuk sebelum pukul 15.00 WIB akan diproses dan dikirim pada hari yang sama. Estimasi waktu pengiriman berkisar antara 1-3 hari kerja tergantung pada wilayah tujuan dan layanan kurir yang Anda pilih.",
        isOpen: false,
    },
    {
        question:
            "Apakah TechStore melayani pembelian dalam jumlah besar (B2B)?",
        answer: "Ya, kami melayani proyek pengadaan atau B2B dengan harga spesial. Silakan hubungi tim kami di support@techstore.id untuk mendapatkan penawaran (quotation) terbaik terkait kebutuhan kantor atau instansi Anda.",
        isOpen: false,
    },
]);

const toggleFaq = (index) => {
    faqs.value[index].isOpen = !faqs.value[index].isOpen;
};

// Handle redirection to home search if user types
const handleSearch = (q) => {
    // If they press enter or logic dictates, you can route to `/?search=${q}`
    // But keeping it simple for UI visual completeness
    searchQuery.value = q;
};
</script>

<template>
    <Head title="Tentang Kami - TechStore" />

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
            <!-- ─── Hero Section ─────────────────────────────────── -->
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
                        Pelajari Lebih Lanjut
                    </span>
                    <h1
                        class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-navy-950 leading-[1.15] tracking-tight mb-6"
                    >
                        Menghubungkan Anda <br class="hidden sm:block" />dengan
                        Teknologi Terbaik.
                    </h1>
                    <p
                        class="text-gray-500 text-base lg:text-lg leading-relaxed max-w-2xl mx-auto"
                    >
                        Mengenal lebih dekat siapa kami, tujuan kami, serta
                        komitmen kami dalam memberikan layanan berkelas untuk
                        setiap kebutuhan perangkat keras Anda.
                    </p>
                </div>
            </section>

            <!-- ─── About Us ────────────────────────────────────── -->
            <section class="bg-gray-50 py-16 lg:py-20 border-b border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div
                        class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center"
                    >
                        <div class="space-y-6">
                            <h2 class="text-2xl font-bold text-navy-950">
                                Cerita Kami
                            </h2>
                            <p
                                class="text-gray-600 leading-relaxed text-sm lg:text-base"
                            >
                                Didirikan dengan visi untuk menyediakan pasar
                                Indonesia dengan perangkat keras, komputer, dan
                                aksesoris orisinal bermutu tinggi, TechStore
                                terus berkembang menjadi portal destinasi utama
                                bagi penggemar teknologi dan pekerja
                                profesional.
                            </p>
                            <p
                                class="text-gray-600 leading-relaxed text-sm lg:text-base"
                            >
                                Kami merancang pengalaman ini bukan hanya
                                sekadar untuk bertransaksi, tapi untuk membangun
                                ekosistem di mana Anda dapat mempercayakan
                                produktivitas Anda pada hardware yang solid,
                                awet, dan mendapat proteksi after-sales (purna
                                jual) yang sempurna.
                            </p>

                            <div
                                class="pt-4 grid grid-cols-1 sm:grid-cols-2 gap-5"
                            >
                                <div
                                    class="bg-white p-5 rounded-xl border border-gray-200 hover:border-navy-300 hover:shadow-lg transition-all duration-300"
                                >
                                    <h3
                                        class="text-brand-orange-600 font-bold mb-2"
                                    >
                                        Visi Kami
                                    </h3>
                                    <p
                                        class="text-xs text-gray-500 leading-relaxed"
                                    >
                                        Menjadi pemimpin pasar dalam penyediaan
                                        barang elektronik dan IT tepercaya
                                        se-Nusantara.
                                    </p>
                                </div>
                                <div
                                    class="bg-white p-5 rounded-xl border border-gray-200 hover:border-navy-300 hover:shadow-lg transition-all duration-300"
                                >
                                    <h3
                                        class="text-brand-orange-600 font-bold mb-2"
                                    >
                                        Misi Kami
                                    </h3>
                                    <p
                                        class="text-xs text-gray-500 leading-relaxed"
                                    >
                                        Menekan batas dengan menyediakan produk
                                        100% orisinal, harga yang transparan,
                                        dan garansi resmi cepat tanggap.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="relative flex flex-col justify-center bg-white border border-gray-100 rounded-3xl p-8 lg:p-12 shadow-sm"
                        >
                            <svg
                                class="w-8 h-8 text-brand-orange-500 mb-6"
                                fill="currentColor"
                                viewBox="0 0 32 32"
                            >
                                <path
                                    d="M10 8v9.333c0 4.603-3.73 8.334-8.333 8.334v-2.667c3.13 0 5.666-2.536 5.666-5.667H2v-9.333h8zm16 0v9.333c0 4.603-3.73 8.334-8.333 8.334v-2.667c3.13 0 5.666-2.536 5.666-5.667H18v-9.333h8z"
                                />
                            </svg>
                            <blockquote
                                class="text-xl md:text-2xl font-bold text-navy-950 leading-snug mb-6"
                            >
                                "Teknologi bukan sekadar alat, melainkan pijakan
                                untuk mencapai produktivitas dan inovasi yang
                                sesungguhnya."
                            </blockquote>
                            <div
                                class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between"
                            >
                                <div>
                                    <p class="font-bold text-navy-900 text-sm">
                                        Tim TechStore
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Sejak 2024
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ─── FAQ Section ──────────────────────────────────── -->
            <section class="bg-white py-16 lg:py-20">
                <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-10">
                        <h2 class="text-2xl font-bold text-navy-950">
                            Pertanyaan Umum (FAQ)
                        </h2>
                        <p class="text-sm text-gray-500 mt-2">
                            Punya pertanyaan lain? Kami mengumpulkan pertanyaan
                            yang sering ditanyakan untuk membantu Anda.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(faq, index) in faqs"
                            :key="index"
                            class="border border-gray-200 rounded-xl overflow-hidden transition-all duration-300"
                            :class="
                                faq.isOpen
                                    ? 'bg-navy-50/50 border-navy-200 shadow-sm'
                                    : 'bg-white hover:border-gray-300'
                            "
                        >
                            <button
                                @click="toggleFaq(index)"
                                class="w-full flex items-center justify-between p-5 text-left focus:outline-none cursor-pointer"
                            >
                                <span
                                    class="font-semibold text-navy-900 pr-5"
                                    :class="
                                        faq.isOpen
                                            ? 'text-brand-orange-600'
                                            : ''
                                    "
                                >
                                    {{ faq.question }}
                                </span>
                                <span
                                    class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center transition-colors duration-200"
                                    :class="
                                        faq.isOpen
                                            ? 'bg-brand-orange-100 text-brand-orange-600'
                                            : 'bg-gray-100 text-gray-500'
                                    "
                                >
                                    <svg
                                        class="w-4 h-4 transform transition-transform duration-300"
                                        :class="faq.isOpen ? 'rotate-180' : ''"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                </span>
                            </button>

                            <div
                                class="overflow-hidden transition-all duration-300 ease-in-out"
                                :style="
                                    faq.isOpen
                                        ? 'max-height: 500px; opacity: 1'
                                        : 'max-height: 0; opacity: 0'
                                "
                            >
                                <div
                                    class="p-5 pt-0 text-sm text-gray-600 leading-relaxed"
                                >
                                    {{ faq.answer }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ─── Call to Action ─────────────────────────────── -->
            <section
                class="bg-navy-950 py-16 text-center border-t border-navy-800"
            >
                <div class="max-w-xl mx-auto px-4">
                    <h2 class="text-2xl font-bold text-white mb-4">
                        Masih Butuh Bantuan?
                    </h2>
                    <p class="text-navy-300 text-sm mb-8">
                        Tim support kami selalu siap membantu Anda 24/7. Hubungi
                        kami sekarang melalui email atau telepon.
                    </p>
                    <a
                        href="mailto:support@techstore.id"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-brand-orange-500 hover:bg-brand-orange-600 text-white font-semibold text-sm rounded-lg shadow-sm transition-colors cursor-pointer"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                            />
                        </svg>
                        Hubungi Tim Support
                    </a>
                </div>
            </section>
        </main>

        <!-- ─── Footer ────────────────────────────────────── -->
        <StoreFooter :categories="categories" />

        <!-- Cart Drawer Dummy (For UI completeness when cart button clicked in this view) -->
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
