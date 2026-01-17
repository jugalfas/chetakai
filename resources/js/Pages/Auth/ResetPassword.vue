<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Reset Password" />

        <div class="text-center mb-3">
            <h1 class="text-2xl font-bold mb-1">Reset Password</h1>
            <div class="text-base text-muted-foreground mb-4">Enter your new password below</div>
        </div>
        <div
            class="border text-card-foreground w-full max-w-md bg-card border-border shadow-2xl rounded-2xl overflow-hidden">
            <form @submit.prevent="submit" class="p-8 space-y-6">
                <div>
                    <InputLabel for="email" value="Email" />

                    <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" autofocus disabled=""
                        autocomplete="username" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="New Password" />

                    <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required
                        autocomplete="new-password" />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password_confirmation" value="Confirm New Password" />

                    <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                        v-model="form.password_confirmation" required autocomplete="new-password" />

                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <div class="mt-4 flex items-center justify-end">
                    <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Reset Password
                    </PrimaryButton>
                </div>
                <div class="text-center pt-2">
                    <Link :href="route('login')"
                        class="text-sm font-medium text-muted-foreground hover:text-primary flex items-center justify-center gap-2">
                        <ArrowLeftIcon class="h-4 w-4" />
                        Back to Login
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
