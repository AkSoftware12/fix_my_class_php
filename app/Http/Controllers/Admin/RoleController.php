<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use RespondsWithDataTable;

    protected const PROTECTED_ROLES = [
        'Super Admin', 'City Admin', 'Coaching Admin', 'Branch Admin', 'Teacher', 'Student',
    ];

    protected const HIDDEN_ROLES = ['Coaching Admin', 'Student'];

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Role::class);

        if ($request->ajax()) {
            $query = Role::query()->withCount(['users', 'permissions'])
                ->whereNotIn('name', self::HIDDEN_ROLES)
                ->when($request->input('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%"));

            return $this->dataTable($request, $query, [
                'id' => fn ($r) => $r->id,
                'name' => fn ($r) => e($r->name),
                'users_count' => fn ($r) => $r->users_count,
                'permissions_count' => fn ($r) => $r->permissions_count,
                'protected' => fn ($r) => in_array($r->name, self::PROTECTED_ROLES)
                    ? '<span class="badge text-bg-info-subtle">System</span>'
                    : '<span class="badge text-bg-secondary-subtle">Custom</span>',
                'actions' => fn ($r) => view('admin.roles.partials.actions', [
                    'role' => $r,
                    'isProtected' => in_array($r->name, self::PROTECTED_ROLES),
                ])->render(),
            ], ['id', 'name', 'users_count', 'permissions_count']);
        }

        return view('admin.roles.index');
    }

    public function create(): View
    {
        $this->authorize('create', Role::class);

        return view('admin.roles.create', [
            'permissions' => Permission::orderBy('module')->orderBy('name')->get()->groupBy('module'),
            'assigned' => [],
        ]);
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $this->authorize('create', Role::class);

        $role = Role::create(['name' => $request->validated('name'), 'guard_name' => 'web']);
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->name}\" created.");
    }

    public function edit(Role $role): View
    {
        $this->authorize('update', $role);

        abort_if($role->name === 'Super Admin', 403, 'The Super Admin role cannot be edited.');

        return view('admin.roles.edit', [
            'role' => $role,
            'permissions' => Permission::orderBy('module')->orderBy('name')->get()->groupBy('module'),
            'assigned' => $role->permissions->pluck('name')->all(),
        ]);
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $this->authorize('update', $role);

        abort_if($role->name === 'Super Admin', 403, 'The Super Admin role cannot be edited.');

        // System role names are fixed; only their permissions may change.
        if (! in_array($role->name, self::PROTECTED_ROLES)) {
            $role->update(['name' => $request->validated('name')]);
        }

        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->name}\" updated.");
    }

    public function destroy(Role $role): JsonResponse
    {
        $this->authorize('delete', $role);

        abort_if(in_array($role->name, self::PROTECTED_ROLES), 403, 'System roles cannot be deleted.');

        if ($role->users()->exists()) {
            return response()->json(['message' => 'Cannot delete a role that is assigned to users.'], 422);
        }

        $role->delete();

        return response()->json(['message' => "Role \"{$role->name}\" deleted."]);
    }
}
