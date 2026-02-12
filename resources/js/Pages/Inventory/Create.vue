<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import InventoryScanner from '@/Components/InventoryScanner.vue';
import axios from 'axios';

// États pour la gestion de l'affichage
const showScanner = ref(false);
const scannedProduct = ref(null);
const isLoading = ref(false);
const error = ref(null);

// Fonction de recherche via l'API
const handleScanResult = async (barcode) => {
    showScanner.value = false;
    isLoading.value = true;
    error.value = null;

    try {
        const response = await axios.get(route('inventory.scan', barcode));
        scannedProduct.value = response.data;
    } catch (err) {
        error.value = "Produit introuvable ou erreur réseau.";
        setTimeout(() => error.value = null, 4000); // Efface l'erreur après 4s
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <Head title="Scan de Produit" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Consultation par Scan</h2>
        </template>

        <div class="py-12">
            <div class="max-w-md mx-auto sm:px-6 lg:px-8 text-center">
                
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <div class="mb-6 flex justify-center">
                        <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Prêt à scanner ?</h3>
                    <p class="text-sm text-gray-500 mb-8">Utilisez votre caméra pour scanner un QR Code ou un Code-barres et obtenir les détails du produit.</p>

                    <button @click="showScanner = true" 
                            class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-bold text-lg hover:bg-indigo-700 transition transform active:scale-95 shadow-lg shadow-indigo-100">
                        Ouvrir le Scanner
                    </button>
                    
                    <div v-if="isLoading" class="mt-4 text-indigo-600 font-medium animate-pulse">Recherche du produit...</div>
                    <div v-if="error" class="mt-4 text-red-500 font-medium">{{ error }}</div>
                </div>

                <div v-if="scannedProduct" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden animate-in zoom-in duration-200">
                        <div class="p-6 text-center text-white" :class="scannedProduct.quantity <= scannedProduct.min_stock ? 'bg-red-500' : 'bg-green-600'">
                            <p class="text-xs uppercase font-black opacity-70 mb-1">Informations Produit</p>
                            <h2 class="text-2xl font-black">{{ scannedProduct.name }}</h2>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-400 font-bold uppercase">Référence</span>
                                <span class="font-mono font-black text-gray-700">{{ scannedProduct.sku }}</span>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-2xl">
                                <div class="flex justify-between items-end">
                                    <div>
                                        <p class="text-[10px] text-gray-400 font-black uppercase">Stock en rayon</p>
                                        <p class="text-3xl font-black" :class="scannedProduct.quantity <= scannedProduct.min_stock ? 'text-red-600' : 'text-gray-800'">
                                            {{ scannedProduct.quantity }} units
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] text-gray-400 font-black uppercase">Prix</p>
                                        <p class="text-xl font-black text-gray-800">{{ scannedProduct.price }} {{ $page.props.app?.currency || '€' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div v-if="scannedProduct.quantity <= scannedProduct.min_stock" class="bg-red-50 p-3 rounded-xl border border-red-100 flex items-center gap-2 text-red-700 text-xs font-bold">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                Stock critique ! Réapprovisionnement nécessaire.
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 flex gap-2">
                            <button @click="scannedProduct = null" class="w-full py-3 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-100 transition">
                                Fermer
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="showScanner" class="fixed inset-0 z-[60] flex items-center justify-center bg-black p-4">
                    <div class="relative w-full max-w-lg h-[80vh]">
                        <button @click="showScanner = false" class="absolute -top-12 right-0 text-white font-bold flex items-center gap-2">
                            Fermer [X]
                        </button>
                        <InventoryScanner @result="handleScanResult" />
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>