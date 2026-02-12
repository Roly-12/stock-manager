<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    product: Object
});

// Calcul de la santé du stock pour l'affichage visuel
const getStockStatus = () => {
    // Sécurisation avec Number() pour le calcul
    const stock = Number(props.product.quantity);
    const min = Number(props.product.min_stock);

    if (stock <= 0) return { label: 'Rupture', class: 'bg-red-500' };
    if (stock <= min) return { label: 'Stock Faible', class: 'bg-amber-500' };
    return { label: 'En Stock', class: 'bg-emerald-500' };
};
</script>

<template>
    <Head :title="'Produit - ' + product.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="route('products.index')" class="flex items-center justify-center w-10 h-10 bg-white rounded-xl shadow-sm border border-slate-100 text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition-all no-underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <h2 class="font-black text-2xl text-slate-800 tracking-tight">Fiche Produit</h2>
                </div>
                <div class="flex items-center gap-2">
                    <div :class="['w-2.5 h-2.5 rounded-full animate-pulse', getStockStatus().class]"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ getStockStatus().label }}</span>
                </div>
            </div>
        </template>

        <div class="py-12 bg-slate-50 min-h-[calc(100vh-160px)] flex flex-col">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 flex-grow w-full">
                
                <div class="bg-white rounded-[3rem] shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-10 border-b border-slate-50 bg-gradient-to-br from-white to-slate-50/50">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div>
                                <span class="inline-block px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-xl text-xs font-black tracking-widest uppercase mb-4">
                                    SKU: {{ product.sku }}
                                </span>
                                <h1 class="text-4xl font-black text-slate-900 tracking-tight">{{ product.name }}</h1>
                                <p class="text-slate-400 mt-2 font-medium">Référence unique du catalogue</p>
                            </div>
                            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm text-center min-w-[160px]">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Prix Unitaire</p>
                                <p class="text-4xl font-black text-indigo-600">{{ product.price }}<span class="text-xl ml-1">€</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="p-10 grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Niveau de Stock Actuel</label>
                            <div class="flex items-end gap-3">
                                <span class="text-5xl font-black text-slate-800">{{ product.quantity || 0 }}</span>
                                <span class="text-slate-400 font-bold mb-1">unités</span>
                            </div>
                            <div class="w-full h-2 bg-slate-100 rounded-full mt-4 overflow-hidden">
                                <div class="h-full transition-all duration-500" 
                                     :class="getStockStatus().class"
                                     :style="{ width: Math.min(((product.quantity || 0) / (product.min_stock * 2)) * 100, 100) + '%' }">
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 rounded-3xl p-6 border border-slate-100">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Seuils d'alerte</label>
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-xs text-slate-500 font-bold uppercase">Minimum</p>
                                    <p class="text-xl font-black text-slate-800">{{ product.min_stock || 0 }} pcs</p>
                                </div>
                                <div class="h-10 w-[1px] bg-slate-200"></div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-500 font-bold uppercase">Catégorie</p>
                                    <p class="text-xl font-black text-slate-800">{{ product.category?.name || 'Général' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-50 flex gap-4">
                        <Link :href="route('products.edit', product.id)" class="flex-1 text-center py-4 bg-slate-900 text-white rounded-2xl font-black hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200 uppercase text-xs tracking-widest no-underline">
                            Modifier le produit
                        </Link>
                    </div>
                </div>

                <footer class="mt-20 py-10 border-t border-slate-200">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-6 text-[11px] text-slate-400 font-black uppercase tracking-[0.2em]">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center border border-slate-100 shadow-sm text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <p>© 2026 StockPro • Protocole de sécurité activé</p>
                        </div>
                        <div class="flex gap-8">
                            <a href="#" class="hover:text-indigo-600 transition-colors no-underline">Rapports</a>
                            <a href="#" class="hover:text-indigo-600 transition-colors no-underline">Historique</a>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </AuthenticatedLayout>
</template>