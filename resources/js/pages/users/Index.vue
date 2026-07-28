<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Archive, PencilIcon, Trash2Icon } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { usePermission } from '@/composables/usePermission';
import usersRoute, { destroy, edit, trashed } from '@/routes/users';
import { useTableQuery } from '@/composables/useTableQuery';

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

const props = defineProps<{
    users: { data: any[]; links: any[] }
    filters: {
        search: string;
        role: string;
        sort: string;
        direction: 'asc' | 'desc'
    }
    roles: string[]
}>()

const { query, sortBy } = useTableQuery(usersRoute.index, {
    search: props.filters.search ?? '',
    role: props.filters.role ?? '',
    sort: props.filters.sort ?? 'created_at',
    direction: props.filters.direction ?? 'desc',
})

const { can } = usePermission();

function confirmDelete(): boolean {
    return window.confirm('Yakin hapus user ini?')
}
</script>

<template>

    <Head title="Manage User" />

    <div class="p-6 space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-foreground">Manajemen User</h1>
                <p class="text-sm text-muted-foreground">Kelola user, role, dan akses aplikasi.</p>
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center justify-between">
            <div class="gap-2 flex">
                <input v-model="query.search" type="text" placeholder="Cari nama/email..."
                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring sm:w-64" />

                <select v-model="query.role"
                    class="h-9 rounded-md border border-input bg-background px-3 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring sm:w-48">
                    <option value="">Semua Role</option>
                    <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                </select>
            </div>
            <div class="gap-2 flex items-center">
                <Button v-if="can('users.create')">
                    <Link :href="usersRoute.create()">
                        + Tambah User
                    </Link>
                </Button>
                <Button v-if="can('users.restore')">
                    <Link :href="trashed()" class="text-sm hover:underline">
                        <Trash2Icon class="w-4" />
                    </Link>
                </Button>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-border">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left text-muted-foreground">
                    <tr class="border-b border-border">
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
                    <tr v-if="users.data.length === 0">
                        <td colspan="4" class="px-4 py-10 text-center text-muted-foreground">
                            Tidak ada user ditemukan.
                        </td>
                    </tr>
                    <tr v-for="user in users.data" :key="user.id" class="transition-colors hover:bg-muted/40">
                        <td class="px-4 py-3 font-medium text-foreground">{{ user.name }}</td>
                        <td class="px-4 py-3 text-muted-foreground">{{ user.email }}</td>
                        <td class="px-4 py-3">
                            <span v-for="role in user.roles" :key="role"
                                class="mr-1 inline-flex items-center rounded-full bg-secondary px-2 py-0.5 text-xs font-medium text-secondary-foreground">
                                {{ role }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-3">

                                <Link :href="edit(user.id)" class="text-sm font-medium text-primary hover:underline">
                                    <PencilIcon class="w-4 cursor" />
                                </Link>
                                <Link :href="destroy(user.id)" method="delete" as="button" @before="confirmDelete"
                                    class="text-sm font-medium text-destructive hover:underline">
                                    <Archive class="w-4 cursor" />
                                </Link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
