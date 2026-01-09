<script setup lang="ts">
interface ChartData {
    label: string;
    value: number;
    color: string;
}

interface Props {
    data: ChartData[];
    size?: number;
    strokeWidth?: number;
}

const props = withDefaults(defineProps<Props>(), {
    size: 200,
    strokeWidth: 20,
});

const total = computed(() => props.data.reduce((sum, item) => sum + item.value, 0));

const radius = computed(() => (props.size - props.strokeWidth) / 2);
const circumference = computed(() => 2 * Math.PI * radius.value);

const segments = computed(() => {
    const filtered = props.data.filter((item) => item.value > 0);
    if (filtered.length === 0) return [];
    
    let currentOffset = 0;
    return filtered.map((item) => {
        const percentage = total.value > 0 ? item.value / total.value : 0;
        const offset = currentOffset;
        const strokeDasharray = `${circumference.value * percentage} ${circumference.value}`;
        currentOffset -= circumference.value * percentage;
        
        return {
            ...item,
            percentage: percentage * 100,
            offset,
            strokeDasharray,
        };
    });
});
</script>

<template>
    <div class="relative inline-flex items-center justify-center">
        <svg
            :width="size"
            :height="size"
            class="transform -rotate-90"
        >
            <circle
                v-for="(segment, index) in segments"
                :key="index"
                :cx="size / 2"
                :cy="size / 2"
                :r="radius"
                fill="none"
                :stroke="segment.color"
                :stroke-width="strokeWidth"
                :stroke-dasharray="segment.strokeDasharray"
                :stroke-dashoffset="segment.offset"
                class="transition-all duration-500 ease-out"
                stroke-linecap="round"
            />
        </svg>
        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <div class="text-2xl font-bold">{{ total }}</div>
            <div class="text-xs text-muted-foreground">Total</div>
        </div>
    </div>
</template>
