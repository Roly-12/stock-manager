<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ users: Array });

// État pour la modale de suppression
const userToDelete = ref(null);

const changeRole = (user, newRole) => {
    router.patch(route('users.updateRole', user.id), { role: newRole });
};

const confirmDeletion = (user) => {
    userToDelete.value = user;
};

const cancelDeletion = () => {
    userToDelete.value = null;
};

const submitDelete = () => {
    router.delete(route('users.destroy', userToDelete.value.id), {
        onSuccess: () => cancelDeletion(),
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Gestion de l'équipe" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <div class="bg-indigo-600 p-2.5 rounded-2xl shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h2 class="font-black text-2xl text-slate-800 tracking-tight">Utilisateurs & Permissions</h2>
            </div>
        </template>

        <div class="py-12 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-400">
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest">Membre</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest">Rôle Actuel</th>
                                <th class="p-6 text-[10px] font-black uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50/50 transition-colors group">
                                <td class="p-6">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold uppercase">
                                            {{ user.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800">{{ user.name }}</div>
                                            <div class="text-xs text-slate-400 font-medium">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-6">
                                    <span :class="{
                                        'bg-purple-50 text-purple-600 border-purple-100': user.role === 'admin',
                                        'bg-blue-50 text-blue-600 border-blue-100': user.role === 'gestionnaire',
                                        'bg-emerald-50 text-emerald-600 border-emerald-100': user.role === 'observateur'
                                    }" class="px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider border">
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="p-6 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <select @change="changeRole(user, $event.target.value)" 
                                                class="text-xs font-bold bg-slate-50 border-slate-200 rounded-xl focus:ring-indigo-500 text-slate-600 cursor-pointer">
                                            <option value="admin" :selected="user.role === 'admin'">Accès Admin</option>
                                            <option value="gestionnaire" :selected="user.role === 'gestionnaire'">Accès Gestionnaire</option>
                                            <option value="observateur" :selected="user.role === 'observateur'">Accès Observateur</option>
                                        </select>

                                        <button @click="confirmDeletion(user)" 
                                                class="p-2 text-slate-300 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" 
                                                title="Supprimer l'utilisateur">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
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
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <p>© 2026 StockPro • Contrôle d'accès sécurisé</p>
                        </div>
                        <div class="flex gap-8">
                            <span class="text-indigo-500/50 underline decoration-indigo-200 underline-offset-4">Journaux d'audit</span>
                            <span class="hover:text-indigo-600 transition-colors cursor-help">Centre de sécurité</span>
                        </div>
                    </div>
                </footer>

            </div>
        </div>

        <div v-if="userToDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <div class="bg-white rounded-[2rem] shadow-2xl max-w-sm w-full p-8 transition-all transform scale-in-center">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-50 mb-6">
                        <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-black text-slate-800 mb-2">Supprimer l'accès ?</h3>
                    <p class="text-sm text-slate-500 mb-8">
                        Êtes-vous sûr de vouloir retirer <span class="font-bold text-slate-800">{{ userToDelete.name }}</span> de l'équipe ? Cette action est irréversible.
                    </p>

                    <div class="flex flex-col gap-3">
                        <button @click="submitDelete" 
                                class="w-full py-4 bg-red-600 hover:bg-red-700 text-white rounded-2xl font-bold text-sm transition-all shadow-lg shadow-red-200">
                            Confirmer la suppression
                        </button>
                        <button @click="cancelDeletion" 
                                class="w-full py-4 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold text-sm transition-all">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>