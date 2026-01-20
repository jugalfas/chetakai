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
                        <div class="h-12 w-12 rounded-xl bg-primary/10 flex items-center justify-center"><svg
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-shield h-6 w-6 text-primary"
                                aria-hidden="true">
                                <path
                                    d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z">
                                </path>
                            </svg></div>
                        <h1 class="text-4xl font-bold josefin-sans tracking-tight">Privacy
                            Policy</h1>
                    </div>
                    <div class="prose prose-slate dark:prose-invert max-w-none space-y-8 text-muted-foreground">
                        <section>
                            <h2 class="text-2xl font-bold text-foreground josefin-sans mb-4">1.
                                Information We Collect</h2>
                            <p>We collect information you provide directly to us when you
                                create an account, use our services, or communicate with us.</p>
                        </section>
                        <section>
                            <h2 class="text-2xl font-bold text-foreground josefin-sans mb-4">2.
                                Data Security</h2>
                            <p>We take reasonable measures to help protect information about
                                you from loss, theft, misuse, and unauthorized access.</p>
                        </section>
                        <section class="bg-muted/30 p-8 rounded-2xl border border-border">
                            <div class="flex items-start gap-4 text-foreground"><svg xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-lock h-6 w-6 text-accent shrink-0 mt-1" aria-hidden="true">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                <div>
                                    <h3 class="text-xl font-bold josefin-sans mb-2">Our
                                        Commitment</h3>
                                    <p class="text-sm text-muted-foreground">Chetak is built
                                        with privacy in mind. We never sell your data.</p>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </main>
    </StaticLayout>
</template>
