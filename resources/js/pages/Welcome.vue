<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { Briefcase, Users, Zap, Target, TrendingUp, CheckCircle2 } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Project Manager" />
    <div
        class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6 text-[#1b1b18] dark:from-slate-950 dark:via-blue-950 dark:to-indigo-950 dark:text-[#EDEDEC]"
    >
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -right-32 -top-32 h-96 w-96 rounded-full bg-gradient-to-br from-blue-400/30 via-indigo-400/30 to-purple-400/30 blur-3xl animate-pulse-slow"></div>
            <div class="absolute -bottom-32 -left-32 h-96 w-96 rounded-full bg-gradient-to-br from-purple-400/30 via-pink-400/30 to-orange-400/30 blur-3xl animate-pulse-slow" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/2 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-gradient-to-br from-cyan-400/20 via-blue-400/20 to-indigo-400/20 blur-3xl animate-pulse-slow" style="animation-delay: 2s;"></div>
        </div>

        <header class="absolute top-0 right-0 z-50 w-full p-6 text-sm lg:p-8">
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="group relative inline-block overflow-hidden rounded-xl border border-primary/20 bg-white/80 backdrop-blur-xl px-5 py-1.5 text-sm leading-normal font-medium text-[#1b1b18] shadow-lg transition-all duration-300 hover:scale-105 hover:border-primary/40 hover:bg-white hover:shadow-xl dark:border-primary/30 dark:bg-slate-900/80 dark:text-[#EDEDEC] dark:hover:border-primary/50"
                >
                    <span class="relative z-10 flex items-center gap-2">
                        <Briefcase class="h-4 w-4 transition-transform duration-300 group-hover:rotate-12" />
                        Dashboard
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-primary/10 via-secondary/10 to-accent/10 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="group relative inline-block overflow-hidden rounded-xl border border-transparent bg-white/60 backdrop-blur-xl px-5 py-1.5 text-sm leading-normal font-medium text-[#1b1b18] transition-all duration-300 hover:scale-105 hover:border-primary/30 hover:bg-white/90 hover:shadow-lg dark:bg-slate-900/60 dark:text-[#EDEDEC] dark:hover:border-primary/40"
                    >
                        <span class="relative z-10">Log in</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-primary/5 to-secondary/5 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="group relative inline-block overflow-hidden rounded-xl border border-primary/20 bg-white/80 backdrop-blur-xl px-5 py-1.5 text-sm leading-normal font-medium text-[#1b1b18] shadow-lg transition-all duration-300 hover:scale-105 hover:border-primary/40 hover:bg-white hover:shadow-xl dark:border-primary/30 dark:bg-slate-900/80 dark:text-[#EDEDEC] dark:hover:border-primary/50"
                    >
                        <span class="relative z-10">Register</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-primary/10 via-secondary/10 to-accent/10 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                    </Link>
                </template>
            </nav>
        </header>

        <main class="relative z-10 w-full max-w-5xl text-center">
            <div class="mb-12 flex items-center justify-center gap-4">
                <div class="flex -space-x-4">
                    <div class="group relative h-16 w-16 rounded-full bg-gradient-to-br from-blue-400 via-cyan-400 to-blue-500 ring-4 ring-white/80 shadow-2xl transition-all duration-300 hover:scale-110 hover:z-10 dark:ring-slate-900/80">
                        <div class="flex h-full w-full items-center justify-center text-2xl">🚀</div>
                        <div class="absolute -bottom-1 -right-1 h-5 w-5 rounded-full bg-green-400 border-2 border-white shadow-lg animate-pulse"></div>
                    </div>
                    <div class="group relative h-16 w-16 rounded-full bg-gradient-to-br from-purple-400 via-pink-400 to-purple-500 ring-4 ring-white/80 shadow-2xl transition-all duration-300 hover:scale-110 hover:z-10 dark:ring-slate-900/80" style="animation-delay: 0.1s;">
                        <div class="flex h-full w-full items-center justify-center text-2xl">✨</div>
                        <div class="absolute -bottom-1 -right-1 h-5 w-5 rounded-full bg-blue-400 border-2 border-white shadow-lg animate-pulse" style="animation-delay: 0.5s;"></div>
                    </div>
                    <div class="group relative h-16 w-16 rounded-full bg-gradient-to-br from-orange-400 via-amber-400 to-orange-500 ring-4 ring-white/80 shadow-2xl transition-all duration-300 hover:scale-110 hover:z-10 dark:ring-slate-900/80" style="animation-delay: 0.2s;">
                        <div class="flex h-full w-full items-center justify-center text-2xl">💼</div>
                        <div class="absolute -bottom-1 -right-1 h-5 w-5 rounded-full bg-yellow-400 border-2 border-white shadow-lg animate-pulse" style="animation-delay: 1s;"></div>
                    </div>
                    <div class="group relative h-16 w-16 rounded-full bg-gradient-to-br from-indigo-400 via-blue-400 to-indigo-500 ring-4 ring-white/80 shadow-2xl transition-all duration-300 hover:scale-110 hover:z-10 dark:ring-slate-900/80" style="animation-delay: 0.3s;">
                        <div class="flex h-full w-full items-center justify-center text-2xl">👥</div>
                        <div class="absolute -bottom-1 -right-1 h-5 w-5 rounded-full bg-purple-400 border-2 border-white shadow-lg animate-pulse" style="animation-delay: 1.5s;"></div>
                    </div>
                </div>
            </div>

            <div
                class="group relative overflow-hidden rounded-3xl bg-white/90 backdrop-blur-2xl p-8 shadow-2xl ring-1 ring-white/50 transition-all duration-500 hover:shadow-[0_25px_60px_rgba(0,0,0,0.15)] lg:p-16 dark:bg-slate-900/90 dark:ring-white/10"
            >
                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-secondary/5 to-accent/5 opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                <div class="absolute -right-20 -top-20 h-40 w-40 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 blur-2xl transition-all duration-700 group-hover:scale-150"></div>
                <div class="absolute -bottom-20 -left-20 h-40 w-40 rounded-full bg-gradient-to-br from-accent/20 to-primary/20 blur-2xl transition-all duration-700 group-hover:scale-150"></div>

                <div class="relative z-10">
                    <div class="mb-6 flex items-center justify-center gap-3">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-primary via-secondary to-accent shadow-xl ring-4 ring-primary/20 transition-all duration-300 group-hover:scale-110 group-hover:rotate-6">
                            <Briefcase class="h-8 w-8 text-white" />
                        </div>
                        <h1
                            class="bg-gradient-to-r from-primary via-secondary to-accent bg-clip-text text-5xl font-bold text-transparent lg:text-7xl"
                        >
                            Project Manager
                        </h1>
                    </div>
                    <p
                        class="mb-8 text-lg text-[#706f6c] lg:text-2xl dark:text-[#A1A09A]"
                    >
                        Organize your projects, manage tasks, and collaborate with your team.
                    </p>

                    <div class="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-4">
                        <div class="group/item flex flex-col items-center gap-2 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 p-4 shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl dark:from-blue-950/50 dark:to-indigo-950/50">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-400 to-indigo-500 shadow-lg transition-transform duration-300 group-hover/item:rotate-12">
                                <Target class="h-6 w-6 text-white" />
                            </div>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Projects</span>
                        </div>
                        <div class="group/item flex flex-col items-center gap-2 rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 p-4 shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl dark:from-purple-950/50 dark:to-pink-950/50">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-purple-400 to-pink-500 shadow-lg transition-transform duration-300 group-hover/item:rotate-12">
                                <CheckCircle2 class="h-6 w-6 text-white" />
                            </div>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Tasks</span>
                        </div>
                        <div class="group/item flex flex-col items-center gap-2 rounded-2xl bg-gradient-to-br from-orange-50 to-amber-50 p-4 shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl dark:from-orange-950/50 dark:to-amber-950/50">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-orange-400 to-amber-500 shadow-lg transition-transform duration-300 group-hover/item:rotate-12">
                                <Users class="h-6 w-6 text-white" />
                            </div>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Team</span>
                        </div>
                        <div class="group/item flex flex-col items-center gap-2 rounded-2xl bg-gradient-to-br from-cyan-50 to-blue-50 p-4 shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl dark:from-cyan-950/50 dark:to-blue-950/50">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-400 to-blue-500 shadow-lg transition-transform duration-300 group-hover/item:rotate-12">
                                <TrendingUp class="h-6 w-6 text-white" />
                            </div>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Analytics</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="login()"
                            class="group relative overflow-hidden rounded-xl border-0 bg-gradient-to-r from-primary via-secondary to-accent px-8 py-4 text-sm leading-normal font-semibold text-white shadow-2xl transition-all duration-300 hover:scale-105 hover:shadow-[0_20px_40px_rgba(99,102,241,0.4)]"
                        >
                            <span class="relative z-10 flex items-center justify-center gap-2">
                                <Zap class="h-5 w-5 transition-transform duration-300 group-hover:rotate-12" />
                                Get Started
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-secondary via-accent to-primary opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                        </Link>
                        <Link
                            v-else
                            :href="dashboard()"
                            class="group relative overflow-hidden rounded-xl border-0 bg-gradient-to-r from-primary via-secondary to-accent px-8 py-4 text-sm leading-normal font-semibold text-white shadow-2xl transition-all duration-300 hover:scale-105 hover:shadow-[0_20px_40px_rgba(99,102,241,0.4)]"
                        >
                            <span class="relative z-10 flex items-center justify-center gap-2">
                                <Briefcase class="h-5 w-5 transition-transform duration-300 group-hover:rotate-12" />
                                Go to Dashboard
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-secondary via-accent to-primary opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                        </Link>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
@keyframes pulse-slow {
    0%, 100% {
        opacity: 0.3;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(1.1);
    }
}
.animate-pulse-slow {
    animation: pulse-slow 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
