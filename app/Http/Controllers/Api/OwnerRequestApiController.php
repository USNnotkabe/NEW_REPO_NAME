<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdoptionRequest;
use App\Models\Pet;
use App\Models\AdoptionHistory;

class OwnerRequestApiController extends Controller
{
    // Get requests for owner's pets
    public function index(Request $request)
    {
        // Get all pets owned by the logged-in user
        $petIds = Pet::where('user_id', $request->user()->id)->pluck('id');

        // Get all requests for those pets
        $requests = AdoptionRequest::with(['user', 'pet'])
            ->whereIn('pet_id', $petIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($req) {
                if ($req->pet && $req->pet->image) {
                    $req->pet->image_url = asset('storage/' . $req->pet->image);
                }
                return $req;
            });

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    // Approve a request
    public function approve(Request $request, $id)
    {
        $adoptionRequest = AdoptionRequest::findOrFail($id);

        // Check if the logged-in user owns this pet
        if ($adoptionRequest->pet->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - You can only manage requests for your own pets'
            ], 403);
        }

        $adoptionRequest->update([
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);

        // Update pet status
        $adoptionRequest->pet->update(['status' => 'adopted']);

        // Create adoption history
        AdoptionHistory::create([
            'user_id' => $adoptionRequest->user_id,
            'pet_id' => $adoptionRequest->pet_id,
            'adoption_request_id' => $adoptionRequest->id,
            'adoption_date' => now(),
            'notes' => 'Approved by pet owner: ' . $request->user()->name,
        ]);

        $adoptionRequest->load(['user', 'pet']);

        if ($adoptionRequest->pet && $adoptionRequest->pet->image) {
            $adoptionRequest->pet->image_url = asset('storage/' . $adoptionRequest->pet->image);
        }

        return response()->json([
            'success' => true,
            'message' => 'Request approved successfully',
            'data' => $adoptionRequest
        ]);
    }

    // Reject a request
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'owner_notes' => 'required|string|min:10',
        ]);

        $adoptionRequest = AdoptionRequest::findOrFail($id);

        // Check if the logged-in user owns this pet
        if ($adoptionRequest->pet->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - You can only manage requests for your own pets'
            ], 403);
        }

        $adoptionRequest->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'owner_notes' => $validated['owner_notes'],
        ]);

        $adoptionRequest->load(['user', 'pet']);

        if ($adoptionRequest->pet && $adoptionRequest->pet->image) {
            $adoptionRequest->pet->image_url = asset('storage/' . $adoptionRequest->pet->image);
        }

        return response()->json([
            'success' => true,
            'message' => 'Request rejected successfully',
            'data' => $adoptionRequest
        ]);
    }
}
