<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    dbInfo: {
        type: Object,
        default: () => ({
            name: 'Database',
            driver: 'mysql',
            host: '127.0.0.1',
            totalTables: 0,
            totalRows: 0,
            tables: [],
        })
    }
});

const page = usePage();
const flash = computed(() => page.props.flash || {});

const restoreForm = useForm({
    file_sql: null,
});

const isRestoring = ref(false);
const isDownloading = ref(false);
const showConfirmModal = ref(false);
const selectedFileName = ref('');
const selectedFileSize = ref('');
const fileInputRef = ref(null);

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        restoreForm.file_sql = file;
        selectedFileName.value = file.name;
        selectedFileSize.value = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
    } else {
        restoreForm.file_sql = null;
        selectedFileName.value = '';
        selectedFileSize.value = '';
    }
};

const handleBackup = () => {
    isDownloading.value = true;
    window.location.href = route('backup.download');
    setTimeout(() => {
        isDownloading.value = false;
    }, 4000);
};

const triggerRestoreConfirmation = () => {
    if (!restoreForm.file_sql) return;
    showConfirmModal.value = true;
};

const executeRestore = () => {
    showConfirmModal.value = false;
    isRestoring.value = true;

    restoreForm.post(route('backup.upload'), {
        preserveScroll: true,
        onSuccess: () => {
            isRestoring.value = false;
            restoreForm.reset();
            selectedFileName.value = '';
            selectedFileSize.value = '';
            if (fileInputRef.value) {
                fileInputRef.value.value = '';
            }
        },
        onError: () => {
            isRestoring.value = false;
        }
    });
};
</script>

