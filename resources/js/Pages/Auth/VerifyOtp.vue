<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    otp: '',
});

const submit = () => {
    form.post(route('otp.verify'), {
        onFinish: () => form.reset('otp'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Verify OTP" />

        <div class="text-center mb-3">
            <h1 class="text-2xl font-bold mb-1">Verify Your Email</h1>
            <div class="text-base text-muted-foreground mb-4">A One-Time Password (OTP) has been sent to your email address. Please enter it below to verify your account.</div>
        </div>
        <div
            class="border text-card-foreground w-full max-w-md bg-card border-border shadow-2xl rounded-2xl overflow-hidden">
            <form @submit.prevent="submit" class="bg-card p-8 rounded-2xl shadow flex flex-col gap-6">
                <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                    {{ status }}
                </div>
                <div class="flex flex-col">
                    <label for="otp" class="text-xs font-bold uppercase mb-2 text-muted-foreground tracking-widest">
                        OTP
                    </label>
                    <TextInput id="otp" type="text" class="h-12" v-model="form.otp" required autofocus
                        autocomplete="one-time-code" placeholder="Enter OTP" />
                    <InputError class="mt-2" :message="form.errors.otp" />
                </div>
                <div class="flex flex-col gap-2">
                    <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Verify
                    </PrimaryButton>
                    <div class="text-center pt-2">
                        <Link :href="route('otp.resend')" method="post" as="button"
                            class="text-sm font-medium text-muted-foreground hover:text-primary">
                            Resend OTP
                        </Link>
                    </div>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
