<script setup lang="ts">
import { computed, ref } from 'vue';
import { type Task } from '@/types';
import { CheckCircle2, ClipboardList, ListTodo, Search } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
import { update as updateTask } from '@/routes/tasks';
import { useToast } from '@/composables/useToast';

interface Props {
    tasks: Task[];
    canChangeStatus?: (task: Task) => boolean;
}

const props = defineProps<Props>();
const { success, error } = useToast();

const draggedTask = ref<Task | null>(null);
const draggedOverColumn = ref<string | null>(null);

const statusColors = {
    to_do: { light: '#A0AEC0', dark: '#718096', bg: 'bg-gray-100 dark:bg-gray-800', text: 'text-gray-700 dark:text-gray-300', hover: 'hover:shadow-gray-500/20' },
    in_progress: { light: '#C83FFF', dark: '#9B67FC', bg: 'bg-purple-100 dark:bg-purple-900/30', text: 'text-purple-700 dark:text-purple-300', hover: 'hover:shadow-[0_8px_32px_rgba(200,63,255,0.3)]' },
    qa: { light: '#2D72F8', dark: '#1851FF', bg: 'bg-blue-100 dark:bg-blue-900/30', text: 'text-blue-700 dark:text-blue-300', hover: 'hover:shadow-[0_8px_32px_rgba(45,114,248,0.3)]' },
    done: { light: '#FF8933', dark: '#DFB729', bg: 'bg-orange-100 dark:bg-orange-900/30', text: 'text-orange-700 dark:text-orange-300', hover: 'hover:shadow-[0_8px_32px_rgba(255,137,51,0.3)]' },
};

const columns = [
    { id: 'to_do', label: 'To Do', icon: ListTodo, ...statusColors.to_do },
    { id: 'in_progress', label: 'In Progress', icon: ClipboardList, ...statusColors.in_progress },
    { id: 'qa', label: 'QA', icon: Search, ...statusColors.qa },
    { id: 'done', label: 'Done', icon: CheckCircle2, ...statusColors.done },
];

const getAvatarUrl = (name: string) => {
    return `https://api.dicebear.com/7.x/avataaars/svg?seed=${encodeURIComponent(name)}`;
};

const tasksByStatus = computed(() => {
    const grouped: Record<string, Task[]> = {
        to_do: [],
        in_progress: [],
        qa: [],
        done: [],
    };
    
    props.tasks.forEach(task => {
        if (task.status && grouped[task.status]) {
            grouped[task.status].push(task);
        }
    });
    
    return grouped;
});

const handleDragStart = (task: Task) => {
    if (props.canChangeStatus && !props.canChangeStatus(task)) {
        return;
    }
    draggedTask.value = task;
};

const handleDragOver = (e: DragEvent, columnId: string) => {
    e.preventDefault();
    draggedOverColumn.value = columnId;
};

const handleDrop = (e: DragEvent, columnId: string) => {
    e.preventDefault();
    
    if (!draggedTask.value) return;
    
    if (draggedTask.value.status === columnId) {
        draggedTask.value = null;
        draggedOverColumn.value = null;
        return;
    }
    
    if (props.canChangeStatus && !props.canChangeStatus(draggedTask.value)) {
        error('You do not have permission to change this task status');
        draggedTask.value = null;
        draggedOverColumn.value = null;
        return;
    }
    
    router.patch(
        updateTask(draggedTask.value.id).url,
        { status: columnId },
        {
            preserveScroll: true,
            onSuccess: () => {
                success(`Task moved to ${columns.find(c => c.id === columnId)?.label || columnId}`);
                router.reload({ preserveScroll: true });
            },
            onError: () => {
                error('Failed to update task status');
            },
        },
    );
    
    draggedTask.value = null;
    draggedOverColumn.value = null;
};

const handleDragEnd = () => {
    draggedTask.value = null;
    draggedOverColumn.value = null;
};

const getPriorityColor = (task: Task) => {
    const daysSinceCreated = Math.floor((Date.now() - new Date(task.created_at).getTime()) / (1000 * 60 * 60 * 24));
    if (daysSinceCreated > 7) return 'border-l-4 border-l-red-500';
    if (daysSinceCreated > 3) return 'border-l-4 border-l-yellow-500';
    return 'border-l-4 border-l-green-500';
};
</script>

