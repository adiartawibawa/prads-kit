<?php

namespace App\Http\Controllers\Roles;

use App\Actions\Roles\CreateRole;
use App\Actions\Roles\DeleteRole;
use App\Actions\Roles\GetRolePermissions;
use App\Actions\Roles\UpdateRole;
use App\Filters\RoleFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Roles\StoreRoleRequest;
use App\Http\Requests\Roles\UpdateRoleRequest;
use App\Http\Resources\Roles\RoleDetailResource;
use App\Http\Resources\Roles\RoleResource;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Role::class);

        $filters = [
            'search' => $request->string('search')->toString() ?: '',
            'permission' => $request->string('permission')->toString() ?: '',
            'sort' => $request->string('sort')->toString() ?: 'created_at',
            'direction' => $request->string('direction')->toString() ?: 'desc',
        ];

        $roles = (new RoleFilters(
            Role::query()->with('permissions'),
            $filters
        ))->apply()->paginate(10)->withQueryString();

        return Inertia::render('roles/Index', [
            'roles' => RoleResource::collection($roles),
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(GetRolePermissions $permissions)
    {
        $this->authorize('create', Role::class);

        return Inertia::render('roles/Create', [
            'permissions' => $permissions->handle(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request, CreateRole $action): RedirectResponse
    {
        $action->handle($request->validated());

        return to_route('roles.index')->with('success', 'User berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role, GetRolePermissions $permissions): Response
    {
        $this->authorize('update', $role);

        return Inertia::render('roles/Edit', [
            'role' => new RoleDetailResource($role),

            'permissions' => $permissions->handle(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role, UpdateRole $action): RedirectResponse
    {
        $action->handle($request->user(), $role, $request->validated());

        return to_route('roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role, DeleteRole $action): RedirectResponse
    {
        $this->authorize('delete', $role);

        $action->handle($role);

        return to_route('roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
