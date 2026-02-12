<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue'; 
import debounce from 'lodash/debounce';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object
});

const search = ref(props.filters.search || '');
const selectedQR = ref(null);
const productToDelete = ref(null); // Nouvel état pour la suppression

// --- CORRECTION DU CALCUL (Anti-NaN) ---
const totalStockQuantity = computed(() => {
    if (!props.products?.data) return 0;
    return props.products.data.reduce((acc, product) => {
        return acc + Number(product.quantity || 0);
    }, 0);
});

const totalAlerts = computed(() => {
    if (!props.products?.data) return 0;
    return props.products.data.filter(p => {
        const stock = Number(p.quantity || 0);
        const min = Number(p.min_stock || 0);
        return stock <= min && stock > 0;
    }).length;
});

watch(search, debounce(() => {
    router.get('/products', { search: search.value }, { preserveState: true, replace: true });
}, 400));

// --- LOGIQUE DE SUPPRESSION CORRIGÉE ---
const confirmDelete = () => {
    if (productToDelete.value) {
        const id = productToDelete.value.id;
        console.log("Tentative de suppression de l'ID:", id); // Vérifie ça dans F12

        router.delete(route('products.destroy', { product: id }), {
            onSuccess: () => { 
                productToDelete.value = null; 
            },
            onError: (err) => {
                console.error("Erreur Inertia:", err);
            },
            preserveScroll: true
        });
    }
};

const getStockClass = (current, min) => {
    const s = Number(current || 0);
    const m = Number(min || 0);
    if (s <= 0) return 'bg-red-50 text-red-600 border border-red-100 shadow-sm shadow-red-50';
    if (s <= m) return 'bg-amber-50 text-amber-600 border border-amber-100 shadow-sm shadow-amber-50';
    return 'bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm shadow-emerald-50';
};

// On récupère l'URL de base du site (ex: http://127.0.0.1:8000)
const baseUrl = window.location.origin;

const getQRCodeUrl = (productId, scale = 2) => {
    // On génère l'URL de la fiche produit : /products/ID
    const productUrl = `${baseUrl}/products/${productId}`;
    
    // On encode l'URL pour qu'elle passe sans erreur dans l'API de génération
    const encodedUrl = encodeURIComponent(productUrl);
    
    return `https://bwipjs-api.metafloor.com/?bcid=qrcode&text=${encodedUrl}&scale=${scale}&backgroundcolor=ffffff`;
};
</script>

