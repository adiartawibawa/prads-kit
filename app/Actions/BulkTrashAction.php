<?php

namespace App\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use InvalidArgumentException;

abstract class BulkTrashAction
{
    public function handle(
        Authenticatable $actor,
        array $ids,
        string $action
    ): int {
        $models = $this->query()
            ->whereIn('id', $ids)
            ->get()
            ->filter(fn ($model) => $actor->can(
                $this->ability($action),
                $model
            ));

        return match ($action) {
            'restore' => $this->restore($models),
            'force-delete' => $this->forceDelete($actor, $models),
            default => throw new InvalidArgumentException("Unknown action [$action]"),
        };
    }

    abstract protected function query(): Builder;

    protected function ability(string $action): string
    {
        return match ($action) {
            'restore' => 'restore',
            'force-delete' => 'forceDelete',
        };
    }

    protected function restore(Collection $models): int
    {
        return $this->query()
            ->whereKey($models->modelKeys())
            ->restore();
    }

    protected function forceDelete(
        Authenticatable $actor,
        Collection $models
    ): int {
        $count = 0;

        foreach ($models as $model) {
            $model->forceDelete();
            $count++;
        }

        return $count;
    }
}
