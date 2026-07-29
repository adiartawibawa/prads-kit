<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Archive, ArrowDownAZ, ArrowDownZA, PencilIcon, ShieldPlus } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { usePermission } from '@/composables/usePermission';
import rolesRoute, { destroy, edit } from '@/routes/roles';
import { useTableQuery } from '@/composables/useTableQuery';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Manage Roles & Permissions',
                href: rolesRoute.index(),
            }
        ],
    }
})

const props = defineProps<{
    roles: { data: any[]; links: any[] } // data user (hasil pagination) + link pagination
    filters: {
        search: string;
        role: string;
        sort: string;
        direction: 'asc' | 'desc'
    }
}>()

// Sinkronkan filter/search/sort ke URL, otomatis kirim request ke server saat berubah.
// Nilai awal diambil dari filters yang dikirim controller (biar tetap konsisten setelah reload/back).
const { query, sortBy } = useTableQuery(rolesRoute.index, {
    search: props.filters.search ?? '',
    permission: props.filters.role ?? '',
    sort: props.filters.sort ?? 'created_at',
    direction: props.filters.direction ?? 'desc',
})

// Untuk cek hak akses, dipakai buat tampilkan/sembunyikan tombol (mis. Tambah User).
const { can } = usePermission();

function confirmDelete(): boolean {
    return window.confirm('Yakin hapus role ini?')
}

const sortIcon = computed(() =>
    query.direction === 'asc'
        ? ArrowDownAZ
        : ArrowDownZA
);
</script>

<template>

    <Head title="Roles & Permission" />

    <div class="p-6 space-y-6">
        <!-- Header halaman -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-foreground">Manajemen Role</h1>
                <p class="text-sm text-muted-foreground">Kelola role dan permission untuk aplikasi.</p>
            </div>
        </div>

        <!-- Toolbar: search, filter role, dan tombol aksi -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center justify-between">
            <div class="gap-2 flex">
                <!-- v-model langsung ke query.search -> otomatis trigger request (debounced) via useTableQuery -->
                <input v-model="query.search" type="text" placeholder="Cari role..."
                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring sm:w-64" />
            </div>
            <div class="gap-2 flex items-center">
                <!-- Tombol hanya muncul kalau user punya permission terkait -->
                <Button v-if="can('roles.create')" as-child size="sm" class="bg-emerald-700 text-slate-50">
                    <Link :href="rolesRoute.create()" class="flex items-center gap-2 cursor-pointer">
                        <ShieldPlus />
                        Add New Role
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
                                Role
                                <span v-if="query.sort === 'name'" class="text-xs">
                                    <component :is="sortIcon" class="size-4" />
                                </span>
                            </span>
                        </th>
                        <th @click="sortBy('permission')"
                            class="cursor-pointer select-none px-4 py-3 font-medium hover:text-foreground">
                            <span class="inline-flex items-center gap-1">
                                Permissions
                                <span v-if="query.sort === 'permission'" class="text-xs">
                                    <component :is="sortIcon" class="size-4" />
                                </span>
                            </span>
                        </th>
                        <th class="px-4 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <!-- State kosong: tampil kalau hasil filter/search tidak ada datanya -->
                    <tr v-if="roles.data.length === 0">
                        <td colspan="4" class="px-4 py-10 text-center text-muted-foreground">
                            Tidak ada role ditemukan.
                        </td>
                    </tr>
                    <!-- Loop tiap baris role -->
                    <tr v-for="role in roles.data" :key="role.id" class="transition-colors hover:bg-muted/40">
                        <td class="px-4 py-3 font-medium text-foreground">{{ role.name }}</td>
                        <td class="px-4 py-3">
                            <!-- Badge untuk tiap role yang dimiliki user (bisa lebih dari satu) -->
                            <span v-for="permission in role.permissions" :key="permission"
                                class="mr-1 mb-1 inline-flex items-center rounded-full bg-secondary px-2 py-0.5 text-xs font-medium text-secondary-foreground">
                                {{ permission }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">

                                <!-- Ke halaman edit role -->
                                <Button as-child variant="outline" size="sm">
                                    <Link :href="edit(role.id)"
                                        class="text-sm font-medium text-primary hover:underline">
                                        <PencilIcon class="w-4" />
                                    </Link>
                                </Button>
                                <!-- Hapus role (soft delete) via method DELETE Inertia; konfirmasi dulu sebelum request dikirim -->
                                <Button as-child variant="outline" size="sm">
                                    <Link :href="destroy(role.id)" method="delete" as="button" @before="confirmDelete"
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
