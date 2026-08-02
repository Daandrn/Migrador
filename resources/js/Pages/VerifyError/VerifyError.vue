<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3';

const verifyErrors = ref([]);
const loading = ref(false);
const loadingMessage = ref('Carregando, aguarde!');
const error = ref(null);

async function loadVerifyErrors() {
    try {
        loading.value = true;
        loadingMessage.value = 'Carregando erros de checagem, aguarde!';
        error.value = null;

        const response = await axios.get(route('api.verifyErrors'));

        verifyErrors.value = response.data.data.verifyErrors;
    } catch (error) {
        error.value = error.response?.data?.message ?? 'Erro ao carregar erros de checagem.';
        console.error(error);
    } finally {
        loading.value = false;
    }
}

async function deleteError(verifyError) {
    if (!confirm(`Deseja realmente excluir o erro de checagem - Estudar o que colocar aqui?`)) {
        return;
    }

    loading.value = true;
    loadingMessage.value = 'Carregando erros de checagem, aguarde!';

    try {
        const ids = new Array;
        ids.push(verifyError.id);
        
        const response = await axios.delete(route('api.verifyErrors.destroy'), {
            data: {
                ids: ids
            }
        });

        verifyErrors.value = verifyErrors.value.filter(item => item.id !== verifyError.id);
        alert(response?.data?.message);
    } catch (error) {
        console.error(error);
        alert(error.response?.data?.message ?? 'Erro ao excluir erro de checagem.');
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    loadVerifyErrors();
});

</script>

<template>
    <Head title="Erros em Checagens" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Erros em Checagens
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

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
                                        Dados do erro
                                    </th>

                                    <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        Tipo
                                    </th>

                                    <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>

                            <tbody 
                                v-if="verifyErrors.length"
                                class="divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="verifyError in verifyErrors"
                                    :key="verifyError.id"
                                >
                                    <td class="whitespace-nowrap px-3 py-3">
                                        {{ verifyError.id }}
                                    </td>

                                    <td class="min-w-96 px-3 py-3">
                                        <textarea
                                            v-model="verifyError.data"
                                            rows="3"
                                            class="w-full rounded-md border-gray-300 bg-white font-mono text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                            spellerror="false"
                                        />
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-3">
                                        {{ verifyError.description_type ?? verifyError.type?.description ?? verifyError.type_id }}
                                    </td>


                                    <td class="whitespace-nowrap px-3 py-3">
                                        <div 
                                            class="flex gap-2"
                                        >
                                            <button
                                                type="button"
                                                class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700"
                                                @click="deleteError(verifyError)"
                                                :disabled="loading"
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
                                        Nenhum erro de checagem encontrado.
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
