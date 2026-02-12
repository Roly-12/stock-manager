<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import InventoryScanner from '@/Components/InventoryScanner.vue';
import axios from 'axios';

const props = defineProps({ products: Array });

// On initialise les lignes d'ajustement
const adjustments = ref(props.products.map(p => ({
    product_id: p.id,
    name: p.name,
    sku: p.sku,
    theoretical: p.current_stock,
    actual_stock: p.current_stock // Par défaut, identique
})));

const form = useForm({
    adjustments: []
});

const submit = () => {
    form.adjustments = adjustments.value;
    form.post(route('inventory.store'));
};

// Calcul du nombre d'écarts détectés
const discrepanciesCount = computed(() => {
    return adjustments.value.filter(a => a.theoretical !== a.actual_stock).length;
});

// Fonction pour supprimer
const deleteProduct = (id) => {
    if (confirm('Supprimer ce produit de l\'inventaire ?')) {
        router.delete(route('inventory.destroy', id));
    }
};
const showScanner = ref(false)

const scannedProduct = ref(null); // Pour stocker les détails du produit scanné

// Fonction pour scanner
const handleScanResult = async (barcode) => {
    showScanner.value = false;
    try {
        const response = await axios.get(route('inventory.scan', barcode));
        scannedProduct.value = response.data;
        // Optionnel : on peut aussi l'ajouter directement à la liste
        alert("Produit trouvé : " + scannedProduct.value.name);
    } catch (error) {
        alert("Code-barres inconnu");
    }
};
</script>

<template>
    <Head title="Inventaire Physique" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800">Ajustement d'Inventaire</h2>
                <div v-if="discrepanciesCount > 0" class="text-sm bg-amber-100 text-amber-700 px-3 py-1 rounded-full font-bold">
                    {{ discrepanciesCount }} écart(s) détecté(s)
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase">Produit</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase">Stock Théorique</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase w-48">Stock Réel (Compté)</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase">Écart</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="(adj, index) in adjustments" :key="adj.product_id">
                                <td class="p-4">
                                    <div class="font-bold text-gray-900">{{ adj.name }}</div>
                                    <div class="text-xs text-gray-400 font-mono">{{ adj.sku }}</div>
                                </td>
                                <td class="p-4 text-gray-600 font-mono italic">
                                    {{ adj.theoretical }}
                                </td>
                                <td class="p-4">
                                    <input v-model.number="adj.actual_stock" type="number" 
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500"
                                           :class="{'border-amber-500 bg-amber-50': adj.theoretical !== adj.actual_stock}" />
                                </td>
                                <td class="p-4">
                                    <span v-if="adj.actual_stock - adj.theoretical > 0" class="text-green-600 font-bold">
                                        +{{ adj.actual_stock - adj.theoretical }}
                                    </span>
                                    <span v-else-if="adj.actual_stock - adj.theoretical < 0" class="text-red-600 font-bold">
                                        {{ adj.actual_stock - adj.theoretical }}
                                    </span>
                                    <span v-else class="text-gray-300">--</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-900 transition p-2 bg-red-50 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <button @click="showScanner = true" class="flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-xl hover:bg-indigo-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Scanner un produit
                    </button>

                    <div v-if="showScanner" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                        <div class="max-w-md w-full p-4">
                            <InventoryScanner @result="handleScanResult" @close="showScanner = false" />
                        </div>
                    </div>

                    <div v-if="scannedProduct" class="fixed bottom-10 right-10 bg-white shadow-2xl p-6 rounded-2xl border-t-4 border-indigo-500 max-w-sm animate-bounce-short">
                        <h4 class="font-bold text-lg">{{ scannedProduct.name }}</h4>
                        <p class="text-sm text-gray-500">SKU: {{ scannedProduct.sku }}</p>
                        <p class="font-black text-indigo-600 mt-2">Stock actuel: {{ scannedProduct.quantity }}</p>
                        <button @click="scannedProduct = null" class="mt-4 text-xs uppercase font-bold text-gray-400 hover:text-gray-600">Fermer</button>
                    </div>

                    <div class="p-6 bg-gray-50 border-t flex justify-end">
                        <button @click="submit" :disabled="form.processing"
                                class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-700 shadow-lg">
                            Valider l'Inventaire & Corriger le Stock
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>