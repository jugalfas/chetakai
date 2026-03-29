<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { computed } from "vue";
import { ChevronLeftIcon, CheckIcon, ChevronDown, Check } from "lucide-vue-next";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Checkbox from "@/Components/Checkbox.vue";
import {
    SelectRoot,
    SelectTrigger,
    SelectValue,
    SelectPortal,
    SelectContent,
    SelectViewport,
    SelectItem,
    SelectItemText,
    SelectItemIndicator,
} from "radix-vue";

const form = useForm({
    name: "",
    type: "paid", // free, paid, custom
    description: "",
    badge_label: "Popular",
    is_most_popular: true,
    monthly_price: 0,
    annual_price: "",
    trial_days: 14,
    currency: "USD",
    stripe_price_id: "",
    post_limit_type: "Monthly", // Lifetime, Monthly, Unlimited
    post_limit_count: 100,
    max_social_accounts: 3,
    max_brand_profiles: 1,
    max_platforms: 2,
    features: {
        auto_scheduling: true,
        batch_generation: true,
        prompt_overrides: true,
        analytics: true,
        calendar_view: true,
        all_content_types: true,
        priority_ai: false,
        white_label: false,
        export_reports: false,
        api_access: false,
    },
    is_active: true,
    allow_new_signups: true,
    show_on_pricing: true,
    internal_notes: "",
});

const submit = () => {
    form.post(route("admin.subscription-plans.store"));
};

const enabledFeaturesCount = computed(() => {
    return Object.values(form.features).filter(Boolean).length;
});

const totalFeaturesCount = computed(() => {
    return Object.keys(form.features).length;
});

const badgeTheme = computed(() => {
    switch (form.badge_label) {
        case "Free":
            return {
                container: "bg-slate-500/10 border-slate-500/20",
                text: "text-slate-400",
                dot: "bg-slate-400",
            };
        case "Popular":
            return {
                container: "bg-purple-500/10 border-purple-500/20",
                text: "text-purple-400",
                dot: "bg-purple-400",
            };
        case "Business":
            return {
                container: "bg-teal-500/10 border-teal-500/20",
                text: "text-teal-400",
                dot: "bg-teal-400",
            };
        case "Enterprise":
            return {
                container: "bg-amber-500/10 border-amber-500/20",
                text: "text-amber-400",
                dot: "bg-amber-400",
            };
        default:
            return {
                container: "bg-primary/5 border-primary/20",
                text: "text-primary",
                dot: "bg-primary",
            };
    }
});

const inputClasses = "w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus:outline-none focus:border-primary focus:ring-0 transition-colors placeholder:text-muted-foreground shadow-sm h-11";
const selectTriggerClasses = "flex h-11 w-full items-center justify-between rounded-md border border-input px-3 py-2 text-sm shadow-sm transition-colors focus:outline-none focus:border-primary focus:ring-0 bg-background text-foreground";
const selectContentClasses = "z-[100] w-[--radix-select-trigger-width] overflow-hidden rounded-md border bg-[#041C32] text-popover-foreground shadow-md border-sidebar-border";
const selectItemClasses = "relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#092644] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors";
</script>

