<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import {
    destroy as destroyComment,
    store as storeComment,
    update as updateComment,
} from '@/routes/comments';
import { attach, detach } from '@/routes/projects/users';
import {
    destroy as destroyTask,
    store as storeTask,
    update as updateTask,
} from '@/routes/tasks';
import { type BreadcrumbItem, type Project, type Task } from '@/types';
import { Form, Head, router, usePage } from '@inertiajs/vue3';
import {
    CheckCircle,
    ClipboardList,
    ListTodo,
    MessageSquare,
    Pencil,
    Plus,
    Search,
    Trash2,
    User as UserIcon,
    Users,
    LayoutGrid,
    Columns3,
    Calendar,
    Timer,
    FolderKanban,
    ChevronDown,
} from 'lucide-vue-next';
import { computed, ref, onMounted } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import KanbanBoard from '@/components/KanbanBoard.vue';
import SearchFilter from '@/components/SearchFilter.vue';
import QuickActionsFAB from '@/components/QuickActionsFAB.vue';
import TaskTimer from '@/components/TaskTimer.vue';
import CalendarView from '@/components/CalendarView.vue';
import { useToast } from '@/composables/useToast';
import { useKeyboardShortcuts } from '@/composables/useKeyboardShortcuts';
import { getInitials } from '@/composables/useInitials';

interface Props {
    project: Project;
    users: Array<{ id: number; name: string; experience?: string }>;
    assignedUsers?: Array<{ id: number; name: string; experience?: string }>;
    isTeamLead?: boolean;
    userExperience?: string;
    isAssignedToProject?: boolean;
    availableUsers?: Array<{ id: number; name: string }>;
}

const props = defineProps<Props>();
const page = usePage();

const createTaskModalOpen = ref(false);
const editTaskModalOpen = ref<number | null>(null);
const commentModalOpen = ref<number | null>(null);
const editCommentModalOpen = ref<number | null>(null);
const addUserModalOpen = ref(false);
const viewMode = ref<'grid' | 'kanban' | 'calendar'>('kanban');
const filteredTasks = ref<Task[]>([]);
const searchInputRef = ref<HTMLInputElement | null>(null);

const { success, error } = useToast();
const { register } = useKeyboardShortcuts();

const currentUser = computed(() => page.props.auth?.user);
const currentUserId = computed(() => currentUser.value?.id);
const userExperience = computed(() => props.userExperience || 'junior');
const isAssignedToProject = computed(() => props.isAssignedToProject || false);

const canChangeTaskStatus = (task: Task): boolean => {
    if (props.isTeamLead) {
        return true;
    }

    if (userExperience.value === 'senior') {
        return isAssignedToProject.value;
    }

    if (userExperience.value === 'middle') {
        const isAssignedToMe = task.user_id === currentUserId.value;
        const isAssignedToJunior = task.user?.experience === 'junior';
        return isAssignedToMe || isAssignedToJunior || false;
    }

    if (userExperience.value === 'junior') {
        return task.user_id === currentUserId.value;
    }

    return false;
};

const canAssignTaskUser = (task: Task): boolean => {
    if (props.isTeamLead) {
        return true;
    }

    if (userExperience.value === 'middle') {
        return true;
    }

    return false;
};

const canCommentOnTask = (task: Task): boolean => {
    if (props.isTeamLead) {
        return true;
    }

    if (userExperience.value === 'senior') {
        return isAssignedToProject.value;
    }

    if (userExperience.value === 'middle') {
        const isAssignedToMe = task.user_id === currentUserId.value;
        const isAssignedToJunior = task.user?.experience === 'junior';
        const hasCommented =
            task.comments?.some((c) => c.user_id === currentUserId.value) ||
            false;
        return isAssignedToMe || isAssignedToJunior || hasCommented;
    }

    if (userExperience.value === 'junior') {
        return task.user_id === currentUserId.value;
    }

    return false;
};

const canAssignToUser = (userId: number | string): boolean => {
    if (props.isTeamLead) {
        return true;
    }

    if (userExperience.value === 'middle') {
        const targetUser = props.users.find((u) => u.id === Number(userId));
        if (!targetUser) {
            return false;
        }
        const isSelf = Number(userId) === currentUserId.value;
        const isJunior = targetUser.experience === 'junior';
        return isSelf || isJunior;
    }

    return false;
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: props.project.title,
        href: `/projects/${props.project.id}`,
    },
];

const formattedDeadline = computed(() => {
    return new Date(props.project.deadline).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
});

const tasks = computed(() => props.project.tasks || []);
const displayTasks = computed(() => {
    return filteredTasks.value.length > 0 ? filteredTasks.value : tasks.value;
});

onMounted(() => {
    register({
        key: 'n',
        ctrl: true,
        action: () => {
            if (props.isTeamLead) {
                createTaskModalOpen.value = true;
            }
        },
    });
    
    register({
        key: 'k',
        ctrl: true,
        action: () => {
            searchInputRef.value?.focus();
        },
    });
    
    register({
        key: '1',
        ctrl: true,
        action: () => {
            viewMode.value = 'kanban';
        },
    });
    
    register({
        key: '2',
        ctrl: true,
        action: () => {
            viewMode.value = 'grid';
        },
    });
    
    register({
        key: '3',
        ctrl: true,
        action: () => {
            viewMode.value = 'calendar';
        },
    });
});

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'to_do':
            return ListTodo;
        case 'in_progress':
            return ClipboardList;
        case 'qa':
            return Search;
        case 'done':
            return CheckCircle;
        default:
            return ListTodo;
    }
};

