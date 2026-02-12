<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({ 
    products: Array 
});

const form = useForm({
    product_id: '',
    type: 'entrée',
    quantity: 1,
    reason: ''
});

const submit = () => {
    form.post(route('movements.store'), { 
        onSuccess: () => {
            // Optionnel : Notification de succès ici
        },
        onError: (errors) => console.log(errors)
    });
};
</script>

<template>
    <Head title="Nouveau Mouvement" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('movements.index')" class="p-2 bg-white rounded-xl shadow-sm border border-slate-100 text-slate-400 hover:text-indigo-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <div>
                    <h2 class="font-black text-2xl text-slate-800 leading-tight">Enregistrer un Flux</h2>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Mise à jour manuelle des stocks</p>
                </div>
            </div>
        </template>

        <div class="py-12 bg-slate-50/50 min-h-screen">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div v-if="$page.props.auth.user.role === 'observateur'" class="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-600 text-sm font-bold flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Mode lecture seule : Vous ne pouvez pas enregistrer de mouvements.
                </div>

                <div class="bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-200 rounded-[2.5rem] overflow-hidden">
                    
                    <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                        <p class="text-sm font-black text-slate-400 uppercase tracking-widest text-center">Formulaire de mouvement</p>
                    </div>

                    <form @submit.prevent="submit" class="p-8 space-y-8">
                        
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider ml-1">Produit concerné</label>
                            <div class="relative group">
                                <select v-model="form.product_id" 
                                    class="custom-select block w-full border-none ring-1 ring-slate-200 focus:ring-2 focus:ring-indigo-500 rounded-2xl shadow-sm py-4 pl-5 pr-12 font-bold text-slate-700 transition-all cursor-pointer bg-white">
                                    <option value="">Sélectionnez un article...</option>
                                    <option v-for="p in products" :key="p.id" :value="p.id">
                                        [{{ p.sku }}] {{ p.name }} — Stock : {{ p.quantity }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                            </div>
                            <p v-if="form.errors.product_id" class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1 tracking-wide">{{ form.errors.product_id }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button type="button" @click="form.type = 'entrée'"
                                :class="form.type === 'entrée' ? 'ring-2 ring-emerald-500 bg-emerald-50 border-emerald-200 shadow-lg shadow-emerald-50' : 'bg-white border-slate-100 opacity-60'"
                                class="flex flex-col items-center gap-3 p-6 rounded-[2rem] border-2 transition-all duration-300">
                                <div :class="form.type === 'entrée' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400'" class="w-12 h-12 rounded-full flex items-center justify-center font-black text-xl shadow-inner">↑</div>
                                <span :class="form.type === 'entrée' ? 'text-emerald-700' : 'text-slate-500'" class="font-black text-[10px] uppercase tracking-widest text-center">Entrée</span>
                            </button>

                            <button type="button" @click="form.type = 'sortie'"
                                :class="form.type === 'sortie' ? 'ring-2 ring-rose-500 bg-rose-50 border-rose-200 shadow-lg shadow-rose-50' : 'bg-white border-slate-100 opacity-60'"
                                class="flex flex-col items-center gap-3 p-6 rounded-[2rem] border-2 transition-all duration-300">
                                <div :class="form.type === 'sortie' ? 'bg-rose-500 text-white' : 'bg-slate-100 text-slate-400'" class="w-12 h-12 rounded-full flex items-center justify-center font-black text-xl shadow-inner">↓</div>
                                <span :class="form.type === 'sortie' ? 'text-rose-700' : 'text-slate-500'" class="font-black text-[10px] uppercase tracking-widest text-center">Sortie</span>
                            </button>

                            <button type="button" @click="form.type = 'ajustement'"
                                :class="form.type === 'ajustement' ? 'ring-2 ring-amber-500 bg-amber-50 border-amber-200 shadow-lg shadow-amber-50' : 'bg-white border-slate-100 opacity-60'"
                                class="flex flex-col items-center gap-3 p-6 rounded-[2rem] border-2 transition-all duration-300">
                                <div :class="form.type === 'ajustement' ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-400'" class="w-12 h-12 rounded-full flex items-center justify-center font-black text-xl shadow-inner">!</div>
                                <span :class="form.type === 'ajustement' ? 'text-amber-700' : 'text-slate-500'" class="font-black text-[10px] uppercase tracking-widest text-center">Correction</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider ml-1">
                                    {{ form.type === 'ajustement' ? 'Valeur réelle' : 'Quantité' }}
                                </label>
                                <input v-model="form.quantity" type="number" min="0" 
                                    class="appearance-none block w-full border-none ring-1 ring-slate-200 focus:ring-2 focus:ring-indigo-500 rounded-2xl shadow-sm py-4 px-5 font-mono font-black text-xl text-slate-700 transition-all" />
                                <p v-if="form.errors.quantity" class="text-rose-500 text-[10px] font-black mt-1 ml-1 tracking-wide uppercase">{{ form.errors.quantity }}</p>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider ml-1">Motif / Référence</label>
                                <input v-model="form.reason" type="text" placeholder="ex: Livraison fournisseur"
                                    class="block w-full border-none ring-1 ring-slate-200 focus:ring-2 focus:ring-indigo-500 rounded-2xl shadow-sm py-4 px-5 font-bold text-slate-700 transition-all placeholder:text-slate-300" />
                                <p v-if="form.errors.reason" class="text-rose-500 text-[10px] font-black mt-1 ml-1 tracking-wide uppercase">{{ form.errors.reason }}</p>
                            </div>
                        </div>

                        <div class="pt-6">
                            <button type="submit" 
                                :disabled="form.processing || $page.props.auth.user.role === 'observateur'"
                                class="w-full relative group overflow-hidden bg-slate-900 disabled:bg-slate-300 disabled:cursor-not-allowed py-5 rounded-[1.5rem] transition-all duration-300 shadow-2xl shadow-slate-200 active:scale-95">
                                <div v-if="!form.processing && $page.props.auth.user.role !== 'observateur'" class="absolute inset-0 w-0 bg-indigo-600 transition-all duration-500 ease-out group-hover:w-full"></div>
                                <span class="relative font-black text-white uppercase tracking-[0.2em] text-sm group-hover:scale-110 transition-transform inline-block">
                                    {{ form.processing ? 'Traitement en cours...' : 'Confirmer l\'opération' }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <footer class="mt-auto py-12 bg-white border-t border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                 <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">
                    &copy; {{ new Date().getFullYear() }} — StockFlow Secure Terminal
                </p>
            </div>
        </footer>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Suppression des flèches par défaut sur tous les navigateurs */
input::-webkit-outer-spin-button, 
input::-webkit-inner-spin-button { 
    -webkit-appearance: none; 
    margin: 0; 
}
input[type=number] { 
    -moz-appearance: textfield; 
    appearance: textfield;
}

.custom-select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}
</style>