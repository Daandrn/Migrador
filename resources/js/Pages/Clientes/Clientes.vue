<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3';

const clientes = ref([]);
const carregando = ref(false);
const erro = ref(null);

async function carregarClientes() {
    try {
        carregando.value = true;

        const response = await axios.get(route('clientes.index'));

        clientes.value = response.data;
    } catch (e) {
        erro.value = 'Erro ao carregar clientes';
        console.error(e);
        
    } finally {
        carregando.value = false;
    }
}

async function alterarCliente(cliente) {
    try {
        await axios.put(route('clientes.index') + `/${cliente.id}`, cliente);

        alert('Cliente atualizado com sucesso!');
    } catch (e) {
        console.error(e);
        alert(e.response?.data?.message ?? 'Erro ao atualizar cliente.');
    }
}

async function excluirCliente(cliente) {
    if (!confirm(`Deseja realmente excluir o cliente ${cliente.id} - ${cliente.nome_banco} - ${cliente.host}?`)) {
        return;
    }

    try {
        await axios.delete(route('clientes.destroy', cliente.id));


        clientes.value = clientes.value.filter(c => c.id !== cliente.id);
    } catch (e) {
        console.error(e);
        alert(e.response?.data?.message ?? 'Erro ao excluir cliente.');
    }
}

onMounted(() => {
    carregarClientes();
});

</script>

<template>
    <Head title="Clientes" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Clientes
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
                                    <th>Host</th>
                                    <th>Porta</th>
                                    <th>Usuário</th>
                                    <th>Senha</th>
                                    <th>Nome Banco</th>
                                    <th>Driver</th>
                                    <th>Ativo</th>
                                </tr>
                            </thead>

                            <tbody v-if="clientes.length">
                                <tr
                                    v-for="cliente in clientes"
                                    :key="cliente.id"
                                >
                                    <td>{{ cliente.id }}</td>

                                    <td>
                                        <input
                                            v-model="cliente.host"
                                            class="border rounded p-1 w-full text-black"
                                        >
                                    </td>

                                    <td>
                                        <input
                                            v-model="cliente.porta"
                                            type="number"
                                            class="border rounded p-1 w-24 text-black"
                                        >
                                    </td>

                                    <td>
                                        <input
                                            v-model="cliente.usuario"
                                            type="text"
                                            class="border rounded p-1 w-full text-black"
                                        >
                                    </td>

                                    <td>
                                        <input
                                            v-model="cliente.senha"
                                            type="password"
                                            class="border rounded p-1 w-full text-black"
                                        >
                                    </td>

                                    <td>
                                        <input
                                            v-model="cliente.nome_banco"
                                            class="border rounded p-1 w-full text-black"
                                        >
                                    </td>

                                    <td>
                                        <input
                                            v-model="cliente.driver"
                                            class="border rounded p-1 w-full text-black"
                                        >
                                    </td>

                                    <td>
                                        <input
                                            type="checkbox"
                                            v-model="cliente.ativo"
                                        >
                                    </td>

                                    <td class="space-x-2">
                                        <button
                                            class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700"
                                            @click="alterarCliente(cliente)"
                                        >
                                            Alterar
                                        </button>

                                        <button
                                            class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
                                            @click="excluirCliente(cliente)"
                                        >
                                            Excluir
                                        </button>
                                    </td>
                                </tr>
                            </tbody>

                            <tbody v-else>
                                <tr>
                                    <td colspan="9">
                                        Nenhum cliente encontrado.
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
