<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    product: Object,
    categories: Array,
    isEditing: Boolean
});

const form = useForm({
    name: props.product?.name ?? '',
    sku: props.product?.sku ?? '',
    category_id: props.product?.category_id ?? '',
    price: props.product?.price ?? '',
    quantity: props.product?.quantity ?? 0,
    min_stock: props.product?.min_stock ?? 5,
    description: props.product?.description ?? '',
});

const submit = () => {
    if (props.isEditing) {
        form.put(route('products.update', props.product.id));
    } else {
        form.post(route('products.store'));
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Modifier Produit' : 'Nouveau Produit'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('products.index')" 
                      class="flex items-center justify-center w-10 h-10 bg-white rounded-xl shadow-sm border border-slate-100 text-slate-400 hover:text-indigo-600 transition-all no-underline">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h2 class="font-black text-2xl text-slate-800 tracking-tight leading-none">
                        {{ isEditing ? 'Mise à jour' : 'Nouveau produit' }}
                    </h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1">StockFlow Inventory Control</p>
                </div>
            </div>
        </template>

        <div class="py-12 bg-slate-50/50 min-h-screen">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="bg-white p-10 shadow-[0_20px_50px_rgba(0,0,0,0.02)] rounded-[2.5rem] border border-slate-100 space-y-8">
                    
                    <div class="grid grid-cols-1 gap-8">
                        
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Référence SKU</label>
                            <input v-model="form.sku" type="text" autofocus :readonly="isEditing"
                                   :class="[isEditing ? 'bg-slate-100 text-slate-400 cursor-not-allowed opacity-60' : 'bg-slate-50 text-indigo-600 focus:ring-2 focus:ring-indigo-500/20']"
                                   class="block w-full border-none rounded-2xl font-mono font-bold p-4 shadow-inner"
                                   placeholder="Code produit..." />
                            <div v-if="form.errors.sku" class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1">{{ form.errors.sku }}</div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Désignation</label>
                            <input v-model="form.name" type="text" 
                                   class="block w-full border-none bg-slate-50 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 font-bold text-slate-700 p-4 shadow-inner" />
                            <div v-if="form.errors.name" class="text-rose-500 text-[10px] font-black uppercase mt-1 ml-1">{{ form.errors.name }}</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Classification</label>
                                    <div class="relative">
                                    <select v-model="form.category_id" class="block w-full border-none bg-slate-50 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 font-bold text-slate-700 p-4 shadow-inner cursor-pointer appearance-none">
                                        <option value="">Sélectionner...</option>
                                        <template v-for="parent in categories" :key="parent.id">
                                            <optgroup v-if="parent.children && parent.children.length" :label="parent.name">
                                                <option :value="parent.id">{{ parent.name }} (Principal)</option>
                                                <option v-for="child in parent.children" :key="child.id" :value="child.id">
                                                    ↳ {{ child.name }}
                                                </option>
                                            </optgroup>
                                            <option v-else :value="parent.id">{{ parent.name }}</option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Prix (€)</label>
                                <input v-model="form.price" type="number" step="0.01" 
                                       class="block w-full border-none bg-slate-50 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 font-bold text-slate-700 p-4 shadow-inner" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-8 bg-indigo-50/40 rounded-[2.5rem] border border-indigo-100/50">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest ml-1">Quantité Réelle</label>
                                <input v-model="form.quantity" type="number" :disabled="isEditing" 
                                    class="block w-full border-none bg-white rounded-xl focus:ring-2 focus:ring-indigo-500/20 font-black text-slate-800 p-4 shadow-sm disabled:bg-slate-100/50" />
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest ml-1">Seuil critique</label>
                                <input v-model="form.min_stock" type="number" 
                                       class="block w-full border-none bg-white rounded-xl focus:ring-2 focus:ring-amber-500/20 font-black text-amber-600 p-4 shadow-sm" />
                            </div>
                        </div>

                        <div class="pt-6">
                            <button type="submit" :disabled="form.processing"
                                    class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-indigo-600 transition-all shadow-xl active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-2">
                                {{ isEditing ? 'Mettre à jour' : 'Enregistrer le produit' }}
                            </button>
                        </div>
                    </div>
                </form>

                <footer class="mt-16 py-8 text-center border-t border-slate-100">
                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">StockFlow Management • 2026</p>
                </footer>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Reset de l'apparence pour compatibilité cross-browser */
.custom-select {
    -webkit-appearance: none;    /* Safari, Chrome, Opera */
    -moz-appearance: none;       /* Firefox */
    appearance: none;            /* Standard */
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236366f1'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1.5rem center;
    background-size: 1.25rem;
}

/* Suppression des flèches sur les inputs number */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type=number] {
    -moz-appearance: textfield;
    appearance: textfield;
}
</style>