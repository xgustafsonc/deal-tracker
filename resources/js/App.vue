<script setup>
import DealForm from './components/DealForm.vue';
import DealList from './components/DealList.vue';
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto max-w-5xl px-6 py-5">
                <h1 class="text-xl font-semibold text-gray-900">Deal Tracker</h1>
                <p class="text-sm text-gray-500">Overzicht van alle deals</p>
            </div>
        </header>
        <main class="mx-auto max-w-5xl px-6 py-8 space-y-8">
            <DealForm />
            <DealList />
        </main>
    </div>
</template>