<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue';
import { bulkTrashAction } from '@/actions/App/Http/Controllers/Users/UserController';
import { usePermission } from '@/composables/usePermission'
import { useTableQuery } from '@/composables/useTableQuery'
import { index, restore, forceDelete } from '@/routes/users'

const props = defineProps<{
    users: {
        data: any[];
        links: any[]
    }
    filters: {
        search: string;
        role: string;
        sort: string;
        direction: 'asc' | 'desc'
    }
}>()

const { can } = usePermission()

// Sinkron filter/sort ke URL, request-nya mengarah ke index() (route users.index)
// karena halaman trashed pakai controller method 'trashed' yang di-URL-kan lewat action index.
const { query, sortBy } = useTableQuery(index, {
    search: props.filters.search,
    role: props.filters.role,
    sort: props.filters.sort,
    direction: props.filters.direction,
})

// Daftar id user yang dicentang untuk aksi massal (bulk restore / force delete).
const selected = ref<string[]>([])

// Checkbox "select all" di header tabel:
// - get: true kalau jumlah selected = jumlah semua data (dan tidak kosong)
// - set: kalau dicentang -> isi selected dengan semua id, kalau di-uncheck -> kosongkan
const allSelected = computed({
    get: () => selected.value.length === props.users.data.length && props.users.data.length > 0,
    set: (value: boolean) => {
        selected.value = value ? props.users.data.map((u) => u.id) : []
    },
})

// Jalankan aksi massal (restore atau hapus permanen) untuk semua user yang dicentang.
function runBulkAction(action: 'restore' | 'force-delete') {
    if (selected.value.length === 0) {
        return;
    }

    // Pesan konfirmasi beda tergantung aksi, khususnya warning untuk force-delete (tidak bisa dibatalkan).
    const confirmMessage = action === 'restore'
        ? `Pulihkan ${selected.value.length} user?`
        : `Hapus permanen ${selected.value.length} user? Tindakan ini tidak bisa dibatalkan.`

    if (!window.confirm(confirmMessage)) {
        return
    }

    // Kirim id-id terpilih + jenis aksi ke UserController@bulkTrashAction.
    // Setelah sukses, kosongkan lagi seleksi checkbox.
    router.post(bulkTrashAction(), {
        ids: selected.value,
        action,
    }, {
        preserveScroll: true,
        onSuccess: () => (selected.value = []),
    })
}

// Catatan: dua fungsi konfirmasi di bawah ini tidak dipakai (nonaktif/di-comment),
// kemungkinan sisa dari versi sebelumnya sebelum aksi restore/hapus per-baris
// dipindah tanpa konfirmasi individual, atau memang belum sempat dihapus.
// function confirmRestore(): boolean {
//     return window.confirm('Pulihkan user ini?')
// }

// function confirmForceDelete(): boolean {
//     return window.confirm('Hapus permanen? Tindakan ini tidak bisa dibatalkan.')
// }
</script>

<template>

    <Head title="Sampah User" />

    <div class="p-6 space-y-6">
        <!-- Header + link kembali ke daftar user aktif -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-foreground">Sampah User</h1>
                <p class="text-sm text-muted-foreground">User yang telah dihapus, bisa dipulihkan atau dihapus permanen.
                </p>
            </div>

            <Link :href="index()" class="text-sm text-primary hover:underline">
                ← Kembali ke daftar user
            </Link>
        </div>

        <div class="flex items-center justify-between gap-3">
            <input v-model="query.search" placeholder="Cari nama/email..."
                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm shadow-sm sm:w-64" />

            <!-- Toolbar aksi massal: hanya muncul kalau ada minimal 1 user yang dicentang -->
            <div v-if="selected.length > 0"
                class="flex items-center gap-2 rounded-md border border-border bg-muted/50 px-3 py-1.5">
                <span class="text-sm text-muted-foreground">{{ selected.length }} dipilih</span>

                <Button v-if="can('users.restore')" size="sm" variant="secondary" @click="runBulkAction('restore')">
                    Pulihkan
                </Button>

                <Button v-if="can('users.force-delete')" size="sm" variant="destructive"
                    @click="runBulkAction('force-delete')">
                    Hapus Permanen
                </Button>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-border">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left text-muted-foreground">
                    <tr class="border-b border-border">
                        <!-- Checkbox select all -->
                        <th class="w-10 px-4 py-3">
                            <input type="checkbox" v-model="allSelected" class="rounded border-input" />
                        </th>
                        <th @click="sortBy('name')" class="cursor-pointer px-4 py-3 font-medium">Nama</th>
                        <th @click="sortBy('email')" class="cursor-pointer px-4 py-3 font-medium">Email</th>
                        <!-- Sort berdasarkan tanggal dihapus, khusus di halaman ini -->
                        <th @click="sortBy('deleted_at')" class="cursor-pointer px-4 py-3 font-medium">Dihapus Pada</th>
                        <th class="px-4 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr v-if="users.data.length === 0">
                        <td colspan="5" class="px-4 py-10 text-center text-muted-foreground">
                            Sampah kosong.
                        </td>
                    </tr>
                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-muted/40">
                        <!-- Checkbox per baris, terikat ke array `selected` via value = id user -->
                        <td class="px-4 py-3">
                            <input type="checkbox" :value="user.id" v-model="selected" class="rounded border-input" />
                        </td>
                        <td class="px-4 py-3 font-medium text-foreground">{{ user.name }}</td>
                        <td class="px-4 py-3 text-muted-foreground">{{ user.email }}</td>
                        <td class="px-4 py-3 text-muted-foreground">{{ user.deleted_at }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-3">
                                <!-- Aksi per baris: pulihkan satu user (method PATCH) -->
                                <!-- Catatan: tidak ada konfirmasi di sini (beda dgn versi bulk yg pakai window.confirm) -->
                                <Link v-if="can('users.restore')" :href="restore(user.id)" method="patch" as="button"
                                    class="text-sm font-medium text-primary hover:underline">
                                    Pulihkan
                                </Link>
                                <!-- Aksi per baris: hapus permanen satu user (method DELETE), juga tanpa konfirmasi -->
                                <Link v-if="can('users.force-delete')" :href="forceDelete(user.id)" method="delete"
                                    as="button" class="text-sm font-medium text-destructive hover:underline">
                                    Hapus Permanen
                                </Link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
