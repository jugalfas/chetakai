<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Register" />

        <div class="text-center mb-3">
            <h1 class="text-2xl font-bold mb-1">Create Account</h1>
            <div class="text-base text-muted-foreground mb-4">Join Chetak to start automating</div>
        </div>
        <div
            class="border text-card-foreground w-full max-w-md bg-card border-border shadow-2xl rounded-2xl overflow-hidden">
            <form @submit.prevent="submit" class="bg-card p-8 rounded-2xl shadow flex flex-col gap-6">
                <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                    {{ status }}
                </div>
                <div class="flex gap-4">
                    <div class="flex flex-1 flex-col">
                        <label for="first_name"
                            class="text-xs font-bold uppercase mb-2 text-muted-foreground tracking-widest">
                            First Name
                        </label>
                        <TextInput id="first_name" type="text" class="h-12" v-model="form.name" required autofocus
                            autocomplete="given-name" placeholder="First Name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div class="flex flex-1 flex-col">
                        <label for="last_name"
                            class="text-xs font-bold uppercase mb-2 text-muted-foreground tracking-widest">
                            Last Name
                        </label>
                        <TextInput id="last_name" type="text" class="h-12" v-model="form.last_name" required
                            autocomplete="family-name" placeholder="Last Name" />
                        <InputError class="mt-2" :message="form.errors.last_name" />
                    </div>
                </div>
                <div class="flex flex-col">
                    <label for="email" class="text-xs font-bold uppercase mb-2 text-muted-foreground tracking-widest">
                        Email
                    </label>
                    <TextInput id="email" type="email" class="h-12" v-model="form.email" required
                        autocomplete="username" placeholder="jugal@chetak.ai" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
                <div class="flex flex-col">
                    <label for="password"
                        class="text-xs font-bold uppercase mb-2 text-muted-foreground tracking-widest">
                        Password
                    </label>
                    <TextInput id="password" type="password" class="h-12" v-model="form.password" required
                        autocomplete="new-password" placeholder="Password" />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
                <div class="flex flex-col">
                    <label for="password_confirmation"
                        class="text-xs font-bold uppercase mb-2 text-muted-foreground tracking-widest">
                        Confirm Password
                    </label>
                    <TextInput id="password_confirmation" type="password" class="h-12"
                        v-model="form.password_confirmation" required autocomplete="new-password"
                        placeholder="Confirm Password" />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
                <div class="flex flex-col gap-2">
                    <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Register
                    </PrimaryButton>
                    <div class="text-center pt-2">
                        <Link :href="route('login')"
                            class="text-sm font-medium text-muted-foreground hover:text-primary">
                            Already have an account? Login
                        </Link>
                    </div>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
