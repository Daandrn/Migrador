<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ListClientForm from './Partials/ListClientForm.vue';
import { onMounted, ref } from 'vue';
import { Head } from '@inertiajs/vue3';

const clients = ref([]);
const loading = ref(false);
const error = ref(null);

async function loadClients() {
    try {
        loading.value = true;
        error.value = null;

        const response = await axios.get(route('api.clients'));

        clients.value = Array.isArray(response.data)
            ? response.data
            : response.data.data ?? [];
    } catch (error) {
        error.value = error.response?.data?.message ?? 'Erro ao carregar clientes.';
        console.error(error);
    } finally {
        loading.value = false;
    }
}

async function updateClient(client) {
    try {
        await axios.put(route('api.clients') + `/${client.id}`, client);

        alert('Cliente atualizado com sucesso!');
    } catch (error) {
        console.error(error);
        alert(error.response?.data?.message ?? 'Erro ao atualizar cliente.');
    }
}

async function verifyUserClient(client) {
    try {
        const response = await axios.get(route('api.clients.UserVerify', client.id));

        if (response.data.error) {
            alert(response.data.message);
            return;
        }

        alert('Usuário do cliente foi verificado e possui a permissão necessária!');
    } catch (error) {
        console.error(error);
        alert(error.response?.data?.message ?? 'Erro ao verificar usuário do cliente.');
    }
}

async function deleteClient(client) {
    if (!confirm(`Deseja realmente excluir o cliente ${client.id} - ${client.db_name} - ${client.host}?`)) {
        return;
    }

    try {
        const response = await axios.delete(route('api.clients.destroy', client.id));

        clients.value = clients.value.filter(item => item.id !== client.id);

        alert(response?.data?.message);
    } catch (error) {
        console.error(error);
        alert(error.response?.data?.message ?? 'Erro ao excluir cliente.');
    }
}

onMounted(() => {
    loadClients();
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
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                    <ListClientForm
                        @client-saved="loadClients"
                    />
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">

                    <div
                        v-if="loading"
                        class="text-sm text-gray-600 dark:text-gray-400"
                    >
                        Carregando clientes...
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
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">
                                        Id
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">
                                        Host
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">
                                        Porta
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">
                                        Usuário
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">
                                        Banco
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">
                                        Password
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">
                                        Driver
                                    </th>

                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">
                                        Status
                                    </th>

                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-300">
                                        Ações
                                    </th>
                                </tr>
                            </thead>

                            <tbody 
                                v-if="clients.length"
                                class="divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="client in clients"
                                    :key="client.id"
                                >
                                    <td class="whitespace-nowrap px-3 py-3">
                                        {{ client.id }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <input
                                            v-model.trim="client.host"
                                            type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                        />
                                    </td>

                                    <td class="px-4 py-3">
                                        <input
                                            v-model="client.port"
                                            type="number"
                                            min="1"
                                            max="65535"
                                            class="w-28 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                        />
                                    </td>

                                    <td class="px-4 py-3">
                                        <input
                                            v-model.trim="client.user"
                                            type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                        />
                                    </td>

                                    <td class="px-4 py-3">
                                        <input
                                            v-model.trim="client.db_name"
                                            type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                        />
                                    </td>

                                    <td>
                                        <input
                                                v-model="client.password"
                                                type="password"
                                                placeholder="password"
                                                autocomplete="new-password"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                            />
                                    </td>

                                    <td class="px-4 py-3">
                                        <input
                                            v-model.trim="client.driver"
                                            type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                        />
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-3 text-center">
                                        <input
                                            v-model="client.active"
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
                                                :disabled="client.saving"
                                                class="rounded bg-blue-600 px-3 py-1 text-white hover:bg-blue-700"
                                                @click="updateClient(client)"
                                            >
                                                Alterar
                                            </button>

                                            <button
                                                type="button"
                                                class="rounded bg-green-600 px-3 py-1 text-white hover:bg-green-700"
                                                @click="verifyUserClient(client)"
                                            >
                                                Verificar Usuário
                                            </button>

                                            <button
                                                type="button"
                                                class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700"
                                                @click="deleteClient(client)"
                                            >
                                                Excluir
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>

                            <tbody v-else>
                                <tr>
                                    <td colspan="8" class="px-3 py-6 text-center text-gray-500 dark:text-gray-400">
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
