import { router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { reactive, watch, toRefs } from 'vue';

// Tipe nilai filter/query yang diizinkan (string, number, boolean, atau kosong).
type QueryParams = Record<string, string | number | boolean | undefined | null>;

// Tipe minimal untuk action Wayfinder (route helper Laravel) yang punya method .url().
interface WayfinderAction {
    url: (options?: { query?: Record<string, any> }) => string;
}

interface UseTableQueryOptions {
    debounceMs?: number; // jeda (ms) sebelum request dikirim untuk field non-immediate, default 300ms
    immediateFields?: string[]; // field yang langsung trigger request tanpa nunggu debounce
    preserveScroll?: boolean; // pertahankan posisi scroll saat navigasi Inertia
    preserveState?: boolean; // pertahankan state komponen Vue saat navigasi Inertia
}

/**
 * Composable untuk mengelola query/filter tabel (search, sort, filter, pagination)
 * yang otomatis sinkron ke URL lewat Inertia router.get().
 *
 * - Field seperti search: pakai debounce (nunggu user berhenti mengetik).
 * - Field seperti status/role/sort: langsung trigger request begitu berubah.
 */
export function useTableQuery(
    action: WayfinderAction,
    initialFilters: QueryParams,
    options: UseTableQueryOptions = {},
) {
    const {
        debounceMs = 300,
        immediateFields = ['status', 'role', 'sort', 'direction', 'author'],
        preserveScroll = true,
        preserveState = true,
    } = options;

    // State reaktif untuk semua filter, diinisialisasi dari initialFilters.
    const query = reactive<QueryParams>({ ...initialFilters });

    // Buang field yang kosong/null/undefined agar tidak ikut jadi query string (?search=&role=...).
    function buildParams(): Record<string, any> {
        return Object.fromEntries(
            Object.entries(query).filter(
                ([, value]) =>
                    value !== '' && value !== null && value !== undefined,
            ),
        );
    }

    // Kirim request GET ke server via Inertia dengan query params terbaru, tanpa reload penuh.
    function visit() {
        router.get(
            action.url({ query: buildParams() }),
            {},
            {
                preserveState,
                preserveScroll,
                replace: true, // ganti history entry, bukan menambah entry baru (biar tombol back tidak "nyangkut" di tiap ketikan)
            },
        );
    }

    // Versi visit() yang di-debounce, dipakai untuk field seperti search.
    const debouncedVisit = useDebounceFn(visit, debounceMs);

    // Pantau semua perubahan pada query (deep watch).
    watch(
        query,
        (_, oldValue) => {
            // Cek apakah yang berubah termasuk field "immediate" (mis. dropdown role/status).
            const changedImmediateField = immediateFields.some(
                (field) => query[field] !== (oldValue as QueryParams)?.[field],
            );

            // Field immediate -> langsung request. Field lain (mis. search) -> pakai debounce.
            if (changedImmediateField) {
                visit();
            } else {
                debouncedVisit();
            }
        },
        { deep: true },
    );

    // Klik header kolom untuk sorting: klik kolom yang sama -> toggle asc/desc,
    // klik kolom baru -> mulai dari asc.
    function sortBy(column: string) {
        if (query.sort === column) {
            query.direction = query.direction === 'asc' ? 'desc' : 'asc';
        } else {
            query.sort = column;
            query.direction = 'asc';
        }
    }

    // Kembalikan semua filter ke nilai awal, lalu langsung request ulang data.
    function resetFilters() {
        Object.keys(query).forEach((key) => {
            query[key] = initialFilters[key] ?? '';
        });
        visit();
    }

    // toRefs(query) agar tiap field bisa dipakai langsung sebagai v-model di komponen (mis. search, sort).
    return { ...toRefs(query), query, sortBy, resetFilters, visit };
}