<template>
    <div class="flex gap-4 overflow-x-auto pb-4">
        <div
            v-for="column in columns"
            :key="column.id"
            @dragover="handleDragOver($event, column.id)"
            @drop="handleDrop($event, column.id)"
            class="group/column relative flex min-w-[300px] flex-col overflow-hidden rounded-3xl border-2 border-border/50 bg-gradient-to-br from-card/90 via-card/95 to-muted/20 backdrop-blur-xl p-6 shadow-xl ring-1 ring-border/30 transition-all duration-500"
            :class="[
                draggedOverColumn === column.id ? 'ring-4 ring-primary/60 scale-[1.03] -translate-y-1 shadow-2xl' : 'hover:scale-[1.01] hover:shadow-2xl hover:ring-primary/30',
            ]"
        >
            <div
                class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r transition-all duration-500"
                :style="{
                    background: `linear-gradient(to right, ${column.light}, ${column.dark})`,
                }"
            ></div>
            
            <div class="absolute inset-0 bg-gradient-to-br from-card to-muted opacity-40 transition-opacity duration-500 group-hover/column:opacity-60"></div>
            
            <div class="relative z-10 mb-6 flex items-center gap-3">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br shadow-lg ring-2 ring-border/30 transition-all duration-300 group-hover/column:scale-110 group-hover/column:rotate-3"
                    :class="column.bg"
                >
                    <component
                        :is="column.icon"
                        class="h-6 w-6 transition-transform duration-300 group-hover/column:scale-110"
                        :class="column.text"
                    />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-extrabold tracking-tight text-foreground">{{ column.label }}</h3>
                </div>
                <span class="rounded-full bg-gradient-to-br from-primary/20 via-secondary/20 to-accent/20 px-3 py-1.5 text-xs font-extrabold shadow-md ring-1 ring-border/30 backdrop-blur-xl">
                    {{ tasksByStatus[column.id]?.length || 0 }}
                </span>
            </div>
            
            <div class="relative z-10 flex flex-1 flex-col gap-4">
                <div
                    v-for="task in tasksByStatus[column.id]"
                    :key="task.id"
                    draggable="true"
                    @dragstart="handleDragStart(task)"
                    @dragend="handleDragEnd"
                    class="group/task relative cursor-move overflow-hidden rounded-2xl border-2 border-border/50 bg-gradient-to-br from-card/90 via-card/95 to-muted/20 backdrop-blur-xl p-5 shadow-lg ring-1 ring-border/30 transition-all duration-500"
                    :class="[
                        getPriorityColor(task),
                        column.hover,
                        !canChangeStatus || !canChangeStatus(task) ? 'opacity-50 cursor-not-allowed' : 'hover:-translate-y-1 hover:scale-[1.02] hover:shadow-xl',
                    ]"
                >
                    <div
                        class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r transition-all duration-500"
                        :style="{
                            background: `linear-gradient(to right, ${column.light}, ${column.dark})`,
                        }"
                    ></div>
                    
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-secondary/5 to-accent/5 opacity-0 transition-opacity duration-300 group-hover/task:opacity-100"></div>
                    
                    <div class="relative z-10 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <h4 class="flex-1 text-base font-bold leading-tight text-foreground transition-colors duration-300 group-hover/task:text-primary">
                                {{ task.title }}
                            </h4>
                        </div>
                        <p class="line-clamp-2 text-sm leading-relaxed text-muted-foreground transition-colors duration-300 group-hover/task:text-foreground">
                            {{ task.description }}
                        </p>
                        <div v-if="task.user" class="flex items-center gap-2.5 rounded-xl border border-border/30 bg-muted/30 px-3 py-2 backdrop-blur-xl">
                            <img
                                :src="getAvatarUrl(task.user.name)"
                                :alt="task.user.name"
                                class="h-8 w-8 rounded-full ring-2 ring-border/50 shadow-md"
                            />
                            <span class="text-sm font-semibold text-foreground">{{ task.user.name }}</span>
                        </div>
                        <div v-else class="flex items-center gap-2 rounded-xl border border-dashed border-border/30 bg-muted/20 px-3 py-2">
                            <span class="text-xs font-medium text-muted-foreground">Unassigned</span>
                        </div>
                    </div>
                </div>
                
                <div
                    v-if="tasksByStatus[column.id]?.length === 0"
                    class="flex flex-1 items-center justify-center rounded-2xl border-2 border-dashed border-border/40 bg-gradient-to-br from-muted/30 to-muted/10 backdrop-blur-xl p-12 text-center transition-all duration-300 hover:border-primary/40"
                >
                    <div class="space-y-2">
                        <component :is="column.icon" class="mx-auto h-8 w-8 text-muted-foreground/30" />
                        <p class="text-sm font-semibold text-muted-foreground">Drop tasks here</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

