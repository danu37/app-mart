<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    products: Array,
    categories: Array,
    filters: Object,
});

const formatRupiah = (val) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(val || 0);
};

const deleteForm = useForm({});
const deleteProduct = (id, name) => {
    if (confirm(`Apakah Anda yakin ingin menghapus produk "${name}"?`)) {
        deleteForm.delete(route("admin.products.destroy", id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Kelola Produk" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2
                        class="font-['Sora'] font-bold text-2xl text-[#1C1E1B] leading-tight"
                    >
                        Kelola Produk
                    </h2>
                    <p class="text-sm text-[#5B5F58] mt-1">
                        Kelola katalog produk TechStore.
                    </p>
                </div>
                <Link
                    :href="route('admin.products.create')"
                    class="px-4 py-2 bg-[#1C1E1B] hover:bg-[#33352F] text-white font-['Sora'] font-semibold text-xs rounded-md transition-colors shadow-2xs"
                >
                    + Tambah Produk
                </Link>
            </div>
        </template>

        <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="bg-white rounded-lg border border-[#E1E2D9] shadow-2xs overflow-hidden"
            >
                <div class="overflow-x-auto">
                    <table
                        class="w-full text-left border-collapse text-xs text-[#1C1E1B]"
                    >
                        <thead
                            class="bg-[#F3F3EE] border-b border-[#E1E2D9] text-[11px] font-semibold text-[#5B5F58] uppercase"
                        >
                            <tr>
                                <th class="py-3.5 px-6">ID</th>
                                <th class="py-3.5 px-6">Produk</th>
                                <th class="py-3.5 px-6">Kategori</th>
                                <th class="py-3.5 px-6 text-right">Harga</th>
                                <th class="py-3.5 px-6 text-right">Stok</th>
                                <th class="py-3.5 px-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E1E2D9]">
                            <tr
                                v-for="product in products"
                                :key="product.id_produk"
                                class="hover:bg-[#F3F3EE]/60 transition-colors"
                            >
                                <td
                                    class="py-4 px-6 font-bold font-['IBM_Plex_Mono']"
                                >
                                    #{{ product.id_produk }}
                                </td>
                                <td class="py-4 px-6 flex items-center gap-3">
                                    <img
                                        v-if="product.gambar"
                                        :src="product.gambar"
                                        class="w-10 h-10 rounded object-cover border border-[#E1E2D9]"
                                    />
                                    <div
                                        v-else
                                        class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center border border-[#E1E2D9]"
                                    >
                                        <span class="text-[10px] text-gray-400"
                                            >No Img</span
                                        >
                                    </div>
                                    <span class="font-semibold">{{
                                        product.nama_produk
                                    }}</span>
                                </td>
                                <td class="py-4 px-6 text-[#5B5F58]">
                                    {{ product.kategori }}
                                </td>
                                <td
                                    class="py-4 px-6 font-bold font-['IBM_Plex_Mono'] text-right"
                                >
                                    {{ formatRupiah(product.harga) }}
                                </td>
                                <td
                                    class="py-4 px-6 font-bold font-['IBM_Plex_Mono'] text-right"
                                >
                                    {{ product.stok }}
                                </td>
                                <td class="py-4 px-6 text-center space-x-2">
                                    <Link
                                        :href="
                                            route(
                                                'admin.products.edit',
                                                product.id_produk,
                                            )
                                        "
                                        class="inline-block px-2.5 py-1 text-white bg-[#1C1E1B] hover:bg-[#33352F] rounded transition-colors"
                                        >Edit</Link
                                    >
                                    <button
                                        @click="
                                            deleteProduct(
                                                product.id_produk,
                                                product.nama_produk,
                                            )
                                        "
                                        class="inline-block px-2.5 py-1 text-white bg-[#1C1E1B] hover:bg-[#33352F] rounded transition-colors"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!products.length">
                                <td
                                    colspan="6"
                                    class="py-6 text-center text-[#5B5F58]"
                                >
                                    Belum ada produk.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
