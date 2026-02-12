<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    movements: Object,
    filters: Object
});

const type = ref(props.filters.type || '');

watch(type, (value) => {
    router.get(route('movements.index'), { type: value }, { preserveState: true });
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit', month: 'long', hour: '2-digit', minute: '2-digit'
    });
};

const isEntry = (t) => t.toLowerCase().includes('entr');
const isAjust = (t) => t.toLowerCase().includes('ajust');
const exportPdf = () => {
    // On envoie le type actuel pour que le PDF soit filtré comme l'écran
    window.location.href = route('movements.export-pdf', { type: type.value });
};
</script>

<template>
    <Head title="Journal des Mouvements" />

    <AuthenticatedLayout>
        <template #header>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-white rounded-2xl shadow-sm border border-slate-100 text-indigo-600">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
            </div>
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight">Journal des Flux</h2>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-[0.2em]">Monitoring de stock en temps réel</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <button @click="exportPdf" 
                class="flex-1 md:flex-none bg-white hover:bg-slate-50 text-slate-600 px-5 py-3 rounded-2xl text-sm font-black transition-all flex items-center justify-center gap-2 border border-slate-200 shadow-sm active:scale-95 group">
                <svg class="w-5 h-5 text-rose-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Historique</span>
            </button>

            <Link v-if="$page.props.auth.user.role !== 'observateur'" 
                :href="route('movements.create')" 
                class="flex-1 md:flex-none bg-slate-900 hover:bg-indigo-600 text-white px-6 py-3 rounded-2xl text-sm font-black transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-2 active:scale-95 group">
                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Nouveau Mouvement</span>
            </Link>
        </div>
    </div>
</template>

        <div class="py-10 bg-slate-50/50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="mb-8 flex items-center">
                    <div class="group relative">
                        <select v-model="type" class="appearance-none pl-5 pr-12 py-3 bg-white border-none ring-1 ring-slate-200 focus:ring-2 focus:ring-indigo-500 rounded-2xl shadow-sm text-sm font-black text-slate-700 transition-all cursor-pointer">
                            <option value="">Tous les flux</option>
                            <option value="entrée">📈 Entrées</option>
                            <option value="sortie">📉 Sorties</option>
                            <option value="ajustement">⚙️ Corrections</option>
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-200 rounded-[2rem] overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="p-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Produit</th>
                                <th class="p-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] text-center">Type</th>
                                <th class="p-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] text-right">Quantité</th>
                                <th class="p-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Motif & Auteur</th>
                                <th class="p-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] text-right">Horodatage</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="mvt in movements.data" :key="mvt.id" 
                                class="group transition-all duration-300 border-l-4 border-transparent"
                                :class="{
                                    'hover:bg-emerald-50/40 hover:border-emerald-500': isEntry(mvt.type),
                                    'hover:bg-rose-50/40 hover:border-rose-500': mvt.type === 'sortie',
                                    'hover:bg-amber-50/40 hover:border-amber-500': isAjust(mvt.type)
                                }">
                                
                                <td class="p-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-black text-[10px]">
                                            {{ mvt.product?.name?.substring(0,2).toUpperCase() }}
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-800 text-sm group-hover:text-indigo-600 transition-colors">{{ mvt.product?.name }}</div>
                                            <div class="text-[10px] font-mono font-bold text-slate-400 uppercase tracking-tighter">{{ mvt.product?.sku }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-6 text-center">
                                    <span :class="{
                                        'bg-emerald-100 text-emerald-700 border-emerald-200': isEntry(mvt.type),
                                        'bg-rose-100 text-rose-700 border-rose-200': mvt.type === 'sortie',
                                        'bg-amber-100 text-amber-700 border-amber-200': isAjust(mvt.type)
                                    }" class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-wider border shadow-sm inline-block">
                                        {{ mvt.type }}
                                    </span>
                                </td>

                                <td class="p-6 text-right font-mono font-black text-xl" 
                                    :class="isEntry(mvt.type) ? 'text-emerald-600' : (isAjust(mvt.type) ? 'text-amber-600' : 'text-rose-600')">
                                    {{ isEntry(mvt.type) ? '+' : (isAjust(mvt.type) ? 'MOD ' : '-') }}{{ mvt.quantity }}
                                </td>

                                <td class="p-6">
                                    <div class="text-sm text-slate-700 font-bold line-clamp-1">"{{ mvt.reason }}"</div>
                                    <div class="text-[10px] text-slate-400 font-black uppercase mt-1 italic">Par: {{ mvt.user?.name }}</div>
                                </td>

                                <td class="p-6 text-right">
                                    <div class="text-sm font-black text-slate-700">{{ formatDate(mvt.created_at) }}</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-10 flex justify-center gap-3">
                    <Link v-for="link in movements.links" :key="link.label" :href="link.url || '#'" 
                          v-html="link.label"
                          :class="['px-5 py-2.5 rounded-2xl text-xs font-black transition-all border shadow-sm', 
                                   link.active ? 'bg-indigo-600 text-white border-indigo-600 shadow-indigo-100 ring-4 ring-indigo-50' : 'bg-white text-slate-600 border-slate-200 hover:border-indigo-300 hover:text-indigo-600',
                                   !link.url ? 'opacity-20 cursor-not-allowed scale-95' : 'hover:scale-105 active:scale-95']" />
                </div>
            </div>
        </div>

        <footer class="mt-auto py-12 bg-white border-t border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                    <div class="flex flex-col items-center md:items-start">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-black text-xs shadow-lg shadow-indigo-100">S</div>
                            <span class="text-lg font-black tracking-tighter text-slate-800">STOCK<span class="text-indigo-600">FLOW</span></span>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Système de gestion intelligent</p>
                    </div>
                    <nav class="flex flex-wrap justify-center gap-x-8 gap-y-4">
                        <Link :href="route('movements.index')" class="text-sm font-black text-slate-500 hover:text-indigo-600 transition-colors uppercase tracking-tight">Journal</Link>
                        <Link href="/products" class="text-sm font-black text-slate-500 hover:text-indigo-600 transition-colors uppercase tracking-tight">Inventaire</Link>
                        <Link href="/dashboard" class="text-sm font-black text-slate-500 hover:text-indigo-600 transition-colors uppercase tracking-tight">Statistiques</Link>
                    </nav>
                </div>
                <div class="mt-12 pt-8 border-t border-slate-50 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-tight">&copy; {{ new Date().getFullYear() }} StockFlow Management.</p>
                </div>
            </div>
        </footer>
    </AuthenticatedLayout>
</template>

<style scoped>
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>