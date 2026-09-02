<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    user: Object,
    canLogin: Boolean,
    canRegister: Boolean,
    cartCount: { type: Number, default: 0 },
    searchQuery: { type: String, default: "" },
});

defineEmits(["update:searchQuery", "openCart"]);
</script>

<template>
    <header class="sticky top-0 z-40 bg-navy-900 shadow-md">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between"
        >
            <!-- brand -->
            <Link
                :href="route('home')"
                class="flex items-center gap-2.5 select-none group"
            >
                <span
                    class="w-8 h-8 rounded-lg bg-brand-orange-500 flex items-center justify-center text-white shadow-sm"
                >
                    <svg
                        class="w-4.5 h-4.5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                        />
                    </svg>
                </span>
                <span
                    class="font-bold text-white tracking-tight text-lg group-hover:text-brand-orange-300 transition-colors"
                    >TechStore</span
                >
            </Link>

            <!-- desktop nav -->
            <nav
                class="hidden md:flex items-center gap-8 text-sm font-medium text-navy-200"
            >
                <Link
                    :href="route('home')"
                    class="hover:text-white transition-colors py-1"
                    :class="{
                        'text-white underline decoration-brand-orange-500 underline-offset-4':
                            $page.url === '/',
                    }"
                    >Beranda</Link
                >
                <Link
                    :href="route('home') + '#katalog'"
                    class="hover:text-white transition-colors py-1"
                    >Katalog</Link
                >
                <Link
                    :href="route('advantages')"
                    class="hover:text-white transition-colors py-1"
                    :class="{
                        'text-white underline decoration-brand-orange-500 underline-offset-4':
                            $page.url.startsWith('/keunggulan'),
                    }"
                    >Keunggulan</Link
                >
                <Link
                    :href="route('about')"
                    class="hover:text-white transition-colors py-1"
                    :class="{
                        'text-white underline decoration-brand-orange-500 underline-offset-4':
                            $page.url.startsWith('/about'),
                    }"
                    >FAQ & About</Link
                >
                <Link
                    :href="route('contact')"
                    class="hover:text-white transition-colors py-1"
                    :class="{
                        'text-white underline decoration-brand-orange-500 underline-offset-4':
                            $page.url.startsWith('/contact'),
                    }"
                    >Kontak</Link
                >
            </nav>

            <!-- actions -->
            <div class="flex items-center gap-3">
                <!-- search (desktop) -->
                <div
                    class="hidden lg:flex items-center gap-2 bg-navy-800 border border-navy-700 rounded-lg px-3 py-2 w-52 focus-within:border-brand-orange-500 transition-colors"
                >
                    <svg
                        class="w-4 h-4 text-navy-400 shrink-0"
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
                        :value="searchQuery"
                        @input="
                            $emit('update:searchQuery', $event.target.value)
                        "
                        type="text"
                        placeholder="Cari produk…"
                        class="bg-transparent text-sm text-white placeholder-navy-400 outline-none w-full"
                    />
                </div>

                <!-- cart -->
                <button
                    @click="$emit('openCart')"
                    class="relative flex items-center gap-2 bg-navy-800 border border-navy-700 rounded-lg px-3 py-2 text-sm font-medium text-navy-100 hover:border-brand-orange-500 hover:text-white transition-colors cursor-pointer"
                >
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                        />
                    </svg>
                    <span class="hidden sm:inline">Keranjang</span>
                    <span
                        v-if="cartCount > 0"
                        class="absolute -top-1.5 -right-1.5 min-w-[20px] h-5 bg-brand-orange-500 text-white text-[11px] font-bold rounded-full flex items-center justify-center px-1 shadow-sm"
                    >
                        {{ cartCount }}
                    </span>
                </button>

                <!-- auth -->
                <Link
                    v-if="user"
                    :href="route('dashboard')"
                    class="hidden sm:flex items-center px-4 py-2 text-sm font-semibold bg-brand-orange-500 text-white rounded-lg hover:bg-brand-orange-600 transition-colors shadow-sm"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        v-if="canLogin"
                        :href="route('login')"
                        class="hidden sm:flex px-4 py-2 text-sm font-medium text-navy-200 hover:text-white transition-colors"
                    >
                        Masuk
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="hidden sm:flex px-4 py-2 text-sm font-semibold bg-brand-orange-500 hover:bg-brand-orange-600 text-white rounded-lg transition-colors shadow-sm"
                    >
                        Daftar
                    </Link>
                </template>
            </div>
        </div>
    </header>
</template>
