<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div
            class="border text-card-foreground w-full max-w-md bg-card border-border shadow-2xl rounded-2xl overflow-hidden">
            <form @submit.prevent="submit" class="p-8 space-y-6">
                <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                    {{ status }}
                </div>
                <div>
                    <TextInput id="email" type="email" class="h-12" placeholder="Email" v-model="form.email" required
                        autofocus autocomplete="username" />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <TextInput id="password" type="password" class="h-12" placeholder="Password" v-model="form.password"
                        required autocomplete="new-password" />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-muted-foreground">Remember
                            me</span>
                    </label>
                    <Link v-if="canResetPassword" :href="route('password.request')"
                        class="text-sm font-medium text-muted-foreground hover:text-primary underline underline-offset-4">
                        Forgot your password?
                    </Link>
                </div>

                <div class="mt-4 flex items-center justify-end">
                    <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Login
                    </PrimaryButton>
                </div>
                <div class="text-center pt-2">
                    <Link :href="route('register')"
                        class="text-sm font-medium text-muted-foreground hover:text-primary">
                        Don't have an account? Create one
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
