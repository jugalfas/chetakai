<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>

    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="max-w-4xl mx-auto space-y-8 pb-12">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-foreground">Profile Settings</h1>
                        <p class="text-muted-foreground mt-1">Manage your account and social connections.</p>
                    </div>
                    <div class="grid gap-8 md:grid-cols-3">
                        <div class="space-y-4">
                            <div
                                class="rounded-xl border text-card-foreground border-border bg-card overflow-hidden shadow-sm">
                                <div class="h-24 bg-gradient-to-r from-primary to-accent opacity-80"></div>
                                <div class="p-6 pt-0 relative px-4 text-center">
                                    <div class="inline-block p-1 bg-background rounded-full -mt-10 mb-4 shadow-xl"><span
                                            class="relative flex shrink-0 overflow-hidden rounded-full h-20 w-20 border-2 border-background"><img
                                                class="aspect-square h-full w-full"
                                                src="https://github.com/shadcn.png"></span>
                                    </div>
                                    <h2 class="text-xl font-bold text-foreground">Jugal Faswala</h2>
                                    <p class="text-xs text-muted-foreground mb-4">Founder @ Chetak Quotes</p><button
                                        class="inline-flex items-center justify-center whitespace-nowrap transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border [border-color:var(--button-outline)] shadow-xs active:shadow-none min-h-8 rounded-md px-3 w-full gap-2 border-border h-8 text-xs font-bold uppercase tracking-widest hover:bg-muted text-foreground"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-camera h-3 w-3" aria-hidden="true">
                                            <path
                                                d="M13.997 4a2 2 0 0 1 1.76 1.05l.486.9A2 2 0 0 0 18.003 7H20a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1.997a2 2 0 0 0 1.759-1.048l.489-.904A2 2 0 0 1 10.004 4z">
                                            </path>
                                            <circle cx="12" cy="13" r="3"></circle>
                                        </svg>Update Photo</button>
                                </div>
                            </div>
                            <nav class="flex flex-col gap-1"><button
                                    class="inline-flex items-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent min-h-9 py-2 justify-start gap-3 bg-accent/10 text-accent h-10 px-3 hover:bg-accent/20"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-user h-4 w-4" aria-hidden="true">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg><span class="text-sm font-medium">Personal Information</span></button><button
                                    class="inline-flex items-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent min-h-9 py-2 justify-start gap-3 text-muted-foreground hover:bg-muted h-10 px-3"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-instagram h-4 w-4"
                                        aria-hidden="true">
                                        <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                        <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                                    </svg><span class="text-sm font-medium">Instagram Account</span></button><button
                                    class="inline-flex items-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent min-h-9 py-2 justify-start gap-3 text-muted-foreground hover:bg-muted h-10 px-3"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-credit-card h-4 w-4"
                                        aria-hidden="true">
                                        <rect width="20" height="14" x="2" y="5" rx="2"></rect>
                                        <line x1="2" x2="22" y1="10" y2="10"></line>
                                    </svg><span class="text-sm font-medium">Billing &amp; Plan</span></button><button
                                    class="inline-flex items-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent min-h-9 py-2 justify-start gap-3 text-muted-foreground hover:bg-muted h-10 px-3"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-shield h-4 w-4" aria-hidden="true">
                                        <path
                                            d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z">
                                        </path>
                                    </svg><span class="text-sm font-medium">Security</span></button></nav>
                        </div>
                        <div class="md:col-span-2 space-y-6">
                            <div class="rounded-xl border text-card-foreground border-border bg-card shadow-sm">
                                <div class="flex flex-col space-y-1.5 p-6">
                                    <div class="font-semibold leading-none tracking-tight text-foreground">Personal
                                        Information
                                    </div>
                                    <div class="text-sm text-muted-foreground">Public details about your profile.</div>
                                </div>
                                <div class="p-6 pt-0 space-y-4">
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="space-y-2"><label
                                                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-foreground"
                                                for="firstName">First Name</label><input
                                                class="flex h-9 w-full rounded-md border px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm bg-muted/20 border-border text-foreground"
                                                id="firstName" value="Jugal">
                                            <div data-lastpass-icon-root=""
                                                style="position: relative !important; height: 0px !important; width: 0px !important; display: initial !important; float: left !important;">
                                            </div>
                                        </div>
                                        <div class="space-y-2"><label
                                                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-foreground"
                                                for="lastName">Last Name</label><input
                                                class="flex h-9 w-full rounded-md border px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm bg-muted/20 border-border text-foreground"
                                                id="lastName" value="Faswala"></div>
                                    </div>
                                    <div class="space-y-2"><label
                                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-foreground"
                                            for="email">Email Address</label>
                                        <div class="relative"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-mail absolute left-3 top-3 h-4 w-4 text-muted-foreground"
                                                aria-hidden="true">
                                                <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path>
                                                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                            </svg><input
                                                class="flex h-9 w-full rounded-md border px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm pl-10 bg-muted/20 border-border text-foreground"
                                                id="email" value="jugal@chetak.ai"></div>
                                    </div>
                                </div>
                                <div class="flex items-center border-t border-border bg-muted/5 p-4"><button
                                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover-elevate active-elevate-2 border border-primary-border min-h-8 rounded-md ml-auto bg-accent text-accent-foreground hover:bg-accent/90 px-6 font-bold uppercase tracking-widest text-xs">Save
                                        Changes</button></div>
                            </div>
                            <div class="rounded-xl border text-card-foreground border-border bg-card shadow-sm">
                                <div class="flex flex-col space-y-1.5 p-6">
                                    <div class="font-semibold leading-none tracking-tight text-foreground">Instagram
                                        Integration
                                    </div>
                                    <div class="text-sm text-muted-foreground">Connect your business account to automate
                                        posts.
                                    </div>
                                </div>
                                <div class="p-6 pt-0 space-y-6">
                                    <div
                                        class="flex items-center justify-between p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/5">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-10 w-10 rounded-full bg-accent/20 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-instagram h-5 w-5 text-accent"
                                                    aria-hidden="true">
                                                    <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                                    <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-foreground">@chetak_quotes</p>
                                                <p class="text-xs text-muted-foreground">Business Account Linked</p>
                                            </div>
                                        </div>
                                        <div
                                            class="whitespace-nowrap inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 hover-elevate border-transparent shadow-xs bg-emerald-500/20 text-emerald-500 hover:bg-emerald-500/20 border-0">
                                            Connected</div>
                                    </div>
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between">
                                            <div class="space-y-0.5"><label
                                                    class="peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-sm font-bold text-foreground">Auto-post
                                                    Schedule</label>
                                                <p class="text-xs text-muted-foreground">Automatically post scheduled
                                                    quotes.
                                                </p>
                                            </div><button type="button" role="switch" aria-checked="true"
                                                data-state="checked" value="on"
                                                class="peer inline-flex h-5 w-9 shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=unchecked]:bg-input"><span
                                                    data-state="checked"
                                                    class="pointer-events-none block h-4 w-4 rounded-full bg-background shadow-lg ring-0 transition-transform data-[state=checked]:translate-x-4 data-[state=unchecked]:translate-x-0"></span></button>
                                        </div>
                                        <div data-orientation="horizontal" role="none"
                                            class="shrink-0 h-[1px] w-full bg-border"></div>
                                        <div class="flex items-center justify-between">
                                            <div class="space-y-0.5"><label
                                                    class="peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-sm font-bold text-foreground">Post
                                                    Notifications</label>
                                                <p class="text-xs text-muted-foreground">Get notified when a post goes
                                                    live.</p>
                                            </div><button type="button" role="switch" aria-checked="true"
                                                data-state="checked" value="on"
                                                class="peer inline-flex h-5 w-9 shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=unchecked]:bg-input"><span
                                                    data-state="checked"
                                                    class="pointer-events-none block h-4 w-4 rounded-full bg-background shadow-lg ring-0 transition-transform data-[state=checked]:translate-x-4 data-[state=unchecked]:translate-x-0"></span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
