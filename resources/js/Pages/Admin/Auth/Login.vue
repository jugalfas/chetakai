<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
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
    form.post(route('admin.login.store'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Admin Log in" />

        <div class="border text-card-foreground w-full max-w-md bg-card border-border shadow-2xl rounded-2xl overflow-hidden">
            <div class="p-8 bg-primary/5 border-b border-border text-center">
                <h2 class="text-2xl font-bold text-foreground">Admin Login</h2>
                <p class="text-sm text-muted-foreground mt-1">Please sign in to access the admin panel</p>
            </div>
            
            <form @submit.prevent="submit" class="p-8 space-y-6">
                <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                    {{ status }}
                </div>
                
                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput 
                        id="email" 
                        type="email" 
                        class="mt-1 block w-full h-12" 
                        v-model="form.email" 
                        required
                        autofocus 
                        autocomplete="username" 
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="Password" />
                    <TextInput 
                        id="password" 
                        type="password" 
                        class="mt-1 block w-full h-12" 
                        v-model="form.password"
                        required 
                        autocomplete="current-password" 
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4 flex items-center">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ml-2 text-sm text-muted-foreground">Remember me</span>
                    </label>
                </div>

                <div class="mt-6 flex items-center justify-end">
                    <PrimaryButton type="submit" class="w-full justify-center h-12" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Login as Admin
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
