<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { reactive, ref } from 'vue';

const emit = defineEmits(['client-saved']);

const saving = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const form = reactive({
    host: '',
    port: 5432,
    user: '',
    password: '',
    db_name: '',
    driver: 'pgsql',
    active: true,
});

const errors = reactive({
    host: '',
    port: '',
    user: '',
    password: '',
    db_name: '',
    driver: '',
    active: '',
});

function clearErrors() {
    errors.host = '',
    errors.port = '',
    errors.user = '',
    errors.password = '',
    errors.db_name = '',
    errors.driver = '',
    errors.active = '',
    errorMessage.value = '';
}

function clearForm() {
    form.host = '';
    form.port = 5432;
    form.user = '';
    form.password = '';
    form.db_name = '';
    form.driver = 'pgsql';
    form.active = true;

    clearErrors();
}

function applyErrorValidation(error) {
    const backendErrors = error.response?.data?.errors;

    if (!backendErrors) {
        errorMessage.value = error.response?.data?.message
            ?? 'Não foi possível salvar o cliente.';
        return;
    }

    errors.host = backendErrors.host?.[0] ?? '';
    errors.port = backendErrors.port?.[0] ?? '';
    errors.user = backendErrors.user?.[0] ?? '';
    errors.password = backendErrors.password?.[0] ?? '';
    errors.db_name = backendErrors.db_name?.[0] ?? '';
    errors.driver = backendErrors.driver?.[0] ?? '';
    errors.active = backendErrors.active?.[0] ?? '';
}

async function saveClient() {
    try {
        saving.value = true;
        successMessage.value = '';
        clearErrors();

        const response = await axios.post(route('api.clients.insert'), {
            host: form.host,
            port: form.port,
            user: form.user,
            password: form.password,
            db_name: form.db_name,
            driver: form.driver || null,
            active: form.active,
        });

        successMessage.value = 'Cliente salvo com sucesso.';
        clearForm();
        emit('client-saved', response.data);
    } catch (error) {
        console.error(error);
        applyErrorValidation(error);
    } finally {
        saving.value = false;
    }
}

</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Novo cliente
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Informe os dados necessários para conexão com o banco do cliente.
            </p>
        </header>

        <form
            class="mt-6 space-y-6"
            @submit.prevent="saveClient"
        >
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div> 
                    <InputLabel
                        for="host"
                        value="Host"
                    />

                    <TextInput
                        id="host"
                        v-model="form.host"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        maxlength="100"
                        autocomplete="off"
                    />

                    <InputError
                        :message="errors.host"
                        class="mt-2"
                    />
                </div>

                <div>
                    <InputLabel
                        for="port"
                        value="Porta"
                    />

                    <input
                        id="port"
                        v-model="form.port"
                        type="number"
                        min="1"
                        max="65535"
                        class="mt-1 block w-full"
                        required
                    />

                    <InputError
                        :message="errors.port"
                        class="mt-2"
                    />
                </div>

                <div>
                    <InputLabel
                        for="user"
                        value="Usuário"
                    />

                    <TextInput
                        id="user"
                        v-model="form.user"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        maxlength="100"
                        autocomplete="off"
                    />

                    <InputError
                        :message="errors.user"
                        class="mt-2"
                    />
                </div>

                <div>
                    <InputLabel
                        for="password"
                        value="Senha"
                    />

                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        required
                        maxlength="100"
                        autocomplete="new-password"
                    />

                    <InputError
                        :message="errors.password"
                        class="mt-2"
                    />
                </div>

                <div>
                    <InputLabel
                        for="db_name"
                        value="Nome do banco"
                    />

                    <TextInput
                        id="db_name"
                        v-model="form.db_name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        maxlength="100"
                        autocomplete="off"
                    />

                    <InputError
                        :message="errors.db_name"
                        class="mt-2"
                    />
                </div>

                <div>
                    <InputLabel
                        for="driver"
                        value="Driver"
                    />

                    <TextInput
                        id="driver"
                        v-model="form.driver"
                        type="text"
                        class="mt-1 block w-full"
                        maxlength="100"
                        autocomplete="off"
                    />

                    <InputError
                        :message="errors.driver"
                        class="mt-2"
                    />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status
                </label>

                <div class="flex gap-6">
                    <label class="flex items-center">
                        <input
                            type="radio"
                            v-model="form.active"
                            :value="true"
                            name="active"
                            class="mr-2"
                        />

                        Ativo
                    </label>

                    <label class="flex items-center">
                        <input
                            type="radio"
                            v-model="form.active"
                            :value="false"
                            name="active"
                            class="mr-2"
                        />

                        Inativo
                    </label>
                </div>

                <InputError
                    :message="errors.active"
                    class="mt-2"
                />
            </div>

            <p
                v-if="errorMessage"
                class="rounded-md bg-red-50 p-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300"
            >
                {{ errorMessage }}
            </p>

            <p
                v-if="successMessage"
                class="rounded-md bg-green-50 p-3 text-sm text-green-700 dark:bg-green-950 dark:text-green-300"
            >
                {{ successMessage }}
            </p>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="saving">
                    {{ saving ? 'Salvando...' : 'Salvar' }}
                </PrimaryButton>

                <SecondaryButton type="button" :disabled="saving" @click="clearForm">
                    Limpar
                </SecondaryButton>
            </div>
        </form>
    </section>
</template>
