<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import {
    Briefcase,
    CheckCircle2,
    ClipboardList,
    Clock,
    ListTodo,
    Search,
    TrendingUp,
    Users,
    Zap,
    Activity,
    FolderKanban,
    Target,
    Gauge,
    Calendar,
    Sparkles,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import DonutChart from '@/components/charts/DonutChart.vue';
import BarChart from '@/components/charts/BarChart.vue';
import { getInitials } from '@/composables/useInitials';
import {
    getUserExperienceLabel,
    getUserExperienceColor,
    type UserExperience,
} from '@/types/UserExperience';

interface DashboardStats {
    total_projects: number;
    team_lead_projects: number;
    total_tasks: number;
    tasks_by_status: {
        to_do: number;
        in_progress: number;
        qa: number;
        done: number;
    };
    tasks_per_project?: Array<{ title: string; task_count: number }>;
    recent_activity?: Array<{
        id: number;
        title: string;
        status: string;
        project_title: string;
        updated_at: string;
    }>;
}

interface Props {
    stats: DashboardStats;
}

const props = defineProps<Props>();

const page = usePage();
const user = computed(() => page.props.auth.user);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const getExperienceBadgeVariant = (experience?: string) => {
    if (!experience) return 'secondary';
    switch (experience) {
        case 'senior':
            return 'default';
        case 'middle':
            return 'secondary';
        case 'junior':
            return 'outline';
        default:
            return 'secondary';
    }
};

const getRoleBadgeVariant = (role?: string) => {
    if (role === 'admin') return 'default';
    return 'secondary';
};

const getStatusBadgeColor = (status: string) => {
    switch (status) {
        case 'to_do':
            return 'bg-[#A0AEC0]/20 text-[#A0AEC0] dark:bg-[#718096]/20 dark:text-[#718096] border-[#A0AEC0]/30 dark:border-[#718096]/30';
        case 'in_progress':
            return 'bg-[#C83FFF]/20 text-[#C83FFF] dark:bg-[#9B67FC]/20 dark:text-[#9B67FC] border-[#C83FFF]/30 dark:border-[#9B67FC]/30';
        case 'qa':
            return 'bg-[#2D72F8]/20 text-[#2D72F8] dark:bg-[#1851FF]/20 dark:text-[#1851FF] border-[#2D72F8]/30 dark:border-[#1851FF]/30';
        case 'done':
            return 'bg-[#FF8933]/20 text-[#FF8933] dark:bg-[#DFB729]/20 dark:text-[#DFB729] border-[#FF8933]/30 dark:border-[#DFB729]/30';
        default:
            return 'bg-[#A0AEC0]/20 text-[#A0AEC0] dark:bg-[#718096]/20 dark:text-[#718096] border-[#A0AEC0]/30 dark:border-[#718096]/30';
    }
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

const pastelColors = {
    primary: '#6C8AE4',
    secondary: '#8BC7B8',
    accent: '#F4A6B9',
    success: '#A7D7C5',
    warning: '#FAD7A0',
    danger: '#F2B5B5',
};

const darkColors = {
    primary: '#4F6BED',
    secondary: '#5FB3A2',
    accent: '#E58FB1',
};

const taskStatusChartData = computed(() => {
    const { to_do, in_progress, qa, done } = props.stats.tasks_by_status;
    return [
        {
            label: 'To Do',
            value: to_do,
            color: 'rgb(148, 163, 184)',
        },
        {
            label: 'In Progress',
            value: in_progress,
            color: 'rgb(99, 102, 241)',
        },
        {
            label: 'QA',
            value: qa,
            color: 'rgb(139, 92, 246)',
        },
        {
            label: 'Done',
            value: done,
            color: 'rgb(236, 72, 153)',
        },
    ].filter((item) => item.value > 0);
});

const tasksPerProjectData = computed(() => {
    if (!props.stats.tasks_per_project || props.stats.tasks_per_project.length === 0) {
        return [];
    }
    const colors = [
        'bg-primary dark:bg-primary',
        'bg-secondary dark:bg-secondary',
        'bg-accent dark:bg-accent',
        'bg-amber-500 dark:bg-amber-400',
        'bg-cyan-500 dark:bg-cyan-400',
        'bg-primary dark:bg-primary',
        'bg-secondary dark:bg-secondary',
        'bg-accent dark:bg-accent',
    ];
    return props.stats.tasks_per_project.map((project, index) => ({
        label: project.title.length > 15 ? project.title.substring(0, 15) + '...' : project.title,
        value: project.task_count,
        color: colors[index % colors.length],
        fullTitle: project.title,
    }));
});

const statCards = computed(() => [
    {
        title: 'Assigned Projects',
        value: props.stats.total_projects,
        subtitle: 'PROJECTS',
        icon: Briefcase,
        color: 'text-primary',
        bgGradient: 'from-primary/10 via-primary/5 to-secondary/5',
        borderColor: 'border-primary/30',
        iconBg: 'bg-primary/20',
    },
    {
        title: 'Leading Projects',
        value: props.stats.team_lead_projects,
        subtitle: 'AS LEAD',
        icon: Users,
        color: 'text-secondary',
        bgGradient: 'from-secondary/10 via-secondary/5 to-accent/5',
        borderColor: 'border-secondary/30',
        iconBg: 'bg-secondary/20',
    },
    {
        title: 'Total Tasks',
        value: props.stats.total_tasks,
        subtitle: 'TASKS',
        icon: ClipboardList,
        color: 'text-accent',
        bgGradient: 'from-accent/10 via-accent/5 to-primary/5',
        borderColor: 'border-accent/30',
        iconBg: 'bg-accent/20',
    },
]);

const taskStatusCards = computed(() => [
    {
        title: 'TO DO',
        value: props.stats.tasks_by_status.to_do,
        icon: ListTodo,
        color: 'text-[#A0AEC0] dark:text-[#718096]',
        bgColor: 'bg-[#A0AEC0]/10 dark:bg-[#718096]/10',
        borderColor: 'border-[#A0AEC0]/30 dark:border-[#718096]/30',
    },
    {
        title: 'IN PROGRESS',
        value: props.stats.tasks_by_status.in_progress,
        icon: Activity,
        color: 'text-[#C83FFF] dark:text-[#9B67FC]',
        bgColor: 'bg-[#C83FFF]/10 dark:bg-[#9B67FC]/10',
        borderColor: 'border-[#C83FFF]/30 dark:border-[#9B67FC]/30',
    },
    {
        title: 'IN QA',
        value: props.stats.tasks_by_status.qa,
        icon: Search,
        color: 'text-[#2D72F8] dark:text-[#1851FF]',
        bgColor: 'bg-[#2D72F8]/10 dark:bg-[#1851FF]/10',
        borderColor: 'border-[#2D72F8]/30 dark:border-[#1851FF]/30',
    },
    {
        title: 'COMPLETED',
        value: props.stats.tasks_by_status.done,
        icon: CheckCircle2,
        color: 'text-[#FF8933] dark:text-[#DFB729]',
        bgColor: 'bg-[#FF8933]/10 dark:bg-[#DFB729]/10',
        borderColor: 'border-[#FF8933]/30 dark:border-[#DFB729]/30',
    },
]);

const completionRate = computed(() => {
    const total = props.stats.total_tasks;
    if (total === 0) return 0;
    return Math.round((props.stats.tasks_by_status.done / total) * 100);
});

const productivityScore = computed(() => {
    const { to_do, in_progress, qa, done } = props.stats.tasks_by_status;
    const total = to_do + in_progress + qa + done;
    if (total === 0) return 50;
    const weighted = (done * 1.0 + qa * 0.75 + in_progress * 0.5) / total;
    return Math.round(weighted * 100);
});

const getAvatarUrl = (name: string, style: string = 'avataaars', options: Record<string, string> = {}) => {
    const baseUrl = `https://api.dicebear.com/7.x/${style}/svg`;
    const defaultOptions: Record<string, string> = {
        seed: encodeURIComponent(name),
        backgroundColor: 'b6e3f4',
        clothingColor: '4a90e2',
    };
    const params = new URLSearchParams({
        ...defaultOptions,
        ...options,
    });
    return `${baseUrl}?${params.toString()}`;
};

const teamAvatars = computed(() => {
    const teamConfigs = [
        { 
            name: 'Emma Professional Woman Smile', 
            style: 'personas', 
            options: { 
                backgroundColor: 'f0f9ff',
                clothing: 'blazerAndShirt',
            } 
        },
        { 
            name: 'Sarah Happy Creative Designer', 
            style: 'avataaars', 
            options: { 
                backgroundColor: 'fef3c7',
                clothingColor: '7c3aed',
            } 
        },
        { 
            name: 'Alex Smile Developer Happy', 
            style: 'avataaars', 
            options: { 
                backgroundColor: 'e0f2fe',
                clothingColor: '0369a1',
            } 
        },
    ];
    return teamConfigs.map((config) => ({
        name: config.name,
        url: getAvatarUrl(config.name, config.style, config.options),
        initials: getInitials(config.name),
    }));
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6 lg:p-8">
            <div
                class="group relative overflow-hidden rounded-3xl border-0 p-6 shadow-2xl transition-all duration-500 md:p-8 bg-gradient-to-r from-primary via-secondary to-accent"
            >
                <div class="absolute inset-0 bg-gradient-to-br from-primary/20 via-secondary/20 to-accent/20 opacity-40"></div>
                
                <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center gap-6">
                        <div class="relative flex items-end gap-3">
                            <div class="flex flex-col gap-2">
                                <div
                                    v-for="(teamMember, index) in teamAvatars"
                                    :key="teamMember.name"
                                    class="group/avatar relative h-10 w-10 rounded-full ring-2 ring-white/80 shadow-xl transition-all duration-300 hover:scale-125 hover:z-10 hover:ring-white overflow-hidden"
                                    :style="{ animationDelay: `${index * 100}ms` }"
                                >
                                    <Avatar class="h-full w-full">
                                        <AvatarImage :src="teamMember.url" :alt="teamMember.name" class="h-full w-full object-cover" />
                                        <AvatarFallback class="bg-gradient-to-br from-primary/80 via-secondary/80 to-accent/80 text-xs font-bold text-white">
                                            {{ teamMember.initials }}
                                        </AvatarFallback>
                                    </Avatar>
                                </div>
                            </div>
                            <div class="relative">
                                <div class="relative h-20 w-20 overflow-hidden rounded-full border-2 border-white/90 shadow-2xl ring-2 ring-white/50 transition-all duration-300 group-hover:scale-105 bg-gradient-to-br from-white/95 via-primary/90 to-secondary/90 flex items-center justify-center backdrop-blur-sm">
                                    <span class="text-2xl font-bold text-primary drop-shadow-lg">
                                        {{ getInitials(user.name) }}
                                    </span>
                                </div>
                                <div class="absolute bottom-0 right-0 h-4 w-4 rounded-full bg-cyan-400 border-2 border-white shadow-lg animate-pulse"></div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-3">
                                <h1 class="text-3xl font-bold tracking-tight text-white drop-shadow-lg md:text-4xl">
                                    Welcome back, {{ user.name.split(' ')[0] }}!
                                </h1>
                                <div class="flex items-center gap-2">
                                    <TrendingUp class="h-5 w-5 text-white/80 drop-shadow-md icon-float" />
                                    <Activity class="h-5 w-5 text-white/80 drop-shadow-md icon-pulse" />
                                    <CheckCircle2 class="h-5 w-5 text-white/80 drop-shadow-md icon-bounce" />
                                </div>
                            </div>
                            <p class="text-sm text-white/90 drop-shadow-md">
                                Here's what's happening with your projects today.
                            </p>
                        </div>
                    </div>
                    <div
                        v-if="user.experience"
                        class="rounded-2xl border border-white/30 bg-white/30 backdrop-blur-xl px-6 py-4 shadow-xl transition-all duration-300 hover:bg-white/40 hover:shadow-2xl"
                    >
                        <Badge
                            :variant="getExperienceBadgeVariant(user.experience)"
                            class="text-sm font-bold px-4 py-2 shadow-lg bg-white/40 backdrop-blur-sm text-primary border-white/50"
                        >
                            {{ getUserExperienceLabel(user.experience as UserExperience) }}
                        </Badge>
                    </div>
                </div>
                <div
                    class="absolute -right-32 -top-32 h-80 w-80 rounded-full bg-primary/30 blur-3xl transition-all duration-700 group-hover:scale-110"
                ></div>
                <div
                    class="absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-accent/30 blur-3xl transition-all duration-700 group-hover:scale-110"
                ></div>
            </div>

            <div class="grid gap-6 sm:grid-cols-3">
                <div
                    v-for="(card, index) in statCards"
                    :key="card.title"
                    class="group relative overflow-hidden rounded-3xl border-2 border-border/50 bg-card p-6 shadow-xl ring-2 ring-border/30 transition-all duration-500 hover:scale-[1.03] hover:shadow-2xl hover:ring-primary/50 dark:ring-border/40 dark:hover:ring-primary/60"
                    :class="[card.borderColor]"
                    :style="{ animationDelay: `${index * 100}ms` }"
                >
                    <div
                        class="absolute inset-0 bg-gradient-to-br opacity-0 transition-opacity duration-500 group-hover:opacity-100"
                        :class="card.bgGradient"
                    ></div>
                    <div class="absolute -right-12 -top-12 h-32 w-32 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 blur-2xl opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    <div class="absolute -bottom-12 -left-12 h-32 w-32 rounded-full bg-gradient-to-br from-accent/20 to-primary/20 blur-2xl opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div class="flex-1">
                            <div class="mb-2 text-xs font-bold uppercase tracking-wider" :class="card.color">
                                {{ card.title }}
                            </div>
                            <div
                                class="text-5xl font-extrabold tracking-tight text-foreground transition-all duration-300 group-hover:scale-110 group-hover:bg-gradient-to-r group-hover:from-primary group-hover:via-secondary group-hover:to-accent group-hover:bg-clip-text group-hover:text-transparent"
                            >
                                {{ card.value }}
                            </div>
                            <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                {{ card.subtitle }}
                            </div>
                        </div>
                        <div
                            class="relative flex h-16 w-16 items-center justify-center rounded-2xl shadow-2xl transition-all duration-500 group-hover:scale-125 group-hover:rotate-12 group-hover:shadow-[0_20px_40px_rgba(0,0,0,0.2)] ring-2 ring-border/30"
                            :class="card.iconBg"
                        >
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-white/50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                            <component :is="card.icon" :class="['h-8 w-8 transition-transform duration-300 group-hover:scale-110', card.color]" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="(card, index) in taskStatusCards"
                    :key="card.title"
                    class="group relative overflow-hidden rounded-2xl border-2 bg-card p-4 shadow-lg ring-1 ring-border/30 transition-all duration-500 hover:scale-[1.05] hover:shadow-xl"
                    :class="[card.borderColor]"
                    :style="{ animationDelay: `${index * 50}ms` }"
                >
                    <div
                        class="absolute inset-0 opacity-0 transition-opacity duration-500 group-hover:opacity-100"
                        :class="card.bgColor"
                    ></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl shadow-md transition-all duration-300 group-hover:scale-110 group-hover:rotate-6"
                                :class="card.bgColor"
                            >
                                <component :is="card.icon" :class="['h-5 w-5 transition-transform duration-300 group-hover:scale-110', card.color]" />
                            </div>
                            <div>
                                <div class="text-xs font-bold uppercase tracking-wide" :class="card.color">
                                    {{ card.title }}
                                </div>
                                <div class="text-2xl font-extrabold text-foreground">
                                    {{ card.value }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div class="group relative overflow-hidden rounded-3xl border-2 border-border/50 bg-card p-6 shadow-xl ring-2 ring-border/30 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:ring-primary/50 dark:ring-border/40 dark:hover:ring-primary/60">
                    <div class="absolute -right-16 -top-16 h-40 w-40 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 blur-3xl opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    <div class="absolute -bottom-16 -left-16 h-40 w-40 rounded-full bg-gradient-to-br from-accent/20 to-primary/20 blur-3xl opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    <div class="relative z-10 mb-6 flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary/30 to-secondary/30 shadow-lg ring-2 ring-primary/30 transition-all duration-300 group-hover:scale-110 group-hover:rotate-6">
                            <Target class="h-6 w-6 text-primary transition-transform duration-300 group-hover:scale-110" />
                        </div>
                        <h2 class="text-xl font-bold text-foreground">Completion Progress</h2>
                    </div>
                    <div class="relative z-10 flex flex-col items-center justify-center space-y-4">
                        <div class="relative flex items-center justify-center">
                            <svg class="transform -rotate-90" width="160" height="160">
                                <defs>
                                    <linearGradient id="completionGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" style="stop-color:#2B6CB0;stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:#4C51BF;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#805AD5;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <circle
                                    cx="80"
                                    cy="80"
                                    r="60"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="12"
                                    class="text-muted/20"
                                />
                                <circle
                                    cx="80"
                                    cy="80"
                                    r="60"
                                    fill="none"
                                    stroke="url(#completionGradient)"
                                    stroke-width="12"
                                    stroke-linecap="round"
                                    :stroke-dasharray="`${2 * Math.PI * 60 * (completionRate / 100)} ${2 * Math.PI * 60}`"
                                    class="transition-all duration-1000 ease-out"
                                />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <div class="text-4xl font-extrabold text-foreground">{{ completionRate }}%</div>
                                <div class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Tasks Complete</div>
                            </div>
                        </div>
                        <div class="grid w-full grid-cols-2 gap-3">
                            <div class="rounded-xl border-2 border-border/30 bg-muted/50 p-3 text-center shadow-md">
                                <div class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Completed</div>
                                <div class="mt-1 text-2xl font-extrabold text-foreground">{{ stats.tasks_by_status.done }}</div>
                            </div>
                            <div class="rounded-xl border-2 border-border/30 bg-muted/50 p-3 text-center shadow-md">
                                <div class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Remaining</div>
                                <div class="mt-1 text-2xl font-extrabold text-foreground">{{ stats.total_tasks - stats.tasks_by_status.done }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-3xl border-2 border-border/50 bg-card p-6 shadow-xl ring-2 ring-border/30 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:ring-secondary/50 dark:ring-border/40 dark:hover:ring-secondary/60">
                    <div class="absolute -right-16 -top-16 h-40 w-40 rounded-full bg-gradient-to-br from-secondary/20 to-accent/20 blur-3xl opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    <div class="absolute -bottom-16 -left-16 h-40 w-40 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 blur-3xl opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    <div class="relative z-10 mb-6 flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-secondary/30 to-accent/30 shadow-lg ring-2 ring-secondary/30 transition-all duration-300 group-hover:scale-110 group-hover:rotate-6">
                            <Gauge class="h-6 w-6 text-secondary transition-transform duration-300 group-hover:scale-110" />
                        </div>
                        <h2 class="text-xl font-bold text-foreground">Productivity Score</h2>
                    </div>
                    <div class="relative z-10 flex flex-col items-center justify-center space-y-4">
                        <div class="relative flex items-center justify-center">
                            <svg width="200" height="140" viewBox="0 0 200 140" class="overflow-visible">
                                <defs>
                                    <linearGradient id="productivityGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#2B6CB0;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#805AD5;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <polygon
                                    points="100,20 180,120 20,120"
                                    fill="url(#productivityGradient)"
                                    opacity="0.2"
                                    class="transition-all duration-500"
                                />
                                <line
                                    x1="20"
                                    y1="120"
                                    :x2="20 + (160 * (productivityScore / 100))"
                                    :y2="120 - (100 * (productivityScore / 100))"
                                    stroke="#10B981"
                                    stroke-width="3"
                                    stroke-dasharray="5,5"
                                    class="transition-all duration-1000"
                                />
                                <circle
                                    :cx="20 + (160 * (productivityScore / 100))"
                                    :cy="120 - (100 * (productivityScore / 100))"
                                    r="6"
                                    fill="#10B981"
                                    class="animate-pulse"
                                />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pt-8">
                                <div class="text-4xl font-extrabold text-foreground">{{ productivityScore }}%</div>
                            </div>
                        </div>
                        <div class="w-full space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold text-muted-foreground">Level:</span>
                                <span class="font-bold text-foreground">{{ productivityScore >= 75 ? 'Excellent' : productivityScore >= 50 ? 'Good' : productivityScore >= 25 ? 'Fair' : 'Needs Improvement' }}</span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-primary via-secondary to-accent transition-all duration-1000"
                                    :style="{ width: `${productivityScore}%` }"
                                ></div>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-muted-foreground">{{ stats.tasks_by_status.in_progress + stats.tasks_by_status.qa + stats.tasks_by_status.done }}/{{ stats.total_tasks }} Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div
                    class="group relative overflow-hidden rounded-3xl border-2 border-border/50 bg-card p-6 shadow-xl ring-2 ring-border/30 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:ring-primary/50 dark:ring-border/40 dark:hover:ring-primary/60"
                >
                    <div class="absolute -right-16 -top-16 h-40 w-40 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 blur-3xl opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    <div class="absolute -bottom-16 -left-16 h-40 w-40 rounded-full bg-gradient-to-br from-accent/20 to-primary/20 blur-3xl opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    <div class="relative z-10 mb-6 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary/30 to-secondary/30 shadow-lg ring-2 ring-primary/30 transition-all duration-300 group-hover:scale-110 group-hover:rotate-6"
                            >
                                <TrendingUp class="h-6 w-6 text-primary transition-transform duration-300 group-hover:scale-110" />
                            </div>
                            <h2 class="text-xl font-bold text-foreground">
                                Task Distribution
                            </h2>
                        </div>
                    </div>
                    <div v-if="taskStatusChartData.length > 0" class="space-y-6">
                        <div class="flex items-center justify-center">
                            <DonutChart :data="taskStatusChartData" :size="200" :stroke-width="28" />
                        </div>
                        <div class="relative z-10 grid grid-cols-2 gap-3">
                            <div
                                v-for="item in taskStatusChartData"
                                :key="item.label"
                                class="group/item relative flex items-center gap-3 rounded-2xl border-2 border-border/30 bg-muted/50 p-4 shadow-md transition-all duration-300 hover:scale-105 hover:border-primary/40 hover:bg-muted hover:shadow-xl"
                            >
                                <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-primary/5 via-secondary/5 to-accent/5 opacity-0 transition-opacity duration-300 group-hover/item:opacity-100"></div>
                                <div
                                    class="relative h-5 w-5 rounded-full shadow-lg ring-2 ring-white/50 transition-all duration-300 group-hover/item:scale-150 group-hover/item:ring-4"
                                    :style="{ backgroundColor: item.color }"
                                ></div>
                                <div class="relative flex flex-1 flex-col">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-muted-foreground transition-colors duration-300 group-hover/item:text-foreground">
                                        {{ item.label }}
                                    </div>
                                    <div class="text-lg font-extrabold text-foreground transition-transform duration-300 group-hover/item:scale-110">
                                        {{ item.value }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        v-else
                        class="flex h-[300px] flex-col items-center justify-center text-center"
                    >
                        <ClipboardList class="mb-3 h-16 w-16 text-muted-foreground/30" />
                        <p class="text-sm font-medium text-muted-foreground">No tasks yet</p>
                        <p class="mt-1 text-xs text-muted-foreground/70">Start by creating a task</p>
                    </div>
                </div>

                <div
                    class="group relative overflow-hidden rounded-3xl border-2 border-border/50 bg-card p-6 shadow-xl ring-2 ring-border/30 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:ring-secondary/50 dark:ring-border/40 dark:hover:ring-secondary/60"
                >
                    <div class="absolute -right-16 -top-16 h-40 w-40 rounded-full bg-gradient-to-br from-secondary/20 to-accent/20 blur-3xl opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    <div class="absolute -bottom-16 -left-16 h-40 w-40 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 blur-3xl opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    <div class="relative z-10 mb-6 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-secondary/30 to-accent/30 shadow-lg ring-2 ring-secondary/30 transition-all duration-300 group-hover:scale-110 group-hover:rotate-6"
                            >
                                <Calendar class="h-6 w-6 text-secondary transition-transform duration-300 group-hover:scale-110" />
                            </div>
                            <h2 class="text-xl font-bold text-foreground">Task Timeline</h2>
                        </div>
                    </div>
                    <div class="relative z-10">
                        <div class="grid grid-cols-7 gap-2">
                            <div
                                v-for="day in 35"
                                :key="day"
                                class="group/day relative aspect-square rounded-lg border-2 border-border/30 bg-muted/30 p-2 text-center shadow-sm transition-all duration-300 hover:scale-110 hover:border-primary/50 hover:bg-primary/10 hover:shadow-md"
                                :class="{
                                    'bg-primary/20 border-primary/50': day % 7 === 0 || day % 7 === 6,
                                    'bg-secondary/20 border-secondary/50': [9, 12, 14, 18, 21, 24, 26, 27, 28, 33, 34, 35].includes(day),
                                    'bg-accent/20 border-accent/50': [10, 15, 20, 25, 30].includes(day),
                                }"
                            >
                                <div class="text-xs font-bold text-foreground">{{ day }}</div>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-center gap-4 text-xs">
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded bg-primary/20 border border-primary/50"></div>
                                <span class="font-medium text-muted-foreground">High Activity</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded bg-accent/20 border border-accent/50"></div>
                                <span class="font-medium text-muted-foreground">Medium Activity</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded bg-muted/30 border border-border/30"></div>
                                <span class="font-medium text-muted-foreground">Low Activity</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">

                <div
                    class="group relative overflow-hidden rounded-3xl border-2 border-border/50 bg-card p-6 shadow-xl ring-2 ring-border/30 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:ring-accent/50 dark:ring-border/40 dark:hover:ring-accent/60 lg:col-span-1"
                >
                    <div class="absolute -right-16 -top-16 h-40 w-40 rounded-full bg-gradient-to-br from-accent/20 to-primary/20 blur-3xl opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    <div class="absolute -bottom-16 -left-16 h-40 w-40 rounded-full bg-gradient-to-br from-secondary/20 to-accent/20 blur-3xl opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    <div class="relative z-10 mb-6 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-accent/30 to-secondary/30 shadow-lg ring-2 ring-accent/30 transition-all duration-300 group-hover:scale-110 group-hover:rotate-6 dark:bg-accent/40 dark:ring-accent/40"
                            >
                                <FolderKanban class="h-6 w-6 text-accent transition-transform duration-300 group-hover:scale-110 dark:text-accent" />
                            </div>
                            <h2 class="text-xl font-bold text-foreground">
                                Tasks Per Project
                            </h2>
                        </div>
                    </div>
                    <div v-if="tasksPerProjectData.length > 0" class="space-y-4">
                        <BarChart :data="tasksPerProjectData" :height="200" :show-values="true" />
                        <div class="relative z-10 space-y-2">
                            <div
                                v-for="(project, index) in tasksPerProjectData.slice(0, 5)"
                                :key="index"
                                class="group/item relative flex items-center justify-between rounded-xl border-2 border-border/30 bg-muted/50 px-4 py-3 shadow-md transition-all duration-300 hover:scale-[1.02] hover:border-primary/40 hover:bg-muted hover:shadow-xl dark:bg-muted/50 dark:hover:bg-muted/80 dark:hover:border-primary/50"
                                :title="project.fullTitle"
                            >
                                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-primary/5 via-secondary/5 to-accent/5 opacity-0 transition-opacity duration-300 group-hover/item:opacity-100"></div>
                                <span class="relative truncate text-sm font-bold text-foreground transition-colors duration-300 group-hover/item:text-primary">
                                    {{ project.fullTitle }}
                                </span>
                                <Badge variant="secondary" class="relative ml-2 font-bold shadow-md transition-transform duration-300 group-hover/item:scale-110">
                                    {{ project.value }}
                                </Badge>
                            </div>
                        </div>
                    </div>
                    <div
                        v-else
                        class="flex h-[300px] flex-col items-center justify-center text-center"
                    >
                        <FolderKanban class="mb-3 h-16 w-16 text-muted-foreground/30" />
                        <p class="text-sm font-medium text-muted-foreground">No project tasks yet</p>
                        <p class="mt-1 text-xs text-muted-foreground/70">Tasks will appear here</p>
                    </div>
                </div>

                <div
                    class="group relative overflow-hidden rounded-3xl border-2 border-border/50 bg-card p-6 shadow-xl ring-2 ring-border/30 transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:ring-secondary/50 dark:ring-border/40 dark:hover:ring-secondary/60 lg:col-span-1"
                >
                    <div class="absolute -right-16 -top-16 h-40 w-40 rounded-full bg-gradient-to-br from-secondary/20 to-accent/20 blur-3xl opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    <div class="absolute -bottom-16 -left-16 h-40 w-40 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 blur-3xl opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                    <div class="relative z-10 mb-6 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-secondary/30 to-primary/30 shadow-lg ring-2 ring-secondary/30 transition-all duration-300 group-hover:scale-110 group-hover:rotate-6 dark:bg-secondary/40 dark:ring-secondary/40"
                            >
                                <Activity class="h-6 w-6 text-secondary transition-transform duration-300 group-hover:scale-110 dark:text-secondary" />
                            </div>
                            <h2 class="text-xl font-bold text-foreground">
                                Recent Activity
                            </h2>
                        </div>
                    </div>
                    <div v-if="stats.recent_activity && stats.recent_activity.length > 0" class="relative z-10 space-y-3">
                        <div
                            v-for="(activity, index) in stats.recent_activity.slice(0, 6)"
                            :key="activity.id"
                            class="group/item relative flex items-start gap-3 rounded-2xl border-2 border-border/30 bg-muted/50 p-4 shadow-md transition-all duration-300 hover:scale-[1.02] hover:border-primary/40 hover:bg-muted hover:shadow-xl dark:bg-muted/50 dark:hover:bg-muted/80 dark:hover:border-primary/50"
                            :style="{ animationDelay: `${index * 50}ms` }"
                        >
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-primary/5 via-secondary/5 to-accent/5 opacity-0 transition-opacity duration-300 group-hover/item:opacity-100"></div>
                            <div
                                class="relative mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-primary/30 to-secondary/30 shadow-lg ring-2 ring-primary/30 transition-all duration-300 group-hover/item:scale-110 group-hover/item:rotate-6 dark:bg-primary/40 dark:ring-primary/40"
                            >
                                <Clock class="h-5 w-5 text-primary transition-transform duration-300 group-hover/item:scale-110 dark:text-primary" />
                            </div>
                            <div class="relative flex-1 space-y-1.5">
                                <div class="flex items-start justify-between gap-2">
                                    <p
                                        class="line-clamp-1 text-sm font-bold text-foreground transition-colors duration-300 group-hover/item:text-primary"
                                    >
                                        {{ activity.title }}
                                    </p>
                                    <Badge
                                        :class="getStatusBadgeColor(activity.status)"
                                        class="shrink-0 text-xs font-semibold shadow-md transition-transform duration-300 group-hover/item:scale-110"
                                    >
                                        {{ getStatusLabel(activity.status) }}
                                    </Badge>
                                </div>
                                <p class="text-xs font-medium text-muted-foreground">{{ activity.project_title }}</p>
                                <p class="text-xs text-muted-foreground/70">{{ activity.updated_at }}</p>
                            </div>
                        </div>
                    </div>
                    <div
                        v-else
                        class="flex h-[300px] flex-col items-center justify-center text-center"
                    >
                        <Activity class="mb-3 h-16 w-16 text-muted-foreground/30" />
                        <p class="text-sm font-medium text-muted-foreground">No recent activity</p>
                        <p class="mt-1 text-xs text-muted-foreground/70">Updates will appear here</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes iconFloat {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-4px) rotate(2deg);
    }
}

@keyframes iconPulse {
    0%, 100% {
        transform: scale(1);
        opacity: 0.8;
    }
    50% {
        transform: scale(1.1);
        opacity: 1;
    }
}

@keyframes iconBounce {
    0%, 100% {
        transform: translateY(0) scale(1);
    }
    25% {
        transform: translateY(-3px) scale(1.05);
    }
    75% {
        transform: translateY(2px) scale(0.95);
    }
}

.icon-float {
    animation: iconFloat 3s ease-in-out infinite;
}

.icon-pulse {
    animation: iconPulse 2s ease-in-out infinite;
    animation-delay: 0.5s;
}

.icon-bounce {
    animation: iconBounce 2.5s ease-in-out infinite;
    animation-delay: 1s;
}

[style*='animation-delay'] {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
