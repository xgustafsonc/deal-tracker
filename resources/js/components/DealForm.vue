<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue';
import { useDeals } from '../composables/useDeals';

const { createDeal, updateDeal, editingDeal, cancelEditing } = useDeals();

const companies = ref([]);
const stages = ref([]);
const errors = ref({});
const saving = ref(false);
const success = ref(false);

const emptyForm = () => ({
    company_id: '', contact_id: '', title: '', value: '',
    stage: '', expected_close_date: '', notes: '',
});

const form = reactive(emptyForm());
const isEditing = computed(() => editingDeal.value !== null);

const availableContacts = computed(() => {
    const company = companies.value.find((c) => c.id === Number(form.company_id));
    return company ? company.contacts : [];
});

// Als er een deal ter bewerking wordt gekozen: vul het formulier.
// Let op: de leesvorm is genest (deal.company.id), de schrijfvorm is plat (company_id).
watch(editingDeal, (deal) => {
    errors.value = {};
    success.value = false;
    if (deal) {
        form.company_id = deal.company.id;
        form.contact_id = deal.contact?.id ?? '';
        form.title = deal.title;
        form.value = deal.value;
        form.stage = deal.stage.value;
        form.expected_close_date = deal.expected_close_date ?? '';
        form.notes = deal.notes ?? '';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
        Object.assign(form, emptyForm());
    }
});

onMounted(async () => {
    const [companiesRes, stagesRes] = await Promise.all([
        window.axios.get('/api/companies'),
        window.axios.get('/api/deal-stages'),
    ]);
    companies.value = companiesRes.data.data;
    stages.value = stagesRes.data;
});

const submit = async () => {
    saving.value = true;
    errors.value = {};
    success.value = false;
    const payload = {
        ...form,
        contact_id: form.contact_id || null,
        expected_close_date: form.expected_close_date || null,
        notes: form.notes || null,
    };
    try {
        if (isEditing.value) {
            await updateDeal(editingDeal.value.id, payload);
            cancelEditing(); // verlaat edit-modus → watcher reset het formulier
        } else {
            await createDeal(payload);
            Object.assign(form, emptyForm());
        }
        success.value = true;
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors;
        } else {
            errors.value = { general: ['Er ging iets mis. Probeer opnieuw.'] };
        }
    } finally {
        saving.value = false;
    }
};

const cancel = () => {
    cancelEditing();
    errors.value = {};
};
</script>

<template>
    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">
            {{ isEditing ? 'Deal bewerken' : 'Nieuwe deal' }}
        </h2>

        <div v-if="success" class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700">
            {{ isEditing ? 'Deal bijgewerkt ✓' : 'Deal aangemaakt ✓' }}
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Bedrijf *</label>
                <select v-model="form.company_id" @change="form.contact_id = ''"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">— Kies een bedrijf —</option>
                    <option v-for="c in companies" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
                <p v-if="errors.company_id" class="mt-1 text-sm text-red-600">{{ errors.company_id[0] }}</p>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Contact</label>
                <select v-model="form.contact_id" :disabled="!form.company_id"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm disabled:bg-gray-50 disabled:text-gray-400">
                    <option value="">— Geen —</option>
                    <option v-for="contact in availableContacts" :key="contact.id" :value="contact.id">
                        {{ contact.full_name }}
                    </option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">Titel *</label>
                <input v-model="form.title" type="text"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title[0] }}</p>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Waarde (€) *</label>
                <input v-model="form.value" type="number" min="0"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
                <p v-if="errors.value" class="mt-1 text-sm text-red-600">{{ errors.value[0] }}</p>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Fase *</label>
                <select v-model="form.stage" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">— Kies een fase —</option>
                    <option v-for="s in stages" :key="s.value" :value="s.value">{{ s.label }}</option>
                </select>
                <p v-if="errors.stage" class="mt-1 text-sm text-red-600">{{ errors.stage[0] }}</p>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Verwachte sluitdatum</label>
                <input v-model="form.expected_close_date" type="date"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" />
            </div>

            <div class="sm:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">Notities</label>
                <textarea v-model="form.notes" rows="2"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
            </div>
        </div>

        <div class="mt-5 flex items-center gap-3">
            <button @click="submit" :disabled="saving"
                class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 disabled:opacity-50">
                {{ saving ? 'Bezig…' : (isEditing ? 'Wijzigingen opslaan' : 'Deal opslaan') }}
            </button>
            <button v-if="isEditing" @click="cancel" type="button"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Annuleren
            </button>
            <span v-if="errors.general" class="text-sm text-red-600">{{ errors.general[0] }}</span>
        </div>
    </div>
</template>