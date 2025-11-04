<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Get current authenticated user profile
     */
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id_user, 'id_user')],
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->only(['name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * Get user statistics
     */
    public function statistics(Request $request)
    {
        $user = $request->user();

        $stats = [
            'total_peminjaman' => 0,
            'pending' => 0,
            'approved' => 0,
            'selesai' => 0,
            'rejected' => 0,
            'cancelled' => 0,
        ];

        if ($user->role === 'peminjam') {
            $peminjaman = $user->peminjaman;
            $stats['total_peminjaman'] = $peminjaman->count();
            $stats['pending'] = $peminjaman->where('status', 'pending')->count();
            $stats['approved'] = $peminjaman->where('status', 'approved')->count();
            $stats['selesai'] = $peminjaman->where('status', 'selesai')->count();
            $stats['rejected'] = $peminjaman->where('status', 'rejected')->count();
            $stats['cancelled'] = $peminjaman->where('status', 'cancelled')->count();
        } elseif (in_array($user->role, ['petugas', 'admin'])) {
            // Get all peminjaman statistics for petugas and admin
            $stats['total_peminjaman'] = \App\Models\Peminjaman::count();
            $stats['pending'] = \App\Models\Peminjaman::where('status', 'pending')->count();
            $stats['approved'] = \App\Models\Peminjaman::where('status', 'approved')->count();
            $stats['selesai'] = \App\Models\Peminjaman::where('status', 'selesai')->count();
            $stats['rejected'] = \App\Models\Peminjaman::where('status', 'rejected')->count();
            $stats['cancelled'] = \App\Models\Peminjaman::where('status', 'cancelled')->count();
        }

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
