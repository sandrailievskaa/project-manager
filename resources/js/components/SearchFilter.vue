<script setup lang="ts">
import { ref, computed } from 'vue';
import { Search, X, Filter } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';

interface Props {
    tasks: any[];
    onFiltered?: (filtered: any[]) => void;
}

const props = defineProps<Props>();

const searchQuery = ref('');
const statusFilter = ref<string | null>(null);
const showFilters = ref(false);

const statusOptions = [
    { value: null, label: 'All Status' },
    { value: 'to_do', label: 'To Do' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'qa', label: 'QA' },
    { value: 'done', label: 'Done' },
];

const filteredTasks = computed(() => {
    let result = [...props.tasks];
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(task =>
            task.title.toLowerCase().includes(query) ||
            task.description?.toLowerCase().includes(query) ||
            task.user?.name?.toLowerCase().includes(query)
        );
    }
    
    if (statusFilter.value) {
        result = result.filter(task => task.status === statusFilter.value);
    }
    
    props.onFiltered?.(result);
    return result;
});

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = null;
};
</script>

<template>
    <div class="flex flex-col gap-4">
        <div class="relative flex items-center gap-2">
            <div class="relative flex-1">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    v-model="searchQuery"
                    placeholder="Search tasks..."
                    class="pl-10 pr-10"
                />
                <button
                    v-if="searchQuery"
                    @click="searchQuery = ''"
                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full p-1 hover:bg-muted"
                >
                    <X class="h-4 w-4" />
                </button>
            </div>
            <Button
                @click="showFilters = !showFilters"
                variant="outline"
                :class="showFilters ? 'bg-primary/10 ring-2 ring-primary/30' : ''"
            >
                <Filter class="h-4 w-4" />
            </Button>
        </div>
        
        <Transition name="slide">
            <div
                v-if="showFilters"
                class="flex flex-wrap items-center gap-2 rounded-xl border-2 border-border/50 bg-card p-4 shadow-md"
            >
                <span class="text-sm font-semibold text-muted-foreground">Status:</span>
                <button
                    v-for="option in statusOptions"
                    :key="option.value || 'all'"
                    @click="statusFilter = option.value"
                    :class="[
                        'rounded-lg px-3 py-1.5 text-xs font-semibold transition-all duration-200 hover:scale-105',
                        statusFilter === option.value
                            ? 'bg-primary text-primary-foreground shadow-md'
                            : 'bg-muted text-muted-foreground hover:bg-muted/80',
                    ]"
                >
                    {{ option.label }}
                </button>
                <Button
                    v-if="searchQuery || statusFilter"
                    @click="clearFilters"
                    variant="ghost"
                    size="sm"
                    class="ml-auto"
                >
                    <X class="h-4 w-4" />
                    Clear
                </Button>
            </div>
        </Transition>
        
        <div v-if="searchQuery || statusFilter" class="flex items-center gap-2">
            <Badge variant="secondary" class="font-semibold">
                {{ filteredTasks.length }} task{{ filteredTasks.length !== 1 ? 's' : '' }} found
            </Badge>
        </div>
    </div>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: all 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>

