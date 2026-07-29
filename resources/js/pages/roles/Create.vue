<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Undo2 } from '@lucide/vue'

import { Button } from '@/components/ui/button'
import { index, store } from '@/routes/roles'

import RoleForm from './RoleForm.vue'

interface PermissionOption {
    label: string
    value: string
}

interface PermissionGroup {
    group: string
    permissions: PermissionOption[]
}

defineProps<{
    permissions: PermissionGroup[]
}>()

const form = useForm({
    name: '',
    guard: 'web',
    permissions: [] as string[],
})

function submit() {
    form.post(store().url)
}
</script>

<template>

    <Head title="Create New Role" />

    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-tight">
                Tambah Role
            </h1>

            <p class="text-sm text-muted-foreground">
                Buat role baru beserta permission-nya.
            </p>
        </div>

        <form class="max-w-3xl space-y-6 rounded-lg border p-6" @submit.prevent="submit">
            <RoleForm v-model:form="form" :permissions="permissions" />

            <div class="flex justify-end gap-3 border-t pt-4">
                <Button variant="outline" as-child>
                    <Link :href="index()">
                        <Undo2 class="mr-2 size-4" />
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
