<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pet;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // Admin Dashboard
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_pets' => Pet::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    // View all users
    public function users()
    {
        $users = User::where('role', 'user')
            ->withCount('pets')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users', compact('users'));
    }

    // View all pets (monitoring only)
    public function pets()
    {
        $pets = Pet::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pets', compact('pets'));
    }

    // Delete user (admin action)
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Cannot delete admin users');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully');
    }
}
