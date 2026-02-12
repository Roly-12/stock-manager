<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Création de compte" />

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl mb-4 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Rejoindre l'équipe</h1>
            <p class="text-slate-500 mt-2">Créez votre accès à l'inventaire en quelques secondes.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="name" value="Nom complet" class="text-slate-700 font-semibold" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full !rounded-xl border-gray-200 focus:ring-emerald-500 shadow-sm transition-all"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Jean Dupont"
                />
                <InputError class="mt-1" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email professionnel" class="text-slate-700 font-semibold" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full !rounded-xl border-gray-200 focus:ring-emerald-500 shadow-sm transition-all"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="nom@entreprise.com"
                />
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <InputLabel for="password" value="Mot de passe" class="text-slate-700 font-semibold" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full !rounded-xl border-gray-200 focus:ring-emerald-500 shadow-sm transition-all"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                </div>
                <div>
                    <InputLabel for="password_confirmation" value="Confirmation" class="text-slate-700 font-semibold" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full !rounded-xl border-gray-200 focus:ring-emerald-500 shadow-sm transition-all"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                </div>
            </div>
            <InputError class="mt-1" :message="form.errors.password" />
            <InputError class="mt-1" :message="form.errors.password_confirmation" />

            <div class="pt-4 flex flex-col gap-4 items-center">
                <PrimaryButton 
                    class="w-full justify-center py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-md shadow-emerald-100 transition-all active:scale-95 border-none" 
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }" 
                    :disabled="form.processing"
                >
                    <span v-if="!form.processing">Créer mon compte</span>
                    <span v-else>Création en cours...</span>
                </PrimaryButton>

                <p class="text-sm text-slate-500">
                    Déjà inscrit ? 
                    <Link :href="route('login')" class="font-bold text-emerald-600 hover:underline">
                        Se connecter
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>