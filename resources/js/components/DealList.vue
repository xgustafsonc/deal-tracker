<script setup>
import { ref, onMounted, computed } from 'vue';

const deals = ref([]);
const loading = ref(true);
const error = ref(null);

// Let op: volledige class-strings, GEEN `bg-${color}-100`.
// Tailwind scant je broncode statisch — dynamisch opgebouwde
// classnames worden niet gevonden en krijgen dus geen styling.
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
        style: 'currency',
        currency: 'EUR',
        maximumFractionDigits: 0,
    }).format(value);

const totalValue = computed(() =>
    deals.value.reduce((sum, deal) => sum + Number(deal.value), 0)
);

onMounted(async () => {
    try {
        const response = await fetch('/api/deals', {
            headers: { Accept: 'application/json' },
        });
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const json = await response.json();
        deals.value = json.data;
    } catch (e) {
        error.value = e.message;
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div v-if="loading" class="text-gray-500">Deals laden…</div>

    <div v-else-if="error" class="rounded-lg bg-red-50 p-4 text-red-700">
        Er ging iets mis: {{ error }}
    </div>

    <div v-else>
        <div class="mb-4 flex items-center justify-between">
            <span class="text-sm text-gray-500">{{ deals.length }} deals</span>
            <span class="text-sm font-medium text-gray-700">
                Totale waarde: {{ formatValue(totalValue) }}
            </span>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
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
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>