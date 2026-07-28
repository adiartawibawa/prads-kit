<!-- resources/js/Pages/Users/Edit.vue -->
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { update } from '@/routes/users'
import UserForm from './UserForm.vue'

interface UserDetail {
    id: string
    name: string
    email: string
    role: string | null
}

const props = defineProps<{
    user: { data: UserDetail }
    roles: string[]
}>()

const form = useForm({
    name: props.user.data.name,
    email: props.user.data.email,
    role: props.user.data.role ?? '',
})

function submit() {
    form.put(update(props.user.data.id).url)
}
</script>

<template>

    <Head title="Edit User" />

    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-tight text-foreground">Edit User</h1>
            <p class="text-sm text-muted-foreground">Perbarui data dan role user.</p>
        </div>

        <form @submit.prevent="submit" class="max-w-lg space-y-6 rounded-lg border border-border p-6">
            <UserForm v-model:form="form" :roles="roles" :show-password="false" />

            <div class="flex items-center justify-end gap-3 border-t border-border pt-4">
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                </Button>
            </div>
        </form>
    </div>
</template>
