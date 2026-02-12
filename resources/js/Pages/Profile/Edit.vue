<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
});
</script>

<template>
    <Head title="Mon Profil" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-white rounded-2xl shadow-sm border border-slate-100 text-indigo-600">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-black text-2xl text-slate-800 leading-tight">Configuration du Compte</h2>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-[0.2em]">Identité et sécurité de l'accès</p>
                    </div>
                </div>
                
                <div class="inline-flex items-center gap-3 px-5 py-2.5 bg-white border border-slate-200 rounded-2xl shadow-sm self-start md:self-center">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-600"></span>
                    </span>
                    <span class="text-[11px] font-black text-slate-700 uppercase tracking-widest">
                        Rôle : {{ $page.props.auth.user.role }}
                    </span>
                </div>
            </div>
        </template>

        <div class="py-12 bg-slate-50/50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-1">
                        <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Informations Personnelles</h3>
                        <p class="mt-2 text-sm text-slate-500 font-medium leading-relaxed">
                            Mettez à jour les informations de profil de votre compte et votre adresse e-mail.
                        </p>
                    </div>
                    <div class="lg:col-span-2">
                        <div class="bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-200 rounded-[2.5rem] p-8 sm:p-10 transition-all hover:shadow-md">
                            <UpdateProfileInformationForm
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                                class="max-w-xl"
                            />
                        </div>
                    </div>
                </div>

                <hr class="border-slate-200/60" />

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-1">
                        <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Sécurité</h3>
                        <p class="mt-2 text-sm text-slate-500 font-medium leading-relaxed">
                            Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester en sécurité.
                        </p>
                    </div>
                    <div class="lg:col-span-2">
                        <div class="bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-200 rounded-[2.5rem] p-8 sm:p-10 transition-all hover:shadow-md">
                            <UpdatePasswordForm class="max-w-xl" />
                        </div>
                    </div>
                </div>

                <hr class="border-slate-200/60" />

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-1">
                        <h3 class="text-lg font-black text-rose-600 uppercase tracking-tight">Zone de Danger</h3>
                        <p class="mt-2 text-sm text-rose-400 font-medium leading-relaxed">
                            Suppression définitive de votre compte et de toutes les données associées.
                        </p>
                    </div>
                    <div class="lg:col-span-2">
                        <div class="bg-rose-50/20 border-2 border-dashed border-rose-100 rounded-[2.5rem] p-8 sm:p-10 transition-all">
                            <DeleteUserForm class="max-w-xl" />
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <footer class="py-12 bg-white border-t border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="flex justify-center items-center gap-2 mb-4">
                    <div class="w-6 h-6 bg-slate-200 rounded flex items-center justify-center text-[10px] font-black">S</div>
                    <span class="text-sm font-black tracking-tighter text-slate-400 uppercase">StockFlow <span class="text-slate-300 font-bold">Secure</span></span>
                </div>
                <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">
                    &copy; {{ new Date().getFullYear() }} — Session Sécurisée
                </p>
            </div>
        </footer>
    </AuthenticatedLayout>
</template>