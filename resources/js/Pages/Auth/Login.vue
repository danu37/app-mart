<script setup>
import { ref } from "vue";
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Masuk ke TechStore" />

        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-600 bg-green-50 p-3 rounded-lg border border-green-100"
        >
            {{ status }}
        </div>

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-navy-950 mb-2 tracking-tight">
                Selamat Datang Kembali
            </h2>
            <p class="text-sm text-gray-500">
                Masuk untuk melanjutkan ke akun TechStore
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel
                    for="email"
                    value="Email"
                    class="text-navy-900 font-semibold"
                />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1.5 block w-full bg-gray-50/50 border-gray-200 focus:border-brand-orange-500 focus:ring-brand-orange-500 rounded-xl transition-colors"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@email.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <InputLabel
                        for="password"
                        value="Password"
                        class="text-navy-900 font-semibold"
                    />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs font-semibold text-brand-orange-500 hover:text-brand-orange-600 transition-colors"
                    >
                        Lupa password?
                    </Link>
                </div>

                <div class="relative mt-1.5">
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="block w-full bg-gray-50/50 border-gray-200 focus:border-brand-orange-500 focus:ring-brand-orange-500 rounded-xl transition-colors pr-10"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-navy-700 transition-colors"
                    >
                        <svg
                            v-if="!showPassword"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                            />
                        </svg>
                        <svg
                            v-else
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                            />
                        </svg>
                    </button>
                </div>

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block">
                <label class="flex items-center cursor-pointer group w-max">
                    <Checkbox
                        name="remember"
                        v-model:checked="form.remember"
                        class="border-gray-300 text-brand-orange-500 focus:ring-brand-orange-500 rounded"
                    />
                    <span
                        class="ms-2 text-sm text-gray-600 group-hover:text-navy-900 transition-colors"
                        >Ingat saya</span
                    >
                </label>
            </div>

            <button
                type="submit"
                class="w-full py-3 px-4 bg-brand-orange-500 hover:bg-brand-orange-600 text-white font-bold rounded-xl transition-all duration-300 shadow-lg shadow-brand-orange-500/30 flex items-center justify-center disabled:opacity-70 disabled:cursor-not-allowed"
                :disabled="form.processing"
            >
                <svg
                    v-if="form.processing"
                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                    xmlns="http://www.w3.org/2000/svg"
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
                Masuk
            </button>
        </form>

        <div class="mt-6 flex items-center gap-3">
            <hr class="flex-1 border-gray-200" />
            <span
                class="text-xs text-gray-400 font-medium uppercase tracking-wide"
                >atau</span
            >
            <hr class="flex-1 border-gray-200" />
        </div>

        <button
            type="button"
            class="mt-6 w-full flex items-center justify-center gap-3 bg-white border border-gray-200 hover:bg-gray-50 text-navy-900 font-semibold py-3 px-4 rounded-xl transition-colors shadow-sm"
        >
            <svg
                class="h-5 w-5"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                    fill="#4285F4"
                />
                <path
                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                    fill="#34A853"
                />
                <path
                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                    fill="#FBBC05"
                />
                <path
                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                    fill="#EA4335"
                />
            </svg>
            Masuk dengan Google
        </button>

        <p class="mt-8 text-center text-sm text-gray-600">
            Belum punya akun?
            <Link
                :href="route('register')"
                class="text-brand-orange-500 hover:text-brand-orange-600 font-bold transition-colors"
            >
                Daftar sekarang
            </Link>
        </p>
    </GuestLayout>
</template>
