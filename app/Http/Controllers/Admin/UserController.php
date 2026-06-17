<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Branch;
use App\Models\City;
use App\Models\Coaching;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\ExportService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected UserRepository $users,
        protected UserService $service,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', User::class);

        if ($request->ajax()) {
            $query = $this->users->filtered($request->user(), $request->only([
                'search', 'role', 'coaching_id', 'branch_id', 'status',
            ]));

            return $this->dataTable($request, $query, [
                'id' => fn ($u) => $u->id,
                'user' => fn ($u) => view('admin.users.partials.identity', ['user' => $u])->render(),
                'role' => fn ($u) => e($u->roles->pluck('name')->join(', ') ?: '—'),
                'coaching' => fn ($u) => e($u->coaching?->name ?? '—'),
                'branch' => fn ($u) => e($u->branch?->name ?? '—'),
                'last_login' => fn ($u) => $u->last_login_at?->diffForHumans() ?? 'Never',
                'status' => fn ($u) => status_badge($u->is_active),
                'actions' => fn ($u) => view('admin.users.partials.actions', ['user' => $u])->render(),
            ], ['id', 'name', null, null, null, 'last_login_at', 'is_active']);
        }

        return view('admin.users.index', $this->formOptions($request->user()));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', User::class);

        return view('admin.users.create', $this->formOptions($request->user()));
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validated();
        $data['avatar'] = $request->file('avatar');

        $user = $this->service->create($data);

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" created.");
    }

    public function edit(Request $request, User $user): View
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', ['user' => $user] + $this->formOptions($request->user()));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validated();
        $data['avatar'] = $request->file('avatar');

        $this->service->update($user, $data);

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" updated.");
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }

        $this->service->delete($user);

        return response()->json(['message' => "User \"{$user->name}\" deleted."]);
    }

    public function toggleStatus(User $user): JsonResponse
    {
        $this->authorize('update', $user);

        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot deactivate your own account.'], 422);
        }

        $user = $this->service->toggleStatus($user);

        return response()->json([
            'message' => $user->is_active
                ? "User \"{$user->name}\" activated."
                : "User \"{$user->name}\" deactivated.",
            'is_active' => $user->is_active,
        ]);
    }

    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $request->validate([
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $password = $this->service->resetPassword($user, $request->input('password'));

        return response()->json([
            'message' => "Password reset for \"{$user->name}\".",
            'password' => $request->filled('password') ? null : $password,
        ]);
    }

    public function permissions(User $user): View
    {
        $this->authorize('assign', $user);

        return view('admin.users.permissions', [
            'user' => $user,
            'permissions' => Permission::orderBy('module')->orderBy('name')->get()->groupBy('module'),
            'direct' => $user->getDirectPermissions()->pluck('name')->all(),
            'viaRoles' => $user->getPermissionsViaRoles()->pluck('name')->all(),
        ]);
    }

    public function syncPermissions(Request $request, User $user): RedirectResponse
    {
        $this->authorize('assign', $user);

        $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $this->service->syncDirectPermissions($user, $request->input('permissions', []));

        return redirect()->route('admin.users.index')
            ->with('success', "Permissions updated for \"{$user->name}\".");
    }

    public function export(Request $request, ExportService $export)
    {
        $this->authorize('export', User::class);

        $rows = $this->users->filtered($request->user(), $request->only(['search', 'role', 'coaching_id', 'branch_id', 'status']))
            ->latest()
            ->lazy()
            ->map(fn ($u) => [
                $u->id, $u->name, $u->email, $u->mobile,
                $u->roles->pluck('name')->join(', '),
                $u->coaching?->name, $u->branch?->name,
                $u->is_active ? 'Active' : 'Inactive',
                $u->last_login_at?->format('d M Y H:i') ?? 'Never',
            ]);

        return $export->download(
            $request->input('format', 'csv'),
            'users-'.now()->format('Ymd-His'),
            'Users',
            ['ID', 'Name', 'Email', 'Mobile', 'Role', 'Coaching', 'Branch', 'Status', 'Last Login'],
            $rows,
        );
    }

    protected function formOptions(User $viewer): array
    {
        if ($viewer->hasRole('Super Admin')) {
            $roles  = ['Super Admin', 'City Admin', 'Branch Admin', 'Teacher'];
            $cities = City::active()->orderBy('name')->get();
        } elseif ($viewer->hasRole('City Admin')) {
            $roles  = ['Branch Admin', 'Teacher'];
            $cities = City::active()->where('id', $viewer->city_id)->get();
        } elseif ($viewer->hasRole('Branch Admin')) {
            $roles  = ['Teacher'];
            $cities = collect();
        } else {
            $roles  = ['Teacher'];
            $cities = collect();
        }

        return [
            'roles'     => collect($roles)->values(),
            'cities'    => $cities,
            'coachings' => Coaching::visibleTo($viewer)->active()->orderBy('name')->get(),
            'branches'  => Branch::visibleTo($viewer)->active()->orderBy('name')->get(),
        ];
    }
}