const statusColors = {
    to_do: { light: '#A0AEC0', dark: '#718096', bg: 'bg-gray-100 dark:bg-gray-800', text: 'text-gray-700 dark:text-gray-300', hover: 'hover:shadow-gray-500/20' },
    in_progress: { light: '#C83FFF', dark: '#9B67FC', bg: 'bg-purple-100 dark:bg-purple-900/30', text: 'text-purple-700 dark:text-purple-300', hover: 'hover:shadow-[0_8px_32px_rgba(200,63,255,0.3)]' },
    qa: { light: '#2D72F8', dark: '#1851FF', bg: 'bg-blue-100 dark:bg-blue-900/30', text: 'text-blue-700 dark:text-blue-300', hover: 'hover:shadow-[0_8px_32px_rgba(45,114,248,0.3)]' },
    done: { light: '#FF8933', dark: '#DFB729', bg: 'bg-orange-100 dark:bg-orange-900/30', text: 'text-orange-700 dark:text-orange-300', hover: 'hover:shadow-[0_8px_32px_rgba(255,137,51,0.3)]' },
};

const getStatusColor = (status: string) => {
    return statusColors[status as keyof typeof statusColors]?.text || 'text-gray-500';
};

const getStatusGradientBar = (status: string) => {
    const colors = statusColors[status as keyof typeof statusColors];
    if (!colors) return 'bg-gray-400';
    return `bg-gradient-to-r from-[${colors.light}] to-[${colors.dark}]`;
};

const getAvatarUrl = (name: string) => {
    return `https://api.dicebear.com/7.x/avataaars/svg?seed=${encodeURIComponent(name)}`;
};

const getStatusLabel = (status: string) => {
    switch (status) {
        case 'to_do':
            return 'To Do';
        case 'in_progress':
            return 'In Progress';
        case 'qa':
            return 'QA';
        case 'done':
            return 'Done';
        default:
            return status;
    }
};

const deleteTask = (taskId: number) => {
    if (confirm('Are you sure you want to delete this task?')) {
        router.delete(destroyTask.url(taskId), {
            preserveScroll: true,
            onSuccess: () => {
                success('Task deleted successfully');
                router.reload({ preserveScroll: true });
            },
            onError: () => {
                error('Failed to delete task');
            },
        });
    }
};

const deleteComment = (commentId: number) => {
    if (confirm('Are you sure you want to delete this comment?')) {
        router.delete(destroyComment.url(commentId), {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ preserveScroll: true });
            },
        });
    }
};

const updateTaskStatus = (taskId: number, status: string) => {
    router.patch(
        updateTask.url(taskId),
        { status },
        {
            preserveScroll: true,
            onSuccess: () => {
                success('Task status updated successfully');
                router.reload({ preserveScroll: true });
            },
            onError: () => {
                error('Failed to update task status');
            },
        },
    );
};

const updateTaskUser = (taskId: number, userId: string | null) => {
    const data: { user_id?: number | null } = {};
    if (userId === '' || userId === null) {
        data.user_id = null;
    } else {
        data.user_id = parseInt(userId, 10);
    }

    router.patch(updateTask.url(taskId), data, {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ preserveScroll: true });
        },
    });
};

const removeUserFromProject = (userId: number) => {
    if (
        confirm('Are you sure you want to remove this user from the project?')
    ) {
        const url = detach.url({ project: props.project.id, user: userId });
        router.delete(url, {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ preserveScroll: true });
            },
            onError: (errors) => {
                console.error('Error removing user:', errors);
                alert('Failed to remove user from project. Please try again.');
            },
        });
    }
};
</script>

