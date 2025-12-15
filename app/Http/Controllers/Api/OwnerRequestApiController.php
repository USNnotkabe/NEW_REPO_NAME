<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdoptionRequest;
use App\Models\Pet;
use App\Models\AdoptionHistory;
use Illuminate\Support\Facades\Storage;

class OwnerRequestApiController extends Controller
{
    // Helper to add image URL with CORS support
    private function appendImageUrl($pet)
    {
        if ($pet && $pet->image) {
            $filename = basename($pet->image);
            $pet->image_url = url('/api/pet-image/' . $filename);
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

        // ✅ FIX: Add full URLs for valid IDs using API routes
        $requests->each(function ($request) {
            // Add pet image URL
            if ($request->pet && $request->pet->image) {
                $filename = basename($request->pet->image);
                $request->pet->image_url = url('/api/pet-image/' . $filename);
            }

            // Add valid ID URLs
            if ($request->valid_id_1) {
                $filename = basename($request->valid_id_1);
                $request->valid_id_1_url = url('/api/valid-id-image/' . $filename);
            }
            if ($request->valid_id_2) {
                $filename = basename($request->valid_id_2);
                $request->valid_id_2_url = url('/api/valid-id-image/' . $filename);
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

        // Reject all other pending requests for this pet
        AdoptionRequest::where('pet_id', $adoptionRequest->pet_id)
            ->where('id', '!=', $id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected', 'admin_notes' => 'Another request was approved']);

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
                'owner_notes' => $validated['owner_notes'] ?? null,
                'reviewed_at' => now(),
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

    // ✅ NEW: Cancel adoption request (for adopters)
    public function cancel(Request $request, $id)
    {
        try {
            $adoptionRequest = AdoptionRequest::findOrFail($id);

            // Check if user is the requester (not the owner)
            if ($adoptionRequest->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - You can only cancel your own requests'
                ], 403);
            }

            // Can only cancel pending requests
            if ($adoptionRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only cancel pending requests'
                ], 400);
            }

            // Delete uploaded valid IDs from storage
            if ($adoptionRequest->valid_id_1) {
                Storage::disk('public')->delete($adoptionRequest->valid_id_1);
            }
            if ($adoptionRequest->valid_id_2) {
                Storage::disk('public')->delete($adoptionRequest->valid_id_2);
            }

            // Delete the request
            $adoptionRequest->delete();

            return response()->json([
                'success' => true,
                'message' => 'Request cancelled successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling request: ' . $e->getMessage()
            ], 500);
        }
    }

    // Store new adoption request
    public function store(Request $request)
    {
        // ✅ Debug logging
        \Log::info('Adoption Request Data:', $request->all());

        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'message' => 'required|string|min:20',
            'applicant_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'valid_id_1' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'valid_id_2' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $pet = Pet::findOrFail($validated['pet_id']);

        // Check if pet is available
        if ($pet->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'This pet is no longer available'
            ], 400);
        }

        // Check if user already has pending request for this pet
        $existingRequest = AdoptionRequest::where('user_id', auth()->id())
            ->where('pet_id', $validated['pet_id'])
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending request for this pet'
            ], 400);
        }

        // Check if user is trying to adopt their own pet
        if ($pet->user_id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot request your own pet'
            ], 400);
        }

        // Upload valid IDs only if provided
        $id1Path = $request->hasFile('valid_id_1')
            ? $request->file('valid_id_1')->store('valid-ids', 'public')
            : null;

        $id2Path = $request->hasFile('valid_id_2')
            ? $request->file('valid_id_2')->store('valid-ids', 'public')
            : null;

        // Create adoption request
        $adoptionRequest = AdoptionRequest::create([
            'user_id' => auth()->id(),
            'pet_id' => $validated['pet_id'],
            'message' => $validated['message'],
            'applicant_name' => $request->input('applicant_name'),
            'phone_number' => $request->input('phone_number'),
            'valid_id_1' => $id1Path,
            'valid_id_2' => $id2Path,
            'status' => 'pending',
        ]);

        $adoptionRequest->load('pet.user');

        // Add image URLs
        if ($adoptionRequest->pet) {
            $this->appendImageUrl($adoptionRequest->pet);
        }

        if ($adoptionRequest->valid_id_1) {
            $filename = basename($adoptionRequest->valid_id_1);
            $adoptionRequest->valid_id_1_url = url('/api/valid-id-image/' . $filename);
        }
        if ($adoptionRequest->valid_id_2) {
            $filename = basename($adoptionRequest->valid_id_2);
            $adoptionRequest->valid_id_2_url = url('/api/valid-id-image/' . $filename);
        }

        return response()->json([
            'success' => true,
            'message' => 'Request submitted successfully',
            'data' => $adoptionRequest
        ], 201);
    }
}
