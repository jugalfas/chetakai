<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Badge from '@/Components/Badge.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    }
});

const user = usePage().props.auth.user;

// Tab state management
const activeTab = ref('personal');

// Form for personal information
const form = useForm({
    first_name: user.first_name,
    last_name: user.last_name,
    email: user.email,
});

// Watch for user changes and update form
watch(() => user.first_name, (newValue) => {
    form.first_name = newValue;
});
watch(() => user.last_name, (newValue) => {
    form.last_name = newValue;
});
watch(() => user.email, (newValue) => {
    form.email = newValue;
});

// Reactive local state for toggles (optimistic updates)
const autoPostScheduleEnabled = ref(user.auto_post_schedule_enabled);
const postNotificationsEnabled = ref(user.post_notifications_enabled);

// Watch for prop changes and update local state (when data refreshes from server)
watch(() => user.auto_post_schedule_enabled, (newValue) => {
    autoPostScheduleEnabled.value = newValue;
});
watch(() => user.post_notifications_enabled, (newValue) => {
    postNotificationsEnabled.value = newValue;
});

// Tab click handler
const setActiveTab = (tab) => {
    activeTab.value = tab;
};

// Password form
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const currentPasswordInput = ref(null);
const passwordInput = ref(null);
const passwordConfirmationInput = ref(null);

// Password visibility toggles
const showCurrentPassword = ref(false);
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const toggleCurrentPasswordVisibility = () => {
    showCurrentPassword.value = !showCurrentPassword.value;
};

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const togglePasswordConfirmationVisibility = () => {
    showPasswordConfirmation.value = !showPasswordConfirmation.value;
};

const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
        onError: () => {
            if (passwordForm.errors.password) {
                passwordForm.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (passwordForm.errors.current_password) {
                passwordForm.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};

// Toggle handlers
const toggleAutoPostSchedule = () => {
    const previousValue = autoPostScheduleEnabled.value;
    // Optimistic update
    autoPostScheduleEnabled.value = !autoPostScheduleEnabled.value;

    router.patch(route('profile.update.auto-post-schedule'), {
        enabled: autoPostScheduleEnabled.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Props will be updated automatically with new user data
        },
        onError: () => {
            // Revert on error
            autoPostScheduleEnabled.value = previousValue;
        }
    });
};

const togglePostNotifications = () => {
    const previousValue = postNotificationsEnabled.value;
    // Optimistic update
    postNotificationsEnabled.value = !postNotificationsEnabled.value;

    router.patch(route('profile.update.post-notifications'), {
        enabled: postNotificationsEnabled.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Props will be updated automatically with new user data
        },
        onError: () => {
            // Revert on error
            postNotificationsEnabled.value = previousValue;
        }
    });
};
</script>

