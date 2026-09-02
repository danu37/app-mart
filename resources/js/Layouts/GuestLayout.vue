<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div
        class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gray-50 font-sans"
    >
        <!-- Background subtle tech graphics -->
        <div class="absolute inset-0">
            <!-- subtle dot pattern -->
            <div
                class="absolute inset-0 opacity-[0.03]"
                style="
                    background-image: radial-gradient(
                        #14213d 1px,
                        transparent 1px
                    );
                    background-size: 24px 24px;
                "
            ></div>

            <!-- Abstract Glow Elements -->
            <div
                class="absolute -top-[15%] -left-[10%] w-[50%] h-[60%] bg-brand-orange-500/10 blur-[120px] rounded-full mix-blend-multiply pointer-events-none"
            ></div>
            <div
                class="absolute bottom-[0%] right-[0%] w-[60%] h-[50%] bg-navy-900/10 blur-[130px] rounded-full mix-blend-multiply pointer-events-none"
            ></div>
        </div>

        <!-- decorative laptop/geometry illustration (super minimal, vector) -->
        <div
            class="absolute hidden lg:block bottom-10 left-16 opacity-30 text-navy-200 pointer-events-none"
        >
            <svg
                width="240"
                height="240"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="0.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
        </div>

        <div
            class="absolute hidden lg:block top-16 right-16 opacity-20 text-brand-orange-300 pointer-events-none"
        >
            <svg
                width="200"
                height="200"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="0.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                <line x1="8" y1="21" x2="16" y2="21"></line>
                <line x1="12" y1="17" x2="12" y2="21"></line>
            </svg>
        </div>

        <div class="relative z-10 w-full max-w-md px-4 sm:px-6 py-10">
            <!-- Logo area -->
            <div class="flex justify-center mb-8">
                <Link href="/" class="flex items-center gap-2 group">
                    <div
                        class="w-10 h-10 rounded-xl bg-brand-orange-500 flex items-center justify-center shadow-lg shadow-brand-orange-500/30 group-hover:scale-105 transition-transform duration-300"
                    >
                        <span class="text-white font-bold text-xl leading-none"
                            >T</span
                        >
                    </div>
                    <span
                        class="text-2xl font-extrabold text-navy-950 tracking-tight"
                        >TechStore</span
                    >
                </Link>
            </div>

            <!-- Form Card -->
            <div
                class="bg-white/80 backdrop-blur-xl px-6 py-8 sm:p-10 shadow-2xl shadow-navy-900/5 rounded-3xl border border-gray-100"
            >
                <slot />
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-xs text-gray-500">
                &copy; {{ new Date().getFullYear() }} TechStore. All rights
                reserved.
            </div>
        </div>
    </div>
</template>
