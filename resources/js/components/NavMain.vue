<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel class="font-semibold">Platform</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="urlIsActive(item.href, page.url)"
                    :tooltip="item.title"
                    class="group/item transition-all duration-200 hover:bg-primary/10 dark:hover:bg-primary/20"
                >
                    <Link :href="item.href" class="flex items-center gap-3">
                        <div class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary/20 ring-1 ring-primary/30 transition-all duration-200 group-hover/item:scale-110 group-hover/item:ring-primary/50 dark:bg-primary/30 dark:ring-primary/40 dark:group-hover/item:ring-primary/60">
                            <component
                                :is="item.icon"
                                class="h-4 w-4 text-primary dark:text-primary transition-transform duration-200 group-hover/item:scale-110"
                            />
                        </div>
                        <span class="font-medium">{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
