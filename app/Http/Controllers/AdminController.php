<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pet;
use App\Models\AdoptionRequest;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // Admin Dashboard (WEB - returns view)
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_pets' => Pet::count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }

    // View all users (WEB - returns view)
    public function users()
    {
        $users = User::where('role', 'user')
            ->withCount('pets')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.users', compact('users'));
    }

    // View all pets (WEB - returns view)
    public function pets()
    {
        $pets = Pet::with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.pets', compact('pets'));
    }

    // Delete user (WEB)
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Cannot delete admin users');
        }
        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully');
    }

    // ========== ADD THESE NEW API METHODS BELOW ==========

    // API: Get admin stats (returns JSON)
    public function apiGetStats()
    {
        return response()->json([
            'total_users' => User::where('role', 'user')->count(),
            'total_pets' => Pet::count(),
            'total_requests' => AdoptionRequest::count(),
        ]);
    }

    // API: Get all users (returns JSON)
   // API: Get all users (returns JSON)
public function apiGetUsers()
{
    // REMOVE where('role', 'user') to show ALL users including admins
    $users = User::withCount(['pets', 'adoptionRequests'])
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json(['data' => $users]);
}

    // API: Get all pets (returns JSON)
    public function apiGetPets()
    {
        $pets = Pet::with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($pet) {
                return [
                    'id' => $pet->id,
                    'pet_name' => $pet->pet_name,
                    'category' => $pet->category,
                    'breed' => $pet->breed,
                    'age' => $pet->age,
                    'gender' => $pet->gender,
                    'listing_type' => $pet->listing_type,
                    'price' => $pet->price,
                    'status' => $pet->status,
                    'image_url' => $pet->image ? asset('storage/' . $pet->image) : null,
                    'created_at' => $pet->created_at,
                    'user' => [
                        'id' => $pet->user->id,
                        'name' => $pet->user->name,
                        'email' => $pet->user->email,
                    ],
                ];
            });

        return response()->json(['data' => $pets]);
    }

    // API: Delete user (returns JSON)
    public function apiDeleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot delete your own account'], 403);
        }

        if ($user->role === 'admin') {
            return response()->json(['message' => 'Cannot delete admin users'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

}
