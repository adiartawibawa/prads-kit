<script setup lang="ts">

import type { InertiaForm } from '@inertiajs/vue3'

// Bentuk data form user (dipakai bareng di Create.vue & Edit.vue).
// password opsional karena tidak selalu dipakai (mis. saat edit).
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
    showPassword?: boolean // kontrol tampil/sembunyi field password (true di Create, false di Edit)
}>()

// defineModel: two-way binding v-model:form dari parent (Create.vue/Edit.vue).
// Jadi komponen ini "berbagi" objek form yang sama dengan parent, bukan copy terpisah.
const form = defineModel<InertiaForm<UserFormData>>('form', { required: true })

</script>

<template>
    <div class="space-y-5">
        <!-- Field Nama -->
        <div class="space-y-1.5">
            <label for="name" class="text-sm font-medium text-foreground">Nama</label>
            <input id="name" v-model="form.name" type="text" placeholder="Nama lengkap"
                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                :class="{ 'border-destructive focus-visible:ring-destructive': form.errors.name }" />
            <!-- Pesan error validasi dari backend (form.errors diisi otomatis oleh Inertia useForm) -->
            <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
        </div>

        <!-- Field Email -->
        <div class="space-y-1.5">
            <label for="email" class="text-sm font-medium text-foreground">Email</label>
            <input id="email" v-model="form.email" type="email" placeholder="nama@contoh.com"
                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                :class="{ 'border-destructive focus-visible:ring-destructive': form.errors.email }" />
            <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
        </div>

        <!-- Field Password: hanya render kalau showPassword true (mis. di form Tambah User) -->
        <div v-if="showPassword" class="space-y-1.5">
            <label for="password" class="text-sm font-medium text-foreground">Password</label>
            <input id="password" v-model="form.password" type="password" placeholder="Minimal 8 karakter"
                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                :class="{ 'border-destructive focus-visible:ring-destructive': form.errors.password }" />
            <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
        </div>

        <!-- Field Role (dropdown dari daftar roles yang dikirim dari controller) -->
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
