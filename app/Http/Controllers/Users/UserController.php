<?php

namespace App\Http\Controllers\Users;

use App\Actions\Users\BulkTrashAction;
use App\Actions\Users\CreateUser;
use App\Actions\Users\DeleteUser;
use App\Actions\Users\ForceDeleteUser;
use App\Actions\Users\RestoreUser;
use App\Actions\Users\UpdateUser;
use App\Enums\Role;
use App\Filters\UserFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\BulkTrashActionRequest;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\Users\UserDetailResource;
use App\Http\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $filters = [
            'search' => $request->string('search')->toString() ?: '',
            'role' => $request->string('role')->toString() ?: '',
            'sort' => $request->string('sort')->toString() ?: 'created_at',
            'direction' => $request->string('direction')->toString() ?: 'desc',
        ];

        $users = (new UserFilters(
            User::query()->with('roles'),
            $filters
        ))->apply()->paginate(10)->withQueryString();

        return Inertia::render('users/Index', [
            'users' => UserResource::collection($users),
            'filters' => $filters,
            'roles' => collect(Role::cases())->pluck('value'),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('users/Create', [
            'roles' => collect(Role::cases())->pluck('value'),
        ]);
    }

    public function store(StoreUserRequest $request, CreateUser $action): RedirectResponse
    {
        $action->handle($request->validated());

        return to_route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user): Response
    {
        $this->authorize('update', $user);

        return Inertia::render('users/Edit', [
            'user' => new UserDetailResource($user),
            'roles' => collect(Role::cases())->pluck('value'),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUser $action): RedirectResponse
    {
        $action->handle($request->user(), $user, $request->validated());

        return to_route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user, DeleteUser $action): RedirectResponse
    {
        $this->authorize('delete', $user);

        $action->handle(request()->user(), $user);

        return to_route('users.index')->with('success', 'User berhasil dihapus.');
    }

    public function trashed(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $filters = [
            'search' => $request->string('search')->toString() ?: '',
            'role' => $request->string('role')->toString() ?: '',
            'sort' => $request->string('sort')->toString() ?: 'deleted_at',
            'direction' => $request->string('direction')->toString() ?: 'desc',
        ];

        $users = (new UserFilters(
            User::onlyTrashed()->with('roles'),
            $filters
        ))->apply()->paginate(10)->withQueryString();

        return Inertia::render('users/Trashed', [
            'users' => UserResource::collection($users),
            'filters' => $filters,
        ]);
    }

    public function restore(int|string $id, RestoreUser $action): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $user);

        $action->handle($user);

        return back()->with('success', 'User berhasil dipulihkan.');
    }

    public function forceDelete(int|string $id, ForceDeleteUser $action): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $user);

        $action->handle(request()->user(), $user);

        return to_route('users.trashed')->with('success', 'User berhasil dihapus permanen.');
    }

    public function bulkTrashAction(BulkTrashActionRequest $request, BulkTrashAction $action): RedirectResponse
    {
        $affected = $action->handle(
            $request->user(),
            $request->validated('ids'),
            $request->validated('action')
        );

        $message = $request->validated('action') === 'restore'
            ? "{$affected} user berhasil dipulihkan."
            : "{$affected} user berhasil dihapus permanen.";

        return back()->with('success', $message);
    }

    // public function bulkTrashAction(BulkTrashActionRequest $request, BulkTrashAction $action): RedirectResponse
    // {
    //     BulkTrashAction::dispatch(
    //         $request->user(),
    //         $request->validated('ids'),
    //         $request->validated('action')
    //     );

    //     return back()->with('success', 'Permintaan sedang diproses, akan selesai dalam beberapa saat.');
    // }
}
