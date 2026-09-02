<script setup>
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import StoreNavbar from "@/Components/StoreNavbar.vue";
import StoreFooter from "@/Components/StoreFooter.vue";

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    products: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

const searchQuery = ref("");
const selectedCategory = ref("Semua");
const sortBy = ref("default");
const isCartOpen = ref(false);
const cart = ref(JSON.parse(localStorage.getItem("techstore_cart")) || []);

import { watch } from "vue";
watch(
    cart,
    (newCart) => {
        localStorage.setItem("techstore_cart", JSON.stringify(newCart));
    },
    { deep: true },
);
const toastMessage = ref("");
const showToast = ref(false);
const selectedProduct = ref(null);
const detailQty = ref(1);
const showNota = ref(false);
const notaData = ref(null);

/* ─── helpers ─────────────────────────────────────── */
const rp = (n) =>
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(n);

const makeInvoiceNo = () => {
    const d = new Date();
    const pad = (x) => String(x).padStart(2, "0");
    return `INV/${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(d.getDate())}/${Math.floor(1000 + Math.random() * 9000)}`;
};

const nowId = () => {
    const d = new Date();
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
    const pad = (x) => String(x).padStart(2, "0");
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()} pukul ${pad(d.getHours())}.${pad(d.getMinutes())}`;
};

/* ─── computed ────────────────────────────────────── */
const filteredProducts = computed(() => {
    let r = [...props.products];
    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase();
        r = r.filter(
            (p) =>
                p.nama_produk?.toLowerCase().includes(q) ||
                p.kategori?.toLowerCase().includes(q),
        );
    }
    if (selectedCategory.value !== "Semua")
        r = r.filter((p) => p.kategori === selectedCategory.value);

    const sorts = {
        "price-low": (a, b) => +a.harga - +b.harga,
        "price-high": (a, b) => +b.harga - +a.harga,
        name: (a, b) => a.nama_produk.localeCompare(b.nama_produk),
        stock: (a, b) => +b.stok - +a.stok,
    };
    if (sorts[sortBy.value]) r.sort(sorts[sortBy.value]);
    return r;
});

const categoryCount = computed(() => {
    const c = { Semua: props.products.length };
    props.products.forEach((p) => {
        if (p.kategori) c[p.kategori] = (c[p.kategori] || 0) + 1;
    });
    return c;
});

const cartCount = computed(() =>
    cart.value.reduce((t, i) => t + i.quantity, 0),
);
const cartTotal = computed(() =>
    cart.value.reduce((t, i) => t + +i.harga * i.quantity, 0),
);

/* ─── actions ─────────────────────────────────────── */
const toast = (msg, ms = 3000) => {
    toastMessage.value = msg;
    showToast.value = true;
    setTimeout(() => {
        showToast.value = false;
    }, ms);
};

const addToCart = (product, qty = 1) => {
    const idx = cart.value.findIndex((i) => i.id_produk === product.id_produk);
    idx > -1
        ? (cart.value[idx].quantity += qty)
        : cart.value.push({ ...product, quantity: qty });
    toast(`"${product.nama_produk}" ditambahkan.`);
};

const updateQty = (id, delta) => {
    const idx = cart.value.findIndex((i) => i.id_produk === id);
    if (idx < 0) return;
    const qty = cart.value[idx].quantity + delta;
    qty <= 0
        ? (cart.value.splice(idx, 1), toast("Produk dihapus."))
        : (cart.value[idx].quantity = qty);
};

const remove = (id) => {
    cart.value = cart.value.filter((i) => i.id_produk !== id);
    toast("Produk dihapus.");
};

const checkout = () => {
    if (!cart.value.length) return;

    if (!user.value) {
        toast("Silakan login untuk checkout.");
        return router.visit(route("login"));
    }

    const payload = cart.value.map((i) => ({
        id_produk: i.id_produk,
        qty: i.quantity,
    }));

    router.post(
        route("customer.cart.update"),
        { cart: payload },
        {
            onSuccess: () => {
                isCartOpen.value = false;
                cart.value = [];
                router.visit(route("customer.checkout"));
            },
        },
    );
};

const openDetail = (p) => {
    selectedProduct.value = p;
    detailQty.value = 1;
};
const closeDetail = () => {
    selectedProduct.value = null;
};
const closeNota = () => {
    showNota.value = false;
    notaData.value = null;
};
const printNota = () => window.print();

const icon = (p) => {
    const n = (p.nama_produk || "").toLowerCase();
    const k = (p.kategori || "").toLowerCase();
    if (n.includes("laptop") || n.includes("notebook") || k.includes("laptop"))
        return "laptop";
    if (n.includes("monitor") || n.includes("layar")) return "monitor";
    if (n.includes("keyboard") || n.includes("keychron")) return "keyboard";
    if (n.includes("mouse")) return "mouse";
    if (n.includes("kabel") || n.includes("hdmi")) return "cable";
    return "device";
};
</script>

<template>
    <Head title="TechStore — Toko Komputer & Aksesoris">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div class="min-h-screen bg-gray-50 text-gray-800 font-sans antialiased">
        <!-- ─── Toast ─────────────────────────────────────── -->
        <Transition name="toast">
            <div
                v-if="showToast"
                class="fixed bottom-5 right-5 z-[70] flex items-center gap-3 bg-navy-900 text-white text-sm px-5 py-3.5 rounded-xl shadow-lg max-w-xs border border-navy-700"
            >
                <span
                    class="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center text-[10px] font-bold shrink-0"
                    >✓</span
                >
                {{ toastMessage }}
            </div>
        </Transition>

        <!-- ─── Navbar ─────────────────────────────────────── -->
        <StoreNavbar
            :user="user"
            :canLogin="canLogin"
            :canRegister="canRegister"
            :cartCount="cartCount"
            :searchQuery="searchQuery"
            @update:searchQuery="searchQuery = $event"
            @openCart="isCartOpen = true"
        />

        <main>
            <!-- ─── Hero ─────────────────────────────────── -->
            <section class="relative overflow-hidden bg-navy-800">
                <!-- Dual pane background: White left, Dark right -->
                <div class="absolute inset-0 flex">
                    <!-- Left white panel melting into the dark -->
                    <div class="w-full lg:w-[45%] bg-white relative">
                        <div
                            class="absolute inset-y-0 -right-32 w-32 bg-gradient-to-r from-white to-transparent z-10 pointer-events-none"
                        ></div>
                        <div
                            class="absolute inset-0 opacity-[0.03]"
                            style="
                                background-image: radial-gradient(
                                    circle,
                                    #1e2d4f 1px,
                                    transparent 1px
                                );
                                background-size: 24px 24px;
                            "
                        ></div>
                    </div>
                </div>

                <!-- Dark side ambient tech effects -->
                <div
                    class="absolute inset-y-0 right-0 w-full lg:w-[65%] z-0 pointer-events-none overflow-hidden"
                >
                    <!-- Glowing ambient orbs -->
                    <div
                        class="absolute top-[20%] right-[10%] w-96 h-96 bg-brand-orange-500/10 blur-[130px] rounded-full"
                    ></div>
                    <div
                        class="absolute bottom-[10%] right-[30%] w-80 h-80 bg-blue-500/10 blur-[120px] rounded-full"
                    ></div>
                </div>

                <div
                    class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 relative z-10"
                >
                    <div
                        class="flex flex-col lg:flex-row items-center gap-12 lg:gap-8"
                    >
                        <!-- copy -->
                        <div class="w-full lg:w-[45%] xl:w-[40%] space-y-6">
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-orange-50 text-brand-orange-600 text-xs font-semibold tracking-wide border border-brand-orange-100"
                            >
                                <span
                                    class="w-1.5 h-1.5 rounded-full bg-brand-orange-500"
                                ></span>
                                Toko Komputer Terpercaya
                            </span>
                            <h1
                                class="text-3xl sm:text-4xl lg:text-5xl xl:text-[3.25rem] font-extrabold text-navy-950 leading-[1.15] tracking-tight"
                            >
                                Peralatan kerja &amp; gaming,
                                <span class="text-brand-orange-500"
                                    >satu tempat.</span
                                >
                            </h1>
                            <p
                                class="text-gray-500 text-base lg:text-lg leading-relaxed max-w-lg"
                            >
                                Laptop, monitor, dan aksesoris pilihan.
                                Spesifikasi jelas, garansi resmi, kirim ke
                                seluruh Indonesia.
                            </p>
                            <div class="flex flex-wrap gap-3 pt-1">
                                <a
                                    href="#katalog"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-navy-950 hover:bg-navy-800 text-white text-sm font-semibold rounded-lg transition-colors shadow-lg shadow-navy-950/20"
                                >
                                    Lihat Katalog
                                    <svg
                                        class="w-4 h-4"
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
                                </a>
                                <a
                                    href="#keunggulan"
                                    class="inline-flex items-center px-6 py-3 bg-white border border-gray-200 hover:border-navy-300 text-navy-800 text-sm font-medium rounded-lg transition-colors"
                                >
                                    Mengapa kami?
                                </a>
                            </div>

                            <!-- stats strip -->
                            <div class="flex items-center gap-6 pt-4">
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-navy-900">
                                        {{ products.length }}+
                                    </p>
                                    <p
                                        class="text-xs text-gray-500 font-medium mt-0.5"
                                    >
                                        Produk
                                    </p>
                                </div>
                                <span class="w-px h-10 bg-gray-200"></span>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-navy-900">
                                        {{ categories.length }}
                                    </p>
                                    <p
                                        class="text-xs text-gray-500 font-medium mt-0.5"
                                    >
                                        Kategori
                                    </p>
                                </div>
                                <span class="w-px h-10 bg-gray-200"></span>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-navy-900">
                                        1 Th
                                    </p>
                                    <p
                                        class="text-xs text-gray-500 font-medium mt-0.5"
                                    >
                                        Garansi Resmi
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- abstract tech dashboard -->
                        <div
                            class="w-full lg:w-[55%] xl:w-[60%] relative mt-12 lg:mt-0 flex justify-end"
                        >
                            <!-- Glassmorphism Panel -->
                            <div
                                class="w-full max-w-[50rem] relative bg-navy-700/70 backdrop-blur-xl border border-white/10 shadow-2xl shadow-black/40 rounded-3xl p-6 md:p-8 overflow-hidden"
                            >
                                <div
                                    class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-8"
                                >
                                    <div>
                                        <h2
                                            class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white mb-1"
                                        >
                                            TECH<span
                                                class="text-brand-orange-500"
                                                >STORE</span
                                            >
                                        </h2>
                                        <p
                                            class="text-gray-400 text-xs sm:text-sm font-medium"
                                        >
                                            Trusted Technology Partner
                                        </p>
                                    </div>
                                    <div
                                        class="flex items-center gap-3 bg-navy-800/80 border border-green-500/20 px-3.5 py-2 rounded-xl backdrop-blur-sm self-start"
                                    >
                                        <div
                                            class="w-2 h-2 rounded-full bg-green-500 animate-[pulse_2s_ease-in-out_infinite] shadow-[0_0_8px_rgba(34,197,94,0.8)]"
                                        ></div>
                                        <div>
                                            <p
                                                class="text-[10px] text-gray-400 leading-none mb-0.5"
                                            >
                                                System Status
                                            </p>
                                            <p
                                                class="text-xs text-green-400 font-semibold tracking-wide"
                                            >
                                                All Systems Operational
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cards Grid -->
                                <div
                                    class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-6"
                                >
                                    <!-- Card 1: Garansi -->
                                    <div
                                        class="bg-navy-800/80 rounded-2xl p-4 sm:p-5 border border-white/5 hover:border-brand-orange-500/40 transition-colors group flex flex-col items-center text-center shadow-inner"
                                    >
                                        <div
                                            class="w-12 h-12 rounded-[14px] border border-brand-orange-500/30 bg-brand-orange-500/10 flex items-center justify-center text-brand-orange-500 mb-4 group-hover:bg-brand-orange-500/20 group-hover:scale-110 transition-all duration-300 shadow-[0_0_15px_rgba(249,115,22,0.1)] group-hover:shadow-[0_0_20px_rgba(249,115,22,0.3)]"
                                        >
                                            <svg
                                                class="w-6 h-6"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.5"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                        </div>
                                        <h3
                                            class="text-xs sm:text-sm font-bold text-gray-200 mb-1.5"
                                        >
                                            Garansi Resmi
                                        </h3>
                                        <p
                                            class="text-[10px] text-gray-500 leading-relaxed"
                                        >
                                            Produk bergaransi resmi & terpercaya
                                        </p>
                                    </div>

                                    <!-- Card 2: Pembayaran -->
                                    <div
                                        class="bg-[#0a1120]/60 rounded-2xl p-4 sm:p-5 border border-white/5 hover:border-brand-orange-500/40 transition-colors group flex flex-col items-center text-center shadow-inner"
                                    >
                                        <div
                                            class="w-12 h-12 rounded-[14px] border border-brand-orange-500/30 bg-brand-orange-500/10 flex items-center justify-center text-brand-orange-500 mb-4 group-hover:bg-brand-orange-500/20 group-hover:scale-110 transition-all duration-300 shadow-[0_0_15px_rgba(249,115,22,0.1)] group-hover:shadow-[0_0_20px_rgba(249,115,22,0.3)]"
                                        >
                                            <svg
                                                class="w-6 h-6"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.5"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"
                                                />
                                            </svg>
                                        </div>
                                        <h3
                                            class="text-xs sm:text-sm font-bold text-gray-200 mb-1.5"
                                        >
                                            Pembayaran Aman
                                        </h3>
                                        <p
                                            class="text-[10px] text-gray-500 leading-relaxed"
                                        >
                                            Transaksi aman dengan sistem
                                            terenkripsi
                                        </p>
                                    </div>

                                    <!-- Card 3: Pengiriman -->
                                    <div
                                        class="bg-navy-800/80 rounded-2xl p-4 sm:p-5 border border-white/5 hover:border-brand-orange-500/40 transition-colors group flex flex-col items-center text-center shadow-inner"
                                    >
                                        <div
                                            class="w-12 h-12 rounded-[14px] border border-brand-orange-500/30 bg-brand-orange-500/10 flex items-center justify-center text-brand-orange-500 mb-4 group-hover:bg-brand-orange-500/20 group-hover:scale-110 transition-all duration-300 shadow-[0_0_15px_rgba(249,115,22,0.1)] group-hover:shadow-[0_0_20px_rgba(249,115,22,0.3)]"
                                        >
                                            <svg
                                                class="w-6 h-6"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.5"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M15.75 10.5V6a3.75 3.75 0 00-3.75-3.75h-3.5m-1.5 15v-4.5m4.5 4.5v-4.5m4.5 4.5v-4.5m4.5 4.5v-4.5m-15 4.5H3m18 0h-3"
                                                />
                                            </svg>
                                        </div>
                                        <h3
                                            class="text-xs sm:text-sm font-bold text-gray-200 mb-1.5"
                                        >
                                            Pengiriman Cepat
                                        </h3>
                                        <p
                                            class="text-[10px] text-gray-500 leading-relaxed"
                                        >
                                            Kirim ke seluruh area Indonesia
                                        </p>
                                    </div>

                                    <!-- Card 4: Support -->
                                    <div
                                        class="bg-navy-800/80 rounded-2xl p-4 sm:p-5 border border-white/5 hover:border-brand-orange-500/40 transition-colors group flex flex-col items-center text-center shadow-inner"
                                    >
                                        <div
                                            class="w-12 h-12 rounded-[14px] border border-brand-orange-500/30 bg-brand-orange-500/10 flex items-center justify-center text-brand-orange-500 mb-4 group-hover:bg-brand-orange-500/20 group-hover:scale-110 transition-all duration-300 shadow-[0_0_15px_rgba(249,115,22,0.1)] group-hover:shadow-[0_0_20px_rgba(249,115,22,0.3)]"
                                        >
                                            <svg
                                                class="w-6 h-6"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.5"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.22-3.92-6.816-6.816l1.293-.97c.362-.271.527-.733.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"
                                                />
                                            </svg>
                                        </div>
                                        <h3
                                            class="text-xs sm:text-sm font-bold text-gray-200 mb-1.5"
                                        >
                                            Support 24/7
                                        </h3>
                                        <p
                                            class="text-[10px] text-gray-500 leading-relaxed"
                                        >
                                            Tim support siap membantu kapan saja
                                        </p>
                                    </div>
                                </div>

                                <!-- Bottom Status Bar -->
                                <div
                                    class="bg-navy-800/80 border border-white/5 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-6"
                                >
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center shadow-inner border border-blue-500/20"
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
                                                    d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"
                                                />
                                                <circle cx="9" cy="7" r="4" />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M23 21v-2a4 4 0 00-3-3.87"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M16 3.13a4 4 0 010 7.75"
                                                />
                                            </svg>
                                        </div>
                                        <div>
                                            <p
                                                class="text-xs sm:text-sm font-bold text-gray-200"
                                            >
                                                Trusted by thousands
                                            </p>
                                            <p
                                                class="text-[11px] sm:text-xs text-gray-500"
                                            >
                                                of happy customers
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Decorative Sparkline -->
                                    <div class="w-32 h-10 hidden md:block">
                                        <svg
                                            class="w-full h-full text-brand-orange-500 opacity-60 drop-shadow-[0_0_4px_rgba(249,115,22,0.6)]"
                                            viewBox="0 0 100 30"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.5"
                                        >
                                            <path
                                                d="M0,25 L15,18 L35,22 L55,10 L75,15 L100,5"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="text-right hidden sm:block">
                                            <p
                                                class="text-[11px] font-bold text-gray-300"
                                            >
                                                Kepuasan Pelanggan
                                            </p>
                                        </div>
                                        <div
                                            class="w-12 h-12 relative flex items-center justify-center"
                                        >
                                            <svg
                                                class="w-full h-full transform -rotate-90"
                                            >
                                                <circle
                                                    cx="24"
                                                    cy="24"
                                                    r="20"
                                                    stroke="rgba(255,255,255,0.05)"
                                                    stroke-width="4"
                                                    fill="none"
                                                />
                                                <circle
                                                    cx="24"
                                                    cy="24"
                                                    r="20"
                                                    stroke="#0ea5e9"
                                                    stroke-width="4"
                                                    fill="none"
                                                    stroke-dasharray="125.6"
                                                    stroke-dashoffset="3"
                                                    stroke-linecap="round"
                                                    class="drop-shadow-[0_0_6px_rgba(14,165,233,0.8)]"
                                                />
                                            </svg>
                                            <span
                                                class="absolute text-[11px] font-bold text-white"
                                                >98%</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Ambient glowing dots around panel -->
                                <div
                                    class="absolute top-10 flex items-center justify-center right-10 -mr-6"
                                >
                                    <div
                                        class="w-1.5 h-1.5 bg-brand-orange-500 rounded-full shadow-[0_0_10px_rgba(249,115,22,1)]"
                                    ></div>
                                </div>
                                <div
                                    class="absolute bottom-20 flex items-center justify-center left-4 -ml-4"
                                >
                                    <div
                                        class="w-2 h-2 bg-blue-500 rounded-full shadow-[0_0_10px_rgba(59,130,246,1)]"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ─── Catalog ───────────────────────────────── -->
            <section id="katalog" class="bg-gray-50 border-t border-gray-100">
                <div
                    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20"
                >
                    <!-- header row -->
                    <div
                        class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10"
                    >
                        <div>
                            <h2 class="text-2xl font-bold text-navy-950">
                                Semua Produk
                            </h2>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ filteredProducts.length }} produk ditemukan
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- mobile search -->
                            <div
                                class="flex lg:hidden items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2 flex-1 focus-within:border-brand-orange-500 transition-colors"
                            >
                                <svg
                                    class="w-4 h-4 text-gray-400 shrink-0"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    />
                                </svg>
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Cari…"
                                    class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none w-full"
                                />
                            </div>
                            <select
                                v-model="sortBy"
                                class="text-sm bg-white border border-gray-200 rounded-lg px-3.5 py-2 text-gray-700 outline-none cursor-pointer hover:border-gray-400 transition-colors focus:border-brand-orange-500"
                            >
                                <option value="default">Rekomendasi</option>
                                <option value="price-low">Harga ↑</option>
                                <option value="price-high">Harga ↓</option>
                                <option value="name">A – Z</option>
                                <option value="stock">Stok</option>
                            </select>
                        </div>
                    </div>

                    <!-- category chips -->
                    <div
                        class="flex gap-2 overflow-x-auto pb-3 mb-8 no-scrollbar"
                    >
                        <button
                            @click="selectedCategory = 'Semua'"
                            :class="
                                selectedCategory === 'Semua'
                                    ? 'bg-brand-orange-500 text-white border-brand-orange-500 shadow-sm'
                                    : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400'
                            "
                            class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2 rounded-full border text-sm font-medium transition-all cursor-pointer"
                        >
                            Semua
                            <span
                                :class="
                                    selectedCategory === 'Semua'
                                        ? 'text-white/70'
                                        : 'text-gray-400'
                                "
                                class="text-xs font-semibold"
                                >{{ categoryCount.Semua }}</span
                            >
                        </button>
                        <button
                            v-for="cat in categories"
                            :key="cat"
                            @click="selectedCategory = cat"
                            :class="
                                selectedCategory === cat
                                    ? 'bg-brand-orange-500 text-white border-brand-orange-500 shadow-sm'
                                    : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400'
                            "
                            class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2 rounded-full border text-sm font-medium transition-all cursor-pointer"
                        >
                            {{ cat }}
                            <span
                                :class="
                                    selectedCategory === cat
                                        ? 'text-white/70'
                                        : 'text-gray-400'
                                "
                                class="text-xs font-semibold"
                                >{{ categoryCount[cat] || 0 }}</span
                            >
                        </button>
                    </div>

                    <!-- grid -->
                    <div
                        v-if="filteredProducts.length"
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5"
                    >
                        <article
                            v-for="p in filteredProducts"
                            :key="p.id_produk"
                            class="bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col group hover:shadow-lg transition-all duration-300"
                        >
                            <!-- image / icon area -->
                            <div
                                @click="openDetail(p)"
                                class="aspect-[4/3] bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center cursor-pointer relative overflow-hidden"
                            >
                                <!-- subtle pattern -->
                                <div
                                    class="absolute inset-0 opacity-40"
                                    style="
                                        background-image: radial-gradient(
                                            circle,
                                            #d1d5db 0.5px,
                                            transparent 0.5px
                                        );
                                        background-size: 16px 16px;
                                    "
                                ></div>
                                <!-- icon -->
                                <template
                                    v-if="p.gambar || icon(p) === 'laptop'"
                                >
                                    <img
                                        :src="
                                            p.gambar ||
                                            'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&q=80&w=800'
                                        "
                                        :alt="p.nama_produk"
                                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    />
                                    <!-- subtle shade for contrast -->
                                    <div
                                        class="absolute inset-0 bg-black/5 group-hover:bg-transparent transition-colors duration-300"
                                    ></div>
                                </template>
                                <div
                                    v-else
                                    class="relative w-14 h-14 text-gray-300 group-hover:text-navy-300 group-hover:scale-110 transition-all duration-300"
                                >
                                    <svg
                                        v-if="icon(p) === 'laptop'"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                        />
                                    </svg>
                                    <svg
                                        v-else-if="icon(p) === 'monitor'"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                        />
                                    </svg>
                                    <svg
                                        v-else-if="icon(p) === 'keyboard'"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1"
                                    >
                                        <rect
                                            x="2"
                                            y="6"
                                            width="20"
                                            height="12"
                                            rx="2"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            d="M6 10h.01M10 10h.01M14 10h.01M18 10h.01M6 14h12M6 10h.01"
                                        />
                                    </svg>
                                    <svg
                                        v-else-if="icon(p) === 'mouse'"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 2C8.686 2 6 4.686 6 8v8c0 3.314 2.686 6 6 6s6-2.686 6-6V8c0-3.314-2.686-6-6-6zM12 2v7"
                                        />
                                    </svg>
                                    <svg
                                        v-else-if="icon(p) === 'cable'"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"
                                        />
                                    </svg>
                                    <svg
                                        v-else
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1"
                                    >
                                        <rect
                                            x="5"
                                            y="2"
                                            width="14"
                                            height="20"
                                            rx="2"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            d="M12 18h.01"
                                        />
                                    </svg>
                                </div>

                                <!-- stock badge -->
                                <span
                                    :class="
                                        p.stok > 20
                                            ? 'text-emerald-700 bg-emerald-50 border-emerald-100'
                                            : 'text-amber-700 bg-amber-50 border-amber-100'
                                    "
                                    class="absolute bottom-3 right-3 text-[11px] font-semibold px-2 py-0.5 rounded-md border"
                                >
                                    {{
                                        p.stok > 20
                                            ? "Stok tersedia"
                                            : `Sisa ${p.stok}`
                                    }}
                                </span>
                            </div>

                            <!-- body -->
                            <div class="p-4 flex flex-col gap-2 flex-1">
                                <p
                                    class="text-[11px] font-semibold uppercase tracking-widest text-brand-orange-500"
                                >
                                    {{ p.kategori }}
                                </p>
                                <h3
                                    @click="openDetail(p)"
                                    class="font-semibold text-sm text-navy-900 leading-snug line-clamp-2 cursor-pointer hover:text-brand-orange-600 transition-colors"
                                    :title="p.nama_produk"
                                >
                                    {{ p.nama_produk }}
                                </h3>
                                <p
                                    class="font-bold text-base text-navy-950 mt-auto pt-1"
                                >
                                    {{ rp(p.harga) }}
                                </p>
                                <div
                                    class="flex gap-2 pt-3 border-t border-gray-100"
                                >
                                    <button
                                        @click="openDetail(p)"
                                        class="flex-1 py-2 text-xs font-medium border border-gray-200 rounded-lg hover:border-navy-300 hover:text-navy-700 transition-colors cursor-pointer text-gray-600"
                                    >
                                        Detail
                                    </button>
                                    <button
                                        @click="addToCart(p)"
                                        class="flex-[2] py-2 text-xs font-semibold bg-navy-900 hover:bg-brand-orange-500 text-white rounded-lg transition-colors cursor-pointer"
                                    >
                                        + Keranjang
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- empty state -->
                    <div v-else class="py-24 text-center">
                        <p class="text-3xl mb-3">🔍</p>
                        <h3 class="font-semibold text-navy-900">
                            Tidak ada hasil
                        </h3>
                        <p class="text-sm text-gray-500 mt-1 mb-5">
                            Coba kata kunci lain atau ubah kategori.
                        </p>
                        <button
                            @click="
                                searchQuery = '';
                                selectedCategory = 'Semua';
                            "
                            class="px-5 py-2.5 bg-navy-900 text-white text-sm font-medium rounded-lg cursor-pointer hover:bg-navy-800 transition-colors"
                        >
                            Reset Filter
                        </button>
                    </div>
                </div>
            </section>

            <!-- ─── Keunggulan ─────────────────────────────── -->
            <section class="bg-white border-t border-gray-100 py-16 lg:py-24">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div
                        class="flex flex-col md:flex-row items-center justify-between gap-8 mb-12"
                    >
                        <div>
                            <h2 class="text-2xl font-bold text-navy-950">
                                Mengapa TechStore?
                            </h2>
                            <p class="text-sm text-gray-500 mt-2 max-w-md">
                                Solusi lengkap untuk kebutuhan teknologi Anda
                                dengan kualitas terdepan.
                            </p>
                        </div>
                        <Link
                            :href="route('advantages')"
                            class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-orange-500 hover:text-brand-orange-600 transition-colors"
                        >
                            Selengkapnya &rarr;
                        </Link>
                    </div>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
                    >
                        <div
                            v-for="(item, idx) in [
                                {
                                    num: '01',
                                    title: 'Bebas ongkir',
                                    desc: 'Gratis kirim ke seluruh area.',
                                },
                                {
                                    num: '02',
                                    title: 'Garansi resmi',
                                    desc: 'Asli dan langsung dari distributor.',
                                },
                                {
                                    num: '03',
                                    title: 'Transaksi aman',
                                    desc: 'Pembayaran terenkripsi, instan.',
                                },
                                {
                                    num: '04',
                                    title: 'Bantuan 24/7',
                                    desc: 'Tim support selalu siap siaga.',
                                },
                            ]"
                            :key="item.title"
                            class="bg-gray-50 border-l-2 border-l-brand-orange-500 border border-gray-100/0 rounded-r-xl p-6 hover:bg-white hover:shadow-md hover:border-gray-200 transition-all duration-300 group"
                        >
                            <div class="text-xs font-bold text-gray-400 mb-2">
                                {{ item.num }}
                            </div>
                            <h3
                                class="font-semibold text-navy-900 text-sm mb-1.5"
                            >
                                {{ item.title }}
                            </h3>
                            <p class="text-sm text-gray-500 leading-relaxed">
                                {{ item.desc }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- ─── Footer ────────────────────────────────────── -->
        <StoreFooter
            :categories="categories"
            @selectCategory="selectedCategory = $event"
        />

        <!-- ═══════════════════════════════════════════════ -->
        <!-- Product Detail Modal                           -->
        <!-- ═══════════════════════════════════════════════ -->
        <Transition name="fade">
            <div
                v-if="selectedProduct"
                class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4"
                @click.self="closeDetail"
            >
                <div
                    class="bg-white w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl overflow-hidden shadow-2xl"
                >
                    <div
                        class="h-44 bg-gradient-to-br from-navy-900 via-navy-800 to-navy-950 flex items-center justify-center relative overflow-hidden"
                    >
                        <template
                            v-if="
                                selectedProduct.gambar ||
                                icon(selectedProduct) === 'laptop'
                            "
                        >
                            <img
                                :src="
                                    selectedProduct.gambar ||
                                    'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&q=80&w=800'
                                "
                                :alt="selectedProduct.nama_produk"
                                class="absolute inset-0 w-full h-full object-cover"
                            />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"
                            ></div>
                        </template>
                        <template v-else>
                            <div
                                class="absolute inset-0 opacity-[0.06]"
                                style="
                                    background-image:
                                        repeating-linear-gradient(
                                            0deg,
                                            #fff 0,
                                            #fff 1px,
                                            transparent 1px,
                                            transparent 28px
                                        ),
                                        repeating-linear-gradient(
                                            90deg,
                                            #fff 0,
                                            #fff 1px,
                                            transparent 1px,
                                            transparent 28px
                                        );
                                "
                            ></div>
                            <svg
                                class="w-16 h-16 text-white/15 relative"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="0.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                />
                            </svg>
                        </template>
                        <button
                            @click="closeDetail"
                            class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white cursor-pointer transition-colors"
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
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-5">
                        <div>
                            <p
                                class="text-[11px] font-bold uppercase tracking-widest text-brand-orange-500"
                            >
                                {{ selectedProduct.kategori }}
                            </p>
                            <h3 class="font-bold text-xl text-navy-950 mt-1">
                                {{ selectedProduct.nama_produk }}
                            </h3>
                            <p
                                class="font-bold text-2xl text-brand-orange-500 mt-1"
                            >
                                {{ rp(selectedProduct.harga) }}
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <div
                                class="flex-1 bg-gray-50 rounded-lg p-3 text-center border border-gray-100"
                            >
                                <p
                                    class="text-[10px] text-gray-400 font-semibold uppercase"
                                >
                                    Kondisi
                                </p>
                                <p
                                    class="text-sm font-semibold text-navy-800 mt-0.5"
                                >
                                    Baru
                                </p>
                            </div>
                            <div
                                class="flex-1 bg-gray-50 rounded-lg p-3 text-center border border-gray-100"
                            >
                                <p
                                    class="text-[10px] text-gray-400 font-semibold uppercase"
                                >
                                    Stok
                                </p>
                                <p
                                    class="text-sm font-semibold text-navy-800 mt-0.5"
                                >
                                    {{ selectedProduct.stok }} unit
                                </p>
                            </div>
                            <div
                                class="flex-1 bg-gray-50 rounded-lg p-3 text-center border border-gray-100"
                            >
                                <p
                                    class="text-[10px] text-gray-400 font-semibold uppercase"
                                >
                                    Garansi
                                </p>
                                <p
                                    class="text-sm font-semibold text-navy-800 mt-0.5"
                                >
                                    1 Tahun
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div
                                class="flex items-center border border-gray-200 rounded-lg overflow-hidden"
                            >
                                <button
                                    @click="detailQty > 1 ? detailQty-- : null"
                                    class="px-3.5 py-2 text-gray-600 hover:bg-gray-50 font-bold transition-colors cursor-pointer"
                                >
                                    −
                                </button>
                                <span
                                    class="px-4 py-2 text-sm font-bold text-navy-900 border-x border-gray-200 select-none"
                                    >{{ detailQty }}</span
                                >
                                <button
                                    @click="
                                        detailQty < selectedProduct.stok
                                            ? detailQty++
                                            : null
                                    "
                                    class="px-3.5 py-2 text-gray-600 hover:bg-gray-50 font-bold transition-colors cursor-pointer"
                                >
                                    +
                                </button>
                            </div>
                            <p class="font-bold text-navy-900 text-lg">
                                {{ rp(+selectedProduct.harga * detailQty) }}
                            </p>
                        </div>

                        <button
                            @click="
                                addToCart(selectedProduct, detailQty);
                                closeDetail();
                            "
                            class="w-full py-3 bg-brand-orange-500 hover:bg-brand-orange-600 text-white font-semibold text-sm rounded-xl transition-colors cursor-pointer shadow-sm"
                        >
                            Tambahkan ke Keranjang
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- ═══════════════════════════════════════════════ -->
        <!-- Cart Drawer                                     -->
        <!-- ═══════════════════════════════════════════════ -->
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
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ cartCount }} item
                            </p>
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
                            v-if="!cart.length"
                            class="h-full flex flex-col items-center justify-center gap-3 text-center py-16"
                        >
                            <span class="text-4xl">🛒</span>
                            <p class="font-semibold text-navy-800">
                                Keranjang kosong
                            </p>
                            <p class="text-sm text-gray-400">
                                Tambahkan produk dari katalog.
                            </p>
                        </div>

                        <div
                            v-for="item in cart"
                            :key="item.id_produk"
                            class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100"
                        >
                            <div
                                class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center shrink-0 text-gray-300 relative overflow-hidden"
                            >
                                <img
                                    v-if="
                                        item.gambar || icon(item) === 'laptop'
                                    "
                                    :src="
                                        item.gambar ||
                                        'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&q=80&w=800'
                                    "
                                    class="absolute inset-0 w-full h-full object-cover"
                                />
                                <svg
                                    v-else
                                    class="w-5 h-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm font-semibold text-navy-900 line-clamp-1"
                                >
                                    {{ item.nama_produk }}
                                </p>
                                <p
                                    class="text-xs text-brand-orange-600 font-bold"
                                >
                                    {{ rp(+item.harga * item.quantity) }}
                                </p>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <button
                                    @click="updateQty(item.id_produk, -1)"
                                    class="w-6 h-6 text-xs font-bold bg-white border border-gray-200 rounded-md hover:bg-gray-100 cursor-pointer flex items-center justify-center"
                                >
                                    −
                                </button>
                                <span
                                    class="w-6 text-center text-xs font-bold"
                                    >{{ item.quantity }}</span
                                >
                                <button
                                    @click="updateQty(item.id_produk, 1)"
                                    class="w-6 h-6 text-xs font-bold bg-white border border-gray-200 rounded-md hover:bg-gray-100 cursor-pointer flex items-center justify-center"
                                >
                                    +
                                </button>
                                <button
                                    @click="remove(item.id_produk)"
                                    class="ml-1 text-gray-300 hover:text-red-500 transition-colors cursor-pointer"
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
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="cart.length"
                        class="px-5 py-4 border-t border-gray-100 space-y-3 bg-gray-50"
                    >
                        <div class="space-y-1.5 text-sm">
                            <div class="flex justify-between text-gray-500">
                                <span>Subtotal</span>
                                <span class="font-semibold text-navy-900">{{
                                    rp(cartTotal)
                                }}</span>
                            </div>
                            <div
                                class="flex justify-between text-emerald-600 text-xs"
                            >
                                <span>Ongkir</span
                                ><span class="font-semibold">Gratis</span>
                            </div>
                            <div
                                class="flex justify-between font-bold text-navy-950 pt-2 border-t border-gray-200"
                            >
                                <span>Total</span
                                ><span>{{ rp(cartTotal) }}</span>
                            </div>
                        </div>
                        <button
                            @click="checkout"
                            class="w-full py-3 bg-brand-orange-500 hover:bg-brand-orange-600 text-white font-semibold text-sm rounded-xl transition-colors cursor-pointer flex items-center justify-center gap-2 shadow-sm"
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
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                />
                            </svg>
                            Bayar &amp; Cetak Nota
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- ═══════════════════════════════════════════════ -->
        <!-- Nota / Receipt Modal                           -->
        <!-- ═══════════════════════════════════════════════ -->
        <Transition name="fade">
            <div
                v-if="showNota && notaData"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-navy-950/80 backdrop-blur-sm p-4 overflow-y-auto print:absolute print:inset-auto print:w-full print:bg-transparent print:backdrop-blur-none print:overflow-visible"
                @click.self="closeNota"
            >
                <div
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden flex flex-col font-mono text-sm relative print:shadow-none print:border-none print:max-w-none print:w-full"
                >
                    <div
                        id="nota-area"
                        class="bg-white p-5 text-black space-y-3 leading-tight font-['Poppins']"
                    >
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

                        <div
                            class="border-t-2 border-dashed border-gray-400 my-1.5"
                        ></div>

                        <div
                            class="space-y-1 text-xs sm:text-[13px] font-medium text-gray-900"
                        >
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">No. Faktur:</span>
                                <span class="font-mono font-bold text-black">{{
                                    notaData.no
                                }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Waktu:</span>
                                <span
                                    class="font-bold text-black whitespace-nowrap"
                                    >{{ notaData.date }}</span
                                >
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Kasir:</span>
                                <span class="font-bold text-black"
                                    >Pembayaran Online</span
                                >
                            </div>
                        </div>

                        <div
                            class="border-t-2 border-dashed border-gray-400 my-1.5"
                        ></div>

                        <div class="space-y-2 text-xs sm:text-[13px]">
                            <div
                                v-for="item in notaData.items"
                                :key="item.id_produk"
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
                                    <span class="font-['IBM_Plex_Mono']"
                                        >{{ item.quantity }} x
                                        {{ rp(item.harga) }}</span
                                    >
                                    <span
                                        class="font-['IBM_Plex_Mono'] font-extrabold"
                                        >{{
                                            rp(+item.harga * item.quantity)
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div
                            class="border-t-2 border-dashed border-gray-400 my-1.5"
                        ></div>

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
                                    >{{ rp(notaData.total) }}</span
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

                        <div
                            class="border-t-2 border-dashed border-gray-400 my-1.5"
                        ></div>

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
                                === STRUK PEMBELIAN ONLINE ===
                            </div>
                        </div>
                    </div>

                    <div
                        class="no-print flex gap-2 p-3.5 bg-gray-100 border-t border-gray-300"
                    >
                        <button
                            @click="printNota"
                            class="flex-1 py-2.5 bg-navy-900 hover:bg-brand-orange-600 text-white text-xs font-bold rounded-xl cursor-pointer transition-colors flex items-center justify-center gap-1.5 shadow-sm"
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
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                                />
                            </svg>
                            Cetak
                        </button>
                        <button
                            @click="closeNota"
                            class="flex-1 py-2.5 bg-white hover:bg-gray-200 text-gray-800 text-xs font-bold rounded-xl border border-gray-300 cursor-pointer transition-colors"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* toast */
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateY(12px);
}

/* fade backdrop */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* cart slide */
.slide-right-enter-active,
.slide-right-leave-active {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-right-enter-from,
.slide-right-leave-to {
    transform: translateX(100%);
}

@media print {
    @page {
        margin: 0;
        size: auto;
    }
    body * {
        visibility: hidden;
    }

    #nota-area,
    #nota-area * {
        visibility: visible;
        color: #000;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    #nota-area {
        position: absolute;
        left: 0;
        top: 0;
        margin: 0;
        padding: 0;
        width: 100%;
        max-width: 320px;
        background: white;
        box-shadow: none;
        border-radius: 0;
        font-family: "Poppins", sans-serif;
    }

    .no-print {
        display: none !important;
    }
}
</style>
