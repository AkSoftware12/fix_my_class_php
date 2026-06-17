<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SetupController extends Controller
{
    public function createSuperAdmin(Request $request): JsonResponse
    {
        if (User::role('Super Admin')->exists()) {
            return response()->json(['message' => 'Super Admin already exists.'], 403);
        }

        $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name'               => $request->input('name'),
            'email'              => $request->input('email'),
            'password'           => $request->input('password'),
            'is_active'          => true,
            'email_verified_at'  => now(),
        ]);

        $user->assignRole('Super Admin');

        return response()->json(['message' => 'Super Admin created successfully. You can now login.']);
    }
}
