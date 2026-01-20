<script setup>
import { Head } from '@inertiajs/vue3';
import StaticLayout from '@/Layouts/StaticLayout.vue';
</script>

<template>
    <Head title="Welcome" />

    <StaticLayout>
        <section class="relative pt-32 pb-16 lg:pt-48 lg:pb-32">
            <div class="absolute inset-0 -z-10 overflow-hidden">
                <div
                    class="absolute top-0 left-1/2 -translate-x-1/2 w-[300px] sm:w-[1000px] h-[300px] sm:h-[600px] bg-primary/10 blur-[60px] sm:blur-[120px] rounded-full opacity-50">
                </div>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-muted/50 border border-border mb-6 sm:mb-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
                    <span class="relative flex h-2 w-2"><span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent opacity-75"></span><span
                            class="relative inline-flex rounded-full h-2 w-2 bg-accent"></span></span><span
                        class="text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Automating
                        10k+ posts daily</span>
                </div>
                <h1
                    class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight mb-6 sm:mb-8 animate-in fade-in slide-in-from-bottom-8 duration-700 delay-100 josefin-sans leading-tight">
                    Automate Your Inspiration with <span class="text-accent">Chetak</span></h1>
                <p
                    class="max-w-2xl mx-auto text-base sm:text-xl text-muted-foreground mb-8 sm:mb-10 animate-in fade-in slide-in-from-bottom-8 duration-700 delay-200">
                    The professional dashboard for quote management and Instagram automation. Elevate your brand with
                    beautifully scheduled content.</p>
                <div
                    class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 animate-in fade-in slide-in-from-bottom-8 duration-700 delay-300">
                    <a href="/register" class="w-full sm:w-auto"><button
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border border-primary-border min-h-10 rounded-md w-full sm:w-auto h-12 sm:h-14 px-8 sm:px-10 bg-primary text-primary-foreground hover:bg-primary/90 font-bold uppercase tracking-widest text-xs sm:text-sm shadow-xl shadow-primary/20 group">Start
                            Free Trial<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-arrow-right ml-2 h-4 w-4 sm:h-5 sm:w-5 group-hover:translate-x-1 transition-transform"
                                aria-hidden="true">
                                <path d="M5 12h14"></path>
                                <path d="m12 5 7 7-7 7"></path>
                            </svg></button></a><button
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border [border-color:var(--button-outline)] shadow-xs active:shadow-none min-h-10 rounded-md w-full sm:w-auto h-12 sm:h-14 px-8 sm:px-10 font-bold uppercase tracking-widest text-xs sm:text-sm border-border hover:bg-muted/50">Watch
                        Demo</button>
                </div>
                <div class="mt-12 sm:mt-20 relative animate-in fade-in zoom-in-95 duration-1000 delay-500">
                    <div
                        class="absolute -inset-1 bg-gradient-to-r from-primary/20 via-accent/20 to-primary/20 rounded-2xl blur-xl opacity-50">
                    </div>
                    <div
                        class="relative rounded-xl sm:rounded-2xl border border-border bg-card shadow-2xl overflow-hidden">
                        <img alt="Dashboard Preview" class="w-full aspect-video object-cover opacity-90"
                            src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&amp;w=2426&amp;auto=format&amp;fit=crop">
                    </div>
                </div>
            </div>
        </section>
        <section id="features" class="py-16 sm:py-24 bg-muted/30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 sm:mb-16">
                    <h2 class="text-2xl sm:text-3xl lg:text-5xl font-bold tracking-tight mb-4 josefin-sans">Everything
                        you need to scale</h2>
                    <p class="text-muted-foreground text-sm sm:text-lg px-4">Powerful tools designed for creators and
                        agencies.</p>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    <div
                        class="rounded-xl border text-card-foreground bg-card border-border hover:border-accent/50 transition-colors shadow-sm group">
                        <div class="p-6 pt-6 px-6 sm:pt-8 sm:px-8 pb-6 sm:pb-8">
                            <div
                                class="h-10 w-10 sm:h-12 sm:w-12 rounded-lg sm:rounded-xl bg-primary/10 flex items-center justify-center mb-4 sm:mb-6 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-panels-top-left h-5 w-5 sm:h-6 sm:w-6 text-primary"
                                    aria-hidden="true">
                                    <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                    <path d="M3 9h18"></path>
                                    <path d="M9 21V9"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3">Inspiration Center</h3>
                            <p class="text-sm sm:text-base text-muted-foreground leading-relaxed">Organize thousands of
                                quotes with powerful categorization and search tools.</p>
                        </div>
                    </div>
                    <div
                        class="rounded-xl border text-card-foreground bg-card border-border hover:border-accent/50 transition-colors shadow-sm group">
                        <div class="p-6 pt-6 px-6 sm:pt-8 sm:px-8 pb-6 sm:pb-8">
                            <div
                                class="h-10 w-10 sm:h-12 sm:w-12 rounded-lg sm:rounded-xl bg-primary/10 flex items-center justify-center mb-4 sm:mb-6 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-instagram h-5 w-5 sm:h-6 sm:w-6 text-primary"
                                    aria-hidden="true">
                                    <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                    <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3">Auto-Post Engine</h3>
                            <p class="text-sm sm:text-base text-muted-foreground leading-relaxed">Schedule and automate
                                posts directly to Instagram with customizable templates.</p>
                        </div>
                    </div>
                    <div
                        class="rounded-xl border text-card-foreground bg-card border-border hover:border-accent/50 transition-colors shadow-sm group">
                        <div class="p-6 pt-6 px-6 sm:pt-8 sm:px-8 pb-6 sm:pb-8">
                            <div
                                class="h-10 w-10 sm:h-12 sm:w-12 rounded-lg sm:rounded-xl bg-primary/10 flex items-center justify-center mb-4 sm:mb-6 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-chart-column h-5 w-5 sm:h-6 sm:w-6 text-primary"
                                    aria-hidden="true">
                                    <path d="M3 3v16a2 2 0 0 0 2 2h16"></path>
                                    <path d="M18 17V9"></path>
                                    <path d="M13 17V5"></path>
                                    <path d="M8 17v-3"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3">Growth Analytics</h3>
                            <p class="text-sm sm:text-base text-muted-foreground leading-relaxed">Track engagement and
                                performance of your automated posts in real-time.</p>
                        </div>
                    </div>
                    <div
                        class="rounded-xl border text-card-foreground bg-card border-border hover:border-accent/50 transition-colors shadow-sm group">
                        <div class="p-6 pt-6 px-6 sm:pt-8 sm:px-8 pb-6 sm:pb-8">
                            <div
                                class="h-10 w-10 sm:h-12 sm:w-12 rounded-lg sm:rounded-xl bg-primary/10 flex items-center justify-center mb-4 sm:mb-6 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-shield h-5 w-5 sm:h-6 sm:w-6 text-primary" aria-hidden="true">
                                    <path
                                        d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3">Enterprise Security</h3>
                            <p class="text-sm sm:text-base text-muted-foreground leading-relaxed">Bank-grade encryption
                                and secure Instagram API integration for your accounts.</p>
                        </div>
                    </div>
                    <div
                        class="rounded-xl border text-card-foreground bg-card border-border hover:border-accent/50 transition-colors shadow-sm group">
                        <div class="p-6 pt-6 px-6 sm:pt-8 sm:px-8 pb-6 sm:pb-8">
                            <div
                                class="h-10 w-10 sm:h-12 sm:w-12 rounded-lg sm:rounded-xl bg-primary/10 flex items-center justify-center mb-4 sm:mb-6 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-zap h-5 w-5 sm:h-6 sm:w-6 text-primary"
                                    aria-hidden="true">
                                    <path
                                        d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3">Instant Chat</h3>
                            <p class="text-sm sm:text-base text-muted-foreground leading-relaxed">Integrated AI chat to
                                help you generate and refine quote ideas instantly.</p>
                        </div>
                    </div>
                    <div
                        class="rounded-xl border text-card-foreground bg-card border-border hover:border-accent/50 transition-colors shadow-sm group">
                        <div class="p-6 pt-6 px-6 sm:pt-8 sm:px-8 pb-6 sm:pb-8">
                            <div
                                class="h-10 w-10 sm:h-12 sm:w-12 rounded-lg sm:rounded-xl bg-primary/10 flex items-center justify-center mb-4 sm:mb-6 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-circle-check h-5 w-5 sm:h-6 sm:w-6 text-primary"
                                    aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="m9 12 2 2 4-4"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3">Cloud Sync
                            </h3>
                            <p class="text-sm sm:text-base text-muted-foreground leading-relaxed">All your data synced
                                across devices instantly. Manage from anywhere.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="automation" class="py-16 sm:py-24 bg-background overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-10 lg:gap-16">
                    <div class="w-full lg:w-1/2 space-y-6 sm:space-y-8">
                        <h2
                            class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight josefin-sans leading-tight">
                            Set it and forget it with smart automation</h2>
                        <p class="text-sm sm:text-lg text-muted-foreground leading-relaxed">Stop spending hours manually
                            posting content. Our engine handles the timing, tags, and formatting so you can focus on
                            creating.</p>
                        <ul class="grid sm:grid-cols-1 gap-3 sm:gap-4">
                            <li class="flex items-start gap-3"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-circle-check h-5 w-5 text-accent shrink-0 mt-0.5"
                                    aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="m9 12 2 2 4-4"></path>
                                </svg><span class="text-xs sm:text-sm font-medium leading-tight">Intelligent scheduling
                                    based on peak engagement</span></li>
                            <li class="flex items-start gap-3"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-circle-check h-5 w-5 text-accent shrink-0 mt-0.5"
                                    aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="m9 12 2 2 4-4"></path>
                                </svg><span class="text-xs sm:text-sm font-medium leading-tight">Automatic tag and
                                    caption generation</span></li>
                            <li class="flex items-start gap-3"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-circle-check h-5 w-5 text-accent shrink-0 mt-0.5"
                                    aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="m9 12 2 2 4-4"></path>
                                </svg><span class="text-xs sm:text-sm font-medium leading-tight">Cross-platform
                                    synchronization (coming soon)</span></li>
                            <li class="flex items-start gap-3"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-circle-check h-5 w-5 text-accent shrink-0 mt-0.5"
                                    aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="m9 12 2 2 4-4"></path>
                                </svg><span class="text-xs sm:text-sm font-medium leading-tight">Seamless integration
                                    with your existing content library</span></li>
                        </ul>
                    </div>
                    <div class="w-full lg:w-1/2 relative mt-8 lg:mt-0">
                        <div class="absolute -inset-4 bg-accent/20 blur-3xl rounded-full opacity-30"></div><img
                            alt="Automation Workflow"
                            class="relative rounded-xl sm:rounded-2xl border border-border shadow-2xl w-full"
                            src="https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?q=80&amp;w=2148&amp;auto=format&amp;fit=crop">
                    </div>
                </div>
            </div>
        </section>
        <section id="pricing" class="py-16 sm:py-24 bg-muted/30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 sm:mb-16">
                    <h2 class="text-2xl sm:text-3xl lg:text-5xl font-bold tracking-tight mb-4 josefin-sans">Simple,
                        transparent pricing</h2>
                    <p class="text-sm sm:text-lg text-muted-foreground px-4">Choose the plan that's right for your
                        growth.</p>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        class="rounded-xl border text-card-foreground shadow bg-card border-border transition-all duration-300 relative sm:col-span-1">
                        <div class="p-6 pt-8 px-6 sm:pt-10 sm:px-8 pb-8 sm:pb-10 text-center">
                            <h3 class="text-lg sm:text-xl font-bold mb-2 josefin-sans">Starter</h3>
                            <div class="text-3xl sm:text-4xl font-bold mb-4">$0<span
                                    class="text-sm font-normal text-muted-foreground">/mo</span></div>
                            <p class="text-muted-foreground text-xs sm:text-sm mb-6 sm:mb-8">Perfect for new creators
                            </p>
                            <ul class="text-left space-y-3 sm:space-y-4 mb-8 sm:mb-10">
                                <li class="flex items-center gap-3 text-xs sm:text-sm"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-circle-check h-4 w-4 text-accent shrink-0"
                                        aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>50 Quotes</li>
                                <li class="flex items-center gap-3 text-xs sm:text-sm"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-circle-check h-4 w-4 text-accent shrink-0"
                                        aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>Basic Categories</li>
                                <li class="flex items-center gap-3 text-xs sm:text-sm"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-circle-check h-4 w-4 text-accent shrink-0"
                                        aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>Manual Posting</li>
                                <li class="flex items-center gap-3 text-xs sm:text-sm"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-circle-check h-4 w-4 text-accent shrink-0"
                                        aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>Community Support</li>
                            </ul><button
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border border-primary-border min-h-9 px-4 py-2 w-full font-bold uppercase tracking-widest text-[10px] sm:text-xs h-10 sm:h-12 bg-primary text-primary-foreground">Choose
                                Plan</button>
                        </div>
                    </div>
                    <div
                        class="rounded-xl border text-card-foreground bg-card transition-all duration-300 relative border-accent shadow-xl shadow-accent/5 lg:scale-105 z-10 sm:col-span-2 lg:col-span-1">
                        <div
                            class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-accent text-accent-foreground text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full">
                            Most Popular</div>
                        <div class="p-6 pt-8 px-6 sm:pt-10 sm:px-8 pb-8 sm:pb-10 text-center">
                            <h3 class="text-lg sm:text-xl font-bold mb-2 josefin-sans">Pro</h3>
                            <div class="text-3xl sm:text-4xl font-bold mb-4">$29<span
                                    class="text-sm font-normal text-muted-foreground">/mo</span></div>
                            <p class="text-muted-foreground text-xs sm:text-sm mb-6 sm:mb-8">For growing brands</p>
                            <ul class="text-left space-y-3 sm:space-y-4 mb-8 sm:mb-10">
                                <li class="flex items-center gap-3 text-xs sm:text-sm"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-circle-check h-4 w-4 text-accent shrink-0"
                                        aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>Unlimited Quotes</li>
                                <li class="flex items-center gap-3 text-xs sm:text-sm"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-circle-check h-4 w-4 text-accent shrink-0"
                                        aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>Advanced Categories</li>
                                <li class="flex items-center gap-3 text-xs sm:text-sm"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-circle-check h-4 w-4 text-accent shrink-0"
                                        aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>Auto-Instagram Posting</li>
                                <li class="flex items-center gap-3 text-xs sm:text-sm"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-circle-check h-4 w-4 text-accent shrink-0"
                                        aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>Priority Support</li>
                            </ul><button
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border border-primary-border min-h-9 px-4 py-2 w-full font-bold uppercase tracking-widest text-[10px] sm:text-xs h-10 sm:h-12 bg-accent text-accent-foreground hover:bg-accent/90">Choose
                                Plan</button>
                        </div>
                    </div>
                    <div
                        class="rounded-xl border text-card-foreground shadow bg-card border-border transition-all duration-300 relative sm:col-span-1">
                        <div class="p-6 pt-8 px-6 sm:pt-10 sm:px-8 pb-8 sm:pb-10 text-center">
                            <h3 class="text-lg sm:text-xl font-bold mb-2 josefin-sans">Agency</h3>
                            <div class="text-3xl sm:text-4xl font-bold mb-4">$99<span
                                    class="text-sm font-normal text-muted-foreground">/mo</span></div>
                            <p class="text-muted-foreground text-xs sm:text-sm mb-6 sm:mb-8">Scale your agency</p>
                            <ul class="text-left space-y-3 sm:space-y-4 mb-8 sm:mb-10">
                                <li class="flex items-center gap-3 text-xs sm:text-sm"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-circle-check h-4 w-4 text-accent shrink-0"
                                        aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>Multiple Accounts</li>
                                <li class="flex items-center gap-3 text-xs sm:text-sm"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-circle-check h-4 w-4 text-accent shrink-0"
                                        aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>Team Collaboration</li>
                                <li class="flex items-center gap-3 text-xs sm:text-sm"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-circle-check h-4 w-4 text-accent shrink-0"
                                        aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>API Access</li>
                                <li class="flex items-center gap-3 text-xs sm:text-sm"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-circle-check h-4 w-4 text-accent shrink-0"
                                        aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="m9 12 2 2 4-4"></path>
                                    </svg>Dedicated Manager</li>
                            </ul><button
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border border-primary-border min-h-9 px-4 py-2 w-full font-bold uppercase tracking-widest text-[10px] sm:text-xs h-10 sm:h-12 bg-primary text-primary-foreground">Choose
                                Plan</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </StaticLayout>
</template>
