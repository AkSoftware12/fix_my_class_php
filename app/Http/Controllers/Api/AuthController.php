<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_id' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        $user->forceFill(['last_login_at' => now()])->save();

        return response()->json([
            'token' => $user->createToken($request->input('device_id', 'app'))->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = [
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'mobile'      => $user->mobile,
            'avatar'      => $user->avatar_url,
            'roles'       => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ];

        if ($user->hasRole('Student') && $student = $user->studentProfile) {
            $student->load(['schoolClass', 'batch', 'branch.coaching']);
            $data['student'] = [
                'id'               => $student->id,
                'admission_number' => $student->admission_number,
                'class'            => $student->schoolClass?->name,
                'batch'            => $student->batch?->name,
                'branch'           => $student->branch?->name,
                'coaching'         => $student->branch?->coaching?->name,
                'guardian_name'    => $student->guardian_name,
                'date_of_birth'    => $student->date_of_birth?->format('d M Y'),
                'photo_url'        => url($student->photo_url),
            ];
        }

        return response()->json(['user' => $data]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out.']);
    }
}
