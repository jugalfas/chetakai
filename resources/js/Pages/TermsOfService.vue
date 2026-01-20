<script setup>
import StaticLayout from '@/Layouts/StaticLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Privacy" />

    <StaticLayout>
        <main>
            <div class="pt-32 pb-20 px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl mx-auto">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="h-12 w-12 rounded-xl bg-primary/10 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-file-text h-6 w-6 text-primary"
                                aria-hidden="true">
                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                                <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                                <path d="M10 9H8"></path>
                                <path d="M16 13H8"></path>
                                <path d="M16 17H8"></path>
                            </svg>
                        </div>
                        <h1 class="text-4xl font-bold josefin-sans tracking-tight">Terms of Service</h1>
                    </div>
                    <div class="prose prose-slate dark:prose-invert max-w-none space-y-8 text-muted-foreground">
                        <section>
                            <h2 class="text-2xl font-bold text-foreground josefin-sans mb-4">1.
                                Acceptance of Terms</h2>
                            <p>By accessing Chetak, you agree to be bound by these Terms of Service.</p>
                        </section>
                        <section class="bg-muted/30 p-8 rounded-2xl border border-border">
                            <div class="flex items-start gap-4 text-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-scale h-6 w-6 text-accent shrink-0 mt-1" aria-hidden="true">
                                    <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"></path>
                                    <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"></path>
                                    <path d="M7 21h10"></path>
                                    <path d="M12 3v18"></path>
                                    <path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"></path>
                                </svg>
                                <div>
                                    <h3 class="text-xl font-bold josefin-sans mb-2">Responsible Usage</h3>
                                    <p class="text-sm text-muted-foreground">Users must comply with Instagram's
                                        Community Guidelines.</p>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </main>
    </StaticLayout>
</template>
