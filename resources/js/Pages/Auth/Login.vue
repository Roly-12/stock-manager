<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Connexion" />

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl mb-4 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Accès à l'Inventaire</h1>
            <p class="text-slate-500 mt-2">Heureux de vous revoir !</p>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600 bg-green-50 p-3 rounded-lg border border-green-100">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Adresse Email" class="text-slate-700 font-semibold" />
                <div class="mt-1 relative group">
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full !rounded-xl border-gray-200 focus:ring-indigo-500 shadow-sm transition-all"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="votre@email.com"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <InputLabel for="password" value="Mot de passe" class="text-slate-700 font-semibold" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.direct', { email: form.email })"
                        class="text-xs font-medium text-indigo-600 hover:text-indigo-500 transition-colors"
                    >
                        Oublié ?
                    </Link>
                </div>
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full !rounded-xl border-gray-200 focus:ring-indigo-500 shadow-sm transition-all"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <div class="flex items-center">
                <Checkbox name="remember" v-model:checked="form.remember" class="rounded text-indigo-600 focus:ring-indigo-500" />
                <span class="ms-2 text-sm text-slate-600">Rester connecté</span>
            </div>

            <div class="pt-2">
                <PrimaryButton 
                    class="w-full justify-center py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-md shadow-indigo-200 transition-all active:scale-95" 
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }" 
                    :disabled="form.processing"
                >
                    <span v-if="!form.processing">Se connecter</span>
                    <span v-else>Connexion en cours...</span>
                </PrimaryButton>
            </div>

            <div class="text-center mt-6">
                <p class="text-sm text-slate-500">
                    Pas encore de compte ? 
                    <Link :href="route('register')" class="font-bold text-indigo-600 hover:underline">
                        Créer un accès
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>