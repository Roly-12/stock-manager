<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-50">
        <div>
            <Link href="/">
                <div class="flex items-center justify-center w-24 h-24 bg-white shadow-lg rounded-full border border-gray-100 p-2 hover:scale-105 transition-transform duration-300">
                    <ApplicationLogo />
                </div>
            </Link>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white shadow-2xl overflow-hidden sm:rounded-3xl border border-gray-50">
            <slot />
        </div>
    </div>
</template>
