<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const reports = [
    { 
        title: 'Inventaire Complet', 
        format: 'Excel (.xlsx)',
        desc: 'Extraction brute de la base de données : SKU, désignations, quantités et seuils d\'alerte pour audit.',
        url: '/exports/excel',
        icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        color: 'text-emerald-600',
        bg: 'bg-emerald-50',
        btn: 'hover:bg-emerald-600'
    },
    { 
        title: 'Valorisation du Stock', 
        format: 'Portable Document (.pdf)',
        desc: 'Calcul automatique de la valeur financière du stock actuel basé sur le dernier prix d\'achat enregistré.',
        url: '/exports/pdf',
        icon: 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
        color: 'text-indigo-600',
        bg: 'bg-indigo-50',
        btn: 'hover:bg-indigo-600'
    }
];
</script>

<template>
    <Head title="Rapports & Exports" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-white rounded-2xl shadow-sm border border-slate-100 text-slate-800">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-2xl text-slate-800 leading-tight">Centre d'Extraction</h2>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-[0.2em]">Rapports comptables et logistiques</p>
                </div>
            </div>
        </template>

        <div class="py-12 bg-slate-50/50 min-h-screen">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div v-for="report in reports" :key="report.title" 
                         class="group bg-white p-8 rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.02)] border border-slate-200 hover:border-indigo-300 transition-all duration-300 relative overflow-hidden">
                        
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-slate-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>

                        <div class="relative z-10">
                            <div :class="[report.bg, report.color]" class="w-14 h-14 flex items-center justify-center rounded-2xl mb-6 shadow-inner group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="report.icon" />
                                </svg>
                            </div>

                            <div class="mb-6">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-xl font-black text-slate-800 tracking-tight">{{ report.title }}</h3>
                                    <span class="px-2 py-0.5 bg-slate-100 text-[9px] font-black text-slate-500 rounded uppercase tracking-tighter">{{ report.format }}</span>
                                </div>
                                <p class="text-sm text-slate-500 font-medium leading-relaxed">{{ report.desc }}</p>
                            </div>
                            
                            <a :href="report.url" 
                               :class="report.btn"
                               class="inline-flex items-center justify-center w-full bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-xl shadow-slate-200 active:scale-95 group/btn">
                                <span>Générer le document</span>
                                <svg class="w-4 h-4 ml-2 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-12 p-6 bg-indigo-50 rounded-3xl border border-indigo-100 flex items-start gap-4">
                    <div class="text-indigo-600 mt-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-indigo-900">Information sur les données</p>
                        <p class="text-xs text-indigo-700/70 font-medium mt-1">Les rapports sont générés en temps réel. Pour toute clôture fiscale, assurez-vous que tous les mouvements de la journée ont été saisis avant l'exportation.</p>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>