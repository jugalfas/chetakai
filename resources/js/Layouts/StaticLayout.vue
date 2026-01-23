<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { Toaster } from 'vue-sonner';

const scrolled = ref(false);

const handleScroll = () => {
    scrolled.value = window.scrollY > 20;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const isDarkMode = ref(false);

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    applyTheme();
};

const applyTheme = () => {
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};
</script>

<template>
    <div class="min-h-screen bg-background text-foreground transition-colors duration-500 overflow-x-hidden">
        <Toaster position="top-right" rich-colors close-button />
        <nav :class="[
            'fixed top-0 w-full z-50 transition-all duration-300 border-b',
            scrolled
                ? 'h-16 bg-background/80 backdrop-blur-md border-border'
                : 'h-20 bg-transparent border-transparent'
        ]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="/logo/Chetak-white.png" alt="Logo" class="w-10 h-10 dark:block hidden">
                    <img src="/logo/Chetak.png" alt="Logo" class="w-10 h-10 dark:hidden block"><span
                        class="text-xl sm:text-2xl font-bold tracking-tight josefin-sans">Chetak</span>
                </div>
                <div class="hidden lg:flex items-center gap-8 text-sm font-medium text-muted-foreground"><a
                        href="#features" class="hover:text-foreground transition-colors">Features</a><a
                        href="#automation" class="hover:text-foreground transition-colors">Automation</a><a
                        href="#pricing" class="hover:text-foreground transition-colors">Pricing</a></div>
                <div class="flex items-center gap-2 sm:gap-4">
                    <div class="hidden sm:flex items-center gap-4">
                        <a href="/login">
                            <button
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent min-h-9 px-4 py-2 font-bold uppercase tracking-widest text-[10px] sm:text-xs">Login</button></a><a
                            href="/register">
                            <button
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border border-primary-border min-h-9 py-2 bg-accent text-accent-foreground hover:bg-accent/90 font-bold uppercase tracking-widest text-[10px] sm:text-xs px-4 sm:px-6 h-9 sm:h-10 shadow-lg shadow-accent/20">Get
                                Started</button>
                        </a>
                    </div>
                    <button
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent h-9 w-9 lg:hidden"
                        type="button" aria-haspopup="dialog" aria-expanded="false" aria-controls="radix-_r_0_"
                        data-state="closed"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu h-6 w-6"
                            aria-hidden="true">
                            <path d="M4 5h16"></path>
                            <path d="M4 12h16"></path>
                            <path d="M4 19h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </nav>

        <slot />

        <button @click="toggleDarkMode"
            class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 min-h-9 px-4 py-2 fixed bottom-6 right-6 sm:bottom-8 sm:right-8 h-12 w-12 sm:h-14 sm:w-14 rounded-full shadow-2xl bg-card border border-border text-foreground hover:bg-muted/80 z-[100] group">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-sun h-5 w-5 sm:h-6 sm:w-6 group-hover:rotate-45 transition-transform"
                aria-hidden="true">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M12 2v2"></path>
                <path d="M12 20v2"></path>
                <path d="m4.93 4.93 1.41 1.41"></path>
                <path d="m17.66 17.66 1.41 1.41"></path>
                <path d="M2 12h2"></path>
                <path d="M20 12h2"></path>
                <path d="m6.34 17.66-1.41 1.41"></path>
                <path d="m19.07 4.93-1.41 1.41"></path>
            </svg>
        </button>

        <footer class="py-12 sm:py-20 border-t border-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="flex items-center justify-center gap-2 mb-6 sm:mb-8">
                    <img src="/logo/Chetak-white.png" alt="Logo" class="w-10 h-10 dark:block hidden">
                    <img src="/logo/Chetak.png" alt="Logo" class="w-10 h-10 dark:hidden block">
                    <span class="text-lg sm:text-xl font-bold tracking-tight josefin-sans">Chetak</span>
                </div>
                <p class="text-muted-foreground text-[10px] sm:text-sm mb-6 sm:mb-8 px-4">© 2026 Chetak Automation Inc.
                    All rights reserved.</p>
                <div
                    class="flex flex-wrap items-center justify-center gap-4 sm:gap-8 text-[10px] font-bold uppercase tracking-widest text-muted-foreground px-4">
                    <Link :href="route('privacy')" class="hover:text-foreground transition-colors">Privacy Policy</Link>
                    <Link :href="route('terms-of-service')" class="hover:text-foreground transition-colors">Terms of
                        Service</Link>
                    <Link :href="route('contact')" class="hover:text-foreground transition-colors">Contact</Link>
                </div>
            </div>
        </footer>
    </div>
</template>
