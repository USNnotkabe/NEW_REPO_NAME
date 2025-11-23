<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdoptionRequest;
use App\Models\Pet;

class AdoptionRequestController extends Controller
{
    // User views their adoption requests
    public function myRequests()
    {
        $requests = AdoptionRequest::with('pet')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('adoption.my-requests', compact('requests'));
    }

    // Show adoption request form
    public function create($petId)
    {
        $pet = Pet::findOrFail($petId);

        // Check if pet is available
        if ($pet->status !== 'available') {
            return redirect()->back()->with('error', 'This pet is no longer available for adoption');
        }

        // Check if user already has a pending request for this pet
        $existingRequest = AdoptionRequest::where('user_id', auth()->id())
            ->where('pet_id', $petId)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'You already have a pending request for this pet');
        }

        return view('adoption.create', compact('pet'));
    }

    // Submit adoption request
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'message' => 'required|string|min:20',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        AdoptionRequest::create($validated);

        return redirect()->route('adoption.my-requests')
            ->with('success', 'Adoption request submitted! Please wait for admin approval.');
    }

    // Cancel adoption request (user)
    public function cancel($id)
    {
        $request = AdoptionRequest::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        $request->delete();

        return redirect()->back()->with('success', 'Adoption request cancelled');
    }
}
