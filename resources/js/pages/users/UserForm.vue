<script setup lang="ts">

import type { InertiaForm } from '@inertiajs/vue3'


interface UserFormData {
    name: string
    email: string
    password?: string
    role: string
    errors?: Record<string, string>
    processing?: boolean
}

defineProps<{
    roles: string[]
    showPassword?: boolean
}>()

const form = defineModel<InertiaForm<UserFormData>>('form', { required: true })

</script>

<template>
    <div class="space-y-5">
        <div class="space-y-1.5">
            <label for="name" class="text-sm font-medium text-foreground">Nama</label>
            <input id="name" v-model="form.name" type="text" placeholder="Nama lengkap"
                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                :class="{ 'border-destructive focus-visible:ring-destructive': form.errors.name }" />
            <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
        </div>

        <div class="space-y-1.5">
            <label for="email" class="text-sm font-medium text-foreground">Email</label>
            <input id="email" v-model="form.email" type="email" placeholder="nama@contoh.com"
                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                :class="{ 'border-destructive focus-visible:ring-destructive': form.errors.email }" />
            <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
        </div>

        <div v-if="showPassword" class="space-y-1.5">
            <label for="password" class="text-sm font-medium text-foreground">Password</label>
            <input id="password" v-model="form.password" type="password" placeholder="Minimal 8 karakter"
                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                :class="{ 'border-destructive focus-visible:ring-destructive': form.errors.password }" />
            <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
        </div>

        <div class="space-y-1.5">
            <label for="role" class="text-sm font-medium text-foreground">Role</label>
            <select id="role" v-model="form.role"
                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                :class="{ 'border-destructive focus-visible:ring-destructive': form.errors.role }">
                <option value="" disabled>Pilih Role</option>
                <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
            </select>
            <p v-if="form.errors.role" class="text-sm text-destructive">{{ form.errors.role }}</p>
        </div>
    </div>
</template>
