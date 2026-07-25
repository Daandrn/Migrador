<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { onMounted, reactive, ref } from 'vue';

const emit = defineEmits(['check-saved']);

const types = ref([]);
const loadingTypes = ref(false);
const saving = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const form = reactive({
    description: '',
    type_id: '',
    sql_query: '',
    active: true,
});

const errors = reactive({
    description: '',
    type_id: '',
    sql_query: '',
    active: '',
});

function clearErrors() {
    errors.description = '';
    errors.type_id = '';
    errors.sql_query = '';
    errors.active = '';
    errorMessage.value = '';
}

function clearForm() {
    form.description = '';
    form.type_id = '';
    form.sql_query = '';
    form.active = true;
    clearErrors();
}

function applyErrorValidation(error) {
    const backendErrors = error.response?.data?.errors;

    if (!backendErrors) {
        errorMessage.value = error.response?.data?.message
            ?? 'Não foi possível salvar a checagem.';
        return;
    }

    errors.description = backendErrors.description?.[0] ?? '';
    errors.type_id = backendErrors.type_id?.[0] ?? '';
    errors.sql_query = backendErrors.sql_query?.[0] ?? '';
    errors.active = backendErrors.active?.[0] ?? '';
}

async function loadTypes() {
    try {
        loadingTypes.value = true;
        errorMessage.value = '';

        const response = await axios.get(route('api.verifyTypes'));
        types.value = Array.isArray(response.data)
            ? response.data
            : response.data.data ?? [];
    } catch (error) {
        console.error(error);
        errorMessage.value = error.response?.data?.message
            ?? 'Não foi possível carregar os tipos de checagem.';
    } finally {
        loadingTypes.value = false;
    }
}

async function saveCheck() {
    try {
        saving.value = true;
        successMessage.value = '';
        clearErrors();

        const response = await axios.post(route('api.checks.insert'), {
            description: form.description,
            type_id: form.type_id,
            sql_query: form.sql_query,
            active: form.active,
        });

        successMessage.value = 'Checagem salva com sucesso.';
        clearForm();
        emit('check-saved', response.data);
    } catch (error) {
        console.error(error);
        applyErrorValidation(error);
    } finally {
        saving.value = false;
    }
}

onMounted(() => {
    loadTypes();
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Nova checagem
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Cadastre a checagem e vincule-a a um tipo padronizado.
            </p>
        </header>

        <form 
            class="mt-6 space-y-6" 
            @submit.prevent="saveCheck"
        >
            <div>
                <InputLabel 
                    for="description" 
                    value="Descrição" 
                />

                <TextInput
                    id="description"
                    v-model="form.description"
                    type="text"
                    class="mt-1 block w-full"
                    maxlength="100"
                    required
                    autocomplete="off"
                />

                <InputError 
                    :message="errors.description" 
                    class="mt-2" 
                />
            </div>

            <div>
                <InputLabel 
                    for="type_id" 
                    value="Tipo" 
                />

                <select
                    id="type_id"
                    v-model="form.type_id"
                    class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                    :disabled="loadingTypes"
                    required
                >
                    <option value="" disabled>
                        {{ loadingTypes ? 'Carregando tipos...' : 'Selecione um tipo' }}
                    </option>

                    <option
                        v-for="type in types"
                        :key="type.id"
                        :value="type.id"
                    >
                        {{ type.description }}
                    </option>
                </select>

                <InputError 
                    :message="errors.type_id" 
                    class="mt-2" 
                />
            </div>

            <div>
                <InputLabel for="sql_query" value="Consulta SQL" />

                <textarea
                    id="sql_query"
                    v-model="form.sql_query"
                    rows="12"
                    class="mt-1 block w-full rounded-md border-gray-300 bg-white font-mono text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                    required
                    spellcheck="false"
                />

                <InputError 
                    :message="errors.sql_query" 
                    class="mt-2" 
                />
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
                <PrimaryButton :disabled="saving || loadingTypes">
                    {{ saving ? 'Salvando...' : 'Salvar' }}
                </PrimaryButton>

                <SecondaryButton type="button" :disabled="saving" @click="clearForm">
                    Limpar
                </SecondaryButton>
            </div>
        </form>
    </section>
</template>
