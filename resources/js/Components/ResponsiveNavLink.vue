<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-[#FF5A1F] text-start text-base font-semibold text-[#1C1E1B] bg-[#E1E2D9]/30 font-[\'Sora\'] focus:outline-none transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-[#5B5F58] hover:text-[#1C1E1B] hover:bg-[#E1E2D9]/20 hover:border-[#E1E2D9] font-[\'Sora\'] focus:outline-none transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
