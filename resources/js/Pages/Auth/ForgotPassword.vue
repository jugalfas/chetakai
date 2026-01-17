<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon, MoveLeft } from 'lucide-vue-next';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>

        <Head title="Forgot Password" />

        <div
            class="border text-card-foreground w-full max-w-md bg-card border-border shadow-2xl rounded-2xl overflow-hidden">
            <form @submit.prevent="submit" class="p-8 space-y-6">
                <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                    {{ status }}
                </div>

                <div class="text-sm text-muted-foreground leading-relaxed">
                    Forgot your password? No problem. Just let us know your email
                    address and we will email you a password reset link that will allow
                    you to choose a new one.
                </div>
                <div>
                    <InputLabel for="email" value="Email" />

                    <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus
                        autocomplete="username" />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4 flex items-center justify-end">
                    <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Email Password Reset Link
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
