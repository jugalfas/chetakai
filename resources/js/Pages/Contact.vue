<script setup>
import StaticLayout from '@/Layouts/StaticLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';


const form = useForm({
    name: '',
    email: '',
    message: '',
});

const submit = () => {
    form.post(route('contact.submit'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Your message has been sent successfully!');
            form.reset();
        },
        onError: () => {
            toast.error('Something went wrong. Please try again.');
        },
    });
};
</script>

<template>

    <Head title="Contact Us" />

    <StaticLayout>
        <main>
            <div class="pt-32 pb-20 px-4 sm:px-6 lg:px-8">
                <div class="max-w-5xl mx-auto">
                    <div class="grid lg:grid-cols-2 gap-16">
                        <div>
                            <h1 class="text-4xl font-bold josefin-sans mb-6">Get in Touch</h1>
                            <p class="text-muted-foreground mb-12">Have questions about automation or pricing? We're
                                here to help.</p>
                            <div class="space-y-8">
                                <div class="flex gap-4">
                                    <div
                                        class="h-10 w-10 rounded-lg bg-muted flex items-center justify-center shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-mail h-5 w-5 text-accent" aria-hidden="true">
                                            <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path>
                                            <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold josefin-sans">Email</h3>
                                        <p class="text-muted-foreground">support@chetak.io</p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div
                                        class="h-10 w-10 rounded-lg bg-muted flex items-center justify-center shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-message-square h-5 w-5 text-accent" aria-hidden="true">
                                            <path
                                                d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold josefin-sans">Live Chat</h3>
                                        <p class="text-muted-foreground">Available 24/7</p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div
                                        class="h-10 w-10 rounded-lg bg-muted flex items-center justify-center shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-map-pin h-5 w-5 text-accent" aria-hidden="true">
                                            <path
                                                d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0">
                                            </path>
                                            <circle cx="12" cy="10" r="3"></circle>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold josefin-sans">Office</h3>
                                        <p class="text-muted-foreground">Silicon Valley, CA</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-xl border text-card-foreground bg-card border-border shadow-2xl">
                            <div class="p-8">
                                <form @submit.prevent="submit" class="space-y-6">
                                    <div>
                                        <input v-model="form.name"
                                            class="flex h-9 w-full rounded-md border px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm bg-muted/50 border-border"
                                            placeholder="Name" required="">
                                        <div v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</div>
                                    </div>
                                    <div>
                                        <input v-model="form.email"
                                            class="flex h-9 w-full rounded-md border px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm bg-muted/50 border-border"
                                            placeholder="Email" required="" type="email">
                                        <div v-if="form.errors.email" class="text-xs text-red-500 mt-1">{{ form.errors.email }}</div>
                                    </div>
                                    <div>
                                        <textarea v-model="form.message"
                                            class="flex w-full rounded-md border px-3 py-2 text-base shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm bg-muted/50 border-border min-h-[150px]"
                                            placeholder="Message" required=""></textarea>
                                        <div v-if="form.errors.message" class="text-xs text-red-500 mt-1">{{ form.errors.message }}</div>
                                    </div>
                                    <button :disabled="form.processing"
                                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 text-primary-foreground border border-primary-border min-h-9 px-4 py-2 w-full bg-primary font-bold uppercase tracking-widest text-xs">
                                        {{ form.processing ? 'Sending...' : 'Send Message' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </StaticLayout>
</template>
