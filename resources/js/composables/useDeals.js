import { ref } from "vue";

const deals = ref([]);
const loading = ref(false);
const error = ref(null);
const editingDeal = ref(null); // null = create-modus, deal-object = edit-modus

export function useDeals() {
    const fetchDeals = async () => {
        loading.value = true;
        error.value = null;
        try {
            const { data } = await window.axios.get("/api/deals");
            deals.value = data.data;
        } catch (e) {
            error.value = e.message;
        } finally {
            loading.value = false;
        }
    };

    const createDeal = async (payload) => {
        const { data } = await window.axios.post("/api/deals", payload);
        deals.value.unshift(data.data);
        return data.data;
    };

    const updateDeal = async (id, payload) => {
        const { data } = await window.axios.put(`/api/deals/${id}`, payload);
        const index = deals.value.findIndex((d) => d.id === id);
        if (index !== -1) deals.value[index] = data.data; // vervang in de lijst
        return data.data;
    };

    const deleteDeal = async (id) => {
        await window.axios.delete(`/api/deals/${id}`);
        deals.value = deals.value.filter((d) => d.id !== id); // weg uit de lijst
    };

    const startEditing = (deal) => {
        editingDeal.value = deal;
    };
    const cancelEditing = () => {
        editingDeal.value = null;
    };

    return {
        deals,
        loading,
        error,
        editingDeal,
        fetchDeals,
        createDeal,
        updateDeal,
        deleteDeal,
        startEditing,
        cancelEditing,
    };
}
