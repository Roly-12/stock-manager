<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    categories: Array
});

const form = useForm({
    name: '',
    parent_id: null
});

// Gestion de la modale de suppression personnalisée
const confirmId = ref(null);
const isDeleting = ref(false);

const openDeleteModal = (id) => {
    confirmId.value = id;
};

const closeDeleteModal = () => {
    confirmId.value = null;
};

const submit = () => {
    form.post(route('categories.store'), {
        onSuccess: () => form.reset()
    });
};

const executeDelete = () => {
    isDeleting.value = true;
    router.delete(route('categories.destroy', confirmId.value), {
        onSuccess: () => {
            closeDeleteModal();
            isDeleting.value = false;
        }
    });
};
</script>

<template>
    <Head title="Catégories" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-white rounded-2xl shadow-sm border border-slate-100 text-indigo-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-2xl text-slate-800 leading-tight">Gestion des Catégories</h2>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-[0.2em]">Organisation de l'inventaire</p>
                </div>
            </div>
        </template>

        <div class="py-12 bg-slate-50/50 min-h-screen relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8 mb-20">
                
                <div class="bg-white p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-200 rounded-[2.5rem] h-fit">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full"></span>
                        Ajouter
                    </h3>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom de catégorie</label>
                            <input v-model="form.name" type="text" 
                                   class="block w-full border-none bg-slate-50 rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-slate-700 p-4 shadow-inner" 
                                   placeholder="Ex: Électronique" />
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Catégorie Parente</label>
                            <select v-model="form.parent_id" 
                                    class="block w-full border-none bg-slate-50 rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-slate-700 p-4 shadow-inner">
                                <option :value="null">-- Aucune (Racine) --</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-indigo-600 transition-all shadow-lg active:scale-95">
                            Enregistrer
                        </button>
                    </form>
                </div>

                <div class="md:col-span-2 bg-white p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-200 rounded-[2.5rem]">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-8 flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-indigo-500 rounded-full"></span>
                        Structure actuelle
                    </h3>

                    <div v-if="categories.length === 0" class="text-slate-400 italic py-10 text-center font-bold uppercase text-[10px] tracking-widest border-2 border-dashed border-slate-100 rounded-3xl">
                        Aucune catégorie disponible.
                    </div>
                    
                    <div class="space-y-4">
                        <div v-for="parent in categories" :key="parent.id" class="group">
                            <div class="flex justify-between items-center p-5 bg-slate-50 rounded-2xl border border-slate-100 group-hover:bg-white group-hover:border-indigo-100 group-hover:shadow-md transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-xs">
                                        {{ parent.name.substring(0,2).toUpperCase() }}
                                    </div>
                                    <span class="font-black text-slate-700 tracking-tight">{{ parent.name }}</span>
                                </div>
                                <button @click="openDeleteModal(parent.id)" class="opacity-0 group-hover:opacity-100 p-2 text-rose-300 hover:text-rose-600 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>

                            <div v-if="parent.children && parent.children.length" class="mt-3 ml-12 space-y-2 border-l-2 border-slate-100 pl-6">
                                <div v-for="child in parent.children" :key="child.id" class="flex justify-between items-center p-3 text-sm text-slate-500 font-bold hover:text-indigo-600 transition-colors">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                                        {{ child.name }}
                                    </div>
                                    <button @click="openDeleteModal(child.id)" class="text-slate-300 hover:text-rose-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="confirmId" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all">
                <div class="bg-white rounded-[2.5rem] p-8 max-w-sm w-full shadow-2xl scale-in">
                    <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <h4 class="text-center font-black text-slate-800 text-xl mb-2">Supprimer ?</h4>
                    <p class="text-center text-slate-500 font-medium text-sm mb-8 leading-relaxed">Cette action est irréversible. Les produits resteront intacts mais orphelins.</p>
                    <div class="flex gap-3">
                        <button @click="closeDeleteModal" class="flex-1 py-4 rounded-xl font-black text-xs uppercase tracking-widest text-slate-400 bg-slate-50 hover:bg-slate-100 transition-colors">Annuler</button>
                        <button @click="executeDelete" :disabled="isDeleting" class="flex-1 py-4 rounded-xl font-black text-xs uppercase tracking-widest text-white bg-rose-500 hover:bg-rose-600 transition-all shadow-lg shadow-rose-100 active:scale-95">Confirmer</button>
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
                            <Link :href="route('products.index')" class="text-sm font-black text-slate-500 hover:text-indigo-600 transition-colors uppercase tracking-tight">Inventaire</Link>
                            <Link :href="route('categories.index')" class="text-sm font-black text-indigo-600 transition-colors uppercase tracking-tight underline underline-offset-8">Catégories</Link>
                        </nav>
                    </div>
                    <div class="mt-12 pt-8 border-t border-slate-50 flex flex-col md:flex-row justify-between items-center gap-4">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-tight">&copy; {{ new Date().getFullYear() }} StockFlow Management.</p>
                    </div>
                </div>
            </footer>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.scale-in {
    animation: scaleIn 0.2s ease-out forwards;
}
@keyframes scaleIn {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
</style>