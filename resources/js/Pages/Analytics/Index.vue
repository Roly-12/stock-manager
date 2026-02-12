<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object, 
    alerts: Array,
    stable: Array
});

const generateOrder = (item) => {
    window.location.href = route('analytics.export-pdf', { product: item.id });
};
</script>

<template>
    <Head title="Analyses & Prévisions" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between py-4">
                <div class="flex items-center gap-6">
                    <div class="p-4 bg-indigo-600 rounded-[1.5rem] shadow-xl shadow-indigo-200 text-white">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2-2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-black text-4xl text-slate-800 tracking-tighter">Analyses & Prévisions</h2>
                        <p class="text-sm text-indigo-500 font-bold uppercase tracking-[0.3em]">Moteur prédictif de stock v1.0</p>
                    </div>
                </div>
                <div class="px-6 py-3 bg-white rounded-2xl border-2 border-slate-100 shadow-sm">
                    <span class="text-lg font-black text-slate-700">
                        {{ (alerts?.length || 0) + (stable?.length || 0) }} <span class="text-slate-400 font-medium text-sm uppercase">Produits analysés</span>
                    </span>
                </div>
            </div>
        </template>

        <div class="py-12 bg-slate-50/50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <div class="bg-white p-6 rounded-[2rem] border-2 border-slate-100 shadow-sm">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Valeur du Stock</p>
                        <p class="text-3xl font-black text-slate-800">{{ stats?.total_value || 0 }} €</p>
                        <div class="mt-2 text-[10px] text-emerald-500 font-bold uppercase text-nowrap">Capital immobilisé</div>
                    </div>

                    <div class="bg-indigo-600 p-6 rounded-[2rem] shadow-xl shadow-indigo-100">
                        <p class="text-xs font-black text-indigo-200 uppercase tracking-widest mb-2">Besoin Réappro</p>
                        <p class="text-3xl font-black text-white">{{ stats?.reorder_cost || 0 }} €</p>
                        <div class="mt-2 text-[10px] text-indigo-200 font-bold italic uppercase">Estimation de remise à niveau</div>
                    </div>

                    <div class="bg-white p-6 rounded-[2rem] border-2 border-slate-100 shadow-sm">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Top Sortie (Mois)</p>
                        <p class="text-xl font-black text-slate-800 truncate">{{ stats?.top_product || 'N/A' }}</p>
                        <div class="mt-2 text-[10px] text-orange-500 font-bold uppercase">Produit le plus sollicité</div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[3rem] border-2 border-slate-100 mb-12 shadow-sm">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter">Flux d'activité (30j)</h3>
                            <p class="text-xs text-slate-400 font-bold uppercase">Volume des entrées et sorties combinées</p>
                        </div>
                        <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-4 py-2 rounded-full border border-indigo-100 tracking-widest">LIVE ANALYTICS</span>
                    </div>
                    <div class="h-64 flex items-end justify-around gap-2 px-4">
                        <template v-if="stats?.daily_flux && stats.daily_flux.length > 0">
                            <div v-for="(value, index) in stats.daily_flux" :key="index" 
                                :style="{ 
                                    height: Math.max(((value / (Math.max(...stats.daily_flux) || 1)) * 100), 5) + '%' 
                                }"
                                class="w-full bg-indigo-100 rounded-t-xl hover:bg-indigo-500 transition-all cursor-help group relative">
                                
                                <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">
                                    Flux : {{ value }} u.
                                </div>
                            </div>
                        </template>
                        
                        <div v-else class="w-full h-full flex flex-col items-center justify-center border-2 border-dashed border-slate-100 rounded-3xl">
                            <p class="text-slate-300 font-black uppercase text-xs tracking-widest">Aucun mouvement sur les 30 derniers jours</p>
                        </div>
                    </div>
                </div>

                <div v-if="alerts && alerts.length > 0" class="mb-16">
                    <div class="flex items-center gap-6 mb-10">
                        <h3 class="font-black text-rose-500 uppercase tracking-[0.2em] text-lg shrink-0">Réapprovisionnement Urgent</h3>
                        <div class="h-[2px] flex-1 bg-rose-100"></div>
                    </div>

                    <div class="grid grid-cols-1 gap-8">
                        <div v-for="item in alerts" :key="item.id" 
                             class="bg-white p-10 rounded-[3rem] border-2 border-rose-100 shadow-xl shadow-rose-500/5 group relative">
                            
                            <div class="flex flex-col lg:flex-row justify-between gap-8">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-3 mb-6">
                                        <div class="inline-flex items-center gap-3 px-4 py-1.5 bg-rose-500 rounded-full">
                                            <span class="flex h-3 w-3 rounded-full bg-white animate-pulse"></span>
                                            <span class="text-xs font-black text-white uppercase tracking-widest italic">Alerte Critique</span>
                                        </div>
                                        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-slate-100 border border-slate-200 rounded-full">
                                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                                                Seuil de commande : {{ item.reorder_point || '?' }} unités
                                            </span>
                                        </div>
                                    </div>
                                    <h3 class="text-5xl font-black text-slate-900 tracking-tighter mb-2 group-hover:text-indigo-600 transition-colors">
                                        {{ item.name }}
                                    </h3>
                                    <p class="text-xl text-slate-400 font-bold uppercase tracking-widest">Référence : {{ item.sku }}</p>
                                </div>

                                <div class="flex items-center gap-8 bg-slate-50 p-6 rounded-[2rem] border border-slate-100">
                                    <div class="text-center px-6">
                                        <p class="text-5xl font-black text-slate-900">{{ item.current_stock }}</p>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">En Stock</p>
                                    </div>
                                    <div class="w-[2px] h-12 bg-slate-200"></div>
                                    <div class="text-center px-6">
                                        <p class="text-5xl font-black text-rose-600">{{ item.prediction?.jours_restants ?? '0' }}</p>
                                        <p class="text-[10px] font-black text-rose-400 uppercase italic text-nowrap">Jours restants</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-10 flex flex-col md:flex-row items-center gap-6">
                                <div class="flex-1 w-full p-6 bg-indigo-50 rounded-[1.5rem] border-2 border-indigo-100">
                                    <p class="text-indigo-900 text-xl font-bold italic leading-relaxed">
                                        "{{ item.prediction?.recommandation ?? 'Calcul en cours...' }}"
                                    </p>
                                </div>
                                <button @click="generateOrder(item)" 
                                        class="w-full md:w-auto px-10 py-6 bg-slate-900 hover:bg-indigo-600 text-white font-black uppercase tracking-[0.2em] text-sm rounded-[1.5rem] transition-all shadow-2xl active:scale-95 flex items-center justify-center gap-4 shrink-0">
                                    Générer le bon d'achat
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="stable && stable.length > 0" class="mt-8">
                    <div class="flex items-center gap-6 mb-8">
                        <h3 class="font-black text-emerald-500 uppercase tracking-[0.2em] text-lg shrink-0">Stocks Sécurisés</h3>
                        <div class="h-[2px] flex-1 bg-emerald-100"></div>
                    </div>

                    <div class="bg-white rounded-[3rem] shadow-xl border-2 border-slate-100 overflow-hidden">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50 border-b-2 border-slate-100">
                                    <th class="p-8 text-sm font-black text-slate-400 uppercase tracking-widest">Désignation</th>
                                    <th class="p-8 text-sm font-black text-slate-400 uppercase tracking-widest text-center">Statut Santé</th>
                                    <th class="p-8 text-sm font-black text-slate-400 uppercase tracking-widest text-right">Sécurité estimée</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-2 divide-slate-50">
                                <tr v-for="item in stable" :key="item.id" class="hover:bg-slate-50 transition-colors">
                                    <td class="p-8">
                                        <div class="text-2xl font-black text-slate-800">{{ item.name }}</div>
                                        <div class="text-sm text-slate-400 font-bold uppercase tracking-widest italic">{{ item.sku }}</div>
                                    </td>
                                    <td class="p-8 text-center">
                                        <span class="inline-flex px-6 py-2 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black uppercase tracking-widest border-2 border-emerald-200">
                                            Optimal
                                        </span>
                                    </td>
                                    <td class="p-8 text-right">
                                        <span class="text-2xl font-black text-slate-400 tabular-nums bg-slate-100 px-4 py-2 rounded-2xl">
                                            +{{ item.prediction?.jours_restants ?? '?' }} jrs
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <footer class="py-12 bg-white border-t-2 border-slate-100 text-center">
            <p class="text-xs font-black text-slate-300 uppercase tracking-[0.5em]">
                Stock Intelligence Engine — Analyse Temps Réel — 2026
            </p>
        </footer>
    </AuthenticatedLayout>
</template>