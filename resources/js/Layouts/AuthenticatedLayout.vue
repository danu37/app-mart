<script setup>
import { ref, computed } from "vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);
const page = usePage();

const isAdmin = computed(() => page.props.auth.user.role === "admin");

const adminLinks = [
    {
        label: "Dashboard",
        route: "admin.dashboard",
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>`,
        match: "admin.dashboard",
    },
    {
        label: "Produk",
        route: "admin.products.index",
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>`,
        match: "admin.products.*",
    },
    {
        label: "Transaksi",
        route: "admin.orders.index",
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>`,
        match: "admin.orders.*",
    },
    {
        label: "Pelanggan",
        route: "admin.pelanggan.index",
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>`,
        match: "admin.pelanggan.*",
    },
    {
        label: "Laporan",
        route: "laporan.index",
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>`,
        match: "laporan.*",
    },
    {
        label: "Backup DB",
        route: "admin.backup.index",
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>`,
        match: "admin.backup.*",
    },
];

const customerLinks = [
    {
        label: "Dashboard",
        route: "customer.dashboard",
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>`,
        match: "customer.dashboard",
    },
    {
        label: "Pesanan Saya",
        route: "customer.orders",
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>`,
        match: "customer.orders*",
    },
    {
        label: "Akun Saya",
        route: "profile.edit",
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>`,
        match: "profile.edit",
    },
];

const navLinks = computed(() => (isAdmin.value ? adminLinks : customerLinks));
</script>

<template>
    <Head>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div class="min-h-screen flex bg-gray-50 font-sans">
        <!-- ==================== DESKTOP SIDEBAR ==================== -->
        <aside
            class="hidden lg:flex flex-col w-[260px] shrink-0 sticky top-0 h-screen bg-navy-950 text-white shadow-xl"
        >
            <!-- Brand -->
            <div
                class="flex items-center gap-3 px-6 h-20 shrink-0 border-b border-navy-800/50"
            >
                <Link
                    :href="route('home')"
                    class="flex items-center gap-2.5 group"
                >
                    <div
                        class="w-9 h-9 rounded-xl bg-brand-orange-500 flex items-center justify-center shadow-lg shadow-brand-orange-500/30 group-hover:scale-105 transition-transform duration-300"
                    >
                        <span class="text-white font-bold text-lg leading-none"
                            >T</span
                        >
                    </div>
                    <span
                        class="font-poppins font-bold text-xl tracking-tight text-white"
                        >TechStore</span
                    >
                </Link>
            </div>

            <!-- Nav Label -->
            <div class="px-6 pt-8 pb-3">
                <span
                    class="text-[11px] font-bold tracking-wider text-navy-400 uppercase"
                >
                    {{ isAdmin ? "Admin Panel" : "Member Area" }}
                </span>
            </div>

            <!-- Nav Links -->
            <nav class="flex-1 overflow-y-auto px-4 space-y-1.5 pb-6">
                <template v-for="link in navLinks" :key="link.route">
                    <Link
                        :href="route(link.route)"
                        class="group flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all duration-200"
                        :class="
                            route().current(link.match)
                                ? 'bg-brand-orange-500 text-white shadow-md shadow-brand-orange-500/20 font-semibold'
                                : 'text-navy-200 hover:bg-navy-800 hover:text-white font-medium'
                        "
                    >
                        <svg
                            class="w-5 h-5 shrink-0 transition-colors"
                            :class="
                                route().current(link.match)
                                    ? 'text-white'
                                    : 'text-navy-400 group-hover:text-white'
                            "
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            v-html="link.icon"
                        ></svg>
                        {{ link.label }}
                    </Link>
                </template>
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 shrink-0 border-t border-navy-800/50">
                <Link
                    :href="route('home')"
                    class="flex justify-center items-center gap-2 w-full px-4 py-3 rounded-xl text-sm font-medium transition-all bg-navy-900 border border-navy-700 text-navy-200 hover:text-white hover:bg-navy-800"
                >
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 12h18M3 12l4-4M3 12l4 4"
                        />
                    </svg>
                    Lihat Toko Utama
                </Link>
            </div>
        </aside>

        <!-- ==================== MAIN CONTENT ==================== -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Bar -->
            <header
                class="sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 lg:px-8 h-20 shrink-0 bg-white/80 backdrop-blur-xl border-b border-gray-200 shadow-sm"
            >
                <!-- Mobile Hamburger -->
                <button
                    @click="
                        showingNavigationDropdown = !showingNavigationDropdown
                    "
                    class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors"
                >
                    <svg
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            v-if="!showingNavigationDropdown"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            v-else
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>

                <!-- Breadcrumb / Search (Placeholder) -->
                <div class="flex-1 hidden lg:flex items-center gap-4">
                    <!-- you can add a search bar here later if needed -->
                </div>

                <!-- User Menu -->
                <div class="flex items-center gap-4">
                    <!-- Status dot -->
                    <div
                        class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-green-50 rounded-full border border-green-100"
                    >
                        <span
                            class="w-2 h-2 rounded-full bg-green-500 animate-pulse"
                        ></span>
                        <span
                            class="text-xs font-semibold text-green-700 uppercase tracking-widest"
                            >Active</span
                        >
                    </div>

                    <Dropdown align="right" width="56">
                        <template #trigger>
                            <button
                                class="group flex items-center gap-3 px-2 py-1.5 rounded-xl border border-transparent hover:bg-gray-50 hover:border-gray-200 transition-all focus:outline-none"
                            >
                                <div
                                    class="w-9 h-9 rounded-full bg-navy-900 flex items-center justify-center text-sm font-bold text-white shadow-sm"
                                >
                                    {{
                                        $page.props.auth.user.name
                                            .charAt(0)
                                            .toUpperCase()
                                    }}
                                </div>
                                <div class="hidden sm:flex flex-col text-left">
                                    <span
                                        class="text-sm font-semibold text-gray-900 leading-none mb-1"
                                        >{{ $page.props.auth.user.name }}</span
                                    >
                                    <span
                                        class="text-xs font-medium text-gray-500 leading-none"
                                        >{{
                                            isAdmin
                                                ? "Administrator"
                                                : "Customer"
                                        }}</span
                                    >
                                </div>
                                <svg
                                    class="w-4 h-4 text-gray-400 group-hover:text-gray-600 ml-1 hidden sm:block"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    />
                                </svg>
                            </button>
                        </template>
                        <template #content>
                            <div class="p-1">
                                <div
                                    class="px-3 py-2 mb-1 border-b border-gray-100 sm:hidden"
                                >
                                    <div
                                        class="text-sm font-semibold text-gray-900"
                                    >
                                        {{ $page.props.auth.user.name }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate">
                                        {{ $page.props.auth.user.email }}
                                    </div>
                                </div>
                                <DropdownLink
                                    :href="route('profile.edit')"
                                    class="!p-2.5 !rounded-lg hover:!bg-gray-50 text-sm font-medium text-gray-700"
                                    >Akun Profil</DropdownLink
                                >
                                <DropdownLink
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="!p-2.5 !rounded-lg hover:!bg-red-50 !text-red-600 text-sm font-medium block w-full text-left"
                                    >Log Out</DropdownLink
                                >
                            </div>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Mobile Nav Dropdown -->
            <div
                v-show="showingNavigationDropdown"
                class="lg:hidden absolute top-20 left-0 w-full bg-navy-950 text-white z-40 border-b border-navy-800 shadow-2xl"
            >
                <div class="p-4 space-y-1">
                    <template
                        v-for="link in navLinks"
                        :key="link.route + '_mob'"
                    >
                        <Link
                            :href="route(link.route)"
                            @click="showingNavigationDropdown = false"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all"
                            :class="
                                route().current(link.match)
                                    ? 'bg-brand-orange-500 text-white'
                                    : 'text-navy-200 hover:bg-navy-800'
                            "
                        >
                            <svg
                                class="w-5 h-5 shrink-0"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                v-html="link.icon"
                            ></svg>
                            {{ link.label }}
                        </Link>
                    </template>
                    <div class="pt-4 mt-4 border-t border-navy-800">
                        <Link
                            :href="route('home')"
                            class="flex justify-center items-center gap-2 w-full px-4 py-3 rounded-xl text-sm font-medium bg-navy-900 border border-navy-700 text-navy-200"
                        >
                            Menuju Toko
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Page Header Slot -->
            <div v-if="$slots.header" class="px-4 sm:px-6 lg:px-8 pt-8 pb-4">
                <slot name="header" />
            </div>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden px-4 sm:px-6 lg:px-8 pb-10">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Custom scrollbar sidebar */
aside nav::-webkit-scrollbar {
    width: 4px;
}
aside nav::-webkit-scrollbar-track {
    background: transparent;
}
aside nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 99px;
}
</style>
