<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Undo2 } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { index, update } from '@/routes/users'
import UserForm from './UserForm.vue'

// Bentuk data user yang dikirim dari UserController@edit (via UserDetailResource).
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

// Form diisi dari data user yang sudah ada (bukan kosong seperti di Create.vue).
// Tidak ada field password -> saat edit, password tidak diubah lewat form ini.
const form = useForm({
    name: props.user.data.name,
    email: props.user.data.email,
    role: props.user.data.role ?? '',
})

// Submit pakai method PUT (update) ke endpoint /users/{id}, bukan POST seperti create.
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
            <!-- show-password: false -> field password disembunyikan di form edit -->
            <UserForm v-model:form="form" :roles="roles" :show-password="false" />

            <div class="flex items-center justify-end gap-3 border-t border-border pt-4">
                <!-- Catatan: tombol ini belum punya :href/@click, jadi belum benar-benar navigasi kembali -->
                <Button variant="outline" as-child>
                    <Link :href="index()" class="flex items-center gap-2">
                        <Undo2 />
                        Kembali
                    </Link>
                </Button>
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                </Button>
            </div>
        </form>
    </div>
</template>
