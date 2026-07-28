import { router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { reactive, watch, toRefs } from 'vue';

type QueryParams = Record<string, string | number | boolean | undefined | null>;

// tipe minimal untuk action Wayfinder yang punya method .url()
interface WayfinderAction {
    url: (options?: { query?: Record<string, any> }) => string;
}

interface UseTableQueryOptions {
    debounceMs?: number;
    immediateFields?: string[];
    preserveScroll?: boolean;
    preserveState?: boolean;
}

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

    const query = reactive<QueryParams>({ ...initialFilters });

    function buildParams(): Record<string, any> {
        return Object.fromEntries(
            Object.entries(query).filter(
                ([, value]) =>
                    value !== '' && value !== null && value !== undefined,
            ),
        );
    }

    function visit() {
        router.get(
            action.url({ query: buildParams() }),
            {},
            {
                preserveState,
                preserveScroll,
                replace: true,
            },
        );
    }

    const debouncedVisit = useDebounceFn(visit, debounceMs);

    watch(
        query,
        (_, oldValue) => {
            const changedImmediateField = immediateFields.some(
                (field) => query[field] !== (oldValue as QueryParams)?.[field],
            );

            if (changedImmediateField) {
                visit();
            } else {
                debouncedVisit();
            }
        },
        { deep: true },
    );

    function sortBy(column: string) {
        if (query.sort === column) {
            query.direction = query.direction === 'asc' ? 'desc' : 'asc';
        } else {
            query.sort = column;
            query.direction = 'asc';
        }
    }

    function resetFilters() {
        Object.keys(query).forEach((key) => {
            query[key] = initialFilters[key] ?? '';
        });
        visit();
    }

    return { ...toRefs(query), query, sortBy, resetFilters, visit };
}
