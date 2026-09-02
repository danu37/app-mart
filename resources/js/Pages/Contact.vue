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

// Form state
const form = ref({
    name: "",
    email: "",
    subject: "",
    message: "",
});
const formStatus = ref("");
const submitForm = () => {
    // Dummy submit logic for UI completeness
    formStatus.value = "loading";
    setTimeout(() => {
        formStatus.value = "success";
        form.value = { name: "", email: "", subject: "", message: "" };
        setTimeout(() => (formStatus.value = ""), 4000);
    }, 1200);
};
</script>

<template>
    <Head title="Kontak - TechStore" />

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
            <!-- ─── Hero / Header ────────────────────────────── -->
            <section
                class="relative bg-navy-950 overflow-hidden pt-20 pb-28 border-b border-navy-900"
            >
                <div
                    class="absolute inset-0 opacity-[0.04]"
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
                    class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center"
                >
                    <h1
                        class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight tracking-tight mb-4"
                    >
                        Hubungi Kami
                    </h1>
                    <p
                        class="text-navy-300 text-base lg:text-lg leading-relaxed max-w-xl mx-auto"
                    >
                        Punya pertanyaan, keluhan, atau ingin kerja sama (B2B)?
                        Jangan ragu untuk mengirimkan pesan kepada tim
                        TechStore.
                    </p>
                </div>
            </section>

            <!-- ─── Content Section ──────────────────────────── -->
            <section
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 mb-24 relative z-10"
            >
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Form Kolom -->
                    <div
                        class="lg:col-span-2 bg-white rounded-2xl shadow-xl border border-gray-100 p-8 sm:p-10"
                    >
                        <h2 class="text-2xl font-bold text-navy-950 mb-2">
                            Kirim Pesan
                        </h2>
                        <p class="text-gray-500 text-sm mb-8">
                            Tim kami akan membalas pesan Anda selambat-lambatnya
                            1x24 jam kerja.
                        </p>

                        <form @submit.prevent="submitForm" class="space-y-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label
                                        class="block text-sm justify-between font-medium text-gray-700 mb-1.5"
                                        >Nama Lengkap</label
                                    >
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        required
                                        class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-lg outline-none focus:bg-white focus:border-brand-orange-500 focus:ring-1 focus:ring-brand-orange-500 transition-colors px-4 py-2.5"
                                        placeholder="Cth. Budi Santoso"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1.5"
                                        >Email</label
                                    >
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-lg outline-none focus:bg-white focus:border-brand-orange-500 focus:ring-1 focus:ring-brand-orange-500 transition-colors px-4 py-2.5"
                                        placeholder="Cth. budi@email.com"
                                    />
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1.5"
                                    >Subjek</label
                                >
                                <select
                                    v-model="form.subject"
                                    required
                                    class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-lg outline-none focus:bg-white focus:border-brand-orange-500 focus:ring-1 focus:ring-brand-orange-500 transition-colors px-4 py-2.5 appearance-none cursor-pointer"
                                >
                                    <option value="" disabled>
                                        Pilih subjek pesan
                                    </option>
                                    <option value="Tanya Produk">
                                        Pertanyaan Produk
                                    </option>
                                    <option value="Garansi">
                                        Klaim Garansi
                                    </option>
                                    <option value="Kerjasama B2B">
                                        Kerjasama B2B / Proyek
                                    </option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1.5"
                                    >Pesan Anda</label
                                >
                                <textarea
                                    v-model="form.message"
                                    rows="5"
                                    required
                                    class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-lg outline-none focus:bg-white focus:border-brand-orange-500 focus:ring-1 focus:ring-brand-orange-500 transition-colors px-4 py-2.5 resize-y"
                                    placeholder="Tuliskan detail pertanyaan atau kebutuhan Anda di sini..."
                                ></textarea>
                            </div>

                            <div class="pt-2">
                                <button
                                    type="submit"
                                    :disabled="formStatus === 'loading'"
                                    class="w-full sm:w-auto inline-flex items-center justify-center min-w-[160px] gap-2 px-6 py-3 bg-brand-orange-500 hover:bg-brand-orange-600 disabled:bg-brand-orange-400 disabled:cursor-not-allowed text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition-all cursor-pointer"
                                >
                                    <template v-if="formStatus === 'loading'">
                                        <svg
                                            class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
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
                                        Mengirim...
                                    </template>
                                    <template
                                        v-else-if="formStatus === 'success'"
                                    >
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
                                                d="M5 13l4 4L19 7"
                                            />
                                        </svg>
                                        Terkirim!
                                    </template>
                                    <template v-else> Kirim Pesan </template>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Kontak Info Kolom -->
                    <div class="space-y-6">
                        <!-- Info Card 1 -->
                        <div
                            class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 flex gap-4"
                        >
                            <div
                                class="w-12 h-12 bg-navy-50 text-navy-900 rounded-full flex shrink-0 items-center justify-center"
                            >
                                <svg
                                    class="w-5 h-5"
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
                            </div>
                            <div>
                                <h3 class="font-bold text-navy-950 mb-1">
                                    Email Surel
                                </h3>
                                <p class="text-sm text-gray-500 mb-0.5">
                                    Dukungan: support@techstore.id
                                </p>
                                <p class="text-sm text-gray-500">
                                    B2B/Bisnis: sales@techstore.id
                                </p>
                            </div>
                        </div>

                        <!-- Info Card 2 -->
                        <div
                            class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 flex gap-4"
                        >
                            <div
                                class="w-12 h-12 bg-navy-50 text-navy-900 rounded-full flex shrink-0 items-center justify-center"
                            >
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-navy-950 mb-1">
                                    Telepon & WA
                                </h3>
                                <p class="text-sm text-gray-500 mb-0.5">
                                    Senin - Jumat: 08:00 - 20:00
                                </p>
                                <p
                                    class="text-sm font-semibold text-brand-orange-500 mt-1"
                                >
                                    +62 812-3456-7890
                                </p>
                            </div>
                        </div>

                        <!-- Info Card 3 (Location) -->
                        <div
                            class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 flex gap-4"
                        >
                            <div
                                class="w-12 h-12 bg-navy-50 text-navy-900 rounded-full flex shrink-0 items-center justify-center"
                            >
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-navy-950 mb-1">
                                    Alamat Gudang
                                </h3>
                                <p
                                    class="text-sm text-gray-500 leading-relaxed"
                                >
                                    Gedung Artha Lt. 4, Menara Teknologi Jl.
                                    Jenderal Sudirman No. 45<br />Jakarta
                                    Selatan 12190
                                </p>
                            </div>
                        </div>
                    </div>
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
