<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3'

import { Checkbox } from '@/components/ui/checkbox'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'

interface PermissionOption {
    label: string
    value: string
}

interface PermissionGroup {
    group: string
    permissions: PermissionOption[]
}

interface RoleFormData {
    name: string
    guard: string
    permissions: string[]
}

const props = defineProps<{
    permissions: PermissionGroup[]
}>()

const form = defineModel<InertiaForm<RoleFormData>>('form', {
    required: true,
})

function togglePermission(permission: string, checked: boolean) {
    if (checked) {
        if (!form.value.permissions.includes(permission)) {
            form.value.permissions.push(permission)
        }

        return
    }

    form.value.permissions = form.value.permissions.filter(
        value => value !== permission,
    )
}
</script>

<template>
    <div class="space-y-6">
        <!-- Name -->
        <div class="space-y-2">
            <Label for="name">
                Role
            </Label>

            <Input id="name" v-model="form.name" placeholder="Role name" />

            <p v-if="form.errors.name" class="text-sm text-destructive">
                {{ form.errors.name }}
            </p>
        </div>

        <!-- Guard -->
        <div class="space-y-2">
            <Label>
                Guard
            </Label>

            <Select v-model="form.guard">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select guard" />
                </SelectTrigger>

                <SelectContent>
                    <SelectItem value="web">
                        web
                    </SelectItem>

                    <SelectItem value="api">
                        api
                    </SelectItem>
                </SelectContent>
            </Select>

            <p v-if="form.errors.guard" class="text-sm text-destructive">
                {{ form.errors.guard }}
            </p>
        </div>

        <!-- Permissions -->
        <div class="space-y-3">
            <Label>
                Permissions
            </Label>

            <div class="space-y-6">
                <div v-for="group in props.permissions" :key="group.group" class="space-y-3">
                    <h3 class="text-sm font-semibold">
                        {{ group.group }}
                    </h3>

                    <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                        <div v-for="permission in group.permissions" :key="permission.value"
                            class="flex items-center space-x-3 rounded-md border p-3">
                            <Checkbox :model-value="form.permissions.includes(permission.value)" @update:model-value="
                                checked =>
                                    togglePermission(permission.value, Boolean(checked))
                            " />

                            <Label class="cursor-pointer font-normal">
                                {{ permission.label }}
                            </Label>
                        </div>
                    </div>
                </div>

                <p v-if="form.errors.permissions" class="text-sm text-destructive">
                    {{ form.errors.permissions }}
                </p>
            </div>
        </div>
    </div>
</template>
