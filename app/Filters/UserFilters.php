<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class UserFilters
{
    protected array $sortable = ['name', 'email', 'created_at', 'deleted_at'];

    public function __construct(
        protected Builder $query,
        protected array $filters
    ) {}

    public function apply(): Builder
    {
        return $this->query
            ->when($this->filters['search'] ?? null, function ($q, $term) {
                $q->where(fn ($q) => $q->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%"));
            })
            ->when($this->filters['role'] ?? null, function ($q, $role) {
                $q->whereHas('roles', fn ($q) => $q->where('name', $role));
            })
            ->when(true, fn ($q) => $this->sort($q));
    }

    protected function sort(Builder $query): Builder
    {
        $sortBy = in_array($this->filters['sort'] ?? '', $this->sortable)
            ? $this->filters['sort']
            : 'created_at';

        $direction = ($this->filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sortBy, $direction);
    }
}
