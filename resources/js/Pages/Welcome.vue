<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Gestion de Stock - Accueil" />

    <div class="min-h-screen bg-slate-50 dark:bg-zinc-950 font-sans antialiased text-gray-900 dark:text-gray-100">
        <nav class="sticky top-0 z-50 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md border-b border-gray-200 dark:border-zinc-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center space-x-2">
                        <div class="bg-indigo-600 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight">StockPro <span class="text-indigo-600">v1.0</span></span>
                    </div>

                    <div v-if="canLogin" class="flex items-center space-x-4">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')" 
                              class="text-sm font-semibold text-gray-700 hover:text-indigo-600 dark:text-gray-300 transition">
                            Accéder au Dashboard →
                        </Link>

                        <template v-else>
                            <Link :href="route('login')" class="text-sm font-semibold hover:text-indigo-600 transition">
                                Connexion
                            </Link>
                            <Link v-if="canRegister" :href="route('register')" 
                                  class="bg-indigo-600 text-white px-5 py-2 rounded-full text-sm font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:scale-105 transition-all">
                                Créer un compte
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-6xl font-black mb-4">
                    Gérez votre stock <br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">en temps réel.</span>
                </h1>
                <p class="text-gray-500 max-w-2xl mx-auto text-lg">
                    Une interface intuitive pour suivre vos produits, gérer vos alertes de stock bas et optimiser votre inventaire.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                <div class="bg-white dark:bg-zinc-900 p-8 rounded-3xl border border-gray-100 dark:border-zinc-800 shadow-sm">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Suivi Précis</h3>
                    <p class="text-gray-500 text-sm">Visualisez chaque mouvement de stock instantanément.</p>
                </div>

                <div class="bg-indigo-600 p-8 rounded-3xl text-white shadow-xl shadow-indigo-200 relative overflow-hidden group">
                    <div class="relative z-10">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Alertes Stock Bas</h3>
                        <p class="text-indigo-100 text-sm">Ne soyez jamais en rupture de stock grâce aux notifications.</p>
                    </div>
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform"></div>
                </div>

                <div class="bg-white dark:bg-zinc-900 p-8 rounded-3xl border border-gray-100 dark:border-zinc-800 shadow-sm">
                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Rapports PDF</h3>
                    <p class="text-gray-500 text-sm">Générez des inventaires complets en un seul clic.</p>
                </div>
            </div>
        </main>

        <footer class="py-10 text-center text-gray-400 text-sm border-t border-gray-100 dark:border-zinc-800">
            StockPro © 2026 - Propulsé par Laravel v{{ laravelVersion }}
        </footer>
    </div>
</template>

<style scoped>
/* Animation légère pour le texte */
h1 {
    animation: fadeInDown 0.8s ease-out;
}
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>