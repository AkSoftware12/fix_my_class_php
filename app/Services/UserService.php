<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserService
{
    public function __construct(
        protected UserRepository $users,
        protected FileUploadService $files,
    ) {
    }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            if (! empty($data['avatar'])) {
                $data['avatar_path'] = $this->files->store($data['avatar'], 'avatars');
            }

            /** @var User $user */
            $user = $this->users->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'] ?? null,
                'password' => $data['password'],
                'city_id' => $data['city_id'] ?? null,
                'coaching_id' => $data['coaching_id'] ?? null,
                'branch_id' => $data['branch_id'] ?? null,
                'avatar_path' => $data['avatar_path'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (! empty($data['role'])) {
                $user->syncRoles([$data['role']]);
            }

            return $user;
        });
    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            if (! empty($data['avatar'])) {
                $data['avatar_path'] = $this->files->replace($data['avatar'], 'avatars', $user->avatar_path);
            }

            $payload = collect($data)->only([
                'name', 'email', 'mobile', 'city_id', 'coaching_id',
                'branch_id', 'avatar_path', 'is_active',
            ])->filter(fn ($v, $k) => $v !== null || in_array($k, ['city_id', 'coaching_id', 'branch_id']))->all();

            if (! empty($data['password'])) {
                $payload['password'] = $data['password'];
            }

            $this->users->update($user, $payload);

            if (! empty($data['role'])) {
                $user->syncRoles([$data['role']]);
            }

            return $user->refresh();
        });
    }

    public function toggleStatus(User $user): User
    {
        $user->is_active = ! $user->is_active;
        $user->save();

        return $user;
    }

    public function resetPassword(User $user, ?string $password = null): string
    {
        $password = $password ?: Str::password(12, symbols: false);

        $user->forceFill(['password' => $password])->save();

        return $password;
    }

    /**
     * @param  array<int, string>  $permissions
     */
    public function syncDirectPermissions(User $user, array $permissions): User
    {
        $user->syncPermissions($permissions);

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
