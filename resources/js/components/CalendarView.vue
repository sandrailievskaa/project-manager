<script setup lang="ts">
import { computed, ref } from 'vue';
import { ChevronLeft, ChevronRight, Calendar as CalendarIcon } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';

interface Task {
    id: number;
    title: string;
    status: string;
    created_at: string;
    updated_at: string;
    project?: { title: string };
}

interface Props {
    tasks: Task[];
    projectDeadline?: string;
}

const props = defineProps<Props>();

const currentDate = ref(new Date());
const selectedDate = ref<Date | null>(null);

const month = computed(() => currentDate.value.getMonth());
const year = computed(() => currentDate.value.getFullYear());

const daysInMonth = computed(() => {
    return new Date(year.value, month.value + 1, 0).getDate();
});

const firstDayOfMonth = computed(() => {
    return new Date(year.value, month.value, 1).getDay();
});

const calendarDays = computed(() => {
    const days: (Date | null)[] = [];
    
    for (let i = 0; i < firstDayOfMonth.value; i++) {
        days.push(null);
    }
    
    for (let day = 1; day <= daysInMonth.value; day++) {
        days.push(new Date(year.value, month.value, day));
    }
    
    return days;
});

const tasksByDate = computed(() => {
    const grouped: Record<string, Task[]> = {};
    
    props.tasks.forEach(task => {
        const date = new Date(task.updated_at);
        const dateKey = `${date.getFullYear()}-${date.getMonth()}-${date.getDate()}`;
        if (!grouped[dateKey]) {
            grouped[dateKey] = [];
        }
        grouped[dateKey].push(task);
    });
    
    return grouped;
});

const getTasksForDate = (date: Date | null): Task[] => {
    if (!date) return [];
    const dateKey = `${date.getFullYear()}-${date.getMonth()}-${date.getDate()}`;
    return tasksByDate.value[dateKey] || [];
};

const isToday = (date: Date | null): boolean => {
    if (!date) return false;
    const today = new Date();
    return date.toDateString() === today.toDateString();
};

const isDeadline = (date: Date | null): boolean => {
    if (!date || !props.projectDeadline) return false;
    const deadline = new Date(props.projectDeadline);
    return date.toDateString() === deadline.toDateString();
};

const isPastDeadline = (date: Date | null): boolean => {
    if (!date || !props.projectDeadline) return false;
    const deadline = new Date(props.projectDeadline);
    return date < deadline && date.toDateString() !== new Date().toDateString();
};

const previousMonth = () => {
    currentDate.value = new Date(year.value, month.value - 1, 1);
};

const nextMonth = () => {
    currentDate.value = new Date(year.value, month.value + 1, 1);
};

const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
</script>

<template>
    <div class="rounded-2xl border-2 border-border/50 bg-card p-6 shadow-xl">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <CalendarIcon class="h-5 w-5 text-primary" />
                <h3 class="text-xl font-bold">Calendar</h3>
            </div>
            <div class="flex items-center gap-2">
                <Button
                    @click="previousMonth"
                    variant="ghost"
                    size="sm"
                    class="h-8 w-8 p-0"
                >
                    <ChevronLeft class="h-4 w-4" />
                </Button>
                <span class="min-w-[120px] text-center font-semibold">
                    {{ monthNames[month] }} {{ year }}
                </span>
                <Button
                    @click="nextMonth"
                    variant="ghost"
                    size="sm"
                    class="h-8 w-8 p-0"
                >
                    <ChevronRight class="h-4 w-4" />
                </Button>
            </div>
        </div>
        
        <div class="grid grid-cols-7 gap-2">
            <div
                v-for="dayName in dayNames"
                :key="dayName"
                class="text-center text-xs font-bold text-muted-foreground"
            >
                {{ dayName }}
            </div>
            
            <div
                v-for="(date, index) in calendarDays"
                :key="index"
                @click="date ? selectedDate = date : null"
                :class="[
                    'group relative min-h-[80px] rounded-xl border-2 p-2 transition-all duration-200',
                    date
                        ? 'cursor-pointer border-border/50 bg-card hover:scale-105 hover:border-primary/50 hover:shadow-md'
                        : 'border-transparent',
                    isToday(date) ? 'ring-2 ring-primary' : '',
                    isDeadline(date) ? 'bg-red-50 dark:bg-red-950/30 border-red-300 dark:border-red-800' : '',
                    isPastDeadline(date) ? 'bg-red-100 dark:bg-red-900/50' : '',
                    selectedDate && date && selectedDate.toDateString() === date.toDateString()
                        ? 'ring-4 ring-primary/50 scale-105'
                        : '',
                ]"
            >
                <div v-if="date" class="flex flex-col gap-1">
                    <span
                        :class="[
                            'text-sm font-semibold',
                            isToday(date) ? 'text-primary' : 'text-foreground',
                        ]"
                    >
                        {{ date.getDate() }}
                    </span>
                    <div class="flex flex-wrap gap-1">
                        <Badge
                            v-for="task in getTasksForDate(date).slice(0, 2)"
                            :key="task.id"
                            :class="[
                                'h-1.5 w-1.5 rounded-full p-0',
                                task.status === 'done' ? 'bg-green-500' : '',
                                task.status === 'in_progress' ? 'bg-orange-500' : '',
                                task.status === 'qa' ? 'bg-blue-500' : '',
                                task.status === 'to_do' ? 'bg-gray-500' : '',
                            ]"
                        />
                        <span
                            v-if="getTasksForDate(date).length > 2"
                            class="text-xs text-muted-foreground"
                        >
                            +{{ getTasksForDate(date).length - 2 }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div
            v-if="selectedDate"
            class="mt-6 rounded-xl border-2 border-border/50 bg-muted/50 p-4"
        >
            <h4 class="mb-3 font-semibold">
                Tasks for {{ selectedDate.toLocaleDateString() }}
            </h4>
            <div class="space-y-2">
                <div
                    v-for="task in getTasksForDate(selectedDate)"
                    :key="task.id"
                    class="rounded-lg border border-border/50 bg-card p-3"
                >
                    <p class="font-semibold">{{ task.title }}</p>
                    <p v-if="task.project" class="text-xs text-muted-foreground">
                        {{ task.project.title }}
                    </p>
                </div>
                <p
                    v-if="getTasksForDate(selectedDate).length === 0"
                    class="text-sm text-muted-foreground"
                >
                    No tasks for this date
                </p>
            </div>
        </div>
    </div>
</template>

