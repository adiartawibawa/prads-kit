import { usePage } from '@inertiajs/vue3';
import type { SharedData } from '@/types';

/**
 * Composable untuk cek permission user di sisi frontend (Vue),
 * berdasarkan data auth yang dikirim dari backend lewat Inertia shared props.
 */
export function usePermission() {
    // Ambil shared props Inertia (data yang selalu dikirim di setiap halaman, mis. auth.user).
    const page = usePage<SharedData>();

    /**
     * Cek apakah user yang login punya permission tertentu.
     * - Super admin selalu return true (bypass, tidak perlu cek daftar permission).
     * - User biasa: dicek apakah nama permission ada di daftar page.props.auth.permissions
     *   (daftar ini biasanya sudah disiapkan/di-resolve dari backend, mis. dari role/permission Spatie).
     */
    function can(permission: string): boolean {
        if (page.props.auth.isSuperAdmin) {
            return true;
        }

        return page.props.auth.permissions.includes(permission);
    }

    return { can };
}
