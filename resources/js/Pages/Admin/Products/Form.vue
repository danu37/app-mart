<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    product: Object,
    categories: Array,
});

const isEditing = !!props.product;

const form = useForm({
    nama_produk: props.product?.nama_produk ?? "",
    kategori: props.product?.kategori ?? "",
    harga: props.product?.harga ?? 0,
    stok: props.product?.stok ?? 0,
    gambar: null,
    _method: isEditing ? "put" : "post",
});

const submitForm = () => {
    // If editing, use the standard post method because it contains `_method: "put"`
    // which allows multipart form data (files) to be handled properly by Laravel
    form.post(
        isEditing
            ? route("admin.products.update", props.product.id_produk)
            : route("admin.products.store"),
    );
};
</script>

<template>
    <Head :title="isEditing ? 'Edit Produk' : 'Tambah Produk'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Link
                        :href="route('admin.products.index')"
                        class="text-sm text-brand-orange-600 hover:underline font-semibold flex items-center gap-1 mb-2"
                    >
                        &larr; Kembali ke Daftar Produk
                    </Link>
                    <h2
                        class="font-['Sora'] font-bold text-2xl text-[#1C1E1B] leading-tight"
                    >
                        {{ isEditing ? "Edit Produk" : "Tambah Produk" }}
                    </h2>
                </div>
            </div>
        </template>

        <div class="py-10 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="bg-white rounded-lg border border-[#E1E2D9] shadow-2xs p-6 sm:p-8"
            >
                <form @submit.prevent="submitForm" class="space-y-6">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Nama Produk</label
                        >
                        <input
                            v-model="form.nama_produk"
                            type="text"
                            class="w-full border-gray-300 focus:border-brand-orange-500 focus:ring-brand-orange-500 rounded-md shadow-sm"
                            required
                        />
                        <div
                            v-if="form.errors.nama_produk"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ form.errors.nama_produk }}
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Kategori</label
                        >
                        <!-- Allows typing new category or using existing ones -->
                        <input
                            v-model="form.kategori"
                            list="kategori-list"
                            type="text"
                            class="w-full border-gray-300 focus:border-brand-orange-500 focus:ring-brand-orange-500 rounded-md shadow-sm"
                            required
                        />
                        <datalist id="kategori-list">
                            <option
                                v-for="cat in categories"
                                :key="cat"
                                :value="cat"
                            />
                        </datalist>
                        <div
                            v-if="form.errors.kategori"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ form.errors.kategori }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Harga (Rp)</label
                            >
                            <input
                                v-model="form.harga"
                                type="number"
                                min="0"
                                class="w-full border-gray-300 focus:border-brand-orange-500 focus:ring-brand-orange-500 rounded-md shadow-sm"
                                required
                            />
                            <div
                                v-if="form.errors.harga"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.harga }}
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Stok</label
                            >
                            <input
                                v-model="form.stok"
                                type="number"
                                min="0"
                                class="w-full border-gray-300 focus:border-brand-orange-500 focus:ring-brand-orange-500 rounded-md shadow-sm"
                                required
                            />
                            <div
                                v-if="form.errors.stok"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ form.errors.stok }}
                            </div>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Gambar Produk (Opsional)</label
                        >
                        <div
                            v-if="isEditing && props.product.gambar"
                            class="mb-3"
                        >
                            <img
                                :src="props.product.gambar"
                                class="w-16 h-16 rounded border border-gray-300 object-cover"
                            />
                        </div>
                        <input
                            @input="form.gambar = $event.target.files[0]"
                            type="file"
                            accept="image/*"
                            class="w-full border-gray-300 focus:border-brand-orange-500 focus:ring-brand-orange-500 rounded-md shadow-sm p-1.5 border"
                        />
                        <div
                            v-if="form.errors.gambar"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ form.errors.gambar }}
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200 text-right">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-[#1C1E1B] hover:bg-[#33352F] text-white font-['Sora'] font-semibold rounded-md transition-colors shadow-sm disabled:opacity-50"
                        >
                            Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
