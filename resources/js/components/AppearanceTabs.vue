<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { Monitor, Moon, Sun, Palette } from 'lucide-vue-next';

const { appearance, updateAppearance } = useAppearance();

const tabs = [
    { value: 'light', Icon: Sun, label: 'Light' },
    { value: 'dark', Icon: Moon, label: 'Dark' },
    { value: 'pastel', Icon: Palette, label: 'Pastel' },
    { value: 'system', Icon: Monitor, label: 'System' },
] as const;
</script>

<template>
    <div
        class="inline-flex gap-1 rounded-xl bg-neutral-100 p-1 shadow-lg ring-1 ring-border/50 dark:bg-neutral-800"
    >
        <button
            v-for="{ value, Icon, label } in tabs"
            :key="value"
            @click="updateAppearance(value)"
            :class="[
                'group relative flex items-center rounded-lg px-3.5 py-1.5 transition-all duration-300 hover:scale-105',
                appearance === value
                    ? 'bg-gradient-to-r from-primary/20 via-secondary/20 to-accent/20 text-primary shadow-md ring-2 ring-primary/30 dark:bg-gradient-to-r dark:from-primary/30 dark:via-secondary/30 dark:to-accent/30 dark:text-primary dark:ring-primary/40'
                    : 'text-neutral-500 hover:bg-neutral-200/60 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-700/60',
            ]"
        >
            <component :is="Icon" class="-ml-1 h-4 w-4 transition-transform duration-300 group-hover:rotate-12" />
            <span class="ml-1.5 text-sm font-medium">{{ label }}</span>
        </button>
    </div>
</template>