<template>
    <Head title="Create New Plan" />
    <AdminLayout>
        <div class="p-6 max-w-7xl mx-auto font-josefin">
            <!-- Top bar -->
            <div class="flex items-center gap-3 mb-8">
                <Link
                    :href="route('admin.subscription-plans.index')"
                    class="flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground transition-colors"
                >
                    <ChevronLeftIcon class="w-4 h-4" />
                    Plans
                </Link>
                <span class="text-muted-foreground/30 text-xs">|</span>
                <h1 class="text-xl font-semibold text-foreground">Create New Plan</h1>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Main Form Area -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Section 1: Basic Info -->
                    <div class="bg-card border border-border rounded-xl p-6 shadow-sm overflow-hidden">
                        <div class="mb-6 pb-4 border-b border-border/50">
                            <h3 class="text-sm font-bold text-foreground uppercase tracking-wider">Basic Information</h3>
                            <p class="text-xs text-muted-foreground mt-1 font-medium">Visible to users on the pricing page and upgrade prompts</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="space-y-2">
                                <InputLabel for="name" value="Plan Name" required />
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    :class="inputClasses"
                                    placeholder="e.g. Pro, Business, Starter"
                                    autofocus
                                />
                                <InputError :message="form.errors.name" />
                                <p class="text-[11px] text-muted-foreground pl-1">Shown on pricing page and invoices</p>
                            </div>

                            <div class="space-y-2">
                                <InputLabel for="type" value="Plan Type" required />
                                <SelectRoot v-model="form.type">
                                    <SelectTrigger :class="selectTriggerClasses">
                                        <SelectValue placeholder="Select type" />
                                        <ChevronDown class="h-4 w-4 opacity-50" />
                                    </SelectTrigger>
                                    <SelectPortal>
                                        <SelectContent :class="selectContentClasses" position="popper" :side-offset="5">
                                            <SelectViewport class="p-1">
                                                <SelectItem value="free" :class="selectItemClasses">
                                                    <SelectItemText>Free</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                                <SelectItem value="paid" :class="selectItemClasses">
                                                    <SelectItemText>Paid</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                                <SelectItem value="custom" :class="selectItemClasses">
                                                    <SelectItemText>Custom / Enterprise</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                            </SelectViewport>
                                        </SelectContent>
                                    </SelectPortal>
                                </SelectRoot>
                                <InputError :message="form.errors.type" />
                                <p class="text-[11px] text-muted-foreground pl-1">Controls billing and upgrade logic</p>
                            </div>
                        </div>

                        <div class="space-y-2 mb-6">
                            <InputLabel for="description" value="Short Description" />
                            <input
                                id="description"
                                v-model="form.description"
                                type="text"
                                :class="inputClasses"
                                placeholder="e.g. Perfect for solo creators and small brands"
                            />
                            <InputError :message="form.errors.description" />
                            <p class="text-[11px] text-muted-foreground pl-1">1 line shown under the plan name on pricing page</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <InputLabel value="Plan Badge Label" />
                                <SelectRoot v-model="form.badge_label">
                                    <SelectTrigger :class="selectTriggerClasses">
                                        <SelectValue placeholder="Select badge" />
                                        <ChevronDown class="h-4 w-4 opacity-50" />
                                    </SelectTrigger>
                                    <SelectPortal>
                                        <SelectContent :class="selectContentClasses" position="popper" :side-offset="5">
                                            <SelectViewport class="p-1">
                                                <SelectItem value="Free" :class="selectItemClasses">
                                                    <SelectItemText>Gray — Free</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                                <SelectItem value="Popular" :class="selectItemClasses">
                                                    <SelectItemText>Purple — Popular</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                                <SelectItem value="Business" :class="selectItemClasses">
                                                    <SelectItemText>Teal — Business</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                                <SelectItem value="Enterprise" :class="selectItemClasses">
                                                    <SelectItemText>Amber — Custom</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                            </SelectViewport>
                                        </SelectContent>
                                    </SelectPortal>
                                </SelectRoot>
                                <div 
                                    :class="badgeTheme.container"
                                    class="mt-3 flex items-center gap-2 p-2 rounded-full border w-fit px-4 transition-colors duration-300"
                                >
                                    <span 
                                        :class="badgeTheme.dot"
                                        class="w-2 h-2 rounded-full animate-pulse transition-colors duration-300"
                                    ></span>
                                    <span 
                                        :class="badgeTheme.text"
                                        class="text-xs font-semibold capitalize transition-colors duration-300"
                                    >
                                        {{ form.name || 'Pro' }} · {{ form.badge_label || 'Popular' }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-col justify-center gap-3">
                                <InputLabel value='Mark as "Most Popular"' />
                                <div class="flex items-center">
                                    <button
                                        type="button"
                                        @click="form.is_most_popular = !form.is_most_popular"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-0"
                                        :class="form.is_most_popular ? 'bg-primary' : 'bg-muted'"
                                    >
                                        <span
                                            aria-hidden="true"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            :class="form.is_most_popular ? 'translate-x-5' : 'translate-x-0'"
                                        ></span>
                                    </button>
                                    <span class="ml-3 text-sm text-foreground font-medium">{{ form.is_most_popular ? 'Enabled' : 'Disabled' }}</span>
                                </div>
                                <p class="text-[11px] text-muted-foreground">Highlights this plan with a badge on the pricing page</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Pricing -->
                    <div class="bg-card border border-border rounded-xl p-6 shadow-sm">
                        <div class="mb-6 pb-4 border-b border-border/50">
                            <h3 class="text-sm font-bold text-foreground uppercase tracking-wider">Pricing</h3>
                            <p class="text-xs text-muted-foreground mt-1 font-medium">Set monthly and annual pricing; annual discount is auto-calculated</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="space-y-2">
                                <InputLabel value="Monthly Price" required />
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground z-10 text-sm">$</span>
                                    <input
                                        v-model="form.monthly_price"
                                        type="number"
                                        :class="[inputClasses, 'pl-7']"
                                        placeholder="19"
                                    />
                                </div>
                                <InputError :message="form.errors.monthly_price" />
                                <p class="text-[11px] text-muted-foreground pl-1">Set 0 for free plans</p>
                            </div>

                            <div class="space-y-2">
                                <InputLabel value="Annual Price (USD)" />
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground z-10 text-sm">$</span>
                                    <input
                                        v-model="form.annual_price"
                                        type="number"
                                        :class="[inputClasses, 'pl-7']"
                                        placeholder="190"
                                    />
                                </div>
                                <InputError :message="form.errors.annual_price" />
                                <p class="text-[11px] text-muted-foreground pl-1">Leave blank to disable annual billing</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="space-y-2">
                                <InputLabel value="Trial Period (days)" />
                                <input
                                    v-model="form.trial_days"
                                    type="number"
                                    :class="inputClasses"
                                    placeholder="14"
                                />
                                <InputError :message="form.errors.trial_days" />
                                <p class="text-[11px] text-muted-foreground pl-1">0 = no trial; 14 days recommended</p>
                            </div>

                            <div class="space-y-2">
                                <InputLabel value="Currency" />
                                <SelectRoot v-model="form.currency">
                                    <SelectTrigger :class="selectTriggerClasses">
                                        <SelectValue placeholder="Select currency" />
                                        <ChevronDown class="h-4 w-4 opacity-50" />
                                    </SelectTrigger>
                                    <SelectPortal>
                                        <SelectContent :class="selectContentClasses" position="popper" :side-offset="5">
                                            <SelectViewport class="p-1">
                                                <SelectItem value="USD" :class="selectItemClasses">
                                                    <SelectItemText>USD ($)</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                                <SelectItem value="EUR" :class="selectItemClasses">
                                                    <SelectItemText>EUR (€)</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                                <SelectItem value="INR" :class="selectItemClasses">
                                                    <SelectItemText>INR (₹)</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                                <SelectItem value="GBP" :class="selectItemClasses">
                                                    <SelectItemText>GBP (£)</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                            </SelectViewport>
                                        </SelectContent>
                                    </SelectPortal>
                                </SelectRoot>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <InputLabel value="Stripe Price ID" />
                            <input
                                v-model="form.stripe_price_id"
                                type="text"
                                :class="inputClasses"
                                placeholder="price_1Abc123..."
                            />
                            <InputError :message="form.errors.stripe_price_id" />
                            <p class="text-[11px] text-muted-foreground pl-1">From your Stripe dashboard — links this plan to billing</p>
                        </div>
                    </div>

                    <!-- Section 3: Content Limits -->
                    <div class="bg-card border border-border rounded-xl p-6 shadow-sm">
                        <div class="mb-6 pb-4 border-b border-border/50">
                            <h3 class="text-sm font-bold text-foreground uppercase tracking-wider">Content Limits</h3>
                            <p class="text-xs text-muted-foreground mt-1 font-medium">Controls how many posts users on this plan can generate and publish</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="space-y-2">
                                <InputLabel value="Post Limit Type" required />
                                <SelectRoot v-model="form.post_limit_type">
                                    <SelectTrigger :class="selectTriggerClasses">
                                        <SelectValue placeholder="Select limit type" />
                                        <ChevronDown class="h-4 w-4 opacity-50" />
                                    </SelectTrigger>
                                    <SelectPortal>
                                        <SelectContent :class="selectContentClasses" position="popper" :side-offset="5">
                                            <SelectViewport class="p-1">
                                                <SelectItem value="Lifetime" :class="selectItemClasses">
                                                    <SelectItemText>Lifetime (one-time cap)</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                                <SelectItem value="Monthly" :class="selectItemClasses">
                                                    <SelectItemText>Monthly (resets each cycle)</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                                <SelectItem value="Unlimited" :class="selectItemClasses">
                                                    <SelectItemText>Unlimited</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-4 w-4 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                            </SelectViewport>
                                        </SelectContent>
                                    </SelectPortal>
                                </SelectRoot>
                            </div>
                            <div class="space-y-2">
                                <InputLabel value="Post Limit Count" />
                                <input
                                    v-model="form.post_limit_count"
                                    type="number"
                                    :class="inputClasses"
                                    placeholder="100"
                                    :disabled="form.post_limit_type === 'Unlimited'"
                                />
                                <InputError :message="form.errors.post_limit_count" />
                                <p class="text-[11px] text-muted-foreground pl-1">Leave blank if Unlimited selected</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="space-y-2">
                                <InputLabel value="Max Social Accounts" />
                                <input
                                    v-model="form.max_social_accounts"
                                    type="number"
                                    :class="inputClasses"
                                    placeholder="3"
                                />
                            </div>
                            <div class="space-y-2">
                                <InputLabel value="Max Brand Profiles" />
                                <input
                                    v-model="form.max_brand_profiles"
                                    type="number"
                                    :class="inputClasses"
                                    placeholder="1"
                                />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <InputLabel value="Max Connected Platforms" />
                            <input
                                v-model="form.max_platforms"
                                type="number"
                                :class="inputClasses"
                                placeholder="2"
                            />
                            <p class="text-[11px] text-muted-foreground pl-1">e.g. Instagram + LinkedIn = 2 platforms</p>
                        </div>
                    </div>

                    <!-- Section 4: Feature Access -->
                    <div class="bg-card border border-border rounded-xl p-6 shadow-sm">
                        <div class="mb-6 pb-4 border-b border-border/50">
                            <h3 class="text-sm font-bold text-foreground uppercase tracking-wider">Feature Access</h3>
                            <p class="text-xs text-muted-foreground mt-1 font-medium">Toggle which features are enabled for users on this plan</p>
                        </div>

                        <div class="grid grid-cols-1 gap-1">
                            <div
                                v-for="(enabled, feature) in form.features"
                                :key="feature"
                                class="flex items-center justify-between py-4 border-b border-border/40 last:border-0 hover:bg-muted/10 transition-colors px-2 rounded-lg"
                            >
                                <div>
                                    <div class="text-sm font-semibold text-foreground capitalize">{{ feature.replace(/_/g, ' ') }}</div>
                                    <div class="text-[11px] text-muted-foreground mt-0.5">
                                        <template v-if="feature === 'auto_scheduling'">AI picks optimal posting times automatically</template>
                                        <template v-else-if="feature === 'batch_generation'">Generate a week's worth of posts in one click</template>
                                        <template v-else-if="feature === 'prompt_overrides'">Users can write their own prompt instructions</template>
                                        <template v-else-if="feature === 'analytics'">Engagement, reach, and post performance data</template>
                                        <template v-else-if="feature === 'calendar_view'">Full calendar with drag-and-reschedule</template>
                                        <template v-else-if="feature === 'all_content_types'">Quotes, reels, carousels, stories — not just captions</template>
                                        <template v-else-if="feature === 'priority_ai'">Faster queue for AI requests (Business+)</template>
                                        <template v-else-if="feature === 'white_label'">No "Powered by" watermark on published posts</template>
                                        <template v-else-if="feature === 'export_reports'">Download content performance reports</template>
                                        <template v-else-if="feature === 'api_access'">Programmatic access for advanced users</template>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    @click="form.features[feature] = !form.features[feature]"
                                    class="relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-1 focus:ring-primary"
                                    :class="form.features[feature] ? 'bg-primary' : 'bg-muted'"
                                >
                                    <span
                                        aria-hidden="true"
                                        class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                        :class="form.features[feature] ? 'translate-x-5' : 'translate-x-0'"
                                    ></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Section 5: Visibility -->
                    <div class="bg-card border border-border rounded-xl p-6 shadow-sm">
                        <div class="mb-6 pb-4 border-b border-border/50">
                            <h3 class="text-sm font-bold text-foreground uppercase tracking-wider">Visibility & Status</h3>
                            <p class="text-xs text-muted-foreground mt-1 font-medium">Control whether this plan is available for new signups</p>
                        </div>

                        <div class="space-y-4 pt-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-semibold text-foreground">Active (Visible to users)</div>
                                    <div class="text-[11px] text-muted-foreground">Inactive plans hide from pricing page; existing users keep access</div>
                                </div>
                                <Checkbox v-model:checked="form.is_active" />
                            </div>

                            <div class="flex items-center justify-between border-t border-border/30 pt-4">
                                <div>
                                    <div class="text-sm font-semibold text-foreground">Allow New Signups</div>
                                    <div class="text-[11px] text-muted-foreground">Uncheck to freeze this plan for existing users only</div>
                                </div>
                                <Checkbox v-model:checked="form.allow_new_signups" />
                            </div>

                            <div class="flex items-center justify-between border-t border-border/30 pt-4">
                                <div>
                                    <div class="text-sm font-semibold text-foreground">Show on Public Pricing Page</div>
                                    <div class="text-[11px] text-muted-foreground">Uncheck for invite-only or enterprise plans</div>
                                </div>
                                <Checkbox v-model:checked="form.show_on_pricing" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Sidebar -->
                <div class="space-y-8">
                    <div class="bg-card border border-border rounded-xl p-6 shadow-sm sticky top-24 overflow-hidden">
                        <h3 class="text-sm font-bold text-foreground uppercase tracking-widest border-b border-border pb-4 mb-6">Plan Summary</h3>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-border/30">
                                <span class="text-xs font-medium text-muted-foreground">Name</span>
                                <span class="text-sm font-bold text-foreground">{{ form.name || 'Untitled' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-border/30">
                                <span class="text-xs font-medium text-muted-foreground">Monthly</span>
                                <span class="text-sm font-bold text-foreground">${{ form.monthly_price || 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-border/30">
                                <span class="text-xs font-medium text-muted-foreground">Annual</span>
                                <span class="text-sm font-bold text-foreground">${{ form.annual_price || 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-border/30">
                                <span class="text-xs font-medium text-muted-foreground">Trial</span>
                                <span class="text-xs font-bold text-primary px-2 py-0.5 bg-primary/10 rounded-full">{{ form.trial_days }} days</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-border/30">
                                <span class="text-xs font-medium text-muted-foreground">Posts</span>
                                <span class="text-sm font-bold text-foreground">{{ form.post_limit_type === 'Unlimited' ? 'Unlimited' : (form.post_limit_count || 0) + ' / ' + form.post_limit_type }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-border/30">
                                <span class="text-xs font-medium text-muted-foreground">Features</span>
                                <span class="text-xs font-bold text-primary px-2 py-0.5 bg-primary/10 rounded-full">{{ enabledFeaturesCount }} / {{ totalFeaturesCount }} enabled</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-xs font-medium text-muted-foreground">Status</span>
                                <span 
                                    class="text-[10px] font-bold uppercase px-3 py-1 rounded-full border"
                                    :class="form.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/10 dark:text-emerald-400 dark:border-emerald-800' : 'bg-muted text-muted-foreground border-border'"
                                >
                                    {{ form.is_active ? 'Active' : 'Draft' }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-8 space-y-4">
                            <div>
                                <InputLabel value="Internal Notes" class="text-xs font-bold uppercase tracking-wider text-muted-foreground mb-2 block" />
                                <textarea
                                    v-model="form.internal_notes"
                                    rows="4"
                                    class="w-full bg-accent/10 border border-input rounded-md p-3 text-sm focus:outline-none focus:border-primary focus:ring-0 transition-all resize-none shadow-inner"
                                    placeholder="Internal notes about this plan..."
                                ></textarea>
                            </div>

                            <div class="bg-amber-50/50 dark:bg-amber-900/5 border border-amber-200/50 dark:border-amber-900/10 rounded-lg p-4 text-[11px] text-amber-800 dark:text-amber-300 leading-relaxed italic">
                                <div class="flex gap-2">
                                    <div class="w-4 h-4 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 font-bold not-italic">!</div>
                                    <p>Changing limits on an active plan affects all existing subscribers immediately. Consider creating a new plan instead of editing.</p>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 pt-4">
                                <button
                                    type="submit"
                                    class="w-full justify-center h-12 text-base font-bold rounded-md bg-primary text-primary-foreground hover:bg-primary/90 shadow-lg shadow-primary/20 transition-all disabled:opacity-50"
                                    :disabled="form.processing"
                                >
                                    {{ form.processing ? 'Saving...' : 'Save Plan' }}
                                </button>
                                
                                <button
                                    type="button"
                                    @click="router.get(route('admin.subscription-plans.index'))"
                                    class="w-full justify-center h-12 text-base font-bold rounded-md border border-input bg-background hover:bg-muted transition-all"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<style scoped>
/* Minimal scrollbar for textareas */
textarea {
    resize: none;
    scrollbar-width: thin;
    scrollbar-color: rgba(155, 155, 155, 0.3) transparent;
}

textarea::-webkit-scrollbar {
    width: 6px;
}

textarea::-webkit-resizer,
textarea::-webkit-scrollbar-corner {
    background-color: transparent;
}

/* Minimal number input (hide default spinners) */
input[type="number"] {
    appearance: textfield;
    -moz-appearance: textfield;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    appearance: none;
    margin: 0;
}

textarea::-webkit-scrollbar-track {
    background: transparent;
}

textarea::-webkit-scrollbar-thumb {
    background: rgba(155, 155, 155, 0.3);
    border-radius: 10px;
}

textarea::-webkit-scrollbar-thumb:hover {
    background: rgba(155, 155, 155, 0.5);
}

/* For Firefox */
textarea {
    scrollbar-width: thin;
    scrollbar-color: rgba(155, 155, 155, 0.3) transparent;
}

/* Custom data-state for Radix Select */
.data-\[state\=checked\]\:bg-\[\#092644\][data-state="checked"] {
    background-color: #092644;
}

.focus\:bg-accent:focus {
    background-color: #092644;
}
</style>
