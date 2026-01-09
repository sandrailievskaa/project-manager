<script setup lang="ts">
import { CheckCircle2, X, XCircle, AlertTriangle, Info } from 'lucide-vue-next';
import { computed } from 'vue';
import { useToast, type Toast as ToastType } from '@/composables/useToast';

const { toasts, remove } = useToast();

const getIcon = (type: ToastType['type']) => {
    switch (type) {
        case 'success':
            return CheckCircle2;
        case 'error':
            return XCircle;
        case 'warning':
            return AlertTriangle;
        default:
            return Info;
    }
};

const getColors = (type: ToastType['type']) => {
    switch (type) {
        case 'success':
            return 'bg-green-50 border-green-200 text-green-800 dark:bg-green-950/50 dark:border-green-800 dark:text-green-300';
        case 'error':
            return 'bg-red-50 border-red-200 text-red-800 dark:bg-red-950/50 dark:border-red-800 dark:text-red-300';
        case 'warning':
            return 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-950/50 dark:border-yellow-800 dark:text-yellow-300';
        default:
            return 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-950/50 dark:border-blue-800 dark:text-blue-300';
    }
};

const getIconColors = (type: ToastType['type']) => {
    switch (type) {
        case 'success':
            return 'text-green-600 dark:text-green-400';
        case 'error':
            return 'text-red-600 dark:text-red-400';
        case 'warning':
            return 'text-yellow-600 dark:text-yellow-400';
        default:
            return 'text-blue-600 dark:text-blue-400';
    }
};
</script>

<template>
    <Teleport to="body">
        <div class="fixed bottom-4 right-4 z-[9999] flex flex-col gap-2">
            <TransitionGroup
                name="toast"
                tag="div"
                class="flex flex-col gap-2"
            >
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    :class="[
                        'group relative flex min-w-[320px] max-w-md items-start gap-3 rounded-xl border-2 p-4 shadow-2xl backdrop-blur-xl transition-all duration-300 hover:scale-105',
                        getColors(toast.type),
                    ]"
                >
                    <component
                        :is="getIcon(toast.type)"
                        :class="['h-5 w-5 shrink-0', getIconColors(toast.type)]"
                    />
                    <p class="flex-1 text-sm font-semibold">{{ toast.message }}</p>
                    <button
                        @click="remove(toast.id)"
                        class="shrink-0 rounded-lg p-1 transition-colors hover:bg-black/10 dark:hover:bg-white/10"
                    >
                        <X class="h-4 w-4" />
                    </button>
                    <div
                        class="absolute bottom-0 left-0 h-1 bg-current opacity-30 animate-shrink"
                        :style="{ animationDuration: `${toast.duration}ms` }"
                    ></div>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from {
    opacity: 0;
    transform: translateX(100%);
}

.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}

@keyframes shrink {
    from {
        width: 100%;
    }
    to {
        width: 0%;
    }
}

.animate-shrink {
    animation: shrink linear forwards;
}
</style>

