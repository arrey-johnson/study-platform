<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col justify-center items-center p-4 bg-gray-100">
        <div class="mb-6">
            <Link href="/">
                <ApplicationLogo class="w-auto h-32" />
            </Link>
        </div>

        <div class="w-full max-w-md bg-white p-6 rounded shadow-md">
            <slot />
        </div>
    </div>
</template>
