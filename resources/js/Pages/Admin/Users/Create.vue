<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { ChevronDown, Check } from "lucide-vue-next";
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
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.css';
import 'flatpickr/dist/themes/dark.css';

const props = defineProps({
    plans: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    timezone: 'Asia/Kolkata',
    password: '',
    password_confirmation: '',
    plan_id: props.plans.length > 0 ? String(props.plans[0].id) : '',
    billing_cycle: 'Monthly',
    starts_at: '',
    ends_at: '',
    internal_notes: '',
    mark_email_verified: true,
    is_active: true,
    send_welcome_email: true,
    require_password_change: false,
});

const submit = () => {
    form.post(route('admin.users.store'));
};

const showPw = ref(false);
const showPw2 = ref(false);

const startsAtInput = ref(null);
const endsAtInput = ref(null);
let fpStart = null;
let fpEnd = null;

onMounted(() => {
    fpStart = flatpickr(startsAtInput.value, {
        dateFormat: "Y-m-d",
        onChange: (selectedDates, dateStr) => form.starts_at = dateStr,
    });
    fpEnd = flatpickr(endsAtInput.value, {
        dateFormat: "Y-m-d",
        onChange: (selectedDates, dateStr) => form.ends_at = dateStr,
    });
});

onUnmounted(() => {
    if (fpStart) fpStart.destroy();
    if (fpEnd) fpEnd.destroy();
});

const timezones = computed(() => {
    try {
        if (typeof Intl === 'undefined' || !Intl.supportedValuesOf) return [];
        
        // Browsers often return 'Asia/Calcutta' as the canonical canonical IANA string, omitting 'Asia/Kolkata'
        let tzs = Intl.supportedValuesOf('timeZone');
        tzs = tzs.map(tz => tz === 'Asia/Calcutta' ? 'Asia/Kolkata' : tz);

        return tzs.map(tz => {
            try {
                const date = new Date();
                const offsetStr = new Intl.DateTimeFormat('en-US', { timeZone: tz, timeZoneName: 'shortOffset' }).format(date);
                const match = offsetStr.match(/(GMT[+-]?\d*(?::\d*)?)/);
                const display = match ? match[1] : 'GMT';
                return {
                    value: tz,
                    label: `${tz} (${display})`
                };
            } catch (e) {
                return { value: tz, label: tz };
            }
        }).sort((a,b) => a.value.localeCompare(b.value));
    } catch {
        return [{ value: 'Asia/Kolkata', label: 'Asia/Kolkata (GMT+5:30)' }];
    }
});

const initials = computed(() => {
    const f = (form.first_name || 'First').trim()[0] || '?';
    const l = (form.last_name || 'Last').trim()[0] || '?';
    return (f + l).toUpperCase();
});

const displayName = computed(() => {
    const f = form.first_name.trim() || 'First';
    const l = form.last_name.trim() || 'Last';
    return `${f} ${l}`;
});

const displayEmail = computed(() => {
    return form.email.trim() || 'email@example.com';
});

const selectedPlan = computed(() => {
    if (!form.plan_id) return null;
    return props.plans.find(p => String(p.id) === form.plan_id);
});

const displayPlan = computed(() => {
    return selectedPlan.value ? selectedPlan.value.name : 'Unknown';
});

const planBadgeClass = computed(() => {
    const p = displayPlan.value;
    if (p === 'Pro') return 'badge-pro';
    if (p === 'Business') return 'badge-biz';
    return 'badge-free';
});

const selectTriggerClasses = "flex w-full items-center justify-between rounded-[calc(var(--radius)-2px)] border border-input px-3 py-2 text-[13px] bg-background text-foreground focus:outline-none focus:border-ring focus:shadow-[0_0_0_2px_hsl(var(--ring)/0.2)] transition-all";
const selectContentClasses = "z-[100] w-[--radix-select-trigger-width] overflow-hidden rounded-md border bg-[#041C32] text-popover-foreground shadow-md border-sidebar-border";
const selectItemClasses = "relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-9 text-[13px] outline-none focus:bg-accent focus:text-accent-foreground data-[state=checked]:bg-[#092644] data-[state=checked]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50 hover:bg-muted/20 transition-colors";
</script>