<template>
    <Head title="Pencadangan & Pemulihan Basis Data" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="font-['Sora'] font-bold text-2xl text-[#1C1E1B] leading-tight">
                        Pencadangan & Pemulihan Basis Data
                    </h2>
                    <p class="text-sm text-[#5B5F58] mt-1">
                        Unduh salinan cadangan database atau pulihkan data toko dari berkas SQL secara terpadu.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold font-['IBM_Plex_Mono'] px-3 py-1.5 bg-[#F3F3EE] text-[#1C1E1B] border border-[#E1E2D9] rounded-md">
                        DATABASE: {{ dbInfo.name }}
                    </span>
                </div>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Flash Alert Banners -->
                <div v-if="flash.success" class="p-4 bg-white border border-[#E1E2D9] border-l-4 border-l-[#0C8B7C] rounded-md shadow-2xs flex items-center justify-between transition-all">
                    <div class="flex items-center gap-3">
                        <div class="p-1.5 bg-[#0C8B7C]/10 rounded text-[#0C8B7C]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold font-['Sora'] text-sm text-[#1C1E1B]">Operasi Berhasil</h4>
                            <p class="text-xs text-[#5B5F58]">{{ flash.success }}</p>
                        </div>
                    </div>
                </div>

                <div v-if="flash.error" class="p-4 bg-white border border-[#E1E2D9] border-l-4 border-l-[#FF5A1F] rounded-md shadow-2xs flex items-center justify-between transition-all">
                    <div class="flex items-center gap-3">
                        <div class="p-1.5 bg-[#FF5A1F]/10 rounded text-[#FF5A1F]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold font-['Sora'] text-sm text-[#1C1E1B]">Pemberitahuan Sistem</h4>
                            <p class="text-xs text-[#5B5F58]">{{ flash.error }}</p>
                        </div>
                    </div>
                </div>

                <!-- Database Status Overview Card (Deep Charcoal & Minimal Accent) -->
                <div class="bg-[#1C1E1B] text-white rounded-lg p-6 sm:p-7 shadow-xs border border-[#1C1E1B] relative overflow-hidden">
                    <div class="relative z-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="space-y-1">
                            <p class="text-[11px] font-['IBM_Plex_Mono'] uppercase tracking-wider text-white/60 font-semibold">Basis Data Aktif</p>
                            <h3 class="text-xl font-bold font-['Sora'] text-white flex items-center gap-2">
                                {{ dbInfo.name }}
                                <span class="text-[10px] font-semibold font-['IBM_Plex_Mono'] px-2 py-0.5 bg-[#0C8B7C]/20 text-[#0C8B7C] border border-[#0C8B7C]/40 rounded">
                                    ONLINE
                                </span>
                            </h3>
                            <p class="text-xs font-['IBM_Plex_Mono'] text-white/60">Host: {{ dbInfo.host }} | {{ dbInfo.driver }}</p>
                        </div>

                        <div class="space-y-1">
                            <p class="text-[11px] font-['IBM_Plex_Mono'] uppercase tracking-wider text-white/60 font-semibold">Jumlah Tabel</p>
                            <h3 class="text-xl font-bold font-['IBM_Plex_Mono'] text-white">{{ dbInfo.totalTables }} <span class="text-xs font-normal text-white/60 font-['Sora']">Tabel Skema</span></h3>
                            <p class="text-xs text-white/60">Struktur relasional & stored procedure</p>
                        </div>

                        <div class="space-y-1">
                            <p class="text-[11px] font-['IBM_Plex_Mono'] uppercase tracking-wider text-white/60 font-semibold">Total Baris Data</p>
                            <h3 class="text-xl font-bold font-['IBM_Plex_Mono'] text-white">{{ dbInfo.totalRows.toLocaleString() }} <span class="text-xs font-normal text-white/60 font-['Sora']">Baris</span></h3>
                            <p class="text-xs text-white/60">Produk, transaksi, pelanggan & log</p>
                        </div>

                        <div class="space-y-1 flex flex-col justify-center">
                            <p class="text-[11px] font-['IBM_Plex_Mono'] uppercase tracking-wider text-white/60 font-semibold">Status Cadangan</p>
                            <div class="flex items-center gap-2 text-xs font-semibold font-['Sora'] text-[#0C8B7C]">
                                <span class="w-2 h-2 rounded-full bg-[#0C8B7C] animate-pulse"></span>
                                Siap Diekspor & Dipulihkan
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Actions Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Card Backup (Unduh Cadangan) -->
                    <div class="bg-white rounded-lg border border-[#E1E2D9] shadow-2xs overflow-hidden flex flex-col justify-between hover:border-[#1C1E1B] transition-colors">
                        <div class="p-6 sm:p-7 space-y-5">
                            <div class="flex items-center gap-3.5 pb-4 border-b border-[#E1E2D9]">
                                <div class="w-10 h-10 rounded-md bg-[#1C1E1B] text-white flex items-center justify-center text-base font-bold shrink-0">
                                    📥
                                </div>
                                <div>
                                    <h3 class="text-base font-bold font-['Sora'] text-[#1C1E1B]">Unduh Cadangan (Backup)</h3>
                                    <p class="text-xs text-[#5B5F58]">Ekspor seluruh struktur dan data database menjadi berkas <code>.sql</code>.</p>
                                </div>
                            </div>

                            <p class="text-[#5B5F58] text-xs leading-relaxed">
                                Fitur ini akan mengekspor seluruh tabel dari database <strong class="text-[#1C1E1B] font-semibold">{{ dbInfo.name }}</strong> ke dalam berkas standar SQL siap simpan untuk arsip atau migrasi sistem.
                            </p>

                            <div class="bg-[#F3F3EE] border border-[#E1E2D9] rounded-md p-4 space-y-2">
                                <div class="text-xs font-bold font-['Sora'] text-[#1C1E1B] flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-[#FF5A1F] rounded-full"></span>
                                    Cakupan Berkas Cadangan:
                                </div>
                                <ul class="text-xs text-[#5B5F58] space-y-1 font-['Inter']">
                                    <li>✓ Skema tabel (DDL), primary key, auto increment & relasi</li>
                                    <li>✓ Seluruh rekaman data transaksi, produk, dan pengguna</li>
                                    <li>✓ Kompatibel dengan MySQL CLI, phpMyAdmin, dan DBeaver</li>
                                </ul>
                            </div>
                        </div>

                        <div class="p-5 bg-[#F3F3EE]/60 border-t border-[#E1E2D9] flex items-center justify-between">
                            <span class="text-[11px] font-['IBM_Plex_Mono'] text-[#5B5F58]">
                                FORMAT: .sql (UTF-8)
                            </span>

                            <button 
                                type="button"
                                @click="handleBackup" 
                                :disabled="isDownloading"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1C1E1B] hover:bg-[#33352F] text-white font-semibold font-['Sora'] text-xs rounded-md transition-colors cursor-pointer shadow-2xs disabled:opacity-60 disabled:cursor-not-allowed"
                            >
                                <svg v-if="isDownloading" class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ isDownloading ? 'Membuat Cadangan...' : 'Unduh Cadangan Database' }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Card Restore (Pulihkan Data) -->
                    <div class="bg-white rounded-lg border border-[#E1E2D9] shadow-2xs overflow-hidden flex flex-col justify-between hover:border-[#1C1E1B] transition-colors">
                        <div class="p-6 sm:p-7 space-y-5">
                            <div class="flex items-center gap-3.5 pb-4 border-b border-[#E1E2D9]">
                                <div class="w-10 h-10 rounded-md bg-[#FF5A1F] text-white flex items-center justify-center text-base font-bold shrink-0">
                                    📤
                                </div>
                                <div>
                                    <h3 class="text-base font-bold font-['Sora'] text-[#1C1E1B]">Pulihkan Data (Restore)</h3>
                                    <p class="text-xs text-[#5B5F58]">Impor dan terapkan berkas cadangan <code>.sql</code> ke dalam database.</p>
                                </div>
                            </div>

                            <div class="p-3.5 bg-[#FF5A1F]/10 border border-[#FF5A1F]/20 rounded-md text-xs text-[#1C1E1B] space-y-1">
                                <p class="font-bold font-['Sora'] text-[#FF5A1F]">Perhatian Saat Pemulihan</p>
                                <p class="text-[#5B5F58]">Proses restore akan mengeksekusi instruksi SQL dari file yang diunggah. Pastikan berkas berasal dari sumber terpercaya.</p>
                            </div>

                            <!-- Dropzone File Upload -->
                            <form @submit.prevent="triggerRestoreConfirmation" id="restore-form">
                                <div class="relative border-2 border-dashed border-[#D8D9CF] hover:border-[#1C1E1B] rounded-lg p-5 text-center transition-colors bg-[#F3F3EE]/50">
                                    <input 
                                        ref="fileInputRef"
                                        type="file" 
                                        id="file_sql"
                                        accept=".sql,.txt"
                                        @change="handleFileChange"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                        :disabled="isRestoring"
                                        required
                                    >
                                    <div class="flex flex-col items-center justify-center space-y-1.5">
                                        <div v-if="selectedFileName" class="space-y-1">
                                            <p class="text-xs font-bold font-['Sora'] text-[#1C1E1B]">{{ selectedFileName }}</p>
                                            <p class="text-[11px] font-['IBM_Plex_Mono'] text-[#5B5F58]">{{ selectedFileSize }}</p>
                                            <span class="inline-block text-[10px] font-semibold font-['IBM_Plex_Mono'] px-2 py-0.5 bg-[#0C8B7C]/10 text-[#0C8B7C] border border-[#0C8B7C]/20 rounded">
                                                SIAP DIPULIHKAN
                                            </span>
                                        </div>
                                        <div v-else class="space-y-1">
                                            <p class="text-xs font-semibold font-['Sora'] text-[#1C1E1B]">Pilih atau seret berkas <span class="text-[#FF5A1F]">.sql</span> ke sini</p>
                                            <p class="text-[11px] text-[#5B5F58]">Maksimal ukuran unggahan: 100 MB</p>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="restoreForm.errors.file_sql" class="text-[#FF5A1F] text-xs font-semibold mt-2">
                                    {{ restoreForm.errors.file_sql }}
                                </div>
                            </form>
                        </div>

                        <div class="p-5 bg-[#F3F3EE]/60 border-t border-[#E1E2D9] flex items-center justify-between">
                            <span class="text-[11px] font-['IBM_Plex_Mono'] text-[#5B5F58]">
                                TARGET: {{ dbInfo.name }}
                            </span>

                            <button 
                                type="button"
                                @click="triggerRestoreConfirmation"
                                :disabled="restoreForm.processing || !restoreForm.file_sql || isRestoring"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#FF5A1F] hover:bg-[#C43D0C] text-white font-semibold font-['Sora'] text-xs rounded-md transition-colors cursor-pointer shadow-2xs disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg v-if="isRestoring" class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ isRestoring ? 'Memulihkan Basis Data...' : 'Pulihkan Basis Data' }}</span>
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- Confirmation Modal -->
        <div v-if="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto bg-[#1C1E1B]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-2xl space-y-4 border border-[#E1E2D9]">
                <div>
                    <h3 class="text-base font-bold font-['Sora'] text-[#1C1E1B]">Konfirmasi Pemulihan Data</h3>
                    <p class="text-xs text-[#5B5F58] mt-1.5 leading-relaxed">
                        Anda akan menimpa data pada database <strong class="text-[#1C1E1B]">{{ dbInfo.name }}</strong> dengan isi berkas cadangan <strong class="text-[#1C1E1B]">{{ selectedFileName }}</strong>.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E1E2D9]">
                    <button 
                        type="button" 
                        @click="showConfirmModal = false"
                        class="px-4 py-2 text-xs font-semibold font-['Sora'] text-[#5B5F58] hover:text-[#1C1E1B] hover:bg-[#F3F3EE] rounded-md transition-colors cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        @click="executeRestore"
                        class="px-4 py-2 text-xs font-semibold font-['Sora'] bg-[#FF5A1F] hover:bg-[#C43D0C] text-white rounded-md transition-colors cursor-pointer shadow-2xs"
                    >
                        Lanjutkan Pemulihan
                    </button>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
