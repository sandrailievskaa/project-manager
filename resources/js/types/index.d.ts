import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    experience?: string;
    created_at: string;
    updated_at: string;
    projects?: Project[];
    team_lead_projects?: Project[];
    teamLeadProjects?: Project[];
}

export interface Project {
    id: number;
    title: string;
    description: string;
    requirements: string;
    estimated_time_of_completion: number;
    deadline: string;
    user_id: number;
    created_at: string;
    updated_at: string;
    tasks?: Task[];
}

export interface Task {
    id: number;
    title: string;
    description: string;
    project_id: number;
    user_id: number | null;
    status: string;
    created_at: string;
    updated_at: string;
    user?: User;
    comments?: Comment[];
}

export interface Comment {
    id: number;
    text: string;
    task_id: number;
    user_id: number;
    created_at: string;
    updated_at: string;
    user?: User;
}

export type BreadcrumbItemType = BreadcrumbItem;