<template>
    <Head :title="project.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6"
        >
            <div class="group relative overflow-hidden rounded-3xl border-0 bg-gradient-to-br from-primary via-secondary to-accent p-8 shadow-2xl transition-all duration-500 hover:shadow-[0_25px_60px_rgba(99,102,241,0.3)]">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/20 via-secondary/20 to-accent/20 opacity-60 dark:opacity-40"></div>
                <div class="absolute -right-32 -top-32 h-80 w-80 rounded-full bg-primary/30 blur-3xl transition-all duration-700 group-hover:scale-110"></div>
                <div class="absolute -bottom-32 -left-32 h-80 w-80 rounded-full bg-accent/30 blur-3xl transition-all duration-700 group-hover:scale-110"></div>
                
                <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center gap-6">
                        <div class="relative flex h-48 w-48 shrink-0 items-center justify-center rounded-3xl bg-white/10 backdrop-blur-xl shadow-2xl ring-4 ring-white/20 transition-all duration-300 group-hover:scale-105 group-hover:rotate-3">
                            <FolderKanban class="h-24 w-24 text-white drop-shadow-2xl" />
                        </div>
                        
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-2">
                                <h1 class="text-4xl font-extrabold tracking-tight text-white drop-shadow-lg md:text-5xl">
                                    {{ project.title }}
                                </h1>
                                <p class="text-base text-white/90 drop-shadow-md">
                                    {{ project.description ? project.description.substring(0, 100) + '...' : 'No description' }}
                                </p>
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-4">
                                <div class="flex items-center gap-3 rounded-2xl border border-white/20 bg-white/10 backdrop-blur-xl p-4 shadow-lg ring-1 ring-white/20">
                                    <Avatar class="h-9 w-9 rounded-full ring-2 ring-white/50 shadow-xl">
                                        <AvatarImage :src="(project as any).team_lead ? getAvatarUrl((project as any).team_lead.name || 'Unknown') : getAvatarUrl('Unassigned')" :alt="(project as any).team_lead?.name || 'Unassigned'" class="h-full w-full object-cover" />
                                        <AvatarFallback class="bg-gradient-to-br from-white/90 via-primary/90 to-secondary/90 text-xs font-bold text-white">
                                            {{ (project as any).team_lead ? getInitials((project as any).team_lead.name) : '?' }}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-white/70">LED BY</span>
                                        <span class="font-bold text-white">{{ (project as any).team_lead?.name || 'Unassigned' }}</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2 rounded-2xl border border-white/20 bg-white/10 backdrop-blur-xl px-4 py-3 shadow-lg ring-1 ring-white/20">
                                    <ClipboardList class="h-5 w-5 text-white" />
                                    <span class="text-sm font-semibold text-white/90">Tasks:</span>
                                    <span class="rounded-full bg-white/20 px-3 py-1 text-sm font-bold text-white">{{ tasks.length }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="group relative overflow-hidden rounded-3xl border-2 border-border/50 bg-card/80 backdrop-blur-xl p-6 shadow-xl ring-1 ring-border/30 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:ring-primary/30">
                    <div class="absolute inset-0 bg-gradient-to-br from-card to-muted opacity-50"></div>
                    <div class="relative z-10">
                        <h2 class="mb-3 text-xl font-bold text-foreground">
                            Description
                        </h2>
                        <p class="whitespace-pre-wrap text-base leading-relaxed text-muted-foreground">
                            {{ project.description }}
                        </p>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-3xl border-2 border-border/50 bg-card/80 backdrop-blur-xl p-6 shadow-xl ring-1 ring-border/30 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:ring-primary/30">
                    <div class="absolute inset-0 bg-gradient-to-br from-card to-muted opacity-50"></div>
                    <div class="relative z-10">
                        <h2 class="mb-3 text-xl font-bold text-foreground">
                            Requirements
                        </h2>
                        <p class="whitespace-pre-wrap text-base leading-relaxed text-muted-foreground">
                            {{ project.requirements }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="group relative overflow-hidden rounded-3xl border-2 border-border/50 bg-gradient-to-br from-card via-card/90 to-muted/50 backdrop-blur-xl p-6 shadow-xl ring-1 ring-border/30 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:ring-primary/30">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-secondary/5 to-accent/5 opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    <div class="relative z-10">
                        <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-muted-foreground">
                            Estimated Time
                        </h3>
                        <p class="text-3xl font-extrabold text-foreground">
                            {{ project.estimated_time_of_completion }} <span class="text-xl font-medium text-muted-foreground">hours</span>
                        </p>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-3xl border-2 border-border/50 bg-gradient-to-br from-card via-card/90 to-muted/50 backdrop-blur-xl p-6 shadow-xl ring-1 ring-border/30 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:ring-primary/30">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-secondary/5 to-accent/5 opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    <div class="relative z-10">
                        <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-muted-foreground">
                            Deadline
                        </h3>
                        <p class="text-3xl font-extrabold text-foreground">
                            {{ formattedDeadline }}
                        </p>
                    </div>
                </div>
            </div>

            <div v-if="isTeamLead" class="flex flex-col gap-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary/20 via-secondary/20 to-accent/20 shadow-lg ring-2 ring-border/30">
                            <Users class="h-6 w-6 text-primary" />
                        </div>
                        <h2 class="text-2xl font-extrabold tracking-tight text-foreground">Project Members</h2>
                    </div>
                    <Dialog
                        v-if="availableUsers && availableUsers.length > 0"
                        v-model:open="addUserModalOpen"
                    >
                        <DialogTrigger as-child>
                            <Button class="rounded-xl border-2 border-primary/30 bg-gradient-to-r from-primary/10 via-secondary/10 to-accent/10 backdrop-blur-xl px-4 py-2 font-semibold shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl">
                                <Plus class="mr-2 h-4 w-4" />
                                Add User
                            </Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Add User to Project</DialogTitle>
                                <DialogDescription>
                                    Select a user to add to this project
                                </DialogDescription>
                            </DialogHeader>
                            <Form
                                :action="attach(project.id).url"
                                :method="attach(project.id).method"
                                :options="{ preserveScroll: true }"
                                @success="
                                    () => {
                                        addUserModalOpen = false;
                                        router.reload({ preserveScroll: true });
                                    }
                                "
                                v-slot="{ errors, processing }"
                                class="space-y-4"
                            >
                                <div class="grid gap-2">
                                    <Label for="user_id">User</Label>
                                    <select
                                        id="user_id"
                                        name="user_id"
                                        required
                                        class="h-9 w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none selection:bg-primary selection:text-primary-foreground file:text-foreground placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 md:text-sm dark:bg-input/30 dark:aria-invalid:ring-destructive/40"
                                    >
                                        <option value="">Select a user</option>
                                        <option
                                            v-for="user in availableUsers"
                                            :key="user.id"
                                            :value="user.id"
                                        >
                                            {{ user.name }}
                                        </option>
                                    </select>
                                    <InputError :message="errors.user_id" />
                                </div>
                                <DialogFooter>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        @click="addUserModalOpen = false"
                                    >
                                        Cancel
                                    </Button>
                                    <Button
                                        type="submit"
                                        :disabled="processing"
                                    >
                                        Add User
                                    </Button>
                                </DialogFooter>
                            </Form>
                        </DialogContent>
                    </Dialog>
                </div>
                <div
                    v-if="assignedUsers && assignedUsers.length > 0"
                    class="grid gap-4 rounded-3xl border-2 border-border/50 bg-gradient-to-br from-card/90 via-card/95 to-muted/20 backdrop-blur-xl p-6 shadow-xl ring-1 ring-border/30 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <div
                        v-for="user in assignedUsers"
                        :key="user.id"
                        class="group relative overflow-hidden rounded-2xl border-2 border-border/50 bg-gradient-to-br from-card via-card/95 to-muted/30 backdrop-blur-xl p-4 shadow-lg ring-1 ring-border/30 transition-all duration-500 hover:-translate-y-1 hover:scale-[1.02] hover:shadow-xl hover:ring-primary/30"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-secondary/5 to-accent/5 opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                        <div class="relative z-10 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <Avatar class="h-11 w-11 rounded-full ring-2 ring-border/50 shadow-lg transition-all duration-300 group-hover:scale-110 group-hover:ring-primary/50">
                                    <AvatarImage :src="getAvatarUrl(user.name)" :alt="user.name" class="h-full w-full object-cover" />
                                    <AvatarFallback class="bg-gradient-to-br from-primary via-secondary to-accent text-xs font-bold text-white">
                                        {{ getInitials(user.name) }}
                                    </AvatarFallback>
                                </Avatar>
                                <div class="flex flex-col">
                                    <span class="font-bold text-foreground">{{ user.name }}</span>
                                    <span v-if="user.experience" class="text-xs font-medium capitalize text-muted-foreground">
                                        {{ user.experience }}
                                    </span>
                                </div>
                            </div>
                            <Button
                                variant="ghost"
                                size="sm"
                                @click="removeUserFromProject(user.id)"
                                class="h-8 w-8 rounded-xl border border-destructive/30 bg-card/80 backdrop-blur-xl p-0 shadow-md transition-all duration-300 hover:scale-110 hover:shadow-lg"
                            >
                                <Trash2 class="h-4 w-4 text-destructive" />
                            </Button>
                        </div>
                    </div>
                </div>
                <div
                    v-else
                    class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-border/40 bg-gradient-to-br from-muted/30 to-muted/10 backdrop-blur-xl p-12 text-center shadow-lg"
                >
                    <Users class="mb-4 h-12 w-12 text-muted-foreground/30" />
                    <p class="text-base font-semibold text-muted-foreground">No users assigned to this project yet.</p>
                </div>
            </div>

            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold">Tasks</h2>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1 rounded-lg border-2 border-border/50 bg-card p-1 shadow-md">
                            <Button
                                @click="viewMode = 'kanban'"
                                variant="ghost"
                                size="sm"
                                :class="viewMode === 'kanban' ? 'bg-primary/10 text-primary' : ''"
                            >
                                <Columns3 class="h-4 w-4" />
                            </Button>
                            <Button
                                @click="viewMode = 'grid'"
                                variant="ghost"
                                size="sm"
                                :class="viewMode === 'grid' ? 'bg-primary/10 text-primary' : ''"
                            >
                                <LayoutGrid class="h-4 w-4" />
                            </Button>
                            <Button
                                @click="viewMode = 'calendar'"
                                variant="ghost"
                                size="sm"
                                :class="viewMode === 'calendar' ? 'bg-primary/10 text-primary' : ''"
                            >
                                <Calendar class="h-4 w-4" />
                            </Button>
                        </div>
                        <Dialog
                            v-if="isTeamLead"
                            v-model:open="createTaskModalOpen"
                        >
                            <DialogTrigger as-child>
                                <Button>
                                    <Plus class="h-4 w-4" />
                                    Create Task
                                </Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogHeader>
                                    <DialogTitle>Create New Task</DialogTitle>
                                    <DialogDescription>
                                        Add a new task to this project
                                    </DialogDescription>
                                </DialogHeader>
                                <Form
                                    :action="storeTask().url"
                                    :method="storeTask().method"
                                    :options="{ preserveScroll: true }"
                                    @success="
                                        () => {
                                            createTaskModalOpen = false;
                                            router.reload({ preserveScroll: true });
                                        }
                                    "
                                    v-slot="{ errors, processing }"
                                    class="space-y-4"
                                >
                                    <input
                                        type="hidden"
                                        name="project_id"
                                        :value="project.id"
                                    />
                                    <input
                                        type="hidden"
                                        name="status"
                                        value="to_do"
                                    />
                                    <div class="grid gap-2">
                                        <Label for="title">Title</Label>
                                        <Input
                                            id="title"
                                            name="title"
                                            required
                                            placeholder="Task title"
                                        />
                                        <InputError :message="errors.title" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="description">Description</Label>
                                        <textarea
                                            id="description"
                                            name="description"
                                            required
                                            rows="4"
                                            class="w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none selection:bg-primary selection:text-primary-foreground file:text-foreground placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 md:text-sm dark:bg-input/30 dark:aria-invalid:ring-destructive/40"
                                            placeholder="Task description"
                                        ></textarea>
                                        <InputError :message="errors.description" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="user_id">Assign To</Label>
                                        <select
                                            id="user_id"
                                            name="user_id"
                                            class="h-9 w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none selection:bg-primary selection:text-primary-foreground file:text-foreground placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 md:text-sm dark:bg-input/30 dark:aria-invalid:ring-destructive/40"
                                        >
                                            <option value="">Unassigned</option>
                                            <option
                                                v-for="user in users"
                                                :key="user.id"
                                                :value="user.id"
                                            >
                                                {{ user.name }}
                                            </option>
                                        </select>
                                        <InputError :message="errors.user_id" />
                                    </div>
                                    <DialogFooter>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            @click="createTaskModalOpen = false"
                                        >
                                            Cancel
                                        </Button>
                                        <Button
                                            type="submit"
                                            :disabled="processing"
                                        >
                                            Create Task
                                        </Button>
                                    </DialogFooter>
                                </Form>
                            </DialogContent>
                        </Dialog>
                    </div>
                </div>
                
                <SearchFilter
                    v-if="viewMode !== 'calendar'"
                    :tasks="tasks"
                    @filtered="(filtered) => filteredTasks = filtered"
                />

                <div
                    v-if="tasks.length === 0"
                    class="rounded-lg border p-8 text-center"
                >
                    <p class="text-muted-foreground">
                        No tasks assigned to this project yet.
                    </p>
                </div>

                <KanbanBoard
                    v-else-if="viewMode === 'kanban'"
                    :tasks="displayTasks"
                    :can-change-status="canChangeTaskStatus"
                    :can-comment="canCommentOnTask"
                />

                <CalendarView
                    v-else-if="viewMode === 'calendar'"
                    :tasks="tasks"
                    :project-deadline="project.deadline"
                />

                <div v-else-if="viewMode === 'grid'" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="task in displayTasks"
                        :key="task.id"
                        class="group relative flex flex-col gap-4 overflow-hidden rounded-3xl border-2 border-border/50 bg-gradient-to-br from-card via-card/95 to-muted/30 backdrop-blur-xl p-6 shadow-xl ring-1 ring-border/30 transition-all duration-500 hover:-translate-y-1 hover:scale-[1.02]"
                        :class="[
                            statusColors[task.status as keyof typeof statusColors]?.hover || '',
                            getPriorityClass(task),
                        ]"
                    >
                        <div
                            class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r"
                            :style="{
                                background: `linear-gradient(to right, ${statusColors[task.status as keyof typeof statusColors]?.light || '#A0AEC0'}, ${statusColors[task.status as keyof typeof statusColors]?.dark || '#718096'})`,
                            }"
                        ></div>
                        
                        <div class="absolute inset-0 bg-gradient-to-br from-card to-muted opacity-30 transition-opacity duration-500 group-hover:opacity-50"></div>
                        <div class="absolute -right-16 -top-16 h-40 w-40 rounded-full bg-gradient-to-br from-primary/10 to-secondary/10 blur-3xl opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                        
                        <div class="relative z-10 flex flex-1 flex-col gap-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex flex-1 items-center gap-3">
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br shadow-lg ring-2 ring-border/30 transition-all duration-300 group-hover:scale-110 group-hover:rotate-6"
                                        :class="statusColors[task.status as keyof typeof statusColors]?.bg || 'bg-gray-100 dark:bg-gray-800'"
                                    >
                                        <component
                                            :is="getStatusIcon(task.status)"
                                            :class="[
                                                'h-6 w-6 transition-transform duration-300 group-hover:scale-110',
                                                statusColors[task.status as keyof typeof statusColors]?.text || 'text-gray-500',
                                            ]"
                                        />
                                    </div>
                                    <h3 class="text-lg font-bold leading-tight text-foreground transition-colors duration-300 group-hover:text-primary">
                                        {{ task.title }}
                                    </h3>
                                </div>
                                
                                <div class="flex shrink-0 items-center gap-2">
                                    <div class="relative group/status">
                                        <select
                                            v-if="canChangeTaskStatus(task)"
                                            :value="task.status"
                                            @change="
                                                updateTaskStatus(
                                                    task.id,
                                                    ($event.target as HTMLSelectElement)
                                                        .value,
                                                )
                                            "
                                            class="appearance-none h-8 rounded-xl border-2 border-border/50 bg-card/80 backdrop-blur-xl pl-3 pr-8 py-1 text-xs font-bold shadow-md transition-all duration-300 hover:scale-105 hover:shadow-lg focus-visible:ring-2 focus-visible:ring-primary focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer"
                                            :class="[
                                                statusColors[task.status as keyof typeof statusColors]?.bg || 'bg-gray-100 dark:bg-gray-800',
                                                statusColors[task.status as keyof typeof statusColors]?.text || 'text-gray-700 dark:text-gray-300',
                                            ]"
                                        >
                                            <option value="to_do">To Do</option>
                                            <option value="in_progress">In Progress</option>
                                            <option value="qa">QA</option>
                                            <option value="done">Done</option>
                                        </select>
                                        <ChevronDown
                                            v-if="canChangeTaskStatus(task)"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 h-3.5 w-3.5 pointer-events-none transition-transform duration-200 group-hover/status:translate-y-0.5"
                                            :class="statusColors[task.status as keyof typeof statusColors]?.text || 'text-gray-700 dark:text-gray-300'"
                                        />
                                        <Badge
                                            v-else
                                            variant="secondary"
                                            class="shrink-0 px-3 py-1.5 text-xs font-bold shadow-lg transition-transform duration-300 hover:scale-105"
                                            :class="[
                                                statusColors[task.status as keyof typeof statusColors]?.bg || 'bg-gray-100 dark:bg-gray-800',
                                                statusColors[task.status as keyof typeof statusColors]?.text || 'text-gray-700 dark:text-gray-300',
                                            ]"
                                        >
                                            {{ getStatusLabel(task.status) }}
                                        </Badge>
                                    </div>
                                    
                                    <Dialog
                                        :open="editTaskModalOpen === task.id"
                                        @update:open="
                                            (value) =>
                                                (editTaskModalOpen = value
                                                    ? task.id
                                                    : null)
                                        "
                                    >
                                        <DialogTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                class="h-8 w-8 rounded-xl border-2 border-border/50 bg-card/80 backdrop-blur-xl p-0 shadow-md transition-all duration-300 hover:scale-110 hover:shadow-lg"
                                            >
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </DialogTrigger>
                                    <DialogContent>
                                        <DialogHeader>
                                            <DialogTitle>Edit Task</DialogTitle>
                                            <DialogDescription>
                                                Update task details
                                            </DialogDescription>
                                        </DialogHeader>
                                        <Form
                                            :action="updateTask(task.id).url"
                                            :method="updateTask(task.id).method"
                                            :options="{ preserveScroll: true }"
                                            @success="
                                                () => {
                                                    editTaskModalOpen = null;
                                                    router.reload({
                                                        preserveScroll: true,
                                                    });
                                                }
                                            "
                                            v-slot="{ errors, processing }"
                                            class="space-y-4"
                                        >
                                            <input
                                                type="hidden"
                                                name="_method"
                                                value="patch"
                                            />
                                            <div class="grid gap-2">
                                                <Label for="edit-title"
                                                    >Title</Label
                                                >
                                                <Input
                                                    id="edit-title"
                                                    name="title"
                                                    required
                                                    :default-value="task.title"
                                                    placeholder="Task title"
                                                />
                                                <InputError
                                                    :message="errors.title"
                                                />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="edit-description"
                                                    >Description</Label
                                                >
                                                <textarea
                                                    id="edit-description"
                                                    name="description"
                                                    required
                                                    rows="4"
                                                    class="w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none selection:bg-primary selection:text-primary-foreground file:text-foreground placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 md:text-sm dark:bg-input/30 dark:aria-invalid:ring-destructive/40"
                                                    placeholder="Task description"
                                                    >{{
                                                        task.description
                                                    }}</textarea
                                                >
                                                <InputError
                                                    :message="
                                                        errors.description
                                                    "
                                                />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="edit-user_id"
                                                    >Assign To</Label
                                                >
                                                <select
                                                    id="edit-user_id"
                                                    name="user_id"
                                                    class="h-9 w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none selection:bg-primary selection:text-primary-foreground file:text-foreground placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 md:text-sm dark:bg-input/30 dark:aria-invalid:ring-destructive/40"
                                                >
                                                    <option value="">
                                                        Unassigned
                                                    </option>
                                                    <option
                                                        v-for="user in users"
                                                        :key="user.id"
                                                        :value="user.id"
                                                        :selected="
                                                            task.user_id ===
                                                            user.id
                                                        "
                                                    >
                                                        {{ user.name }}
                                                    </option>
                                                </select>
                                                <InputError
                                                    :message="errors.user_id"
                                                />
                                            </div>
                                            <div class="grid gap-2">
                                                <Label for="edit-status"
                                                    >Status</Label
                                                >
                                                <select
                                                    id="edit-status"
                                                    name="status"
                                                    required
                                                    class="h-9 w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none selection:bg-primary selection:text-primary-foreground file:text-foreground placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 md:text-sm dark:bg-input/30 dark:aria-invalid:ring-destructive/40"
                                                >
                                                    <option
                                                        value="to_do"
                                                        :selected="
                                                            task.status ===
                                                            'to_do'
                                                        "
                                                    >
                                                        To Do
                                                    </option>
                                                    <option
                                                        value="in_progress"
                                                        :selected="
                                                            task.status ===
                                                            'in_progress'
                                                        "
                                                    >
                                                        In Progress
                                                    </option>
                                                    <option
                                                        value="qa"
                                                        :selected="
                                                            task.status === 'qa'
                                                        "
                                                    >
                                                        QA
                                                    </option>
                                                    <option
                                                        value="done"
                                                        :selected="
                                                            task.status ===
                                                            'done'
                                                        "
                                                    >
                                                        Done
                                                    </option>
                                                </select>
                                                <InputError
                                                    :message="errors.status"
                                                />
                                            </div>
                                            <DialogFooter>
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    @click="
                                                        editTaskModalOpen = null
                                                    "
                                                >
                                                    Cancel
                                                </Button>
                                                <Button
                                                    type="submit"
                                                    :disabled="processing"
                                                >
                                                    Update Task
                                                </Button>
                                            </DialogFooter>
                                        </Form>
                                    </DialogContent>
                                </Dialog>
                                        <Button
                                            v-if="isTeamLead"
                                            variant="ghost"
                                            size="sm"
                                            @click="deleteTask(task.id)"
                                            class="h-8 w-8 rounded-xl border-2 border-destructive/50 bg-card/80 backdrop-blur-xl p-0 shadow-md transition-all duration-300 hover:scale-110 hover:shadow-lg"
                                        >
                                            <Trash2 class="h-4 w-4 text-destructive" />
                                        </Button>
                                </div>
                            </div>
                            
                            <p class="line-clamp-3 text-sm leading-relaxed text-muted-foreground transition-colors duration-300 group-hover:text-foreground">
                                {{ task.description }}
                            </p>
                            
                            <TaskTimer :task-id="task.id" />
                            
                            <div
                                class="group/assignee relative flex items-center gap-3 rounded-xl border-2 p-3 transition-all duration-300"
                                :class="task.user ? 'border-border/50 bg-gradient-to-br from-muted/50 to-muted/30 backdrop-blur-xl shadow-md hover:scale-[1.02] hover:shadow-lg' : 'border-dashed border-border/30 bg-muted/20 hover:border-primary/40'"
                            >
                                <div v-if="task.user" class="relative">
                                    <Avatar class="h-10 w-10 rounded-full ring-2 ring-border/50 shadow-lg transition-all duration-300 group-hover/assignee:scale-110 group-hover/assignee:ring-primary/50">
                                        <AvatarImage :src="getAvatarUrl(task.user.name)" :alt="task.user.name" class="h-full w-full object-cover" />
                                        <AvatarFallback class="bg-gradient-to-br from-primary via-secondary to-accent text-xs font-bold text-white">
                                            {{ getInitials(task.user.name) }}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div class="absolute -bottom-0.5 -right-0.5 h-3.5 w-3.5 rounded-full bg-green-400 border-2 border-white shadow-lg"></div>
                                </div>
                                <div v-else class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-dashed border-border/50 bg-muted/30">
                                    <UserIcon class="h-5 w-5 text-muted-foreground/50" />
                                </div>
                                
                                <div class="flex flex-1 items-center gap-2">
                                    <select
                                        v-if="isTeamLead || canAssignTaskUser(task)"
                                        :value="task.user_id || ''"
                                        @change="
                                            updateTaskUser(
                                                task.id,
                                                ($event.target as HTMLSelectElement)
                                                    .value,
                                            )
                                        "
                                        class="h-8 flex-1 rounded-xl border-2 border-border/50 bg-card/80 backdrop-blur-xl px-3 py-1 text-xs font-semibold shadow-md transition-all duration-300 hover:scale-[1.02] hover:shadow-lg focus-visible:ring-2 focus-visible:ring-primary focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        <option value="">Unassigned</option>
                                        <option
                                            v-for="user in users"
                                            :key="user.id"
                                            :value="user.id"
                                            :selected="task.user_id === user.id"
                                            :disabled="!canAssignToUser(user.id)"
                                        >
                                            {{ user.name }}
                                        </option>
                                    </select>
                                    <div
                                        v-else
                                        class="flex flex-1 items-center gap-2"
                                    >
                                        <span v-if="task.user" class="font-semibold text-foreground">
                                            {{ task.user.name }}
                                        </span>
                                        <span v-else class="font-medium text-muted-foreground">
                                            Unassigned
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="relative z-10 flex items-center gap-2 mt-2">
                                <Dialog
                                    :open="commentModalOpen === task.id"
                                    @update:open="
                                        (value) =>
                                            (commentModalOpen = value
                                                ? task.id
                                                : null)
                                    "
                                >
                                    <DialogTrigger as-child>
                                        <Button
                                            v-if="canCommentOnTask(task)"
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 flex-1 rounded-xl border-2 border-border/50 bg-card/80 backdrop-blur-xl px-3 py-1.5 text-xs font-semibold shadow-md transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                                        >
                                            <MessageSquare class="mr-2 h-3.5 w-3.5" />
                                            <span>Comment</span>
                                            <span
                                                v-if="task.comments && task.comments.length > 0"
                                                class="ml-2 rounded-full bg-primary/20 px-1.5 py-0.5 text-xs font-bold"
                                            >
                                                {{ task.comments.length }}
                                            </span>
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <DialogHeader>
                                            <DialogTitle>Add Comment</DialogTitle>
                                            <DialogDescription>
                                                Leave a comment on this task
                                            </DialogDescription>
                                        </DialogHeader>
                                        <Form
                                            :action="storeComment().url"
                                            :method="storeComment().method"
                                            :options="{ preserveScroll: true }"
                                            @success="
                                                () => {
                                                    commentModalOpen = null;
                                                    router.reload({
                                                        preserveScroll: true,
                                                    });
                                                }
                                            "
                                            v-slot="{ errors, processing }"
                                            class="space-y-4"
                                        >
                                            <input
                                                type="hidden"
                                                name="task_id"
                                                :value="task.id"
                                            />
                                            <div class="grid gap-2">
                                                <Label for="text">Comment</Label>
                                                <Input
                                                    id="text"
                                                    name="text"
                                                    required
                                                    placeholder="Enter your comment"
                                                />
                                                <InputError
                                                    :message="errors.text"
                                                />
                                            </div>
                                            <DialogFooter>
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    @click="commentModalOpen = null"
                                                >
                                                    Cancel
                                                </Button>
                                                <Button
                                                    type="submit"
                                                    :disabled="processing"
                                                >
                                                    Add Comment
                                                </Button>
                                            </DialogFooter>
                                        </Form>
                                    </DialogContent>
                                </Dialog>
                            </div>
                            
                            <div
                                v-if="task.comments && task.comments.length > 0"
                                class="relative z-10 flex flex-col gap-3 rounded-2xl border-2 border-border/50 bg-gradient-to-br from-primary/5 via-secondary/5 to-accent/5 backdrop-blur-xl p-4 shadow-md"
                            >
                                <div class="flex items-center gap-2">
                                    <MessageSquare class="h-4 w-4 text-primary" />
                                    <h4 class="text-xs font-bold uppercase tracking-wide text-foreground">
                                        Comments ({{ task.comments.length }})
                                    </h4>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <div
                                        v-for="comment in task.comments"
                                        :key="comment.id"
                                        class="group/comment relative flex items-start justify-between gap-3 rounded-xl border border-border/30 bg-card/80 backdrop-blur-xl p-3 shadow-sm transition-all duration-300 hover:scale-[1.01] hover:shadow-md"
                                    >
                                        <div class="flex-1 space-y-1.5">
                                            <p class="text-sm font-medium leading-relaxed text-foreground">
                                                {{ comment.text }}
                                            </p>
                                            <div v-if="comment.user" class="flex items-center gap-2">
                                                <Avatar class="h-6 w-6 rounded-full ring-1 ring-border/50">
                                                    <AvatarImage :src="getAvatarUrl(comment.user.name)" :alt="comment.user.name" class="h-full w-full object-cover" />
                                                    <AvatarFallback class="bg-gradient-to-br from-primary via-secondary to-accent text-xs font-bold text-white">
                                                        {{ getInitials(comment.user.name) }}
                                                    </AvatarFallback>
                                                </Avatar>
                                                <p class="text-xs font-semibold text-muted-foreground">
                                                    {{ comment.user.name }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <Dialog
                                                :open="
                                                    editCommentModalOpen ===
                                                    comment.id
                                                "
                                                @update:open="
                                                    (value) =>
                                                        (editCommentModalOpen =
                                                            value
                                                                ? comment.id
                                                                : null)
                                                "
                                            >
                                                <DialogTrigger as-child>
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        class="h-6 w-6 p-0"
                                                    >
                                                        <Pencil class="h-3 w-3" />
                                                    </Button>
                                                </DialogTrigger>
                                                <DialogContent>
                                                    <DialogHeader>
                                                        <DialogTitle>
                                                            Edit Comment
                                                        </DialogTitle>
                                                        <DialogDescription>
                                                            Update your comment
                                                        </DialogDescription>
                                                    </DialogHeader>
                                                    <Form
                                                        :action="
                                                            updateComment(
                                                                comment.id,
                                                            ).url
                                                        "
                                                        :method="
                                                            updateComment(
                                                                comment.id,
                                                            ).method
                                                        "
                                                        :options="{
                                                            preserveScroll: true,
                                                        }"
                                                        @success="
                                                            () => {
                                                                editCommentModalOpen =
                                                                    null;
                                                                router.reload({
                                                                    preserveScroll: true,
                                                                });
                                                            }
                                                        "
                                                        v-slot="{
                                                            errors,
                                                            processing,
                                                        }"
                                                        class="space-y-4"
                                                    >
                                                        <input
                                                            type="hidden"
                                                            name="_method"
                                                            value="patch"
                                                        />
                                                        <div class="grid gap-2">
                                                            <Label
                                                                for="edit-comment-text"
                                                                >Comment</Label
                                                            >
                                                            <Input
                                                                id="edit-comment-text"
                                                                name="text"
                                                                required
                                                                :default-value="
                                                                    comment.text
                                                                "
                                                                placeholder="Enter your comment"
                                                            />
                                                            <InputError
                                                                :message="
                                                                    errors.text
                                                                "
                                                            />
                                                        </div>
                                                        <DialogFooter>
                                                            <Button
                                                                type="button"
                                                                variant="outline"
                                                                @click="
                                                                    editCommentModalOpen =
                                                                        null
                                                                "
                                                            >
                                                                Cancel
                                                            </Button>
                                                            <Button
                                                                type="submit"
                                                                :disabled="
                                                                    processing
                                                                "
                                                            >
                                                                Update Comment
                                                            </Button>
                                                        </DialogFooter>
                                                    </Form>
                                                </DialogContent>
                                            </Dialog>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                @click="deleteComment(comment.id)"
                                                class="h-7 w-7 rounded-lg border border-destructive/30 bg-card/80 backdrop-blur-xl p-0 shadow-sm transition-all duration-300 hover:scale-110 hover:shadow-md"
                                            >
                                                <Trash2
                                                    class="h-3.5 w-3.5 text-destructive"
                                                />
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <QuickActionsFAB
            :on-create-task="() => createTaskModalOpen = true"
            :on-add-user="() => addUserModalOpen = true"
            :on-search="() => searchInputRef?.focus()"
        />
        
        <Toast />
    </AppLayout>
</template>
