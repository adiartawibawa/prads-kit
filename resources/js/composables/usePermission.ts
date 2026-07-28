import { usePage } from '@inertiajs/vue3';
import type { SharedData } from '@/types';

export function usePermission() {
    const page = usePage<SharedData>();

    function can(permission: string): boolean {
        if (page.props.auth.isSuperAdmin) {
            return true;
        }

        return page.props.auth.permissions.includes(permission);
    }

    return { can };
}
