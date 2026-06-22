<script setup>
import { onMounted, computed, ref, watch } from 'vue';
import { useDeals } from '../composables/useDeals';

const { deals, loading, error, fetchDeals, startEditing, deleteDeal } = useDeals();

const search = ref('');
const stageFilter = ref('');

const stages = [
    { value: '', label: 'Alle fases' },
    { value: 'lead', label: 'Lead' },
    { value: 'qualified', label: 'Gekwalificeerd' },
    { value: 'proposal', label: 'Voorstel' },
    { value: 'negotiation', label: 'Onderhandeling' },
    { value: 'won', label: 'Gewonnen' },
    { value: 'lost', label: 'Verloren' },
];

const stageClasses = {
    gray: 'bg-gray-100 text-gray-700',
    blue: 'bg-blue-100 text-blue-700',
    indigo: 'bg-indigo-100 text-indigo-700',
    amber: 'bg-amber-100 text-amber-700',
    green: 'bg-green-100 text-green-700',
    red: 'bg-red-100 text-red-700',
};

const formatValue = (value) =>
    new Intl.NumberFormat('nl-NL', {
        style: 'currency', currency: 'EUR', maximumFractionDigits: 0,
    }).format(value);

const totalValue = computed(() =>
    deals.value.reduce((sum, deal) => sum + Number(deal.value), 0)
);

const confirmDelete = (deal) => {
    if (confirm(`Deal "${deal.title}" verwijderen?`)) {
        deleteDeal(deal.id);
    }
};

const initialLoad = ref(true);

const applyFilters = () => {
    fetchDeals({ search: search.value, stage: stageFilter.value });
};

let timer = null;
watch(search, () => {
    clearTimeout(timer);
    timer = setTimeout(applyFilters, 300);
});

watch(stageFilter, applyFilters);

onMounted(async () => {
    await fetchDeals();
    initialLoad.value = false;
});
</script>

<template>
    <div v-if="initialLoad" class="text-gray-500">Deals laden…</div>

    <div v-else-if="error" class="rounded-lg bg-red-50 p-4 text-red-700">
        Er ging iets mis: {{ error }}
    </div>

    <div v-else>
        <div class="mb-4 flex flex-col gap-3 sm:flex-row">
            <input v-model="search" type="text" placeholder="Zoek op titel…"
                class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm" />
            <select v-model="stageFilter" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option v-for="s in stages" :key="s.value" :value="s.value">{{ s.label }}</option>
            </select>
        </div>
        <div class="mb-4 flex items-center justify-between">
            <span class="text-sm text-gray-500">{{ deals.length }} deals</span>
            <span class="text-sm font-medium text-gray-700">
                Totale waarde: {{ formatValue(totalValue) }}
            </span>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white" :class="{ 'opacity-60': loading }">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Deal
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Bedrijf</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Fase
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                            Waarde</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                            Acties</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="deal in deals" :key="deal.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ deal.title }}</div>
                            <div v-if="deal.contact" class="text-sm text-gray-500">{{ deal.contact.full_name }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ deal.company.name }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="stageClasses[deal.stage.color]">
                                {{ deal.stage.label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">
                            {{ formatValue(deal.value) }}
                        </td>
                        <td class="px-4 py-3 text-right text-sm whitespace-nowrap">
                            <button @click="startEditing(deal)"
                                class="font-medium text-blue-600 hover:text-blue-800">Bewerken</button>
                            <button @click="confirmDelete(deal)"
                                class="ml-3 font-medium text-red-600 hover:text-red-800">Verwijderen</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>