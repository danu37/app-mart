<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    categories: { type: Array, default: () => [] },
});

defineEmits(["selectCategory"]);
</script>

<template>
    <footer id="kontak" class="bg-navy-950">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 md:grid-cols-4 gap-10"
        >
            <div class="md:col-span-2 space-y-4">
                <div class="flex items-center gap-2.5">
                    <span
                        class="w-7 h-7 rounded-md bg-brand-orange-500 flex items-center justify-center text-white text-xs font-bold shadow-sm"
                        >T</span
                    >
                    <span class="font-bold text-white text-lg">TechStore</span>
                </div>
                <p class="text-sm text-navy-300 max-w-xs leading-relaxed">
                    Platform belanja peralatan komputer, laptop, dan aksesoris
                    dengan garansi resmi.
                </p>
            </div>
            <div>
                <p
                    class="text-xs font-semibold text-navy-200 uppercase tracking-wide mb-4"
                >
                    Kategori
                </p>
                <ul class="space-y-2.5 text-sm text-navy-400">
                    <li v-for="cat in categories" :key="cat">
                        <Link
                            :href="route('home') + '#katalog'"
                            @click="$emit('selectCategory', cat)"
                            class="hover:text-brand-orange-400 transition-colors"
                        >
                            {{ cat }}
                        </Link>
                    </li>
                </ul>
            </div>
            <div>
                <p
                    class="text-xs font-semibold text-navy-200 uppercase tracking-wide mb-4"
                >
                    Kontak
                </p>
                <ul class="space-y-2.5 text-sm text-navy-400">
                    <li>support@techstore.id</li>
                    <li>+62 812-3456-7890</li>
                    <li>08.00–21.00 WIB</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-navy-800">
            <div
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-navy-500"
            >
                <p>© 2026 TechStore. Hak cipta dilindungi.</p>
                <div class="flex gap-6">
                    <Link
                        :href="route('about')"
                        class="hover:text-navy-200 transition-colors"
                        >Tentang Kami</Link
                    >
                    <span
                        class="hover:text-navy-200 cursor-pointer transition-colors"
                        >Syarat & Ketentuan</span
                    >
                    <span
                        class="hover:text-navy-200 cursor-pointer transition-colors"
                        >Privasi</span
                    >
                </div>
            </div>
        </div>
    </footer>
</template>
