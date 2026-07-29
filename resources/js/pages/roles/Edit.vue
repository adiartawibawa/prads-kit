<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Undo2 } from '@lucide/vue'

import { Button } from '@/components/ui/button'
import { index, update } from '@/routes/roles'

import RoleForm from './RoleForm.vue'

interface RoleDetail {
    id: string
    name: string
    guard: string
    permissions: string[]
}

interface PermissionOption {
    label: string
    value: string
}

interface PermissionGroup {
    group: string
    permissions: PermissionOption[]
}

const props = defineProps<{
    role: {
        data: RoleDetail
    }
    permissions: PermissionGroup[]
}>()

const role = props.role.data

const form = useForm({
    name: role.name,
    guard: role.guard,
    permissions: [...role.permissions],
})

function submit() {
    form.put(update(role.id).url)
}
</script>

<template>

    <Head title="Edit Role" />

    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-tight">
                Edit Role
            </h1>

            <p class="text-sm text-muted-foreground">
                Perbarui role beserta permission yang dimilikinya.
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
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </Button>
            </div>
        </form>
    </div>
</template>
