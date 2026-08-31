<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    products:   { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
});

const page = usePage();
const user  = computed(() => page.props.auth?.user);

const searchQuery      = ref('');
const selectedCategory = ref('Semua');
const sortBy           = ref('default');
const isCartOpen       = ref(false);
const cart             = ref([]);
const toastMessage     = ref('');
const showToast        = ref(false);
const selectedProduct  = ref(null);
const detailQty        = ref(1);
const showNota         = ref(false);
const notaData         = ref(null);

/* ─── helpers ─────────────────────────────────────── */
const rp = (n) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR',
        minimumFractionDigits: 0 }).format(n);

const makeInvoiceNo = () => {
    const d = new Date();
    const pad = (x) => String(x).padStart(2, '0');
    return `INV/${d.getFullYear()}${pad(d.getMonth()+1)}${pad(d.getDate())}/${Math.floor(1000 + Math.random() * 9000)}`;
};

const nowId = () => {
    const d = new Date();
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const pad = (x) => String(x).padStart(2, '0');
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()} pukul ${pad(d.getHours())}.${pad(d.getMinutes())}`;
};

/* ─── computed ────────────────────────────────────── */
const filteredProducts = computed(() => {
    let r = [...props.products];
    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase();
        r = r.filter(p =>
            p.nama_produk?.toLowerCase().includes(q) ||
            p.kategori?.toLowerCase().includes(q)
        );
    }
    if (selectedCategory.value !== 'Semua')
        r = r.filter(p => p.kategori === selectedCategory.value);

    const sorts = {
        'price-low':  (a, b) => +a.harga - +b.harga,
        'price-high': (a, b) => +b.harga - +a.harga,
        'name':       (a, b) => a.nama_produk.localeCompare(b.nama_produk),
        'stock':      (a, b) => +b.stok - +a.stok,
    };
    if (sorts[sortBy.value]) r.sort(sorts[sortBy.value]);
    return r;
});

const categoryCount = computed(() => {
    const c = { Semua: props.products.length };
    props.products.forEach(p => { if (p.kategori) c[p.kategori] = (c[p.kategori] || 0) + 1; });
    return c;
});

const cartCount = computed(() => cart.value.reduce((t, i) => t + i.quantity, 0));
const cartTotal = computed(() => cart.value.reduce((t, i) => t + +i.harga * i.quantity, 0));

/* ─── actions ─────────────────────────────────────── */
const toast = (msg, ms = 3000) => {
    toastMessage.value = msg;
    showToast.value    = true;
    setTimeout(() => { showToast.value = false; }, ms);
};

const addToCart = (product, qty = 1) => {
    const idx = cart.value.findIndex(i => i.id_produk === product.id_produk);
    idx > -1 ? (cart.value[idx].quantity += qty) : cart.value.push({ ...product, quantity: qty });
    toast(`"${product.nama_produk}" ditambahkan.`);
};

const updateQty = (id, delta) => {
    const idx = cart.value.findIndex(i => i.id_produk === id);
    if (idx < 0) return;
    const qty = cart.value[idx].quantity + delta;
    qty <= 0 ? (cart.value.splice(idx, 1), toast('Produk dihapus.')) : (cart.value[idx].quantity = qty);
};

const remove = (id) => { cart.value = cart.value.filter(i => i.id_produk !== id); toast('Produk dihapus.'); };

const checkout = () => {
    if (!cart.value.length) return;
    notaData.value = {
        no:    makeInvoiceNo(),
        date:  nowId(),
        items: cart.value.map(i => ({ ...i })),
        total: cartTotal.value,
    };
    cart.value       = [];
    isCartOpen.value = false;
    showNota.value   = true;
};

const openDetail = (p)  => { selectedProduct.value = p; detailQty.value = 1; };
const closeDetail = ()  => { selectedProduct.value = null; };
const closeNota   = ()  => { showNota.value = false; notaData.value = null; };
const printNota   = ()  => window.print();

const icon = (p) => {
    const n = (p.nama_produk || '').toLowerCase();
    const k = (p.kategori   || '').toLowerCase();
    if (n.includes('laptop') || n.includes('notebook') || k.includes('laptop')) return 'laptop';
    if (n.includes('monitor') || n.includes('layar'))   return 'monitor';
    if (n.includes('keyboard') || n.includes('keychron')) return 'keyboard';
    if (n.includes('mouse'))                            return 'mouse';
    if (n.includes('kabel') || n.includes('hdmi'))      return 'cable';
    return 'device';
};
</script>

<template>
    <Head title="TechStore — Toko Komputer & Aksesoris">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet" />
    </Head>

    <div class="min-h-screen bg-white text-gray-900 font-['Poppins'] antialiased">

        <!-- ─── Toast ─────────────────────────────────────── -->
        <Transition name="toast">
            <div v-if="showToast"
                class="fixed bottom-5 right-5 z-[70] flex items-center gap-3 bg-gray-900 text-white text-sm px-4 py-3 rounded-xl shadow-2xl max-w-xs">
                <span class="text-green-400">✓</span>
                {{ toastMessage }}
            </div>
        </Transition>

        <!-- ─── Navbar ─────────────────────────────────────── -->
        <header class="sticky top-0 z-40 bg-white border-b border-gray-100">
            <div class="max-w-screen-xl mx-auto px-5 lg:px-10 h-16 flex items-center justify-between">

                <!-- brand -->
                <Link :href="route('home')" class="flex items-center gap-2 select-none">
                    <span class="w-7 h-7 rounded-lg bg-orange-500 flex items-center justify-center text-white">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <span class="font-bold text-gray-900 tracking-tight">TechStore</span>
                </Link>

                <!-- desktop nav -->
                <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-gray-500">
                    <a href="#katalog"    class="hover:text-gray-900 transition-colors">Katalog</a>
                    <a href="#keunggulan" class="hover:text-gray-900 transition-colors">Keunggulan</a>
                    <a href="#kontak"     class="hover:text-gray-900 transition-colors">Kontak</a>
                </nav>

                <!-- actions -->
                <div class="flex items-center gap-2">
                    <!-- search (desktop only) -->
                    <div class="hidden lg:flex items-center gap-1.5 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 w-44 focus-within:border-gray-400 transition-colors">
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input v-model="searchQuery" type="text" placeholder="Cari produk…"
                            class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none w-full" />
                    </div>

                    <!-- cart -->
                    <button @click="isCartOpen = true"
                        class="relative flex items-center gap-1.5 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-sm font-medium text-gray-700 hover:border-gray-400 transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="hidden sm:inline">Keranjang</span>
                        <span v-if="cartCount > 0"
                            class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] bg-orange-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center px-1">
                            {{ cartCount }}
                        </span>
                    </button>

                    <!-- auth -->
                    <Link v-if="user" :href="route('dashboard')"
                        class="hidden sm:flex items-center px-3.5 py-1.5 text-sm font-semibold bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link v-if="canLogin" :href="route('login')"
                            class="hidden sm:flex px-3.5 py-1.5 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                            Masuk
                        </Link>
                        <Link v-if="canRegister" :href="route('register')"
                            class="hidden sm:flex px-3.5 py-1.5 text-sm font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors">
                            Daftar
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <main>
            <!-- ─── Hero ─────────────────────────────────── -->
            <section class="border-b border-gray-100 bg-gray-50">
                <div class="max-w-screen-xl mx-auto px-5 lg:px-10 py-14 lg:py-20">
                    <div class="flex flex-col lg:flex-row items-center gap-12">

                        <!-- copy -->
                        <div class="flex-1 space-y-6 lg:max-w-xl">
                            <p class="text-xs font-semibold text-orange-500 uppercase tracking-widest">Toko Komputer Terpercaya</p>
                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight">
                                Peralatan kerja &amp; gaming,<br class="hidden sm:block"> satu tempat.
                            </h1>
                            <p class="text-gray-500 text-base leading-relaxed max-w-md">
                                Laptop, monitor, dan aksesoris pilihan. Spesifikasi jelas, garansi resmi, kirim ke seluruh Indonesia.
                            </p>
                            <div class="flex flex-wrap gap-3 pt-2">
                                <a href="#katalog"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                    Lihat Katalog
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </a>
                                <a href="#keunggulan"
                                    class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 hover:border-gray-400 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                                    Mengapa kami?
                                </a>
                            </div>
                            <!-- stats strip -->
                            <div class="flex items-center gap-6 pt-4 text-sm text-gray-500">
                                <span><strong class="text-gray-900 font-semibold">{{ products.length }}+</strong> Produk</span>
                                <span class="w-px h-4 bg-gray-200"></span>
                                <span><strong class="text-gray-900 font-semibold">{{ categories.length }}</strong> Kategori</span>
                                <span class="w-px h-4 bg-gray-200"></span>
                                <span><strong class="text-gray-900 font-semibold">Garansi</strong> Resmi</span>
                            </div>
                        </div>

                        <!-- featured card -->
                        <div class="w-full lg:w-80 xl:w-96">
                            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                                <div class="h-48 bg-gray-900 flex items-center justify-center relative">
                                    <!-- subtle grid pattern -->
                                    <div class="absolute inset-0 opacity-[0.07]"
                                        style="background-image:repeating-linear-gradient(0deg,#fff 0,#fff 1px,transparent 1px,transparent 32px),repeating-linear-gradient(90deg,#fff 0,#fff 1px,transparent 1px,transparent 32px)">
                                    </div>
                                    <svg class="w-20 h-20 text-white/20 relative" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="absolute top-3 left-3 text-[10px] font-bold bg-orange-500 text-white px-2 py-0.5 rounded tracking-wide uppercase">Terlaris</span>
                                </div>
                                <div class="p-5 space-y-4">
                                    <div>
                                        <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-widest">Gaming · Ready Stock</p>
                                        <h3 class="font-bold text-lg text-gray-900 mt-0.5">Laptop ASUS ROG</h3>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        <span class="text-[11px] px-2 py-0.5 bg-gray-100 text-gray-600 rounded font-medium">Core i9</span>
                                        <span class="text-[11px] px-2 py-0.5 bg-gray-100 text-gray-600 rounded font-medium">RTX 4080</span>
                                        <span class="text-[11px] px-2 py-0.5 bg-gray-100 text-gray-600 rounded font-medium">32 GB</span>
                                    </div>
                                    <div class="flex items-end justify-between gap-3">
                                        <p class="font-bold text-xl text-gray-900">{{ rp(25_000_000) }}</p>
                                        <button
                                            @click="addToCart({ id_produk:'hero-rog', nama_produk:'Laptop ASUS ROG', kategori:'Elektronik', harga:25000000, stok:50 })"
                                            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-colors cursor-pointer shrink-0">
                                            + Keranjang
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <!-- ─── Catalog ───────────────────────────────── -->
            <section id="katalog" class="max-w-screen-xl mx-auto px-5 lg:px-10 py-14 lg:py-16">

                <!-- header row -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Semua Produk</h2>
                        <p class="text-sm text-gray-500 mt-0.5">{{ filteredProducts.length }} produk ditemukan</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- mobile search -->
                        <div class="flex lg:hidden items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 flex-1">
                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input v-model="searchQuery" type="text" placeholder="Cari…"
                                class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none w-full"/>
                        </div>
                        <select v-model="sortBy"
                            class="text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-gray-700 outline-none cursor-pointer hover:border-gray-400 transition-colors">
                            <option value="default">Rekomendasi</option>
                            <option value="price-low">Harga ↑</option>
                            <option value="price-high">Harga ↓</option>
                            <option value="name">A – Z</option>
                            <option value="stock">Stok</option>
                        </select>
                    </div>
                </div>

                <!-- category chips -->
                <div class="flex gap-2 overflow-x-auto pb-2 mb-8 no-scrollbar">
                    <button @click="selectedCategory = 'Semua'"
                        :class="selectedCategory === 'Semua'
                            ? 'bg-gray-900 text-white border-gray-900'
                            : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400'"
                        class="shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border text-sm font-medium transition-colors cursor-pointer">
                        Semua
                        <span :class="selectedCategory === 'Semua' ? 'text-white/60' : 'text-gray-400'"
                            class="text-xs font-semibold">{{ categoryCount.Semua }}</span>
                    </button>
                    <button v-for="cat in categories" :key="cat" @click="selectedCategory = cat"
                        :class="selectedCategory === cat
                            ? 'bg-gray-900 text-white border-gray-900'
                            : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400'"
                        class="shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border text-sm font-medium transition-colors cursor-pointer">
                        {{ cat }}
                        <span :class="selectedCategory === cat ? 'text-white/60' : 'text-gray-400'"
                            class="text-xs font-semibold">{{ categoryCount[cat] || 0 }}</span>
                    </button>
                </div>

                <!-- grid -->
                <div v-if="filteredProducts.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <article v-for="p in filteredProducts" :key="p.id_produk"
                        class="bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col group hover:border-gray-400 hover:shadow-md transition-all duration-200">

                        <!-- image / icon area -->
                        <div @click="openDetail(p)"
                            class="aspect-[4/3] bg-gray-50 flex items-center justify-center border-b border-gray-100 cursor-pointer relative overflow-hidden">
                            <!-- subtle pattern -->
                            <div class="absolute inset-0"
                                style="background-image:radial-gradient(circle,#e5e7eb 1px,transparent 1px);background-size:20px 20px;">
                            </div>
                            <!-- icon -->
                            <div class="relative w-16 h-16 text-gray-300 group-hover:text-gray-400 transition-colors">
                                <!-- laptop -->
                                <svg v-if="icon(p) === 'laptop'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <!-- monitor -->
                                <svg v-else-if="icon(p) === 'monitor'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <!-- keyboard -->
                                <svg v-else-if="icon(p) === 'keyboard'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <rect x="2" y="6" width="20" height="12" rx="2"/>
                                    <path stroke-linecap="round" d="M6 10h.01M10 10h.01M14 10h.01M18 10h.01M6 14h12M6 10h.01"/>
                                </svg>
                                <!-- mouse -->
                                <svg v-else-if="icon(p) === 'mouse'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C8.686 2 6 4.686 6 8v8c0 3.314 2.686 6 6 6s6-2.686 6-6V8c0-3.314-2.686-6-6-6zM12 2v7"/>
                                </svg>
                                <!-- cable -->
                                <svg v-else-if="icon(p) === 'cable'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <!-- default device -->
                                <svg v-else fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <rect x="5" y="2" width="14" height="20" rx="2"/>
                                    <path stroke-linecap="round" d="M12 18h.01"/>
                                </svg>
                            </div>

                            <!-- stock badge -->
                            <span :class="p.stok > 20 ? 'text-emerald-600 bg-emerald-50' : 'text-amber-600 bg-amber-50'"
                                class="absolute bottom-2.5 right-2.5 text-[10px] font-semibold px-1.5 py-0.5 rounded">
                                {{ p.stok > 20 ? 'Stok tersedia' : `Sisa ${p.stok}` }}
                            </span>
                        </div>

                        <!-- body -->
                        <div class="p-4 flex flex-col gap-2 flex-1">
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">{{ p.kategori }}</p>
                            <h3 @click="openDetail(p)"
                                class="font-semibold text-sm text-gray-900 leading-snug line-clamp-2 cursor-pointer hover:text-orange-600 transition-colors"
                                :title="p.nama_produk">
                                {{ p.nama_produk }}
                            </h3>
                            <p class="font-bold text-base text-gray-900 mt-auto pt-1">{{ rp(p.harga) }}</p>
                            <div class="flex gap-2 pt-2 border-t border-gray-100">
                                <button @click="openDetail(p)"
                                    class="flex-1 py-1.5 text-xs font-medium border border-gray-200 rounded-lg hover:border-gray-400 transition-colors cursor-pointer text-gray-600">
                                    Detail
                                </button>
                                <button @click="addToCart(p)"
                                    class="flex-[2] py-1.5 text-xs font-semibold bg-gray-900 hover:bg-orange-500 text-white rounded-lg transition-colors cursor-pointer">
                                    + Keranjang
                                </button>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- empty state -->
                <div v-else class="py-24 text-center">
                    <p class="text-3xl mb-3">🔍</p>
                    <h3 class="font-semibold text-gray-800">Tidak ada hasil</h3>
                    <p class="text-sm text-gray-500 mt-1 mb-5">Coba kata kunci lain atau ubah kategori.</p>
                    <button @click="searchQuery = ''; selectedCategory = 'Semua'"
                        class="px-5 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg cursor-pointer hover:bg-gray-700 transition-colors">
                        Reset Filter
                    </button>
                </div>
            </section>

            <!-- ─── Keunggulan ─────────────────────────────── -->
            <section id="keunggulan" class="border-t border-gray-100 bg-gray-50">
                <div class="max-w-screen-xl mx-auto px-5 lg:px-10 py-14 lg:py-16">
                    <h2 class="text-xl font-bold text-gray-900 mb-8">Mengapa TechStore?</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-px bg-gray-200 border border-gray-200 rounded-xl overflow-hidden">
                        <div v-for="item in [
                            { icon:'🚚', title:'Bebas ongkir', desc:'Gratis kirim ke seluruh kota, syarat berlaku.' },
                            { icon:'🛡️', title:'Garansi resmi', desc:'Semua produk bergaransi langsung dari distributor.' },
                            { icon:'🔒', title:'Transaksi aman', desc:'Pembayaran terenkripsi, konfirmasi instan.' },
                            { icon:'💬', title:'Bantuan 24/7', desc:'Tim support siap membantu kapan pun.' },
                        ]" :key="item.title"
                            class="bg-gray-50 p-6 hover:bg-white transition-colors">
                            <p class="text-2xl mb-3">{{ item.icon }}</p>
                            <h3 class="font-semibold text-gray-900 text-sm mb-1">{{ item.title }}</h3>
                            <p class="text-sm text-gray-500 leading-relaxed">{{ item.desc }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- ─── Footer ────────────────────────────────────── -->
        <footer id="kontak" class="border-t border-gray-100 bg-white">
            <div class="max-w-screen-xl mx-auto px-5 lg:px-10 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2 space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-md bg-orange-500 flex items-center justify-center text-white text-xs">T</span>
                        <span class="font-bold text-gray-900">TechStore</span>
                    </div>
                    <p class="text-sm text-gray-500 max-w-xs leading-relaxed">
                        Platform belanja peralatan komputer, laptop, dan aksesoris dengan garansi resmi.
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-900 uppercase tracking-wide mb-3">Kategori</p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li v-for="cat in categories" :key="cat">
                            <a href="#katalog" @click="selectedCategory = cat" class="hover:text-gray-900 transition-colors">{{ cat }}</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-900 uppercase tracking-wide mb-3">Kontak</p>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li>support@techstore.id</li>
                        <li>+62 812-3456-7890</li>
                        <li>08.00–21.00 WIB</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-100 py-4 px-5 lg:px-10 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-400 max-w-screen-xl mx-auto">
                <p>© 2026 TechStore. Hak cipta dilindungi.</p>
                <div class="flex gap-5">
                    <span class="hover:text-gray-700 cursor-pointer transition-colors">Syarat & Ketentuan</span>
                    <span class="hover:text-gray-700 cursor-pointer transition-colors">Privasi</span>
                </div>
            </div>
        </footer>

        <!-- ═══════════════════════════════════════════════ -->
        <!-- Product Detail Modal                           -->
        <!-- ═══════════════════════════════════════════════ -->
        <Transition name="fade">
            <div v-if="selectedProduct"
                class="fixed inset-0 z-50 bg-black/40 flex items-end sm:items-center justify-center p-0 sm:p-4"
                @click.self="closeDetail">
                <div class="bg-white w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl overflow-hidden shadow-2xl">
                    <!-- product banner -->
                    <div class="h-40 bg-gray-900 flex items-center justify-center relative">
                        <div class="absolute inset-0 opacity-[0.06]"
                            style="background-image:repeating-linear-gradient(0deg,#fff 0,#fff 1px,transparent 1px,transparent 28px),repeating-linear-gradient(90deg,#fff 0,#fff 1px,transparent 1px,transparent 28px)">
                        </div>
                        <svg class="w-16 h-16 text-white/20 relative" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <button @click="closeDetail"
                            class="absolute top-3 right-3 w-7 h-7 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white cursor-pointer transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-5">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ selectedProduct.kategori }}</p>
                            <h3 class="font-bold text-xl text-gray-900 mt-1">{{ selectedProduct.nama_produk }}</h3>
                            <p class="font-bold text-2xl text-orange-500 mt-1">{{ rp(selectedProduct.harga) }}</p>
                        </div>

                        <!-- specs row -->
                        <div class="flex gap-3">
                            <div class="flex-1 bg-gray-50 rounded-lg p-3 text-center">
                                <p class="text-[10px] text-gray-400 font-semibold uppercase">Kondisi</p>
                                <p class="text-sm font-semibold text-gray-800 mt-0.5">Baru</p>
                            </div>
                            <div class="flex-1 bg-gray-50 rounded-lg p-3 text-center">
                                <p class="text-[10px] text-gray-400 font-semibold uppercase">Stok</p>
                                <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ selectedProduct.stok }} unit</p>
                            </div>
                            <div class="flex-1 bg-gray-50 rounded-lg p-3 text-center">
                                <p class="text-[10px] text-gray-400 font-semibold uppercase">Garansi</p>
                                <p class="text-sm font-semibold text-gray-800 mt-0.5">1 Tahun</p>
                            </div>
                        </div>

                        <!-- qty + subtotal -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                                <button @click="detailQty > 1 ? detailQty-- : null"
                                    class="px-3.5 py-2 text-gray-600 hover:bg-gray-50 font-bold transition-colors cursor-pointer">−</button>
                                <span class="px-4 py-2 text-sm font-bold text-gray-900 border-x border-gray-200 select-none">{{ detailQty }}</span>
                                <button @click="detailQty < selectedProduct.stok ? detailQty++ : null"
                                    class="px-3.5 py-2 text-gray-600 hover:bg-gray-50 font-bold transition-colors cursor-pointer">+</button>
                            </div>
                            <p class="font-bold text-gray-900">{{ rp(+selectedProduct.harga * detailQty) }}</p>
                        </div>

                        <button @click="addToCart(selectedProduct, detailQty); closeDetail()"
                            class="w-full py-3 bg-gray-900 hover:bg-orange-500 text-white font-semibold text-sm rounded-xl transition-colors cursor-pointer">
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
            <div v-if="isCartOpen"
                class="fixed inset-0 z-50 flex justify-end"
                @click.self="isCartOpen = false">
                <!-- backdrop -->
                <div class="absolute inset-0 bg-black/30" @click="isCartOpen = false"></div>
                <div class="relative w-full max-w-sm bg-white h-full flex flex-col shadow-2xl">

                    <!-- header -->
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <div>
                            <h3 class="font-bold text-gray-900">Keranjang</h3>
                            <p class="text-xs text-gray-400">{{ cartCount }} item</p>
                        </div>
                        <button @click="isCartOpen = false"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors cursor-pointer">
                            <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- items -->
                    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3">
                        <div v-if="!cart.length" class="h-full flex flex-col items-center justify-center gap-3 text-center py-16">
                            <span class="text-4xl">🛒</span>
                            <p class="font-semibold text-gray-700">Keranjang kosong</p>
                            <p class="text-sm text-gray-400">Tambahkan produk dari katalog.</p>
                        </div>

                        <div v-for="item in cart" :key="item.id_produk"
                            class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <!-- mini icon -->
                            <div class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center shrink-0 text-gray-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 line-clamp-1">{{ item.nama_produk }}</p>
                                <p class="text-xs text-orange-600 font-bold">{{ rp(+item.harga * item.quantity) }}</p>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <button @click="updateQty(item.id_produk, -1)"
                                    class="w-6 h-6 text-xs font-bold bg-white border border-gray-200 rounded-md hover:bg-gray-100 cursor-pointer flex items-center justify-center">−</button>
                                <span class="w-6 text-center text-xs font-bold">{{ item.quantity }}</span>
                                <button @click="updateQty(item.id_produk, 1)"
                                    class="w-6 h-6 text-xs font-bold bg-white border border-gray-200 rounded-md hover:bg-gray-100 cursor-pointer flex items-center justify-center">+</button>
                                <button @click="remove(item.id_produk)"
                                    class="ml-1 text-gray-300 hover:text-red-500 transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- footer total -->
                    <div v-if="cart.length" class="px-5 py-4 border-t border-gray-100 space-y-3 bg-gray-50">
                        <div class="space-y-1.5 text-sm">
                            <div class="flex justify-between text-gray-500">
                                <span>Subtotal</span>
                                <span class="font-semibold text-gray-900">{{ rp(cartTotal) }}</span>
                            </div>
                            <div class="flex justify-between text-emerald-600 text-xs">
                                <span>Ongkir</span><span class="font-semibold">Gratis</span>
                            </div>
                            <div class="flex justify-between font-bold text-gray-900 pt-2 border-t border-gray-200">
                                <span>Total</span><span>{{ rp(cartTotal) }}</span>
                            </div>
                        </div>
                        <button @click="checkout"
                            class="w-full py-3 bg-gray-900 hover:bg-orange-500 text-white font-semibold text-sm rounded-xl transition-colors cursor-pointer flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
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
            <div v-if="showNota && notaData"
                class="fixed inset-0 z-[60] bg-black/60 flex items-center justify-center p-3">
                <div class="bg-white w-full max-w-[340px] rounded-2xl shadow-2xl overflow-hidden border border-gray-300">

                    <div id="nota-area" class="bg-white p-5 text-black space-y-3 leading-tight font-['Poppins']">
                        <!-- header -->
                        <div class="text-center space-y-1">
                            <h2 class="font-extrabold text-xl text-black tracking-tight uppercase">TOKO TEKNOLOGI</h2>
                            <p class="text-xs font-semibold text-gray-800">Jl. Boulevard Teknologi No.88</p>
                            <p class="text-xs font-medium text-gray-700">Telp: 0812-3456-7890</p>
                        </div>

                        <div class="border-t-2 border-dashed border-gray-400 my-1.5"></div>

                        <!-- meta -->
                        <div class="space-y-1 text-xs sm:text-[13px] font-medium text-gray-900">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">No. Faktur:</span>
                                <span class="font-mono font-bold text-black">{{ notaData.no }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Waktu:</span>
                                <span class="font-bold text-black whitespace-nowrap">{{ notaData.date }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Kasir:</span>
                                <span class="font-bold text-black">Pembayaran Online</span>
                            </div>
                        </div>

                        <div class="border-t-2 border-dashed border-gray-400 my-1.5"></div>

                        <!-- items -->
                        <div class="space-y-2 text-xs sm:text-[13px]">
                            <div v-for="item in notaData.items" :key="item.id_produk"
                                class="space-y-0.5">
                                <p class="font-bold text-black text-[13.5px] leading-snug">{{ item.nama_produk }}</p>
                                <div class="flex justify-between items-center text-xs font-bold text-black">
                                    <span class="font-['IBM_Plex_Mono']">{{ item.quantity }} x {{ rp(item.harga) }}</span>
                                    <span class="font-['IBM_Plex_Mono'] font-extrabold">{{ rp(+item.harga * item.quantity) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="border-t-2 border-dashed border-gray-400 my-1.5"></div>

                        <!-- total -->
                        <div class="space-y-1 text-xs sm:text-[13px] font-medium text-gray-900">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Ongkir:</span>
                                <span class="font-bold text-black font-['IBM_Plex_Mono']">Rp 0 (GRATIS)</span>
                            </div>
                            <div class="flex justify-between items-center pt-2 border-t-2 border-dashed border-gray-400 my-1">
                                <span class="font-black text-sm text-black uppercase">TOTAL:</span>
                                <span class="text-xl font-black text-black font-['IBM_Plex_Mono'] whitespace-nowrap">{{ rp(notaData.total) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs pt-0.5">
                                <span class="text-gray-600">Status:</span>
                                <span class="font-black text-black uppercase tracking-wider">LUNAS</span>
                            </div>
                        </div>

                        <div class="border-t-2 border-dashed border-gray-400 my-1.5"></div>

                        <div class="text-center space-y-1 text-xs text-gray-800 pt-1">
                            <p class="font-bold text-black text-xs">Terima Kasih Telah Berbelanja!</p>
                            <p class="text-[11px] text-gray-700 font-medium">Garansi resmi 1 tahun toko.</p>
                            <div class="pt-1 font-mono text-[10px] text-gray-600 font-bold tracking-widest uppercase">
                                === STRUK PEMBELIAN ONLINE ===
                            </div>
                        </div>
                    </div>

                    <!-- actions (hidden on print) -->
                    <div class="no-print flex gap-2 p-3.5 bg-gray-100 border-t border-gray-300">
                        <button @click="printNota"
                            class="flex-1 py-2.5 bg-black hover:bg-orange-600 text-white text-xs font-bold rounded-xl cursor-pointer transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Cetak
                        </button>
                        <button @click="closeNota"
                            class="flex-1 py-2.5 bg-white hover:bg-gray-200 text-gray-800 text-xs font-bold rounded-xl border border-gray-300 cursor-pointer transition-colors">
                            Tutup
                        </button>
                    </div>

                </div>
            </div>
        </Transition>

    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

/* toast */
.toast-enter-active, .toast-leave-active { transition: all .25s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(8px); }

/* fade backdrop */
.fade-enter-active, .fade-leave-active { transition: opacity .2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* cart slide */
.slide-right-enter-active, .slide-right-leave-active { transition: transform .25s ease; }
.slide-right-enter-from, .slide-right-leave-to { transform: translateX(100%); }

@media print {
    @page {
        size: auto;
        margin: 5mm;
    }
    
    html, body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    body * {
        visibility: hidden !important;
    }

    #nota-area,
    #nota-area * {
        visibility: visible !important;
        color: #000 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    #nota-area {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 340px !important;
        max-width: 340px !important;
        margin: 10px auto !important;
        padding: 12px 16px !important;
        background: #fff !important;
        border: none !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        font-family: 'Poppins', sans-serif !important;
    }

    .no-print {
        display: none !important;
    }
}
</style>