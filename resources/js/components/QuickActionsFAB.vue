<script setup lang="ts">
import { ref, computed } from 'vue';
import { Plus, X, FileText, UserPlus, Calendar, Search } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

interface Action {
    icon: any;
    label: string;
    action: () => void;
    color?: string;
}

interface Props {
    actions?: Action[];
    onCreateTask?: () => void;
    onAddUser?: () => void;
    onSearch?: () => void;
}

const props = defineProps<Props>();
const isOpen = ref(false);

const defaultActions: Action[] = [
    {
        icon: FileText,
        label: 'New Task',
        action: () => {
            props.onCreateTask?.();
            isOpen.value = false;
        },
        color: 'bg-primary hover:bg-primary/90',
    },
    {
        icon: UserPlus,
        label: 'Add User',
        action: () => {
            props.onAddUser?.();
            isOpen.value = false;
        },
        color: 'bg-secondary hover:bg-secondary/90',
    },
    {
        icon: Search,
        label: 'Search',
        action: () => {
            props.onSearch?.();
            isOpen.value = false;
        },
        color: 'bg-accent hover:bg-accent/90',
    },
];

</script>

<template>
    <div class="fixed bottom-6 right-6 z-50">
        <TransitionGroup
            name="fab"
            tag="div"
            class="relative"
        >
            <Button
                v-for="(action, index) in isOpen ? (props.actions || defaultActions) : []"
                :key="action.label"
                :style="{ transitionDelay: `${index * 50}ms` }"
                @click="action.action"
                :class="[
                    'absolute bottom-16 right-0 mb-2 flex items-center gap-2 rounded-full px-4 py-2 shadow-2xl transition-all duration-300 hover:scale-110',
                    action.color || 'bg-primary hover:bg-primary/90',
                ]"
                size="lg"
            >
                <component :is="action.icon" class="h-5 w-5" />
                <span class="font-semibold text-white">{{ action.label }}</span>
            </Button>
        </TransitionGroup>
        
        <Button
            @click="isOpen = !isOpen"
            class="h-14 w-14 rounded-full bg-gradient-to-r from-primary via-secondary to-accent shadow-2xl transition-all duration-300 hover:scale-110 hover:shadow-[0_20px_40px_rgba(99,102,241,0.4)]"
            size="lg"
        >
            <Transition
                name="rotate"
                mode="out-in"
            >
                <X
                    v-if="isOpen"
                    key="close"
                    class="h-6 w-6 text-white"
                />
                <Plus
                    v-else
                    key="plus"
                    class="h-6 w-6 text-white transition-transform duration-300"
                />
            </Transition>
        </Button>
    </div>
</template>

<style scoped>
.fab-enter-active,
.fab-leave-active {
    transition: all 0.3s ease;
}

.fab-enter-from {
    opacity: 0;
    transform: translateY(20px) scale(0.8);
}

.fab-leave-to {
    opacity: 0;
    transform: translateY(20px) scale(0.8);
}

.rotate-enter-active,
.rotate-leave-active {
    transition: all 0.3s ease;
}

.rotate-enter-from {
    opacity: 0;
    transform: rotate(-90deg);
}

.rotate-leave-to {
    opacity: 0;
    transform: rotate(90deg);
}
</style>

