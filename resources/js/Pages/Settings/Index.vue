<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({ settings: Object });

const form = useForm({
    app_name: props.settings.app_name ?? 'Stock Manager Pro',
    currency: props.settings.currency ?? '€',
    alert_email: props.settings.alert_email ?? '',
});

const submit = () => form.post(route('settings.update'), {
    preserveScroll: true,
    onSuccess: () => {
        // Optionnel : ajouter une notification de succès ici
    }
});
</script>

<template>
    <Head title="Paramètres Système" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <div class="bg-slate-800 p-2.5 rounded-2xl shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h2 class="font-black text-2xl text-slate-800 tracking-tight">Configuration Système</h2>
            </div>
        </template>

        <div class="py-12 bg-slate-50 min-h-screen">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-8 md:p-12">
                        <form @submit.prevent="submit" class="space-y-8">
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                                <div>
                                    <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-1">Identité</h3>
                                    <p class="text-xs text-slate-400">Nom public de votre instance.</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-2 ml-1">Nom de l'Entrepôt</label>
                                    <input v-model="form.app_name" type="text" 
                                        class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold text-slate-700" />
                                </div>
                            </div>

                            <hr class="border-slate-50" />

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                                <div>
                                    <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-1">Finances</h3>
                                    <p class="text-xs text-slate-400">Paramètres monétaires globaux.</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-2 ml-1">Devise par défaut</label>
                                    <select v-model="form.currency" 
                                        class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold text-slate-700 cursor-pointer">
                                        <option value="€">Euro (€)</option>
                                        <option value="$">Dollar ($)</option>
                                        <option value="CHF">Franc Suisse (CHF)</option>
                                        <option value="FCFA">Franc CFA (FCFA)</option>
                                    </select>
                                </div>
                            </div>

                            <hr class="border-slate-50" />

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                                <div>
                                    <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-1">Alertes</h3>
                                    <p class="text-xs text-slate-400">Destinataire des alertes de stock.</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-2 ml-1">Email de rupture de stock</label>
                                    <input v-model="form.alert_email" type="email" placeholder="admin@exemple.com"
                                        class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold text-slate-700" />
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" :disabled="form.processing"
                                    class="w-full bg-slate-900 text-white py-5 rounded-[1.5rem] font-black text-sm uppercase tracking-widest hover:bg-indigo-600 hover:shadow-xl hover:shadow-indigo-200 transition-all flex items-center justify-center gap-3 disabled:opacity-50">
                                    <span v-if="form.processing" class="animate-spin text-lg">◌</span>
                                    Enregistrer les réglages
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <footer class="mt-20 py-10 border-t border-slate-200">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-6 text-[11px] text-slate-400 font-black uppercase tracking-[0.2em]">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center border border-slate-100 shadow-sm text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <p>© 2026 {{ form.app_name }} • Configuration sécurisée</p>
                        </div>
                        <div class="flex gap-8">
                            <span class="text-slate-500/50 underline decoration-slate-200 underline-offset-4">Documentation</span>
                            <span class="hover:text-slate-600 transition-colors cursor-help">État du serveur</span>
                        </div>
                    </div>
                </footer>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.scale-in-center {
    animation: scale-in-center 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94) both;
}
@keyframes scale-in-center {
    0% { transform: scale(0.9); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
</style>