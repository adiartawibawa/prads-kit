<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import { Undo2 } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { index, store } from '@/routes/users'
import UserForm from './UserForm.vue';

// Daftar role dikirim dari UserController@create, dipakai sebagai pilihan di UserForm.
defineProps<{ roles: string[] }>()

// useForm dari Inertia: state form + helper submit yang otomatis handle
// loading state (processing), error validasi (form.errors), dsb.
const form = useForm({
    name: '',
    email: '',
    password: '',
    role: '',
})

// Submit form ke endpoint store (POST /users). Validasi & penyimpanan
// ditangani backend (StoreUserRequest + CreateUser action).
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

        <!-- @submit.prevent supaya browser tidak reload halaman, submit ditangani via Inertia -->
        <form @submit.prevent="submit" class="max-w-lg space-y-6 rounded-lg border border-border p-6">
            <!-- Komponen form reusable (dipakai juga di Edit.vue), form di-bind via v-model:form -->
            <!-- show-password: true karena di form tambah, password wajib diisi (beda dgn edit) -->
            <UserForm v-model:form="form" :roles="roles" :show-password="true" />

            <div class="flex items-center justify-end gap-3 border-t border-border pt-4">
                <Button variant="outline" as-child>
                    <Link :href="index()" class="flex items-center gap-2 cursor-pointer">
                        <Undo2 />
                        Kembali
                    </Link>
                </Button>
                <!-- Tombol disabled + ubah teks saat request masih diproses, mencegah submit ganda -->
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                </Button>
            </div>
        </form>
    </div>
</template>
