<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const reports = [
    { 
        title: 'Inventaire Complet (Excel)', 
        desc: 'Télécharge la liste complète des produits, SKU et stocks actuels pour modification en masse.',
        url: '/exports/excel',
        icon: '📊',
        color: 'bg-green-100 text-green-700'
    },
    { 
        title: 'Rapport de Valorisation (PDF)', 
        desc: 'Génère un document PDF calculant la valeur monétaire totale de votre stock actuel.',
        url: '/exports/pdf',
        icon: '📄',
        color: 'bg-blue-100 text-blue-700'
    }
];
</script>

<template>
    <Head title="Rapports & Exports" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Centre de Rapports</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div v-for="report in reports" :key="report.title" 
                     class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div :class="['w-12 h-12 flex items-center justify-center rounded-xl text-2xl mb-4', report.color]">
                        {{ report.icon }}
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ report.title }}</h3>
                    <p class="text-gray-500 mb-6">{{ report.desc }}</p>
                    
                    <a :href="report.url" 
                       class="inline-block bg-gray-900 text-white px-6 py-2 rounded-lg font-medium hover:bg-black transition">
                        Générer le document
                    </a>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>