<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3';

const checks = ref([]);
const carregando = ref(false);
const erro = ref(null);

async function carregarChecks() {
    try {
        carregando.value = true;

        const response = await axios.get(route('checks.index'));

        checks.value = response.data;
    } catch (e) {
        erro.value = 'Erro ao carregar checks';
        console.error(e);
        
    } finally {
        carregando.value = false;
    }
}

async function alterarCheck(check) {
    try {
        await axios.put(route('checks.index') + `/${check.id}`, check);

        alert('Check atualizado com sucesso!');
    } catch (e) {
        console.error(e);
        alert(e.response?.data?.message ?? 'Erro ao atualizar Check.');
    }
}

async function excluirCheck(check) {
    if (!confirm(`Deseja realmente excluir o check ${check.id} - ${check.descricao}?`)) {
        return;
    }

    try {
        await axios.delete(route('checks.destroy', check.id));


        checks.value = checks.value.filter(c => c.id !== check.id);
    } catch (e) {
        console.error(e);
        alert(e.response?.data?.message ?? 'Erro ao excluir check.');
    }
}

onMounted(() => {
    carregarChecks();
});

</script>

<template>
    <Head title="Checks" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Checks
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <p v-if="carregando">
                    Carregando...
                </p>

                <p v-if="erro">
                    {{ erro }}
                </p>

                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <table>
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Descrição</th>
                                    <th>Tipo</th>
                                    <th>Consulta SQL</th>
                                    <th>Ativo</th>
                                </tr>
                            </thead>

                            <tbody v-if="checks.length">
                                <tr
                                    v-for="check in checks"
                                    :key="check.id"
                                >
                                    <td>{{ check.id }}</td>

                                    <td>
                                        <input
                                            v-model="check.descricao"
                                            type="text"
                                            class="border rounded p-1 w-full text-black"
                                        >
                                    </td>

                                    <td>
                                        <input
                                            v-model="check.descricao_tipo"
                                            type="text"
                                            class="border rounded p-1 w-24 text-black"
                                        >
                                    </td>

                                    <td>
                                        <input
                                            v-model="check.consulta_sql"
                                            type="text"
                                            class="border rounded p-1 w-full text-black"
                                        >
                                    </td>

                                    <td>
                                        <input
                                            type="checkbox"
                                            v-model="check.ativo"
                                        >
                                    </td>

                                    <td class="space-x-2">
                                        <button
                                            class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700"
                                            @click="alterarCheck(check)"
                                        >
                                            Alterar
                                        </button>

                                        <button
                                            class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
                                            @click="excluirCheck(check)"
                                        >
                                            Excluir
                                        </button>
                                    </td>
                                </tr>
                            </tbody>

                            <tbody v-else>
                                <tr>
                                    <td colspan="6">
                                        Nenhum check encontrado.
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