<template>
    <Head title="Produit" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="bg-indigo-600 p-2.5 rounded-2xl shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h2 class="font-black text-2xl text-slate-800 tracking-tight">Gestion des produits</h2>
                </div>
                <Link :href="route('products.create')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-2xl font-bold text-sm transition shadow-sm">
                    + Nouveau Produit
                </Link>
            </div>
        </template>

        <div class="py-12 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100 flex items-center gap-5">
                        <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-2xl">📦</div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Total en Stock</p>
                            <p class="text-3xl font-black text-slate-800">{{ totalStockQuantity }} <span class="text-xs text-slate-400 tracking-normal">unités</span></p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100 flex items-center gap-5">
                        <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-2xl">⚠️</div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Alertes Stock</p>
                            <p class="text-3xl font-black text-slate-800">{{ totalAlerts }} <span class="text-xs text-slate-400 tracking-normal">références</span></p>
                        </div>
                    </div>

                    <div class="bg-indigo-600 p-6 rounded-[2.5rem] shadow-xl shadow-indigo-100 flex items-center gap-5 text-white">
                        <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center text-2xl">✨</div>
                        <div>
                            <p class="text-[10px] font-black text-indigo-200 uppercase tracking-[0.2em]">Catalogue</p>
                            <p class="text-3xl font-black">{{ products.data.length }} <span class="text-xs text-indigo-200 tracking-normal">produits</span></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-100">
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">🔍</span>
                        <input v-model="search" type="text" placeholder="Rechercher par SKU ou nom..." 
                               class="pl-12 w-full border-none bg-slate-50 rounded-2xl focus:ring-2 focus:ring-indigo-500 font-medium text-slate-600" />
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-50 text-slate-400">
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest">SKU</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest">Produit & Prix</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-center">Quantité</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-right">Actions & QR</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="product in products.data" :key="product.id" class="hover:bg-slate-50/50 transition-colors group">
                                <td class="p-6">
                                    <span class="font-mono text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100">
                                        {{ product.sku }}
                                    </span>
                                </td>

                                <td class="p-6">
                                    <div class="font-bold text-slate-800">{{ product.name }}</div>
                                    <div class="text-slate-400 font-bold text-xs mt-0.5">{{ product.price }} €</div>
                                </td>

                                <td class="p-6 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span :class="['min-w-[60px] px-4 py-2 rounded-2xl text-sm font-black tracking-tight border transition-all', getStockClass(product.quantity, product.min_stock)]">
                                            {{ product.quantity }}
                                        </span>
                                        <span class="text-[9px] font-bold uppercase text-slate-300 tracking-widest">
                                            {{ product.quantity <= 0 ? 'Vide' : 'En Stock' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="p-6 text-right">
                                    <div class="flex justify-end items-center gap-4">
                                        <Link :href="route('products.show', product.id)" class="text-slate-400 hover:text-indigo-600 transition" title="Détails">👁️</Link>
                                        <Link :href="route('products.edit', product.id)" class="text-slate-400 hover:text-amber-600 transition" title="Modifier">✏️</Link>
                                        <button @click="productToDelete = product" class="text-slate-300 hover:text-red-600 transition" title="Supprimer">🗑️</button>
                                        
                                        <button @click="selectedQR = product.sku" class="ml-2 p-1 bg-white border border-slate-100 rounded-xl shadow-sm hover:border-indigo-400 hover:scale-110 transition-all" title="Agrandir le QR Code">
                                            <img :src="getQRCodeUrl(product.id, 1)" :alt="product.sku" class="h-10 w-10 object-contain" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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

        <div v-if="productToDelete" class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.stop class="bg-white rounded-[2.5rem] shadow-2xl max-w-md w-full overflow-hidden border border-slate-100 animate-in fade-in zoom-in duration-200">
                <div class="p-8 text-center">
                    <div class="w-20 h-20 bg-red-50 text-red-500 rounded-3xl flex items-center justify-center text-3xl mx-auto mb-6">⚠️</div>
                    <h3 class="text-2xl font-black text-slate-800 mb-2">Supprimer le produit ?</h3>
                    <p class="text-slate-500 font-medium">
                        Êtes-vous sûr de vouloir supprimer <span class="text-slate-800 font-bold">"{{ productToDelete.name }}"</span> ? 
                        Cette action est irréversible.
                    </p>
                </div>
                <div class="flex gap-3 p-6 bg-slate-50">
                    <button @click="productToDelete = null" class="flex-1 px-6 py-4 bg-white text-slate-600 rounded-2xl font-bold hover:bg-slate-100 transition border border-slate-200">Annuler</button>
                    <button @click="confirmDelete" class="flex-1 px-6 py-4 bg-red-600 text-white rounded-2xl font-bold hover:bg-red-700 transition shadow-lg shadow-red-200">Supprimer</button>
                </div>
            </div>
        </div>

        <div v-if="selectedQR" @click="selectedQR = null" class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/80 backdrop-blur-md p-4 transition-all">
            <div @click.stop class="bg-white p-8 rounded-[3rem] shadow-2xl max-w-sm w-full text-center animate-in fade-in zoom-in duration-300">
                <div class="mb-6 flex justify-between items-center px-2">
                    <h3 class="font-black text-slate-800 uppercase tracking-widest text-sm">Aperçu SKU</h3>
                    <button @click="selectedQR = null" class="text-slate-400 hover:text-slate-600 text-2xl font-bold">&times;</button>
                </div>
                
                <div class="bg-slate-50 p-6 rounded-[2rem] border-2 border-dashed border-slate-200 mb-6 group">
                    <img :src="getQRCodeUrl(selectedQR.id, 4)" 
                        :alt="selectedQR" 
                        class="w-full h-auto mx-auto rounded-xl shadow-sm group-hover:scale-105 transition-transform duration-500" />
                </div>

                <p class="font-mono text-xl font-black text-indigo-600 tracking-tighter mb-2">{{ selectedQR }}</p>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Scanner pour inventaire</p>
                
                <button @click="selectedQR = null" class="mt-8 w-full py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold hover:bg-slate-200 transition">
                    Fermer
                </button>
            </div>
        </div>

    </AuthenticatedLayout>
</template>