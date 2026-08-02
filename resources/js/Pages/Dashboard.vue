<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const clients = ref([]);
const checks = ref([]);
const selectedClientId = ref('');
const selectedCheckIds = ref([]);
const results = ref([]);
const loadingOptions = ref(false);
const executing = ref(false);
const error = ref(null);
const success = ref(null);

async function loadOptions() {
    loadingOptions.value = true;
    error.value = null;

    try {
        const [clientsResponse, checksResponse] = await Promise.all([
            axios.get(route('api.clients')),
            axios.get(route('api.checks')),
        ]);

        clients.value = clientsResponse.data.data.clients;

        checks.value = checksResponse.data.data.checks;

        selectedClientId.value = clients.value[0]?.id ?? '';
        selectedCheckIds.value = checks.value
            .filter((check) => check.active)
            .map((check) => check.id);
    } catch (exception) {
        error.value = exception.response?.data?.message
            ?? 'Não foi possível carregar clientes e checagens.';
    } finally {
        loadingOptions.value = false;
    }
}

async function executeChecks() {
    if (!selectedClientId.value) {
        error.value = 'Selecione um cliente.';
        return;
    }

    if (selectedCheckIds.value.length === 0) {
        error.value = 'Selecione pelo menos uma checagem.';
        return;
    }

    executing.value = true;
    error.value = null;
    success.value = null;
    results.value = [];

    try {
        const response = await axios.post(route('api.checks.execute'), {
            client_id: selectedClientId.value,
            check_ids: selectedCheckIds.value,
        });

        success.value = response.data.message;
        results.value = response.data.data ?? [];
    } catch (exception) {
        error.value = exception.response?.data?.message
            ?? 'Falha ao executar as checagens.';
    } finally {
        executing.value = false;
    }
}

onMounted(loadOptions);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Executar verificações
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <p v-if="loadingOptions" class="text-sm text-gray-600 dark:text-gray-300">
                        Carregando opções...
                    </p>

                    <template v-else>
                        <div class="space-y-5">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Cliente
                                </label>

                                <select
                                    v-model="selectedClientId"
                                    class="w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                >
                                    <option value="" disabled>
                                        Selecione um cliente
                                    </option>

                                    <option
                                        v-for="client in clients"
                                        :key="client.id"
                                        :value="client.id"
                                    >
                                        {{ client.id }} - {{ client.user }} - {{ client.db_name }} - {{ client.host }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Checagens
                                </p>

                                <div class="grid gap-2 md:grid-cols-2">
                                    <label
                                        v-for="check in checks.filter((item) => item.active)"
                                        :key="check.id"
                                        class="flex items-start gap-2 rounded border border-gray-200 p-3 dark:border-gray-700"
                                    >
                                        <input
                                            v-model="selectedCheckIds"
                                            type="checkbox"
                                            :value="check.id"
                                            class="mt-1 rounded border-gray-300 text-indigo-600"
                                        >

                                        <span class="text-sm text-gray-800 dark:text-gray-100">
                                            {{ check.id }} - {{ check.description }}
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <button
                                type="button"
                                :disabled="executing"
                                class="rounded bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                                @click="executeChecks"
                            >
                                {{ executing ? 'Executando...' : 'Iniciar verificações' }}
                            </button>
                        </div>
                    </template>

                    <p
                        v-if="error"
                        class="mt-4 rounded-md bg-red-50 p-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300"
                    >
                        {{ error }}
                    </p>

                    <p
                        v-if="success"
                        class="mt-4 rounded-md bg-green-50 p-3 text-sm text-green-700 dark:bg-green-950 dark:text-green-300"
                    >
                        {{ success }}
                    </p>
                </div>

                <div
                    v-if="results.length"
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">Check</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">Problemas</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">Tempo</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="result in results" :key="result.check_id">
                                    <td class="px-3 py-3">
                                        {{ result.check_id }} - {{ result.description }}
                                    </td>
                                    <td class="px-3 py-3">
                                        {{ result.status }}
                                    </td>
                                    <td class="px-3 py-3">
                                        {{ result.found_count }}
                                    </td>
                                    <td class="px-3 py-3">
                                        {{ result.duration_ms }} ms
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
