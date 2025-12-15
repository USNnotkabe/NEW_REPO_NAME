<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdoptionRequest;
use App\Models\Pet;
use App\Models\AdoptionHistory;

class OwnerRequestApiController extends Controller
{
    // Helper to add image URL with CORS support
    private function appendImageUrl($pet)
    {
        if ($pet && $pet->image) {
            $baseUrl = request()->getSchemeAndHttpHost();
            $filename = basename($pet->image);
            $pet->image_url = $baseUrl . '/api/pet-image/' . $filename;
        }
        return $pet;
    }

    // Get requests for owner's pets
    public function index()
    {
        $requests = AdoptionRequest::with(['pet', 'user'])
            ->whereHas('pet', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Add full URLs for valid IDs
        $requests->each(function ($request) {
            if ($request->valid_id_1) {
                $request->valid_id_1_url = asset('storage/' . $request->valid_id_1);
            }
            if ($request->valid_id_2) {
                $request->valid_id_2_url = asset('storage/' . $request->valid_id_2);
            }
        });

        return response()->json($requests, 200);
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

        if ($adoptionRequest->pet) {
            $this->appendImageUrl($adoptionRequest->pet);
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
        // Make owner_notes optional
        $validated = $request->validate([
            'owner_notes' => 'nullable|string'
        ]);

        try {
            $adoptionRequest = AdoptionRequest::findOrFail($id);

            if ($adoptionRequest->status !== 'pending') {
                return response()->json([
                    'message' => 'This request has already been processed'
                ], 400);
            }

            $pet = Pet::findOrFail($adoptionRequest->pet_id);

            if ($pet->user_id !== auth()->id()) {
                return response()->json([
                    'message' => 'Unauthorized - You are not the pet owner'
                ], 403);
            }

            AdoptionRequest::where('id', $id)->update([
                'status' => 'rejected',
                'owner_notes' => $validated['owner_notes'] ?? null
            ]);

            return response()->json([
                'message' => 'Request rejected successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error rejecting request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // ✅ ADD THIS DEBUG
        \Log::info('Adoption Request Data:', $request->all());

        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'message' => 'required|string|min:20',
            'applicant_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'valid_id_1' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'valid_id_2' => 'nullable|file|mimes:jpeg,pn    g,jpg,pdf|max:5120',
        ]);

        // ... rest of code
    }
}
