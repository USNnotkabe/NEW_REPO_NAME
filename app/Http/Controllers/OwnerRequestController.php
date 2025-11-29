<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdoptionRequest;
use App\Models\Pet;

class OwnerRequestController extends Controller
{
    public function index()
    {
        // Get all pets owned by the logged-in user
        $petIds = Pet::where('user_id', auth()->id())->pluck('id');

        // Get all requests for those pets
        $requests = AdoptionRequest::with(['user', 'pet'])
            ->whereIn('pet_id', $petIds)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('owner.requests', compact('requests'));
    }

    public function approve($id)
    {
        $request = AdoptionRequest::findOrFail($id);

        // Check if the logged-in user owns this pet
        if ($request->pet->user_id !== auth()->id()) {
            abort(403, 'Unauthorized - You can only manage requests for your own pets');
        }

        $request->update([
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);

        // Update pet status
        $request->pet->update(['status' => 'adopted']);

        // Create adoption history
        \App\Models\AdoptionHistory::create([
            'user_id' => $request->user_id,
            'pet_id' => $request->pet_id,
            'adoption_request_id' => $request->id,
            'adoption_date' => now(),
            'notes' => 'Approved by pet owner: ' . auth()->user()->name,
        ]);

        return redirect()->back()->with('success', 'Request approved! The adopter can now contact you.');
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'owner_notes' => 'required|string|min:10',
        ]);

        $adoptionRequest = AdoptionRequest::findOrFail($id);

        // Check if the logged-in user owns this pet
        if ($adoptionRequest->pet->user_id !== auth()->id()) {
            abort(403, 'Unauthorized - You can only manage requests for your own pets');
        }

        $adoptionRequest->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'owner_notes' => $validated['owner_notes'],
        ]);

        return redirect()->back()->with('success', 'Request rejected.');
    }
}
