<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;

// class BulkTrashAction implements ShouldQueue
// {
class BulkTrashAction
{
    // use Dispatchable, InteractsWithQueue, Queueable;

    // public function __construct(
    //     public User $actor,
    //     public array $ids,
    //     public string $action
    // ) {}

    public function handle(User $actor, array $ids, string $action): int
    {
        $users = User::onlyTrashed()
            ->whereIn('id', $ids)
            ->get()
            ->filter(fn (User $user) => $actor->can($this->ability($action), $user));

        return match ($action) {
            'restore' => $this->restore($users),
            'force-delete' => $this->forceDelete($actor, $users),
        };
    }
    // public function handle(): void
    // {
    //     User::onlyTrashed()->whereIn('id', $this->ids)->chunkById(100, function ($users) {
    //         foreach ($users as $user) {
    //             if (! $this->actor->can($this->action === 'restore' ? 'restore' : 'forceDelete', $user)) {
    //                 continue;
    //             }

    //             $this->action === 'restore' ? $user->restore() : $user->forceDelete();
    //         }
    //     });
    // }

    protected function ability(string $action): string
    {
        return match ($action) {
            'restore' => 'restore',
            'force-delete' => 'forceDelete',
        };
    }

    protected function restore(Collection $users): int
    {
        $ids = $users->pluck('id');

        return User::onlyTrashed()->whereIn('id', $ids)->restore();
    }

    /**
     * Kenapa force-delete di-loop, bukan whereIn(...)->delete(): karena mass delete lewat query builder tidak memicu model event (deleting, deleted).
     * Kalau nanti kamu punya listener yang bergantung pada event tersebut (misal hapus file avatar terkait, catat activity log),
     * query builder langsung akan melewatinya diam-diam. Loop per-model memastikan semua event/observer tetap jalan
     * — trade-off-nya performa lebih lambat untuk dataset besar
     */
    protected function forceDelete(User $actor, Collection $users): int
    {
        // cegah actor menghapus permanen akun sendiri lewat bulk action
        $users = $users->reject(fn (User $user) => $actor->is($user));

        $count = 0;
        foreach ($users as $user) {
            $user->forceDelete();
            $count++;
        }

        return $count;
    }
}