<template>
    <Head title="Create User" />

    <AdminLayout>
        <div class="px-6 py-6 max-w-7xl mx-auto">
            <div class="topbar">
                <Link :href="route('admin.users.index')" class="back-btn">
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none" class="mr-1"><path d="M8 2L3 6.5L8 11" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Users
                </Link>
                <span class="crumb-sep">/</span>
                <div>
                    <div class="page-title">Create user</div>
                    <div class="page-sub">Manually add a new user account</div>
                </div>
            </div>

            <form @submit.prevent="submit" class="layout" autocomplete="off">
                <div>
                    <div class="section">
                        <div class="section-title">Personal information</div>
                        <div class="section-desc">Basic identity fields — maps directly to your users table</div>

                        <div class="field-row">
                            <div>
                                <label>First name <span class="req">*</span></label>
                                <input type="text" v-model="form.first_name" placeholder="Arjun" :class="{ 'border-rose-500': form.errors.first_name }" autocomplete="off">
                                <div v-if="form.errors.first_name" class="text-xs text-rose-500 mt-1">{{ form.errors.first_name }}</div>
                            </div>
                            <div>
                                <label>Last name <span class="req">*</span></label>
                                <input type="text" v-model="form.last_name" placeholder="Mehta" :class="{ 'border-rose-500': form.errors.last_name }" autocomplete="off">
                                <div v-if="form.errors.last_name" class="text-xs text-rose-500 mt-1">{{ form.errors.last_name }}</div>
                            </div>
                        </div>

                        <div class="field">
                            <label>Email address <span class="req">*</span></label>
                            <input type="email" v-model="form.email" placeholder="arjun.mehta@gmail.com" :class="{ 'border-rose-500': form.errors.email }" autocomplete="off" spellcheck="false" data-lpignore="true">
                            <div v-if="form.errors.email" class="text-xs text-rose-500 mt-1 mb-1">{{ form.errors.email }}</div>
                            <div class="hint" v-else>Must be unique — used for OTP login</div>
                        </div>

                        <div class="field">
                            <label>Timezone</label>
                            <SelectRoot v-model="form.timezone">
                                <SelectTrigger :class="[selectTriggerClasses, form.errors.timezone ? 'border-rose-500' : '']">
                                    <SelectValue placeholder="e.g. UTC" />
                                    <ChevronDown class="h-3 w-3 opacity-50" />
                                </SelectTrigger>
                                <SelectPortal>
                                    <SelectContent :class="[selectContentClasses, 'max-h-[250px]']" position="popper" :side-offset="5">
                                        <SelectViewport class="p-1 overflow-y-auto max-h-[240px]">
                                            <SelectItem v-for="tz in timezones" :key="tz.value" :value="tz.value" :class="selectItemClasses">
                                                <SelectItemText>{{ tz.label }}</SelectItemText>
                                                <SelectItemIndicator class="absolute right-3">
                                                    <Check class="h-3 w-3 stroke-[3px]" />
                                                </SelectItemIndicator>
                                            </SelectItem>
                                        </SelectViewport>
                                    </SelectContent>
                                </SelectPortal>
                            </SelectRoot>
                            <div v-if="form.errors.timezone" class="text-xs text-rose-500 mt-1 mb-1">{{ form.errors.timezone }}</div>
                            <div class="hint" v-else>Used for scheduling — defaults to UTC if blank</div>
                        </div>
                    </div>

                    <div class="section">
                        <div class="section-title">Password</div>
                        <div class="section-desc">Set a temporary password — user can reset via OTP on first login</div>

                        <div class="field-row">
                            <div>
                                <label>Password</label>
                                <div class="pw-wrap">
                                    <input :type="showPw ? 'text' : 'password'" v-model="form.password" placeholder="Min 8 characters" :class="{ 'border-rose-500': form.errors.password }" autocomplete="new-password" data-lpignore="true">
                                    <button type="button" class="pw-toggle" @click="showPw = !showPw">{{ showPw ? 'Hide' : 'Show' }}</button>
                                </div>
                                <div v-if="form.errors.password" class="text-xs text-rose-500 mt-1">{{ form.errors.password }}</div>
                            </div>
                            <div>
                                <label>Confirm password</label>
                                <div class="pw-wrap">
                                    <input :type="showPw2 ? 'text' : 'password'" v-model="form.password_confirmation" placeholder="Re-enter password" :class="{ 'border-rose-500': form.errors.password_confirmation }" autocomplete="new-password" data-lpignore="true">
                                    <button type="button" class="pw-toggle" @click="showPw2 = !showPw2">{{ showPw2 ? 'Hide' : 'Show' }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="hint" style="margin-top:0">Tip: You can also leave this blank and send the user a "set password" OTP email instead</div>
                    </div>

                    <div class="section">
                        <div class="section-title">Plan assignment</div>
                        <div class="section-desc">Assign a subscription plan — no Stripe charge is triggered from admin</div>

                        <div class="field-row">
                            <div>
                                <label>Plan <span class="req">*</span></label>
                                <SelectRoot v-model="form.plan_id">
                                    <SelectTrigger :class="[selectTriggerClasses, form.errors.plan_id ? 'border-rose-500' : '']">
                                        <SelectValue placeholder="Select plan" />
                                        <ChevronDown class="h-3 w-3 opacity-50" />
                                    </SelectTrigger>
                                    <SelectPortal>
                                        <SelectContent :class="selectContentClasses" position="popper" :side-offset="5">
                                            <SelectViewport class="p-1">
                                                <SelectItem v-for="plan in plans" :key="plan.id" :value="String(plan.id)" :class="selectItemClasses">
                                                    <SelectItemText>{{ plan.name }}</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-3 w-3 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                                <SelectItem v-if="plans.length === 0" value="" :class="selectItemClasses">
                                                    <SelectItemText>No plans available</SelectItemText>
                                                </SelectItem>
                                            </SelectViewport>
                                        </SelectContent>
                                    </SelectPortal>
                                </SelectRoot>
                                <div v-if="form.errors.plan_id" class="text-xs text-rose-500 mt-1">{{ form.errors.plan_id }}</div>
                            </div>
                            <div>
                                <label>Billing cycle</label>
                                <SelectRoot v-model="form.billing_cycle">
                                    <SelectTrigger :class="[selectTriggerClasses, form.errors.billing_cycle ? 'border-rose-500' : '']">
                                        <SelectValue placeholder="Select billing cycle" />
                                        <ChevronDown class="h-3 w-3 opacity-50" />
                                    </SelectTrigger>
                                    <SelectPortal>
                                        <SelectContent :class="selectContentClasses" position="popper" :side-offset="5">
                                            <SelectViewport class="p-1">
                                                <SelectItem value="Monthly" :class="selectItemClasses">
                                                    <SelectItemText>Monthly</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-3 w-3 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                                <SelectItem value="Annual" :class="selectItemClasses">
                                                    <SelectItemText>Annual</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-3 w-3 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                                <SelectItem value="Manual / custom" :class="selectItemClasses">
                                                    <SelectItemText>Manual / custom</SelectItemText>
                                                    <SelectItemIndicator class="absolute right-3">
                                                        <Check class="h-3 w-3 stroke-[3px]" />
                                                    </SelectItemIndicator>
                                                </SelectItem>
                                            </SelectViewport>
                                        </SelectContent>
                                    </SelectPortal>
                                </SelectRoot>
                                <div v-if="form.errors.billing_cycle" class="text-xs text-rose-500 mt-1">{{ form.errors.billing_cycle }}</div>
                            </div>
                        </div>

                        <div class="field-row">
                            <div>
                                <label>Subscription start date</label>
                                <input type="text" ref="startsAtInput" v-model="form.starts_at" :class="{ 'border-rose-500': form.errors.starts_at }" placeholder="Today (auto)" autocomplete="off">
                                <div v-if="form.errors.starts_at" class="text-xs text-rose-500 mt-1 mb-1">{{ form.errors.starts_at }}</div>
                                <div class="hint" v-else>Leave blank to use today's date</div>
                            </div>
                            <div>
                                <label>Subscription end / expiry date</label>
                                <input type="text" ref="endsAtInput" v-model="form.ends_at" :class="{ 'border-rose-500': form.errors.ends_at }" placeholder="e.g. 12 Apr 2026" autocomplete="off">
                                <div v-if="form.errors.ends_at" class="text-xs text-rose-500 mt-1 mb-1">{{ form.errors.ends_at }}</div>
                                <div class="hint" v-else>Leave blank for auto-renewal logic</div>
                            </div>
                        </div>

                        <div class="field">
                            <label>Internal note about plan assignment</label>
                            <input type="text" v-model="form.internal_notes" placeholder="e.g. Complimentary Pro for beta tester" :class="{ 'border-rose-500': form.errors.internal_notes }">
                            <div v-if="form.errors.internal_notes" class="text-xs text-rose-500 mt-1 mb-1">{{ form.errors.internal_notes }}</div>
                            <div class="hint" v-else>Admin-only — not visible to the user</div>
                        </div>
                    </div>

                    <div class="section">
                        <div class="section-title">Account settings</div>
                        <div class="section-desc">Control initial account state and email behaviour</div>

                        <div class="toggle-row">
                            <div class="toggle-info">
                                <div class="tl">Mark email as verified</div>
                                <div class="th">Skips OTP email verification on first login</div>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" v-model="form.mark_email_verified">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="toggle-row">
                            <div class="toggle-info">
                                <div class="tl">Account active</div>
                                <div class="th">Uncheck to create the account in suspended state</div>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" v-model="form.is_active">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="toggle-row">
                            <div class="toggle-info">
                                <div class="tl">Send welcome email</div>
                                <div class="th">Sends login link + getting started instructions</div>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" v-model="form.send_welcome_email">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="toggle-row">
                            <div class="toggle-info">
                                <div class="tl">Require password change on first login</div>
                                <div class="th">Forces user to set their own password immediately</div>
                            </div>
                            <label class="toggle">
                                <input type="checkbox" v-model="form.require_password_change">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="sidebar-card">
                        <div class="sidebar-title">Preview</div>
                        <div class="preview-avatar">{{ initials }}</div>
                        <div class="preview-name">{{ displayName }}</div>
                        <div class="preview-email">{{ displayEmail }}</div>
                        <div class="preview-badges">
                            <span v-if="form.is_active" class="badge badge-active">Active</span>
                            <span v-else class="badge bg-amber-100 text-amber-700">Suspended</span>
                            <span class="badge" :class="planBadgeClass">{{ displayPlan }}</span>
                        </div>
                    </div>

                    <div class="sidebar-card">
                        <div class="sidebar-title">Summary</div>
                        <div class="summary-row"><span class="sk">Name</span><span class="sv">{{ displayName }}</span></div>
                        <div class="summary-row"><span class="sk">Plan</span><span class="sv">{{ displayPlan }} · {{ form.billing_cycle }}</span></div>
                        <div class="summary-row">
                            <span class="sk">Email verified</span>
                            <span class="sv" :style="form.mark_email_verified ? 'color: #10b981' : 'color: hsl(var(--muted-foreground))'">
                                {{ form.mark_email_verified ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="summary-row">
                            <span class="sk">Welcome email</span>
                            <span class="sv" :style="form.send_welcome_email ? 'color: #10b981' : 'color: hsl(var(--muted-foreground))'">
                                {{ form.send_welcome_email ? 'Will send' : 'Skipped' }}
                            </span>
                        </div>
                        <div class="summary-row"><span class="sk">Post limit</span><span class="sv">100 / month</span></div>
                        <div class="summary-row">
                            <span class="sk">Status</span>
                            <span class="sv" :style="form.is_active ? 'color: #10b981' : 'color: #f59e0b'">
                                {{ form.is_active ? 'Active' : 'Suspended' }}
                            </span>
                        </div>
                    </div>

                    <div class="info-box">
                        No Stripe charge is triggered when creating a user from admin. Billing will activate when the user connects payment details from their panel.
                    </div>

                    <div class="btn-bar">
                        <Link :href="route('admin.users.index')" class="btn-secondary w-1/2">Cancel</Link>
                        <button
                            type="submit"
                            class="w-1/2 justify-center h-12 text-base font-bold rounded-md bg-primary text-primary-foreground hover:bg-primary/90 shadow-lg shadow-primary/20 transition-all disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Creating...' : 'Create user' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<style scoped>
* { box-sizing: border-box; }
.topbar { display: flex; align-items: flex-start; gap: 8px; margin-bottom: 24px }
.back-btn { display: flex; align-items: center; gap: 5px; font-size: 13px; color: hsl(var(--muted-foreground)); cursor: pointer; background: none; border: none; padding: 0; outline: none; margin-top: 4px; text-decoration: none }
.back-btn:hover { color: hsl(var(--foreground)) }
.crumb-sep { color: hsl(var(--muted-foreground)); opacity: 0.5; font-size: 13px; margin-top: 3px }
.page-title { font-size: 20px; font-weight: 600; color: hsl(var(--foreground)); line-height: 1.2 }
.page-sub { font-size: 13px; color: hsl(var(--muted-foreground)); margin-top: 4px }
.layout { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 20px; align-items: start }

.section, .sidebar-card { background: hsl(var(--card)); box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); border: 1px solid hsl(var(--border)); border-radius: var(--radius, 0.75rem); padding: 1.5rem; margin-bottom: 16px }
.section:last-child, .sidebar-card:last-child { margin-bottom: 0 }

.section-title, .sidebar-title { font-size: 14px; font-weight: 600; color: hsl(var(--foreground)); margin-bottom: 4px }
.sidebar-title { margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid hsl(var(--border)) }
.section-desc { font-size: 13px; color: hsl(var(--muted-foreground)); margin-bottom: 1.25rem; padding-bottom: 1.25rem; border-bottom: 1px solid hsl(var(--border)) }

.field { margin-bottom: 1rem }
.field:last-child { margin-bottom: 0 }
.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 1rem }
.field-row:last-child { margin-bottom: 0 }

label { display: block; font-size: 13px; font-weight: 500; color: hsl(var(--foreground)); margin-bottom: 6px }
.req { color: #ef4444; margin-left: 2px }
.hint { font-size: 12px; color: hsl(var(--muted-foreground)); opacity: 0.9; margin-top: 6px }

input[type=text], input[type=email], input[type=password], select { 
    width: 100%; 
    font-size: 13px; 
    border-radius: calc(var(--radius) - 2px); 
    border: 1px solid hsl(var(--input)); 
    background: hsl(var(--background)); 
    padding: 8px 12px; 
    color: hsl(var(--foreground));
    transition: all 0.2s;
}
input:focus, select:focus { outline: none; border-color: hsl(var(--ring)); box-shadow: 0 0 0 2px hsl(var(--ring) / 0.2) }

/* Fix Webkit autofill turning white in dark mode */
input:-webkit-autofill,
input:-webkit-autofill:hover, 
input:-webkit-autofill:focus, 
input:-webkit-autofill:active {
    -webkit-box-shadow: 0 0 0 30px hsl(var(--background)) inset !important;
    -webkit-text-fill-color: hsl(var(--foreground)) !important;
    transition: background-color 5000s ease-in-out 0s;
}

.toggle-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid hsl(var(--border)) }
.toggle-row:last-child { border-bottom: none; padding-bottom: 0 }
.toggle-row:first-child { padding-top: 0 }
.toggle-info .tl { font-size: 13px; font-weight: 500; color: hsl(var(--foreground)) }
.toggle-info .th { font-size: 12px; color: hsl(var(--muted-foreground)); margin-top: 2px }

.toggle { position: relative; width: 36px; height: 20px; flex-shrink: 0; margin-left: 12px }
.toggle input { opacity: 0; width: 0; height: 0; position: absolute }
.slider { position: absolute; inset: 0; background: hsl(var(--muted)); border-radius: 10px; cursor: pointer; transition: background .2s, border-color .2s; border: 1px solid transparent; }
.toggle input:checked + .slider { background: #10b981; border-color: #059669; }
.slider:before { content: ''; position: absolute; width: 14px; height: 14px; left: 2px; top: 2px; background: white; border-radius: 50%; box-shadow: 0 1px 2px rgba(0,0,0,0.1); transition: transform .2s }
.toggle input:checked + .slider:before { transform: translateX(16px) }

.preview-avatar { width: 56px; height: 56px; border-radius: 50%; background: #EEEDFE; color: #3C3489; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 600; margin: 0 auto 12px }
.preview-name { font-size: 15px; font-weight: 600; color: hsl(var(--foreground)); text-align: center }
.preview-email { font-size: 13px; color: hsl(var(--muted-foreground)); text-align: center; margin-top: 2px }

.preview-badges { display: flex; justify-content: center; gap: 8px; margin-top: 10px; flex-wrap: wrap }
.badge { display: inline-flex; align-items: center; justify-content: center; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; letter-spacing: 0.02em }
.badge-active { background: #EAF3DE; color: #27500A }
.badge-free { background: #F1EFE8; color: #444441 }
.badge-pro { background: #EEEDFE; color: #3C3489 }
.badge-biz { background: #E1F5EE; color: #085041 }

.summary-row { display: flex; justify-content: space-between; font-size: 13px; padding: 8px 0; border-bottom: 1px solid hsl(var(--border)) }
.summary-row:last-child { border-bottom: none }
.sk { color: hsl(var(--muted-foreground)) }
.sv { color: hsl(var(--foreground)); font-weight: 500; text-align: right; }

.info-box { background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: var(--radius, 0.5rem); padding: 12px 14px; font-size: 12px; color: #2563eb; margin-bottom: 16px; line-height: 1.5 }

.btn-bar { display: flex; gap: 12px; justify-content: flex-end }
.btn-primary { background: hsl(var(--primary)); color: hsl(var(--primary-foreground)); border: none; padding: 10px 24px; border-radius: calc(var(--radius) - 2px); font-size: 13px; font-weight: 500; cursor: pointer; transition: opacity .2s }
.btn-secondary { background: transparent; color: hsl(var(--foreground)); border: 1px solid hsl(var(--input)); padding: 10px 24px; border-radius: calc(var(--radius) - 2px); font-size: 13px; font-weight: 500; cursor: pointer; transition: background .2s, text-decoration .2s; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
.btn-primary:hover { opacity: .9; }
.btn-secondary:hover { background: hsl(var(--muted)); }

.pw-wrap { position: relative }
.pw-toggle { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: hsl(var(--muted-foreground)); font-size: 12px; font-weight: 500; padding: 0 }
.pw-wrap input { padding-right: 56px }
</style>

<style>
/* Override Flatpickr dark theme to match the app's dark theme */
.flatpickr-calendar {
    background: hsl(var(--card)) !important;
    border: 1px solid hsl(var(--border)) !important;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.2), 0 2px 4px -2px rgb(0 0 0 / 0.2) !important;
}
.flatpickr-calendar.arrowTop:before { border-bottom-color: hsl(var(--border)) !important; }
.flatpickr-calendar.arrowTop:after { border-bottom-color: hsl(var(--card)) !important; }
.flatpickr-calendar.arrowBottom:before { border-top-color: hsl(var(--border)) !important; }
.flatpickr-calendar.arrowBottom:after { border-top-color: hsl(var(--card)) !important; }

.flatpickr-day { color: hsl(var(--foreground)) !important; }
.flatpickr-day:hover, .flatpickr-day:focus {
    background: hsl(var(--muted)) !important;
    border-color: hsl(var(--muted)) !important;
}
.flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay {
    background: hsl(var(--primary)) !important;
    border-color: hsl(var(--primary)) !important;
    color: hsl(var(--primary-foreground)) !important;
}
.flatpickr-months .flatpickr-month {
    background: hsl(var(--card)) !important;
    color: hsl(var(--foreground)) !important;
    fill: hsl(var(--foreground)) !important;
    height: 42px !important;
}
.flatpickr-current-month .flatpickr-monthDropdown-months,
.flatpickr-current-month input.cur-year {
    font-size: 14px !important;
    font-weight: 600 !important;
    background: transparent !important;
    border: none !important;
    color: hsl(var(--foreground)) !important;
    appearance: none !important; /* Hide native select arrow */
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    padding: 8px !important;
    border-radius: 4px !important;
    cursor: pointer;
}
.flatpickr-current-month .flatpickr-monthDropdown-months:hover,
.flatpickr-current-month input.cur-year:hover {
    background: hsl(var(--muted)) !important;
}
.flatpickr-current-month .flatpickr-monthDropdown-months .flatpickr-monthDropdown-month {
    background-color: hsl(var(--card)) !important;
    color: hsl(var(--foreground)) !important;
    font-size: 14px !important;
}
span.flatpickr-weekday {
    background: hsl(var(--card)) !important;
    color: hsl(var(--muted-foreground)) !important;
}
.flatpickr-months .flatpickr-prev-month, .flatpickr-months .flatpickr-next-month {
    fill: hsl(var(--foreground)) !important;
    display: flex;
    align-items: center;
    width: 10%;
    justify-content: center;
    padding: 0;
    height: 42px;
}
.flatpickr-months .flatpickr-prev-month:hover, .flatpickr-months .flatpickr-next-month:hover {
    fill: hsl(var(--primary)) !important;
}
.flatpickr-day.today {
    border-color: hsl(var(--primary)) !important;
}
.flatpickr-day.flatpickr-disabled, .flatpickr-day.flatpickr-disabled:hover {
    color: hsl(var(--muted-foreground)) !important;
}

/* Scrollbar for Select Viewport dropdowns */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}
.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
    background: hsl(var(--muted));
    border-radius: 4px;
}
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: hsl(var(--muted-foreground));
}
</style>
