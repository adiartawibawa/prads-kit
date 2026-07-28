<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Archive, PencilIcon, Trash2Icon, UserPlus2 } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { usePermission } from '@/composables/usePermission';
import { useTableQuery } from '@/composables/useTableQuery';
import usersRoute, { destroy, edit, trashed } from '@/routes/users';

// Atur layout halaman + breadcrumb (link "Manage Users" di navigasi atas).
defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Manage Users',
                href: usersRoute.index(),
            },
        ],
    },
});

// Props ini dikirim dari UserController@index (Inertia::render).
const props = defineProps<{
    users: { data: any[]; links: any[] } // data user (hasil pagination) + link pagination
    filters: {
        search: string;
        role: string;
        sort: string;
        direction: 'asc' | 'desc'
    }
    roles: string[] // daftar role untuk dropdown filter
}>()

// Sinkronkan filter/search/sort ke URL, otomatis kirim request ke server saat berubah.
// Nilai awal diambil dari filters yang dikirim controller (biar tetap konsisten setelah reload/back).
const { query, sortBy } = useTableQuery(usersRoute.index, {
    search: props.filters.search ?? '',
    role: props.filters.role ?? '',
    sort: props.filters.sort ?? 'created_at',
    direction: props.filters.direction ?? 'desc',
})

// Untuk cek hak akses, dipakai buat tampilkan/sembunyikan tombol (mis. Tambah User).
const { can } = usePermission();

// Konfirmasi browser sebelum request hapus user dikirim (dipanggil via event @before Inertia Link).
function confirmDelete(): boolean {
    return window.confirm('Yakin hapus user ini?')
}
</script>

<template>

    <Head title="Manage User" />

    <div class="p-6 space-y-6">
        <!-- Header halaman -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-foreground">Manajemen User</h1>
                <p class="text-sm text-muted-foreground">Kelola user, role, dan akses aplikasi.</p>
            </div>
        </div>

        <!-- Toolbar: search, filter role, dan tombol aksi -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center justify-between">
            <div class="gap-2 flex">
                <!-- v-model langsung ke query.search -> otomatis trigger request (debounced) via useTableQuery -->
                <input v-model="query.search" type="text" placeholder="Cari nama/email..."
                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring sm:w-64" />

                <!-- v-model ke query.role -> termasuk immediateFields, jadi langsung request tanpa debounce -->
                <select v-model="query.role"
                    class="h-9 rounded-md border border-input bg-background px-3 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring sm:w-48">
                    <option value="">Semua Role</option>
                    <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                </select>
            </div>
            <div class="gap-2 flex items-center">
                <!-- Tombol hanya muncul kalau user punya permission terkait -->
                <Button v-if="can('users.create')" as-child size="sm" class="bg-emerald-700 text-slate-50">
                    <Link :href="usersRoute.create()" class="flex items-center gap-2 cursor-pointer">
                        <UserPlus2 />
                        Add User
                    </Link>
                </Button>
                <Button v-if="can('users.restore')" size="sm">
                    <!-- Link ke halaman "Trashed" (daftar user yang sudah dihapus/soft delete) -->
                    <Link :href="trashed()" class="text-sm hover:underline">
                        <Trash2Icon />
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Tabel data user -->
        <div class="overflow-hidden rounded-lg border border-border">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left text-muted-foreground">
                    <tr class="border-b border-border">
                        <!-- Klik header kolom untuk sorting; panah ↑/↓ menandakan kolom & arah sort aktif -->
                        <th @click="sortBy('name')"
                            class="cursor-pointer select-none px-4 py-3 font-medium hover:text-foreground">
                            <span class="inline-flex items-center gap-1">
                                Nama
                                <span v-if="query.sort === 'name'" class="text-xs">
                                    {{ query.direction === 'asc' ? '↑' : '↓' }}
                                </span>
                            </span>
                        </th>
                        <th @click="sortBy('email')"
                            class="cursor-pointer select-none px-4 py-3 font-medium hover:text-foreground">
                            <span class="inline-flex items-center gap-1">
                                Email
                                <span v-if="query.sort === 'email'" class="text-xs">
                                    {{ query.direction === 'asc' ? '↑' : '↓' }}
                                </span>
                            </span>
                        </th>
                        <th class="px-4 py-3 font-medium">Role</th>
                        <th class="px-4 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <!-- State kosong: tampil kalau hasil filter/search tidak ada datanya -->
                    <tr v-if="users.data.length === 0">
                        <td colspan="4" class="px-4 py-10 text-center text-muted-foreground">
                            Tidak ada user ditemukan.
                        </td>
                    </tr>
                    <!-- Loop tiap baris user -->
                    <tr v-for="user in users.data" :key="user.id" class="transition-colors hover:bg-muted/40">
                        <td class="px-4 py-3 font-medium text-foreground">{{ user.name }}</td>
                        <td class="px-4 py-3 text-muted-foreground">{{ user.email }}</td>
                        <td class="px-4 py-3">
                            <!-- Badge untuk tiap role yang dimiliki user (bisa lebih dari satu) -->
                            <span v-for="role in user.roles" :key="role"
                                class="mr-1 inline-flex items-center rounded-full bg-secondary px-2 py-0.5 text-xs font-medium text-secondary-foreground">
                                {{ role }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">

                                <!-- Ke halaman edit user -->
                                <Button as-child variant="outline" size="sm">
                                    <Link :href="edit(user.id)"
                                        class="text-sm font-medium text-primary hover:underline">
                                        <PencilIcon class="w-4" />
                                    </Link>
                                </Button>
                                <!-- Hapus user (soft delete) via method DELETE Inertia; konfirmasi dulu sebelum request dikirim -->
                                <Button as-child variant="outline" size="sm">
                                    <Link :href="destroy(user.id)" method="delete" as="button" @before="confirmDelete"
                                        class="text-sm font-medium text-destructive hover:underline">
                                        <Archive class="w-4" />
                                    </Link>
                                </Button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
