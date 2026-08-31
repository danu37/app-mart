<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Head, Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <Head>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet" />
    </Head>

    <div class="min-h-screen bg-[#F3F3EE] text-[#1C1E1B] font-['Inter'] flex flex-col">
        <!-- Top Navbar -->
        <nav class="sticky top-0 z-40 bg-[#F3F3EE]/95 backdrop-blur border-b border-[#E1E2D9]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex h-[76px] justify-between">
                    <div class="flex items-center">
                        <!-- Brand Logo -->
                        <div class="flex shrink-0 items-center">
                            <Link :href="route('home')" class="flex items-center gap-2.5 group">
                                <span class="w-8 h-8 rounded-md bg-[#1C1E1B] flex flex-col items-center justify-center gap-[3px] shrink-0">
                                    <span class="w-4 h-[2px] bg-[#FF5A1F]"></span>
                                    <span class="w-4 h-[2px] bg-white/70"></span>
                                </span>
                                <span class="font-['Sora'] font-bold text-lg tracking-tight text-[#1C1E1B]">TechStore</span>
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex h-full">
                            <NavLink
                                :href="route('dashboard')"
                                :active="route().current('dashboard')"
                            >
                                Dashboard
                            </NavLink>
                            <NavLink
                                :href="route('pelanggan.index')"
                                :active="route().current('pelanggan.*')"
                            >
                                Data Pelanggan
                            </NavLink>
                            <NavLink
                                :href="route('backup.index')"
                                :active="route().current('backup.*')"
                            >
                                Backup & Restore
                            </NavLink>
                        </div>
                    </div>

                    <div class="hidden sm:ms-6 sm:flex sm:items-center gap-3">
                        <Link
                            :href="route('home')"
                            class="px-3.5 py-1.5 text-xs font-semibold font-['Sora'] text-[#5B5F58] hover:text-[#1C1E1B] hover:bg-white rounded-md border border-transparent hover:border-[#E1E2D9] transition-colors"
                        >
                            &larr; Lihat Toko
                        </Link>

                        <!-- Settings Dropdown -->
                        <div class="relative ms-2">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-md border border-[#E1E2D9] bg-white px-3.5 py-2 text-sm font-semibold font-['Sora'] leading-4 text-[#1C1E1B] transition hover:bg-[#F3F3EE] focus:outline-none"
                                        >
                                            <span class="w-2 h-2 rounded-full bg-[#0C8B7C] mr-2"></span>
                                            {{ $page.props.auth.user.name }}

                                            <svg
                                                class="-me-0.5 ms-2 h-4 w-4 text-[#5B5F58]"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <DropdownLink
                                        :href="route('profile.edit')"
                                    >
                                        Profile
                                    </DropdownLink>
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                    >
                                        Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center rounded-md p-2 text-[#5B5F58] hover:bg-white hover:text-[#1C1E1B] focus:outline-none"
                        >
                            <svg
                                class="h-6 w-6"
                                stroke="currentColor"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex': !showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex': showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div
                :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown,
                }"
                class="sm:hidden border-t border-[#E1E2D9] bg-white"
            >
                <div class="space-y-1 pb-3 pt-2">
                    <ResponsiveNavLink
                        :href="route('dashboard')"
                        :active="route().current('dashboard')"
                    >
                        Dashboard
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('pelanggan.index')"
                        :active="route().current('pelanggan.*')"
                    >
                        Data Pelanggan
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('backup.index')"
                        :active="route().current('backup.*')"
                    >
                        Backup & Restore
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('home')">
                        Kunjungi Toko
                    </ResponsiveNavLink>
                </div>

                <!-- Responsive Settings Options -->
                <div class="border-t border-[#E1E2D9] pb-3 pt-4 px-4 bg-[#F3F3EE]/50">
                    <div class="text-base font-semibold font-['Sora'] text-[#1C1E1B]">
                        {{ $page.props.auth.user.name }}
                    </div>
                    <div class="text-xs text-[#5B5F58]">
                        {{ $page.props.auth.user.email }}
                    </div>

                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')">
                            Profile
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('logout')"
                            method="post"
                            as="button"
                        >
                            Log Out
                        </ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading Header -->
        <header
            class="bg-white border-b border-[#E1E2D9]"
            v-if="$slots.header"
        >
            <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1">
            <slot />
        </main>
    </div>
</template>
