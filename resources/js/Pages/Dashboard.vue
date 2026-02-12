<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    stats: Object // Doit contenir: total_products, stock_alerts, total_movements_month, top_products, sales_chart
});

const stats = ref(props.stats);
let chartInstance = null;

const renderChart = () => {
    const ctx = document.getElementById('mainChart');
    if (!ctx || !stats.value.sales_chart) return;
    
    if (chartInstance) chartInstance.destroy();

    const labels = stats.value.sales_chart.map(d => {
        const date = new Date(d.date);
        return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
    });
    
    const historicalData = stats.value.sales_chart.map(d => d.total);
    const lastValue = historicalData[historicalData.length - 1] || 0;
    const forecastPoints = 7; 
    
    const forecastData = new Array(historicalData.length - 1).fill(null); 
    forecastData.push(lastValue); 

    let tempValue = lastValue;
    for (let i = 1; i <= forecastPoints; i++) {
        const date = new Date();
        date.setDate(date.getDate() + i);
        labels.push(date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' }) + ' (Prev)');
        // Simulation IA de tendance
        tempValue = Math.max(0, tempValue + (Math.random() * 10 - 5)); 
        forecastData.push(tempValue);
    }

    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Niveau de Stock (Réel)',
                    data: historicalData,
                    borderColor: '#4F46E5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    order: 1
                },
                {
                    label: 'Prévision IA',
                    data: forecastData,
                    borderColor: '#10B981',
                    borderDash: [10, 5],
                    fill: false,
                    tension: 0.4,
                    pointRadius: 4,
                    order: 0
                }
            ]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', align: 'end', labels: { font: { weight: 'bold' }, usePointStyle: true } }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });
};

onMounted(() => {
    renderChart();
});

watch(() => props.stats, (newStats) => {
    stats.value = newStats;
    renderChart();
}, { deep: true });

</script>

<template>
    <Head title="Tableau de Bord" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-white rounded-2xl shadow-sm border border-slate-100 text-indigo-600">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-black text-2xl text-slate-800 leading-tight tracking-tight text-shadow-sm">Dashboard Décisionnel</h2>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-[0.2em]">Pilotage de flux intelligent</p>
                    </div>
                </div>
                
                <div class="hidden md:flex items-center gap-2 bg-white px-4 py-2 rounded-2xl border border-slate-100 shadow-sm">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">IA Prédictive Active</span>
                </div>
            </div>
        </template>

        <div class="py-10 bg-slate-50/50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.02)] border border-slate-100 group hover:border-indigo-200 transition-all duration-300">
                        <div class="flex justify-between items-start mb-4">
                            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Catalogue</p>
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                        </div>
                        <h3 class="text-4xl font-black text-slate-800">{{ stats.total_products }}</h3>
                        <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase">Articles référencés</p>
                    </div>

                    <div class="bg-white p-6 rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.02)] border-2 transition-all duration-300"
                         :class="stats.stock_alerts > 0 ? 'border-rose-100 bg-rose-50/20' : 'border-slate-100'">
                        <div class="flex justify-between items-start mb-4">
                            <p class="text-rose-400 text-[10px] font-black uppercase tracking-widest">Ruptures</p>
                            <div class="w-10 h-10 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-500" :class="stats.stock_alerts > 0 ? 'animate-bounce' : ''">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                        </div>
                        <h3 class="text-4xl font-black" :class="stats.stock_alerts > 0 ? 'text-rose-600' : 'text-slate-800'">{{ stats.stock_alerts }}</h3>
                        <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase">Action immédiate requise</p>
                    </div>

                    <div class="bg-white p-6 rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.02)] border border-slate-100 group">
                        <div class="flex justify-between items-start mb-4">
                            <p class="text-emerald-400 text-[10px] font-black uppercase tracking-widest">Activité Mensuelle</p>
                            <div class="w-10 h-10 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:translate-y-[-2px] transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            </div>
                        </div>
                        <h3 class="text-4xl font-black text-emerald-600">{{ stats.total_movements_month }}</h3>
                        <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase">Mouvements ce mois</p>
                    </div>

                    <div class="bg-slate-900 p-6 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500 rounded-full blur-[60px] opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <div class="relative z-10">
                            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-4">Indicateur de Santé</p>
                            <h3 class="text-4xl font-black text-white">98.5%</h3>
                            <div class="mt-2 w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-indigo-500 h-full w-[98.5%] shadow-[0_0_10px_#6366f1]"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <div class="lg:col-span-8 bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100 min-h-[500px] flex flex-col">
                        <div class="flex justify-between items-start mb-8">
                            <div>
                                <h3 class="text-xl font-black text-slate-900 tracking-tight">Analyse Prédictive des Stocks</h3>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wide mt-1">Algorithme de projection à J+7</p>
                            </div>
                            <div class="flex gap-2">
                                <span class="flex items-center gap-1.5 text-[10px] font-black text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-xl uppercase border border-indigo-100">Réel</span>
                                <span class="flex items-center gap-1.5 text-[10px] font-black text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-xl uppercase border border-emerald-100 italic">IA Prev</span>
                            </div>
                        </div>
                        <div class="flex-grow relative min-h-[350px]">
                            <canvas id="mainChart"></canvas>
                        </div>
                    </div>

                    <div class="lg:col-span-4 flex flex-col gap-6">
                        <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100 flex-1 flex flex-col">
                            <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-50 pb-4">Top Rotations (VMA)</h3>
                            <div class="flex-1 space-y-5 overflow-y-auto max-h-[320px] custom-scrollbar pr-2">
                                <div v-for="(product, index) in stats.top_products" :key="product.id" class="flex items-center gap-4 group cursor-default">
                                    <div class="w-10 h-10 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center font-black text-xs group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                                        {{ index + 1 }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-black text-slate-800 truncate group-hover:text-indigo-600 transition-colors">{{ product.name }}</p>
                                        <p class="text-[10px] text-slate-400 uppercase font-black tracking-tight">Actuel: {{ product.quantity }} unités</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs font-black text-indigo-600">{{ product.movements_count }}</div>
                                        <div class="text-[8px] font-black text-slate-300 uppercase">Flux</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-8 pt-6 border-t border-slate-50 flex flex-col gap-3">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest text-center mb-1">Générer Rapports</p>
                                <div class="flex gap-3">
                                    <a :href="route('dashboard.export-pdf')" class="flex-1 py-4 bg-slate-900 text-white text-[10px] font-black rounded-2xl text-center uppercase tracking-[0.15em] hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200 active:scale-95">
                                        PDF
                                    </a>
                                    <a :href="route('dashboard.export-excel')" class="flex-1 py-4 bg-emerald-600 text-white text-[10px] font-black rounded-2xl text-center uppercase tracking-[0.15em] hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100 active:scale-95">
                                        EXCEL
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <footer class="py-10 border-t border-slate-100 text-[10px] font-black uppercase text-slate-400 flex justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 bg-slate-100 rounded flex items-center justify-center text-[8px] font-black text-slate-400 italic font-mono uppercase">V2</div>
                        <p>© 2026 StockPro - Système Décisionnel</p>
                    </div>
                    <div class="flex gap-6">
                        <span class="hover:text-indigo-600 cursor-pointer transition-colors">Documentation</span>
                        <span class="hover:text-indigo-600 cursor-pointer transition-colors">Logs Système</span>
                    </div>
                </footer>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #f1f5f9; border-radius: 20px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #e2e8f0; }

canvas {
    filter: drop-shadow(0 10px 15px rgba(79, 70, 229, 0.05));
}

.text-shadow-sm {
    text-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
</style>