<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { urlIsActive } from '@/lib/utils';
import { show } from '@/routes/projects';
import { type Project } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Folder, FolderKanban, Sparkles } from 'lucide-vue-next';
import { computed } from 'vue';
import { getInitials } from '@/composables/useInitials';

const page = usePage();
const auth = computed(() => page.props.auth);
const user = computed(() => auth.value?.user);

const projects = computed(() => {
    const currentUser = user.value;
    if (!currentUser) {
        return [];
    }

    const assignedProjects: Project[] = currentUser.projects || [];
    const teamLeadProjects: Project[] =
        (currentUser as any).team_lead_projects ||
        (currentUser as any).teamLeadProjects ||
        [];

    const allProjects = [...assignedProjects, ...teamLeadProjects];
    const uniqueProjects = allProjects.filter(
        (project, index, self) =>
            index === self.findIndex((p) => p.id === project.id),
    );

    return uniqueProjects.sort((a, b) => a.title.localeCompare(b.title));
});

const getAvatarUrl = (name: string) => {
    return `https://api.dicebear.com/7.x/avataaars/svg?seed=${encodeURIComponent(name)}&backgroundColor=b6e3f4&clothingColor=4a90e2`;
};

const activeProjectsCount = computed(() => projects.value.length);

const motivationalMessage = computed(() => {
    const count = activeProjectsCount.value;
    if (count === 0) {
        return "Ready to start?";
    } else if (count === 1) {
        return "Keep up the great work!";
    } else {
        return "You're doing amazing!";
    }
});
</script>

<template>
    <div class="flex flex-col gap-3">
        <div class="relative mx-2 mt-4 mb-2 overflow-hidden rounded-2xl border border-border/50 bg-gradient-to-br from-primary/30 via-secondary/30 to-accent/30 p-4 shadow-xl transition-all duration-300 hover:shadow-2xl hover:from-primary/40 hover:via-secondary/40 hover:to-accent/40 group/card">
            <div class="absolute inset-0 bg-gradient-to-br from-primary/10 via-secondary/10 to-accent/10 opacity-60"></div>
            <div class="absolute -right-16 -top-16 h-32 w-32 rounded-full bg-primary/20 blur-2xl opacity-50 transition-opacity duration-500 group-hover/card:opacity-70"></div>
            <div class="absolute -bottom-16 -left-16 h-32 w-32 rounded-full bg-accent/20 blur-2xl opacity-50 transition-opacity duration-500 group-hover/card:opacity-70"></div>
            
            <div class="relative flex flex-col items-center gap-3">
                <div class="relative group/avatar">
                    <div class="absolute -inset-2 rounded-full bg-gradient-to-r from-primary via-secondary to-accent opacity-30 blur-lg group-hover/avatar:opacity-50 transition-opacity duration-300"></div>
                    <div class="relative h-20 w-20 rounded-full border-2 border-white/80 dark:border-primary/40 shadow-2xl ring-4 ring-primary/20 dark:ring-primary/30 transition-all duration-300 group-hover/avatar:scale-110 group-hover/avatar:rotate-3 group-hover/avatar:border-primary/60 group-hover/avatar:ring-primary/40 overflow-hidden bg-card/50 backdrop-blur-sm flex items-center justify-center">
                        <svg width="65" height="75" viewBox="0 0 80 90" class="fill-primary/75 dark:fill-primary/55 transition-all duration-300 group-hover/avatar:scale-110 group-hover/avatar:fill-primary/95 dark:group-hover/avatar:fill-primary/80" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="40" cy="22" r="15" />
                            <path d="M20 45 L20 70 C20 76, 24 81, 30 83 L40 87 L50 83 C56 81, 60 76, 60 70 L60 45 C60 41, 58 38, 55 37 L40 35 L25 37 C22 38, 20 41, 20 45 Z" />
                            <circle cx="33" cy="20" r="2" class="fill-card dark:fill-foreground opacity-90" />
                            <circle cx="47" cy="20" r="2" class="fill-card dark:fill-foreground opacity-90" />
                            <path d="M30 32 Q33 36, 40 32 Q47 36, 50 32" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" />
                        </svg>
                    </div>
                    <div class="absolute -bottom-1 -right-1 h-6 w-6 rounded-full bg-primary border-2 border-card shadow-xl flex items-center justify-center ring-2 ring-primary/40 transition-all duration-300 group-hover/avatar:scale-110 group-hover/avatar:rotate-12">
                        <Sparkles class="h-3.5 w-3.5 text-white animate-pulse" />
                    </div>
                </div>
                <div class="flex-1 text-center space-y-1">
                    <p class="font-bold text-sm text-foreground drop-shadow-sm leading-tight">
                        {{ motivationalMessage }}
                    </p>
                    <p v-if="activeProjectsCount > 0" class="text-xs font-semibold text-primary dark:text-primary/80 mt-1">
                        {{ activeProjectsCount }} {{ activeProjectsCount === 1 ? 'active project' : 'active projects' }}
                    </p>
                    <p v-else class="text-xs font-medium text-muted-foreground mt-1">
                        Create your first project
                    </p>
                </div>
            </div>
        </div>

        <SidebarGroup v-if="projects.length > 0" class="px-2 py-0">
            <SidebarGroupLabel class="flex items-center gap-2 font-semibold">
                <FolderKanban class="h-4 w-4 text-primary dark:text-primary" />
                Projects
            </SidebarGroupLabel>
            <SidebarGroupContent>
                <SidebarMenu>
                    <SidebarMenuItem v-for="project in projects" :key="project.id">
                        <SidebarMenuButton
                            as-child
                            :is-active="
                                urlIsActive(show.url(project.id).url, page.url)
                            "
                            :tooltip="project.title"
                            class="group/item transition-all duration-200 hover:bg-primary/10 dark:hover:bg-primary/20"
                        >
                            <Link :href="show(project.id).url" class="flex items-center gap-3">
                                <div class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary/20 ring-1 ring-primary/30 transition-all duration-200 group-hover/item:scale-110 group-hover/item:ring-primary/50 dark:bg-primary/30 dark:ring-primary/40 dark:group-hover/item:ring-primary/60">
                                    <Folder class="h-4 w-4 text-primary dark:text-primary transition-transform duration-200 group-hover/item:scale-110" />
                                </div>
                                <span class="truncate font-medium">{{ project.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroupContent>
        </SidebarGroup>
    </div>
</template>
