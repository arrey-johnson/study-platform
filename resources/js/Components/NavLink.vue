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
        ? 'inline-flex items-center px-3 pt-1 border-b-2 border-purple-600 text-sm font-medium leading-5 text-purple-600 focus:outline-none focus:border-purple-700 transition duration-150 ease-in-out'
        : 'inline-flex items-center px-3 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-purple-600 hover:border-gray-300 focus:outline-none focus:text-purple-600 focus:border-gray-300 transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