<template>

    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Profile
            </h2>
        </template>

        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto space-y-8 pb-12">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">Profile Settings</h1>
                    <p class="text-muted-foreground mt-1">Manage your account and social connections.</p>
                </div>
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="space-y-6">
                        <div
                            class="rounded-xl border text-card-foreground border-border bg-card overflow-hidden shadow-sm">
                            <div class="h-24 bg-gradient-to-r from-primary to-accent opacity-80"></div>
                            <div class="p-6 pt-0 relative px-4 text-center">
                                <div class="inline-block p-1 bg-background rounded-full -mt-10 mb-4 shadow-xl">
                                    <span
                                        class="relative flex shrink-0 overflow-hidden rounded-full h-24 w-24 border-4 border-background">
                                        <img class="aspect-square h-full w-full" src="https://github.com/shadcn.png">
                                    </span>
                                </div>
                                <h2 class="text-xl font-bold text-foreground">{{ user.first_name }} {{
                                    user.last_name }}
                                </h2>
                                <p class="text-sm text-muted-foreground mb-6">Founder @ Chetak Quotes</p>
                                <button type="button" size="sm" fullWidth class="inline-flex items-center justify-center whitespace-nowrap transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border shadow-xs active:shadow-none min-h-8 rounded-md px-3 w-full gap-2 border-border h-10 text-xs font-bold uppercase tracking-widest hover:bg-muted text-foreground">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-camera h-3 w-3" aria-hidden="true">
                                        <path
                                            d="M13.997 4a2 2 0 0 1 1.76 1.05l.486.9A2 2 0 0 0 18.003 7H20a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1.997a2 2 0 0 0 1.759-1.048l.489-.904A2 2 0 0 1 10.004 4z">
                                        </path>
                                        <circle cx="12" cy="13" r="3"></circle>
                                    </svg>
                                    Update Photo
                                </button>
                            </div>
                        </div>
                        <div
                            class="rounded-xl border text-card-foreground border-border bg-card shadow-sm p-2">
                            <nav class="flex flex-col gap-1">
                                <button @click="setActiveTab('personal')" :class="activeTab === 'personal'
                                    ? 'bg-accent/10 text-accent hover:bg-accent/20'
                                    : 'text-muted-foreground hover:bg-muted'"
                                    class="inline-flex items-center whitespace-nowrap rounded-md text-sm font-medium focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent min-h-9 py-2 justify-start gap-3 h-11 px-4 transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-user h-4 w-4" aria-hidden="true">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    <span class="text-sm font-medium">Personal Information</span>
                                </button>
                                <button @click="setActiveTab('instagram')" :class="activeTab === 'instagram'
                                    ? 'bg-accent/10 text-accent hover:bg-accent/20'
                                    : 'text-muted-foreground hover:bg-muted'"
                                    class="inline-flex items-center whitespace-nowrap rounded-md text-sm font-medium focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent min-h-9 py-2 justify-start gap-3 h-11 px-4 transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-instagram h-4 w-4" aria-hidden="true">
                                        <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                        <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                                    </svg>
                                    <span class="text-sm font-medium">Instagram Account</span>
                                </button>
                                <button @click="setActiveTab('billing')" :class="activeTab === 'billing'
                                    ? 'bg-accent/10 text-accent'
                                    : 'text-muted-foreground hover:bg-muted'"
                                    class="inline-flex items-center whitespace-nowrap rounded-md text-sm font-medium focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent min-h-9 py-2 justify-start gap-3 h-11 px-4 transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-credit-card h-4 w-4"
                                        aria-hidden="true">
                                        <rect width="20" height="14" x="2" y="5" rx="2"></rect>
                                        <line x1="2" x2="22" y1="10" y2="10"></line>
                                    </svg>
                                    <span class="text-sm font-medium">Billing &amp; Plan</span>
                                </button>
                                <button @click="setActiveTab('security')" :class="activeTab === 'security'
                                    ? 'bg-accent/10 text-accent hover:bg-accent/20'
                                    : 'text-muted-foreground hover:bg-muted'"
                                    class="inline-flex items-center whitespace-nowrap rounded-md text-sm font-medium focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border border-transparent min-h-9 py-2 justify-start gap-3 h-11 px-4 transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-shield h-4 w-4" aria-hidden="true">
                                        <path
                                            d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z">
                                        </path>
                                    </svg>
                                    <span class="text-sm font-medium">Security</span>
                                </button>
                            </nav>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <!-- Personal Information Tab -->
                        <div v-show="activeTab === 'personal'"
                            class="rounded-xl border text-card-foreground border-border bg-card shadow-sm">
                            <div class="flex flex-col space-y-1.5 p-6">
                                <div class="font-semibold leading-none tracking-tight text-foreground">Personal
                                    Information
                                </div>
                                <div class="text-sm text-muted-foreground">Public details about your profile.</div>
                            </div>
                            <form @submit.prevent="form.patch(route('profile.update'), {
                                preserveScroll: true
                            })">
                                <div class="p-6 pt-0 space-y-6">
                                    <div class="grid gap-6 sm:grid-cols-2">
                                        <div>
                                            <InputLabel for="firstName" value="First Name" />
                                            <input id="firstName" v-model="form.first_name" type="text"
                                                autocomplete="given-name" class="flex w-full rounded-md border px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm bg-muted/10 border-border text-foreground h-11"/>
                                            <InputError :message="form.errors.first_name" />
                                        </div>
                                        <div>
                                            <InputLabel for="lastName" value="Last Name" />
                                            <input id="lastName" v-model="form.last_name" type="text"
                                                autocomplete="family-name" class="flex w-full rounded-md border px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm bg-muted/10 border-border text-foreground h-11"/>
                                            <InputError :message="form.errors.last_name" />
                                        </div>
                                    </div>
                                    <div>
                                        <InputLabel for="email" value="Email Address" />
                                        <div class="relative">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-mail absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground z-10"
                                                aria-hidden="true">
                                                <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path>
                                                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                            </svg>
                                            <input id="email" v-model="form.email" type="email" autocomplete="email"
                                                class="pl-10 flex w-full rounded-md border px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm bg-muted/10 border-border text-foreground h-11" />
                                        </div>
                                        <InputError :message="form.errors.email" />
                                    </div>
                                </div>
                                <div class="flex items-center justify-end border-t border-border bg-muted/5 p-4">
                                    <button type="submit" :disabled="form.processing"
                                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover-elevate active-elevate-2 border border-primary-border min-h-9 py-2 bg-accent text-accent-foreground hover:bg-accent/90 px-8 font-bold uppercase tracking-widest text-xs h-10"
                                        size="sm">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Instagram Account Tab -->
                        <div v-show="activeTab === 'instagram'"
                            class="rounded-xl border text-card-foreground border-border bg-card shadow-sm">
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
                                    class="flex items-center justify-between p-5 rounded-2xl border border-emerald-500/20 bg-emerald-500/5">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-12 w-12 rounded-full bg-accent/20 flex items-center justify-center border border-accent/20">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-instagram h-6 w-6 text-accent" aria-hidden="true">
                                                <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                                <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-base font-bold text-foreground">@{{ user.instagram_username || 'Not connected' }}</p>
                                            <p class="text-xs text-muted-foreground">{{ user.instagram_account_type || 'Connect your account' }}</p>
                                        </div>
                                    </div>
                                    <Badge v-if="user.instagram_connected" variant="success">
                                        Connected
                                    </Badge>
                                    <button v-else size="sm" type="button">
                                        Connect
                                    </button>
                                </div>
                                <div class="space-y-6">
                                    <div class="flex items-center justify-between">
                                        <div class="space-y-1">
                                            <label class="peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-sm font-bold text-foreground">Auto-post Schedule</label>
                                            <p class="text-xs text-muted-foreground">Automatically post scheduled
                                                quotes.
                                            </p>
                                        </div>
                                        <button type="button" role="switch" :aria-checked="autoPostScheduleEnabled"
                                            :data-state="autoPostScheduleEnabled ? 'checked' : 'unchecked'"
                                            @click="toggleAutoPostSchedule"
                                            class="peer inline-flex h-5 w-9 shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=unchecked]:bg-input">
                                            <span :data-state="autoPostScheduleEnabled ? 'checked' : 'unchecked'"
                                                class="pointer-events-none block h-4 w-4 rounded-full bg-background shadow-lg ring-0 transition-transform data-[state=checked]:translate-x-4 data-[state=unchecked]:translate-x-0"></span>
                                        </button>
                                    </div>
                                    <div data-orientation="horizontal" role="none"
                                        class="shrink-0 h-[1px] w-full bg-border">
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="space-y-0.5">
                                            <InputLabel value="Post Notifications" />
                                            <p class="text-xs text-muted-foreground">Get notified when a post goes
                                                live.</p>
                                        </div>
                                        <button type="button" role="switch" :aria-checked="postNotificationsEnabled"
                                            :data-state="postNotificationsEnabled ? 'checked' : 'unchecked'"
                                            @click="togglePostNotifications"
                                            class="peer inline-flex h-5 w-9 shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=unchecked]:bg-input">
                                            <span :data-state="postNotificationsEnabled ? 'checked' : 'unchecked'"
                                                class="pointer-events-none block h-4 w-4 rounded-full bg-background shadow-lg ring-0 transition-transform data-[state=checked]:translate-x-4 data-[state=unchecked]:translate-x-0"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Billing & Plan Tab -->
                        <div v-show="activeTab === 'billing'"
                            class="rounded-xl border text-card-foreground border-border bg-card shadow-sm">
                            <div class="flex flex-col space-y-1.5 p-6">
                                <div class="font-semibold leading-none tracking-tight text-foreground">Billing &amp;
                                    Plan</div>
                                <div class="text-sm text-muted-foreground">Manage your subscription and billing
                                    information.
                                </div>
                            </div>
                            <div class="p-6 pt-0 space-y-6">
                                <div
                                    class="flex items-center justify-between p-6 rounded-xl border border-border bg-muted/5">
                                    <div>
                                        <p class="text-lg font-bold text-foreground">Free Plan</p>
                                        <p class="text-sm text-muted-foreground mt-1">You're currently on the free plan
                                        </p>
                                    </div>
                                    <PrimaryButton type="button" size="sm">
                                        Upgrade Plan
                                    </PrimaryButton>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <InputLabel value="Payment Method" />
                                        <p class="text-xs text-muted-foreground mt-1">No payment method on file</p>
                                    </div>
                                    <div>
                                        <InputLabel value="Billing History" />
                                        <p class="text-xs text-muted-foreground mt-1">No billing history available</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Tab -->
                        <div v-show="activeTab === 'security'"
                            class="rounded-xl border text-card-foreground border-border bg-card shadow-sm">
                            <div class="flex flex-col space-y-1.5 p-6">
                                <div class="font-semibold leading-none tracking-tight text-foreground">Security Settings
                                </div>
                                <div class="text-sm text-muted-foreground">Update your password to keep your account
                                    secure.
                                </div>
                            </div>
                            <form @submit.prevent="updatePassword" class="p-6 pt-0 space-y-6">
                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <InputLabel for="current_password" value="OLD PASSWORD" />
                                        <div class="relative">
                                            <TextInput id="current_password" ref="currentPasswordInput"
                                                v-model="passwordForm.current_password"
                                                :type="showCurrentPassword ? 'text' : 'password'" class="pr-10"
                                                autocomplete="current-password" />
                                            <button type="button" @click="toggleCurrentPasswordVisibility"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                                                <svg v-if="showCurrentPassword" xmlns="http://www.w3.org/2000/svg"
                                                    width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" class="lucide lucide-eye-off">
                                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                                                    <path
                                                        d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68">
                                                    </path>
                                                    <path
                                                        d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61">
                                                    </path>
                                                    <line x1="2" x2="22" y1="2" y2="22"></line>
                                                </svg>
                                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-eye">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </button>
                                        </div>
                                        <InputError :message="passwordForm.errors.current_password" />
                                    </div>

                                    <div class="space-y-2">
                                        <InputLabel for="password" value="NEW PASSWORD" />
                                        <div class="relative">
                                            <TextInput id="password" ref="passwordInput" v-model="passwordForm.password"
                                                :type="showPassword ? 'text' : 'password'" class="pr-10"
                                                autocomplete="new-password" />
                                            <button type="button" @click="togglePasswordVisibility"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                                                <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" width="20"
                                                    height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-eye-off">
                                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                                                    <path
                                                        d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68">
                                                    </path>
                                                    <path
                                                        d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61">
                                                    </path>
                                                    <line x1="2" x2="22" y1="2" y2="22"></line>
                                                </svg>
                                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-eye">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </button>
                                        </div>
                                        <InputError :message="passwordForm.errors.password" />
                                    </div>

                                    <div class="space-y-2">
                                        <InputLabel for="password_confirmation" value="CONFIRM NEW PASSWORD" />
                                        <div class="relative">
                                            <TextInput id="password_confirmation" ref="passwordConfirmationInput"
                                                v-model="passwordForm.password_confirmation"
                                                :type="showPasswordConfirmation ? 'text' : 'password'" class="pr-10"
                                                autocomplete="new-password" />
                                            <button type="button" @click="togglePasswordConfirmationVisibility"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                                                <svg v-if="showPasswordConfirmation" xmlns="http://www.w3.org/2000/svg"
                                                    width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" class="lucide lucide-eye-off">
                                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                                                    <path
                                                        d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68">
                                                    </path>
                                                    <path
                                                        d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61">
                                                    </path>
                                                    <line x1="2" x2="22" y1="2" y2="22"></line>
                                                </svg>
                                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-eye">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </button>
                                        </div>
                                        <InputError :message="passwordForm.errors.password_confirmation" />
                                    </div>
                                </div>

                                <div class="flex items-center border-t border-border bg-muted/5 p-4">
                                    <PrimaryButton type="submit" :disabled="passwordForm.processing" class="ml-auto"
                                        fullWidth>
                                        UPDATE PASSWORD
                                    </PrimaryButton>
                                </div>
                            </form>

                            <div data-orientation="horizontal" role="none"
                                class="shrink-0 h-[1px] w-full bg-border mx-6"></div>

                            <div class="p-6">
                                <DeleteUserForm />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
