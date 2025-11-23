<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pet;
use App\Models\AdoptionRequest;

class AdminController extends Controller
{
    // Admin Dashboard
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_pets' => Pet::count(),
            'pending_requests' => AdoptionRequest::where('status', 'pending')->count(),
            'approved_requests' => AdoptionRequest::where('status', 'approved')->count(),
        ];

        $recentRequests = AdoptionRequest::with(['user', 'pet'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentRequests'));
    }

    // View all users
    public function users()
    {
        $users = User::where('role', 'user')
            ->withCount('pets', 'adoptionRequests')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users', compact('users'));
    }

    // View all pets
    public function pets()
    {
        $pets = Pet::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pets', compact('pets'));
    }

    // View adoption requests
    public function adoptionRequests()
    {
        $requests = AdoptionRequest::with(['user', 'pet', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.adoption-requests', compact('requests'));
    }

    // Approve adoption request
    public function approveRequest($id)
    {
        $request = AdoptionRequest::findOrFail($id);

        $request->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Update pet status to adopted
        $request->pet->update(['status' => 'adopted']);

        return redirect()->back()->with('success', 'Adoption request approved!');
    }

    // Reject adoption request
    public function rejectRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $adoptionRequest = AdoptionRequest::findOrFail($id);

        $adoptionRequest->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => $validated['admin_notes'],
        ]);

        return redirect()->back()->with('success', 'Adoption request rejected.');
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

    // Delete pet (admin action)
    public function deletePet($id)
    {
        $pet = Pet::findOrFail($id);

        // Delete image if exists
        if ($pet->image) {
            \Storage::disk('public')->delete($pet->image);
        }

        $pet->delete();

        return redirect()->back()->with('success', 'Pet deleted successfully');
    }
}
