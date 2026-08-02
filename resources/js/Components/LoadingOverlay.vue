<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    message: {
        type: String,
        default: 'Carregando...',
    },
});
</script>

<template>
    <Teleport to="body">
        <Transition name="loading-fade">
            <div
                v-if="show"
                class="loading-overlay"
                role="status"
                aria-live="polite"
                aria-busy="true"
            >
                <div class="loading-content">
                    <div class="loading-spinner"></div>

                    <span class="loading-message">
                        {{ message }}
                    </span>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.loading-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;

    display: flex;
    align-items: center;
    justify-content: center;

    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);

    cursor: wait;
}

.loading-content {
    min-width: 220px;
    padding: 28px 36px;

    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 18px;

    background: #ffffff;
    border-radius: 16px;

    box-shadow:
        0 20px 25px -5px rgba(0, 0, 0, 0.15),
        0 8px 10px -6px rgba(0, 0, 0, 0.1);
}

.loading-spinner {
    width: 52px;
    height: 52px;

    border: 5px solid #e2e8f0;
    border-top-color: #2563eb;
    border-radius: 50%;

    animation: loading-spin 0.8s linear infinite;
}

.loading-message {
    color: #334155;
    font-size: 1rem;
    font-weight: 600;
    text-align: center;
}

.loading-fade-enter-active,
.loading-fade-leave-active {
    transition: opacity 0.2s ease;
}

.loading-fade-enter-from,
.loading-fade-leave-to {
    opacity: 0;
}

@keyframes loading-spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
