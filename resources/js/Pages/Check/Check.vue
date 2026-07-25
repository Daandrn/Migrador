<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ListCheckForm from '@/Pages/Check/Partials/ListCheckForm.vue';
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3';

const checks = ref([]);
const loading = ref(false);
const error = ref(null);

async function loadChecks() {
    try {
        loading.value = true;
        error.value = null;

        const response = await axios.get(route('api.checks'));

        checks.value = Array.isArray(response.data)
            ? response.data
            : response.data.data ?? [];
    } catch (error) {
        error.value = error.response?.data?.message ?? 'Erro ao carregar checagens.';
        console.error(error);
    } finally {
        loading.value = false;
    }
}

async function updateCheck(check) {
    try {
        await axios.put(route('api.checks') + `/${check.id}`, check);

        alert('Check atualizado com sucesso!');
    } catch (error) {
        console.error(error);
        alert(error.response?.data?.message ?? 'Erro ao atualizar Check.');
    }
}

async function deleteCheck(check) {
    if (!confirm(`Deseja realmente excluir o check ${check.id} - ${check.description}?`)) {
        return;
    }

    try {
        await axios.delete(route('api.checks.destroy', check.id));


        checks.value = checks.value.filter(item => item.id !== check.id);
    } catch (error) {
        console.error(error);
        alert(error.response?.data?.message ?? 'Erro ao excluir check.');
    }
}

onMounted(() => {
    loadChecks();
});

</script>

<template>
    <Head title="Checagens" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Checagens
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                    <ListCheckForm 
                        @check-saved="loadChecks" 
                    />
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">

                    <div
                        v-if="loading"
                        class="text-sm text-gray-600 dark:text-gray-400"
                    >
                        Carregando...
                    </div>

                    <p 
                        v-else-if="error"
                        class="rounded-md bg-red-50 p-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300"
                    >
                        {{ error }}
                    </p>

                    <div
                        v-else 
                        class="overflow-x-auto"
                    >
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        Id
                                    </th>

                                    <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        Descrição
                                    </th>

                                    <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        Tipo
                                    </th>

                                    <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        Consulta SQL
                                    </th>

                                    <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        Ativo
                                    </th>

                                    <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>

                            <tbody 
                                v-if="checks.length"
                                class="divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="check in checks"
                                    :key="check.id"
                                >
                                    <td class="whitespace-nowrap px-3 py-3">
                                        {{ check.id }}
                                    </td>

                                    <td class="min-w-64 px-3 py-3">
                                        <input
                                            v-model="check.description"
                                            type="text"
                                            class="w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        >
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-3">
                                        {{ check.description_type ?? check.type?.description ?? check.type_id }}
                                    </td>

                                    <td class="min-w-96 px-3 py-3">
                                        <textarea
                                            v-model="check.sql_query"
                                            rows="3"
                                            class="w-full rounded-md border-gray-300 bg-white font-mono text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                            spellcheck="false"
                                        />
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-3 text-center">
                                        <input
                                            v-model="check.active"
                                            type="checkbox"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        >
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-3">
                                        <div 
                                            class="flex gap-2"
                                        >
                                            <button
                                                type="button"
                                                :disabled="check.saving"
                                                class="rounded bg-blue-600 px-3 py-1 text-white hover:bg-blue-700"
                                                @click="updateCheck(check)"
                                            >
                                                Alterar
                                            </button>

                                            <button
                                                type="button"
                                                class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700"
                                                @click="deleteCheck(check)"
                                            >
                                                Excluir
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>

                            <tbody v-else>
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-gray-500 dark:text-gray-400">
                                        Nenhuma checagem encontrada.
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
