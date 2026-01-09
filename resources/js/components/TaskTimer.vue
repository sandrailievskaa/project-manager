<script setup lang="ts">
import { ref, computed, onUnmounted } from 'vue';
import { Play, Pause, Square, Clock } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

interface Props {
    taskId: number;
    initialTime?: number;
}

const props = defineProps<Props>();

const isRunning = ref(false);
const elapsedTime = ref(props.initialTime || 0);
const intervalId = ref<number | null>(null);

const formattedTime = computed(() => {
    const hours = Math.floor(elapsedTime.value / 3600);
    const minutes = Math.floor((elapsedTime.value % 3600) / 60);
    const seconds = elapsedTime.value % 60;
    
    if (hours > 0) {
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
});

const start = () => {
    if (intervalId.value) return;
    
    isRunning.value = true;
    intervalId.value = window.setInterval(() => {
        elapsedTime.value++;
        localStorage.setItem(`task_timer_${props.taskId}`, elapsedTime.value.toString());
    }, 1000);
};

const pause = () => {
    if (intervalId.value) {
        clearInterval(intervalId.value);
        intervalId.value = null;
    }
    isRunning.value = false;
};

const reset = () => {
    pause();
    elapsedTime.value = 0;
    localStorage.removeItem(`task_timer_${props.taskId}`);
};

const toggle = () => {
    if (isRunning.value) {
        pause();
    } else {
        start();
    }
};

onUnmounted(() => {
    if (intervalId.value) {
        clearInterval(intervalId.value);
    }
});

const savedTime = localStorage.getItem(`task_timer_${props.taskId}`);
if (savedTime) {
    elapsedTime.value = parseInt(savedTime, 10);
}
</script>

<template>
    <div class="flex items-center gap-2 rounded-xl border-2 border-border/50 bg-card p-3 shadow-md">
        <Clock class="h-4 w-4 text-muted-foreground" />
        <span class="font-mono text-lg font-bold text-foreground">{{ formattedTime }}</span>
        <div class="ml-auto flex items-center gap-1">
            <Button
                @click="toggle"
                variant="ghost"
                size="sm"
                class="h-8 w-8 p-0"
            >
                <Play v-if="!isRunning" class="h-4 w-4" />
                <Pause v-else class="h-4 w-4" />
            </Button>
            <Button
                @click="reset"
                variant="ghost"
                size="sm"
                class="h-8 w-8 p-0"
            >
                <Square class="h-4 w-4" />
            </Button>
        </div>
    </div>
</template>

