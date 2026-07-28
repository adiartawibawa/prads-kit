<!-- resources/js/Pages/Users/Create.vue -->
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { store } from '@/routes/users'
import UserForm from './UserForm.vue';

defineProps<{ roles: string[] }>()

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: '',
})

function submit() {
    form.post(store().url)
}
</script>

<template>

    <Head title="Tambah User" />

    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-tight text-foreground">Tambah User</h1>
            <p class="text-sm text-muted-foreground">Buat akun user baru beserta role-nya.</p>
        </div>

        <form @submit.prevent="submit" class="max-w-lg space-y-6 rounded-lg border border-border p-6">
            <UserForm v-model:form="form" :roles="roles" :show-password="true" />

            <div class="flex items-center justify-end gap-3 border-t border-border pt-4">
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                </Button>
            </div>
        </form>
    </div>
</template>
