<script setup lang="ts">
interface ChartData {
    label: string;
    value: number;
    color?: string;
}

interface Props {
    data: ChartData[];
    maxValue?: number;
    height?: number;
    showValues?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    height: 200,
    showValues: true,
});

const maxValue = computed(() => {
    if (props.maxValue !== undefined) return props.maxValue;
    return Math.max(...props.data.map((d) => d.value), 1);
});

const barWidth = computed(() => 100 / props.data.length);

const defaultColors = [
    'bg-blue-500',
    'bg-indigo-500',
    'bg-purple-500',
    'bg-pink-500',
    'bg-teal-500',
    'bg-cyan-500',
];
</script>

<template>
    <div class="w-full" :style="{ height: `${height}px` }">
        <div class="flex h-full items-end gap-2">
            <div
                v-for="(item, index) in data"
                :key="index"
                class="group relative flex-1"
            >
                <div
                    class="relative h-full w-full rounded-t transition-all duration-500 ease-out hover:opacity-90"
                    :class="item.color || defaultColors[index % defaultColors.length]"
                    :style="{
                        height: `${(item.value / maxValue) * 100}%`,
                        minHeight: item.value > 0 ? '4px' : '0',
                    }"
                >
                    <div
                        v-if="showValues && item.value > 0"
                        class="absolute -top-6 left-1/2 hidden -translate-x-1/2 transform text-xs font-medium group-hover:block"
                    >
                        {{ item.value }}
                    </div>
                </div>
                <div
                    class="mt-2 truncate text-center text-xs text-muted-foreground"
                    :title="item.label"
                >
                    {{ item.label }}
                </div>
            </div>
        </div>
    </div>
</template>
